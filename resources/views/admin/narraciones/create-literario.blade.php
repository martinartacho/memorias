@extends('layouts.literario')

@section('title', 'Crear Narración - ' . __('site.name'))

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
    position: sticky;
    top: 0;
    z-index: 50;
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

.editor-btn:hover { background: #e9ecef; border-color: #adb5bd; }
.editor-btn.active { background: #007bff; color: white; border-color: #007bff; }

.editor-scroll-wrap {
    height: 480px;
    overflow-y: scroll;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 8px 8px;
    background: white;
}

.editor-scroll-wrap::-webkit-scrollbar        { width: 10px; }
.editor-scroll-wrap::-webkit-scrollbar-track  { background: #f1f1f1; }
.editor-scroll-wrap::-webkit-scrollbar-thumb  { background: #b0b0b0; border-radius: 4px; }
.editor-scroll-wrap::-webkit-scrollbar-thumb:hover { background: #888; }

.editor-content {
    min-height: 100%;
    padding: 15px;
    background: white;
    font-family: Georgia, serif;
    font-size: 18px;
    line-height: 1.6;
    color: #374151;
    outline: none;
}

.editor-scroll-wrap:focus-within {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.editor-separator { width: 1px; background: #dee2e6; margin: 0 2px; height: 20px; }

.preview-container p  { margin-bottom: 1rem; line-height: 1.6; }
.preview-container ul, .preview-container ol { margin: 1rem 0; padding-left: 2rem; }
.preview-container li { margin-bottom: 0.5rem; }
.preview-container blockquote { border-left: 4px solid #d1d5db; padding-left: 1rem; margin: 1rem 0; font-style: italic; color: #6b7280; }
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
        <h2 class="text-3xl font-serif font-bold text-stone-900">Crear Nueva Narración</h2>
        <p class="text-stone-600 mt-2">Comparte una nueva historia con el mundo</p>
      </div>
    </div>
  </div>

  <!-- Form -->
  <div class="bg-white rounded-lg border border-stone-300 overflow-hidden">
    <form action="{{ route('admin.narraciones.store') }}" method="POST">
      @csrf

      <div class="p-8 space-y-8">

        <!-- Título -->
        <div>
          <label for="titulo" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Título <span class="text-red-600">*</span>
          </label>
          <input type="text"
                 id="titulo"
                 name="titulo"
                 value="{{ old('titulo') }}"
                 class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-serif text-lg"
                 placeholder="Escribe un título cautivador..."
                 required>
          @error('titulo')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Contenido: editor rich text -->
        <div>
          <label class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
            Contenido <span class="text-red-600">*</span>
          </label>

          <!-- Contador palabras/caracteres -->
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-stone-500 font-sans">
              Palabras: <span id="word-count" class="font-medium">0</span>
            </span>
            <span class="text-sm text-stone-500 font-sans">
              Caracteres: <span id="char-count" class="font-medium">0</span>
            </span>
          </div>

          <!-- Toolbar -->
          <div class="editor-toolbar">
            <button type="button" class="editor-btn" data-command="formatBlock" data-value="h1" title="Título 1">H1</button>
            <button type="button" class="editor-btn" data-command="formatBlock" data-value="h2" title="Título 2">H2</button>
            <button type="button" class="editor-btn" data-command="formatBlock" data-value="h3" title="Título 3">H3</button>
            <div class="editor-separator"></div>
            <button type="button" class="editor-btn" data-command="bold"          title="Negrita"><strong>B</strong></button>
            <button type="button" class="editor-btn" data-command="italic"        title="Cursiva"><em>I</em></button>
            <button type="button" class="editor-btn" data-command="underline"     title="Subrayado"><u>U</u></button>
            <button type="button" class="editor-btn" data-command="strikeThrough" title="Tachado"><s>S</s></button>
            <div class="editor-separator"></div>
            <button type="button" class="editor-btn" data-command="formatBlock"        data-value="p" title="Párrafo">P</button>
            <button type="button" class="editor-btn" data-command="insertUnorderedList" title="Lista viñetas">•</button>
            <button type="button" class="editor-btn" data-command="insertOrderedList"   title="Lista numerada">1.</button>
            <div class="editor-separator"></div>
            <button type="button" class="editor-btn" data-command="justifyLeft"   title="Izquierda">⬅</button>
            <button type="button" class="editor-btn" data-command="justifyCenter" title="Centrar">⬌</button>
            <button type="button" class="editor-btn" data-command="justifyRight"  title="Derecha">➡</button>
            <div class="editor-separator"></div>
            <button type="button" class="editor-btn" data-command="createLink"    title="Insertar enlace">🔗</button>
            <button type="button" class="editor-btn" data-command="removeFormat"  title="Limpiar formato">✖</button>
            <div class="editor-separator"></div>
            {{-- Dictado por voz --}}
            <button type="button" id="dictate-btn" class="editor-btn"
                    title="Dictar por voz (es-ES) — Chrome/Edge"
                    style="gap:4px; display:flex; align-items:center;">
              <i class="bi bi-mic" id="dictate-icon"></i>
              <span id="dictate-label" style="font-size:11px;">Dictar</span>
            </button>
          </div>

          {{-- Aviso dictado activo --}}
          <div id="dictate-status" style="display:none; padding:4px 12px; background:#fef3c7; border:1px solid #fbbf24; border-top:none; font-family:var(--sans); font-size:0.7rem; color:#92400e; align-items:center; gap:6px;">
            <span style="display:inline-block; width:8px; height:8px; background:#dc2626; border-radius:50%; animation:pulse-red 1s infinite;"></span>
            Escuchando… Habla ahora. Haz clic en <strong>Detener</strong> cuando termines.
            <span id="dictate-interim" style="color:#78350f; font-style:italic; margin-left:4px;"></span>
          </div>
          <style>@keyframes pulse-red { 0%,100%{opacity:1} 50%{opacity:0.3} }</style>

          <!-- Área editable -->
          <div class="editor-scroll-wrap">
            <div class="editor-content" id="editor-content" contenteditable="true">{!! old('contenido') !!}</div>
          </div>

          <!-- Input hidden que se envía al servidor -->
          <input type="hidden" name="contenido" id="contenido" value="{{ old('contenido') }}">

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
                 value="{{ old('fecha_publicacion', now()->format('Y-m-d')) }}"
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
          <select id="orden" name="orden"
                  class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-sans">
            <option value="0"    {{ old('orden', 0) == 0    ? 'selected' : '' }}>Automático (por fecha)</option>
            <option value="1000" {{ old('orden') == 1000     ? 'selected' : '' }}>Preferente</option>
          </select>
          <p class="mt-2 text-sm text-stone-500 font-sans">Automático: orden cronológico · Preferente: aparece primero</p>
          @error('orden')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Permiso de lectura -->
        <div>
          <label class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">Permiso de lectura</label>
          <div class="space-y-3">
            @foreach(['publico' => ['Público','Visible para todos'], 'seguidores' => ['Seguidores','Solo para suscriptores'], 'privado' => ['Privado','Solo para mí']] as $val => [$nom,$desc])
              <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
                <input type="radio" name="permiso_lectura" value="{{ $val }}"
                       {{ old('permiso_lectura', 'publico') === $val ? 'checked' : '' }}
                       class="mr-3 text-stone-600 focus:ring-stone-500">
                <div>
                  <div class="font-sans font-medium text-stone-900">{{ $nom }}</div>
                  <div class="font-sans text-sm text-stone-600">{{ $desc }}</div>
                </div>
              </label>
            @endforeach
          </div>
          @error('permiso_lectura')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Estado -->
        <div>
          <label class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">Estado <span class="text-red-600">*</span></label>
          <div class="space-y-3">
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
              <input type="radio" name="estado" value="borrador"
                     {{ old('estado', 'borrador') === 'borrador' ? 'checked' : '' }}
                     class="mr-3 text-stone-600 focus:ring-stone-500">
              <div>
                <div class="font-sans font-medium text-stone-900">Borrador</div>
                <div class="font-sans text-sm text-stone-600">No visible públicamente</div>
              </div>
            </label>
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50">
              <input type="radio" name="estado" value="publicado"
                     {{ old('estado') === 'publicado' ? 'checked' : '' }}
                     class="mr-3 text-stone-600 focus:ring-stone-500">
              <div>
                <div class="font-sans font-medium text-stone-900">Publicado</div>
                <div class="font-sans text-sm text-stone-600">Visible públicamente · Texto completo</div>
              </div>
            </label>
            <label class="flex items-center p-4 border border-amber-200 bg-amber-50 rounded-lg cursor-pointer hover:bg-amber-100">
              <input type="radio" name="estado" value="promocion"
                     {{ old('estado') === 'promocion' ? 'checked' : '' }}
                     class="mr-3 text-amber-600 focus:ring-amber-500">
              <div>
                <div class="font-sans font-medium text-amber-900">Promoción</div>
                <div class="font-sans text-sm text-amber-700">Visible · Solo muestra los primeros 100 caracteres</div>
              </div>
            </label>
          </div>
          @error('estado')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

      </div>

      <!-- Actions -->
      <div class="px-8 py-6 bg-stone-100 border-t border-stone-300 flex justify-between">
        <a href="{{ route('admin.narraciones.index') }}"
           class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-stone-600 bg-white border border-stone-300 hover:bg-stone-50 transition-colors">
          Cancelar
        </a>
        <div class="space-x-4">
          <button type="submit" name="estado" value="borrador"
                  class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-stone-600 bg-white border border-stone-300 hover:bg-stone-50 transition-colors">
            Guardar Borrador
          </button>
          <button type="submit" name="estado" value="publicado"
                  class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-white bg-stone-900 hover:bg-stone-800 transition-colors">
            Publicar
          </button>
        </div>
      </div>
    </form>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const wordCountEl   = document.getElementById('word-count');
    const charCountEl   = document.getElementById('char-count');
    const hiddenInput   = document.getElementById('contenido');
    const editorContent = document.getElementById('editor-content');

    function updateWordCount() {
        const text  = editorContent.textContent || '';
        const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
        wordCountEl.textContent = words;
        charCountEl.textContent = text.length;
    }

    function updateHiddenInput() {
        hiddenInput.value = editorContent.innerHTML;
    }

    // Toolbar
    document.querySelectorAll('.editor-btn[data-command]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const command = this.dataset.command;
            const value   = this.dataset.value || null;

            if (command === 'createLink') {
                const url = prompt('Introduce la URL del enlace:');
                if (url) document.execCommand(command, false, url);
            } else {
                document.execCommand(command, false, value);
            }

            updateHiddenInput();
            updateWordCount();
            editorContent.focus();
        });
    });

    // Eventos del editor
    editorContent.addEventListener('input', function () {
        updateHiddenInput();
        updateWordCount();
    });

    // Paste: esperar a que el browser procese y luego sincronizar
    editorContent.addEventListener('paste', function () {
        setTimeout(function () {
            updateHiddenInput();
            updateWordCount();
        }, 50);
    });

    // Estado activo de los botones
    document.addEventListener('selectionchange', function () {
        if (document.activeElement === editorContent) {
            document.querySelectorAll('.editor-btn[data-command]').forEach(btn => {
                const cmd = btn.dataset.command;
                if (cmd && document.queryCommandState(cmd)) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
    });

    // Sincronizar antes de enviar el formulario
    document.querySelector('form').addEventListener('submit', function () {
        updateHiddenInput();
    });

    // ── Dictado por voz ──────────────────────────────────────────────────────
    const dictateBtn    = document.getElementById('dictate-btn');
    const dictateIcon   = document.getElementById('dictate-icon');
    const dictateLabel  = document.getElementById('dictate-label');
    const dictateStatus = document.getElementById('dictate-status');
    const dictateInterim= document.getElementById('dictate-interim');

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
        dictateBtn.style.display = 'none';
    } else {
        const recog = new SpeechRecognition();
        recog.lang           = 'es-ES';
        recog.continuous     = true;
        recog.interimResults = true;

        let isListening = false;
        let savedRange  = null;

        function saveCaretPosition() {
            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0 && editorContent.contains(sel.anchorNode)) {
                savedRange = sel.getRangeAt(0).cloneRange();
            }
        }

        function insertTextAtCaret(text) {
            editorContent.focus();
            const sel = window.getSelection();
            let range;
            if (savedRange) {
                range = savedRange.cloneRange();
                sel.removeAllRanges();
                sel.addRange(range);
            } else if (sel && sel.rangeCount > 0) {
                range = sel.getRangeAt(0);
            } else {
                range = document.createRange();
                range.selectNodeContents(editorContent);
                range.collapse(false);
                sel.removeAllRanges();
                sel.addRange(range);
            }
            range.deleteContents();
            const node = document.createTextNode(text);
            range.insertNode(node);
            range.setStartAfter(node);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
            savedRange = range.cloneRange();
            updateHiddenInput();
            updateWordCount();
        }

        recog.onresult = function (e) {
            let interim = '', finalTxt = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                const t = e.results[i][0].transcript;
                if (e.results[i].isFinal) finalTxt += t;
                else interim += t;
            }
            dictateInterim.textContent = interim;
            if (finalTxt) {
                const prev   = editorContent.textContent;
                const prefix = prev.length > 0 && !prev.endsWith(' ') ? ' ' : '';
                insertTextAtCaret(prefix + finalTxt);
                dictateInterim.textContent = '';
            }
        };

        recog.onerror = function (e) {
            if (e.error === 'not-allowed') alert('Permiso de micrófono denegado.');
            stopDictation();
        };

        recog.onend = function () { if (isListening) stopDictation(); };

        function startDictation() {
            saveCaretPosition();
            recog.start();
            isListening = true;
            dictateIcon.className  = 'bi bi-mic-fill';
            dictateLabel.textContent = 'Detener';
            dictateBtn.style.background = '#fef3c7';
            dictateStatus.style.display = 'flex';
        }

        function stopDictation() {
            recog.stop();
            isListening = false;
            dictateIcon.className  = 'bi bi-mic';
            dictateLabel.textContent = 'Dictar';
            dictateBtn.style.background = '';
            dictateStatus.style.display = 'none';
            dictateInterim.textContent  = '';
        }

        dictateBtn.addEventListener('click', function (e) {
            e.preventDefault();
            isListening ? stopDictation() : startDictation();
        });
    }

    // Init
    updateHiddenInput();
    updateWordCount();
});
</script>
@endsection
