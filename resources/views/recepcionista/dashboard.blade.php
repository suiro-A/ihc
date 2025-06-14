@extends('layouts.app')

@section('title', 'Dashboard - Recepcionista')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600">Bienvenida, {{ session('user_data')['name'] }}. Aquí está el resumen del día.</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Citas Hoy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['citas_hoy'] }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['citas_confirmadas'] }} confirmadas</p>
                </div>
                <div class="text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
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
                        @if($stats['proxima_cita'])
                            {{ $stats['proxima_cita']['paciente']['nombre'] }} - {{ $stats['proxima_cita']['doctor']['name'] }}
                        @else
                            Sin citas
                        @endif
                    </p>
                </div>
                <div class="text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pacientes Registrados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pacientes_registrados'] }}</p>
                    <p class="text-sm text-gray-500">Total en sistema</p>
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
                    <p class="text-sm font-medium text-gray-600">Citas Confirmadas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['citas_confirmadas'] }}</p>
                    <p class="text-sm text-gray-500">{{ $stats['citas_hoy'] > 0 ? round(($stats['citas_confirmadas'] / $stats['citas_hoy']) * 100) : 0 }}% del total</p>
                </div>
                <div class="text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Citas de Hoy y Accesos Rápidos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Citas de Hoy</h3>
            <p class="text-gray-600 text-sm mb-4">Citas programadas para hoy</p>
            
            <div class="space-y-4">
                @forelse($citasHoy->take(5) as $cita)
                    <div class="flex items-center justify-between border-b pb-4 last:border-0 last:pb-0">
                        <div>
                            <p class="font-medium">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</p>
                            <p class="text-sm text-gray-500">{{ $cita['doctor']['name'] }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium">{{ $cita['hora'] }}</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $cita['estado'] === 'agendada' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($cita['estado']) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No hay citas programadas para hoy</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Accesos Rápidos</h3>
            <p class="text-gray-600 text-sm mb-4">Funciones frecuentes</p>
            
            <div class="space-y-2">
                <a href="{{ route('recepcionista.pacientes.registrar') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Registrar nuevo paciente
                </a>
                <a href="{{ route('recepcionista.citas.agendar') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Agendar nueva cita
                </a>
                <a href="{{ route('recepcionista.pacientes.buscar') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar paciente
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
