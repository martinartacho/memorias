@extends('layouts.literario')

@section('title', $narracion->titulo . ' · Memorias sin orden')

@section('content')
<main class="container">

  {{-- ── CABECERA DOS COLUMNAS ── --}}
  <div class="featured">

    {{-- Columna izquierda: metadatos --}}
    <div class="featured-meta">
      <div class="section-label">Narración</div>
      <div class="featured-number">{{ str_pad($narracion->id, 2, '0', STR_PAD_LEFT) }}</div>
      <div style="margin-top:auto;">
        <div style="font-family:var(--sans); font-size:1.4rem; font-weight:700; color:var(--parchment-dark); line-height:1; margin-bottom:0.25rem;">
          {{ $narracion->palabra_count }}
        </div>
        <div class="featured-category">palabras</div>
      </div>
      <div class="featured-category" style="margin-top:1rem;">
        Narración · {{ $narracion->fecha_publicacion->format('Y') }}
      </div>
    </div>

    <div class="featured-divider"></div>

    {{-- Columna derecha: contenido --}}
    <div class="featured-text">

      {{-- Línea de fecha + permiso --}}
      <div class="featured-author" style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;">
        <span>Publicado · {{ $narracion->fecha_publicacion->format('F Y') }}</span>

        @if($narracion->estado === 'promocion')
          <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--gold); border:1px solid var(--gold); padding:0.15rem 0.5rem;">
            Vista previa
          </span>
        @endif

        @switch($narracion->permiso_lectura)
          @case('publico')
            <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted);">
              <i class="bi bi-globe2"></i> Público
            </span>
            @break
          @case('seguidores')
            <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--accent);">
              <i class="bi bi-envelope-heart-fill"></i> Suscriptores
            </span>
            @break
          @case('privado')
            <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted);">
              <i class="bi bi-lock-fill"></i> Privado
            </span>
            @break
        @endswitch
      </div>

      {{-- Título --}}
      <h2>{{ $narracion->titulo }}</h2>

      {{-- Contenido: completo o truncado según estado --}}
      @if($narracion->estado === 'promocion')

        {{-- MODO PROMOCION: solo primer párrafo / 100 chars --}}
        @php
          $promo_texto = Str::limit(strip_tags($narracion->contenido), 100);
        @endphp
        <p style="font-size:1rem; line-height:1.8; color:var(--ink-soft); margin-bottom:1.5rem; position:relative;">
          {{ $promo_texto }}…
          <span style="display:block; height:3rem; background:linear-gradient(to bottom, transparent, var(--parchment)); position:absolute; bottom:-1.5rem; left:0; right:0;"></span>
        </p>

        <div style="border:1px solid var(--parchment-dark); background:#fff; padding:1.5rem 2rem; margin-top:2rem; text-align:center;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--gold); margin-bottom:0.75rem;">
            <i class="bi bi-eye"></i> Vista previa · Texto completo para suscriptores
          </div>
          <p style="font-size:0.9rem; color:var(--ink-muted); margin-bottom:1.25rem;">
            Suscríbete gratis para leer «{{ $narracion->titulo }}» al completo.
          </p>
          <a href="{{ route('suscripcion.form', ['slug' => $narracion->slug]) }}"
             style="display:inline-block; background:var(--accent); color:var(--parchment); font-family:var(--sans); font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.75rem 1.75rem; text-decoration:none; transition:background 0.2s;"
             onmouseover="this.style.background='var(--ink)'"
             onmouseout="this.style.background='var(--accent)'">
            Suscribirme gratis →
          </a>
        </div>

      @else

        {{-- MODO NORMAL: contenido completo --}}
        <div style="font-size:1.05rem; line-height:1.9; color:var(--ink-soft);">
          {!! $narracion->contenido !!}
        </div>

      @endif

      {{-- Navegación inferior --}}
      <div style="margin-top:2.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; border-top:1px solid var(--parchment-dark); padding-top:1.5rem;">
        <a href="{{ route('narraciones.index') }}" class="read-more">← Volver a narraciones</a>
        <span style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink-muted);">
          {{ $narracion->count_read }} {{ $narracion->count_read === 1 ? 'lectura' : 'lecturas' }}
        </span>
      </div>

    </div>
  </div>

  {{-- ── OTRAS MEMORIAS ── --}}
  @php
    $hoy = now()->toDateString();
    $relatedQuery = \App\Models\Narracion::visible()
        ->where('id', '!=', $narracion->id)
        ->where('fecha_publicacion', '<=', $hoy)
        ->orderByFecha();

    if (auth()->check() && auth()->user()->canAccessAdmin()) {
        // Admin ve todo
    } elseif (request()->cookie('subscriber_token') && \App\Models\Subscriber::tokenIsValid(request()->cookie('subscriber_token'))) {
        $relatedQuery->where('permiso_lectura', '!=', 'privado');
    } else {
        $relatedQuery->where('permiso_lectura', 'publico');
    }

    $related = $relatedQuery->take(3)->get();
  @endphp

  <div class="grid-label" style="margin-top:3rem;">
    Otras memorias <span>Explora más historias</span>
  </div>

  <div class="stories-grid">
    @foreach($related as $key => $story)
      @php $esPromo = $story->estado === 'promocion'; @endphp
      <div class="story-card {{ $esPromo ? 'story-card--promo' : '' }}"
           onclick="window.location.href='{{ route('narraciones.show', $story->slug) }}'">
        <div class="card-num">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="card-tag">
          @if($esPromo)
            <span style="color:var(--gold);">Vista previa</span>
          @else
            Narración
          @endif
          @if($story->permiso_lectura === 'seguidores')
            <span style="margin-left:0.4rem; color:var(--accent);"><i class="bi bi-envelope-heart-fill"></i></span>
          @elseif($story->permiso_lectura === 'privado')
            <span style="margin-left:0.4rem; color:var(--ink-muted);"><i class="bi bi-lock-fill"></i></span>
          @endif
        </div>
        <div class="card-title">{{ $story->titulo }}</div>
        <div class="card-author">{{ $story->fecha_publicacion->format('F Y') }}</div>
        <p class="card-excerpt">{{ Str::limit(strip_tags($story->contenido), $esPromo ? 100 : 120) }}</p>
      </div>
    @endforeach

    @for($i = $related->count(); $i < 3; $i++)
      <div class="story-card" style="opacity:0.3; pointer-events:none;">
        <div class="card-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="card-tag">Próximamente</div>
        <div class="card-title">Nueva historia</div>
        <div class="card-author">En preparación</div>
        <p class="card-excerpt">Pronto habrá nuevas narraciones disponibles…</p>
      </div>
    @endfor
  </div>

</main>
@endsection
