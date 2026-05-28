<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    /* ── Tipografía base ── */
    @font-face { }
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: Georgia, 'Times New Roman', serif;
      font-size: 11pt;
      line-height: 1.85;
      color: #1a1a1a;
      background: #ffffff;
    }

    /* ── Cabecera del documento ── */
    .doc-header {
      border-bottom: 0.5pt solid #c8bfb0;
      padding-bottom: 18pt;
      margin-bottom: 28pt;
    }
    .doc-site {
      font-family: 'Courier New', Courier, monospace;
      font-size: 6.5pt;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      color: #8a7f74;
      margin-bottom: 10pt;
    }
    .doc-title {
      font-size: 22pt;
      font-weight: bold;
      line-height: 1.25;
      color: #1a1a1a;
      margin-bottom: 8pt;
    }
    .doc-meta {
      font-family: 'Courier New', Courier, monospace;
      font-size: 7pt;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: #a09080;
    }
    .doc-divider {
      border: none;
      border-top: 0.5pt solid #c8bfb0;
      margin: 14pt 0;
    }

    /* ── Contenido narrativo ── */
    .narracion-body p {
      margin-bottom: 1em;
      text-indent: 1.5em;
    }
    .narracion-body p:first-child {
      text-indent: 0;
    }
    .narracion-body p:first-child::first-letter {
      font-size: 2.8em;
      font-weight: bold;
      float: left;
      line-height: 0.8;
      margin-right: 4pt;
      margin-top: 4pt;
      color: #1a1a1a;
    }
    .narracion-body br { display: block; margin-bottom: 0.5em; }
    .narracion-body em { font-style: italic; }
    .narracion-body strong { font-weight: bold; }

    /* ── Pie de página personalizado ── */
    .custom-footer {
      font-family: 'Courier New', Courier, monospace;
      font-size: 6pt;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: #b0a898;
      text-align: center;
      margin-top: 28pt;
      padding-top: 10pt;
      border-top: 0.5pt solid #e0d8d0;
    }
  </style>
</head>
<body>

  {{-- Cabecera --}}
  <div class="doc-header">
    <div class="doc-site">{{ __('site.name') }} &nbsp;·&nbsp; {{ __('site.subtitle') }}</div>
    <div class="doc-title">{{ $narracion->titulo }}</div>
    <div class="doc-meta">
      {{ $narracion->fecha_publicacion->translatedFormat('j \d\e F \d\e Y') }}
      &nbsp;·&nbsp;
      {{ $narracion->palabra_count }} palabras
    </div>
    <hr class="doc-divider">
  </div>

  {{-- Cuerpo --}}
  <div class="narracion-body">
    {!! $narracion->contenido !!}
  </div>

  {{-- Pie de autoría personalizada --}}
  <div class="custom-footer">
    Documento generado para &nbsp;<strong>{{ $emailSuscriptor }}</strong>
    &nbsp;·&nbsp;
    {{ __('site.name') }}
    &nbsp;·&nbsp;
    {{ url('/') }}
  </div>

</body>
</html>
