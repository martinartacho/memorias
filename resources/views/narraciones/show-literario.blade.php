@extends('layouts.literario')

@section('title', $narracion->titulo . ' · Memorias sin orden')

@push('styles')
<style>
/* ── Barra TTS sticky ─────────────────────────────────────────────────────── */
#tts-bar {
  position: sticky;
  top: 0;
  z-index: 90;
  background: rgba(245, 240, 232, 0.97);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border-bottom: 1px solid #d6cfc4;
  padding: 0.6rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

/* Botón play circular */
#tts-play-btn {
  width: 38px; height: 38px;
  border-radius: 50%;
  border: 1.5px solid var(--accent);
  background: transparent;
  color: var(--accent);
  font-size: 1rem;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; flex-shrink: 0;
  transition: all 0.2s;
  -webkit-tap-highlight-color: transparent;
  line-height: 1;
  padding: 0;
}
#tts-play-btn:hover   { background: var(--accent); color: var(--parchment); }
#tts-play-btn.playing { background: var(--accent); color: var(--parchment); }

/* Info central */
.tts-info { flex: 1; min-width: 0; }
.tts-label {
  font-family: var(--sans);
  font-size: 0.6rem;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--ink-muted);
  margin-bottom: 0.3rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.tts-progress-wrap { display: flex; align-items: center; gap: 0.5rem; }
.tts-track {
  flex: 1; height: 2px;
  background: #d6cfc4;
  border-radius: 2px; overflow: hidden;
}
.tts-track-fill {
  height: 100%; width: 0%;
  background: var(--accent);
  border-radius: 2px;
  transition: width 0.4s linear;
}
.tts-pct {
  font-family: var(--sans);
  font-size: 0.58rem;
  color: var(--ink-muted);
  width: 28px; text-align: right; flex-shrink: 0;
}

/* Selector de voz */
#tts-voice-select {
  border: 1px solid #d6cfc4;
  background: white;
  color: var(--ink);
  font-family: var(--sans);
  font-size: 0.6rem;
  padding: 0.25rem 0.4rem;
  cursor: pointer; outline: none;
  max-width: 130px;
  display: none; /* se muestra cuando hay más de 1 voz */
}
#tts-voice-select:focus { border-color: var(--accent); }

/* Botón stop */
#tts-stop-btn {
  width: 28px; height: 28px;
  border-radius: 50%;
  border: 1px solid #d6cfc4;
  background: transparent;
  color: var(--ink-muted);
  font-size: 0.65rem;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; flex-shrink: 0;
  transition: all 0.2s;
  -webkit-tap-highlight-color: transparent;
  line-height: 1; padding: 0;
}
#tts-stop-btn:hover { border-color: #dc2626; color: #dc2626; }
</style>
@endpush

@section('content')

{{-- ── Barra TTS sticky ─────────────────────────────────────────────────────── --}}
<div id="tts-bar">
  {{-- Botón play/pausa --}}
  <button id="tts-play-btn" title="Escuchar / Pausar" aria-label="Reproducir">▶</button>

  {{-- Info + barra de progreso --}}
  <div class="tts-info">
    <div class="tts-label" id="tts-label">Escuchar narración</div>
    <div class="tts-progress-wrap">
      <div class="tts-track"><div class="tts-track-fill" id="tts-fill"></div></div>
      <div class="tts-pct" id="tts-pct"></div>
    </div>
  </div>

  {{-- Selector de voz (se muestra solo si hay varias) --}}
  <select id="tts-voice-select" title="Voz" aria-label="Seleccionar voz"></select>

  {{-- Botón stop --}}
  <button id="tts-stop-btn" title="Detener">■</button>
</div>

