@extends('layouts.literario')
@section('title', 'Líneas argumentales · ' . __('site.name'))

@section('content')
<div class="container">
<div style="max-width:720px; margin:0 auto;">

  <div style="margin-bottom:3rem; border-bottom:1px solid var(--parchment-dark); padding-bottom:2rem;">
    <div class="section-label">Colección</div>
    <h1 style="font-family:var(--serif); font-size:2.25rem; font-weight:700; color:var(--ink); margin-bottom:0.5rem;">Líneas argumentales</h1>
    <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.95rem; line-height:1.75;">
      Narraciones que se conectan entre sí, formando series o historias con múltiples caminos posibles.
    </p>
  </div>

  @forelse($lineas as $linea)
    <div style="margin-bottom:2.5rem; padding-bottom:2.5rem; border-bottom:1px solid var(--parchment-dark);">
      <div style="display:flex; align-items:baseline; gap:1rem; margin-bottom:0.75rem; flex-wrap:wrap;">
        <h2 style="font-family:var(--serif); font-size:1.4rem; font-weight:700; color:var(--ink);">{{ $linea->titulo }}</h2>
        <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; color:{{ $linea->esRamificada() ? 'var(--gold)' : 'var(--ink-muted)' }};">
          <i class="bi {{ $linea->esRamificada() ? 'bi-diagram-3' : 'bi-arrow-right-circle' }}"></i>
          {{ $linea->tipo }}
        </span>
        <span style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted);">
          {{ $linea->nodos->count() }} {{ $linea->nodos->count() === 1 ? 'narración' : 'narraciones' }}
        </span>
      </div>

      @if($linea->descripcion)
        <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75; margin-bottom:1rem;">{{ $linea->descripcion }}</p>
      @endif

      {{-- Nodos raíz como punto de entrada --}}
      @php $raices = $linea->nodosRaiz(); @endphp
      @if($raices->isNotEmpty())
        <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">Empieza por</div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
          @foreach($raices as $raiz)
            <a href="{{ route('narraciones.show', $raiz->narracion->slug) }}"
               style="font-family:var(--sans); font-size:0.7rem; border:1px solid var(--accent); color:var(--accent); padding:0.35rem 0.85rem; text-decoration:none; transition:all 0.2s;"
               onmouseover="this.style.background='var(--accent)';this.style.color='var(--parchment)'"
               onmouseout="this.style.background='transparent';this.style.color='var(--accent)'">
              @if($raiz->etiqueta) {{ $raiz->etiqueta }} · @endif{{ $raiz->narracion->titulo }}
            </a>
          @endforeach
        </div>
      @endif
    </div>
  @empty
    <div style="text-align:center; padding:3rem; color:var(--ink-muted); font-family:var(--body);">
      Aún no hay líneas argumentales publicadas.
    </div>
  @endforelse

  <div style="margin-top:1rem;">
    <a href="{{ route('narraciones.index') }}" class="read-more">← Volver al catálogo</a>
  </div>

</div>
</div>
@endsection
