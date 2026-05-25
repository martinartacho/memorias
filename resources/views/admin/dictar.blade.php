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

    let listening = false;
    const recog   = new SR();
    recog.lang         = 'es-ES';
    recog.continuous   = true;
    recog.interimResults = true;

    /* ── Actualizar contador y habilitar guardar ── */
    function sync() {
        const words = dictated.value.trim().split(/\s+/).filter(w => w).length;
        wcEl.textContent = words + ' palabra' + (words === 1 ? '' : 's');
        const hasText = dictated.value.trim().length > 0;
        saveBtn.disabled     = !hasText;
        saveBtn.style.opacity  = hasText ? '1'        : '0.45';
        saveBtn.style.cursor   = hasText ? 'pointer'  : 'not-allowed';
        hidden.value = dictated.value;
    }

    /* ── Resultado de reconocimiento ── */
    recog.onresult = function (e) {
        let interim = '';
        let final   = '';
        for (let i = e.resultIndex; i < e.results.length; i++) {
            const t = e.results[i][0].transcript;
            e.results[i].isFinal ? (final += t) : (interim += t);
        }
        interimTxt.textContent      = interim;
        interimWrap.style.display   = interim ? 'block' : 'none';

        if (final) {
            const prev   = dictated.value;
            const space  = prev.length && !/[\s\n]$/.test(prev) ? ' ' : '';
            dictated.value += space + final.trim();
            interimTxt.textContent    = '';
            interimWrap.style.display = 'none';
            dictated.scrollTop = dictated.scrollHeight;
            sync();
        }
    };

    /* ── Errores ── */
    recog.onerror = function (e) {
        if (e.error === 'not-allowed')
            alert('Permiso de micrófono denegado. Actívalo en la configuración del navegador.');
        if (e.error !== 'no-speech') stopListening();
    };

    /* ── Al parar: reinicio automático (uso en exterior, sin eco de altavoces) ── */
    recog.onend = function () {
        if (listening) {
            try { recog.start(); } catch (_) {}
        }
    };

    /* ── Arrancar ── */
    function startListening() {
        recog.start();
        listening = true;
        micIcon.className       = 'bi bi-mic-fill';
        micBtn.style.background = '#dc2626';
        micBtn.classList.add('listening');
        micStatus.textContent   = 'Escuchando…';
        micStatus.style.color   = '#dc2626';
    }

    /* ── Parar ── */
    function stopListening() {
        listening = false;
        recog.stop();
        micIcon.className       = 'bi bi-mic';
        micBtn.style.background = 'var(--ink)';
        micBtn.classList.remove('listening');
        micStatus.textContent   = 'Pulsa para continuar';
        micStatus.style.color   = 'var(--ink-muted)';
        interimWrap.style.display = 'none';
    }

    micBtn.addEventListener('click', () => listening ? stopListening() : startListening());

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
