<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva narración publicada</title>
</head>
<body style="margin:0; padding:0; background:#f5f0e8; font-family:Georgia, serif; color:#1a1a1a;">

  <div style="max-width:560px; margin:2rem auto; background:#ffffff; border:1px solid #d6cfc4;">

    {{-- Cabecera --}}
    <div style="background:#1a1a1a; padding:1.5rem 2rem;">
      <p style="font-family:'Courier New', monospace; font-size:0.65rem; letter-spacing:0.3em; text-transform:uppercase; color:#a09080; margin:0 0 0.4rem;">
        {{ __('site.name') }}
      </p>
      <p style="font-family:'Courier New', monospace; font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; color:#6b6055; margin:0;">
        Nueva publicación
      </p>
    </div>

    {{-- Cuerpo --}}
    <div style="padding:2rem 2rem 1.5rem;">

      <p style="font-family:'Courier New', monospace; font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; color:#7a6f63; margin:0 0 1rem;">
        {{ $narracion->fecha_publicacion->translatedFormat('j \d\e F \d\e Y') }}
      </p>

      <h1 style="font-size:1.5rem; font-weight:400; font-style:italic; color:#1a1a1a; margin:0 0 1.25rem; line-height:1.3;">
        {{ $narracion->titulo }}
      </h1>

      @php
        $extracto = Str::limit(strip_tags($narracion->contenido), 220);
      @endphp
      <p style="font-size:0.95rem; line-height:1.85; color:#3c3830; margin:0 0 1.75rem;">
        {{ $extracto }}…
      </p>

      <a href="{{ route('narraciones.show', $narracion->slug) }}"
         style="display:inline-block; background:#1a1a1a; color:#f5f0e8; font-family:'Courier New', monospace; font-size:0.62rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.75rem 1.75rem; text-decoration:none;">
        Leer narración →
      </a>

    </div>

    {{-- Separador --}}
    <div style="border-top:1px solid #e8e0d4; margin:0 2rem;"></div>

    {{-- Pie --}}
    <div style="padding:1.25rem 2rem; background:#faf8f5;">
      <p style="font-family:'Courier New', monospace; font-size:0.58rem; letter-spacing:0.15em; text-transform:uppercase; color:#a09080; margin:0; line-height:2;">
        {{ __('site.name') }} · <a href="{{ url('/') }}" style="color:#a09080;">{{ parse_url(url('/'), PHP_URL_HOST) }}</a><br>
        Recibes este email porque eres suscriptor/a.<br>
        <a href="{{ url('/suscripcion/baja/') }}" style="color:#a09080; text-decoration:underline;">Cancelar suscripción</a>
      </p>
    </div>

  </div>

</body>
</html>
