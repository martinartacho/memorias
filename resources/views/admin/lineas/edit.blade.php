@extends('layouts.literario')
@section('title', 'Editar línea · ' . $linea->titulo . ' · Memorias sin orden')

@push('styles')
<style>
/* ── Drag & drop ── */
.sortable-item {
    display:flex; align-items:center; gap:0.75rem;
    padding:0.9rem 1.25rem; border-bottom:1px solid var(--parchment-dark);
    background:#fff; transition:background 0.15s;
    user-select:none; -webkit-user-select:none;
}
.sortable-item:last-child { border-bottom:none; }
.sortable-item.sortable-ghost  { background:var(--parchment-mid); opacity:0.6; }
.sortable-item.sortable-chosen { background:#f0f4ff; box-shadow:0 2px 8px rgba(0,0,0,0.1); }

.drag-handle {
    color:var(--ink-muted); cursor:grab; font-size:1.1rem;
    flex-shrink:0; padding:0 2px;
}
.drag-handle:active { cursor:grabbing; }

.node-arrow {
    text-align:center; color:var(--accent); font-size:1rem;
    padding:0.15rem 1.25rem; font-family:var(--sans);
}
</style>
@endpush

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
    <div id="flash-ok" style="background:#f0fdf4; border:1px solid #86efac; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem; color:#166534;">
      <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div style="background:#fef2f2; border:1px solid #fca5a5; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem; color:#991b1b;">
      <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    </div>
  @endif

  {{-- Aviso AJAX (drag & drop) --}}
  <div id="ajax-msg" style="display:none; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem;"></div>

  <div style="display:grid; grid-template-columns:1fr 340px; gap:1.5rem; align-items:start;">

    {{-- ══ COLUMNA IZQUIERDA ══ --}}
    <div>

      {{-- ── LINEAL: lista arrastrable ── --}}
      @if($linea->esLineal())

        <div style="background:#fff; border:1px solid var(--parchment-dark); margin-bottom:1.5rem;">

          {{-- Cabecera con contador y botón guardar orden --}}
          <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--parchment-dark); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
            <div style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">
              <i class="bi bi-list-ol"></i> Narraciones · arrastra para reordenar ({{ $linea->nodos->count() }})
            </div>
            <button id="save-order-btn" type="button" style="display:none; background:var(--accent); color:var(--parchment); border:none; padding:0.4rem 1rem; font-family:var(--sans); font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;">
              <i class="bi bi-floppy"></i> Guardar orden
            </button>
          </div>

          @if($linea->nodos->isEmpty())
            <div style="padding:2rem; text-align:center; color:var(--ink-muted); font-family:var(--sans); font-size:0.8rem;">
              Añade narraciones desde el panel de la derecha.
            </div>
          @else
            <ul id="sortable-list" style="list-style:none; margin:0; padding:0;">
              @foreach($linea->nodos as $i => $nodo)
                <li class="sortable-item" data-id="{{ $nodo->id }}">
                  {{-- Posición --}}
                  <span class="node-pos" style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted); min-width:1.5rem; text-align:right;">{{ $i + 1 }}</span>
                  {{-- Flecha --}}
                  @if(!$loop->first)
                    {{-- se muestra solo visualmente; la flecha real está en el CSS de .node-arrow --}}
                  @endif
                  {{-- Handle --}}
                  <span class="drag-handle bi bi-grip-vertical"></span>
                  {{-- Info --}}
                  <div style="flex:1; min-width:0;">
                    @if($nodo->etiqueta)
                      <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.15em; color:var(--gold); border:1px solid var(--gold); padding:0.1rem 0.4rem; margin-right:0.4rem;">{{ $nodo->etiqueta }}</span>
                    @endif
                    <span style="font-family:var(--serif); font-weight:700; color:var(--ink);">{{ $nodo->narracion->titulo }}</span>
                  </div>
                  {{-- Quitar --}}
                  <form action="{{ route('admin.lineas.nodos.remove', [$linea, $nodo]) }}" method="POST"
                        onsubmit="return confirm('¿Retirar «{{ addslashes($nodo->narracion->titulo) }}» de la línea?')" style="flex-shrink:0;">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none; border:none; color:#dc2626; cursor:pointer; font-size:0.8rem;" title="Retirar">
                      <i class="bi bi-x-circle"></i>
                    </button>
                  </form>
                </li>
              @endforeach
            </ul>

            {{-- Nota: conexiones automáticas --}}
            @if($linea->nodos->count() > 1)
              <div style="padding:0.75rem 1.25rem; background:var(--parchment-mid); border-top:1px solid var(--parchment-dark); font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted);">
                <i class="bi bi-info-circle"></i>
                Las conexiones se crean automáticamente según el orden. Al guardar: {{ $linea->nodos->count() - 1 }} conexión(es) en cadena.
              </div>
            @endif
          @endif
        </div>

      {{-- ── RAMIFICADA: lista estática con conexiones manuales ── --}}
      @else

        <div style="background:#fff; border:1px solid var(--parchment-dark); margin-bottom:1.5rem;">
          <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--parchment-dark);">
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

      @endif

    </div>

    {{-- ══ COLUMNA DERECHA: formularios ══ --}}
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
          @if($linea->esRamificada())
          <div style="margin-bottom:0.75rem;">
            <input type="text" name="etiqueta" placeholder="Etiqueta (ej: 1.1, opcional)"
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink); outline:none;">
          </div>
          @endif
          <button type="submit"
            style="width:100%; background:var(--ink); color:var(--parchment); border:none; padding:0.6rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;">
            Añadir →
          </button>
        </form>
      </div>

      {{-- Añadir conexión (solo ramificada) --}}
      @if($linea->esRamificada() && $linea->nodos->count() >= 2)
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
            <input type="text" name="etiqueta" placeholder="Texto del enlace (ej: Con violencia…)"
              style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.5rem 0.75rem; font-family:var(--body); font-size:0.85rem; color:var(--ink); outline:none;">
          </div>
          <button type="submit"
            style="width:100%; background:var(--accent); color:var(--parchment); border:none; padding:0.6rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;">
            Conectar →
          </button>
        </form>
      </div>
      @endif

      {{-- Configuración de la línea --}}
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

