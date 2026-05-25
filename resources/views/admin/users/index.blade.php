@extends('layouts.literario')

@section('title', 'Usuarios · Admin · Memorias sin orden')

@section('content')
<div class="container">

  <div style="max-width:900px; margin:0 auto;">

    {{-- Cabecera --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
      <div>
        <div class="section-label">Administración</div>
        <h1 style="font-family:var(--serif); font-size:2rem; font-weight:700; color:var(--ink);">Gestión de usuarios</h1>
      </div>
      <a href="{{ route('dashboard') }}" class="read-more">← Dashboard</a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
      <div style="background:#f0fdf4; border:1px solid #86efac; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem; color:#166534;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div style="background:#fef2f2; border:1px solid #fca5a5; padding:0.9rem 1.25rem; margin-bottom:1.5rem; font-family:var(--sans); font-size:0.8rem; color:#991b1b;">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
      </div>
    @endif

    {{-- Tabla de usuarios --}}
    <div style="background:#fff; border:1px solid var(--parchment-dark);">

      {{-- Cabecera tabla --}}
      <div style="display:grid; grid-template-columns:1fr 1fr 120px 120px; gap:0; border-bottom:2px solid var(--parchment-dark); padding:0.75rem 1.5rem; background:var(--parchment-mid);">
        <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">Nombre</div>
        <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">Email</div>
        <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">Rol actual</div>
        <div style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.3em; text-transform:uppercase; color:var(--ink-muted);">Cambiar rol</div>
      </div>

      @foreach($users as $user)
        @php
          $rolColor = match($user->role) {
            'admin'  => 'var(--accent)',
            'editor' => 'var(--gold)',
            default  => 'var(--ink-muted)',
          };
          $esMismoUsuario = $user->id === auth()->id();
        @endphp
        <div style="display:grid; grid-template-columns:1fr 1fr 120px 120px; gap:0; padding:1rem 1.5rem; border-bottom:1px solid var(--parchment-dark); align-items:center; {{ $esMismoUsuario ? 'background:var(--parchment);' : '' }}">

          {{-- Nombre --}}
          <div>
            <div style="font-family:var(--serif); font-weight:700; color:var(--ink);">
              {{ $user->name }}
              @if($esMismoUsuario)
                <span style="font-family:var(--sans); font-size:0.55rem; letter-spacing:0.2em; color:var(--ink-muted); margin-left:0.5rem;">(tú)</span>
              @endif
            </div>
            <div style="font-family:var(--sans); font-size:0.65rem; color:var(--ink-muted);">
              Desde {{ $user->created_at->format('d/m/Y') }}
            </div>
          </div>

          {{-- Email --}}
          <div style="font-family:var(--sans); font-size:0.8rem; color:var(--ink-soft);">
            {{ $user->email }}
          </div>

          {{-- Rol actual --}}
          <div>
            <span style="font-family:var(--sans); font-size:0.6rem; letter-spacing:0.2em; text-transform:uppercase; color:{{ $rolColor }}; border:1px solid {{ $rolColor }}; padding:0.15rem 0.5rem;">
              {{ $user->role }}
            </span>
          </div>

          {{-- Selector de rol --}}
          <div>
            @if(!$esMismoUsuario)
              <form action="{{ route('admin.users.role', $user) }}" method="POST" style="display:flex; gap:0.5rem; align-items:center;">
                @csrf
                @method('PATCH')
                <select name="role"
                  style="font-family:var(--sans); font-size:0.65rem; border:1px solid var(--parchment-dark); background:var(--parchment); padding:0.3rem 0.5rem; color:var(--ink); cursor:pointer;"
                  onchange="this.form.submit()">
                  <option value="lector"  {{ $user->role === 'lector'  ? 'selected' : '' }}>Lector</option>
                  <option value="editor"  {{ $user->role === 'editor'  ? 'selected' : '' }}>Editor</option>
                  <option value="admin"   {{ $user->role === 'admin'   ? 'selected' : '' }}>Admin</option>
                </select>
              </form>
            @else
              <span style="font-family:var(--sans); font-size:0.62rem; color:var(--ink-muted); opacity:0.5;">—</span>
            @endif
          </div>

        </div>
      @endforeach

    </div>

    {{-- Resumen --}}
    <div style="margin-top:1rem; display:flex; gap:2rem; flex-wrap:wrap;">
      @foreach(['admin' => 'var(--accent)', 'editor' => 'var(--gold)', 'lector' => 'var(--ink-muted)'] as $rol => $color)
        @php $count = $users->where('role', $rol)->count(); @endphp
        <span style="font-family:var(--sans); font-size:0.65rem; letter-spacing:0.15em; text-transform:uppercase; color:{{ $color }};">
          {{ $count }} {{ $rol }}{{ $count !== 1 ? ($rol === 'lector' ? 'es' : 'es') : '' }}
        </span>
      @endforeach
    </div>

  </div>
</div>
@endsection
