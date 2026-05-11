@extends('layouts.literario')

@section('title', 'Narraciones - Memorias sin orden')

@push('styles')
<style>
/* ── FIX: Aislamos el layout del contenedor padre (.container es block, no grid) */
.narraciones-layout {
  display: block !important;
  width: 100% !important;
  max-width: 100% !important;
  clear: both !important;
  margin: 0 auto !important;
  padding: 0 !important;
  box-sizing: border-box !important;
}

/* ── FIX: Definimos el grid explícitamente aquí, no dependemos del CSS padre */
.stories-grid {
  display: grid !important;
  grid-template-columns: repeat(3, 1fr) !important;
  gap: 1.5rem !important;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

@media (max-width: 1024px) {
  .stories-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 640px) {
  .stories-grid { grid-template-columns: 1fr !important; }
}

.story-card {
  display: block;
  width: 100%;
  box-sizing: border-box;
}

/* ── FIX: Featured debe ser block para no interferir con su grid interno */
.featured {
  display: block !important;
  width: 100% !important;
  max-width: 100% !important;
}

/* ── FIX: Paginación fuera del flujo del grid, siempre ancho completo */
.pagination-outer {
  display: block !important;
  width: 100% !important;
  max-width: 100% !important;
  clear: both !important;
  text-align: center !important;
  margin-top: 3rem !important;
  padding-top: 2rem !important;
  box-sizing: border-box !important;
}

.pagination-outer .pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.pagination-outer .pagination li {
  display: inline-block;
  margin: 0 2px;
}

.pagination-outer .pagination li a,
.pagination-outer .pagination li span {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2.5rem;
  height: 2.5rem;
  padding: 0 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s;
}

.pagination-outer .pagination li.active span {
  background-color: #9333ea;
  color: white;
  border-color: #9333ea;
}

.pagination-outer .pagination li a:hover {
  background-color: #f3f4f6;
  color: #374151;
}
</style>
@endpush

@section('content')
<main class="container">

  @if($narraciones->count() > 0)
    @php
        $featured = $narraciones->first();
        $total_count = $narraciones->total();
    @endphp

    {{-- FEATURED --}}
    <div class="featured">
      <div class="featured-meta">
        <div class="section-label">Destacado</div>
        <div class="featured-number">{{ str_pad($narraciones->firstItem(), 2, '0', STR_PAD_LEFT) }}</div>
        <div class="featured-category">Narración · {{ $featured->fecha_publicacion->format('Y') }}</div>
      </div>
      <div class="featured-divider"></div>
      <div class="featured-text">
        <div class="featured-author">Publicado · {{ $featured->fecha_publicacion->format('F Y') }}</div>
        <h2>{!! Str::limit($featured->titulo, 50) !!}</h2>
        <p class="dropcap">{!! Str::limit($featured->contenido, 300) !!}</p>
        <a href="{{ route('narraciones.show', $featured->slug) }}" class="read-more">Leer narración →</a>
      </div>
    </div>

    {{-- ── FIX: Wrapper que aísla grid + paginación del contenedor padre ── --}}
    <div class="narraciones-layout">

      <div class="grid-label">
        Todas las narraciones <span>{{ $total_count }} textos</span>
      </div>

      <div class="stories-grid">
        @foreach($narraciones as $key => $narracion)
          <div class="story-card" onclick="window.location.href='{{ route('narraciones.show', $narracion->slug) }}'">
            <div class="card-num">{{ str_pad($narraciones->firstItem() + $key, 2, '0', STR_PAD_LEFT) }}</div>
            <div class="card-tag">
              Narración
              <span class="ml-2">
                @switch($narracion->permiso_lectura)
                  @case('publico')
                    <span class="material-icons text-blue-600" title="Público">public</span>
                    @break
                  @case('seguidores')
                    @php
                      $isFollowing = auth()->check() && auth()->user()->following()->where('followed_id', $narracion->user_id)->exists();
                    @endphp
                    @if($isFollowing)
                      <span class="material-icons text-green-600" title="Acceso permitido">lock_open</span>
                    @else
                      <span class="material-icons text-red-600" title="Solo seguidores">lock</span>
                    @endif
                    @break
                @endswitch
              </span>
            </div>
            <div class="card-title">{{ $narracion->titulo }}</div>
            <div class="card-author">{{ $narracion->fecha_publicacion->format('F Y') }}</div>
            <p class="card-excerpt">{!! Str::limit(strip_tags($narracion->contenido), 120) !!}</p>
          </div>
        @endforeach
      </div>

      {{-- ── FIX: Paginación con clase dedicada, sin depender de Tailwind --}}
      <div class="pagination-outer">
        {{ $narraciones->links('vendor.pagination.bootstrap-4') }}
      </div>

    </div>{{-- /.narraciones-layout --}}

  @else
    <div class="text-center py-20">
      <div class="text-stone-400 mb-8">
        <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
      </div>
      <h3 class="text-2xl font-serif font-medium text-stone-900 mb-4">No hay narraciones publicadas</h3>
      <p class="text-stone-600 text-lg">Las primeras historias pronto estarán disponibles.</p>
    </div>
  @endif

</main>
@endsection