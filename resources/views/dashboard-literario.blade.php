@extends('layouts.literario')

@section('title', 'Panel - Memorias sin orden')

@section('content')
<main class="container">
  <!-- Welcome Section -->
  <div class="text-center mb-12">
    <h1 class="text-4xl font-serif font-bold text-stone-900 mb-4">
      Bienvenido, {{ Auth::user()->name }}
    </h1>
    <p class="text-stone-600 text-lg">Tu espacio literario personal</p>
  </div>

  <!-- User Info Card -->
  <div class="max-w-4xl mx-auto mb-12">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-serif font-bold text-stone-900 mb-2">
            Información de Usuario
          </h2>
          <p class="text-stone-600">{{ Auth::user()->email }}</p>
        </div>
        <div class="text-right">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-stone-100 text-stone-800">
            <i class="bi bi-person-circle mr-2"></i>
            {{ Auth::user()->role ?? 'Lector' }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stats -->
        <div class="text-center p-6 bg-stone-50 rounded-lg">
          <i class="bi bi-book text-3xl text-stone-600 mb-3"></i>
          <div class="text-2xl font-bold text-stone-900">0</div>
          <div class="text-sm text-stone-600 font-sans">Narraciones</div>
        </div>
        <div class="text-center p-6 bg-stone-50 rounded-lg">
          <i class="bi bi-calendar text-3xl text-stone-600 mb-3"></i>
          <div class="text-2xl font-bold text-stone-900">{{ Auth::user()->created_at->format('M Y') }}</div>
          <div class="text-sm text-stone-600 font-sans">Miembro desde</div>
        </div>
        <div class="text-center p-6 bg-stone-50 rounded-lg">
          <i class="bi bi-clock text-3xl text-stone-600 mb-3"></i>
          <div class="text-2xl font-bold text-stone-900">Reciente</div>
          <div class="text-sm text-stone-600 font-sans">Última visita</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
  <div class="max-w-4xl mx-auto mb-12">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <h2 class="text-2xl font-serif font-bold text-stone-900 mb-6">
        Acciones Rápidas
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.narraciones.index') }}" 
           class="flex flex-col items-center p-6 bg-stone-50 rounded-lg hover:bg-stone-100 transition-colors text-center">
          <i class="bi bi-gear text-3xl text-stone-600 mb-3"></i>
          <span class="text-sm font-medium text-stone-900">Administrar</span>
        </a>
        @endif
        
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
        <a href="{{ route('admin.narraciones.create') }}" 
           class="flex flex-col items-center p-6 bg-stone-50 rounded-lg hover:bg-stone-100 transition-colors text-center">
          <i class="bi bi-plus-circle text-3xl text-stone-600 mb-3"></i>
          <span class="text-sm font-medium text-stone-900">Nueva Narración</span>
        </a>
        @endif
        
        <a href="{{ route('narraciones.index') }}" 
           class="flex flex-col items-center p-6 bg-stone-50 rounded-lg hover:bg-stone-100 transition-colors text-center">
          <i class="bi bi-book text-3xl text-stone-600 mb-3"></i>
          <span class="text-sm font-medium text-stone-900">Leer Narraciones</span>
        </a>
        
        <a href="#" 
           class="flex flex-col items-center p-6 bg-stone-50 rounded-lg hover:bg-stone-100 transition-colors text-center">
          <i class="bi bi-person text-3xl text-stone-600 mb-3"></i>
          <span class="text-sm font-medium text-stone-900">Mi Perfil</span>
        </a>
      </div>
    </div>
  </div>
  @endif

  <!-- Recent Activity -->
  <div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <h2 class="text-2xl font-serif font-bold text-stone-900 mb-6">
        Actividad Reciente
      </h2>
      
      <div class="text-center py-12 text-stone-500">
        <i class="bi bi-clock-history text-4xl mb-4"></i>
        <p class="font-sans">No hay actividad reciente</p>
        <p class="text-sm font-sans mt-2">Tus acciones aparecerán aquí</p>
      </div>
    </div>
  </div>
</main>
@endsection
