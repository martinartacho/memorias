@extends('layouts.literario')

@section('title', 'Narraciones - Memorias sin orden')

@section('content')
<main class="container">

  <!-- FEATURED -->
  @if($narraciones->count() > 0)
    @php
        $featured = $narraciones->first();
        $total_count = $narraciones->total();
    @endphp
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

    <!-- GRID -->
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
                  <span class="material-icons text-purple-600" title="Solo seguidores">group</span>
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

    <!-- Pagination -->
    <div class="mt-12">
        {{ $narraciones->links('vendor.pagination.bootstrap-4') }}
    </div>

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
