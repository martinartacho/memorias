@extends('layouts.literario')

@section('title', $narracion->titulo . ' · ' . __('site.name'))

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

/* Botón preferencias (suscriptores) */
#tts-prefs-btn {
  width: 28px; height: 28px;
  border-radius: 50%;
  border: 1px solid #d6cfc4;
  background: transparent;
  color: var(--ink-muted);
  font-size: 0.7rem;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; flex-shrink: 0;
  transition: all 0.2s;
  -webkit-tap-highlight-color: transparent;
  line-height: 1; padding: 0;
}
#tts-prefs-btn:hover,
#tts-prefs-btn.open { border-color: var(--accent); color: var(--accent); background: #fdf7ec; }

/* Panel de preferencias */
#tts-prefs-panel {
  position: absolute;
  top: 100%; right: 0; left: 0;
  background: #fffdf8;
  border-bottom: 2px solid var(--accent);
  border-top: 1px solid #ede8df;
  padding: 1rem 1.25rem 1.1rem;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  z-index: 89;
  font-family: var(--sans);
  display: none;
  animation: slideDown 0.18s ease;
}
#tts-prefs-panel.open { display: block; }
@keyframes slideDown {
  from { opacity:0; transform: translateY(-6px); }
  to   { opacity:1; transform: translateY(0); }
}

.prefs-row {
  display: flex; align-items: center; gap: 0.65rem;
  flex-wrap: wrap; margin-bottom: 0.65rem;
}
.prefs-row:last-child { margin-bottom: 0; }

.prefs-lbl {
  font-size: 0.57rem; letter-spacing: 0.14em; text-transform: uppercase;
  color: var(--ink-muted); min-width: 65px; flex-shrink: 0;
}

/* Presets de velocidad */
.rate-btn {
  padding: 0.25rem 0.7rem;
  border: 1px solid #d6cfc4; background: white; color: #5a5248;
  font-family: var(--sans); font-size: 0.58rem;
  letter-spacing: 0.1em; text-transform: uppercase;
  cursor: pointer; transition: all 0.15s;
}
.rate-btn:hover  { border-color: var(--accent); color: var(--accent); }
.rate-btn.active { background: var(--ink); border-color: var(--ink); color: var(--parchment); }

/* Slider tono */
.pitch-wrap { display: flex; align-items: center; gap: 0.5rem; }
#tts-pitch { flex:1; min-width:80px; max-width:150px; accent-color: var(--accent); }
#tts-pitch-val { font-size: 0.65rem; color: var(--ink); min-width:2rem; }

/* Select voz en panel */
#tts-voice-panel {
  flex: 1; max-width: 300px;
  border: 1px solid #d6cfc4; background: white;
  color: var(--ink); font-family: var(--sans); font-size: 0.68rem;
  padding: 0.28rem 0.5rem; cursor: pointer; outline: none;
}
#tts-voice-panel:focus { border-color: var(--accent); }

.prefs-reset {
  font-size: 0.57rem; letter-spacing: 0.08em; color: var(--ink-muted);
  text-decoration: underline; cursor: pointer;
  background: none; border: none; font-family: var(--sans); padding: 0;
}
.prefs-reset:hover { color: var(--ink); }
.prefs-saved {
  font-size: 0.57rem; color: #14532d; letter-spacing: 0.06em;
  opacity: 0; transition: opacity 0.3s;
}
.prefs-saved.show { opacity: 1; }
</style>
@endpush

@section('content')

