@extends('layouts.literario')
@section('title', 'Nueva línea argumental · ' . __('site.name'))

@section('content')
<div class="container">
<div style="max-width:720px; margin:0 auto;">

  <div style="margin-bottom:2rem;">
    <div class="section-label">Líneas argumentales</div>
    <h1 style="font-family:var(--serif); font-size:2rem; font-weight:700; color:var(--ink);">Nueva línea</h1>
  </div>

  <div style="background:#fff; border:1px solid var(--parchment-dark); padding:2rem 2.5rem;">
    <form action="{{ route('admin.lineas.store') }}" method="POST">
      @csrf

      {{-- Título --}}
      <div style="margin-bottom:1.5rem;">
        <label style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); display:block; margin-bottom:0.5rem;">
          Título *
        </label>
        <input type="text" name="titulo" value="{{ old('titulo') }}" required
          style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.75rem 1rem; font-family:var(--body); font-size:0.95rem; color:var(--ink); outline:none;"
          onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--parchment-dark)'"
          placeholder="Ej: La máquina de escribir">
        @error('titulo')<p style="color:#dc2626; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
      </div>

      {{-- Descripción --}}
      <div style="margin-bottom:1.5rem;">
        <label style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); display:block; margin-bottom:0.5rem;">
          Descripción (opcional)
        </label>
        <textarea name="descripcion" rows="3"
          style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.75rem 1rem; font-family:var(--body); font-size:0.9rem; color:var(--ink); resize:vertical; outline:none;"
          onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--parchment-dark)'"
          placeholder="Breve descripción de la serie o historia ramificada…">{{ old('descripcion') }}</textarea>
      </div>

      {{-- Tipo --}}
      <div style="margin-bottom:1.5rem;">
        <label style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); display:block; margin-bottom:0.75rem;">
          Tipo de línea *
        </label>
        <div style="display:flex; gap:1.5rem; flex-wrap:wrap;">
          @foreach(['lineal' => ['icon'=>'bi-arrow-right-circle','desc'=>'Secuencia simple: I → II → III'], 'ramificada' => ['icon'=>'bi-diagram-3','desc'=>'Historia con caminos y opciones']] as $val => $opt)
            <label style="display:flex; align-items:flex-start; gap:0.75rem; cursor:pointer; padding:1rem 1.25rem; border:1px solid var(--parchment-dark); flex:1; min-width:200px;">
              <input type="radio" name="tipo" value="{{ $val }}" {{ old('tipo','lineal') === $val ? 'checked' : '' }} style="margin-top:0.2rem;">
              <div>
                <div style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink);">
                  <i class="bi {{ $opt['icon'] }}"></i> {{ ucfirst($val) }}
                </div>
                <div style="font-family:var(--body); font-size:0.8rem; color:var(--ink-muted); margin-top:0.2rem;">{{ $opt['desc'] }}</div>
              </div>
            </label>
          @endforeach
        </div>
      </div>

      {{-- Pública --}}
      <div style="margin-bottom:2rem;">
        <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer;">
          <input type="checkbox" name="publica" value="1" {{ old('publica') ? 'checked' : '' }}>
          <div>
            <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink);">Visible en el índice público</span>
            <div style="font-family:var(--body); font-size:0.8rem; color:var(--ink-muted);">Los lectores podrán ver esta línea en <code>/lineas</code></div>
          </div>
        </label>
      </div>

      <div style="display:flex; gap:1rem; align-items:center;">
        <button type="submit"
          style="background:var(--ink); color:var(--parchment); border:none; padding:0.75rem 1.75rem; font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;"
          onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--ink)'">
          Crear línea →
        </button>
        <a href="{{ route('admin.lineas.index') }}" class="read-more">Cancelar</a>
      </div>

    </form>
  </div>

</div>
</div>
@endsection
