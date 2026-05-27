<?php

namespace App\Console\Commands;

use App\Mail\NarracionPublicada;
use App\Models\Narracion;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PublicarNarracionesProgramadas extends Command
{
    protected $signature   = 'narraciones:publicar-programadas';
    protected $description = 'Publica narraciones en estado "promocion" cuya fecha de publicación ya ha llegado y notifica por email';

    public function handle(): int
    {
        $hoy = now()->toDateString();

        // Narraciones en promocion con fecha cumplida
        $pendientes = Narracion::where('estado', 'promocion')
            ->where('fecha_publicacion', '<=', $hoy)
            ->with('user')
            ->get();

        if ($pendientes->isEmpty()) {
            $this->info('Sin narraciones pendientes de publicar.');
            return self::SUCCESS;
        }

        foreach ($pendientes as $narracion) {
            // 1. Cambiar estado a publicado
            $narracion->update(['estado' => 'publicado']);
            $this->info("✓ Publicada: «{$narracion->titulo}» (id {$narracion->id})");

            // 2. Notificar solo si es pública o de seguidores (no privada)
            if ($narracion->permiso_lectura === 'privado') {
                $this->line("  → Privada, sin notificaciones.");
                continue;
            }

            $mailable = new NarracionPublicada($narracion);

            // Administradores
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send($mailable);
            }
            $this->line("  → Email a {$admins->count()} admin(s).");

            // Suscriptores verificados (solo si la narración no es privada)
            if ($narracion->permiso_lectura !== 'privado') {
                $suscriptores = Subscriber::whereNotNull('verified_at')->get();
                foreach ($suscriptores as $s) {
                    Mail::to($s->email)->send($mailable);
                }
                $this->line("  → Email a {$suscriptores->count()} suscriptor(es).");
            }
        }

        $this->info("Proceso completado: {$pendientes->count()} narración/es publicada/s.");
        return self::SUCCESS;
    }
}
