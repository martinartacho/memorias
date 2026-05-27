@extends('layouts.literario')
@section('title', 'Narraciones · Admin · ' . __('site.name'))

@push('styles')
<style>
/* ─── Admin narraciones ──────────────────────────────────────────────────── */

/* Stats */
.an-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.an-stat {
    text-align: center;
    padding: 1rem 0.5rem;
    border: 1px solid #d6cfc4;
    background: #fff;
}
.an-stat .n {
    font-family: var(--body);
    font-size: 2.1rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.3rem;
}
.an-stat .l {
    font-family: var(--sans);
    font-size: 0.58rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--ink-muted);
}

/* Barra de filtros */
.an-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.8rem 0.9rem;
    background: #faf8f5;
    border: 1px solid #d6cfc4;
}
.an-filters input[type=text] {
    flex: 1;
    min-width: 160px;
    border: 1px solid #c8bfb4;
    padding: 0.45rem 0.7rem;
    font-family: var(--body);
    font-size: 0.82rem;
    color: var(--ink);
    background: #fff;
    outline: none;
}
.an-filters input[type=text]:focus { border-color: var(--accent); }
.an-filters select {
    border: 1px solid #c8bfb4;
    padding: 0.45rem 0.6rem;
    font-family: var(--sans);
    font-size: 0.68rem;
    color: var(--ink);
    background: #fff;
    outline: none;
    cursor: pointer;
}
.an-filters select:focus { border-color: var(--accent); }
.an-filters .f-btn {
    background: var(--ink);
    color: var(--parchment);
    border: none;
    padding: 0.45rem 1rem;
    font-family: var(--sans);
    font-size: 0.6rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    cursor: pointer;
    white-space: nowrap;
}
.an-filters .f-btn:hover { background: #333; }
.an-filters .f-reset {
    font-family: var(--sans);
    font-size: 0.62rem;
    color: var(--ink-muted);
    text-decoration: underline;
    white-space: nowrap;
}

/* Tabla */
.an-table {
    width: 100%;
    border-collapse: collapse;
    font-family: var(--sans);
    font-size: 0.78rem;
    border: 1px solid #c8bfb4;
}
.an-table thead th {
    background: #2c2c2c;
    color: #f5f0e8;
    padding: 0.6rem 0.85rem;
    text-align: left;
    font-size: 0.58rem;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    white-space: nowrap;
    font-weight: 500;
    border-bottom: 2px solid #111;
}
.an-table thead th a { color: #f5f0e8; text-decoration: none; }
.an-table thead th a:hover { text-decoration: underline; }

.an-table tbody tr:nth-child(even) td { background: #faf8f5; }
.an-table tbody tr:nth-child(odd)  td { background: #ffffff; }
.an-table tbody tr:hover td { background: #f3ede4 !important; }

.an-table tbody td {
    padding: 0.7rem 0.85rem;
    border-bottom: 1px solid #ddd6cc;
    vertical-align: top;
    color: #2c2c2c;
}
.an-table tbody tr:last-child td { border-bottom: none; }

.an-title-strong {
    display: block;
    font-family: var(--body);
    font-size: 0.88rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.2rem;
    line-height: 1.3;
}
.an-snippet {
    font-size: 0.68rem;
    color: #7a6f63;
    line-height: 1.4;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 0.2rem 0.55rem;
    font-size: 0.58rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    border-radius: 2px;
    white-space: nowrap;
}
.b-pub  { background: #dcfce7; color: #14532d; border: 1px solid #bbf7d0; }
.b-prom { background: #ede9fe; color: #4c1d95; border: 1px solid #ddd6fe; }
.b-bor  { background: #fef9c3; color: #713f12; border: 1px solid #fde68a; }
.b-pub2 { background: #dbeafe; color: #1e3a8a; border: 1px solid #bfdbfe; }
.b-seg  { background: #ede9fe; color: #4c1d95; border: 1px solid #ddd6fe; }
.b-priv { background: #fee2e2; color: #7f1d1d; border: 1px solid #fecaca; }

/* Acciones */
.an-action {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.28rem 0.6rem;
    font-size: 0.6rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border: 1px solid;
    cursor: pointer;
    text-decoration: none !important;
    white-space: nowrap;
    transition: background 0.12s;
    line-height: 1;
}
.an-ver  { background: #fff; border-color: #c8bfb4; color: #3c3c3c; }
.an-ver:hover  { background: #f0ebe3; }
.an-edit { background: #2c2c2c; border-color: #2c2c2c; color: #f5f0e8; }
.an-edit:hover { background: #444; border-color: #444; }
.an-del  { background: #fff; border-color: #fca5a5; color: #dc2626; }
.an-del:hover  { background: #fef2f2; }
</style>
@endpush

@section('content')
<main style="max-width:960px; margin:0 auto; padding:2rem 1.25rem 4rem;">

  {{-- ── Cabecera ── --}}
  <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.75rem; gap:1rem; flex-wrap:wrap;">
    <div>
      <div class="section-label">Administración</div>
      <h2 style="font-family:var(--body); font-size:1.5rem; margin:0.25rem 0 0; color:var(--ink);">Narraciones</h2>
    </div>
    <a href="{{ route('admin.narraciones.create') }}"
       style="display:inline-flex; align-items:center; gap:0.4rem;
              background:var(--ink); color:var(--parchment);
              padding:0.7rem 1.25rem; text-decoration:none;
              font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase;"
       onmouseover="this.style.background='#444'" onmouseout="this.style.background='var(--ink)'">
      <i class="bi bi-plus-lg"></i> Nueva narración
    </a>
  </div>

  {{-- ── Stats ── --}}
  <div class="an-stats">
    <div class="an-stat">
      <div class="n" style="color:#2c2c2c;">{{ $stats['total'] }}</div>
      <div class="l">Total</div>
    </div>
    <div class="an-stat">
      <div class="n" style="color:#14532d;">{{ $stats['publicadas'] }}</div>
      <div class="l">Publicadas</div>
    </div>
    <div class="an-stat">
      <div class="n" style="color:#4c1d95;">{{ $stats['promocion'] }}</div>
      <div class="l">Promoción</div>
    </div>
    <div class="an-stat">
      <div class="n" style="color:#713f12;">{{ $stats['borradores'] }}</div>
      <div class="l">Borradores</div>
    </div>
  </div>

  {{-- ── Barra de filtros ── --}}
  <form method="GET" action="{{ route('admin.narraciones.index') }}" class="an-filters">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por título o contenido…">

    <select name="estado">
      <option value="">Todos los estados</option>
      <option value="publicado" @selected(request('estado') === 'publicado')>Publicado</option>
      <option value="promocion" @selected(request('estado') === 'promocion')>Promoción</option>
      <option value="borrador"  @selected(request('estado') === 'borrador')>Borrador</option>
    </select>

    <select name="permiso">
      <option value="">Todos los permisos</option>
      <option value="publico"    @selected(request('permiso') === 'publico')>Público</option>
      <option value="seguidores" @selected(request('permiso') === 'seguidores')>Seguidores</option>
      <option value="privado"    @selected(request('permiso') === 'privado')>Privado</option>
    </select>

    <select name="orden">
      <option value="fecha_desc" @selected(request('orden','fecha_desc') === 'fecha_desc')>Más reciente</option>
      <option value="fecha_asc"  @selected(request('orden') === 'fecha_asc')>Más antigua</option>
      <option value="titulo_asc" @selected(request('orden') === 'titulo_asc')>Título A–Z</option>
      <option value="titulo_desc"@selected(request('orden') === 'titulo_desc')>Título Z–A</option>
      <option value="lecturas"   @selected(request('orden') === 'lecturas')>Más leídas</option>
    </select>

    <button type="submit" class="f-btn"><i class="bi bi-search"></i> Buscar</button>

    @if(request()->hasAny(['q','estado','permiso','orden']))
      <a href="{{ route('admin.narraciones.index') }}" class="f-reset">Limpiar</a>
    @endif
  </form>

  {{-- Resumen de búsqueda --}}
  @if(request('q'))
    <p style="font-family:var(--sans); font-size:0.7rem; color:var(--ink-muted); margin-bottom:0.75rem; letter-spacing:0.04em;">
      {{ $narraciones->total() }} resultado{{ $narraciones->total() === 1 ? '' : 's' }} para <em>«{{ request('q') }}»</em>
    </p>
  @endif

  {{-- ── Tabla ── --}}
  @if($narraciones->count() > 0)
    <div style="overflow-x:auto;">
      <table class="an-table">
        <thead>
          <tr>
            @php
              $cur = request('orden','fecha_desc');
              $qs  = fn($p) => request()->fullUrlWithQuery(array_merge($p, ['page'=>1]));
            @endphp
            <th style="width:38%;">
              @if($cur === 'titulo_asc')
                <a href="{{ $qs(['orden'=>'titulo_desc']) }}">Título ↑</a>
              @elseif($cur === 'titulo_desc')
                <a href="{{ $qs(['orden'=>'titulo_asc']) }}">Título ↓</a>
              @else
                <a href="{{ $qs(['orden'=>'titulo_asc']) }}">Título</a>
              @endif
            </th>
            <th>Estado</th>
            <th>Permiso</th>
            <th>
              @if($cur === 'fecha_asc')
                <a href="{{ $qs(['orden'=>'fecha_desc']) }}">Fecha ↑</a>
              @elseif($cur === 'fecha_desc')
                <a href="{{ $qs(['orden'=>'fecha_asc']) }}">Fecha ↓</a>
              @else
                <a href="{{ $qs(['orden'=>'fecha_desc']) }}">Fecha</a>
              @endif
            </th>
            <th style="text-align:right;">
              <a href="{{ $qs(['orden'=>'lecturas']) }}" style="color:#f5f0e8;">
                Lecturas @if($cur==='lecturas') ↓ @endif
              </a>
            </th>
            <th style="text-align:center;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($narraciones as $narracion)
          <tr>
            <td>
              <span class="an-title-strong">{{ $narracion->titulo }}</span>
              <span class="an-snippet">{{ Str::limit(strip_tags($narracion->contenido), 90) }}</span>
            </td>
            <td>
              @switch($narracion->estado)
                @case('publicado') <span class="badge b-pub">Publicado</span>  @break
                @case('promocion') <span class="badge b-prom">Promoción</span> @break
                @default           <span class="badge b-bor">Borrador</span>
              @endswitch
            </td>
            <td>
              @switch($narracion->permiso_lectura)
                @case('publico')    <span class="badge b-pub2">Público</span>     @break
                @case('seguidores') <span class="badge b-seg">Seguidores</span>   @break
                @case('privado')    <span class="badge b-priv">Privado</span>     @break
              @endswitch
            </td>
            <td style="white-space:nowrap; font-size:0.72rem; color:#7a6f63;">
              {{ $narracion->fecha_publicacion->format('d/m/Y') }}
            </td>
            <td style="text-align:right; color:#7a6f63;">
              {{ number_format($narracion->count_read) }}
            </td>
            <td style="white-space:nowrap; text-align:center;">
              <div style="display:inline-flex; gap:0.3rem; align-items:center;">
                <a href="{{ route('narraciones.show', $narracion->slug) }}"
                   target="_blank" class="an-action an-ver" title="Ver en público">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('admin.narraciones.edit', $narracion->slug) }}"
                   class="an-action an-edit">
                  <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('admin.narraciones.destroy', $narracion->slug) }}"
                      method="POST" style="display:inline;"
                      onsubmit="return confirm('¿Eliminar «{{ addslashes($narracion->titulo) }}»?\nEsta acción no se puede deshacer.')">
                  @csrf
                  @method('DELETE')
                  <input type="hidden" name="confirmar_eliminacion" value="ELIMINAR_{{ $narracion->slug }}">
                  <button type="submit" class="an-action an-del" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Paginación --}}
    <div style="margin-top:1.25rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem; font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted); letter-spacing:0.04em;">
      <span>
        Mostrando {{ $narraciones->firstItem() }}–{{ $narraciones->lastItem() }} de {{ $narraciones->total() }}
      </span>
      {{ $narraciones->links() }}
    </div>

  @else
    {{-- Estado vacío --}}
    <div style="text-align:center; padding:3.5rem 1rem; border:1px solid #d6cfc4; background:#faf8f5;">
      <i class="bi bi-file-earmark-text" style="font-size:2.5rem; color:#b0a898; display:block; margin-bottom:0.75rem;"></i>
      @if(request()->hasAny(['q','estado','permiso']))
        <p style="font-family:var(--body); font-size:1rem; color:var(--ink); margin-bottom:0.4rem;">Sin resultados</p>
        <p style="font-family:var(--sans); font-size:0.72rem; color:var(--ink-muted); margin-bottom:1.25rem;">
          Prueba a ajustar los filtros.
        </p>
        <a href="{{ route('admin.narraciones.index') }}" class="read-more" style="font-size:0.62rem;">Quitar filtros</a>
      @else
        <p style="font-family:var(--body); font-size:1rem; color:var(--ink); margin-bottom:0.4rem;">No hay narraciones</p>
        <p style="font-family:var(--sans); font-size:0.72rem; color:var(--ink-muted); margin-bottom:1.25rem;">
          Comienza creando tu primera narración.
        </p>
        <a href="{{ route('admin.narraciones.create') }}" style="
              display:inline-flex; align-items:center; gap:0.4rem;
              background:var(--ink); color:var(--parchment);
              padding:0.7rem 1.5rem; text-decoration:none;
              font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase;">
          <i class="bi bi-plus-lg"></i> Nueva narración
        </a>
      @endif
    </div>
  @endif

  {{-- Accesos rápidos --}}
  <div style="margin-top:2.5rem; text-align:center; border-top:1px solid #d6cfc4; padding-top:1.5rem; display:flex; justify-content:center; gap:2rem; flex-wrap:wrap;">
    <a href="{{ route('admin.lineas.index') }}" class="read-more" style="font-size:0.6rem;">Líneas argumentales</a>
    <a href="{{ route('admin.dictar') }}" class="read-more" style="font-size:0.6rem;"><i class="bi bi-mic"></i> Dictar</a>
    <a href="{{ route('dashboard') }}" class="read-more" style="font-size:0.6rem;">← Dashboard</a>
  </div>

</main>
@endsection
