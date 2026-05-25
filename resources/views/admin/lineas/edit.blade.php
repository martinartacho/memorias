@extends('layouts.literario')
@section('title', 'Editar línea · ' . $linea->titulo . ' · Memorias sin orden')

@section('content')
<div class="container">
<div style="max-width:960px; margin:0 auto;">

  {{-- Cabecera --}}
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
    <div>
      <div class="section-label">{{ $linea->tipo === 'ramificada' ? 'Línea ramificada' : 'Línea lineal' }}</div>
      <h1 style="font-family:var(--serif); font-size:2rem; font-weight:700; color:var(--ink);">{{ $linea->titulo }}</h1>
    </div>
    <a href="{{ route('admin.lineas.index') }}" class="read-more">← Todas las líneas</a>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #86efac; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem; color:#166534;">
      <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div style="background:#fef2f2; border:1px solid #fca5a5; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem; color:#991b1b;">
      <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    </div>
  @endif

  <div style="display:grid; grid-template-columns:1fr 340px; gap:1.5rem; align-items:start;">

    {{-- ── COLUMNA IZQUIERDA: grafo actual ── --}}
    <div>

      {{-- Nodos actuales --}}
      <div style="background:#fff; border:1px solid var(--parchment-dark); margin-bottom:1.5rem;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--parchment-dark); display:flex; justify-content:space-between; align-items:center;">
          <div style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">
            Narraciones en esta línea ({{ $linea->nodos->count() }})
          </div>
        </div>

        @if($linea->nodos->isEmpty())
          <div style="padding:2rem; text-align:center; color:var(--ink-muted); font-family:var(--sans); font-size:0.8rem;">
            Añade narraciones desde el panel de la derecha.
          </div>
        @else
          @foreach($linea->nodos as $nodo)
            <div style="padding:1rem 1.5rem; border-bottom:1px solid var(--parchment-dark);">

              {{-- Nodo --}}
              <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem;">
                <div>
                  @if($nodo->etiqueta)
                    <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.2em; color:var(--gold); border:1px solid var(--gold); padding:0.1rem 0.4rem; margin-right:0.5rem;">{{ $nodo->etiqueta }}</span>
                  @endif
                  <span style="font-family:var(--serif); font-weight:700; color:var(--ink);">{{ $nodo->narracion->titulo }}</span>
                  <div style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted); margin-top:0.2rem;">
                    @if($nodo->esRaiz()) <span style="color:var(--accent);">● inicio</span> &nbsp; @endif
                    @if($nodo->esHoja()) <span style="color:var(--ink-muted);">● fin</span> @endif
                  </div>
                </div>
                <form action="{{ route('admin.lineas.nodos.remove', [$linea, $nodo]) }}" method="POST"
                      onsubmit="return confirm('¿Retirar esta narración de la línea?')">
                  @csrf @method('DELETE')
                  <button type="submit" style="background:none; border:none; color:#dc2626; cursor:pointer; font-size:0.75rem;" title="Retirar">
                    <i class="bi bi-x-circle"></i>
                  </button>
                </form>
              </div>

              {{-- Conexiones salientes de este nodo --}}
              @if($nodo->conexionesSalientes->isNotEmpty())
                <div style="margin-top:0.75rem; padding-left:1rem; border-left:2px solid var(--parchment-dark);">
                  @foreach($nodo->conexionesSalientes as $cx)
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.35rem;">
                      <span style="font-family:var(--sans); font-size:0.7rem; color:var(--ink-soft);">
                        <i class="bi bi-arrow-right" style="color:var(--accent);"></i>
                        @if($cx->etiqueta) <strong>{{ $cx->etiqueta }}</strong> · @endif
                        {{ $cx->hasta?->narracion?->titulo ?? '—' }}
                      </span>
                      <form action="{{ route('admin.lineas.conexiones.remove', [$linea, $cx]) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:#dc2626; cursor:pointer; font-size:0.7rem;">
                          <i class="bi bi-x"></i>
                        </button>
                      </form>
                    </div>
                  @endforeach
                </div>
              @endif

            </div>
          @endforeach
        @endif
      </div>

    </div>

    {{-- ── COLUMNA DERECHA: formularios ── --}}
    <div style="display:flex; flex-direction:column; gap:1.25rem;">

      {{-- Añadir narración --}}
      <div style="background:#fff; border:1px solid var(--parchment-dark); padding:1.5rem;">
        <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:1rem;">
          <i class="bi bi-plus-circle"></i> Añadir narración
        </div>
        <form action="{{ route('admin.lineas.nodos.add', $linea) }}" method="POST">
          @csrf
          <div style="margin-bottom:0.75rem;">
            <select name="narracion_id" required
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink);">
              <option value="">— Selecciona narración —</option>
              @foreach($disponibles as $n)
                @if(!in_array($n->id, $enLinea))
                  <option value="{{ $n->id }}">{{ $n->titulo }}</option>
                @endif
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:0.75rem;">
            <input type="text" name="etiqueta" placeholder="Etiqueta (ej: 1.1, opcional)"
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink); outline:none;">
          </div>
          <button type="submit"
            style="width:100%; background:var(--ink); color:var(--parchment); border:none; padding:0.6rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;">
            Añadir →
          </button>
        </form>
      </div>

      {{-- Añadir conexión --}}
      @if($linea->nodos->count() >= 2)
      <div style="background:#fff; border:1px solid var(--parchment-dark); padding:1.5rem;">
        <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:1rem;">
          <i class="bi bi-arrow-right-circle"></i> Añadir conexión
        </div>
        <form action="{{ route('admin.lineas.conexiones.add', $linea) }}" method="POST">
          @csrf
          <div style="margin-bottom:0.75rem;">
            <label style="font-family:var(--sans); font-size:0.6rem; color:var(--ink-muted); display:block; margin-bottom:0.3rem;">Desde</label>
            <select name="from_id" required
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink);">
              <option value="">— Narración origen —</option>
              @foreach($linea->nodos as $n)
                <option value="{{ $n->id }}">{{ $n->etiqueta ? '['.$n->etiqueta.'] ' : '' }}{{ $n->narracion->titulo }}</option>
              @endforeach
            </select>
          </div>
          <div style="text-align:center; color:var(--accent); font-size:1.2rem; margin-bottom:0.75rem;">↓</div>
          <div style="margin-bottom:0.75rem;">
            <label style="font-family:var(--sans); font-size:0.6rem; color:var(--ink-muted); display:block; margin-bottom:0.3rem;">Hasta</label>
            <select name="to_id" required
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink);">
              <option value="">— Narración destino —</option>
              @foreach($linea->nodos as $n)
                <option value="{{ $n->id }}">{{ $n->etiqueta ? '['.$n->etiqueta.'] ' : '' }}{{ $n->narracion->titulo }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:0.75rem;">
            <input type="text" name="etiqueta" placeholder="Texto del enlace (ej: Con violencia, 1.1…)"
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink); outline:none;">
          </div>
          <button type="submit"
            style="width:100%; background:var(--accent); color:var(--parchment); border:none; padding:0.6rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;">
            Conectar →
          </button>
        </form>
      </div>
      @endif

      {{-- Metadatos de la línea --}}
      <div style="background:#fff; border:1px solid var(--parchment-dark); padding:1.5rem;">
        <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:1rem;">
          <i class="bi bi-gear"></i> Configuración
        </div>
        <form action="{{ route('admin.lineas.update', $linea) }}" method="POST">
          @csrf @method('PUT')
          <div style="margin-bottom:0.75rem;">
            <input type="text" name="titulo" value="{{ $linea->titulo }}" required
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink); outline:none;"
              placeholder="Título">
          </div>
          <div style="margin-bottom:0.75rem;">
            <textarea name="descripcion" rows="2"
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink); resize:vertical; outline:none;"
              placeholder="Descripción…">{{ $linea->descripcion }}</textarea>
          </div>
          <div style="margin-bottom:0.75rem; display:flex; gap:1rem;">
            @foreach(['lineal','ramificada'] as $t)
              <label style="display:flex; align-items:center; gap:0.4rem; font-family:var(--sans); font-size:0.65rem; cursor:pointer;">
                <input type="radio" name="tipo" value="{{ $t }}" {{ $linea->tipo === $t ? 'checked' : '' }}>
                {{ ucfirst($t) }}
              </label>
            @endforeach
          </div>
          <div style="margin-bottom:0.75rem;">
            <label style="display:flex; align-items:center; gap:0.5rem; font-family:var(--sans); font-size:0.65rem; cursor:pointer;">
              <input type="checkbox" name="publica" value="1" {{ $linea->publica ? 'checked' : '' }}>
              Visible en índice público
            </label>
          </div>
          <button type="submit"
            style="width:100%; background:var(--ink); color:var(--parchment); border:none; padding:0.6rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;">
            Guardar cambios
          </button>
        </form>
      </div>

    </div>
  </div>

</div>
</div>
@endsection
