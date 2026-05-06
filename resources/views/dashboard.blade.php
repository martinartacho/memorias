@extends('layouts.literario')

@section('title', 'Dashboard - Memorias sin orden')

@section('content')
<div class="container">
  <!-- Welcome Section -->
  <div class="max-w-4xl mx-auto mb-12">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">
            Hola, {{ Auth::user()->name }}
          </h1>
          <p class="text-stone-600">{{ Auth::user()->email }}</p>
        </div>
        <div class="text-right"> 
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-stone-100 text-stone-800">
            <i class="bi bi-person-circle mr-2"></i>
            {{ Auth::user()->role ?? 'Lector' }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-6">
        <!-- Stats -->
        <div class="text-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
          <i class="bi bi-book text-4xl text-stone-600 mb-4"></i>
          <div class="text-3xl font-serif font-bold text-stone-900 mb-2">0</div>
          <div class="text-sm text-stone-600 font-sans tracking-wide">NARRACIONES</div>
        </div>
        <div class="text-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
          <i class="bi bi-calendar text-4xl text-stone-600 mb-4"></i>
          <div class="text-3xl font-serif font-bold text-stone-900 mb-2">{{ Auth::user()->created_at->format('M Y') }}</div>
          <div class="text-sm text-stone-600 font-sans tracking-wide">MIEMBRO DESDE</div>
        </div>
        <div class="text-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
          <i class="bi bi-clock text-4xl text-stone-600 mb-4"></i>
          <div class="text-3xl font-serif font-bold text-stone-900 mb-2">Reciente</div>
          <div class="text-sm text-stone-600 font-sans tracking-wide">ÚLTIMA VISITA</div>
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
      
      <div class="grid grid-cols-4 gap-6">
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.narraciones.index') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-gear text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">ADMINISTRAR</span>
        </a>
        @endif
        
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
        <a href="{{ route('admin.narraciones.create') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-plus-circle text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">NUEVA NARRACIÓN</span>
        </a>
        @endif
        
        <a href="{{ route('narraciones.index') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-book text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">LEER NARRACIONES</span>
        </a>
        
        <a href="{{ route('profile.edit') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-person text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">MI PERFIL</span>
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
      
      <div class="text-center py-12">
        <i class="bi bi-clock-history text-5xl text-stone-300 mb-4"></i>
        <p class="text-stone-600 font-sans">No hay actividad reciente</p>
        <p class="text-sm text-stone-500 font-sans mt-2">Tu actividad aparecerá aquí</p>
      </div>
    </div>
  </div>
</div>
@endsection
