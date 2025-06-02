@extends('layouts.app')

<!-- @section('title', 'Detalle de Cita') -->

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('doctor.agenda') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle de Cita</h1>
            <p class="text-gray-600">Información de la cita con {{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Información de la Cita</h3>
                    <p class="text-gray-600">Detalles de la cita médica</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Fecha</p>
                            <div class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p>{{ \Carbon\Carbon::parse($cita['fecha'])->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Hora</p>
                            <div class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>{{ $cita['hora'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Motivo de Consulta</p>
                        <p class="mt-1">{{ $cita['motivo'] }}</p>
                    </div>
                    <div>
                        <span class="px-2 py-1 text-sm rounded-full {{ $cita['estado'] === 'agendada' ? 'bg-green-100 text-green-800' : ($cita['estado'] === 'completada' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($cita['estado']) }}
                        </span>
                    </div>
                </div>
            </div>

            @if($cita['estado'] === 'agendada')
                <div class="mt-6 bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold">Registrar Diagnóstico</h3>
                        <p class="text-gray-600">Ingrese el diagnóstico para {{ $cita['paciente']['nombre'] }}</p>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('doctor.citas.diagnostico', $cita['id']) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="diagnostico" class="block text-sm font-medium text-gray-700">Diagnóstico</label>
                                <textarea id="diagnostico" name="diagnostico" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                          placeholder="Ingrese el diagnóstico" required></textarea>
                            </div>
                            <div>
                                <label for="indicaciones" class="block text-sm font-medium text-gray-700">Indicaciones</label>
                                <textarea id="indicaciones" name="indicaciones" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                          placeholder="Ingrese las indicaciones para el paciente"></textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Guardar Diagnóstico
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Información del Paciente</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</h4>
                            <p class="text-sm text-gray-500">{{ \App\Services\DataService::getEdadPaciente($cita['paciente']['fecha_nacimiento']) }} años</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium">DNI:</span> {{ $cita['paciente']['dni'] }}</p>
                        <p><span class="font-medium">Teléfono:</span> {{ $cita['paciente']['telefono'] }}</p>
                        <p><span class="font-medium">Email:</span> {{ $cita['paciente']['email'] ?? 'No disponible' }}</p>
                        @if($cita['paciente']['alergias'])
                            <p><span class="font-medium">Alergias:</span> {{ $cita['paciente']['alergias'] }}</p>
                        @endif
                    </div>

                    <a href="{{ route('doctor.historial.paciente', $cita['paciente']['id']) }}" 
                       class="inline-flex items-center w-full justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Ver Historial Completo
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Historial Reciente</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($historial as $item)
                            <div class="border-b pb-4 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <p class="font-medium">{{ $item['diagnostico'] }}</p>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">{{ \Carbon\Carbon::parse($item['fecha_consulta'])->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">No hay historial disponible</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
