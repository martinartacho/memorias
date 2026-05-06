@extends('layouts.literario')

@section('title', 'Editar Narración - Memorias sin orden')

@section('content')
<main class="container">
  <div class="mb-8">
    <div class="flex items-center mb-6">
      <a href="{{ route('admin.narraciones.index') }}" 
         class="text-stone-600 hover:text-stone-900 mr-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
      </a>
      <div>
        <h2 class="text-3xl font-serif font-bold text-stone-900">Editar Narración</h2>
        <p class="text-stone-600 mt-2">Modifica tu historia literaria</p>
      </div>
    </div>
  </div>

  <!-- Form -->
  <div class="bg-white rounded-lg border border-stone-300 overflow-hidden">
    <form id="edit-form" action="{{ route('admin.narraciones.update', $narracion->slug) }}" method="POST">
      @csrf
      @method('PUT')
      
      <div class="p-8 space-y-8">
        <!-- Título -->
        <div>
          <label for="titulo" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Título <span class="text-red-600">*</span>
          </label>
          <input type="text" 
                 id="titulo" 
                 name="titulo" 
                 value="{{ old('titulo', $narracion->titulo) }}"
                 class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-serif text-lg"
                 placeholder="Escribe un título cautivador..."
                 required>
          @error('titulo')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Contenido -->
        <div>
          <label for="contenido" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Contenido <span class="text-red-600">*</span>
          </label>
          <textarea id="contenido" 
                    name="contenido" 
                    rows="15"
                    class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-serif text-lg leading-relaxed"
                    placeholder="Escribe tu narración aquí...">{{ old('contenido', $narracion->contenido) }}</textarea>
          @error('contenido')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
          <p class="mt-2 text-sm text-stone-500 font-sans">
            Puedes usar saltos de línea para separar párrafos.
          </p>
        </div>

        <!-- Fecha de Publicación -->
        <div>
          <label for="fecha_publicacion" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Fecha de Publicación <span class="text-red-600">*</span>
          </label>
          <input type="date" 
                 id="fecha_publicacion" 
                 name="fecha_publicacion" 
                 value="{{ old('fecha_publicacion', $narracion->fecha_publicacion->format('Y-m-d')) }}"
                 class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-sans"
                 required>
          @error('fecha_publicacion')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Estado -->
        <div>
          <label class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Estado <span class="text-red-600">*</span>
          </label>
          <div class="space-y-3">
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
              <input type="radio" 
                     name="estado" 
                     value="borrador" 
                     {{ old('estado', $narracion->estado) === 'borrador' ? 'checked' : '' }}
                     class="mr-3 text-stone-600 focus:ring-stone-500">
              <div>
                <div class="font-sans font-medium text-stone-900">Borrador</div>
                <div class="font-sans text-sm text-stone-600">No visible públicamente</div>
              </div>
            </label>
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
              <input type="radio" 
                     name="estado" 
                     value="publicado" 
                     {{ old('estado', $narracion->estado) === 'publicado' ? 'checked' : '' }}
                     class="mr-3 text-stone-600 focus:ring-stone-500">
              <div>
                <div class="font-sans font-medium text-stone-900">Publicado</div>
                <div class="font-sans text-sm text-stone-600">Visible públicamente</div>
              </div>
            </label>
          </div>
          @error('estado')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Preview -->
        <div class="border-t border-stone-200 pt-8">
          <h3 class="text-xl font-serif font-bold text-stone-900 mb-6">Vista Previa</h3>
          <div class="bg-stone-50 rounded-lg p-8 border border-stone-200">
            <h4 class="text-2xl font-serif font-bold text-stone-900 mb-4" id="preview-title">
              {{ old('titulo', $narracion->titulo) }}
            </h4>
            <div class="font-sans text-sm text-stone-600 mb-4" id="preview-date">
              {{ old('fecha_publicacion', $narracion->fecha_publicacion->format('Y-m-d')) }}
            </div>
            <div class="font-serif text-stone-800 whitespace-pre-wrap leading-relaxed" id="preview-content">
              {{ old('contenido', $narracion->contenido) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="px-8 py-6 bg-stone-100 border-t border-stone-300">
        <div class="flex justify-between items-center">
          <div class="space-x-4">
            @if($narracion->estado === 'publicado')
              <a href="{{ route('narraciones.show', $narracion->slug) }}" 
                 target="_blank"
                 class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-stone-600 bg-white border border-stone-300 hover:bg-stone-50 transition-colors no-underline">
                Ver Publicada
              </a>
            @endif
          </div>
          <div class="space-x-4">
            <a href="{{ route('admin.narraciones.index') }}" 
               class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-stone-600 bg-white border border-stone-300 hover:bg-stone-50 transition-colors no-underline">
              Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-white bg-stone-700 hover:bg-stone-600 transition-colors">
              Actualizar
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tituloInput = document.getElementById('titulo');
    const contenidoInput = document.getElementById('contenido');
    const fechaInput = document.getElementById('fecha_publicacion');
    const previewTitle = document.getElementById('preview-title');
    const previewContent = document.getElementById('preview-content');
    const previewDate = document.getElementById('preview-date');

    function updatePreview() {
        previewTitle.textContent = tituloInput.value || 'Tu título aparecerá aquí';
        previewContent.textContent = contenidoInput.value || 'Tu contenido aparecerá aquí...';
        
        if (fechaInput.value) {
            const date = new Date(fechaInput.value);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            previewDate.textContent = date.toLocaleDateString('es-ES', options);
        }
    }

    // Log para detectar envío del formulario
    document.getElementById('edit-form').addEventListener('submit', function(e) {
        console.log('=== FORMULARIO EDIT ENVIADO ===');
        console.log('Action:', this.action);
        console.log('Method:', this.method);
        console.log('FormData:', new FormData(this));
    });

    tituloInput.addEventListener('input', updatePreview);
    contenidoInput.addEventListener('input', updatePreview);
    fechaInput.addEventListener('change', updatePreview);
});
</script>
@endsection
