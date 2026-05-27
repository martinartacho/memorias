@extends('layouts.literario')

@section('title', 'Suscribirse · ' . __('site.name'))

@section('content')
<main class="container">
  <div style="max-width:560px; margin:0 auto; padding: 3rem 0;">

    {{-- Narración bloqueada --}}
    @if($narracion)
    <div style="background:#fff; border:1px solid var(--parchment-dark); padding:1.75rem 2rem; margin-bottom:2rem;">
      <div class="section-label">Contenido exclusivo</div>
      <div style="font-family:var(--serif); font-size:1.4rem; font-weight:700; color:var(--ink); margin-bottom:0.5rem; line-height:1.3;">
        {{ $narracion->titulo }}
      </div>
      <div style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--ink-muted); margin-bottom:1rem;">
        {{ $narracion->fecha_publicacion->format('F Y') }}
      </div>
      <p style="font-size:0.95rem; line-height:1.75; color:var(--ink-soft);">
        {{ Str::limit(strip_tags($narracion->contenido), 200) }}…
      </p>
    </div>
    @endif

    {{-- Formulario --}}
    <div style="background:#fff; border:1px solid var(--parchment-dark); padding:2.5rem 2.5rem;">

      <div class="section-label">Acceso de suscriptor</div>

      <h2 style="font-family:var(--serif); font-size:1.8rem; font-weight:700; color:var(--ink); margin-bottom:0.75rem; line-height:1.2;">
        Introduce tu correo<br>para leer este texto
      </h2>
      <p style="font-family:var(--body); font-size:0.95rem; line-height:1.7; color:var(--ink-soft); margin-bottom:2rem;">
        Te enviaremos un enlace de acceso directo. Sin contraseña, sin registro.
        Para darte de baja basta un clic en el mismo correo.
      </p>

      @if ($errors->any())
        <div style="background:#fef2f2; border:1px solid #fca5a5; padding:1rem 1.25rem; margin-bottom:1.5rem; font-size:0.9rem; color:#991b1b;">
          {{ $errors->first('email') }}
        </div>
      @endif

      <form action="{{ route('suscripcion.store') }}" method="POST">
        @csrf
        @if($narracion)
          <input type="hidden" name="slug" value="{{ $narracion->slug }}">
        @endif

        <div style="margin-bottom:1.25rem;">
          <label style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); display:block; margin-bottom:0.6rem;">
            Correo electrónico
          </label>
          <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="tu@correo.com"
            required
            autofocus
            style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.75rem 1rem; font-family:var(--body); font-size:1rem; color:var(--ink); outline:none; transition:border-color 0.2s;"
            onfocus="this.style.borderColor='var(--accent)'"
            onblur="this.style.borderColor='var(--parchment-dark)'"
          >
        </div>

        <button
          type="submit"
          style="width:100%; background:var(--accent); color:var(--parchment); border:none; padding:0.9rem 1.5rem; font-family:var(--sans); font-size:0.72rem; letter-spacing:0.25em; text-transform:uppercase; cursor:pointer; transition:background 0.2s;"
          onmouseover="this.style.background='var(--ink)'"
          onmouseout="this.style.background='var(--accent)'"
        >
          Enviarme el enlace de acceso →
        </button>
      </form>

      <p style="margin-top:1.5rem; font-family:var(--sans); font-size:0.62rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--ink-muted); text-align:center;">
        Sin spam · Sin contraseña · Baja inmediata
      </p>
    </div>

    <div style="text-align:center; margin-top:1.5rem;">
      <a href="{{ route('narraciones.index') }}" class="read-more">← Volver a narraciones</a>
    </div>

  </div>
</main>
@endsection