{{-- ── Barra TTS sticky ─────────────────────────────────────────────────────── --}}
<div id="tts-bar" style="position:relative;">
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

  {{-- Botón preferencias (solo suscriptores o admin) --}}
  @if(!empty($esSubscriptor) || !empty($esAdmin))
    <button id="tts-prefs-btn" title="Preferencias de lectura" aria-label="Preferencias">⚙</button>
  @endif

  {{-- Botón stop --}}
  <button id="tts-stop-btn" title="Detener">■</button>

  {{-- Panel de preferencias ──────────────────────────────────────────── --}}
  @if(!empty($esSubscriptor) || !empty($esAdmin))
  <div id="tts-prefs-panel" role="region" aria-label="Preferencias de audio">

    {{-- Voz --}}
    <div class="prefs-row">
      <span class="prefs-lbl">Voz</span>
      <select id="tts-voice-panel"></select>
    </div>

    {{-- Velocidad --}}
    <div class="prefs-row">
      <span class="prefs-lbl">Velocidad</span>
      <button class="rate-btn" data-rate="0.72">🐢 Lenta</button>
      <button class="rate-btn active" data-rate="0.88">▶ Normal</button>
      <button class="rate-btn" data-rate="1.1">⚡ Rápida</button>
      <button class="rate-btn" data-rate="1.3">🚀 Muy rápida</button>
    </div>

    {{-- Tono --}}
    <div class="prefs-row">
      <span class="prefs-lbl">Tono</span>
      <div class="pitch-wrap">
        <input type="range" id="tts-pitch" min="0.7" max="1.3" step="0.05" value="1.0">
        <span id="tts-pitch-val">1.0</span>
      </div>
    </div>

    {{-- Acciones --}}
    <div class="prefs-row" style="margin-top:0.25rem; justify-content:space-between;">
      <button class="prefs-reset" id="tts-reset-btn">Restablecer valores</button>
      <span class="prefs-saved" id="tts-saved-msg">✓ Guardado</span>
    </div>

  </div>
  @endif
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
        <span>Publicado · {{ $narracion->fecha_publicacion->translatedFormat('j \d\e F \d\e Y') }}</span>

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
      @if($esFutura ?? false)

        {{-- FECHA FUTURA: próximamente --}}
        <div style="border:1px solid var(--parchment-dark); background:var(--parchment); padding:2rem; margin-top:1rem; text-align:center;">
          <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--gold); margin-bottom:0.75rem;">
            <i class="bi bi-clock-fill"></i> &nbsp;Publicación programada
          </div>
          <p style="font-family:var(--body); font-size:1.05rem; color:var(--ink-soft); margin-bottom:0.5rem; line-height:1.7;">
            Esta narración estará disponible el<br>
            <strong style="font-size:1.15rem; color:var(--ink);">{{ $narracion->fecha_publicacion->translatedFormat('j \d\e F \d\e Y') }}</strong>
          </p>
          @if(!($esSubscriptor ?? false) && !($esAdmin ?? false))
            <p style="font-family:var(--sans); font-size:0.72rem; color:var(--ink-muted); margin-top:1rem;">
              Suscríbete para recibir un aviso cuando esté disponible.
            </p>
            <a href="{{ route('suscripcion.form') }}"
               style="display:inline-block; margin-top:0.75rem; background:var(--accent); color:var(--parchment); font-family:var(--sans); font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.65rem 1.5rem; text-decoration:none;">
              Suscribirme gratis →
            </a>
          @endif
        </div>

      @elseif($narracion->estado === 'promocion')

        {{-- MODO PROMOCION: solo título + CTA, sin contenido --}}
        <div style="border:1px solid var(--parchment-dark); background:#fff; padding:1.5rem 2rem; margin-top:1rem; text-align:center;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--gold); margin-bottom:0.75rem;">
            <i class="bi bi-clock-fill"></i> Próximamente · Texto completo disponible en breve
          </div>
          <p style="font-size:0.9rem; color:var(--ink-muted); margin-bottom:1.25rem;">
            Suscríbete gratis para leer «{{ $narracion->titulo }}» en cuanto se publique y recibir un aviso.
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
        <div id="narracion-content"
             style="font-size:1.05rem; line-height:1.9; color:var(--ink-soft);
                    {{ !$esAdmin ? 'user-select:none; -webkit-user-select:none;' : '' }}">
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
              @php
                $siguiente    = $nodo->conexionesSalientes->first()->hasta;
                $sigNar       = $siguiente?->narracion;
                $hoyStr       = now()->toDateString();
                // Accesible = publicado/promocion Y fecha no futura
                $sigAccesible = $sigNar
                    && in_array($sigNar->estado, ['publicado', 'promocion'])
                    && $sigNar->fecha_publicacion->toDateString() <= $hoyStr;
                $sigFutura    = $sigNar
                    && in_array($sigNar->estado, ['publicado', 'promocion'])
                    && $sigNar->fecha_publicacion->toDateString() > $hoyStr;
              @endphp

              @if($sigAccesible)
                {{-- Siguiente disponible: enlace normal --}}
                <a href="{{ route('narraciones.show', $sigNar->slug) }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--parchment); background:var(--ink); padding:0.65rem 1.25rem; text-decoration:none; transition:background 0.2s;"
                   onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--ink)'">
                  La historia continúa &nbsp;→&nbsp; <em style="font-style:italic; font-family:var(--serif); text-transform:none; font-size:0.85rem; letter-spacing:0;">{{ $sigNar->titulo }}</em>
                </a>

              @elseif($sigFutura)
                {{-- Siguiente existe pero fecha futura: teaser --}}
                <div style="display:inline-flex; align-items:center; gap:0.5rem; font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--gold); border:1px solid var(--gold); padding:0.65rem 1.25rem; opacity:0.8;">
                  <i class="bi bi-clock"></i> Próxima entrega &nbsp;·&nbsp;
                  <em style="font-style:italic; font-family:var(--serif); text-transform:none; font-size:0.85rem; letter-spacing:0;">
                    {{ $sigNar->fecha_publicacion->translatedFormat('j \d\e F') }}
                  </em>
                </div>

              @elseif($sigNar)
                {{-- Siguiente es borrador (no publicado aún): teaser genérico --}}
                <div style="display:inline-flex; align-items:center; gap:0.5rem; font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink-muted); border:1px solid var(--parchment-dark); padding:0.65rem 1.25rem; opacity:0.65;">
                  <i class="bi bi-three-dots"></i> Próxima entrega en preparación
                </div>
              @endif
            @else
              <div style="font-family:var(--body); font-size:0.9rem; color:var(--ink-soft); margin-bottom:0.75rem;">¿Cómo sigue la historia?</div>
              <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                @foreach($nodo->conexionesSalientes as $cx)
                  @php
                    $cxNar = $cx->hasta?->narracion;
                    $cxAccesible = $cxNar
                        && in_array($cxNar->estado, ['publicado','promocion'])
                        && $cxNar->fecha_publicacion->toDateString() <= now()->toDateString();
                  @endphp
                  @if($cxAccesible)
                    <a href="{{ route('narraciones.show', $cxNar->slug) }}"
                       style="font-family:var(--sans); font-size:0.7rem; border:1px solid var(--ink); color:var(--ink); padding:0.4rem 0.9rem; text-decoration:none; transition:all 0.2s;"
                       onmouseover="this.style.background='var(--ink)';this.style.color='var(--parchment)'"
                       onmouseout="this.style.background='transparent';this.style.color='var(--ink)'"
                       title="{{ $cxNar->titulo }}">
                      {{ $cx->etiqueta ?: $cxNar->titulo }}
                    </a>
                  @elseif($cxNar)
                    <span style="font-family:var(--sans); font-size:0.7rem; border:1px solid var(--parchment-dark); color:var(--ink-muted); padding:0.4rem 0.9rem; opacity:0.6;"
                          title="Próximamente">
                      <i class="bi bi-three-dots"></i> {{ $cx->etiqueta ?: $cxNar->titulo }}
                    </span>
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

        <div style="display:flex; align-items:center; gap:1.5rem;">

          {{-- Descarga PDF: solo suscriptores/admins, narración publicada y sin fecha futura --}}
          @if(($esSubscriptor || $esAdmin) && $narracion->estado === 'publicado' && !$esFutura)
            <button onclick="document.getElementById('pdf-modal').style.display='flex'"
               style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.15em;
                      text-transform:uppercase; color:var(--ink-muted);
                      background:none; border:none; cursor:pointer; padding:0;
                      display:inline-flex; align-items:center; gap:0.4rem;"
               onmouseover="this.style.color='var(--ink)';"
               onmouseout="this.style.color='var(--ink-muted)';">
              <i class="bi bi-file-earmark-arrow-down" style="font-size:0.9rem;"></i>
              Descargar PDF
            </button>

            {{-- Modal contraseña PDF --}}
            <div id="pdf-modal"
                 style="display:none; position:fixed; inset:0; z-index:200;
                        background:rgba(26,26,26,0.55); align-items:center; justify-content:center;"
                 onclick="if(event.target===this) this.style.display='none'">
              <div style="background:var(--parchment); border:1px solid var(--parchment-dark);
                          max-width:380px; width:90%; padding:2rem 2rem 1.75rem;">

                {{-- Icono + título --}}
                <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em;
                            text-transform:uppercase; color:var(--ink-muted); margin-bottom:1rem;">
                  Descarga · PDF
                </div>
                <p style="font-family:var(--serif); font-size:1.05rem; font-weight:700;
                          color:var(--ink); margin-bottom:0.75rem; line-height:1.4;">
                  El documento está protegido
                </p>
                <p style="font-family:var(--body); font-size:0.88rem; color:var(--ink-soft);
                          line-height:1.7; margin-bottom:0.5rem;">
                  Cuando el lector PDF te pida contraseña, introduce tu <strong>correo electrónico</strong>.
                </p>
                <p style="font-family:var(--sans); font-size:0.7rem; letter-spacing:0.04em;
                          color:var(--ink-muted); background:var(--parchment-dark);
                          padding:0.5rem 0.75rem; margin-bottom:1.5rem;">
                  <i class="bi bi-key" style="margin-right:0.4rem;"></i>
                  Tu correo electrónico = contraseña
                </p>

                {{-- Botones --}}
                <div style="display:flex; gap:0.75rem; justify-content:flex-end; align-items:center;">
                  <button onclick="document.getElementById('pdf-modal').style.display='none'"
                          style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.12em;
                                 text-transform:uppercase; color:var(--ink-muted);
                                 background:none; border:none; cursor:pointer; padding:0.5rem;">
                    Cancelar
                  </button>
                  <a href="{{ route('narraciones.pdf', $narracion->slug) }}"
                     onclick="setTimeout(()=>document.getElementById('pdf-modal').style.display='none', 400)"
                     style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.15em;
                            text-transform:uppercase; color:var(--parchment);
                            background:var(--ink); padding:0.6rem 1.25rem;
                            text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="bi bi-download"></i> Descargar
                  </a>
                </div>

              </div>
            </div>
          @endif

          {{-- ── Reacciones 👍 👎 (solo narraciones publicadas con fecha pasada) ── --}}
          @if($narracion->estado === 'publicado' && !$esFutura)
            <div style="display:inline-flex; align-items:center; gap:0.5rem;">
              <button id="btn-like"
                      data-slug="{{ $narracion->slug }}"
                      data-tipo="like"
                      title="Me ha gustado"
                      style="background:none; border:1px solid var(--parchment-dark); cursor:pointer;
                             padding:0.3rem 0.7rem; font-size:0.85rem; display:inline-flex;
                             align-items:center; gap:0.3rem; color:var(--ink-muted);
                             font-family:var(--sans); font-size:0.62rem; letter-spacing:0.05em;
                             transition:border-color 0.2s, color 0.2s;">
                👍 <span id="count-like">{{ $narracion->count_feedback }}</span>
              </button>
              <button id="btn-dislike"
                      data-slug="{{ $narracion->slug }}"
                      data-tipo="dislike"
                      title="No me ha convencido"
                      style="background:none; border:1px solid var(--parchment-dark); cursor:pointer;
                             padding:0.3rem 0.7rem; font-size:0.85rem; display:inline-flex;
                             align-items:center; gap:0.3rem; color:var(--ink-muted);
                             font-family:var(--sans); font-size:0.62rem; letter-spacing:0.05em;
                             transition:border-color 0.2s, color 0.2s;">
                👎 <span id="count-dislike">{{ $narracion->count_dislikes }}</span>
              </button>
            </div>
          @endif

          <span style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink-muted);">
            {{ $narracion->count_read }} {{ $narracion->count_read === 1 ? 'lectura' : 'lecturas' }}
          </span>
        </div>
      </div>

    </div>
  </div>

  {{-- ── COMENTARIOS ── --}}
  @if(!$esFutura)
  <div id="comentarios" style="max-width:720px; margin:3rem auto 0; padding:0 1.5rem;">

    {{-- Cabecera --}}
    <div class="grid-label" style="margin-bottom:1.5rem;">
      Comentarios
      @if($comentarios->count() > 0)
        <span>{{ $comentarios->count() }} {{ $comentarios->count() === 1 ? 'comentario' : 'comentarios' }}</span>
      @endif
    </div>

    {{-- Mensajes de sesión --}}
    @if(session('success') && str_contains(session('success'), 'omentario'))
      <div style="font-family:var(--sans); font-size:0.72rem; letter-spacing:0.1em;
                  background:var(--parchment-mid); border-left:3px solid var(--accent);
                  color:var(--ink); padding:0.75rem 1rem; margin-bottom:1.5rem;">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error') && str_contains(session('error'), 'omentario'))
      <div style="font-family:var(--sans); font-size:0.72rem; letter-spacing:0.1em;
                  background:var(--parchment-mid); border-left:3px solid #c0392b;
                  color:var(--ink); padding:0.75rem 1rem; margin-bottom:1.5rem;">
        {{ session('error') }}
      </div>
    @endif

    {{-- Lista de comentarios --}}
    @if($comentarios->isEmpty())
      <p style="font-family:var(--body); font-size:0.9rem; color:var(--ink-muted);
                font-style:italic; margin-bottom:2rem;">
        Sé el primero en dejar un comentario.
      </p>
    @else
      <div style="display:flex; flex-direction:column; gap:1.25rem; margin-bottom:2.5rem;">
        @foreach($comentarios as $com)
          @php
            $esPropio = $subscriber && $com->subscriber_id === $subscriber->id;
          @endphp
          <div id="com-{{ $com->id }}"
               style="border:1px solid {{ $esPropio ? 'var(--accent)' : 'var(--parchment-dark)' }};
                      padding:1.1rem 1.25rem; background:var(--parchment-mid); position:relative;">

            {{-- Autor + fecha + acciones --}}
            <div style="display:flex; align-items:center; justify-content:space-between;
                        margin-bottom:0.65rem; gap:0.5rem; flex-wrap:wrap;">
              <span style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.2em;
                           text-transform:uppercase; color:{{ $esPropio ? 'var(--accent)' : 'var(--ink-soft)' }};">
                <i class="bi bi-person-circle" style="margin-right:0.25rem;"></i>
                {{ $com->alias }}
                @if($esPropio)
                  <span style="font-size:0.55rem; opacity:0.7; margin-left:0.3rem;">(tú)</span>
                @endif
              </span>
              <div style="display:flex; align-items:center; gap:0.75rem;">
                <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.1em;
                             color:var(--ink-muted);">
                  {{ $com->created_at->translatedFormat('j \d\e F \d\e Y') }}
                </span>

                {{-- Botones editar/borrar: propio suscriptor o admin --}}
                @if($esPropio || $esAdmin)
                  <button onclick="toggleEdit({{ $com->id }})"
                          style="background:none; border:none; cursor:pointer; color:var(--ink-muted);
                                 font-size:0.72rem; padding:0.1rem 0.3rem;"
                          title="Modificar"
                          onmouseover="this.style.color='var(--ink)'"
                          onmouseout="this.style.color='var(--ink-muted)'">
                    <i class="bi bi-pencil"></i>
                  </button>

                  <form method="POST"
                        action="{{ route('narraciones.comment.delete', [$narracion->slug, $com->id]) }}"
                        style="display:inline;"
                        onsubmit="return confirm('¿Eliminar este comentario? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            style="background:none; border:none; cursor:pointer; color:var(--ink-muted);
                                   font-size:0.72rem; padding:0.1rem 0.3rem;"
                            title="Eliminar"
                            onmouseover="this.style.color='#c0392b'"
                            onmouseout="this.style.color='var(--ink-muted)'">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @endif
              </div>
            </div>

            {{-- Texto del comentario --}}
            <p id="com-text-{{ $com->id }}"
               style="font-family:var(--body); font-size:0.92rem; color:var(--ink);
                      line-height:1.7; margin:0; white-space:pre-wrap;">{{ $com->contenido }}</p>

            {{-- Formulario de edición (oculto por defecto) --}}
            @if($esPropio || $esAdmin)
              <form id="edit-form-{{ $com->id }}"
                    method="POST"
                    action="{{ route('narraciones.comment.update', [$narracion->slug, $com->id]) }}"
                    style="display:none; margin-top:0.85rem;"
                    onsubmit="return confirm('¿Confirmas que quieres guardar los cambios?')">
                @csrf
                @method('PUT')
                <textarea name="contenido"
                          rows="4"
                          maxlength="1000"
                          required
                          style="width:100%; box-sizing:border-box; resize:vertical;
                                 font-family:var(--body); font-size:0.92rem; color:var(--ink);
                                 background:var(--parchment); border:1px solid var(--parchment-dark);
                                 padding:0.65rem; outline:none; line-height:1.6;"
                          onfocus="this.style.borderColor='var(--ink-soft)'"
                          onblur="this.style.borderColor='var(--parchment-dark)'">{{ $com->contenido }}</textarea>
                <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:0.5rem;">
                  <button type="button"
                          onclick="toggleEdit({{ $com->id }})"
                          style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.12em;
                                 text-transform:uppercase; color:var(--ink-muted);
                                 background:none; border:1px solid var(--parchment-dark); cursor:pointer;
                                 padding:0.45rem 0.9rem;">
                    Cancelar
                  </button>
                  <button type="submit"
                          style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.12em;
                                 text-transform:uppercase; color:var(--parchment);
                                 background:var(--ink); border:none; cursor:pointer;
                                 padding:0.45rem 0.9rem; transition:background 0.2s;"
                          onmouseover="this.style.background='var(--accent)'"
                          onmouseout="this.style.background='var(--ink)'">
                    Guardar cambios
                  </button>
                </div>
              </form>
            @endif

          </div>
        @endforeach
      </div>
    @endif

    {{-- Área de acción para el suscriptor --}}
    @if($esSubscriptor || $esAdmin)

      @if(!$miComentario)
        {{-- Aún no ha comentado: formulario de nuevo comentario --}}
        <form id="form-nuevo-comentario"
              method="POST"
              action="{{ route('narraciones.comment', $narracion->slug) }}"
              style="border:1px solid var(--parchment-dark); padding:1.5rem; background:var(--parchment-mid);"
              onsubmit="return confirm('¿Confirmas que quieres publicar este comentario?')">
          @csrf

          <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em;
                      text-transform:uppercase; color:var(--ink-muted); margin-bottom:1rem;">
            <i class="bi bi-pencil-square" style="margin-right:0.3rem;"></i>
            Dejar un comentario
          </div>

          <textarea name="contenido"
                    rows="4"
                    maxlength="1000"
                    placeholder="Escribe tu comentario…"
                    required
                    style="width:100%; box-sizing:border-box; resize:vertical;
                           font-family:var(--body); font-size:0.92rem; color:var(--ink);
                           background:var(--parchment); border:1px solid var(--parchment-dark);
                           padding:0.75rem; outline:none; line-height:1.6; min-height:90px;"
                    onfocus="this.style.borderColor='var(--ink-soft)'"
                    onblur="this.style.borderColor='var(--parchment-dark)'">{{ old('contenido') }}</textarea>

          @error('contenido')
            <p style="font-family:var(--sans); font-size:0.68rem; color:#c0392b;
                      margin:0.4rem 0 0;">{{ $message }}</p>
          @enderror

          <div style="display:flex; justify-content:flex-end; margin-top:0.85rem;">
            <button type="submit"
                    style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.15em;
                           text-transform:uppercase; color:var(--parchment);
                           background:var(--ink); border:none; cursor:pointer;
                           padding:0.65rem 1.5rem; transition:background 0.2s;"
                    onmouseover="this.style.background='var(--accent)'"
                    onmouseout="this.style.background='var(--ink)'">
              <i class="bi bi-send" style="margin-right:0.4rem;"></i> Enviar comentario
            </button>
          </div>
        </form>
      @endif

    @else
      {{-- Visitante no suscriptor: invitación --}}
      <div style="border:1px solid var(--parchment-dark); padding:1.25rem 1.5rem;
                  background:var(--parchment-mid); text-align:center;">
        <p style="font-family:var(--body); font-size:0.92rem; color:var(--ink-soft);
                  margin:0 0 1rem;">
          Solo los suscriptores pueden dejar comentarios.
        </p>
        <a href="{{ route('suscripcion.form', ['slug' => $narracion->slug]) }}"
           style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.15em;
                  text-transform:uppercase; color:var(--ink); border:1px solid var(--ink);
                  padding:0.6rem 1.25rem; text-decoration:none; display:inline-block;
                  transition:all 0.2s;"
           onmouseover="this.style.background='var(--ink)';this.style.color='var(--parchment)'"
           onmouseout="this.style.background='transparent';this.style.color='var(--ink)'">
          <i class="bi bi-envelope-heart" style="margin-right:0.4rem;"></i>
          Suscribirme para comentar
        </a>
      </div>
    @endif

  </div>

  {{-- JS: toggle edición inline --}}
  <script>
  function toggleEdit(id) {
    const text = document.getElementById('com-text-' + id);
    const form = document.getElementById('edit-form-' + id);
    if (!text || !form) return;
    const editing = form.style.display !== 'none';
    form.style.display = editing ? 'none' : 'block';
    text.style.display = editing ? '' : 'none';
  }
  </script>

  @endif

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
        <div class="card-author">{{ $story->fecha_publicacion->translatedFormat('j \d\e F \d\e Y') }}</div>
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
  const synth    = window.speechSynthesis;
  const playBtn  = document.getElementById('tts-play-btn');
  const stopBtn  = document.getElementById('tts-stop-btn');
  const label    = document.getElementById('tts-label');
  const fill     = document.getElementById('tts-fill');
  const pct      = document.getElementById('tts-pct');

  // Elementos del panel de preferencias (pueden ser null si no es suscriptor)
  const prefsBtn   = document.getElementById('tts-prefs-btn');
  const prefsPanel = document.getElementById('tts-prefs-panel');
  const voiceSel   = document.getElementById('tts-voice-panel');
  const pitchSlider= document.getElementById('tts-pitch');
  const pitchVal   = document.getElementById('tts-pitch-val');
  const savedMsg   = document.getElementById('tts-saved-msg');
  const resetBtn   = document.getElementById('tts-reset-btn');
  const rateBtns   = document.querySelectorAll('.rate-btn');

  const texto      = {{ Js::from($textoVoz) }};
  const totalChars = texto.length;

  // Ocultar barra TTS en dispositivos móviles (no funciona correctamente)
  const esMobil = ('ontouchstart' in window || navigator.maxTouchPoints > 0)
                  && window.innerWidth < 1024;
  if (!synth || esMobil) { document.getElementById('tts-bar').style.display = 'none'; return; }

  let utter = null;
  let state = 'idle';

  /* ── Persistencia de preferencias ──────────────────────────────────────── */
  const PREFS_KEY = 'tts_prefs_v1';
  const DEFAULTS  = { voice: null, rate: 0.88, pitch: 1.0 };

  function loadPrefs() {
    try { return Object.assign({}, DEFAULTS, JSON.parse(localStorage.getItem(PREFS_KEY))); }
    catch { return { ...DEFAULTS }; }
  }
  function savePrefs(prefs) {
    localStorage.setItem(PREFS_KEY, JSON.stringify(prefs));
    if (savedMsg) {
      savedMsg.classList.add('show');
      setTimeout(() => savedMsg.classList.remove('show'), 1800);
    }
  }

  let prefs = loadPrefs();

  /* ── Helpers de voz ─────────────────────────────────────────────────────── */
  const MALE_NAMES = /\b(álvaro|alvaro|pablo|jorge|raúl|raul|sergio|andrés|andres|miguel|carlos|diego|enrique|antonio|juan|pedro|lucas|martín|martin|alejandro|héctor|hector)\b/i;
  function isMicrosoft(v)  { return /microsoft/i.test(v.name); }
  function isOnlineVoice(v){ return /online|natural/i.test(v.name) || !v.localService; }
  function isMale(v)       { return MALE_NAMES.test(v.name); }

  function getVoices() {
    const es = synth.getVoices().filter(v => v.lang.startsWith('es'));
    const noGoogle = es.filter(v => !/google/i.test(v.name));
    const list = noGoogle.length > 0 ? noGoogle : es;
    list.sort((a, b) => {
      const esA = a.lang === 'es-ES' ? 0 : 1, esB = b.lang === 'es-ES' ? 0 : 1;
      if (esA !== esB) return esA - esB;
      const msA = isMicrosoft(a) ? 0 : 1, msB = isMicrosoft(b) ? 0 : 1;
      if (msA !== msB) return msA - msB;
      const onA = isOnlineVoice(a) ? 0 : 1, onB = isOnlineVoice(b) ? 0 : 1;
      return onA - onB;
    });
    return list;
  }

  function shortVoiceName(v) {
    return v.name
      .replace(/\s*online\s*/i, ' ')
      .replace(/\s*\(natural\)\s*/i, '')
      .replace(/\s*-\s*spanish\s*(\(.*?\))?\s*/i, '')
      .replace(/\s{2,}/g, ' ').trim();
  }

  function getBestVoice(voices) {
    if (prefs.voice) {
      const found = voices.find(v => v.name === prefs.voice);
      if (found) return found;
    }
    return voices[0] || null;
  }

  /* ── Poblar selector de voz (panel) ────────────────────────────────────── */
  function populateVoiceSelect(voices) {
    if (!voiceSel) return;
    voiceSel.innerHTML = '';
    voices.forEach(v => {
      const opt = document.createElement('option');
      opt.value = v.name;
      const mark = isOnlineVoice(v) ? ' ★' : '';
      const sex  = isMale(v) ? ' ♂' : '';
      opt.textContent = shortVoiceName(v) + sex + mark;
      if (v.name === prefs.voice) opt.selected = true;
      voiceSel.appendChild(opt);
    });
  }

  /* ── Sincronizar UI del panel con prefs actuales ───────────────────────── */
  function syncPanelUI() {
    // Velocidad: marcar botón activo
    rateBtns.forEach(btn => {
      btn.classList.toggle('active', parseFloat(btn.dataset.rate) === prefs.rate);
    });
    // Tono
    if (pitchSlider) {
      pitchSlider.value = prefs.pitch;
      pitchVal.textContent = prefs.pitch.toFixed(1);
    }
  }

  /* ── Panel de preferencias: eventos ────────────────────────────────────── */
  if (prefsBtn && prefsPanel) {
    prefsBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const open = prefsPanel.classList.toggle('open');
      prefsBtn.classList.toggle('open', open);
    });
    document.addEventListener('click', (e) => {
      if (!prefsPanel.contains(e.target) && e.target !== prefsBtn) {
        prefsPanel.classList.remove('open');
        prefsBtn.classList.remove('open');
      }
    });
  }

  // Cambio de voz
  if (voiceSel) {
    voiceSel.addEventListener('change', () => {
      prefs.voice = voiceSel.value;
      savePrefs(prefs);
      if (state !== 'idle') { synth.cancel(); startReading(); }
    });
  }

  // Cambio de velocidad (presets)
  rateBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      prefs.rate = parseFloat(btn.dataset.rate);
      savePrefs(prefs);
      syncPanelUI();
      if (state !== 'idle') { synth.cancel(); startReading(); }
    });
  });

  // Cambio de tono
  if (pitchSlider) {
    pitchSlider.addEventListener('input', () => {
      prefs.pitch = parseFloat(pitchSlider.value);
      pitchVal.textContent = prefs.pitch.toFixed(1);
    });
    pitchSlider.addEventListener('change', () => {
      prefs.pitch = parseFloat(pitchSlider.value);
      savePrefs(prefs);
      if (state !== 'idle') { synth.cancel(); startReading(); }
    });
  }

  // Reset
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      prefs = { ...DEFAULTS };
      savePrefs(prefs);
      syncPanelUI();
      const voices = getVoices();
      populateVoiceSelect(voices);
      if (state !== 'idle') { synth.cancel(); startReading(); }
    });
  }

  /* ── Progreso ───────────────────────────────────────────────────────────── */
  function setProgress(charIndex, done) {
    const p = done ? 100 : Math.min(99, Math.round((charIndex / totalChars) * 100));
    fill.style.width = p + '%';
    pct.textContent  = p + '%';
  }
  function resetProgress() { fill.style.width = '0%'; pct.textContent = ''; }

  /* ── Estado visual ──────────────────────────────────────────────────────── */
  function setState(s) {
    state = s;
    if (s === 'playing') {
      playBtn.textContent = '⏸';
      playBtn.classList.add('playing');
      label.textContent = 'Reproduciendo…';
    } else if (s === 'paused') {
      playBtn.textContent = '▶';
      playBtn.classList.remove('playing');
      label.textContent = 'En pausa · toca para continuar';
    } else {
      playBtn.textContent = '▶';
      playBtn.classList.remove('playing');
      label.textContent = 'Escuchar narración';
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
    utter.rate  = prefs.rate;
    utter.pitch = prefs.pitch;
    if (voice) utter.voice = voice;

    utter.onboundary = (e) => { if (e.name === 'word') setProgress(e.charIndex, false); };
    utter.onend  = () => {
      setProgress(0, true);
      label.textContent = 'Narración completada';
      setTimeout(() => setState('idle'), 1800);
    };
    utter.onerror = (e) => { if (e.error !== 'interrupted') setState('idle'); };

    synth.speak(utter);
    setState('playing');
  }

  /* ── Controles principales ──────────────────────────────────────────────── */
  playBtn.addEventListener('click', () => {
    if      (state === 'idle')    startReading();
    else if (state === 'playing') { synth.pause();  setState('paused'); }
    else if (state === 'paused')  { synth.resume(); setState('playing'); }
  });

  stopBtn.addEventListener('click', () => { synth.cancel(); setState('idle'); utter = null; });
  window.addEventListener('beforeunload', () => synth.cancel());

  /* ── Inicializar voces ──────────────────────────────────────────────────── */
  function initVoices() {
    const voices = getVoices();
    if (voices.length === 0) return;
    populateVoiceSelect(voices);
    syncPanelUI();
  }

  if (synth.getVoices().length > 0) {
    initVoices();
  } else {
    synth.onvoiceschanged = () => { synth.onvoiceschanged = null; initVoices(); };
    setTimeout(initVoices, 1500);
  }
})();
</script>