<main class="container">

  {{-- ── CABECERA DOS COLUMNAS ── --}}
  <div class="featured">

    {{-- Columna izquierda: metadatos --}}
    <div class="featured-meta">
      <div class="section-label">Narración</div>
      <div class="featured-number">{{ str_pad($narracion->id, 2, '0', STR_PAD_LEFT) }}</div>
      <div style="margin-top:auto;">
        <div style="font-family:var(--sans); font-size:1.4rem; font-weight:700; color:var(--parchment-dark); line-height:1; margin-bottom:0.25rem;">
          {{ $narracion->palabra_count }}
        </div>
        <div class="featured-category">palabras</div>
      </div>
      <div class="featured-category" style="margin-top:1rem;">
        Narración · {{ $narracion->fecha_publicacion->format('Y') }}
      </div>
    </div>

    <div class="featured-divider"></div>

    {{-- Columna derecha: contenido --}}
    <div class="featured-text">

      {{-- Línea de fecha + permiso --}}
      <div class="featured-author" style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;">
        <span>Publicado · {{ $narracion->fecha_publicacion->format('F Y') }}</span>

        @if($narracion->estado === 'promocion')
          <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--gold); border:1px solid var(--gold); padding:0.15rem 0.5rem;">
            Vista previa
          </span>
        @endif

        @switch($narracion->permiso_lectura)
          @case('publico')
            <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted);">
              <i class="bi bi-globe2"></i> Público
            </span>
            @break
          @case('seguidores')
            <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--accent);">
              <i class="bi bi-envelope-heart-fill"></i> Suscriptores
            </span>
            @break
          @case('privado')
            <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted);">
              <i class="bi bi-lock-fill"></i> Privado
            </span>
            @break
        @endswitch
      </div>

      {{-- Título --}}
      <h2>{{ $narracion->titulo }}</h2>

      {{-- ── Reproductor de voz (sticky, fuera del flujo del texto) ── --}}
      @php
        // Texto limpio para la voz
        $textoVoz = trim(preg_replace('/\s+/', ' ',
          strip_tags(html_entity_decode(
            $narracion->estado === 'promocion'
              ? Str::limit(strip_tags($narracion->contenido), 500)
              : $narracion->contenido
          ))
        ));
      @endphp

      {{-- Contenido: completo o truncado según estado --}}
      @if($narracion->estado === 'promocion')

        {{-- MODO PROMOCION: solo primer párrafo / 100 chars --}}
        @php
          $promo_texto = Str::limit(strip_tags($narracion->contenido), 100);
        @endphp
        <p style="font-size:1rem; line-height:1.8; color:var(--ink-soft); margin-bottom:1.5rem; position:relative;">
          {{ $promo_texto }}…
          <span style="display:block; height:3rem; background:linear-gradient(to bottom, transparent, var(--parchment)); position:absolute; bottom:-1.5rem; left:0; right:0;"></span>
        </p>

        <div style="border:1px solid var(--parchment-dark); background:#fff; padding:1.5rem 2rem; margin-top:2rem; text-align:center;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--gold); margin-bottom:0.75rem;">
            <i class="bi bi-eye"></i> Vista previa · Texto completo para suscriptores
          </div>
          <p style="font-size:0.9rem; color:var(--ink-muted); margin-bottom:1.25rem;">
            Suscríbete gratis para leer «{{ $narracion->titulo }}» al completo.
          </p>
          <a href="{{ route('suscripcion.form', ['slug' => $narracion->slug]) }}"
             style="display:inline-block; background:var(--accent); color:var(--parchment); font-family:var(--sans); font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.75rem 1.75rem; text-decoration:none; transition:background 0.2s;"
             onmouseover="this.style.background='var(--ink)'"
             onmouseout="this.style.background='var(--accent)'">
            Suscribirme gratis →
          </a>
        </div>

      @else

        {{-- MODO NORMAL: contenido completo --}}
        <div style="font-size:1.05rem; line-height:1.9; color:var(--ink-soft);">
          {!! $narracion->contenido !!}
        </div>

      @endif

      {{-- ── Líneas argumentales: navegación siguiente / fin / inicio ── --}}
      @php
        $nodosDeEsta = $narracion->nodosLinea()->with(['linea','conexionesSalientes.hasta.narracion'])->get();
      @endphp
      @foreach($nodosDeEsta as $nodo)

        @if($nodo->conexionesSalientes->isNotEmpty())
          {{-- ── Hay siguiente: mostrar botón de continuación ── --}}
          <div style="margin-top:2rem; padding:1.5rem; border:1px solid var(--parchment-dark); background:var(--parchment-mid);">
            <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.75rem;">
              <i class="bi bi-{{ $nodo->linea->esRamificada() ? 'diagram-3' : 'arrow-right-circle' }}"></i>
              &nbsp;{{ $nodo->linea->titulo }}
            </div>

            @if($nodo->linea->esLineal())
              @php $siguiente = $nodo->conexionesSalientes->first()->hasta; @endphp
              @if($siguiente?->narracion)
                <a href="{{ route('narraciones.show', $siguiente->narracion->slug) }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--parchment); background:var(--ink); padding:0.65rem 1.25rem; text-decoration:none; transition:background 0.2s;"
                   onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--ink)'">
                  La historia continúa &nbsp;→&nbsp; <em style="font-style:italic; font-family:var(--serif); text-transform:none; font-size:0.85rem; letter-spacing:0;">{{ $siguiente->narracion->titulo }}</em>
                </a>
              @endif
            @else
              <div style="font-family:var(--body); font-size:0.9rem; color:var(--ink-soft); margin-bottom:0.75rem;">¿Cómo sigue la historia?</div>
              <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                @foreach($nodo->conexionesSalientes as $cx)
                  @if($cx->hasta?->narracion)
                    <a href="{{ route('narraciones.show', $cx->hasta->narracion->slug) }}"
                       style="font-family:var(--sans); font-size:0.7rem; border:1px solid var(--ink); color:var(--ink); padding:0.4rem 0.9rem; text-decoration:none; transition:all 0.2s;"
                       onmouseover="this.style.background='var(--ink)';this.style.color='var(--parchment)'"
                       onmouseout="this.style.background='transparent';this.style.color='var(--ink)'"
                       title="{{ $cx->hasta->narracion->titulo }}">
                      {{ $cx->etiqueta ?: $cx->hasta->narracion->titulo }}
                    </a>
                  @endif
                @endforeach
              </div>
            @endif
          </div>

        @elseif($nodo->linea->esLineal())
          {{-- ── Fin de línea lineal: ofrecer volver al inicio ── --}}
          @php
            $primerNodo = $nodo->linea->nodos()->orderBy('orden')->with('narracion')->first();
          @endphp
          @if($primerNodo && $primerNodo->narracion_id !== $narracion->id)
            <div style="margin-top:2rem; padding:1.5rem; border:1px solid var(--parchment-dark); background:var(--parchment-mid); text-align:center;">
              <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.75rem;">
                <i class="bi bi-flag-fill" style="color:var(--accent);"></i>
                &nbsp;{{ $nodo->linea->titulo }} · Fin
              </div>
              <p style="font-family:var(--body); font-size:0.9rem; color:var(--ink-soft); margin-bottom:1.25rem;">
                Has llegado al final de esta historia.
              </p>
              <a href="{{ route('narraciones.show', $primerNodo->narracion->slug) }}"
                 style="display:inline-flex; align-items:center; gap:0.5rem; font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink); border:1px solid var(--ink); padding:0.65rem 1.25rem; text-decoration:none; transition:all 0.2s;"
                 onmouseover="this.style.background='var(--ink)';this.style.color='var(--parchment)'"
                 onmouseout="this.style.background='transparent';this.style.color='var(--ink)'">
                ← Volver al inicio &nbsp;·&nbsp; <em style="font-style:italic; font-family:var(--serif); text-transform:none; font-size:0.85rem; letter-spacing:0;">{{ $primerNodo->narracion->titulo }}</em>
              </a>
            </div>
          @endif

        @endif
      @endforeach

      {{-- Navegación inferior --}}
      <div style="margin-top:2.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; border-top:1px solid var(--parchment-dark); padding-top:1.5rem;">
        <a href="{{ route('narraciones.index') }}" class="read-more">← Volver a narraciones</a>
        <span style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink-muted);">
          {{ $narracion->count_read }} {{ $narracion->count_read === 1 ? 'lectura' : 'lecturas' }}
        </span>
      </div>

    </div>
  </div>

  {{-- ── OTRAS MEMORIAS ── --}}
  @php
    $hoy = now()->toDateString();
    $relatedQuery = \App\Models\Narracion::visible()
        ->where('id', '!=', $narracion->id)
        ->where('fecha_publicacion', '<=', $hoy)
        ->orderByFecha();

    if (auth()->check() && auth()->user()->canAccessAdmin()) {
        // Admin ve todo
    } elseif (request()->cookie('subscriber_token') && \App\Models\Subscriber::tokenIsValid(request()->cookie('subscriber_token'))) {
        $relatedQuery->where('permiso_lectura', '!=', 'privado');
    } else {
        $relatedQuery->where('permiso_lectura', 'publico');
    }

    $related = $relatedQuery->take(3)->get();
  @endphp

  <div class="grid-label" style="margin-top:3rem;">
    Otras memorias <span>Explora más historias</span>
  </div>

  <div class="stories-grid">
    @foreach($related as $key => $story)
      @php $esPromo = $story->estado === 'promocion'; @endphp
      <div class="story-card {{ $esPromo ? 'story-card--promo' : '' }}"
           onclick="window.location.href='{{ route('narraciones.show', $story->slug) }}'">
        <div class="card-num">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="card-tag">
          @if($esPromo)
            <span style="color:var(--gold);">Vista previa</span>
          @else
            Narración
          @endif
          @if($story->permiso_lectura === 'seguidores')
            <span style="margin-left:0.4rem; color:var(--accent);"><i class="bi bi-envelope-heart-fill"></i></span>
          @elseif($story->permiso_lectura === 'privado')
            <span style="margin-left:0.4rem; color:var(--ink-muted);"><i class="bi bi-lock-fill"></i></span>
          @endif
        </div>
        <div class="card-title">{{ $story->titulo }}</div>
        <div class="card-author">{{ $story->fecha_publicacion->format('F Y') }}</div>
        <p class="card-excerpt">{{ Str::limit(strip_tags($story->contenido), $esPromo ? 100 : 120) }}</p>
      </div>
    @endforeach

    @for($i = $related->count(); $i < 3; $i++)
      <div class="story-card" style="opacity:0.3; pointer-events:none;">
        <div class="card-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="card-tag">Próximamente</div>
        <div class="card-title">Nueva historia</div>
        <div class="card-author">En preparación</div>
        <p class="card-excerpt">Pronto habrá nuevas narraciones disponibles…</p>
      </div>
    @endfor
  </div>

