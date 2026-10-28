<?php

namespace Tests\Feature;

use App\Models\Narracion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorradorPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function borrador(User $autor): Narracion
    {
        return Narracion::create([
            'titulo' => 'Un borrador',
            'contenido' => '<p>Texto</p>',
            'slug' => 'un-borrador-1',
            'fecha_publicacion' => now()->subDay(),
            'estado' => 'borrador',
            'user_id' => $autor->id,
            'permiso_lectura' => 'publico',
        ]);
    }

    public function test_un_visitante_no_ve_un_borrador(): void
    {
        $this->borrador(User::factory()->create(['role' => 'admin']));

        $this->get('/narracion/un-borrador-1')->assertNotFound();
    }

    public function test_el_admin_puede_previsualizar_un_borrador_sin_contar_lectura(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $narracion = $this->borrador($admin);

        $this->actingAs($admin)->get('/narracion/un-borrador-1')->assertOk()->assertSee('Un borrador');

        $this->assertSame(0, (int) $narracion->fresh()->count_read);
    }

    public function test_un_editor_solo_previsualiza_sus_propios_borradores(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $otro = User::factory()->create(['role' => 'editor']);
        $this->borrador($otro);

        $this->actingAs($editor)->get('/narracion/un-borrador-1')->assertForbidden();
        $this->actingAs($otro)->get('/narracion/un-borrador-1')->assertOk();
    }
}
