@extends('layouts.literario')
@section('title', 'Líneas argumentales · Admin · Memorias sin orden')

@section('content')
<div class="container">
<div style="max-width:900px; margin:0 auto;">

  {{-- Cabecera --}}
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
    <div>
      <div class="section-label">Administración</div>
      <h1 style="font-family:var(--serif); font-size:2rem; font-weight:700; color:var(--ink);">Líneas argumentales</h1>
    </div>
    <div style="display:flex; gap:1rem; align-items:center;">
      <a href="{{ route('admin.lineas.create') }}"
         style="background:var(--accent); color:var(--parchment); font-family:var(--sans); font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.65rem 1.4rem; text-decoration:none;">
        + Nueva línea
      </a>
      <a href="{{ route('dashboard') }}" class="read-more">← Dashboard</a>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #86efac; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem; color:#166534;">
      <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
  @endif

  @if($lineas->isEmpty())
    <div style="background:#fff; border:1px solid var(--parchment-dark); padding:3rem; text-align:center;">
      <i class="bi bi-diagram-3" style="font-size:2.5rem; color:var(--parchment-dark);"></i>
      <p style="font-family:var(--sans); font-size:0.8rem; color:var(--ink-muted); margin-top:1rem;">
        Aún no hay líneas argumentales. Crea la primera para conectar narraciones.
      </p>
    </div>
  @else
    <div style="background:#fff; border:1px solid var(--parchment-dark);">
      {{-- Cabecera tabla --}}
      <div style="display:grid; grid-template-columns:1fr 100px 100px 120px; padding:0.75rem 1.5rem; background:var(--parchment-mid); border-bottom:2px solid var(--parchment-dark);">
        @foreach(['Título','Tipo','Nodos','Acciones'] as $col)
          <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">{{ $col }}</div>
        @endforeach
      </div>

      @foreach($lineas as $linea)
        @php
          $tipoColor = $linea->esRamificada() ? 'var(--gold)' : 'var(--ink-muted)';
          $tipoIcon  = $linea->esRamificada() ? 'bi-diagram-3' : 'bi-arrow-right-circle';
        @endphp
        <div style="display:grid; grid-template-columns:1fr 100px 100px 120px; padding:1rem 1.5rem; border-bottom:1px solid var(--parchment-dark); align-items:center;">

          <div>
            <div style="font-family:var(--serif); font-weight:700; color:var(--ink);">{{ $linea->titulo }}</div>
            @if($linea->descripcion)
              <div style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted); margin-top:0.2rem;">{{ Str::limit($linea->descripcion, 60) }}</div>
            @endif
            @if($linea->publica)
              <span style="font-family:var(--sans); font-size:0.55rem; letter-spacing:0.2em; color:var(--accent);">● PÚBLICA</span>
            @endif
          </div>

          <div>
            <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.15em; text-transform:uppercase; color:{{ $tipoColor }};">
              <i class="bi {{ $tipoIcon }}"></i> {{ $linea->tipo }}
            </span>
          </div>

          <div style="font-family:var(--serif); font-size:1.25rem; font-weight:700; color:var(--ink);">
            {{ $linea->nodos->count() }}
          </div>

          <div style="display:flex; gap:0.75rem;">
            <a href="{{ route('admin.lineas.edit', $linea) }}" style="font-family:var(--sans); font-size:0.62rem; color:var(--ink-muted);">
              <i class="bi bi-pencil"></i> Editar
            </a>
            <form action="{{ route('admin.lineas.destroy', $linea) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar «{{ $linea->titulo }}»?')">
              @csrf @method('DELETE')
              <button type="submit" style="background:none; border:none; font-family:var(--sans); font-size:0.62rem; color:#dc2626; cursor:pointer; padding:0;">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </form>
          </div>

        </div>
      @endforeach
    </div>
  @endif

</div>
</div>
@endsection