{{-- ── Reacciones: JS AJAX ────────────────────────────────────────────────── --}}
@if($narracion->estado === 'publicado' && !$esFutura)
<script>
(function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
  const VOTED_KEY = 'reaccion_{{ $narracion->id }}';

  const btnLike    = document.getElementById('btn-like');
  const btnDislike = document.getElementById('btn-dislike');
  if (!btnLike || !btnDislike) return;

  // Restaurar estado visual si ya votó en esta sesión
  const votoGuardado = localStorage.getItem(VOTED_KEY);
  if (votoGuardado) aplicarEstadoVotado(votoGuardado);

  function aplicarEstadoVotado(tipo) {
    const btn = tipo === 'like' ? btnLike : btnDislike;
    btn.style.borderColor = tipo === 'like' ? '#16a34a' : '#dc2626';
    btn.style.color       = tipo === 'like' ? '#16a34a' : '#dc2626';
    btnLike.disabled    = true;
    btnDislike.disabled = true;
    btnLike.style.cursor    = 'default';
    btnDislike.style.cursor = 'default';
  }

  async function enviarReaccion(tipo) {
    if (localStorage.getItem(VOTED_KEY)) return; // ya votó

    try {
      const res  = await fetch('{{ route('narraciones.react', $narracion->slug) }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body:    JSON.stringify({ tipo }),
      });
      const data = await res.json();

      if (data.ok) {
        document.getElementById('count-like').textContent    = data.likes;
        document.getElementById('count-dislike').textContent = data.dislikes;
        localStorage.setItem(VOTED_KEY, tipo);
        aplicarEstadoVotado(tipo);
      } else if (data.motivo === 'ya_votado') {
        // El servidor rechazó el voto duplicado
        localStorage.setItem(VOTED_KEY, tipo); // sincronizar localStorage
        aplicarEstadoVotado(tipo);
      }
    } catch (e) {
      console.error('Error enviando reacción:', e);
    }
  }

  btnLike.addEventListener('click',    () => enviarReaccion('like'));
  btnDislike.addEventListener('click', () => enviarReaccion('dislike'));
})();
</script>
@endif

