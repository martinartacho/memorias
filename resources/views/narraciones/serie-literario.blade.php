@extends('layouts.literario')

@section('title', $tituloSerie . ' · Serie · ' . __('site.name'))

@section('content')
@php
    $total = $capitulos->count();
    $leidosN = $capitulos->filter(fn ($c) => in_array($c->id, $leidas, true))->count();
    $siguiente = $capitulos->first(fn ($c) => ! in_array($c->id, $leidas, true));
@endphp
<main class="container" style="max-width:720px; margin:0 auto; padding:2.5rem 1.5rem 3rem;">

  <p class="featured-author" style="margin-bottom:0.6rem;">
    <a href="{{ route('narraciones.index') }}" style="color:inherit; text-decoration:none;">Narraciones</a> · Serie
  </p>

  <h1 style="font-family:var(--serif); font-weight:400; font-style:italic; font-size:2.1rem; line-height:1.25; color:var(--ink); margin:0 0 0.75rem;">
    {{ $tituloSerie }}
  </h1>

  <p style="font-family:var(--sans); font-size:0.7rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted); margin:0 0 1.5rem;">
    {{ $total }} {{ $total === 1 ? 'capítulo' : 'capítulos' }}
    @if($leidosN > 0) · leídos {{ $leidosN }} de {{ $total }} @endif
    @if($lecturas > 0) · {{ $lecturas }} {{ $lecturas === 1 ? 'lectura' : 'lecturas' }} @endif
  </p>

  @if($resumen !== '')
    <p style="font-family:var(--serif); font-size:1.05rem; line-height:1.8; color:var(--ink); margin:0 0 2rem;">{{ $resumen }}</p>
  @endif

  <h2 style="font-family:var(--sans); font-size:0.7rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin:0 0 0.5rem;">Capítulos</h2>

  <div style="border-top:1px solid var(--parchment-dark);">
    @foreach($capitulos as $i => $cap)
      @php
        $leido = in_array($cap->id, $leidas, true);
        $cerrado = $cap->permiso_lectura === 'seguidores' && ! $esSubscriptor && ! $esAdmin;
      @endphp
      <a href="{{ route('narraciones.show', $cap->slug) }}"
         style="display:flex; align-items:baseline; gap:0.9rem; padding:0.95rem 0; border-bottom:1px solid var(--parchment-dark); text-decoration:none; color:var(--ink);">
        <span style="font-family:var(--sans); font-size:0.7rem; letter-spacing:0.12em; color:var(--ink-muted); min-width:1.8rem;">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
        <span style="flex:1; font-family:var(--serif); font-size:1.05rem; line-height:1.4;">
          {{ trim((string) preg_replace('/^.*?Cap[ií]tulo\s*\d+\s*[—–-]\s*/iu', '', $cap->titulo)) ?: $cap->titulo }}
          @if($cerrado)<i class="bi bi-envelope-heart-fill" title="Para suscriptores" style="color:var(--ink-muted); font-size:0.8rem;"></i>@endif
        </span>
        @if($leido)
          <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.12em; text-transform:uppercase; color:#16a34a;">✓ leído</span>
        @endif
      </a>
    @endforeach
  </div>

  <div style="margin-top:1.75rem; display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;">
    @if($siguiente)
      <a href="{{ route('narraciones.show', $siguiente->slug) }}"
         style="display:inline-block; background:var(--ink); color:var(--parchment); font-family:var(--sans); font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.8rem 1.6rem; text-decoration:none;">
        {{ $leidosN > 0 ? 'Seguir leyendo' : 'Empezar a leer' }} →
      </a>
    @endif

    @if(! $esSubscriptor && ! $esAdmin)
      <a href="{{ route('suscripcion.form', ['slug' => $primero->slug]) }}"
         style="display:inline-block; border:1px solid var(--accent); color:var(--accent); font-family:var(--sans); font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.8rem 1.6rem; text-decoration:none;">
        Avísame del siguiente
      </a>
    @endif
  </div>

  @if(! $esSubscriptor && ! $esAdmin)
    <p style="margin-top:0.75rem; font-family:var(--sans); font-size:0.8rem; color:var(--ink-muted); line-height:1.6;">
      Suscríbete gratis y te escribo cuando salga cada capítulo.
    </p>
  @endif
</main>
@endsection
