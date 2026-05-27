@extends('layouts.literario')

@section('title', 'Narraciones · Memorias sin orden')

@section('content')
<main class="container">

  {{-- ── DESTACADO (solo en página 1) ── --}}
  @if($featured && $narraciones->currentPage() === 1)
    <div class="featured">
      <div class="featured-meta">
        <div class="section-label">Destacado</div>
        <div class="featured-number">{{ str_pad($featured->id, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="featured-category">Narración · {{ $featured->fecha_publicacion->format('Y') }}</div>
      </div>
      <div class="featured-divider"></div>
      <div class="featured-text">
        <div class="featured-author">Publicado · {{ $featured->fecha_publicacion->format('F Y') }}</div>
        <h2>{{ Str::limit(strip_tags($featured->titulo), 60) }}</h2>
        <p class="dropcap">{{ Str::limit(strip_tags($featured->contenido), 280) }}</p>
        <a href="{{ route('narraciones.show', $featured->slug) }}" class="read-more">Leer narración →</a>
      </div>
    </div>
  @endif

  {{-- ── GRID ── --}}
  @if($narraciones->count() > 0)

    <div class="grid-label">
      Todas las narraciones
      <span>{{ $narraciones->total() }} texto{{ $narraciones->total() !== 1 ? 's' : '' }}</span>
    </div>

    <div class="stories-grid">
      @foreach($narraciones as $key => $narracion)

        @php
          $esPromo  = $narracion->estado === 'promocion';
          $esFutura = $narracion->fecha_publicacion->isFuture();
        @endphp

        <div class="story-card {{ $esPromo ? 'story-card--promo' : '' }}"
             onclick="window.location.href='{{ route('narraciones.show', $narracion->slug) }}'"
             style="{{ $esFutura ? 'opacity:0.78;' : '' }}">

          <div class="card-num">{{ str_pad($narraciones->firstItem() + $key, 2, '0', STR_PAD_LEFT) }}</div>

          {{-- Badge de estado (siempre visible) --}}
          <div class="card-tag">
            @if($esPromo)
              <span style="color:var(--gold);">Vista previa</span>
            @else
              Narración
            @endif

            {{-- Permiso de lectura (siempre visible) --}}
            @if($narracion->permiso_lectura === 'seguidores')
              <span title="Solo suscriptores" style="margin-left:0.5rem; color:var(--accent);">
                <i class="bi bi-envelope-heart-fill"></i>
              </span>
            @elseif($narracion->permiso_lectura === 'privado')
              <span title="Privado" style="margin-left:0.5rem; color:var(--ink-muted);">
                <i class="bi bi-lock-fill"></i>
              </span>
            @else
              <span title="Público" style="margin-left:0.5rem; color:var(--ink-muted); opacity:0.4;">
                <i class="bi bi-globe2"></i>
              </span>
            @endif

            {{-- Fecha futura --}}
            @if($esFutura)
              <span title="Publicación programada" style="margin-left:0.3rem; color:var(--gold);">
                <i class="bi bi-clock-fill"></i>
              </span>
            @endif

            {{-- Contador de palabras --}}
            <span style="margin-left:0.5rem; opacity:0.55;">· {{ $narracion->palabra_count }} pal.</span>
          </div>

          <div class="card-title">{{ $narracion->titulo }}</div>
          <div class="card-author">{{ $narracion->fecha_publicacion->format('F Y') }}</div>

          {{-- Excerpt: 100 chars para promocion, 120 normal --}}
          <p class="card-excerpt">
            {{ Str::limit(strip_tags($narracion->contenido), $esPromo ? 100 : 120) }}
          </p>

          {{-- Etiqueta inferior según estado y rol --}}
          @if($esFutura)
            <div style="margin-top:0.75rem; font-family:var(--sans); font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--gold);">
              <i class="bi bi-clock-fill"></i> Disponible el {{ $narracion->fecha_publicacion->translatedFormat('j \d\e F') }}
            </div>
          @elseif($esPromo)
            <div style="margin-top:0.75rem; font-family:var(--sans); font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--gold);">
              <i class="bi bi-eye"></i> Solo vista previa
            </div>
          @elseif($narracion->permiso_lectura === 'privado')
            <div style="margin-top:0.75rem; font-family:var(--sans); font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted);">
              <i class="bi bi-lock-fill"></i> Solo visible para ti
            </div>
          @elseif($narracion->permiso_lectura === 'seguidores' && !$esSubscriptor && !$esAdmin)
            <div style="margin-top:0.75rem; font-family:var(--sans); font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--accent);">
              <i class="bi bi-envelope-heart-fill"></i> Requiere suscripción
            </div>
          @endif

        </div>
      @endforeach
    </div>

    {{-- ── PAGINACIÓN ── --}}
    {{ $narraciones->links('pagination.literario') }}

    {{-- ── TEASER PARA VISITANTES ANÓNIMOS ── --}}
    @if(!$esSubscriptor && !$esAdmin && $ocultas->isNotEmpty())
      <div style="margin-top:3rem; border-top:1px solid var(--parchment-dark); padding-top:2rem; text-align:center;">
        <p style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.35em; text-transform:uppercase; color:var(--accent); margin-bottom:0.75rem;">
          Algunos textos son exclusivos para suscriptores
        </p>
        <p style="font-family:var(--serif); font-style:italic; color:var(--ink-muted); font-size:0.95rem; margin-bottom:1.25rem; line-height:1.7;">
          {{ $ocultas->implode(' · ') }}
        </p>
        <a href="{{ route('suscripcion.form') }}" class="read-more">
          Suscribirme gratis →
        </a>
      </div>
    @endif

  @else
    <div style="text-align:center; padding:5rem 0;">
      <p style="font-family:var(--serif); font-size:1.5rem; color:var(--ink-muted);">
        Aún no hay narraciones publicadas.
      </p>
    </div>
  @endif

</main>
@endsection
