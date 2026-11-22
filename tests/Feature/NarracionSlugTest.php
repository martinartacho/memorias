<?php

namespace Tests\Feature;

use App\Models\Narracion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NarracionSlugTest extends TestCase
{
    use RefreshDatabase;

    private function narracion(string $slug, string $estado = 'publicado'): Narracion
    {
        return Narracion::create([
            'titulo' => 'Capítulo 1',
            'contenido' => '<p>Texto</p>',
            'slug' => $slug,
            'fecha_publicacion' => now()->subDay(),
            'estado' => $estado,
            'user_id' => User::factory()->create(['role' => 'admin'])->id,
            'permiso_lectura' => 'publico',
        ]);
    }

    public function test_la_url_antigua_con_numero_redirige_a_la_nueva(): void
    {
        $this->narracion('capitulo-1-la-casa');

        $this->get('/narracion/capitulo-1-la-casa-1795017686')
            ->assertStatus(301)
            ->assertRedirect('/narracion/capitulo-1-la-casa');
    }

    public function test_no_redirige_si_la_narracion_no_es_visible(): void
    {
        $this->narracion('un-borrador', 'borrador');

        $this->get('/narracion/un-borrador-1795017686')->assertNotFound();
    }

    public function test_un_slug_inexistente_sigue_dando_404(): void
    {
        $this->get('/narracion/nada-1795017686')->assertNotFound();
        $this->get('/narracion/nada')->assertNotFound();
    }

    public function test_al_crear_no_se_anade_numero_y_los_repetidos_llevan_sufijo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $datos = ['titulo' => 'La casa de la encina', 'contenido' => '<p>Hola</p>', 'fecha_publicacion' => now()->toDateString(), 'estado' => 'borrador', 'permiso_lectura' => 'publico'];

        $this->actingAs($admin)->post(route('admin.narraciones.store'), $datos);
        $this->actingAs($admin)->post(route('admin.narraciones.store'), $datos);

        $this->assertSame(['la-casa-de-la-encina', 'la-casa-de-la-encina-2'], Narracion::orderBy('id')->pluck('slug')->all());
    }
}
