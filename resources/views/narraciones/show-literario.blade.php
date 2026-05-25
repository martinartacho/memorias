@extends('layouts.literario')

@section('title', $narracion->titulo . ' · Memorias sin orden')

@section('content')
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

      {{-- ── Reproductor de voz ── --}}
      @php
        $textoVoz = strip_tags($narracion->estado === 'promocion'
          ? Str::limit(strip_tags($narracion->contenido), 100)
          : $narracion->contenido);
      @endphp
      <div id="tts-bar" style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem; flex-wrap:wrap;">
        <button id="tts-play" type="button"
          style="display:flex; align-items:center; gap:0.4rem; background:none; border:1px solid var(--parchment-dark); padding:0.3rem 0.85rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted); cursor:pointer; transition:all 0.2s;"
          onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
          onmouseout="if(!window._ttsPlaying){this.style.borderColor='var(--parchment-dark)';this.style.color='var(--ink-muted)'}">
          <i class="bi bi-play-fill" id="tts-icon"></i>
          <span id="tts-label">Escuchar</span>
        </button>
        <button id="tts-stop" type="button"
          style="display:none; align-items:center; gap:0.4rem; background:none; border:1px solid var(--parchment-dark); padding:0.3rem 0.85rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted); cursor:pointer;"
          onmouseover="this.style.borderColor='var(--ink)';this.style.color='var(--ink)'"
          onmouseout="this.style.borderColor='var(--parchment-dark)';this.style.color='var(--ink-muted)'">
          <i class="bi bi-stop-fill"></i> Detener
        </button>
        <span id="tts-unsupported" style="display:none; font-family:var(--sans); font-size:0.62rem; color:var(--ink-muted);">
          <i class="bi bi-exclamation-circle"></i> Tu navegador no soporta la lectura en voz alta.
        </span>
      </div>
      <script>
      (function() {
        const synth   = window.speechSynthesis;
        const playBtn = document.getElementById('tts-play');
        const stopBtn = document.getElementById('tts-stop');
        const icon    = document.getElementById('tts-icon');
        const label   = document.getElementById('tts-label');
        const texto   = {{ Js::from($textoVoz) }};
        window._ttsPlaying = false;
        let utter = null;

        if (!synth) {
          document.getElementById('tts-unsupported').style.display = 'inline';
          playBtn.style.display = 'none';
          return;
        }

        function getSpanishVoice() {
          const voices = synth.getVoices();
          return voices.find(v => v.lang === 'es-ES') ||
                 voices.find(v => v.lang.startsWith('es')) ||
                 null;
        }

        function setPlaying(on) {
          window._ttsPlaying = on;
          icon.className  = on ? 'bi bi-pause-fill' : 'bi bi-play-fill';
          label.textContent = on ? 'Pausar' : (utter ? 'Continuar' : 'Escuchar');
          playBtn.style.borderColor = on ? 'var(--accent)' : 'var(--parchment-dark)';
          playBtn.style.color       = on ? 'var(--accent)' : 'var(--ink-muted)';
          stopBtn.style.display     = on ? 'flex' : 'none';
        }

        function startReading() {
          synth.cancel();
          utter = new SpeechSynthesisUtterance(texto);
          utter.lang  = 'es-ES';
          utter.rate  = 0.95;
          utter.pitch = 1;
          const voice = getSpanishVoice();
          if (voice) utter.voice = voice;
          utter.onstart = () => setPlaying(true);
          utter.onend   = () => { setPlaying(false); utter = null; label.textContent = 'Escuchar'; };
          utter.onerror = () => { setPlaying(false); utter = null; };
          synth.speak(utter);
        }

        playBtn.addEventListener('click', function() {
          if (!synth.speaking) {
            startReading();
          } else if (synth.paused) {
            synth.resume();
            setPlaying(true);
          } else {
            synth.pause();
            setPlaying(false);
            icon.className   = 'bi bi-play-fill';
            label.textContent = 'Continuar';
          }
        });

        stopBtn.addEventListener('click', function() {
          synth.cancel();
          setPlaying(false);
          utter = null;
          label.textContent = 'Escuchar';
        });

        // Las voces se cargan de forma asíncrona en Chrome
        if (typeof speechSynthesis.onvoiceschanged !== 'undefined') {
          speechSynthesis.onvoiceschanged = getSpanishVoice;
        }
      })();
      </script>

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

      {{-- ── Líneas argumentales: navegación siguiente ── --}}
      @php
        $nodosDeEsta = $narracion->nodosLinea()->with(['linea','conexionesSalientes.hasta.narracion'])->get();
      @endphp
      @foreach($nodosDeEsta as $nodo)
        @if($nodo->conexionesSalientes->isNotEmpty())
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
@endsection
