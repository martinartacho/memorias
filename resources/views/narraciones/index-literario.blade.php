@extends('layouts.literario')

@section('title', 'Narraciones · ' . __('site.name'))

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
        <div class="featured-author">Publicado · {{ $featured->fecha_publicacion->translatedFormat('j \d\e F \d\e Y') }}</div>
        <h2>{{ Str::limit(strip_tags($featured->titulo), 60) }}</h2>
        <p class="dropcap">{{ Str::limit(strip_tags($featured->contenido), 280) }}</p>
        <a href="{{ route('narraciones.show', $featured->slug) }}" class="read-more">Leer narración →</a>
      </div>
    </div>
  @endif

  {{-- ── BUSCADOR ── --}}
  <form method="GET" action="{{ route('narraciones.index') }}"
        style="margin:2rem 0 0; display:flex; gap:0.5rem; align-items:center;">
    <div style="flex:1; position:relative;">
      <i class="bi bi-search"
         style="position:absolute; left:0.85rem; top:50%; transform:translateY(-50%);
                color:var(--ink-muted); font-size:0.75rem; pointer-events:none;"></i>
      <input type="text" name="q" value="{{ $q }}"
             placeholder="Buscar por título…"
             autocomplete="off"
             style="width:100%; padding:0.55rem 0.85rem 0.55rem 2.2rem;
                    font-family:var(--sans); font-size:0.72rem; letter-spacing:0.04em;
                    border:1px solid var(--parchment-dark); background:var(--parchment);
                    color:var(--ink); outline:none;
                    transition:border-color 0.2s;">
    </div>
    <select name="orden" onchange="this.form.submit()"
            style="padding:0.55rem 0.7rem; font-family:var(--sans); font-size:0.65rem;
                   letter-spacing:0.05em; border:1px solid var(--parchment-dark);
                   background:var(--parchment); color:var(--ink); cursor:pointer;">
      <option value="fecha_desc" @selected($orden === 'fecha_desc')>Más reciente</option>
      <option value="fecha_asc"  @selected($orden === 'fecha_asc')>Más antigua</option>
      <option value="titulo_asc" @selected($orden === 'titulo_asc')>Título A–Z</option>
      <option value="titulo_desc"@selected($orden === 'titulo_desc')>Título Z–A</option>
    </select>
    <button type="submit"
            style="padding:0.55rem 1.1rem; font-family:var(--sans); font-size:0.65rem;
                   letter-spacing:0.15em; text-transform:uppercase;
                   background:var(--ink); color:var(--parchment);
                   border:none; cursor:pointer;">
      Buscar
    </button>
    @if($q !== '' || $orden !== 'fecha_desc')
      <a href="{{ route('narraciones.index') }}"
         style="padding:0.55rem 0.75rem; font-family:var(--sans); font-size:0.65rem;
                letter-spacing:0.1em; color:var(--ink-muted); text-decoration:none;
                border:1px solid var(--parchment-dark);">
        <i class="bi bi-x-lg"></i>
      </a>
    @endif
  </form>

  {{-- ── GRID ── --}}
  @if($narraciones->count() > 0)

    <div class="grid-label">
      @if($q !== '')
        Resultados para «{{ $q }}»
      @else
        Todas las narraciones
      @endif
      <span>{{ $narraciones->total() }} texto{{ $narraciones->total() !== 1 ? 's' : '' }}</span>
    </div>

    <div class="stories-list">
      @foreach($narraciones as $key => $narracion)

        @php
          $esPromo  = $narracion->estado === 'promocion';
          $esFutura = $narracion->fecha_publicacion->isFuture();
        @endphp

        <a href="{{ route('narraciones.show', $narracion->slug) }}"
           class="story-row"
           style="{{ $esFutura ? 'opacity:0.7;' : '' }}">

          <span class="row-num">{{ str_pad($narraciones->firstItem() + $key, 2, '0', STR_PAD_LEFT) }}</span>

          <span class="row-title">
            {{ $narracion->titulo }}
            @if($esPromo)
              <i class="bi bi-eye" title="Solo vista previa"></i>
            @endif
            @if($narracion->permiso_lectura === 'seguidores')
              <i class="bi bi-envelope-heart-fill" title="Requiere suscripción"></i>
            @elseif($narracion->permiso_lectura === 'privado')
              <i class="bi bi-lock-fill" title="Privado"></i>
            @endif
            @if($esFutura)
              <i class="bi bi-clock-fill" title="Disponible el {{ $narracion->fecha_publicacion->translatedFormat('j \d\e F') }}"></i>
            @endif
          </span>

          <span class="row-date">
            {{ $narracion->fecha_publicacion->translatedFormat('j \d\e F \d\e Y') }}
          </span>

        </a>
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
    <div style="text-align:center; padding:4rem 0 5rem;">
      @if($q !== '')
        <p style="font-family:var(--serif); font-size:1.2rem; color:var(--ink-muted); margin-bottom:1.25rem;">
          Sin resultados para «{{ $q }}»
        </p>
        <a href="{{ route('narraciones.index') }}"
           style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em;
                  text-transform:uppercase; color:var(--ink-muted); text-decoration:none;
                  border-bottom:1px solid var(--parchment-dark); padding-bottom:0.1rem;">
          ← Ver todas las narraciones
        </a>
      @else
        <p style="font-family:var(--serif); font-size:1.5rem; color:var(--ink-muted);">
          Aún no hay narraciones publicadas.
        </p>
      @endif
    </div>
  @endif

</main>
@endsection
