@extends('layouts.literario')

@section('title', 'Editar Perfil - Memorias sin orden')

@section('content')
<div class="container">
  <div class="max-w-2xl mx-auto mt-16">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">
          Editar Perfil
        </h1>
        <p class="text-stone-600">Actualiza tu información personal</p>
      </div>

      @if (session('status') === 'profile-updated')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
          <div class="flex items-center">
            <i class="bi bi-check-circle text-green-600 mr-3"></i>
            <p class="text-green-800 font-sans">Perfil actualizado correctamente.</p>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('put')

        <!-- Name -->
        <div class="mb-6">
          <label for="name" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Nombre Completo
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-person text-stone-400"></i>
            </div>
            <input id="name" 
                   type="text" 
                   class="w-full pl-10 pr-4 py-3 text-base border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                   name="name" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autocomplete="name"
                   placeholder="José Martín Artacho">
          </div>
          
          @error('name')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Email -->
        <div class="mb-6">
          <label for="email" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Correo Electrónico
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-envelope text-stone-400"></i>
            </div>
            <input id="email" 
                   type="email" 
                   class="w-full pl-10 pr-4 py-3 text-base border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                   name="email" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="email"
                   placeholder="tu@email.com">
          </div>
          
          @error('email')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Role Information -->
        <div class="mb-6">
          <label class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Rol de Usuario
          </label>
          <div class="flex items-center p-4 bg-stone-50 rounded-md border border-stone-200">
            <i class="bi bi-shield-check text-stone-600 mr-3"></i>
            <span class="text-stone-900 font-sans font-medium capitalize">
              {{ $user->role ?? 'Lector' }}
            </span>
          </div>
          <p class="mt-2 text-sm text-stone-500 font-sans">
            El rol de usuario es asignado por el administrador y no puede ser modificado.
          </p>
        </div>

        <!-- Member Since -->
        <div class="mb-6">
          <label class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Miembro Desde
          </label>
          <div class="flex items-center p-4 bg-stone-50 rounded-md border border-stone-200">
            <i class="bi bi-calendar text-stone-600 mr-3"></i>
            <span class="text-stone-900 font-sans">
              {{ $user->created_at->format('d \d\e F \d\e Y') }}
            </span>
          </div>
        </div>

        <!-- Password Change Section -->
        <div class="mb-6 p-4 bg-stone-50 rounded-md border border-stone-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <i class="bi bi-shield-lock text-stone-600 mr-3"></i>
              <span class="text-stone-900 font-sans font-medium">Seguridad de la cuenta</span>
            </div>
            <a href="{{ route('profile.password.edit') }}" 
               class="text-sm text-stone-700 hover:text-stone-900 font-sans font-medium transition-colors">
              <i class="bi bi-key mr-2"></i>
              Cambiar Contraseña
            </a>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
          <a href="{{ route('dashboard') }}" 
             class="text-stone-600 hover:text-stone-900 font-sans transition-colors">
            <i class="bi bi-arrow-left mr-2"></i>
            Volver al Dashboard
          </a>
          
          <button type="submit" 
                  class="flex items-center px-6 py-3 bg-stone-900 text-stone-100 font-sans text-sm font-medium tracking-wide uppercase hover:bg-stone-800 transition-colors rounded-md">
            <i class="bi bi-check-circle mr-2"></i>
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>


  <!-- Account Deletion Section -->
@if(Auth::user()->role !== 'admin')
<div class="max-w-2xl mx-auto mt-8">
  <div class="bg-red-50 border border-red-200 rounded-lg p-6">
    <div class="flex items-center mb-4">
      <span class="material-icons text-red-600 mr-3">warning</span>
      <h3 class="text-lg font-serif font-bold text-red-900">
        Zona de Peligro - Eliminar Cuenta
      </h3>
    </div>
    
    <p class="text-red-800 mb-6 font-sans">
      Esta acción es <strong>permanente e irreversible</strong>. Si eliminas tu cuenta, perderás acceso a:
    </p>
    
    @if(Auth::user()->role === 'editor')
      <ul class="list-disc list-inside text-red-700 space-y-2 ml-6 mb-6">
        <li><strong>Todas tus narraciones</strong> y todo su contenido</li>
        <li><strong>Todos tus seguidores</strong> y las personas que sigues</li>
        <li><strong>Tu perfil</strong> y toda tu información personal</li>
        <li><strong>Tu historial completo</strong> de feedback y comentarios</li>
        <li><strong>Tu actividad</strong> y estadísticas en la plataforma</li>
        <li class="font-semibold">Como editor, se eliminarán todas tus creaciones literarias</li>
      </ul>
    @elseif(Auth::user()->role === 'lector')
      <ul class="list-disc list-inside text-red-700 space-y-2 ml-6 mb-6">
        <li><strong>Tus seguidores</strong> (personas que te siguen)</li>
        <li><strong>Las personas que sigues</strong> (autores que sigues)</li>
        <li><strong>Tu perfil</strong> y toda tu información personal</li>
        <li><strong>Todos tus comentarios</strong> y feedback dejados</li>
        <li><strong>Tu actividad</strong> y estadísticas en la plataforma</li>
        <li class="font-semibold">Como lector, se eliminarán tus interacciones pero no las narraciones</li>
      </ul>
    @else
      <ul class="list-disc list-inside text-red-700 space-y-2 ml-6 mb-6">
        <li><strong>Tu perfil</strong> y toda tu información personal</li>
        <li><strong>Tus seguidores</strong> y las personas que sigues</li>
        <li><strong>Tu actividad</strong> y estadísticas en la plataforma</li>
      </ul>
    @endif
    
    <div class="text-center">
      <a href="{{ route('account.delete') }}" 
         class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-sans font-medium rounded-lg hover:bg-red-700 transition-colors">
        <span class="material-icons mr-2">delete_forever</span>
        Eliminar Mi Cuenta Permanentemente
      </a>
    </div>
  </div>
</div>
@endif
</div>
@endsection
