@extends('layouts.literario')

@section('title', 'Novedades · ' . __('site.name'))

@section('content')
<div class="container">
  <div style="max-width:720px; margin:0 auto;">

    {{-- Cabecera --}}
    <div style="margin-bottom:3rem; border-bottom:1px solid var(--parchment-dark); padding-bottom:2rem;">
      <div class="section-label">Historial de versiones</div>
      <h1 style="font-family:var(--serif); font-size:2.25rem; font-weight:700; color:var(--ink); margin-bottom:0.5rem;">
        Novedades
      </h1>
      <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.95rem; line-height:1.75;">
        Aquí encontrarás un registro de todo lo que va mejorando en {{ __('site.name') }},
        explicado sin tecnicismos.
      </p>
    </div>

    {{-- ── v1.2.0 ── --}}
    <div style="margin-bottom:3.5rem;">

      <div style="display:flex; align-items:baseline; gap:1.25rem; margin-bottom:1.25rem; flex-wrap:wrap;">
        <span style="font-family:var(--serif); font-size:1.5rem; font-weight:700; color:var(--ink);">v1.2.0</span>
        <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--parchment); background:var(--accent); padding:0.2rem 0.65rem;">
          Nuevo
        </span>
        <span style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted);">Mayo 2026</span>
      </div>

      <div style="border-left:2px solid var(--accent); padding-left:1.5rem;">

        <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.95rem; line-height:1.8; margin-bottom:1.5rem;">
          La lectura deja de ser una experiencia solitaria. Los suscriptores pueden ahora reaccionar,
          comentar y compartir las narraciones que les llegan al alma.
        </p>

        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--accent); margin-bottom:0.5rem;">
            <i class="bi bi-hand-thumbs-up"></i> &nbsp;Reacciones
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Al final de cada narración publicada aparecen dos botones: 👍 si el texto te ha tocado,
            👎 si no era lo que esperabas. Cualquier lector puede reaccionar una sola vez, sin necesidad
            de suscribirse.
          </p>
        </div>

        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--accent); margin-bottom:0.5rem;">
            <i class="bi bi-chat-text"></i> &nbsp;Comentarios
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Los suscriptores verificados pueden dejar un comentario en cada narración. Solo uno,
            para que cada voz cuente. Puede editarse o eliminarse en cualquier momento.
            Los comentarios son públicos y aparecen bajo el texto.
          </p>
        </div>

        <div style="margin-bottom:0;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--accent); margin-bottom:0.5rem;">
            <i class="bi bi-whatsapp"></i> &nbsp;Compartir
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Dos botones discretos bajo el título y al pie de cada narración permiten enviarla
            por WhatsApp con un mensaje ya redactado, o copiar el enlace directo al portapapeles.
            En móvil, se usa el menú de compartir nativo del dispositivo.
          </p>
        </div>

      </div>
    </div>

    {{-- ── v1.1.0 ── --}}
    <div style="margin-bottom:3.5rem;">

      <div style="display:flex; align-items:baseline; gap:1.25rem; margin-bottom:1.25rem; flex-wrap:wrap;">
        <span style="font-family:var(--serif); font-size:1.5rem; font-weight:700; color:var(--ink);">v1.1.0</span>
        <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink); background:var(--parchment-dark); padding:0.2rem 0.65rem;">
          Mejoras
        </span>
        <span style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted);">Mayo 2026</span>
      </div>

      <div style="border-left:2px solid var(--parchment-dark); padding-left:1.5rem;">

        <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.95rem; line-height:1.8; margin-bottom:1.5rem;">
          Un conjunto de mejoras centradas en la experiencia de lectura: escuchar, buscar, descargar
          y seguir el hilo de una historia a través de varias narraciones.
        </p>

        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-search"></i> &nbsp;Búsqueda de narraciones
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            El catálogo incorpora un buscador por título. Basta escribir una palabra para filtrar
            las narraciones al momento, sin recargar la página.
          </p>
        </div>

        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-volume-up"></i> &nbsp;Escuchar la narración
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Una barra fija en la parte superior permite escuchar cualquier narración en voz alta.
            Los suscriptores pueden elegir la voz, la velocidad y el tono desde un panel de preferencias
            que se guarda para futuras visitas.
          </p>
        </div>

        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-diagram-3"></i> &nbsp;Líneas argumentales
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Algunas narraciones forman parte de una historia más grande. Al pie del texto aparece
            la continuación, o varias bifurcaciones si la historia ofrece caminos alternativos.
            De este modo el lector puede seguir el hilo sin perder el rumbo.
          </p>
        </div>

        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-file-earmark-arrow-down"></i> &nbsp;Descarga en PDF
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Los suscriptores pueden descargarse cualquier narración publicada en formato PDF.
            El documento queda protegido con contraseña —el propio correo electrónico del suscriptor—
            y lleva una marca discreta que identifica para quién fue generado.
          </p>
        </div>

        <div style="margin-bottom:0;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-shield-lock"></i> &nbsp;Protección del texto
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            El contenido de las narraciones no puede copiarse directamente. Si alguien intenta
            seleccionar y copiar un fragmento, el texto que llega al portapapeles incluye automáticamente
            la fuente y el enlace a la narración original.
          </p>
        </div>

      </div>
    </div>

    {{-- ── v1.0.0 ── --}}
    <div style="margin-bottom:3.5rem;">

      <div style="display:flex; align-items:baseline; gap:1.25rem; margin-bottom:1.25rem; flex-wrap:wrap;">
        <span style="font-family:var(--serif); font-size:1.5rem; font-weight:700; color:var(--ink);">v1.0.0</span>
        <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--parchment); background:var(--ink); padding:0.2rem 0.65rem;">
          Versión inicial
        </span>
        <span style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted);">Mayo 2026</span>
      </div>

      <div style="border-left:2px solid var(--parchment-dark); padding-left:1.5rem;">

        <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.95rem; line-height:1.8; margin-bottom:1.5rem;">
          Primera versión pública de <em>{{ __('site.name') }}</em>. El proyecto nace como un espacio
          personal para publicar y compartir narraciones, con especial cuidado en la experiencia
          de lectura y en el control de quién puede acceder a cada texto.
        </p>

        {{-- Suscripción --}}
        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--accent); margin-bottom:0.5rem;">
            <i class="bi bi-envelope-heart"></i> &nbsp;Acceso por suscripción
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Para leer contenido exclusivo no hace falta crear ninguna cuenta. Basta con dejar el correo
            electrónico, confirmar el enlace que llega al buzón, y ya está. Sin contraseñas que recordar.
            La baja también es inmediata desde el mismo email.
          </p>
        </div>

        {{-- Niveles de acceso --}}
        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-shield"></i> &nbsp;Tres niveles de acceso por narración
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Cada texto puede ser público (visible para cualquier visitante), exclusivo para suscriptores,
            o completamente privado. El autor decide en cada caso.
          </p>
        </div>

        {{-- Estado promoción --}}
        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--gold); margin-bottom:0.5rem;">
            <i class="bi bi-eye"></i> &nbsp;Vista previa de narraciones
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Algunas narraciones pueden mostrarse en modo adelanto: el lector ve las primeras líneas
            y, si le pica la curiosidad, puede suscribirse para leer el texto completo.
          </p>
        </div>

        {{-- Publicación programada --}}
        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-calendar-event"></i> &nbsp;Publicación programada
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Es posible preparar una narración con antelación y programar la fecha en que aparecerá
            en el catálogo. Hasta entonces permanece invisible para los lectores.
          </p>
        </div>

        {{-- Contador de palabras --}}
        <div style="margin-bottom:1.25rem;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-fonts"></i> &nbsp;Contador de palabras
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            Cada narración muestra cuántas palabras tiene, para que el lector sepa de antemano
            si tiene cinco minutos o media hora por delante.
          </p>
        </div>

        {{-- Roles --}}
        <div style="margin-bottom:0;">
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:0.5rem;">
            <i class="bi bi-people"></i> &nbsp;Editores y administración
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.9rem; line-height:1.75;">
            El sitio admite varios autores con su propio espacio de trabajo. Cada editor gestiona
            únicamente sus propias narraciones. El administrador tiene visión completa del conjunto.
          </p>
        </div>

      </div>
    </div>

    {{-- Volver --}}
    <div style="padding-top:1.5rem; border-top:1px solid var(--parchment-dark);">
      <a href="{{ route('narraciones.index') }}" class="read-more">← Volver al catálogo</a>
    </div>

  </div>
</div>
@endsection
