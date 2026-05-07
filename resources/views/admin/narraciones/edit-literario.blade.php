@extends('layouts.literario')

@section('title', 'Editar Narración - Memorias sin orden')

@push('styles')
<link rel="stylesheet" href="https://cdn.tiny.cloud/7/tinymce/7/tinymce.min.css" />
@endpush

@push('styles')
<style>
.editor-toolbar {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-bottom: none;
    padding: 8px;
    border-radius: 8px 8px 0 0;
    display: flex;
    gap: 3px;
    align-items: center;
    flex-wrap: nowrap;
    overflow-x: auto;
}

.editor-btn {
    padding: 4px 8px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
    white-space: nowrap;
    min-width: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.editor-btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.editor-btn.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.editor-content {
    border: 1px solid #dee2e6;
    border-radius: 0 0 8px 8px;
    min-height: 400px;
    padding: 15px;
    background: white;
    font-family: Georgia, serif;
    font-size: 18px;
    line-height: 1.6;
    color: #374151;
    outline: none;
}

.editor-content:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.editor-separator {
    width: 1px;
    background: #dee2e6;
    margin: 0 2px;
    height: 20px;
}

.preview-container {
    background: #fafafa;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow-y: auto;
    max-height: 500px;
}

.preview-container h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 1.5rem 0 1rem 0;
    color: #111827;
}

.preview-container h2 {
    font-size: 2rem;
    font-weight: bold;
    margin: 1.25rem 0 0.75rem 0;
    color: #111827;
}

.preview-container h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 1rem 0 0.5rem 0;
    color: #111827;
}

