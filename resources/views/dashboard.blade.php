@extends('layouts.literario')

@section('title', 'Dashboard · Memorias sin orden')

@section('content')
<div class="container">

  {{-- ── CABECERA ── --}}
  <div style="max-width:900px; margin:0 auto 2rem;">
    <div style="background:#fff; border:1px solid var(--parchment-dark); padding:2rem 2.5rem; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
      <div>
        <div class="section-label">Mi cuenta</div>
        <h1 style="font-family:var(--serif); font-size:2rem; font-weight:700; color:var(--ink); margin-bottom:0.3rem;">
          Hola, {{ $user->name }}
        </h1>
        <p style="font-family:var(--sans); font-size:0.75rem; letter-spacing:0.1em; color:var(--ink-muted);">{{ $user->email }}</p>
      </div>
      <div style="display:flex; flex-direction:column; align-items:flex-end; gap:0.5rem;">
        @php $rolColor = match($user->role) { 'admin' => 'var(--accent)', 'editor' => 'var(--gold)', default => 'var(--ink-muted)' }; @endphp
        <span style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.25em; text-transform:uppercase; color:{{ $rolColor }}; border:1px solid {{ $rolColor }}; padding:0.2rem 0.65rem;">
          <i class="bi bi-person-circle"></i> {{ $user->role ?? 'lector' }}
        </span>
        <a href="{{ route('profile.edit') }}" class="read-more" style="font-size:0.62rem;">Editar perfil →</a>
      </div>
    </div>
  </div>

  {{-- ── 4 CONTADORES (todos los roles) ── --}}
  <div style="max-width:900px; margin:0 auto 2rem; display:grid; grid-template-columns:repeat(4,1fr); gap:1.25rem;">

    @php
      $contadores = [
        ['valor' => $stats['total'],     'label' => 'Total',     'icono' => 'bi-journals',       'color' => 'var(--ink)'],
        ['valor' => $stats['publicadas'],'label' => 'Publicadas','icono' => 'bi-check-circle',   'color' => '#16a34a',   'borde' => '#16a34a'],
        ['valor' => $stats['promocion'], 'label' => 'Promoción', 'icono' => 'bi-eye',            'color' => 'var(--gold)','borde' => 'var(--gold)'],
        ['valor' => $stats['borradores'],'label' => 'Borradores','icono' => 'bi-pencil-square',  'color' => 'var(--ink-muted)'],
      ];
    @endphp

    @foreach($contadores as $c)
      <div style="background:#fff; border:1px solid var(--parchment-dark); {{ isset($c['borde']) ? 'border-top:3px solid '.$c['borde'].';' : '' }} padding:1.75rem 1.5rem; text-align:center;">
        <i class="bi {{ $c['icono'] }}" style="font-size:1.75rem; color:{{ $c['color'] }};"></i>
        <div style="font-family:var(--serif); font-size:2.25rem; font-weight:700; color:var(--ink); margin:0.5rem 0;">
          {{ $c['valor'] }}
        </div>
        <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">
          {{ $c['label'] }}
        </div>
      </div>
    @endforeach
  </div>

  {{-- ══════════════════════════════════════════════════════════════════════ --}}
  @if($user->isAdmin())
  {{-- ── ADMIN ── --}}

    <div style="max-width:900px; margin:0 auto 2rem;">
      <div style="background:#fff; border:1px solid var(--parchment-dark); padding:2rem 2.5rem;">
        <div class="section-label">Acciones rápidas</div>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px,1fr)); gap:1rem; margin-top:1rem;">

          @php $acciones = [
            ['href' => route('admin.narraciones.create'), 'icono' => 'bi-plus-circle',  'color' => 'var(--accent)',    'label' => 'Nueva narración'],
            ['href' => route('admin.dictar'),             'icono' => 'bi-mic-fill',     'color' => '#dc2626',         'label' => 'Dictar'],
            ['href' => route('admin.users.index'),        'icono' => 'bi-people',       'color' => 'var(--gold)',      'label' => 'Usuarios'],
            ['href' => route('admin.narraciones.index'),  'icono' => 'bi-list-ul',      'color' => 'var(--ink-muted)', 'label' => 'Mis narraciones'],
            ['href' => route('admin.lineas.index'),       'icono' => 'bi-diagram-3',    'color' => 'var(--ink-muted)', 'label' => 'Líneas argumentales'],
            ['href' => route('profile.edit'),             'icono' => 'bi-person',       'color' => 'var(--ink-muted)', 'label' => 'Mi perfil'],
          ]; @endphp

          @foreach($acciones as $a)
            <a href="{{ $a['href'] }}"
               style="display:flex; flex-direction:column; align-items:center; padding:1.75rem 1rem; border:1px solid var(--parchment-dark); text-decoration:none; text-align:center; transition:box-shadow 0.2s;"
               onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
              <i class="bi {{ $a['icono'] }}" style="font-size:2rem; color:{{ $a['color'] }}; margin-bottom:0.75rem;"></i>
              <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink);">{{ $a['label'] }}</span>
            </a>
          @endforeach

        </div>
      </div>
    </div>

  {{-- ══════════════════════════════════════════════════════════════════════ --}}
  @elseif($user->isEditor())
  {{-- ── EDITOR ── --}}

    <div style="max-width:900px; margin:0 auto 2rem;">
      <div style="background:#fff; border:1px solid var(--parchment-dark); padding:2rem 2.5rem;">
        <div class="section-label">Acciones rápidas</div>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px,1fr)); gap:1rem; margin-top:1rem;">

          @php $acciones = [
            ['href' => route('admin.narraciones.create'), 'icono' => 'bi-plus-circle', 'color' => 'var(--accent)',    'label' => 'Nueva narración'],
            ['href' => route('admin.dictar'),             'icono' => 'bi-mic-fill',    'color' => '#dc2626',         'label' => 'Dictar'],
            ['href' => route('admin.narraciones.index'),  'icono' => 'bi-list-ul',     'color' => 'var(--ink-muted)', 'label' => 'Mis narraciones'],
            ['href' => route('admin.lineas.index'),       'icono' => 'bi-diagram-3',   'color' => 'var(--ink-muted)', 'label' => 'Líneas argumentales'],
            ['href' => route('profile.edit'),             'icono' => 'bi-person',      'color' => 'var(--ink-muted)', 'label' => 'Mi perfil'],
          ]; @endphp

          @foreach($acciones as $a)
            <a href="{{ $a['href'] }}"
               style="display:flex; flex-direction:column; align-items:center; padding:1.75rem 1rem; border:1px solid var(--parchment-dark); text-decoration:none; text-align:center; transition:box-shadow 0.2s;"
               onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
              <i class="bi {{ $a['icono'] }}" style="font-size:2rem; color:{{ $a['color'] }}; margin-bottom:0.75rem;"></i>
              <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.2em; text-transform:uppercase; color:var(--ink);">{{ $a['label'] }}</span>
            </a>
          @endforeach

        </div>
      </div>
    </div>

  {{-- ══════════════════════════════════════════════════════════════════════ --}}
  @else
  {{-- ── LECTOR ── --}}

    {{-- Banner suscripción --}}
    <div style="max-width:900px; margin:0 auto 2rem;">
      <div style="background:#fff; border:1px solid var(--parchment-dark); border-left:3px solid {{ $esSubscriptor ? 'var(--accent)' : 'var(--gold)' }}; padding:1.5rem 2rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
          <div style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:{{ $esSubscriptor ? 'var(--accent)' : 'var(--gold)' }}; margin-bottom:0.4rem;">
            <i class="bi bi-envelope-heart{{ $esSubscriptor ? '-fill' : '' }}"></i>
            Suscripción por email
          </div>
          <p style="font-family:var(--body); color:var(--ink-soft); font-size:0.95rem;">
            @if($esSubscriptor)
              Tienes acceso a todo el contenido exclusivo para suscriptores.
            @else
              Suscríbete gratis para acceder a las narraciones exclusivas.
            @endif
          </p>
        </div>
        @if(!$esSubscriptor)
          <a href="{{ route('suscripcion.form') }}"
             style="white-space:nowrap; background:var(--accent); color:var(--parchment); font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; padding:0.65rem 1.4rem; text-decoration:none;"
             onmouseover="this.style.background='var(--ink)'" onmouseout="this.style.background='var(--accent)'">
            Suscribirme gratis →
          </a>
        @else
          <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:var(--accent);">
            <i class="bi bi-check-circle-fill"></i> Activa
          </span>
        @endif
      </div>
    </div>

    {{-- Solicitar rol de editor --}}
    <div style="max-width:900px; margin:0 auto 2rem;">
      <div style="background:#fff; border:1px solid var(--parchment-dark); padding:2rem 2.5rem;">
        <div class="section-label">¿Quieres publicar?</div>
        <h2 style="font-family:var(--serif); font-size:1.4rem; font-weight:700; color:var(--ink); margin-bottom:0.75rem;">
          Solicitar rol de editor
        </h2>
        <p style="font-size:0.95rem; line-height:1.75; color:var(--ink-soft); margin-bottom:1.5rem;">
          Los editores pueden crear y gestionar sus propias narraciones. Envía una solicitud al administrador.
        </p>

        @if(session('editor_solicitado') || session('success'))
          <div style="background:#f0fdf4; border:1px solid #86efac; padding:0.9rem 1.25rem; font-family:var(--sans); font-size:0.8rem; color:#166534;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success', 'Solicitud ya enviada. El administrador te contactará por email.') }}
          </div>
        @else
          <form action="{{ route('dashboard.solicitar-editor') }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
              <label style="font-family:var(--sans); font-size:0.62rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted); display:block; margin-bottom:0.5rem;">
                Motivo (opcional)
              </label>
              <textarea name="motivo" rows="3" placeholder="Me gustaría compartir mis narraciones…"
                style="width:100%; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.75rem 1rem; font-family:var(--body); font-size:0.9rem; color:var(--ink); resize:vertical; outline:none;"
                onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--parchment-dark)'"></textarea>
            </div>
            <button type="submit"
              style="background:var(--ink); color:var(--parchment); border:none; padding:0.75rem 1.75rem; font-family:var(--sans); font-size:0.68rem; letter-spacing:0.2em; text-transform:uppercase; cursor:pointer; transition:background 0.2s;"
              onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--ink)'">
              Enviar solicitud →
            </button>
          </form>
        @endif

      </div>
    </div>

  @endif

</div>
@endsection
