@extends('layouts.literario')

@section('title', 'Gestión de Seguidores - Admin')

@section('content')
<div class="container">
  <div class="max-w-6xl mx-auto mt-8">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200">
      <!-- Header -->
      <div class="p-6 border-b border-stone-200">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-serif font-bold text-stone-900">
              <span class="material-icons text-stone-600 mr-2">people</span>
              Gestión de Seguidores
            </h1>
            <p class="text-stone-600 mt-1">Monitorea todas las relaciones de seguimiento en la plataforma</p>
          </div>
          
          <!-- Stats -->
          <div class="flex space-x-6">
            <div class="text-center">
              <div class="text-2xl font-serif font-bold text-stone-900">{{ \App\Models\Follow::count() }}</div>
              <div class="text-sm text-stone-600">Total Seguidores</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-serif font-bold text-purple-600">{{ \App\Models\User::count() }}</div>
              <div class="text-sm text-stone-600">Usuarios Totales</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Fecha
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Seguidor
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Seguido
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Estado
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-200">
            @forelse ($followers as $follow)
              <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-900">
                  {{ $follow->followed_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="material-icons text-purple-600 text-sm">person</span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-stone-900">{{ $follow->follower->name }}</div>
                      <div class="text-xs text-stone-500">{{ $follow->follower->email }}</div>
                      <div class="text-xs text-purple-600">{{ $follow->follower->role }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="material-icons text-blue-600 text-sm">person</span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-stone-900">{{ $follow->followed->name }}</div>
                      <div class="text-xs text-stone-500">{{ $follow->followed->email }}</div>
                      <div class="text-xs text-blue-600">{{ $follow->followed->role }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if($follow->approved)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      <span class="material-icons text-xs mr-1">check_circle</span>
                      Aprobado
                    </span>
                  @else
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      <span class="material-icons text-xs mr-1">pending</span>
                      Pendiente
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <div class="flex space-x-2">
                    @if(!$follow->approved)
                      <form method="POST" action="{{ route('admin.followers.approve', $follow->id) }}">
                        @csrf
                        <button type="submit" class="text-green-600 hover:text-green-900" title="Aprobar">
                          <span class="material-icons text-sm">check</span>
                        </button>
                      </form>
                      <form method="POST" action="{{ route('admin.followers.reject', $follow->id) }}" 
                            onsubmit="return confirm('¿Estás seguro de rechazar esta solicitud?')">
                        @csrf
                        <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Rechazar">
                          <span class="material-icons text-sm">close</span>
                        </button>
                      </form>
                    @endif
                    <form method="POST" action="{{ route('admin.followers.destroy', $follow->id) }}" 
                          onsubmit="return confirm('¿Estás seguro de eliminar esta relación de seguimiento?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                        <span class="material-icons text-sm">delete</span>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-stone-500">
                  <span class="material-icons text-4xl text-stone-300 mb-2">people_outline</span>
                  <p class="text-lg font-medium">No hay relaciones de seguimiento</p>
                  <p class="text-sm text-stone-400">Los usuarios aún no han comenzado a seguirse entre sí</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if ($followers->hasPages())
        <div class="px-6 py-4 border-t border-stone-200">
          {{ $followers->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

<script>
function confirmDelete(message) {
  return confirm(message);
}
</script>
@endsection
