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
                                @if(!empty($consulta['receta_medica']))
                                    <div class="space-y-3">
                                        @foreach($consulta['receta_medica'] as $medicamento)
                                            <div class="border rounded-md p-4 bg-green-50">
                                                <h6 class="font-medium text-green-900">{{ $medicamento['nombre'] }}</h6>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2 text-sm">
                                                    <div>
                                                        <span class="text-gray-600">Dosis:</span>
                                                        <span class="text-gray-900 font-medium">{{ $medicamento['dosis'] }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Frecuencia:</span>
                                                        <span class="text-gray-900 font-medium">{{ $medicamento['frecuencia'] }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Duración:</span>
                                                        <span class="text-gray-900 font-medium">{{ $medicamento['duracion'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">No se prescribieron medicamentos</p>
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
