@extends('layouts.literario')

@section('title', 'Verificar Email - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-md mx-auto mt-16">
    <div class="text-center mb-8">
      <div class="mb-6">
        <span class="material-icons text-6xl text-purple-600">email</span>
      </div>
      <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">
        Verifica tu Email
      </h1>
      <p class="text-stone-600">Hemos enviado un enlace de verificación a tu correo electrónico</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
      <div class="text-center mb-6">
        <p class="text-stone-700 mb-4">
          Para completar tu registro, por favor verifica tu email haciendo clic en el enlace que te enviamos.
        </p>
        
        @if(session('verification_email'))
          <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-purple-800">
              <strong>Para desarrollo:</strong> El enlace de verificación es:<br>
              <code class="block mt-2 text-xs bg-purple-100 p-2 rounded">
                {{ url('/verify-email/' . session('verification_token')) }}
              </code>
            </p>
          </div>
        @endif
      </div>

      <!-- Resend Form -->
      <form method="POST" action="{{ route('verify.resend') }}">
        @csrf
        <div class="mb-4">
          <label for="email" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            ¿No recibiste el email? Reenviar a:
          </label>
          <input 
            id="email" 
            name="email" 
            type="email" 
            class="w-full px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="tu@email.com"
            value="{{ old('email') }}"
            required
          >
        </div>

        <button type="submit" 
                class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
          <span class="material-icons mr-2">refresh</span>
          Reenviar Email de Verificación
        </button>
      </form>

      <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-stone-600 hover:text-stone-900 font-sans text-sm">
          <span class="material-icons text-sm mr-1">arrow_back</span>
          Volver al Inicio de Sesión
        </a>
      </div>
    </div>
  </div>
</main>
@endsection
