@extends('layouts.literario')

@section('title', 'Revisa tu correo · Memorias sin orden')

@section('content')
<main class="container">
  <div style="max-width:520px; margin:0 auto; padding:3rem 0; text-align:center;">

    <div style="font-size:3.5rem; margin-bottom:1.5rem;">✉️</div>

    <div class="section-label" style="justify-content:center;">Acceso enviado</div>

    <h2 style="font-family:var(--serif); font-size:2rem; font-weight:700; color:var(--ink); margin-bottom:1rem; line-height:1.2;">
      Revisa tu correo
    </h2>

    <p style="font-size:1rem; line-height:1.8; color:var(--ink-soft); margin-bottom:0.5rem;">
      Te hemos enviado un enlace de acceso a:
    </p>
    <p style="font-family:var(--sans); font-size:0.9rem; letter-spacing:0.1em; color:var(--accent); margin-bottom:2rem;">
      {{ $email }}
    </p>

    @if($narracion)
    <div style="background:#fff; border:1px solid var(--parchment-dark); padding:1.25rem 1.75rem; margin-bottom:2rem; text-align:left;">
      <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.4rem;">Te espera</div>
      <div style="font-family:var(--serif); font-size:1.1rem; font-weight:700; color:var(--ink);">{{ $narracion->titulo }}</div>
    </div>
    @endif

    <p style="font-size:0.9rem; line-height:1.7; color:var(--ink-muted);">
      Haz clic en el enlace del correo para acceder.<br>
      Si no lo encuentras, revisa la carpeta de spam.
    </p>

    <div style="margin-top:2.5rem;">
      <a href="{{ route('narraciones.index') }}" class="read-more">← Mientras tanto, otras narraciones</a>
    </div>

  </div>
</main>
@endsection
