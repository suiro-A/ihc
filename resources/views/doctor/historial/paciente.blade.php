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
            <p class="text-gray-600">DNI: {{ $paciente['dni'] }} | Edad: {{ \Carbon\Carbon::parse($paciente['fecha_nacimiento'])->age }} años</p>
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
                    <div class="border rounded-lg bg-white shadow-sm">
                        <!-- Encabezado de la consulta -->
                        <div class="border-b bg-gray-50 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Consulta del {{ \Carbon\Carbon::parse($consulta['fecha_consulta'])->format('d/m/Y') }}</h4>
                                    <p class="text-sm text-gray-600">Dr. {{ $consulta['doctor_nombre'] ?? 'No especificado' }}</p>
                                    <p class="text-xs text-gray-500">{{ $consulta['especialidad'] ?? 'Especialidad no especificada' }}</p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">Atendida</span>
                            </div>
                        </div>

                        <!-- Contenido de la consulta en pestañas -->
                        <div class="p-6">
                            <!-- Navegación de pestañas -->
                            <div class="border-b border-gray-200 mb-6">
                                <nav class="flex space-x-8" aria-label="Tabs">
                                    <button class="tab-btn active border-b-2 border-green-500 py-2 px-1 text-sm font-medium text-green-600" data-tab="apuntes-{{ $loop->index }}">
                                        Apuntes
                                    </button>
                                    <button class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="diagnostico-{{ $loop->index }}">
                                        Diagnóstico
                                    </button>
                                    <button class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="examenes-{{ $loop->index }}">
                                        Exámenes
                                    </button>
                                    <button class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="receta-{{ $loop->index }}">
                                        Receta
                                    </button>
                                    <button class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="indicaciones-{{ $loop->index }}">
                                        Indicaciones
                                    </button>
                                </nav>
                            </div>

                            <!-- Contenido de las pestañas -->
                            <!-- Apuntes -->
                            <div id="apuntes-{{ $loop->index }}" class="tab-content">
                                <div class="space-y-4">
                                    <div>
                                        <h5 class="font-medium text-gray-900 mb-2">Síntomas Reportados:</h5>
                                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md">{{ $consulta['sintomas_reportados'] ?? 'No registrado' }}</p>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900 mb-2">Exploración Física:</h5>
                                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md">{{ $consulta['exploracion_fisica'] ?? 'No registrado' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Diagnóstico -->
                            <div id="diagnostico-{{ $loop->index }}" class="tab-content hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <h5 class="font-medium text-blue-900 mb-2">Diagnóstico:</h5>
                                    <p class="text-blue-800">{{ $consulta['diagnostico'] ?? 'No registrado' }}</p>
                                </div>
                            </div>

                            <!-- Exámenes -->
                            <div id="examenes-{{ $loop->index }}" class="tab-content hidden">
                                @if(!empty($consulta['examenes']))
                                    <div class="space-y-3">
                                        @foreach($consulta['examenes'] as $examen)
                                            <div class="border rounded-md p-3 bg-yellow-50">
                                                <h6 class="font-medium text-gray-900">{{ $examen['nombre'] }}</h6>
                                                <p class="text-sm text-gray-600 mt-1">{{ $examen['descripcion'] ?? 'Sin descripción' }}</p>
                                                <span class="inline-block px-2 py-1 text-xs bg-yellow-200 text-yellow-800 rounded mt-2">
                                                    {{ $examen['estado'] ?? 'Pendiente' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">No se solicitaron exámenes</p>
                                @endif
                            </div>

                            <!-- Receta -->
                            <div id="receta-{{ $loop->index }}" class="tab-content hidden">
                                @if(!empty($consulta['receta_medica']) && count($consulta['receta_medica']) > 0)
                                    <!-- Header de la receta -->
                                    <div class="border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-white p-4 mb-6 rounded-r-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="bg-green-500 rounded-full p-2 mr-3">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h5 class="text-lg font-bold text-gray-900">Receta Médica</h5>
                                                    <p class="text-sm text-gray-600">{{ count($consulta['receta_medica']) }} medicamento(s) prescrito(s)</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                    VÁLIDA
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Lista de medicamentos en formato tabla -->
                                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                                        @foreach($consulta['receta_medica'] as $index => $medicamento)
                                            <div class="border-b border-gray-100 last:border-b-0 bg-white hover:bg-gray-50 transition-colors duration-200">
                                                <div class="p-6">
                                                    <!-- Header del medicamento -->
                                                    <div class="flex items-start justify-between mb-4">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                                                <span class="text-blue-600 font-bold text-sm">{{ $index + 1 }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="text-lg font-semibold text-gray-900 mb-1">{{ $medicamento['nombre'] }}</h6>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                    Medicamento
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <span class="text-sm text-gray-500">Prescrito</span>
                                                        </div>
                                                    </div>

                                                    <!-- Información del medicamento en formato horizontal -->
                                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                                        <!-- Dosis -->
                                                        <div class="flex items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                                            <div class="flex-shrink-0">
                                                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div class="ml-3">
                                                                <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Dosis</p>
                                                                <p class="text-sm font-bold text-gray-900">{{ $medicamento['dosis'] }}</p>
                                                            </div>
                                                        </div>

                                                        <!-- Frecuencia -->
                                                        <div class="flex items-center p-3 bg-purple-50 rounded-lg border border-purple-100">
                                                            <div class="flex-shrink-0">
                                                                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div class="ml-3">
                                                                <p class="text-xs font-medium text-purple-600 uppercase tracking-wide">Frecuencia</p>
                                                                <p class="text-sm font-bold text-gray-900">{{ $medicamento['frecuencia'] }}</p>
                                                            </div>
                                                        </div>

                                                        <!-- Duración -->
                                                        <div class="flex items-center p-3 bg-orange-50 rounded-lg border border-orange-100">
                                                            <div class="flex-shrink-0">
                                                                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div class="ml-3">
                                                                <p class="text-xs font-medium text-orange-600 uppercase tracking-wide">Duración</p>
                                                                <p class="text-sm font-bold text-gray-900">{{ $medicamento['duracion'] }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Instrucciones especiales -->
                                                    @if(!empty($medicamento['instrucciones']))
                                                        <div class="mt-4 p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-lg">
                                                            <div class="flex items-start">
                                                                <div class="flex-shrink-0">
                                                                    <div class="w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center">
                                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                                <div class="ml-3">
                                                                    <h6 class="text-sm font-semibold text-amber-800 mb-1">Instrucciones Especiales</h6>
                                                                    <p class="text-sm text-amber-700">{{ $medicamento['instrucciones'] }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Footer informativo -->
                                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Conserve esta receta para futuras consultas. Siga las indicaciones médicas al pie de la letra.</span>
                                        </div>
                                    </div>
                                @else
                                    <!-- Estado vacío mejorado -->
                                    <div class="text-center py-12">
                                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Sin medicamentos prescritos</h3>
                                        <p class="text-gray-500 max-w-sm mx-auto">En esta consulta no se emitió receta médica. El tratamiento pudo haber sido no farmacológico.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Indicaciones -->
                            <div id="indicaciones-{{ $loop->index }}" class="tab-content hidden">
                                <div class="bg-purple-50 border border-purple-200 rounded-md p-4">
                                    <h5 class="font-medium text-purple-900 mb-2">Indicaciones Médicas:</h5>
                                    <p class="text-purple-800">{{ $consulta['indicaciones'] ?? 'Sin indicaciones específicas' }}</p>
                                </div>
                            </div>
                        </div>
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

<script>
// Funcionalidad de pestañas para el historial
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clicks en pestañas
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            const consultaIndex = targetTab.split('-')[1];
            
            // Remover clase active de todas las pestañas de esta consulta
            const consultaTabs = document.querySelectorAll(`[data-tab*="-${consultaIndex}"]`);
            consultaTabs.forEach(tab => {
                tab.classList.remove('active', 'border-green-500', 'text-green-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Agregar clase active a la pestaña clickeada
            this.classList.add('active', 'border-green-500', 'text-green-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            // Ocultar todos los contenidos de pestañas de esta consulta
            const consultaContents = document.querySelectorAll(`[id*="-${consultaIndex}"]`);
            consultaContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Mostrar el contenido de la pestaña activa
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
});
</script>
@endsection
