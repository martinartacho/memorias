@extends('layouts.literario')

@section('title', 'Cancelar suscripción · Memorias sin orden')

@section('content')
<main class="container">
  <div style="max-width:480px; margin:0 auto; padding:3rem 0; text-align:center;">

    <div style="font-size:3rem; margin-bottom:1.5rem; opacity:0.5;">🔕</div>

    <div class="section-label" style="justify-content:center;">Cancelar suscripción</div>

    <h2 style="font-family:var(--serif); font-size:1.8rem; font-weight:700; color:var(--ink); margin-bottom:1rem;">
      ¿Confirmar baja?
    </h2>

    <p style="font-size:1rem; line-height:1.8; color:var(--ink-soft); margin-bottom:2rem;">
      El correo <span style="color:var(--accent); font-family:var(--sans);">{{ $subscriber->email }}</span>
      dejará de tener acceso al contenido exclusivo.
    </p>

    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
      <form action="{{ route('suscripcion.baja.confirmar', $subscriber->token) }}" method="POST">
        @csrf
        @method('DELETE')
        <button
          type="submit"
          style="background:var(--accent); color:var(--parchment); border:none; padding:0.8rem 1.75rem; font-family:var(--sans); font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer; transition:background 0.2s;"
          onmouseover="this.style.background='var(--ink)'"
          onmouseout="this.style.background='var(--accent)'"
        >
          Sí, darme de baja
        </button>
      </form>

      <a
        href="{{ route('narraciones.index') }}"
        style="display:inline-flex; align-items:center; padding:0.8rem 1.75rem; border:1px solid var(--parchment-dark); font-family:var(--sans); font-size:0.7rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink-muted); text-decoration:none; transition:border-color 0.2s;"
      >
        Cancelar
      </a>
    </div>

  </div>
</main>
@endsection