.preview-container p {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.preview-container ul, .preview-container ol {
    margin: 1rem 0;
    padding-left: 2rem;
}

.preview-container li {
    margin-bottom: 0.5rem;
}

.preview-container a {
    color: #2563eb;
    text-decoration: underline;
}

.preview-container a:hover {
    color: #1d4ed8;
}

.preview-container img {
    max-width: 100%;
    height: auto;
    margin: 1rem 0;
    border-radius: 4px;
}

.preview-container blockquote {
    border-left: 4px solid #d1d5db;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6b7280;
}

.preview-container code {
    background: #f3f4f6;
    padding: 0.125rem 0.25rem;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.875rem;
}

.preview-container pre {
    background: #f3f4f6;
    padding: 1rem;
    border-radius: 4px;
    overflow-x: auto;
    margin: 1rem 0;
}

.preview-container pre code {
    background: none;
    padding: 0;
}
</style>
@endpush

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
          
          <!-- Contador de palabras y caracteres -->
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-stone-500 font-sans">
              Palabras: <span id="word-count" class="font-medium">0</span>
            </span>
            <span class="text-sm text-stone-500 font-sans">
              Caracteres: <span id="char-count" class="font-medium">0</span>
            </span>
          </div>
          
          <!-- Editor Local con Vista Previa -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Columna del Editor -->
              <div>
                  <h3 class="text-lg font-medium text-stone-700 mb-3">Editor</h3>
                  <div class="editor-container">
                      <div class="editor-toolbar">
                          <button type="button" class="editor-btn" data-command="formatBlock" data-value="h1" title="Título 1">
                              H1
                          </button>
                          <button type="button" class="editor-btn" data-command="formatBlock" data-value="h2" title="Título 2">
                              H2
                          </button>
                          <button type="button" class="editor-btn" data-command="formatBlock" data-value="h3" title="Título 3">
                              H3
                          </button>
                          |
                          <button type="button" class="editor-btn" data-command="bold" title="Negrita">
                              <strong>B</strong>
                          </button>
                          <button type="button" class="editor-btn" data-command="italic" title="Cursiva">
                              <em>I</em>
                          </button>
                          <button type="button" class="editor-btn" data-command="underline" title="Subrayado">
                              <u>U</u>
                          </button>
                          <button type="button" class="editor-btn" data-command="strikeThrough" title="Tachado">
                              <s>S</s>
                          </button>
                           |
                          <button type="button" class="editor-btn" data-command="formatBlock" data-value="p" title="Párrafo">
                              P
                          </button>
                          <button type="button" class="editor-btn" data-command="insertUnorderedList" title="Lista con viñetas">
                              •
                          </button>
                          <button type="button" class="editor-btn" data-command="insertOrderedList" title="Lista numerada">
                              1.
                          </button>
                           |
                          <button type="button" class="editor-btn" data-command="justifyLeft" title="Alinear izquierda">
                              ⬅
                          </button>
                          <button type="button" class="editor-btn" data-command="justifyCenter" title="Centrar">
                              ⬌
                          </button>
                          <button type="button" class="editor-btn" data-command="justifyRight" title="Alinear derecha">
                              ➡
                          </button>
                           | 
                          <button type="button" class="editor-btn" data-command="createLink" title="Insertar enlace">
                              🔗
                          </button>
                          <button type="button" class="editor-btn" id="image-btn" title="Subir imagen">
                              🖼️
                          </button>
                          <button type="button" class="editor-btn" data-command="removeFormat" title="Limpiar formato">
                              ✖
                          </button>
                      </div>
                      <div class="editor-content" id="editor-content" contenteditable="true">
                          {!! old('contenido', $narracion->contenido) !!}
                      </div>
                  </div>
              </div>
              
              <!-- Columna de Vista Previa -->
              <div>
                  <h3 class="text-lg font-medium text-stone-700 mb-3">Vista Previa</h3>
                  <div class="preview-container border border-stone-300 rounded-lg bg-white p-6 min-h-[400px] prose prose-lg max-w-none">
                      <div id="preview-content" class="font-serif text-lg leading-relaxed text-stone-800">
                          {!! old('contenido', $narracion->contenido) !!}
                      </div>
                  </div>
              </div>
          </div>
          <input type="hidden" name="contenido" id="contenido" value="{{ old('contenido', $narracion->contenido) }}">
          
          @error('contenido')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
          
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

        <!-- Orden -->
        <div>
          <label for="orden" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Orden de presentación
          </label>
          <input type="number" 
                 id="orden" 
                 name="orden" 
                 value="{{ old('orden', $narracion->orden) }}"
                 class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-sans"
                 placeholder="1000 = preferente">
          <p class="mt-2 text-sm text-stone-500 font-sans">
            Números más bajos aparecen primero (1000 = preferente)
          </p>
          @error('orden')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Permiso de lectura -->
        <div>
          <label class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Permiso de lectura
          </label>
          <div class="space-y-3">
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
              <input type="radio" 
                     name="permiso_lectura" 
                     value="publico" 
                     {{ old('permiso_lectura', $narracion->permiso_lectura) === 'publico' ? 'checked' : '' }}
                     class="mr-3 text-stone-600 focus:ring-stone-500">
              <div>
                <div class="font-sans font-medium text-stone-900">Público</div>
                <div class="font-sans text-sm text-stone-600">Visible para todos</div>
              </div>
            </label>
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
              <input type="radio" 
                     name="permiso_lectura" 
                     value="seguidores" 
                     {{ old('permiso_lectura') === 'seguidores' ? 'checked' : '' }}
                     class="mr-3 text-stone-600 focus:ring-stone-500">
              <div>
                <div class="font-sans font-medium text-stone-900">Seguidores</div>
                <div class="font-sans text-sm text-stone-600">Solo para usuarios registrados</div>
              </div>
            </label>
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
              <input type="radio" 
                     name="permiso_lectura" 
                     value="privado" 
                     {{ old('permiso_lectura') === 'privado' ? 'checked' : '' }}
                     class="mr-3 text-stone-600 focus:ring-stone-500">
              <div>
                <div class="font-sans font-medium text-stone-900">Privado</div>
                <div class="font-sans text-sm text-stone-600">Solo para mí</div>
              </div>
            </label>
          </div>
          @error('permiso_lectura')
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
    const wordCountEl = document.getElementById('word-count');
    const charCountEl = document.getElementById('char-count');
    const hiddenInput = document.getElementById('contenido');
    const editorContent = document.getElementById('editor-content');
    
    // Función para actualizar contador
    function updateWordCount() {
        const text = editorContent.textContent || editorContent.innerText || '';
        const words = text.trim().split(/\s+/).filter(word => word.length > 0).length;
        const chars = text.length;
        
        wordCountEl.textContent = words;
        charCountEl.textContent = chars;
    }
    
    // Actualizar input hidden cuando cambia el contenido
    function updateHiddenInput() {
        hiddenInput.value = editorContent.innerHTML;
    }
    
    // Actualizar vista previa cuando cambia el contenido
    function updatePreview() {
        const previewContent = document.getElementById('preview-content');
        previewContent.innerHTML = editorContent.innerHTML;
    }
    
    // Manejar clics en la toolbar
    document.querySelectorAll('.editor-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const command = this.dataset.command;
            const value = this.dataset.value || null;
            
            if (command === 'createLink') {
                const url = prompt('Introduce la URL del enlace:');
                if (url) {
                    document.execCommand(command, false, url);
                }
            } else {
                document.execCommand(command, false, value);
            }
            
            updateHiddenInput();
            updateWordCount();
            updatePreview();
            
            // Poner foco en el editor
            editorContent.focus();
        });
    });
    
    // Botón de imagen
    document.getElementById('image-btn').addEventListener('click', function(e) {
        e.preventDefault();
        
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.click();
        
        input.onchange = function() {
            const file = input.files[0];
            if (file) {
                uploadImage(file);
            }
        };
    });
    
    // Función para subir imágenes
    function uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('narracion_id', '{{ $narracion->id }}');

        fetch('{{ route("admin.narraciones.uploadImage") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const img = `<img src="${data.url}" alt="${data.alt || ''}" style="max-width: 100%; height: auto;">`;
                document.execCommand('insertHTML', false, img);
                updateHiddenInput();
                updateWordCount();
            } else {
                alert('Error al subir la imagen: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error al subir imagen:', error);
            alert('Error al subir la imagen');
        });
    }
    
    // Eventos de entrada
    editorContent.addEventListener('input', function() {
        updateHiddenInput();
        updateWordCount();
        updatePreview();
    });
    
    editorContent.addEventListener('paste', function(e) {
        setTimeout(function() {
            updateHiddenInput();
            updateWordCount();
            updatePreview();
        }, 100);
    });
    
    // Actualizar botones activos según la selección
    document.addEventListener('selectionchange', function() {
        if (document.activeElement === editorContent) {
            updateToolbarState();
        }
    });
    
    function updateToolbarState() {
        document.querySelectorAll('.editor-btn').forEach(btn => {
            const command = btn.dataset.command;
            if (command && document.queryCommandState(command)) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
    
    // Inicializar
    updateHiddenInput();
    updateWordCount();
    updatePreview();
    updateToolbarState();
    
    console.log('Editor local inicializado correctamente');
});
</script>
</script>
@endsection
