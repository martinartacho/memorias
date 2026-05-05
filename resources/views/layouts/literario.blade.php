<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Memorias sin orden - Un espacio para narraciones personales">
    <title>@yield('title', 'Memorias sin orden')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lora:ital@0;1&family=Josefin+Sans:wght@300;400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

      :root {
        --ink: #1a1612;
        --ink-soft: #4a443c;
        --ink-muted: #8a7f72;
        --parchment: #f5f0e8;
        --parchment-mid: #ede6d8;
        --parchment-dark: #d4c9b4;
        --accent: #8b2a2a;
        --accent-soft: #c4604a;
        --gold: #b8933a;
        --serif: 'Playfair Display', Georgia, serif;
        --body: 'Lora', Georgia, serif;
        --sans: 'Josefin Sans', sans-serif;
      }

      body {
        font-family: var(--body);
        background: var(--parchment);
        color: var(--ink);
        min-height: 100vh;
      }

      /* ── HEADER ── */
      header {
        background: var(--ink);
        color: var(--parchment);
        padding: 0;
        position: relative;
        overflow: hidden;
      }
      header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(
          0deg,
          transparent,
          transparent 39px,
          rgba(255,255,255,0.03) 39px,
          rgba(255,255,255,0.03) 40px
        );
        pointer-events: none;
      }
      .header-inner {
        max-width: 900px;
        margin: 0 auto;
        padding: 3rem 2rem 2.5rem;
        position: relative;
      }
      .header-label {
        font-family: var(--sans);
        font-size: 0.65rem;
        letter-spacing: 0.35em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 1rem;
      }
      h1.site-title {
        font-family: var(--serif);
        font-size: clamp(3rem, 8vw, 5.5rem);
        font-weight: 700;
        line-height: 0.95;
        letter-spacing: -0.02em;
        color: var(--parchment);
        margin-bottom: 1rem;
      }
      h1.site-title em {
        font-style: italic;
        color: var(--accent-soft);
      }
      .header-rule {
        width: 60px;
        height: 2px;
        background: var(--gold);
        margin-bottom: 1rem;
      }
      .header-sub {
        font-family: var(--sans);
        font-size: 0.8rem;
        letter-spacing: 0.15em;
        color: var(--ink-muted);
        text-transform: uppercase;
      }

      /* ── NAV ── */
      nav {
        background: var(--accent);
        padding: 0;
      }
      nav ul {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 2rem;
        list-style: none;
        display: flex;
        gap: 0;
      }
      nav a {
        font-family: var(--sans);
        font-size: 0.72rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: rgba(245,240,232,0.75);
        text-decoration: none;
        display: block;
        padding: 0.85rem 1.2rem;
        transition: color 0.2s;
      }
      nav a:hover, nav a.active { color: var(--parchment); }
      nav a.active { border-bottom: 2px solid var(--gold); }

      /* ── MAIN LAYOUT ── */
      .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 3rem 2rem;
      }

      /* ── FEATURED ── */
      .featured {
        display: grid;
        grid-template-columns: 0.75fr 1px 1.75fr;
        gap: 0;
        margin-bottom: 3rem;
        border: 1px solid var(--parchment-dark);
        background: #fff;
      }
      .featured-meta {
        padding: 2.5rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: var(--parchment-mid);
      }
      .featured-divider {
        background: var(--parchment-dark);
      }
      .featured-text {
        padding: 2.5rem 2.5rem;
      }
      .section-label {
        font-family: var(--sans);
        font-size: 0.62rem;
        letter-spacing: 0.4em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }
      .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--accent);
        opacity: 0.3;
      }
      .featured-number {
        font-family: var(--serif);
        font-size: 5rem;
        font-weight: 700;
        color: var(--parchment-dark);
        line-height: 1;
        margin-bottom: auto;
      }
      .featured-category {
        font-family: var(--sans);
        font-size: 0.65rem;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: var(--ink-muted);
      }
      .featured h2 {
        font-family: var(--serif);
        font-size: 1.9rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--ink);
        margin-bottom: 0.5rem;
      }
      .featured h2 em { font-style: italic; color: var(--accent); }
      .featured-author {
        font-family: var(--sans);
        font-size: 0.72rem;
        letter-spacing: 0.15em;
        color: var(--ink-muted);
        text-transform: uppercase;
        margin-bottom: 1.2rem;
      }
      .featured p {
        font-size: 1rem;
        line-height: 1.8;
        color: var(--ink-soft);
        margin-bottom: 1.5rem;
      }
      .featured p.dropcap::first-letter {
        font-family: var(--serif);
        font-size: 3.5rem;
        font-weight: 700;
        float: left;
        line-height: 0.8;
        margin: 0.1em 0.1em 0 0;
        color: var(--accent);
      }
      .read-more {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-family: var(--sans);
        font-size: 0.72rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--accent);
        text-decoration: none;
        border-bottom: 1px solid currentColor;
        padding-bottom: 2px;
        transition: color 0.2s;
      }
      .read-more:hover { color: var(--ink); }

      /* ── GRID ── */
      .grid-label {
        font-family: var(--sans);
        font-size: 0.62rem;
        letter-spacing: 0.4em;
        text-transform: uppercase;
        color: var(--ink-muted);
        border-top: 1px solid var(--parchment-dark);
        border-bottom: 1px solid var(--parchment-dark);
        padding: 0.75rem 0;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .grid-label span { color: var(--accent); }
      .stories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
      }
      .story-card {
        background: #fff;
        border: 1px solid var(--parchment-dark);
        padding: 1.75rem 1.5rem;
        cursor: pointer;
        transition: box-shadow 0.2s, transform 0.2s;
        position: relative;
      }
      .story-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(26,22,18,0.1);
      }
      .story-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 3px; height: 0;
        background: var(--accent);
        transition: height 0.25s;
      }
      .story-card:hover::before { height: 100%; }
      .card-num {
        font-family: var(--serif);
        font-size: 2rem;
        font-weight: 700;
        color: var(--parchment-dark);
        margin-bottom: 0.75rem;
      }
      .card-tag {
        font-family: var(--sans);
        font-size: 0.6rem;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        color: var(--accent-soft);
        margin-bottom: 0.5rem;
      }
      .card-title {
        font-family: var(--serif);
        font-size: 1.15rem;
        font-weight: 700;
        line-height: 1.3;
        color: var(--ink);
        margin-bottom: 0.5rem;
      }
      .card-author {
        font-family: var(--sans);
        font-size: 0.65rem;
        letter-spacing: 0.1em;
        color: var(--ink-muted);
        text-transform: uppercase;
        margin-bottom: 0.75rem;
      }
      .card-excerpt {
        font-size: 0.9rem;
        line-height: 1.7;
        color: var(--ink-soft);
      }

      /* ── MODAL ── */
      .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(26,22,18,0.7);
        z-index: 100;
        padding: 2rem 1rem;
        overflow-y: auto;
        backdrop-filter: blur(2px);
      }
      .modal-overlay.open { display: flex; align-items: flex-start; justify-content: center; }
      .modal {
        background: var(--parchment);
        max-width: 680px;
        width: 100%;
        margin: auto;
        position: relative;
      }
      .modal-header {
        background: var(--ink);
        padding: 2.5rem 3rem 2rem;
        color: var(--parchment);
      }
      .modal-tag {
        font-family: var(--sans);
        font-size: 0.62rem;
        letter-spacing: 0.35em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 0.75rem;
      }
      .modal-title {
        font-family: var(--serif);
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 0.5rem;
      }
      .modal-author {
        font-family: var(--sans);
        font-size: 0.72rem;
        letter-spacing: 0.15em;
        color: var(--ink-muted);
        text-transform: uppercase;
      }
      .modal-body {
        padding: 2.5rem 3rem;
      }
      .modal-body p {
        font-size: 1.05rem;
        line-height: 1.9;
        color: var(--ink-soft);
        margin-bottom: 1.5rem;
      }
      .modal-body p.dropcap::first-letter {
        font-family: var(--serif);
        font-size: 4rem;
        font-weight: 700;
        float: left;
        line-height: 0.8;
        margin: 0.1em 0.1em 0 0;
        color: var(--accent);
      }
      .modal-body p.dropcap { margin-bottom: 1.25rem; }
      .modal-close {
        position: absolute;
        top: 1rem; right: 1rem;
        background: none;
        border: none;
        color: var(--parchment);
        font-family: var(--sans);
        font-size: 0.65rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        cursor: pointer;
        padding: 0.5rem 0.75rem;
        opacity: 0.6;
        transition: opacity 0.2s;
      }
      .modal-close:hover { opacity: 1; }

      /* ── FOOTER ── */
      footer {
        background: var(--ink);
        color: var(--ink-muted);
        padding: 2.5rem 2rem;
        text-align: center;
      }
      footer .footer-title {
        font-family: var(--serif);
        font-size: 1.5rem;
        font-style: italic;
        color: var(--parchment);
        margin-bottom: 0.5rem;
      }
      footer p {
        font-family: var(--sans);
        font-size: 0.65rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
      }

      @media (max-width: 640px) {
        .featured { grid-template-columns: 1fr; }
        .featured-divider { display: none; }
        .featured-meta { padding: 1.5rem; }
        .featured-number { font-size: 3rem; }
        .featured-text { padding: 1.5rem; }
        .featured h2 { font-size: 1.5rem; }
        .modal-header, .modal-body { padding: 1.5rem; }
        .modal-title { font-size: 1.6rem; }
      }
    </style>
