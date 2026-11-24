<?php

namespace Tests\Feature;

use App\Models\Narracion;
use App\Models\NarracionReaction;
use App\Models\User;
use App\Support\SerieOrden;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SerieCapitulosTest extends TestCase
{
    use RefreshDatabase;

    private ?User $autor = null;

    private function narracion(string $titulo, array $extra = []): Narracion
    {
        $this->autor ??= User::factory()->create(['role' => 'admin']);

        return Narracion::create($extra + [
            'titulo' => $titulo,
            'contenido' => '<p>Texto de '.$titulo.'</p>',
            'slug' => \Illuminate\Support\Str::slug($titulo),
            'fecha_publicacion' => now()->subDay(),
            'estado' => 'publicado',
            'user_id' => $this->autor->id,
            'permiso_lectura' => 'publico',
        ]);
    }

    private function votar(Narracion $n, string $tipo = 'like'): void
    {
        $this->postJson(route('narraciones.react', $n->slug), ['tipo' => $tipo])->assertOk()->assertJson(['ok' => true]);
    }

    public function test_el_listado_agrupa_los_capitulos_donde_cae_el_primero(): void
    {
        $uno = $this->narracion('Cap uno', ['fecha_publicacion' => now()->subDays(5)]);
        $suelta = $this->narracion('Suelta', ['fecha_publicacion' => now()->subDays(3)]);
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id, 'fecha_publicacion' => now()->subDays(1)]);

        $orden = SerieOrden::agrupar(Narracion::orderBy('fecha_publicacion', 'asc')->get())->pluck('titulo')->all();

        $this->assertSame(['Cap uno', 'Cap dos', 'Suelta'], $orden);
        // El listado público va de más reciente a más antiguo: la serie baja entera, capítulo 1 antes que el 2
        $this->get('/narraciones')->assertOk()->assertViewHas(
            'narraciones',
            fn ($p) => $p->pluck('titulo')->all() === ['Suelta', 'Cap uno', 'Cap dos'],
        );
    }

    public function test_un_capitulo_cuyo_anterior_no_esta_en_la_lista_sale_solo(): void
    {
        $uno = $this->narracion('Cap uno');
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);

        $this->assertSame(['Cap dos'], SerieOrden::agrupar(collect([$dos]))->pluck('titulo')->all());
    }

    public function test_el_siguiente_capitulo_exige_valorar_el_anterior(): void
    {
        $uno = $this->narracion('Cap uno', ['pide_valoracion' => true]);
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);

        $this->get(route('narraciones.show', $dos->slug))
            ->assertRedirect(route('narraciones.show', $uno->slug))
            ->assertSessionHas('serie_aviso');

        $this->votar($uno, 'dislike');

        $this->get(route('narraciones.show', $dos->slug))->assertOk()->assertSee('Texto de Cap dos');
    }

    public function test_el_voto_de_otra_persona_no_abre_el_capitulo(): void
    {
        $uno = $this->narracion('Cap uno', ['pide_valoracion' => true]);
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);
        NarracionReaction::create(['narracion_id' => $uno->id, 'identifier' => 'ip:otra-persona', 'tipo' => 'like']);

        $this->get(route('narraciones.show', $dos->slug))->assertRedirect(route('narraciones.show', $uno->slug));
    }

    public function test_sin_la_casilla_el_siguiente_capitulo_se_abre_sin_valorar(): void
    {
        $uno = $this->narracion('Cap uno');
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);

        $this->assertFalse($uno->fresh()->pide_valoracion);
        $this->get(route('narraciones.show', $dos->slug))->assertOk()->assertSee('Texto de Cap dos');
        $this->get(route('narraciones.show', $uno->slug))->assertOk()->assertDontSee('valora este con');
    }

    public function test_la_invitacion_a_suscribirse_sale_solo_si_el_siguiente_es_de_seguidores(): void
    {
        $uno = $this->narracion('Cap uno');
        $this->narracion('Cap dos', ['after_id' => $uno->id, 'permiso_lectura' => 'seguidores']);

        $this->get(route('narraciones.show', $uno->slug))->assertOk()->assertSee('serie-invitacion', false);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('narraciones.show', $uno->slug))->assertOk()->assertDontSee('serie-invitacion', false);
    }

    public function test_sin_siguiente_de_seguidores_no_hay_invitacion(): void
    {
        $uno = $this->narracion('Cap uno');
        $this->narracion('Cap dos', ['after_id' => $uno->id]);

        $this->get(route('narraciones.show', $uno->slug))->assertOk()->assertDontSee('serie-invitacion', false);
    }

    public function test_recargar_no_suma_lecturas_pero_otro_visitante_si(): void
    {
        $uno = $this->narracion('Cap uno');

        $this->get(route('narraciones.show', $uno->slug))->assertOk();
        $this->get(route('narraciones.show', $uno->slug))->assertOk();
        $this->assertSame(1, $uno->fresh()->count_read);

        $this->withHeader('User-Agent', 'otro-navegador')
            ->get(route('narraciones.show', $uno->slug))->assertOk();
        $this->assertSame(2, $uno->fresh()->count_read);
    }

    public function test_el_admin_no_suma_lecturas(): void
    {
        $uno = $this->narracion('Cap uno');

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('narraciones.show', $uno->slug))->assertOk();

        $this->assertSame(0, $uno->fresh()->count_read);
    }

    public function test_la_pagina_de_la_serie_lista_los_capitulos_y_marca_los_leidos(): void
    {
        $uno = $this->narracion('Linaje. Capitulo 1 — La encina', ['fecha_publicacion' => now()->subDays(3)]);
        $dos = $this->narracion('Linaje. Capitulo 2 — El camino', ['after_id' => $uno->id, 'fecha_publicacion' => now()->subDays(2)]);
        $tres = $this->narracion('Linaje. Capitulo 3 — Burgos', ['after_id' => $dos->id, 'permiso_lectura' => 'seguidores', 'fecha_publicacion' => now()->subDay()]);

        $this->get(route('narraciones.serie', $dos->slug))->assertOk()
            ->assertSee('Linaje')
            ->assertSee('La encina')
            ->assertSee('El camino')
            ->assertSee('3 capítulos')
            ->assertSee('Avísame del siguiente')
            ->assertDontSee('✓ leído');

        $this->get(route('narraciones.show', $uno->slug))->assertOk()
            ->assertCookie('narraciones_leidas', (string) $uno->id);

        $this->withCookie('narraciones_leidas', (string) $uno->id)
            ->get(route('narraciones.serie', $uno->slug))->assertOk()
            ->assertSee('✓ leído')
            ->assertSee('leídos 1 de 3')
            ->assertSee('Seguir leyendo');
    }

    public function test_una_narracion_suelta_no_tiene_pagina_de_serie(): void
    {
        $suelta = $this->narracion('Suelta');

        $this->get(route('narraciones.serie', $suelta->slug))->assertRedirect(route('narraciones.show', $suelta->slug));
    }

    public function test_el_listado_y_el_capitulo_enlazan_a_toda_la_serie(): void
    {
        $uno = $this->narracion('Cap uno', ['fecha_publicacion' => now()->subDays(2)]);
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id, 'fecha_publicacion' => now()->subDay()]);

        $this->get('/narraciones')->assertOk()->assertSee(route('narraciones.serie', $uno->slug), false);
        $this->get(route('narraciones.show', $dos->slug))->assertOk()->assertSee('Ver toda la serie');
    }

    public function test_el_admin_no_necesita_valorar(): void
    {
        $uno = $this->narracion('Cap uno', ['pide_valoracion' => true]);
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('narraciones.show', $dos->slug))->assertOk();
    }

    public function test_la_pagina_del_capitulo_enlaza_anterior_y_siguiente(): void
    {
        $uno = $this->narracion('Cap uno');
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);
        $tres = $this->narracion('Cap tres', ['after_id' => $dos->id]);
        $this->votar($uno);

        $this->get(route('narraciones.show', $dos->slug))
            ->assertSee('Capítulo 2')
            ->assertSee('Capítulo anterior: Cap uno')
            ->assertSee('Siguiente capítulo: Cap tres');
    }

    public function test_la_destacada_de_portada_nunca_es_un_capitulo_intermedio(): void
    {
        $uno = $this->narracion('Cap uno', ['fecha_publicacion' => now()->subDays(5)]);
        $this->narracion('Cap dos', ['after_id' => $uno->id, 'fecha_publicacion' => now()->subDay(), 'orden' => 1000]);

        $this->get('/narraciones')->assertOk()->assertViewHas('featured', fn ($f) => $f->titulo === 'Cap uno');
    }

    public function test_un_capitulo_con_fecha_futura_queda_en_promocion_y_el_comando_lo_publica_ese_dia(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $uno = $this->narracion('Cap uno');

        $this->actingAs($admin)->post(route('admin.narraciones.store'), [
            'titulo' => 'Cap dos', 'contenido' => '<p>Hola</p>', 'estado' => 'publicado',
            'fecha_publicacion' => now()->addDay()->toDateString(), 'permiso_lectura' => 'publico', 'after_id' => $uno->id,
        ])->assertRedirect();

        $dos = Narracion::where('titulo', 'Cap dos')->firstOrFail();
        $this->assertSame('promocion', $dos->estado);
        $this->assertSame($uno->id, $dos->after_id);

        $this->travel(2)->days();
        $this->artisan('narraciones:publicar-programadas')->assertExitCode(0);

        $this->assertSame('publicado', $dos->fresh()->estado);
    }

    public function test_no_se_puede_crear_un_ciclo_ni_dos_siguientes_para_el_mismo_capitulo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $uno = $this->narracion('Cap uno');
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);
        $otra = $this->narracion('Otra');
        $base = ['contenido' => '<p>Hola</p>', 'estado' => 'borrador', 'fecha_publicacion' => now()->toDateString(), 'permiso_lectura' => 'publico'];

        $this->actingAs($admin)->put(route('admin.narraciones.update', $uno->slug), $base + ['titulo' => 'Cap uno', 'after_id' => $dos->id])
            ->assertSessionHasErrors('after_id');

        $this->actingAs($admin)->put(route('admin.narraciones.update', $otra->slug), $base + ['titulo' => 'Otra', 'after_id' => $uno->id])
            ->assertSessionHasErrors('after_id');

        $this->assertNull($uno->fresh()->after_id);
        $this->assertNull($otra->fresh()->after_id);
    }

    public function test_las_candidatas_excluyen_a_si_misma_a_sus_siguientes_y_a_las_ocupadas(): void
    {
        $uno = $this->narracion('Cap uno');
        $dos = $this->narracion('Cap dos', ['after_id' => $uno->id]);
        $suelta = $this->narracion('Suelta');

        $titulos = SerieOrden::candidatas($uno)->pluck('titulo')->all();

        $this->assertSame(['Suelta'], $titulos);
        $this->assertContains('Cap dos', SerieOrden::candidatas($suelta)->pluck('titulo')->all());
        $this->assertNotContains('Cap uno', SerieOrden::candidatas($suelta)->pluck('titulo')->all());
    }
}
