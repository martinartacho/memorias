@extends('layouts.literario')

@section('title', 'Administración - Memorias sin orden')

@push('styles')
<style>
.narraciones-container {
  display: block;
  width: 100%;
}

.narracion-item {
  display: block;
  width: 100%;
}

.pagination-container {
  display: flex;
  justify-content: center;
  padding-top: 2rem;
  margin-top: 2rem;
  clear: both;
}

/* Asegurar que la paginación no flote */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
}
</style>
@endpush

@section('content')
<main class="container">
  <div class="mb-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-3xl font-serif font-bold text-stone-900 mb-2">Administración</h2>
        <p class="text-stone-600">Gestiona tus narraciones literarias</p>
      </div>
      <a href="{{ route('admin.narraciones.create') }}" 
         class="inline-flex items-center px-6 py-3 bg-stone-700 text-white font-sans text-xs tracking-widest uppercase hover:bg-stone-600 transition-colors shadow-sm">
        <i class="bi bi-plus-circle icon-sm mr-2"></i>
        Nueva Narración
      </a>
    </div>
  </div>

  <!-- Stats Row -->
  <div class="bg-white rounded-lg border border-stone-300 p-6 mb-12">
    <div class="grid grid-cols-3 gap-8 text-center">
      <div>
        <div class="text-3xl font-serif font-bold text-stone-700 mb-1">{{ $narraciones->total() }}</div>
        <div class="text-sm font-sans text-stone-600 tracking-wider uppercase">Total Narraciones</div>
      </div>
      <div>
        <div class="text-3xl font-serif font-bold text-green-700 mb-1">{{ App\Models\Narracion::where('estado', 'publicado')->count() }}</div>
        <div class="text-sm font-sans text-stone-600 tracking-wider uppercase">Publicadas</div>
      </div>
      <div>
        <div class="text-3xl font-serif font-bold text-yellow-700 mb-1">{{ App\Models\Narracion::where('estado', 'borrador')->count() }}</div>
        <div class="text-sm font-sans text-stone-600 tracking-wider uppercase">Borradores</div>
      </div>
    </div>
  </div>

  <!-- Narraciones Grid -->
  <div class="space-y-6 narraciones-container">
    @if($narraciones->count() > 0)
      @foreach($narraciones as $narracion)
        <div class="bg-white rounded-lg border border-stone-300 overflow-hidden hover:shadow-lg transition-shadow duration-300 narracion-item">
          <div class="p-6">
            <!-- Header with Title and Status -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1 pr-4">
                <h3 class="text-xl font-serif font-bold text-stone-900 mb-2 leading-tight">
                  {{ $narracion->titulo }}
                </h3>
                <div class="font-sans text-sm text-stone-600 leading-relaxed">
                  {!! Str::limit(strip_tags($narracion->contenido), 120) !!}
                </div>
              </div>
              <div class="flex-shrink-0">
                @if($narracion->estado === 'publicado')
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-sans font-medium bg-green-100 text-green-800 border border-green-200">
                    Publicado
                  </span>
                @else
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-sans font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                    Borrador
                  </span>
                @endif
              </div>
            </div>
            
            <!-- Meta Section -->
            <div class="font-sans text-xs text-stone-500 tracking-wider uppercase mb-4">
              <div class="grid grid-cols-4 gap-6 text-center">
                <div>
                  <div class="text-stone-400 mb-1">Publicado</div>
                  <div class="text-stone-700 font-medium">{{ $narracion->fecha_publicacion->format('M') }}</div>
                </div>
                <div>
                  <div class="text-stone-400 mb-1">Orden</div>
                  @if($narracion->orden == 1000)
                    <div class="text-green-700 font-medium">Preferente</div>
                  @else
                    <div class="text-stone-600">Automático</div>
                  @endif
                </div>
                <div>
                  <div class="text-stone-400 mb-1">Lectores</div>
                  <div class="text-stone-600">{{ $narracion->count_read }}</div>
                </div>
                <div>
                  <div class="text-stone-400 mb-1">Permisos</div>
                  @switch($narracion->permiso_lectura)
                    @case('publico')
                      <div class="text-blue-700 font-medium">Público</div>
                      @break
                    @case('seguidores')
                      <div class="text-purple-700 font-medium">Seguidores</div>
                      @break
                    @case('privado')
                      <div class="text-red-700 font-medium">Privado</div>
                      @break
                  @endswitch
                </div>
              </div>
            </div>
             
            <!-- Actions Section -->
            <div class="flex items-center justify-between border-t border-stone-200 pt-4">
              <div class="text-xs font-sans text-stone-600 tracking-wider uppercase">
                Acciones
              </div>
              <div class="flex items-center gap-2">
                <a href="{{ route('narraciones.show', $narracion->slug) }}" 
                   target="_blank"
                   class="inline-flex items-center px-3 py-1.5 bg-white border border-stone-300 rounded font-sans text-xs tracking-wider text-stone-700 hover:bg-stone-100 hover:border-stone-400 transition-colors btn-compact-sm no-underline">
                  <i class="bi bi-eye icon-xs mr-1"></i>
                  Ver
                </a>
                <a href="{{ route('admin.narraciones.edit', $narracion->slug) }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-stone-700 text-white border border-stone-700 rounded font-sans text-xs tracking-wider hover:bg-stone-600 hover:border-stone-600 transition-colors btn-compact-sm no-underline">
                  <i class="bi bi-pencil icon-xs mr-1"></i>
                  Editar
                </a>
                <form action="{{ route('admin.narraciones.destroy', $narracion->slug) }}" 
                      method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                  <input type="hidden" name="confirmar_eliminacion" value="ELIMINAR_{{ $narracion->slug }}">
                  <button type="submit" 
                          class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 text-red-600 rounded font-sans text-xs tracking-wider hover:bg-red-50 hover:border-red-400 transition-colors btn-compact-sm"
                          onclick="return confirm('¿Estás seguro de eliminar esta narración?')">
                    <i class="bi bi-trash icon-xs mr-1"></i>
                    Eliminar
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach

      <!-- Pagination -->
      <div class="pagination-container flex justify-center pt-8 mt-8">
        {{ $narraciones->links() }}
      </div>
    @else
      <div class="text-center py-16">
        <div class="text-stone-400 mb-6">
          <i class="bi bi-file-text text-6xl"></i>
        </div>
        <h3 class="text-2xl font-serif font-medium text-stone-900 mb-3">No hay narraciones</h3>
        <p class="text-stone-600 mb-6">Comienza creando tu primera narración literaria.</p>
        <a href="{{ route('admin.narraciones.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-stone-700 text-stone-100 font-sans text-xs tracking-widest uppercase hover:bg-stone-600 transition-colors">
          <i class="bi bi-plus-circle icon-sm mr-2"></i>
          Crear Primera Narración
        </a>
      </div>
    @endif
  </div>
</main>
@endsection