@if(!$esAdmin)
{{-- ── Protección de contenido (no aplica a admins) ────────────────────── --}}
<div id="copy-toast"
     style="display:none; position:fixed; bottom:2rem; left:50%; transform:translateX(-50%);
            z-index:500; background:var(--ink); color:var(--parchment);
            font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em;
            text-transform:uppercase; padding:0.65rem 1.4rem;
            box-shadow:0 4px 16px rgba(0,0,0,0.25); white-space:nowrap;
            transition:opacity 0.4s;">
  <i class="bi bi-lock-fill" style="margin-right:0.4rem;"></i>
  Contenido protegido · {{ __('site.name') }}
</div>

<script>
(function () {
  const content = document.getElementById('narracion-content');
  if (!content) return;

  const titulo   = {{ Js::from($narracion->titulo) }};
  const siteName = {{ Js::from(__('site.name')) }};
  const siteUrl  = {{ Js::from(url('/')) }};
  const atribucion = '\n\n— «' + titulo + '» · ' + siteName + ' · ' + siteUrl;

  // Toast de aviso
  function showToast() {
    const toast = document.getElementById('copy-toast');
    toast.style.display = 'block';
    toast.style.opacity = '1';
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => toast.style.display = 'none', 400);
    }, 2500);
  }

  // 1. Bloquear menú contextual sobre el contenido
  content.addEventListener('contextmenu', e => {
    e.preventDefault();
    showToast();
  });

  // 2. Interceptar copia: reemplazar portapapeles con atribución
  document.addEventListener('copy', e => {
    const sel = window.getSelection();
    if (!sel || sel.isCollapsed) return;
    // Solo actuar si la selección toca el bloque de contenido
    if (!content.contains(sel.anchorNode) && !content.contains(sel.focusNode)) return;

    const textoSeleccionado = sel.toString().trim();
    if (!textoSeleccionado) return;

    e.clipboardData.setData('text/plain', textoSeleccionado + atribucion);
    e.preventDefault();
    showToast();
  });
})();
</script>
@endif

@endsection
