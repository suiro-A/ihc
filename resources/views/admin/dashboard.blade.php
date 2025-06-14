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
                <div class="text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Doctores</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['doctores'] }}</p>
                    <p class="text-sm text-gray-500">Activos</p>
                </div>
                <div class="text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
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
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
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
                <div class="text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
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
                            <p class="font-medium">{{ $usuario['name'] }}</p>
                            <p class="text-sm text-gray-500 capitalize">{{ $usuario['role'] }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y') }}</span>
                            <a href="{{ route('admin.usuarios.editar', $usuario['id']) }}" 
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
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Crear nuevo usuario
                </a>
                <a href="{{ route('admin.disponibilidad.index') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Configurar disponibilidad médica
                </a>
                
            </div>
        </div>
    </div>
</div>
@endsection
