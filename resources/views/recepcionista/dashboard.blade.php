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
                    <img src="{{ asset('icons/citas.png') }}" alt="Ícono de citas" class="w-12 h-12">
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
                    <img src="{{ asset('icons/horario.png') }}" alt="Ícono de citas" class="w-12 h-12">
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
                    <img src="{{ asset('icons/paciente.png') }}" alt="Ícono de pacientes" class="w-12 h-12">
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
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Citas de Hoy</h3>
            <p class="text-gray-600 text-sm mb-4">Próximas citas agendadas para hoy</p>
            
            <div class="space-y-4">
                @forelse($citasHoy as $cita)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 last:border-0 last:pb-0 rounded-lg p-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-medium">Dr.</span> {{ $cita['doctor']['name'] }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-700">{{ $cita['hora'] }}</span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 border border-green-300 shadow-sm">
                                Agendada
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-3">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 font-semibold text-lg mb-2">No hay citas agendadas para hoy</p>
                        <p class="text-gray-500 text-sm mb-4">Las próximas citas aparecerán aquí cuando se programen</p>
                        <a href="{{ route('recepcionista.citas.agendar') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agendar nueva cita
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Accesos Rápidos</h3>
            <p class="text-gray-600 text-sm mb-4">Funciones frecuentes</p>
            
            <div class="space-y-2">
                <a href="{{ route('recepcionista.pacientes.registrar') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <img src="{{ asset('icons/paciente_agregar.png') }}" alt="Ícono de registrar nuevo paciente" class="w-8 h-8 inline-block mr-2">
                    Registrar nuevo paciente
                </a>
                <a href="{{ route('recepcionista.citas.agendar') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <img src="{{ asset('icons/cita_me_agregar.png') }}" alt="Ícono de agendar nueva cita" class="w-8 h-8 inline-block mr-2">
                    Agendar nueva cita
                </a>
                <a href="{{ route('recepcionista.pacientes.buscar') }}" 
                   class="flex items-center w-full px-4 py-2 text-left bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                    <img src="{{ asset('icons/paciente_buscar.png') }}" alt="Ícono de búsqueda de paciente" class="w-8 h-8 inline-block mr-2">
                    Buscar paciente
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
