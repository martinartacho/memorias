<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Publicar narraciones programadas ─────────────────────────────────────────
// Cada día a las 08:00 h: cambia estado promocion→publicado si fecha_publicacion
// ya ha llegado y envía email a admins + suscriptores verificados.
Schedule::command('narraciones:publicar-programadas')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();
