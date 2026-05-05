@extends('layouts.literario')

@section('content')
<main class="container">
  <div class="max-w-md mx-auto">
    <div class="text-center mb-8">
      @if(request()->is('login'))
        <h2 class="text-3xl font-serif font-bold text-stone-900 mb-4">{{ __('Iniciar Sesión') }}</h2>
        <p class="text-stone-600">{{ __('Ingresa tus credenciales para gestionar tus narraciones') }}</p>
      @elseif(request()->is('register'))
        <h2 class="text-3xl font-serif font-bold text-stone-900 mb-4">{{ __('Registrarse') }}</h2>
        <p class="text-stone-600">{{ __('Crea tu cuenta para comenzar a publicar') }}</p>
      @elseif(request()->is('forgot-password'))
        <h2 class="text-3xl font-serif font-bold text-stone-900 mb-4">{{ __('Recuperar Contraseña') }}</h2>
        <p class="text-stone-600">{{ __('Recibirás un enlace para restablecer tu contraseña') }}</p>
      @else
        <h2 class="text-3xl font-serif font-bold text-stone-900 mb-4">{{ __('Acceso') }}</h2>
        <p class="text-stone-600">{{ __('Gestiona tu cuenta de Memorias sin orden') }}</p>
      @endif
    </div>

    <div class="bg-white rounded-lg border border-stone-300 overflow-hidden">
      {{ $slot }}
    </div>
  </div>
</main>
@endsection
