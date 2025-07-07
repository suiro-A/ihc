@extends('layouts.app')

<!-- @section('title', 'Dashboard - Administrativo') -->

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Administrativo</h1>
        <p class="text-gray-600">Bienvenido, {{ session('user_data')['name'] }}. Aquí está el resumen del sistema.</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_usuarios'] }}</p>
                    <p class="text-sm text-gray-500">En el sistema</p>
                </div>
                <img src="{{ asset('icons/usuario.png') }}" alt="Ícono de citas" class="w-16 h-16 inline-block">
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Doctores</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['doctores'] }}</p>
                    <p class="text-sm text-gray-500">Activos</p>
                </div>
                <img src="{{ asset('icons/medico.png') }}" alt="Ícono de citas" class="w-18 h-18 inline-block">
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Roles</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['roles'] }}</p>
                    <p class="text-sm text-gray-500">Configurados</p>
                </div>
                <div class="text-green-600">
                    <img src="{{ asset('icons/roles.png') }}" alt="Ícono de citas" class="w-18 h-18 inline-block">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Horarios Configurados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['horarios_configurados'] }}</p>
                    <p class="text-sm text-gray-500">Próximos días</p>
                </div>
                <img src="{{ asset('icons/definir_horario.png') }}" alt="Ícono de citas" class="w-16 h-16 inline-block">
            </div>
        </div>
    </div>

    <!-- Usuarios Recientes y Accesos Rápidos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Usuarios Recientes</h3>
            <p class="text-gray-600 text-sm mb-4">Últimos usuarios registrados en el sistema</p>
            
            <div class="space-y-4">
                @forelse($usuariosRecientes as $usuario)
                    <div class="flex items-center justify-between border-b pb-4 last:border-0 last:pb-0">
                        <div>
                            <p class="font-medium">{{ $usuario->nombres }} {{ $usuario->apellidos }}</p>
                            <p class="text-sm text-gray-500 capitalize">{{ $usuario->rolNombre->rol ?? 'Rol desconocido' }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y') }}</span>
                            <a href="{{ route('admin.usuarios.editar', $usuario->id_usuario) }}" 
                               class="text-green-600 hover:text-green-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hay usuarios recientes</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Accesos Rápidos</h3>
            <p class="text-gray-600 text-sm mb-4">Funciones administrativas frecuentes</p>
            
            <div class="space-y-2">
                <a href="{{ route('admin.usuarios.crear') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <img src="{{ asset('icons/usuario_agregar.png') }}" alt="Ícono de citas" class="w-8 h-8 mr-4 inline-block">
                    Crear nuevo usuario
                </a>
                <a href="{{ route('admin.disponibilidad.index') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <img src="{{ asset('icons/definir_horario.png') }}" alt="Ícono de citas" class="w-8 h-8 mr-4 inline-block">
                    Configurar disponibilidad médica
                </a>
                
            </div>
        </div>
    </div>
</div>
@endsection
