@extends('layouts.app')

<!-- @section('title', 'Gestión de Usuarios') -->

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
            <p class="text-gray-600">Administre los usuarios del sistema</p>
        </div>
        <a href="{{ route('admin.usuarios.crear') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
           <img src="{{ asset('icons/usuario_agregar.png') }}" alt="Crear" class="w-10 h-10 mr-2">
            Crear Usuario
        </a>
    </div>

    <div class="bg-white rounded-xl shadow border">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Usuarios del Sistema</h3>
            <p class="text-gray-600">Visualice y gestione todos los usuarios</p>
        </div>
        <div class="p-6">
            <!-- Buscador -->
            <form method="GET" class="mb-4">
                <div class="relative rounded-md shadow-sm">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"></path>
                        </svg>
                    </span>
                    <input type="text" name="buscar" placeholder="Buscar por nombre o email..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-green-500 focus:border-green-500 text-sm"
                        value="{{ request('buscar') }}">
                </div>
            </form>
            <!-- Tabla -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Creación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $usuario->correo }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 capitalize">{{ $usuario->rolNombre->rol ?? 'Desconocido' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($usuario->created_at)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs rounded-full font-semibold
                                        {{ $usuario->estado ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $usuario->estado ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.usuarios.editar', $usuario->id_usuario) }}" 
                                           class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded hover:bg-yellow-200" title="Editar">
                                           <img src="{{ asset('icons/usuario_editar.png') }}" alt="Crear" class="w-9 h-9">
                                        </a>
                                        <form action="{{ route('admin.usuarios.toggle', $usuario->id_usuario) }}" method="POST" class="inline toggle-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" 
                                                    class="inline-flex items-center px-2 py-1 {{ $usuario->estado ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} text-xs rounded toggle-button"
                                                    title="{{ $usuario->estado ? 'Desactivar' : 'Activar' }}"
                                                    data-usuario-nombre="{{ $usuario->nombres }} {{ $usuario->apellidos }}"
                                                    data-estado-actual="{{ $usuario->estado ? 'activo' : 'inactivo' }}"
                                                    data-accion="{{ $usuario->estado ? 'desactivar' : 'activar' }}">
                                                @if($usuario->estado)
                                                    <img src="{{ asset('icons/desactivar.png') }}" alt="paciente" class="w-8 h-8 inline-block">
                                                @else
                                                    <img src="{{ asset('icons/activar.png') }}" alt="paciente" class="w-8 h-8 inline-block">
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No hay usuarios registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación y texto de registros -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4">
                <div class="text-sm text-gray-500">
                    Mostrando {{ count($usuarios) }} {{ Str::plural('registro', count($usuarios)) }}
                </div>
                <div>
                    <!-- Aquí iría la paginación si usas Laravel paginator -->
                    {{-- {{ $usuarios->links() }} --}}
                    <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span>&lt;</span>
                        </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span>&gt;</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clicks en botones de toggle
    document.querySelectorAll('.toggle-button').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.toggle-form');
            const nombreUsuario = this.dataset.usuarioNombre;
            const estadoActual = this.dataset.estadoActual;
            const accion = this.dataset.accion;
            
            // Determinar el texto y colores según la acción
            const textoAccion = accion === 'activar' ? 'activar' : 'desactivar';
            const colorBoton = accion === 'activar' ? '#10b981' : '#ef4444';
            const iconoAccion = accion === 'activar' ? 'question' : 'warning';
            
            Swal.fire({
                title: `¿${textoAccion.charAt(0).toUpperCase() + textoAccion.slice(1)} usuario?`,
                html: `¿Estás seguro que deseas <strong>${textoAccion}</strong> al usuario:<br><strong>"${nombreUsuario}"</strong>?`,
                icon: iconoAccion,
                showCancelButton: true,
                confirmButtonColor: colorBoton,
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Sí, ${textoAccion}`,
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading mientras se procesa
                    Swal.fire({
                        title: 'Procesando...',
                        text: `${textoAccion.charAt(0).toUpperCase() + textoAccion.slice(1)}ando usuario`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Enviar el formulario
                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection