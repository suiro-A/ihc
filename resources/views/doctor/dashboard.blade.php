@extends('layouts.app')

@section('title', 'Dashboard - Doctor')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600">Bienvenido, {{ session('user_data')['name'] }}. Aquí está el resumen de su día.</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Citas Hoy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['citas_hoy'] }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['citas_hoy'] - $stats['pacientes_atendidos'] }} pendientes</p>
                </div>
                <div class="text-green-600">
                    <img src="{{ asset('icons/citas.png') }}" alt="Ver agenda completa" class="w-16 h-16 mr-2">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Próxima Cita</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $stats['proxima_cita'] ? $stats['proxima_cita']['hora'] : 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $stats['proxima_cita'] ? $stats['proxima_cita']['paciente']['nombre'] : 'Sin citas' }}
                    </p>
                </div>
                <div class="text-green-600">
                    <img src="{{ asset('icons/horario.png') }}" alt="Ver agenda completa" class="w-16 h-16 mr-2">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pacientes Atendidos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pacientes_atendidos'] }}</p>
                    <p class="text-sm text-gray-500">Hoy</p>
                </div>
                <div class="text-green-600">
                    <img src="{{ asset('icons/paciente.png') }}" alt="Ver pacientes atendidos" class="w-16 h-16  mr-2">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Recetas Emitidas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['recetas_emitidas'] }}</p>
                    <p class="text-sm text-gray-500">Hoy</p>
                </div>
                <div class="text-green-600">
                    <img src="{{ asset('icons/recetamedica.png') }}" alt="Ver recetas emitidas" class="w-16 h-16 mr-2">
                </div>
            </div>
        </div>
    </div>

    <!-- Próximas Citas y Accesos Rápidos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Próximas Citas</h3>
            <p class="text-gray-600 text-sm mb-4">Citas programadas para hoy</p>
            
            <div class="space-y-4">
                @forelse($proximasCitas as $cita)
                    <div class="flex items-center justify-between border-b pb-4 last:border-0 last:pb-0">
                        <div>
                            <p class="font-medium">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</p>
                            <p class="text-sm text-gray-500">{{ $cita['motivo'] }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium">{{ $cita['hora'] }}</span>
                            <a href="{{ route('doctor.citas.detalle', $cita['id']) }}" 
                               class="text-green-600 hover:text-green-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hay citas programadas</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Accesos Rápidos</h3>
            <p class="text-gray-600 text-sm mb-4">Funciones frecuentes</p>
            
            <div class="space-y-2">
                <a href="{{ route('doctor.agenda') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <img src="{{ asset('icons/citas.png') }}" alt="Ver agenda completa" class="w-8 h-8 mr-2">
                    Ver agenda completa
                </a>
                <a href="{{ route('doctor.historial.index') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <img src="{{ asset('icons/historial.png') }}" alt="Buscar historial de paciente" class="w-8 h-8 mr-2">
                    Buscar historial de paciente
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