</head>
<body>

<header>
  <div class="header-inner">
    <div class="header-label">Edición digital</div>
    <h1 class="site-title">Memorias <em>sin</em> orden</h1>
    <div class="header-rule"></div>
    <div class="header-sub">Palabras que perduran</div>
    <p class="text-gray-600 mt-2"> </p>
  </div>
</header>

<nav>
  <ul>
    <li><a href="{{ route('home') }}" class="{{ request()->is('/') ? 'active' : '' }}">Inicio</a></li>
    <li><a href="{{ route('narraciones.index') }}" class="{{ request()->is('narraciones*') ? 'active' : '' }}">Narraciones</a></li>
    @auth
      <li><a href="{{ route('admin.narraciones.index') }}" class="{{ request()->is('admin*') ? 'active' : '' }}">Administrar</a></li>
      <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</a></li>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    @else
      <li><a href="{{ route('login') }}">Iniciar sesión</a></li>
    @endauth
  </ul>
</nav>

@yield('content')

<footer>
  <div class="footer-title">Memorias sin orden</div>
  <p>Colección literaria digital · Todos los derechos reservados</p>
</footer>

<!-- Flash Messages -->
@if(session('success'))
    <div class="fixed bottom-8 right-8 bg-emerald-600 text-white px-8 py-4 rounded-xl shadow-2xl z-50 transform transition-all duration-500">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed bottom-8 right-8 bg-red-600 text-white px-8 py-4 rounded-xl shadow-2xl z-50 transform transition-all duration-500">
        {{ session('error') }}
    </div>
@endif

</body>
</html>
