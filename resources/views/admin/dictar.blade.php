@extends('layouts.literario')
@section('title', 'Dictar · Memorias sin orden')

@push('styles')
<style>
/* ── Animación de pulso cuando está escuchando ── */
@keyframes pulse-mic {
  0%,100% { box-shadow: 0 0 0 0   rgba(220,38,38,0.5), 0 4px 24px rgba(220,38,38,0.3); }
  50%      { box-shadow: 0 0 0 18px rgba(220,38,38,0),   0 4px 24px rgba(220,38,38,0.3); }
}
.listening { animation: pulse-mic 1.4s ease-out infinite; }
</style>
@endpush

@section('content')
<main style="max-width:560px; margin:0 auto; padding:2rem 1.25rem 4rem;">

  {{-- Cabecera --}}
  <div style="text-align:center; margin-bottom:2.5rem;">
    <div class="section-label">Dictado de voz</div>
    <p style="font-family:var(--sans); font-size:0.72rem; letter-spacing:0.05em; color:var(--ink-muted); margin-top:0.5rem;">
      Habla con naturalidad · El texto se guarda como borrador privado
    </p>
  </div>

  {{-- ── Botón micrófono ── --}}
  <div style="text-align:center; margin-bottom:2rem;">
    <button id="mic-btn" type="button" style="
        width:110px; height:110px; border-radius:50%;
        background:var(--ink); border:none; cursor:pointer;
        display:inline-flex; align-items:center; justify-content:center;
        box-shadow:0 4px 24px rgba(0,0,0,0.18); transition:background 0.25s;
        -webkit-tap-highlight-color:transparent;">
      <i class="bi bi-mic-fill" id="mic-icon" style="font-size:2.75rem; color:var(--parchment);"></i>
    </button>

    <div id="mic-status" style="margin-top:1.25rem; font-family:var(--sans); font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">
      Pulsa para dictar
    </div>

    {{-- Texto provisional (lo que el navegador está reconociendo ahora mismo) --}}
    <div id="interim-wrap" style="display:none; margin-top:1rem; padding:0.6rem 1rem; background:#fef3c7; border:1px solid #fbbf24; font-family:var(--body); font-size:0.9rem; font-style:italic; color:#78350f; text-align:left; border-radius:4px;">
      <span id="interim-txt"></span>
    </div>
  </div>

  {{-- ── Texto acumulado ── --}}
  <div style="position:relative; margin-bottom:1.25rem;">
    <textarea id="dictated" rows="10"
      placeholder="El texto dictado irá apareciendo aquí…"
      style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment);
             padding:1rem 1rem 2rem; font-family:var(--body); font-size:1rem; line-height:1.75;
             color:var(--ink); resize:vertical; outline:none; box-sizing:border-box;"
      onfocus="this.style.borderColor='var(--accent)'"
      onblur="this.style.borderColor='var(--parchment-dark)'"></textarea>
    <div id="wc" style="position:absolute; bottom:0.5rem; right:0.75rem; font-family:var(--sans); font-size:0.6rem; color:var(--ink-muted); pointer-events:none;">
      0 palabras
    </div>
  </div>

  {{-- Aviso navegador sin soporte --}}
  <div id="no-support" style="display:none; background:#fef2f2; border:1px solid #fca5a5; padding:1rem; margin-bottom:1.25rem; font-family:var(--sans); font-size:0.78rem; color:#991b1b; text-align:center; border-radius:4px;">
    <i class="bi bi-exclamation-triangle-fill"></i>
    Tu navegador no soporta el dictado por voz.<br>
    Usa <strong>Chrome</strong> o <strong>Edge</strong> en Android, o Safari en iOS.
  </div>

  {{-- ── Formulario de guardado ── --}}
  <form id="save-form" action="{{ route('admin.dictar.guardar') }}" method="POST">
    @csrf
    <input type="hidden" name="contenido" id="contenido-hidden">

    <div style="margin-bottom:0.85rem;">
      <input type="text" name="titulo" id="titulo"
        placeholder="Título (opcional — si lo dejas vacío usará fecha y hora)"
        style="width:100%; border:1px solid var(--parchment-dark); background:white;
               padding:0.7rem 1rem; font-family:var(--body); font-size:0.9rem;
               color:var(--ink); outline:none; box-sizing:border-box;"
        onfocus="this.style.borderColor='var(--accent)'"
        onblur="this.style.borderColor='var(--parchment-dark)'">
    </div>

    <div style="display:grid; grid-template-columns:1fr auto; gap:0.75rem;">
      <button type="submit" id="save-btn" disabled style="
          background:var(--ink); color:var(--parchment); border:none;
          padding:1rem; font-family:var(--sans); font-size:0.68rem;
          letter-spacing:0.2em; text-transform:uppercase;
          cursor:not-allowed; opacity:0.45; transition:opacity 0.2s;">
        <i class="bi bi-floppy"></i> Guardar borrador
      </button>
      <button type="button" id="clear-btn" style="
          background:none; border:1px solid var(--parchment-dark);
          color:var(--ink-muted); padding:1rem 1.25rem;
          font-family:var(--sans); font-size:0.68rem;
          letter-spacing:0.2em; text-transform:uppercase; cursor:pointer;">
        Limpiar
      </button>
    </div>

    @if(session('success'))
      <div style="margin-top:1rem; background:#f0fdf4; border:1px solid #86efac; padding:0.9rem 1.25rem; font-family:var(--sans); font-size:0.8rem; color:#166534;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
      </div>
    @endif
  </form>

  <div style="text-align:center; margin-top:2rem;">
    <a href="{{ route('admin.narraciones.index') }}" class="read-more" style="font-size:0.62rem;">← Mis narraciones</a>
  </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const micBtn      = document.getElementById('mic-btn');
    const micIcon     = document.getElementById('mic-icon');
    const micStatus   = document.getElementById('mic-status');
    const interimWrap = document.getElementById('interim-wrap');
    const interimTxt  = document.getElementById('interim-txt');
    const dictated    = document.getElementById('dictated');
    const wcEl        = document.getElementById('wc');
    const saveBtn     = document.getElementById('save-btn');
    const clearBtn    = document.getElementById('clear-btn');
    const hidden      = document.getElementById('contenido-hidden');

    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SR) {
        document.getElementById('no-support').style.display = 'block';
        micBtn.disabled  = true;
        micBtn.style.opacity = '0.3';
        return;
    }

    // Estados: 'idle' | 'listening' | 'paused'
    let state = 'idle';
    const recog = new SR();
    recog.lang           = 'es-ES';
    recog.continuous     = true;
    recog.interimResults = true;

    /* ── Mapa de puntuación hablada → símbolo ──────────────────────────────
       Orden: frases largas primero, palabras sueltas al final.
       Sin \b en palabras con tildes (JavaScript no las trata bien sin flag u) */
    const PUNCT = [
        [/punto y aparte/gi,              '.\n\n'],
        [/punto y seguido/gi,             '. '],
        [/punto y coma/gi,                '; '],
        [/puntos? suspensivos?/gi,        '… '],  // singular y plural
        [/punto final/gi,                 '. '],
        [/dos puntos/gi,                  ': '],
        [/salto de p[aá]rrafo/gi,         '\n\n'],
        [/nuevo p[aá]rrafo/gi,            '\n\n'],
        [/nueva l[ií]nea/gi,              '\n'],
        [/signo de interrogaci[oó]n/gi,   '? '],
        [/signo de exclamaci[oó]n/gi,     '! '],
        [/abrir par[eé]ntesis/gi,         '('],
        [/cerrar par[eé]ntesis/gi,        ') '],
        [/abrir comillas/gi,              '«'],
        [/cerrar comillas/gi,             '» '],
        // Palabras sueltas — sin \b para tildes, lo bastante específicas
        [/(^|\s)punto(\s|$)/gi,           '$1. '],
        [/(^|\s)coma(\s|$)/gi,            '$1, '],
        [/interrogaci[oó]n/gi,            '? '],
        [/exclamaci[oó]n/gi,              '! '],
        [/gui[oó]n/gi,                    ' — '],
    ];

    function applyPunct(text) {
        let t = text;
        for (const [re, sym] of PUNCT) t = t.replace(re, sym);
        return t.replace(/ {2,}/g, ' ').trim();
    }

    /* ── Buffer de la sesión actual (no se escribe al área hasta que para) ── */
    let sesionFinal   = '';   // resultados marcados isFinal en esta sesión
    let sesionInterim = '';   // resultado provisional en curso
    let paroManual    = false;

    /* ── Añadir texto al área con espaciado inteligente ── */
    function appendText(raw) {
        const processed = applyPunct(raw);
        if (!processed) return;
        const prev  = dictated.value;
        const space = prev.length && !/[\s\n(«]$/.test(prev) && !/^[,\.;:\?!)\»…\n]/.test(processed) ? ' ' : '';
        dictated.value += space + processed;
        dictated.scrollTop = dictated.scrollHeight;
        sync();
    }

    /* ── Volcar el buffer de sesión al área de texto ── */
    function flushBuffer() {
        const todo = (sesionFinal + sesionInterim).trim();
        if (todo) appendText(todo);
        sesionFinal = sesionInterim = '';
        interimTxt.textContent    = '';
        interimWrap.style.display = 'none';
    }

    /* ── Actualizar contador y habilitar guardar ── */
    function sync() {
        const words = dictated.value.trim().split(/\s+/).filter(w => w).length;
        wcEl.textContent = words + ' palabra' + (words === 1 ? '' : 's');
        const hasText = dictated.value.trim().length > 0;
        saveBtn.disabled      = !hasText;
        saveBtn.style.opacity = hasText ? '1'       : '0.45';
        saveBtn.style.cursor  = hasText ? 'pointer' : 'not-allowed';
        hidden.value = dictated.value;
    }

    /* ── Resultado de reconocimiento ──────────────────────────────────────────
       Bucle desde 0 (no desde resultIndex) para obtener la transcripción
       completa de la sesión — Chrome puede corregir resultados anteriores.
       Solo actualizamos el buffer visual; el textarea se toca en onend.     */
    recog.onresult = function (e) {
        sesionFinal   = '';
        sesionInterim = '';
        for (let i = 0; i < e.results.length; i++) {
            const t = e.results[i][0].transcript;
            e.results[i].isFinal ? (sesionFinal += t) : (sesionInterim += t);
        }
        const display = (sesionFinal + sesionInterim).trim();
        interimTxt.textContent    = display;
        interimWrap.style.display = display ? 'block' : 'none';
    };

    /* ── Errores ── */
    recog.onerror = function (e) {
        if (e.error === 'not-allowed')
            alert('Permiso de micrófono denegado. Actívalo en la configuración del navegador.');
        if (e.error !== 'no-speech') { flushBuffer(); setState('idle'); }
    };

    /* ── onend: siempre volcar el buffer y cambiar estado ── */
    recog.onend = function () {
        if (state !== 'listening') return;
        flushBuffer();
        setState(paroManual ? 'idle' : 'paused');
        paroManual = false;
    };

    /* ── Máquina de estados visual ── */
    function setState(s) {
        state = s;
        interimWrap.style.display = 'none';

        if (s === 'idle') {
            micIcon.className       = 'bi bi-mic';
            micBtn.style.background = 'var(--ink)';
            micBtn.classList.remove('listening', 'paused-pulse');
            micStatus.textContent   = 'Pulsa para dictar';
            micStatus.style.color   = 'var(--ink-muted)';

        } else if (s === 'listening') {
            micIcon.className       = 'bi bi-mic-fill';
            micBtn.style.background = '#dc2626';
            micBtn.classList.add('listening');
            micBtn.classList.remove('paused-pulse');
            micStatus.textContent   = 'Escuchando…';
            micStatus.style.color   = '#dc2626';

        } else if (s === 'paused') {
            micIcon.className       = 'bi bi-mic';
            micBtn.style.background = '#d97706';   // ámbar
            micBtn.classList.remove('listening');
            micStatus.innerHTML     = 'Pausa por silencio &nbsp;·&nbsp; <strong>Toca para continuar</strong>';
            micStatus.style.color   = '#d97706';
        }
    }

    micBtn.addEventListener('click', function () {
        if (state === 'listening') {
            paroManual = true;   // onend sabrá que fue manual → estado idle
            recog.stop();        // onend vuelca buffer y cambia estado
        } else {
            // idle o paused → nueva sesión
            sesionFinal = sesionInterim = '';
            try { recog.start(); setState('listening'); }
            catch (err) { /* ya corriendo */ }
        }
    });

    /* ── Limpiar ── */
    clearBtn.addEventListener('click', function () {
        if (!dictated.value || confirm('¿Borrar todo el texto?')) {
            dictated.value = '';
            sync();
            micStatus.textContent = 'Pulsa para dictar';
        }
    });

    /* ── Edición manual ── */
    dictated.addEventListener('input', sync);

    sync();
});
</script>
@endsection
