@extends('layouts.literario')

@section('title', 'Administración - Memorias sin orden')

@section('content')
<main class="container">
  <div class="text-center mb-12">
    <h2 class="text-3xl font-serif font-bold text-stone-900 mb-4">Administración</h2>
    <p class="text-stone-600">Gestiona tus narraciones literarias</p>
    <div class="mt-6">
      <a href="{{ route('admin.narraciones.create') }}" 
         class="inline-flex items-center px-6 py-3 bg-stone-900 text-stone-100 font-sans text-xs tracking-widest uppercase hover:bg-stone-800 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Nueva Narración
      </a>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-white rounded-lg border border-stone-300 p-6 text-center">
      <div class="text-4xl font-serif font-bold text-stone-700 mb-2">{{ $narraciones->total() }}</div>
      <div class="text-sm font-sans text-stone-600 tracking-wider uppercase">Total Narraciones</div>
    </div>

    <div class="bg-white rounded-lg border border-stone-300 p-6 text-center">
      <div class="text-4xl font-serif font-bold text-green-700 mb-2">{{ App\Models\Narracion::where('estado', 'publicado')->count() }}</div>
      <div class="text-sm font-sans text-stone-600 tracking-wider uppercase">Publicadas</div>
    </div>

    <div class="bg-white rounded-lg border border-stone-300 p-6 text-center">
      <div class="text-4xl font-serif font-bold text-yellow-700 mb-2">{{ App\Models\Narracion::where('estado', 'borrador')->count() }}</div>
      <div class="text-sm font-sans text-stone-600 tracking-wider uppercase">Borradores</div>
    </div>
  </div>

  <!-- Narraciones Grid -->
  <div class="space-y-6">
    @if($narraciones->count() > 0)
      @foreach($narraciones as $narracion)
        <div class="bg-white rounded-lg border border-stone-300 overflow-hidden hover:shadow-lg transition-shadow duration-300">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-0">
            <!-- Content Section -->
            <div class="md:col-span-3 p-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <h3 class="text-xl font-serif font-bold text-stone-900 mb-2 leading-tight">
                    {{ $narracion->titulo }}
                  </h3>
                  <div class="font-sans text-sm text-stone-600 leading-relaxed mb-3">
                    {!! Str::limit(strip_tags($narracion->contenido), 120) !!}
                  </div>
                </div>
                <div class="ml-4">
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
              
              <div class="flex items-center justify-between">
                <div class="font-sans text-xs text-stone-500 tracking-wider uppercase">
                  <div>Publicado: {{ $narracion->fecha_publicacion->format('d \d\e F \d\e Y') }}</div>
                  <div>Creado: {{ $narracion->created_at->format('d \d\e F \d\e Y') }}</div>
                </div>
              </div>
            </div>
            
            <!-- Actions Section -->
            <div class="border-l border-stone-200 bg-stone-50 p-4 flex flex-col justify-center space-y-3">
              <div>
                <a href="{{ route('narraciones.show', $narracion->slug) }}" 
                 target="_blank"
                 class="flex items-center justify-center px-4 py-2 bg-white border border-stone-300 rounded-lg font-sans text-xs tracking-wider uppercase text-stone-700 hover:bg-stone-100 hover:border-stone-400 transition-colors">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Ver
              </a>
              </div>
              <div>
              <a href="{{ route('admin.narraciones.edit', $narracion->id) }}" 
                 class="flex items-center justify-center px-4 py-2 bg-stone-900 text-white rounded-lg font-sans text-xs tracking-wider uppercase hover:bg-stone-800 transition-colors">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
              </a>
              </div>
              <div>
              <form action="{{ route('admin.narraciones.destroy', $narracion->id) }}" 
                    method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="flex items-center justify-center w-full px-4 py-2 bg-white border border-red-300 text-red-600 rounded-lg font-sans text-xs tracking-wider uppercase hover:bg-red-50 hover:border-red-400 transition-colors"
                        onclick="return confirm('¿Estás seguro de eliminar esta narración?')">
                  <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Eliminar
                </button>
              </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach

      <!-- Pagination -->
      <div class="flex justify-center pt-8">
        {{ $narraciones->links() }}
      </div>
    @else
      <div class="text-center py-16">
        <div class="text-stone-400 mb-6">
          <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-serif font-medium text-stone-900 mb-3">No hay narraciones</h3>
        <p class="text-stone-600 mb-6">Comienza creando tu primera narración literaria.</p>
        <a href="{{ route('admin.narraciones.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-stone-900 text-stone-100 font-sans text-xs tracking-widest uppercase hover:bg-stone-800 transition-colors">
          Crear Primera Narración
        </a>
      </div>
    @endif
  </div>
</main>
@endsection