</main>

<script>
(function () {
  const synth     = window.speechSynthesis;
  const playBtn   = document.getElementById('tts-play-btn');
  const stopBtn   = document.getElementById('tts-stop-btn');
  const label     = document.getElementById('tts-label');
  const fill      = document.getElementById('tts-fill');
  const pct       = document.getElementById('tts-pct');
  const voiceSel  = document.getElementById('tts-voice-select');

  // Texto limpio inyectado desde PHP
  const texto     = {{ Js::from($textoVoz) }};
  const totalChars = texto.length;

  // Si no hay soporte, ocultar la barra
  if (!synth) {
    document.getElementById('tts-bar').style.display = 'none';
    return;
  }

  let utter    = null;
  let state    = 'idle'; // idle | playing | paused
  const STORAGE_KEY = 'tts_preferred_voice';

  /* ── Selección de voces ─────────────────────────────────────────────────── */
  function getVoices() {
    const all = synth.getVoices();
    // Filtrar español, priorizar es-ES, luego online/natural, luego el resto
    const es = all.filter(v => v.lang.startsWith('es'));
    es.sort((a, b) => {
      // 1. es-ES antes que otros
      const aES = a.lang === 'es-ES' ? 0 : 1;
      const bES = b.lang === 'es-ES' ? 0 : 1;
      if (aES !== bES) return aES - bES;
      // 2. Online/Natural antes que local
      const aOn = /online|natural/i.test(a.name) ? 0 : 1;
      const bOn = /online|natural/i.test(b.name) ? 0 : 1;
      return aOn - bOn;
    });
    return es;
  }

  function populateVoiceSelect(voices) {
    voiceSel.innerHTML = '';
    const saved = localStorage.getItem(STORAGE_KEY);
    voices.forEach(v => {
      const opt = document.createElement('option');
      opt.value = v.name;
      // Nombre corto: quitar " Online (Natural)" y similares del final
      const shortName = v.name
        .replace(/\s*online\s*/i, ' ')
        .replace(/\s*\(natural\)\s*/i, '')
        .replace(/\s*-\s*spanish\s*.*/i, '')
        .trim();
      const isOnline = /online|natural/i.test(v.name) || !v.localService;
      opt.textContent = shortName + (isOnline ? ' ★' : '');
      if (v.name === saved) opt.selected = true;
      voiceSel.appendChild(opt);
    });
    // Mostrar selector solo si hay más de 1 voz
    if (voices.length > 1) voiceSel.style.display = 'block';
  }

  function getBestVoice(voices) {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
      const found = voices.find(v => v.name === saved);
      if (found) return found;
    }
    return voices[0] || null; // ya ordenadas: online es-ES primero
  }

  voiceSel.addEventListener('change', () => {
    localStorage.setItem(STORAGE_KEY, voiceSel.value);
    // Si estaba reproduciendo, reiniciar con la nueva voz
    if (state !== 'idle') {
      synth.cancel();
      startReading();
    }
  });

  /* ── Progreso ──────────────────────────────────────────────────────────── */
  function setProgress(charIndex, done) {
    const p = done ? 100 : Math.min(99, Math.round((charIndex / totalChars) * 100));
    fill.style.width    = p + '%';
    pct.textContent     = p + '%';
  }

  function resetProgress() {
    fill.style.width = '0%';
    pct.textContent  = '';
  }

  /* ── Estado visual ─────────────────────────────────────────────────────── */
  function setState(s) {
    state = s;
    if (s === 'playing') {
      playBtn.textContent = '⏸';
      playBtn.classList.add('playing');
      label.textContent   = 'Reproduciendo…';
    } else if (s === 'paused') {
      playBtn.textContent = '▶';
      playBtn.classList.remove('playing');
      label.textContent   = 'En pausa · toca para continuar';
    } else {
      playBtn.textContent = '▶';
      playBtn.classList.remove('playing');
      label.textContent   = 'Escuchar narración';
      resetProgress();
    }
  }

  /* ── Lectura ────────────────────────────────────────────────────────────── */
  function startReading() {
    synth.cancel();
    const voices = getVoices();
    const voice  = getBestVoice(voices);

    utter       = new SpeechSynthesisUtterance(texto);
    utter.lang  = 'es-ES';
    utter.rate  = 0.88;
    utter.pitch = 1.0;
    if (voice) utter.voice = voice;

    utter.onboundary = (e) => {
      if (e.name === 'word') setProgress(e.charIndex, false);
    };
    utter.onend = () => {
      setProgress(0, true);
      label.textContent = 'Narración completada';
      setTimeout(() => setState('idle'), 1800);
    };
    utter.onerror = (e) => {
      if (e.error !== 'interrupted') setState('idle');
    };

    synth.speak(utter);
    setState('playing');
  }

  /* ── Controles ─────────────────────────────────────────────────────────── */
  playBtn.addEventListener('click', () => {
    if (state === 'idle') {
      startReading();
    } else if (state === 'playing') {
      synth.pause();
      setState('paused');
    } else if (state === 'paused') {
      synth.resume();
      setState('playing');
    }
  });

  stopBtn.addEventListener('click', () => {
    synth.cancel();
    setState('idle');
    utter = null;
  });

  window.addEventListener('beforeunload', () => synth.cancel());

  /* ── Inicializar voces (async en Chrome) ─────────────────────────────── */
  function initVoices() {
    const voices = getVoices();
    if (voices.length > 0) populateVoiceSelect(voices);
  }

  if (synth.getVoices().length > 0) {
    initVoices();
  } else {
    synth.onvoiceschanged = () => { synth.onvoiceschanged = null; initVoices(); };
    setTimeout(initVoices, 1500); // fallback
  }
})();
</script>

@endsection
