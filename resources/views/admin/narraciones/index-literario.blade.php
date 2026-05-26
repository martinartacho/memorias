@extends('layouts.literario')
@section('title', 'Narraciones · Admin · Memorias sin orden')

@push('styles')
<style>
/* ── Tabla admin narraciones ─────────────────────────────────────────────── */
.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-family: var(--sans);
    font-size: 0.78rem;
}
.admin-table th {
    background: var(--ink);
    color: var(--parchment);
    padding: 0.6rem 0.9rem;
    text-align: left;
    font-size: 0.6rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    white-space: nowrap;
    font-weight: 500;
}
.admin-table th a { color: var(--parchment); text-decoration: none; }
.admin-table th a:hover { text-decoration: underline; }
.admin-table th .sort-icon { opacity: 0.5; margin-left: 0.3em; }
.admin-table th.sorted .sort-icon { opacity: 1; }

.admin-table td {
    padding: 0.65rem 0.9rem;
    border-bottom: 1px solid var(--parchment-dark);
    vertical-align: top;
    color: var(--ink);
}
.admin-table tr:last-child td { border-bottom: none; }
.admin-table tr:hover td { background: #faf8f5; }

.admin-table .titulo-cell { max-width: 320px; }
.admin-table .titulo-cell strong {
    display: block;
    font-family: var(--body);
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 0.2rem;
    line-height: 1.3;
}
.admin-table .titulo-cell .snippet {
    font-size: 0.7rem;
    color: var(--ink-muted);
    line-height: 1.4;
}

.badge {
    display: inline-block;
    padding: 0.18rem 0.55rem;
    font-size: 0.58rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-weight: 600;
    border-radius: 2px;
    white-space: nowrap;
}
.badge-pub    { background: #dcfce7; color: #166534; }
.badge-bor    { background: #fef9c3; color: #713f12; }
.badge-prom   { background: #ede9fe; color: #5b21b6; }
.badge-pub2   { background: #dbeafe; color: #1e40af; }
.badge-seg    { background: #ede9fe; color: #5b21b6; }
.badge-priv   { background: #fee2e2; color: #991b1b; }

.admin-table .num-cell { text-align: right; color: var(--ink-muted); }

.tbl-action {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.28rem 0.65rem;
    font-size: 0.6rem; letter-spacing: 0.1em; text-transform: uppercase;
    border: 1px solid; cursor: pointer; text-decoration: none;
    white-space: nowrap; transition: background 0.15s;
}
.tbl-action-ver  { background: white; border-color: var(--parchment-dark); color: var(--ink); }
.tbl-action-ver:hover  { background: var(--parchment-dark); }
.tbl-action-edit { background: var(--ink); border-color: var(--ink); color: var(--parchment); }
.tbl-action-edit:hover { background: #333; }
.tbl-action-del  { background: white; border-color: #fca5a5; color: #dc2626; }
.tbl-action-del:hover  { background: #fef2f2; }

/* Barra búsqueda / filtros */
.filter-bar {
    display: flex; flex-wrap: wrap; gap: 0.6rem;
    align-items: center;
    margin-bottom: 1.25rem;
    padding: 0.85rem 1rem;
    background: var(--parchment);
    border: 1px solid var(--parchment-dark);
}
.filter-bar input[type=text] {
    flex: 1; min-width: 180px;
    border: 1px solid var(--parchment-dark);
    padding: 0.5rem 0.75rem;
    font-family: var(--body); font-size: 0.82rem;
    color: var(--ink); background: white; outline: none;
}
.filter-bar input[type=text]:focus { border-color: var(--accent); }
.filter-bar select {
    border: 1px solid var(--parchment-dark);
    padding: 0.5rem 0.75rem;
    font-family: var(--sans); font-size: 0.7rem;
    color: var(--ink); background: white; outline: none;
    letter-spacing: 0.05em; cursor: pointer;
}
.filter-bar select:focus { border-color: var(--accent); }
.filter-bar button[type=submit] {
    background: var(--ink); color: var(--parchment);
    border: 1px solid var(--ink);
    padding: 0.5rem 1.1rem;
    font-family: var(--sans); font-size: 0.62rem;
    letter-spacing: 0.15em; text-transform: uppercase;
    cursor: pointer; white-space: nowrap;
}
.filter-bar button[type=submit]:hover { background: #333; }
.filter-bar .reset-link {
    font-family: var(--sans); font-size: 0.62rem;
    color: var(--ink-muted); text-decoration: underline;
    white-space: nowrap; letter-spacing: 0.05em;
}

/* Stats */
.stats-row {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem; margin-bottom: 1.5rem;
}
.stat-box {
    text-align: center; padding: 0.85rem 0.5rem;
    border: 1px solid var(--parchment-dark);
    background: white;
}
.stat-box .num {
    font-family: var(--body); font-size: 2rem;
    font-weight: 700; line-height: 1;
    margin-bottom: 0.25rem;
}
.stat-box .lbl {
    font-family: var(--sans); font-size: 0.58rem;
    letter-spacing: 0.15em; text-transform: uppercase;
    color: var(--ink-muted);
}
</style>
@endpush

@section('content')
<main style="max-width:960px; margin:0 auto; padding:2rem 1.25rem 4rem;">

  {{-- ── Cabecera ── --}}
  <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:2rem; gap:1rem;">
    <div>
      <div class="section-label">Administración</div>
      <h2 style="font-family:var(--body); font-size:1.5rem; margin:0.25rem 0 0; color:var(--ink);">Narraciones</h2>
    </div>
    <a href="{{ route('admin.narraciones.create') }}" style="
        display:inline-flex; align-items:center; gap:0.4rem;
        background:var(--ink); color:var(--parchment); border:none;
        padding:0.7rem 1.25rem;
        font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase;
        text-decoration:none; white-space:nowrap; transition:background 0.2s;"
       onmouseover="this.style.background='#333'" onmouseout="this.style.background='var(--ink)'">
      <i class="bi bi-plus-lg"></i> Nueva narración
    </a>
  </div>

  {{-- ── Stats ── --}}
  <div class="stats-row">
    <div class="stat-box">
      <div class="num" style="color:var(--ink);">{{ $stats['total'] }}</div>
      <div class="lbl">Total</div>
    </div>
    <div class="stat-box">
      <div class="num" style="color:#166534;">{{ $stats['publicadas'] }}</div>
      <div class="lbl">Publicadas</div>
    </div>
    <div class="stat-box">
      <div class="num" style="color:#5b21b6;">{{ $stats['promocion'] }}</div>
      <div class="lbl">Promoción</div>
    </div>
    <div class="stat-box">
      <div class="num" style="color:#713f12;">{{ $stats['borradores'] }}</div>
      <div class="lbl">Borradores</div>
    </div>
  </div>

  {{-- ── Barra de búsqueda y filtros ── --}}
  <form method="GET" action="{{ route('admin.narraciones.index') }}" class="filter-bar">
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

    <button type="submit"><i class="bi bi-search"></i> Buscar</button>

    @if(request()->hasAny(['q','estado','permiso','orden']))
      <a href="{{ route('admin.narraciones.index') }}" class="reset-link">Limpiar</a>
    @endif
  </form>

  {{-- Resultado de búsqueda --}}
  @if(request('q'))
    <p style="font-family:var(--sans); font-size:0.7rem; color:var(--ink-muted); margin-bottom:1rem; letter-spacing:0.05em;">
      {{ $narraciones->total() }} resultado{{ $narraciones->total() === 1 ? '' : 's' }} para <em>«{{ request('q') }}»</em>
    </p>
  @endif

  {{-- ── Tabla ── --}}
  @if($narraciones->count() > 0)
    <div style="border:1px solid var(--parchment-dark); overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width:40%;">
              @php
                $tAsc  = request()->fullUrlWithQuery(['orden'=>'titulo_asc',  'page'=>1]);
                $tDesc = request()->fullUrlWithQuery(['orden'=>'titulo_desc', 'page'=>1]);
                $curOrden = request('orden','fecha_desc');
              @endphp
              @if($curOrden === 'titulo_asc')
                <a href="{{ $tDesc }}">Título <span class="sort-icon">↑</span></a>
              @elseif($curOrden === 'titulo_desc')
                <a href="{{ $tAsc }}">Título <span class="sort-icon">↓</span></a>
              @else
                <a href="{{ $tAsc }}">Título</a>
              @endif
            </th>
            <th>Estado</th>
            <th>Permiso</th>
            <th>
              @php
                $fAsc  = request()->fullUrlWithQuery(['orden'=>'fecha_asc',  'page'=>1]);
                $fDesc = request()->fullUrlWithQuery(['orden'=>'fecha_desc', 'page'=>1]);
              @endphp
              @if($curOrden === 'fecha_asc')
                <a href="{{ $fDesc }}">Fecha <span class="sort-icon">↑</span></a>
              @elseif($curOrden === 'fecha_desc')
                <a href="{{ $fAsc }}">Fecha <span class="sort-icon">↓</span></a>
              @else
                <a href="{{ $fDesc }}">Fecha</a>
              @endif
            </th>
            <th style="text-align:right;">
              @php $lDesc = request()->fullUrlWithQuery(['orden'=>'lecturas', 'page'=>1]); @endphp
              <a href="{{ $lDesc }}" style="color:var(--parchment);">
                Lecturas @if($curOrden === 'lecturas')<span class="sort-icon">↓</span>@endif
              </a>
            </th>
            <th style="text-align:center;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($narraciones as $narracion)
          <tr>
            {{-- Título + extracto --}}
            <td class="titulo-cell">
              <strong>{{ $narracion->titulo }}</strong>
              <span class="snippet">{{ Str::limit(strip_tags($narracion->contenido), 90) }}</span>
            </td>

            {{-- Estado --}}
            <td>
              @switch($narracion->estado)
                @case('publicado')
                  <span class="badge badge-pub">Publicado</span> @break
                @case('promocion')
                  <span class="badge badge-prom">Promoción</span> @break
                @default
                  <span class="badge badge-bor">Borrador</span>
              @endswitch
            </td>

            {{-- Permiso --}}
            <td>
              @switch($narracion->permiso_lectura)
                @case('publico')
                  <span class="badge badge-pub2">Público</span> @break
                @case('seguidores')
                  <span class="badge badge-seg">Seguidores</span> @break
                @case('privado')
                  <span class="badge badge-priv">Privado</span> @break
              @endswitch
            </td>

            {{-- Fecha --}}
            <td style="white-space:nowrap; font-size:0.72rem; color:var(--ink-muted);">
              {{ $narracion->fecha_publicacion->format('d/m/Y') }}
            </td>

            {{-- Lecturas --}}
            <td class="num-cell">{{ number_format($narracion->count_read) }}</td>

            {{-- Acciones --}}
            <td style="white-space:nowrap; text-align:center;">
              <div style="display:inline-flex; gap:0.35rem; align-items:center;">
                <a href="{{ route('narraciones.show', $narracion->slug) }}"
                   target="_blank" class="tbl-action tbl-action-ver" title="Ver en público">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('admin.narraciones.edit', $narracion->slug) }}"
                   class="tbl-action tbl-action-edit">
                  <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('admin.narraciones.destroy', $narracion->slug) }}"
                      method="POST" style="display:inline;"
                      onsubmit="return confirm('¿Eliminar «{{ addslashes($narracion->titulo) }}»? Esta acción no se puede deshacer.')">
                  @csrf
                  @method('DELETE')
                  <input type="hidden" name="confirmar_eliminacion" value="ELIMINAR_{{ $narracion->slug }}">
                  <button type="submit" class="tbl-action tbl-action-del" title="Eliminar">
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
    @if($narraciones->hasPages())
      <div style="margin-top:1.5rem; display:flex; justify-content:space-between; align-items:center; font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted); letter-spacing:0.05em;">
        <span>
          Mostrando {{ $narraciones->firstItem() }}–{{ $narraciones->lastItem() }} de {{ $narraciones->total() }}
        </span>
        <div>{{ $narraciones->links() }}</div>
      </div>
    @else
      <p style="margin-top:1rem; font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted); text-align:right; letter-spacing:0.05em;">
        {{ $narraciones->total() }} narración{{ $narraciones->total() === 1 ? '' : 'es' }} en total
      </p>
    @endif

  @else
    {{-- Estado vacío --}}
    <div style="text-align:center; padding:4rem 1rem; border:1px solid var(--parchment-dark); background:var(--parchment);">
      <i class="bi bi-file-earmark-text" style="font-size:3rem; color:var(--ink-muted); display:block; margin-bottom:1rem;"></i>
      @if(request()->hasAny(['q','estado','permiso']))
        <p style="font-family:var(--body); font-size:1rem; color:var(--ink); margin-bottom:0.5rem;">Sin resultados</p>
        <p style="font-family:var(--sans); font-size:0.72rem; color:var(--ink-muted); margin-bottom:1.5rem;">
          Prueba a ajustar los filtros de búsqueda.
        </p>
        <a href="{{ route('admin.narraciones.index') }}" class="read-more" style="font-size:0.62rem;">Quitar filtros</a>
      @else
        <p style="font-family:var(--body); font-size:1rem; color:var(--ink); margin-bottom:0.5rem;">No hay narraciones</p>
        <p style="font-family:var(--sans); font-size:0.72rem; color:var(--ink-muted); margin-bottom:1.5rem;">
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
  <div style="margin-top:2.5rem; text-align:center; border-top:1px solid var(--parchment-dark); padding-top:1.5rem; display:flex; justify-content:center; gap:2rem; flex-wrap:wrap;">
    <a href="{{ route('admin.lineas.index') }}" class="read-more" style="font-size:0.6rem;">Líneas argumentales</a>
    <a href="{{ route('admin.dictar') }}" class="read-more" style="font-size:0.6rem;"><i class="bi bi-mic"></i> Dictar</a>
    <a href="{{ route('dashboard') }}" class="read-more" style="font-size:0.6rem;">← Dashboard</a>
  </div>

</main>
@endsection