@if($linea->esLineal() && $linea->nodos->count() > 1)
{{-- SortableJS solo para líneas lineales con nodos --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
(function () {
    const list    = document.getElementById('sortable-list');
    const saveBtn = document.getElementById('save-order-btn');
    const msgBox  = document.getElementById('ajax-msg');

    if (!list) return;

    const sortable = Sortable.create(list, {
        animation: 180,
        handle: '.drag-handle',
        ghostClass:  'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function () {
            // Actualizar números de posición visualmente
            list.querySelectorAll('.sortable-item').forEach((el, i) => {
                const pos = el.querySelector('.node-pos');
                if (pos) pos.textContent = i + 1;
            });
            saveBtn.style.display = 'inline-block';
        }
    });

    saveBtn.addEventListener('click', function () {
        const orden = Array.from(list.querySelectorAll('[data-id]'))
                          .map(el => el.dataset.id);

        saveBtn.disabled     = true;
        saveBtn.textContent  = 'Guardando…';

        fetch('{{ route('admin.lineas.reordenar', $linea) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ orden })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showMsg(data.message, '#f0fdf4', '#166534', '#86efac');
                saveBtn.style.display = 'none';
            } else {
                showMsg(data.error || 'Error al guardar.', '#fef2f2', '#991b1b', '#fca5a5');
            }
        })
        .catch(() => showMsg('Error de conexión.', '#fef2f2', '#991b1b', '#fca5a5'))
        .finally(() => {
            saveBtn.disabled    = false;
            saveBtn.innerHTML   = '<i class="bi bi-floppy"></i> Guardar orden';
        });
    });

    function showMsg(text, bg, color, border) {
        msgBox.style.cssText = `display:block; background:${bg}; color:${color}; border:1px solid ${border};`;
        msgBox.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + text;
        setTimeout(() => msgBox.style.display = 'none', 4000);
    }
})();
</script>
@endif

@endsection
