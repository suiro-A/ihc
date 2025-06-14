@extends('layouts.app')

<!-- @section('title', 'Historial de ' . $paciente['nombre']) -->

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('doctor.historial.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Historial de {{ $paciente['nombre'] }} {{ $paciente['apellidos'] }}</h1>
            <p class="text-gray-600">DNI: {{ $paciente['dni'] }} | Edad: {{ \App\Services\DataService::getEdadPaciente($paciente['fecha_nacimiento']) }} años</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Historial Clínico</h3>
            <p class="text-gray-600">Consultas y diagnósticos del paciente</p>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                @forelse($historial as $consulta)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-lg font-medium text-gray-900">{{ $consulta['diagnostico'] }}</h4>
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm rounded">{{ \Carbon\Carbon::parse($consulta['fecha_consulta'])->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="font-medium mb-1">Indicaciones:</h5>
                            <p class="text-sm text-gray-700">{{ $consulta['indicaciones'] ?? 'Sin indicaciones específicas' }}</p>
                        </div>

                        @if($consulta['receta_medica'])
                            <div>
                                <h5 class="font-medium mb-2">Receta médica:</h5>
                                <div class="space-y-2">
                                    @foreach($consulta['receta_medica'] as $medicamento)
                                        <div class="p-3 border rounded-md bg-gray-50">
                                            <p class="font-medium">{{ $medicamento['nombre'] }}</p>
                                            <div class="grid grid-cols-3 gap-2 mt-1 text-sm text-gray-600">
                                                <p>Dosis: {{ $medicamento['dosis'] }}</p>
                                                <p>Frecuencia: {{ $medicamento['frecuencia'] }}</p>
                                                <p>Duración: {{ $medicamento['duracion'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">No hay historial clínico disponible para este paciente</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
