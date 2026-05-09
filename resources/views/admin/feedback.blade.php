@extends('layouts.literario')

@section('title', 'Gestión de Feedback - Admin')

@section('content')
<div class="container">
  <div class="max-w-6xl mx-auto mt-8">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200">
      <!-- Header -->
      <div class="p-6 border-b border-stone-200">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-serif font-bold text-stone-900">
              <span class="material-icons text-stone-600 mr-2">rate_review</span>
              Gestión de Feedback
            </h1>
            <p class="text-stone-600 mt-1">Revisa y aprueba los comentarios de los usuarios</p>
          </div>
          
          <!-- Stats -->
          <div class="flex space-x-6">
            <div class="text-center">
              <div class="text-2xl font-serif font-bold text-green-600">{{ \App\Models\Feedback::where('aprobado', true)->count() }}</div>
              <div class="text-sm text-stone-600">Aprobados</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-serif font-bold text-yellow-600">{{ \App\Models\Feedback::where('aprobado', false)->count() }}</div>
              <div class="text-sm text-stone-600">Pendientes</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-serif font-bold text-stone-900">{{ \App\Models\Feedback::count() }}</div>
              <div class="text-sm text-stone-600">Total</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="px-6 py-4 border-b border-stone-200 bg-stone-50">
        <div class="flex items-center space-x-4">
          <form method="GET" class="flex items-center space-x-4">
            <label class="text-sm font-medium text-stone-700">Filtrar:</label>
            <select name="status" class="px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              <option value="">Todos</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
              <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobados</option>
              <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazados</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
              <span class="material-icons text-sm">filter_list</span>
            </button>
          </form>
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
                Narración
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Usuario
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">
                Feedback
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
            @forelse ($feedbacks as $feedback)
              <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-900">
                  {{ $feedback->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-6 py-4">
                  @if($feedback->narracion)
                    <div>
                      <div class="text-sm font-medium text-stone-900">{{ $feedback->narracion->titulo }}</div>
                      <div class="text-xs text-stone-500">por {{ $feedback->narracion->user->name }}</div>
                    </div>
                  @else
                    <span class="text-sm text-stone-400">Narración eliminada</span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-stone-900">{{ $feedback->nombre }}</div>
                  <div class="text-xs text-stone-500">{{ $feedback->email }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="max-w-xs">
                    <div class="flex items-center mb-2">
                      <span class="material-icons text-{{ $feedback->tipo_feedback == 'excelente' ? 'green' : ($feedback->tipo_feedback == 'bueno' ? 'blue' : 'yellow') }}-600 mr-2">
                        @switch($feedback->tipo_feedback)
                          @case('excelente')
                            thumb_up
                          @break
                          @case('bueno')
                            thumbs_up_down
                          @break
                          @case('regular')
                            thumb_down
                          @break
                        @endswitch
                      </span>
                      <span class="text-sm font-medium">{{ $feedback->tipo_feedback }}</span>
                    </div>
                    @if($feedback->comentario)
                      <p class="text-xs text-stone-600 line-clamp-2">{{ $feedback->comentario }}</p>
                    @endif
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if($feedback->aprobado)
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
                    @if(!$feedback->aprobado)
                      <form method="POST" action="{{ route('admin.feedback.approve', $feedback->id) }}">
                        @csrf
                        <button type="submit" class="text-green-600 hover:text-green-900" title="Aprobar">
                          <span class="material-icons text-sm">check</span>
                        </button>
                      </form>
                      <form method="POST" action="{{ route('admin.feedback.reject', $feedback->id) }}">
                        @csrf
                        <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Rechazar">
                          <span class="material-icons text-sm">close</span>
                        </button>
                      </form>
                    @endif
                    <form method="POST" action="{{ route('admin.feedback.delete', $feedback->id) }}" 
                          onsubmit="return confirm('¿Estás seguro de eliminar este feedback permanentemente?')">
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
                <td colspan="6" class="px-6 py-12 text-center text-stone-500">
                  <span class="material-icons text-4xl text-stone-300 mb-2">rate_review</span>
                  <p class="text-lg font-medium">No hay feedback</p>
                  <p class="text-sm text-stone-400">Los usuarios aún no han dejado comentarios</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if ($feedbacks->hasPages())
        <div class="px-6 py-4 border-t border-stone-200">
          {{ $feedbacks->links() }}
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
