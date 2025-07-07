@extends('layouts.app')

@section('content')
@php
    $tab = request('tab', 'detalle');
    $estadoCita = $cita['estado'] ?? 'Agendada';
    $esAtendida = $estadoCita === 'Atendida';
    $esAusente = $estadoCita === 'Ausente';
    $esSoloLectura = $esAtendida || $esAusente;
    $puedeEditar = !$esSoloLectura;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Simple -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('doctor.agenda') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            ← Volver
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detalle de Cita</h1>
            <p class="text-gray-600">Información de la cita con {{ $cita['paciente']['nombres'] }} {{ $cita['paciente']['apellidos'] }}</p>
        </div>
    </div>

    @if($esAtendida)
        <!-- Banner Atendida -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Cita Atendida</h3>
                    <p class="text-sm text-blue-700">Esta cita ya fue atendida. Solo puedes visualizar la información.</p>
                </div>
            </div>
        </div>
    @elseif($esAusente)
        <!-- Banner Ausente -->
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Paciente Ausente</h3>
                    <p class="text-sm text-red-700">El paciente no asistió a la cita. No se puede registrar información médica.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Pestañas Simples -->
    <div class="mb-6">
        <div class="flex bg-gray-100 rounded-lg p-1">
            <a href="#" data-tab="detalle" class="tab-link px-4 py-2 rounded-md text-sm bg-white shadow">
                Información
            </a>
            <a href="#" data-tab="apuntes" class="tab-link px-4 py-2 rounded-md text-sm">
                Apuntes
                @if($apuntesActual)
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                @endif
            </a>
            <a href="#" data-tab="diagnostico" class="tab-link px-4 py-2 rounded-md text-sm">
                Diagnóstico
                @if(isset($diagnosticoActual) && $diagnosticoActual)
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                @endif
            </a>
            <a href="#" data-tab="examenes" class="tab-link px-4 py-2 rounded-md text-sm">
                Exámenes
            </a>
            <a href="#" data-tab="receta" class="tab-link px-4 py-2 rounded-md text-sm">
                Receta
                @if($recetaActual && $recetaActual->recetaMedicamentos->count() > 0)
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                @endif
            </a>
            <a href="#" data-tab="indicaciones" class="tab-link px-4 py-2 rounded-md text-sm">
                Indicaciones
                @if(isset($indicacionesActual) && $indicacionesActual)
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                @endif
            </a>
        </div>
    </div>

    <!-- Contenido con Sidebar -->
    <div class="grid gap-6 md:grid-cols-3">
        <!-- Contenido Principal -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Formulario único para toda la consulta -->
                <form id="form-consulta-completa" method="POST" action="{{ route('doctor.citas.finalizar', $cita['id']) }}">
                    @csrf
                    <!-- Sección Detalle -->
                    <div id="seccion-detalle" style="display: block;">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Información de la Cita</h3>
                        <p class="text-gray-500 mb-8">Citas programadas para hoy</p>
                        
                        <div class="space-y-6">
                            <!-- Fecha y Hora -->
                            <div class="flex justify-between items-start max-w-lg">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Fecha</p>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($cita['fecha'])->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Hora</p>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-medium">{{ $cita['hora'] }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Motivo de Consulta -->
                            <div class="max-w-lg">
                                <p class="text-sm text-gray-500 mb-2">Motivo de Consulta</p>
                                <p class="font-medium text-gray-900 mb-3">{{ $cita['motivo'] ?? 'Control mensual de tratamiento para hipertensión' }}</p>
                                
                                <!-- Estado de la Cita -->
                                <div class="inline-block">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                        {{ $cita['estado'] ?? 'Agendada' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Apuntes -->
                    <div id="seccion-apuntes" style="display: none;">
                        <h3 class="text-lg font-bold mb-4">Apuntes de la Consulta</h3>
                        
                        @if($esAusente)
                            <div class="text-center py-8">
                                <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    No se pueden registrar apuntes para una cita ausente
                                </div>
                            </div>
                        @else
                            <div class="space-y-6">
                                <!-- Síntomas Reportados (CRÍTICO) -->
                                <div>
                                    <label class="block text-sm font-medium mb-1 {{ $esAtendida ? 'text-gray-600' : 'text-red-600' }}">
                                        Síntomas Reportados {{ $puedeEditar ? '*' : '' }}
                                    </label>
                                    <textarea id="sintomas_reportados" name="sintomas_reportados" rows="3" 
                                             class="w-full border rounded-md p-2 {{ $esSoloLectura ? 'bg-gray-50 text-gray-700' : '' }}" 
                                             placeholder="{{ $puedeEditar ? '¿Qué síntomas describe el paciente? Ej: Dolor de cabeza intenso desde hace 3 días...' : '' }}"
                                             {{ $esSoloLectura ? 'readonly' : '' }}
                                             >{{ old('sintomas_reportados', $apuntesActual->sintomas_reportados ?? '') }}</textarea>
                                </div>

                                <!-- Exploración Física (OPCIONAL) -->
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-600">Exploración Física</label>
                                    <textarea id="exploracion_fisica" name="exploracion_fisica" rows="3" 
                                             class="w-full border rounded-md p-2 {{ $esSoloLectura ? 'bg-gray-50 text-gray-700' : '' }}" 
                                             placeholder="{{ $puedeEditar ? 'Resultados del examen físico. Ej: Rigidez de nuca presente, pupilas reactivas...' : '' }}"
                                             {{ $esSoloLectura ? 'readonly' : '' }}
                                             >{{ old('exploracion_fisica', $apuntesActual->exploracion_fisica ?? '') }}</textarea>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-6 flex justify-between">
                            <a href="#" data-tab="detalle" class="tab-link px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                ← Anterior
                            </a>
                            <a href="#" data-tab="diagnostico" class="tab-link px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Siguiente →
                            </a>
                        </div>
                    </div>

                    <!-- Sección Diagnóstico -->
                    <div id="seccion-diagnostico" style="display: none;">
                        <h3 class="text-lg font-bold mb-4">Diagnóstico</h3>
                        
                        @if($esAusente)
                            <div class="text-center py-8">
                                <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    No se puede registrar diagnóstico para una cita ausente
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1 {{ $esAtendida ? 'text-gray-600' : 'text-red-600' }}">
                                        Diagnóstico {{ $puedeEditar ? '*' : '' }}
                                    </label>
                                    <input type="text" id="diagnostico" name="diagnostico" 
                                           class="w-full border rounded-md p-2 {{ $esSoloLectura ? 'bg-gray-50 text-gray-700' : '' }}"
                                           placeholder="{{ $puedeEditar ? 'Ej: Hipertensión arterial, Diabetes tipo 2, etc.' : '' }}"
                                           value="{{ old('diagnostico', $diagnosticoActual->descripcion ?? '') }}"
                                           maxlength="255"
                                           {{ $esSoloLectura ? 'readonly' : '' }}>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-6 flex justify-between">
                            <a href="#" data-tab="apuntes" class="tab-link px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                ← Anterior
                            </a>
                            <a href="#" data-tab="examenes" class="tab-link px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Siguiente →
                            </a>
                        </div>
                    </div>

                    <!-- Sección Exámenes -->
                    <div id="seccion-examenes" style="display: none;">
                        <h3 class="text-lg font-bold mb-4">Exámenes Médicos</h3>
                        
                        @if($esAusente)
                            <div class="text-center py-8">
                                <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    No se pueden registrar exámenes para una cita ausente
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-600">Exámenes de Laboratorio</label>
                                    <textarea name="examenes_laboratorio" rows="3" 
                                             class="w-full border rounded-md p-2 {{ $esSoloLectura ? 'bg-gray-50 text-gray-700' : '' }}" 
                                             placeholder="{{ $puedeEditar ? 'Hemograma completo, Glucosa en ayunas, etc.' : '' }}"
                                             {{ $esSoloLectura ? 'readonly' : '' }}></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-600">Exámenes de Imágenes</label>
                                    <textarea name="examenes_imagenes" rows="3" 
                                             class="w-full border rounded-md p-2 {{ $esSoloLectura ? 'bg-gray-50 text-gray-700' : '' }}" 
                                             placeholder="{{ $puedeEditar ? 'Radiografía de tórax, Ecografía abdominal, etc.' : '' }}"
                                             {{ $esSoloLectura ? 'readonly' : '' }}></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-600">Otros Exámenes</label>
                                    <textarea name="otros_examenes" rows="3" 
                                             class="w-full border rounded-md p-2 {{ $esSoloLectura ? 'bg-gray-50 text-gray-700' : '' }}" 
                                             placeholder="{{ $puedeEditar ? 'Electrocardiograma, Espirometría, etc.' : '' }}"
                                             {{ $esSoloLectura ? 'readonly' : '' }}></textarea>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-6 flex justify-between">
                            <a href="#" data-tab="diagnostico" class="tab-link px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                ← Anterior
                            </a>
                            <a href="#" data-tab="receta" class="tab-link px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Siguiente →
                            </a>
                        </div>
                    </div>

                    <!-- Sección Receta -->
                    <div id="seccion-receta" style="display: none;">
                        <h3 class="text-lg font-bold mb-4">Receta Médica</h3>
                        
                        @if($esAusente)
                            <div class="text-center py-8">
                                <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    No se puede recetar medicamentos para una cita ausente
                                </div>
                            </div>
                        @else
                        <div id="medicamentos-container" class="space-y-4">
                            @if($recetaActual && $recetaActual->recetaMedicamentos->count() > 0)
                                @foreach($recetaActual->recetaMedicamentos as $index => $recetaMed)
                                    @if($esAtendida)
                                        <!-- Vista de solo lectura para cita atendida -->
                                        <div class="medicamento-item border-2 border-blue-200 rounded-lg p-4 bg-blue-50">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-medium text-blue-800">
                                                    💊 Medicamento {{ $index + 1 }}
                                                    <span class="text-blue-600 text-sm ml-2">✓ Recetado</span>
                                                </h4>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-gray-600">Medicamento</label>
                                                    <div class="w-full border border-gray-300 rounded-md p-2 bg-white text-gray-800">
                                                        @php
                                                            $medicamentoEncontrado = $medicamentos->firstWhere('id_medicamento', $recetaMed->id_medicamento);
                                                        @endphp
                                                        {{ $medicamentoEncontrado ? $medicamentoEncontrado->nombre . ' - ' . $medicamentoEncontrado->presentacion : 'Medicamento no encontrado' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-gray-600">Dosis</label>
                                                    <div class="w-full border border-gray-300 rounded-md p-2 bg-white text-gray-800">
                                                        {{ $recetaMed->dosis }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-gray-600">Frecuencia</label>
                                                    <div class="w-full border border-gray-300 rounded-md p-2 bg-white text-gray-800">
                                                        @php
                                                            $frecuenciaEncontrada = $frecuencias->firstWhere('id_frecuencia', $recetaMed->id_frecuencia);
                                                        @endphp
                                                        {{ $frecuenciaEncontrada ? $frecuenciaEncontrada->descripcion : 'Frecuencia no encontrada' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-gray-600">Duración</label>
                                                    <div class="w-full border border-gray-300 rounded-md p-2 bg-white text-gray-800">
                                                        {{ $recetaMed->duración == 'Continuo' ? 'Tratamiento continuo' : $recetaMed->duración }}
                                                    </div>
                                                </div>
                                            </div>
                                            @if($recetaMed->instrucciones)
                                                <div class="mt-4">
                                                    <label class="block text-sm font-medium mb-1 text-gray-600">Instrucciones Especiales</label>
                                                    <div class="w-full border border-gray-300 rounded-md p-2 bg-white text-gray-800">
                                                        {{ $recetaMed->instrucciones }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <!-- Vista editable normal -->
                                        <div class="medicamento-item border rounded-lg p-4 bg-gray-50 relative cursor-pointer medicamento-bloqueado" onclick="confirmarDesbloqueoMedicamento(this)" data-medicamento-index="{{ $index }}" data-bloqueado="true">
                                            <!-- Overlay de bloqueo más transparente y solo sobre el contenido -->
                                            <div class="medicamento-overlay absolute top-0 left-0 right-0 bottom-0 bg-green-100 bg-opacity-20 flex items-center justify-center rounded-lg z-10" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: 0.5rem;">
                                                <div class="text-center p-2">
                                                    <div class="bg-green-200 bg-opacity-90 border border-green-400 rounded-lg p-2 shadow-sm">
                                                        <div class="flex items-center justify-center mb-1">
                                                            <svg class="w-4 h-4 text-green-700 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span class="text-green-800 font-medium text-sm">MEDICAMENTO {{ $index + 1 }} GUARDADO</span>
                                                        </div>
                                                        <p class="text-green-700 text-xs mb-2">Haz clic para modificar</p>
                                                        <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs" 
                                                                onclick="event.stopPropagation(); confirmarDesbloqueoMedicamento(this.closest('.medicamento-item'))">
                                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Modificar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-medium text-gray-800">Medicamento {{ $index + 1 }}</h4>
                                                <button type="button" class="btn-remover-medicamento text-red-500 hover:text-red-700 {{ $index == 0 ? 'hidden' : '' }}" onclick="event.stopPropagation(); removerMedicamento(this)">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Campos hidden para garantizar que se envíen los datos protegidos -->
                                                <input type="hidden" name="medicamentos[{{ $index }}][medicamento]" value="{{ $recetaMed->id_medicamento }}">
                                                <input type="hidden" name="medicamentos[{{ $index }}][dosis]" value="{{ $recetaMed->dosis }}">
                                                <input type="hidden" name="medicamentos[{{ $index }}][frecuencia]" value="{{ $recetaMed->id_frecuencia }}">
                                                <input type="hidden" name="medicamentos[{{ $index }}][duracion]" value="{{ $recetaMed->duración }}">
                                                <input type="hidden" name="medicamentos[{{ $index }}][instrucciones]" value="{{ $recetaMed->instrucciones }}">
                                                
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-red-600">Medicamento *</label>
                                                    <select name="medicamentos_visual[{{ $index }}][medicamento]" class="w-full border rounded-md p-2" disabled data-original="{{ $recetaMed->id_medicamento }}">
                                                        <option value="">Seleccione un medicamento</option>
                                                        @foreach($medicamentos as $medicamento)
                                                            <option value="{{ $medicamento->id_medicamento }}" {{ $medicamento->id_medicamento == $recetaMed->id_medicamento ? 'selected' : '' }}>
                                                                {{ $medicamento->nombre }} - {{ $medicamento->presentacion }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-red-600">Dosis *</label>
                                                    <input type="text" name="medicamentos_visual[{{ $index }}][dosis]" class="w-full border rounded-md p-2" 
                                                           placeholder="Ej: 500mg, 1 tableta" value="{{ $recetaMed->dosis }}" disabled>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-red-600">Frecuencia *</label>
                                                    <select name="medicamentos_visual[{{ $index }}][frecuencia]" class="w-full border rounded-md p-2" disabled>
                                                        <option value="">Seleccione frecuencia</option>
                                                        @foreach($frecuencias as $frecuencia)
                                                            <option value="{{ $frecuencia->id_frecuencia }}" {{ $frecuencia->id_frecuencia == $recetaMed->id_frecuencia ? 'selected' : '' }}>
                                                                {{ $frecuencia->descripcion }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium mb-1 text-red-600">Duración *</label>
                                                    <select name="medicamentos_visual[{{ $index }}][duracion]" class="w-full border rounded-md p-2" disabled>
                                                        <option value="">Seleccione duración</option>
                                                        @foreach(['3 días', '5 días', '7 días', '10 días', '14 días', '21 días', '30 días', '60 días', '90 días', 'Continuo'] as $duracion)
                                                            <option value="{{ $duracion }}" {{ $duracion == $recetaMed->duración ? 'selected' : '' }}>
                                                                {{ $duracion == 'Continuo' ? 'Tratamiento continuo' : $duracion }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label class="block text-sm font-medium mb-1">Instrucciones Especiales</label>
                                                <textarea name="medicamentos_visual[{{ $index }}][instrucciones]" rows="2" 
                                                         class="w-full border rounded-md p-2" 
                                                         placeholder="Ej: Tomar con abundante agua, evitar alcohol, etc." disabled>{{ $recetaMed->instrucciones }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                @if($esAtendida)
                                    <!-- No hay medicamentos en cita atendida -->
                                    <div class="text-center py-8">
                                        <div class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            No se recetaron medicamentos en esta consulta
                                        </div>
                                    </div>
                                @else
                                    <!-- Medicamento 1 (inicial) para citas no atendidas -->
                                    <div class="medicamento-item border rounded-lg p-4 bg-gray-50">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium text-gray-800">Medicamento 1</h4>
                                            <button type="button" class="btn-remover-medicamento text-red-500 hover:text-red-700 hidden" onclick="removerMedicamento(this)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-red-600">Medicamento *</label>
                                                <select name="medicamentos[0][medicamento]" class="w-full border rounded-md p-2">
                                                    <option value="">Seleccione un medicamento</option>
                                                    @foreach($medicamentos as $medicamento)
                                                        <option value="{{ $medicamento->id_medicamento }}">
                                                            {{ $medicamento->nombre }} - {{ $medicamento->presentacion }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-red-600">Dosis *</label>
                                                <input type="text" name="medicamentos[0][dosis]" class="w-full border rounded-md p-2" 
                                                       placeholder="Ej: 500mg, 1 tableta">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-red-600">Frecuencia *</label>
                                                <select name="medicamentos[0][frecuencia]" class="w-full border rounded-md p-2">
                                                    <option value="">Seleccione frecuencia</option>
                                                    @foreach($frecuencias as $frecuencia)
                                                        <option value="{{ $frecuencia->id_frecuencia }}">{{ $frecuencia->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-red-600">Duración *</label>
                                                <select name="medicamentos[0][duracion]" class="w-full border rounded-md p-2">
                                                    <option value="">Seleccione duración</option>
                                                    <option value="3 días">3 días</option>
                                                    <option value="5 días">5 días</option>
                                                    <option value="7 días">7 días</option>
                                                    <option value="10 días">10 días</option>
                                                    <option value="14 días">14 días</option>
                                                    <option value="21 días">21 días</option>
                                                    <option value="30 días">30 días</option>
                                                    <option value="60 días">60 días</option>
                                                    <option value="90 días">90 días</option>
                                                    <option value="Continuo">Tratamiento continuo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium mb-1">Instrucciones Especiales</label>
                                            <textarea name="medicamentos[0][instrucciones]" rows="2" 
                                                     class="w-full border rounded-md p-2" 
                                                     placeholder="Ej: Tomar con abundante agua, evitar alcohol, etc."></textarea>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Botón para agregar medicamento -->
                        @if($puedeEditar)
                            <div class="mt-4">
                                <button type="button" id="btn-agregar-medicamento" 
                                        class="flex items-center px-4 py-2 border border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400 hover:text-gray-800">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Agregar otro medicamento
                                </button>
                            </div>
                        @elseif($esAtendida && $recetaActual && $recetaActual->recetaMedicamentos->count() == 0)
                            <div class="text-center py-4">
                                <p class="text-gray-500 italic">No se recetaron medicamentos en esta consulta</p>
                            </div>
                        @endif
                        @endif

                        <div class="mt-6 flex justify-between">
                            <a href="#" data-tab="examenes" class="tab-link px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                ← Anterior
                            </a>
                            <a href="#" data-tab="indicaciones" class="tab-link px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Siguiente →
                            </a>
                        </div>
                    </div>

                    <!-- Sección Indicaciones -->
                    <div id="seccion-indicaciones" style="display: none;">
                        <h3 class="text-lg font-bold mb-4">Indicaciones</h3>
                        
                        @if($esAusente)
                            <div class="text-center py-8">
                                <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    No se pueden registrar indicaciones para una cita ausente
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1 {{ $esAtendida ? 'text-gray-600' : 'text-red-600' }}">
                                        Indicaciones {{ $puedeEditar ? '*' : '' }}
                                    </label>
                                    <textarea name="indicaciones" rows="5" 
                                             class="w-full border rounded-md p-2 {{ $esSoloLectura ? 'bg-gray-50 text-gray-700' : '' }} @error('indicaciones') border-red-500 @enderror"
                                             placeholder="{{ $puedeEditar ? 'Escriba las indicaciones y recomendaciones para el paciente...' : '' }}"
                                             {{ $esSoloLectura ? 'readonly' : '' }}
                                             >{{ old('indicaciones', $indicacionesActual->descripcion ?? '') }}</textarea>
                                    @error('indicaciones')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-6 flex justify-between">
                            <a href="#" data-tab="receta" class="tab-link px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                ← Anterior
                            </a>
                            @if($puedeEditar)
                                <div class="flex gap-2">
                                    <button type="button" id="btn-validar-finalizar" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                                        Finalizar Consulta ✓
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar: Información del Paciente y Historial -->
        <div class="space-y-6">
            <!-- Información del Paciente -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-bold">Información del Paciente</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">{{ $cita['paciente']['nombres'] }} {{ $cita['paciente']['apellidos'] }}</h4>
                            <p class="text-sm text-gray-500">
                                @if(isset($cita['paciente']['fecha_nac']))
                                    @php
                                        $edad = \Carbon\Carbon::parse($cita['paciente']['fecha_nac'])->age;
                                    @endphp
                                    {{ $edad }} años
                                @else
                                    Edad no especificada
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium">DNI:</span> {{ $cita['paciente']['dni'] ?? 'No especificado' }}</p>
                        <p><span class="font-medium">Teléfono:</span> {{ $cita['paciente']['telefono'] ?? 'No especificado' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $cita['paciente']['correo'] ?? 'No especificado' }}</p>
                    </div>
                    @if(isset($cita['paciente']['id']))
                        <a href="{{ route('doctor.historial.paciente', $cita['paciente']['id']) }}"
                           class="inline-flex items-center w-full justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <img src="{{ asset('icons/ver_historial.png') }}" alt="Ver Historial Completo" class="w-8 h-8 mr-2">
                            Ver Historial Completo
                        </a>
                    @endif
                </div>
            </div>

            <!-- Historial Reciente -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-bold">Historial Reciente</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if(isset($cita['historial']) && count($cita['historial']) > 0)
                            @foreach($cita['historial'] as $item)
                                <div class="border-b pb-4 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start">
                                        <p class="font-medium">{{ $item['diagnostico'] }}</p>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                                            {{ $item['fecha'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 text-center">No hay historial disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
.border-3 {
    border-width: 3px;
}

.medicamento-item.border-green-400 {
    border-color: #4ade80 !important;
    box-shadow: 0 0 0 1px rgba(74, 222, 128, 0.1);
}

.medicamento-item.border-yellow-400 {
    border-color: #fbbf24 !important;
    box-shadow: 0 0 0 1px rgba(251, 191, 36, 0.1);
}

.medicamento-item.border-gray-300 {
    border-color: #d1d5db !important;
}

/* Hacer más visibles los bordes de los campos individuales */
.border-red-500 {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.1);
}

.border-green-500 {
    border-color: #22c55e !important;
    box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.1);
}

/* Overlay transparente que permite ver el contenido */
.medicamento-overlay {
    backdrop-filter: blur(1px);
    transition: all 0.3s ease;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    border-radius: 0.5rem; /* rounded-lg */
}

.medicamento-overlay:hover {
    backdrop-filter: blur(0px);
    background-opacity: 0.4;
}

/* Asegurar que el contenedor del medicamento tenga posición relativa */
.medicamento-item {
    position: relative !important;
    overflow: hidden !important;
}

/* Permitir que el texto sea parcialmente visible a través del overlay */
.medicamento-item.medicamento-bloqueado input,
.medicamento-item.medicamento-bloqueado select,
.medicamento-item.medicamento-bloqueado textarea {
    color: #374151 !important;
    opacity: 0.7;
}

.medicamento-item.medicamento-bloqueado label {
    opacity: 0.8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables de estado de la cita desde PHP
    const estadoCita = @json($estadoCita);
    const esAtendida = @json($esAtendida);
    const esAusente = @json($esAusente);
    const esSoloLectura = @json($esSoloLectura);
    const puedeEditar = @json($puedeEditar);
    
    // Aplicar restricciones según el estado de la cita
    if (esSoloLectura) {
        aplicarModoSoloLectura();
    }
    
    // Inicializar contador según los medicamentos existentes
    let contadorMedicamentos = document.querySelectorAll('.medicamento-item').length;
    
    // Datos de medicamentos y frecuencias desde la base de datos
    const medicamentosDB = @json($medicamentos ?? []);
    const frecuenciasDB = @json($frecuencias ?? []);
    
    // Configurar botones de remover iniciales
    actualizarBotonesRemover();
    
    // Marcar medicamentos existentes de la base de datos como guardados
    marcarMedicamentosExistentesComoGuardados();
    
    // Funciones para proteger medicamentos existentes (basado en el patrón mejorado)
    window.confirmarDesbloqueoMedicamento = function(bloque) {
        // Solo procesar si está bloqueado
        if (bloque.dataset.bloqueado === 'true') {
            Swal.fire({
                title: "✏️ Modificar Medicamento",
                text: "Este medicamento ya está guardado. ¿Desea modificarlo?",
                icon: "question",
                showDenyButton: true,
                confirmButtonText: "Sí, modificar",
                denyButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Desbloquear todo el bloque
                    desbloquearMedicamento(bloque);
                }
            });
        }
    };

    function desbloquearMedicamento(bloque) {
        // Remover el overlay
        const overlay = bloque.querySelector('.medicamento-overlay');
        if (overlay) {
            overlay.remove();
        }
        
        // Obtener el índice del medicamento
        const index = bloque.dataset.medicamentoIndex;
        
        // Eliminar los campos hidden
        const hiddenFields = bloque.querySelectorAll('input[type="hidden"]');
        hiddenFields.forEach(field => field.remove());
        
        // Habilitar todos los campos visuales y cambiar sus nombres a los normales
        const camposVisuales = bloque.querySelectorAll('select[name*="medicamentos_visual"], input[name*="medicamentos_visual"], textarea[name*="medicamentos_visual"]');
        camposVisuales.forEach(campo => {
            campo.disabled = false;
            campo.classList.remove('bg-gray-100', 'cursor-not-allowed');
            campo.classList.add('bg-white');
            
            // Cambiar el name del campo visual al name normal
            const currentName = campo.name;
            const newName = currentName.replace('medicamentos_visual', 'medicamentos');
            campo.name = newName;
        });
        
        // Marcar como desbloqueado
        bloque.dataset.bloqueado = 'false';
        bloque.onclick = null;
        bloque.classList.remove('cursor-pointer', 'medicamento-bloqueado');
        
        // Agregar indicador visual de que está desbloqueado
        const titulo = bloque.querySelector('h4');
        if (titulo && !titulo.querySelector('.indicador-desbloqueado')) {
            titulo.innerHTML += ' <span class="indicador-desbloqueado text-orange-500 text-sm">🔓 Desbloqueado</span>';
        }
        
        // Configurar confirmación de cambio en el select de medicamento
        const selectMedicamento = bloque.querySelector('select[name*="[medicamento]"]');
        if (selectMedicamento) {
            selectMedicamento.onchange = confirmarCambioMedicamento;
        }
        
        // Guardar cambios después de desbloquear
        setTimeout(() => {
            guardarDatosEnSesion();
        }, 100);
    }

    window.confirmarCambioMedicamento = function() {
        const select = this;
        const original = select.dataset.original;
        if (select.value !== original && select.value !== '') {
            Swal.fire({
                title: "Confirmar cambio",
                text: "¿Confirma el cambio de medicamento?",
                icon: "question",
                showDenyButton: true,
                confirmButtonText: "Confirmar",
                denyButtonText: "Cancelar"
            }).then((result) => {
                if (!result.isConfirmed) {
                    select.value = original;
                }
            });
        }
    };
    
    // Función para agregar medicamento
    document.getElementById('btn-agregar-medicamento')?.addEventListener('click', function() {
        // Verificar si se puede editar
        if (esSoloLectura) {
            if (esAtendida) {
                Swal.fire({
                    title: "Cita Atendida",
                    text: "Esta cita ya fue atendida. No se pueden agregar más medicamentos.",
                    icon: "info",
                    confirmButtonText: "Entendido"
                });
            }
            return;
        }
        
        // Validar que todos los medicamentos existentes tengan los campos obligatorios completos
        if (!validarMedicamentosCompletos()) {
            return; // No agregar si hay medicamentos incompletos
        }
        
        // Bloquear medicamentos existentes (solo los que estén completos)
        bloquearMedicamentosExistentes();
        
        const container = document.getElementById('medicamentos-container');
        const nuevoMedicamento = crearMedicamento(contadorMedicamentos);
        container.appendChild(nuevoMedicamento);
        contadorMedicamentos++;
        actualizarNumeracion();
        actualizarBotonesRemover();
        
        // Guardar estado después de agregar
        setTimeout(() => {
            guardarDatosEnSesion();
        }, 100);
    });
    
    // Función para validar que todos los medicamentos existentes estén completos
    function validarMedicamentosCompletos() {
        const medicamentos = document.querySelectorAll('.medicamento-item');
        const medicamentosIncompletos = [];
        
        medicamentos.forEach((medicamento, index) => {
            // Solo validar medicamentos que NO estén bloqueados (los bloqueados ya están guardados)
            if (medicamento.dataset.bloqueado === 'true') {
                return; // Saltar medicamentos ya bloqueados/guardados
            }
            
            // Obtener los campos obligatorios del medicamento
            const selectMedicamento = medicamento.querySelector('select[name*="[medicamento]"]');
            const inputDosis = medicamento.querySelector('input[name*="[dosis]"]');
            const selectFrecuencia = medicamento.querySelector('select[name*="[frecuencia]"]');
            const selectDuracion = medicamento.querySelector('select[name*="[duracion]"]');
            
            // Verificar que todos los campos obligatorios estén completos
            let incompleto = false;
            const camposFaltantes = [];
            
            if (!selectMedicamento || !selectMedicamento.value) {
                incompleto = true;
                camposFaltantes.push('Medicamento');
            }
            
            if (!inputDosis || !inputDosis.value.trim()) {
                incompleto = true;
                camposFaltantes.push('Dosis');
            }
            
            if (!selectFrecuencia || !selectFrecuencia.value) {
                incompleto = true;
                camposFaltantes.push('Frecuencia');
            }
            
            if (!selectDuracion || !selectDuracion.value) {
                incompleto = true;
                camposFaltantes.push('Duración');
            }
            
            if (incompleto) {
                medicamentosIncompletos.push({
                    numero: index + 1,
                    campos: camposFaltantes
                });
            }
        });
        
        // Si hay medicamentos incompletos, mostrar error
        if (medicamentosIncompletos.length > 0) {
            let mensaje = '<div class="text-left">';
            mensaje += '<p class="mb-3">Para agregar un nuevo medicamento, primero debe completar:</p>';
            
            medicamentosIncompletos.forEach(med => {
                mensaje += `<div class="bg-yellow-50 border border-yellow-200 rounded p-2 mb-2">`;
                mensaje += `<strong class="text-yellow-800">📋 Medicamento ${med.numero}:</strong><br>`;
                mensaje += `<span class="text-yellow-700">• Faltan: ${med.campos.join(', ')}</span>`;
                mensaje += `</div>`;
            });
            
            mensaje += '<p class="mt-3 text-sm text-gray-600">💡 Complete estos campos y luego podrá agregar otro medicamento.</p>';
            mensaje += '</div>';
            
            Swal.fire({
                title: "⚠️ Medicamentos incompletos",
                html: mensaje,
                icon: "warning",
                confirmButtonText: "Entendido",
                confirmButtonColor: "#f59e0b",
                width: '500px'
            });
            
            return false;
        }
        
        return true;
    }
    
    // Función para bloquear medicamentos existentes (solo los completos)
    function bloquearMedicamentosExistentes() {
        const medicamentos = document.querySelectorAll('.medicamento-item');
        
        medicamentos.forEach((medicamento, index) => {
            // Solo bloquear medicamentos que NO estén ya bloqueados
            if (medicamento.dataset.bloqueado === 'true') {
                return; // Ya está bloqueado, saltar
            }
            
            // Verificar que el medicamento esté completo antes de bloquearlo
            const selectMedicamento = medicamento.querySelector('select[name*="[medicamento]"]');
            const inputDosis = medicamento.querySelector('input[name*="[dosis]"]');
            const selectFrecuencia = medicamento.querySelector('select[name*="[frecuencia]"]');
            const selectDuracion = medicamento.querySelector('select[name*="[duracion]"]');
            
            const estaCompleto = selectMedicamento?.value && 
                               inputDosis?.value?.trim() && 
                               selectFrecuencia?.value && 
                               selectDuracion?.value;
            
            if (!estaCompleto) {
                return; // No bloquear medicamentos incompletos
            }
            
            // Obtener todos los campos del medicamento
            const campos = medicamento.querySelectorAll('select, input[type="text"], textarea');
            
            // Crear campos hidden con los valores actuales
            campos.forEach(campo => {
                if (campo.value && campo.value.trim() !== '') {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = campo.name;
                    hiddenField.value = campo.value;
                    medicamento.appendChild(hiddenField);
                    
                    // Cambiar el name del campo visual
                    const visualName = campo.name.replace('medicamentos', 'medicamentos_visual');
                    campo.name = visualName;
                }
            });
            
            // Marcar como bloqueado
            medicamento.dataset.bloqueado = 'true';
            medicamento.dataset.medicamentoIndex = index;
            medicamento.onclick = function() { confirmarDesbloqueoMedicamento(this); };
            medicamento.classList.add('cursor-pointer', 'medicamento-bloqueado');
            
            // Crear y agregar overlay de bloqueo
            const overlay = crearOverlayBloqueo(index + 1);
            medicamento.appendChild(overlay);
            
            // Deshabilitar campos visuales
            campos.forEach(campo => {
                campo.disabled = true;
                campo.classList.add('bg-gray-100', 'cursor-not-allowed');
                campo.classList.remove('bg-white');
            });
            

        });
    }
    
    // Función para marcar medicamentos existentes como guardados
    function marcarMedicamentosExistentesComoGuardados() {
        const medicamentos = document.querySelectorAll('.medicamento-item');
        
        // Los medicamentos existentes ya vienen con el patrón correcto desde el Blade
        // Solo necesitamos asegurar que tengan el z-index correcto para el overlay
        medicamentos.forEach((medicamento, index) => {
            if (medicamento.classList.contains('medicamento-bloqueado')) {
                medicamento.style.position = 'relative';
                medicamento.dataset.medicamentoIndex = index;
                const overlay = medicamento.querySelector('.medicamento-overlay');
                if (overlay) {
                    overlay.style.zIndex = '10';
                }
            }
        });
    }
    
    // Función para crear overlay de bloqueo
    function crearOverlayBloqueo(numeroMedicamento) {
        const overlay = document.createElement('div');
        overlay.className = 'medicamento-overlay absolute top-0 left-0 right-0 bottom-0 bg-green-100 bg-opacity-20 flex items-center justify-center rounded-lg z-10';
        overlay.style.position = 'absolute';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.right = '0';
        overlay.style.bottom = '0';
        overlay.style.borderRadius = '0.5rem';
        overlay.innerHTML = `
            <div class="text-center p-2">
                <div class="bg-green-200 bg-opacity-90 border border-green-400 rounded-lg p-2 shadow-sm">
                    <div class="flex items-center justify-center mb-1">
                        <svg class="w-4 h-4 text-green-700 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-green-800 font-medium text-sm">MEDICAMENTO ${numeroMedicamento} GUARDADO</span>
                    </div>
                    <p class="text-green-700 text-xs mb-2">Haz clic para modificar</p>
                    <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs" 
                            onclick="event.stopPropagation(); confirmarDesbloqueoMedicamento(this.closest('.medicamento-item'))">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modificar
                    </button>
                </div>
            </div>
        `;
        return overlay;
    }
    
    // Función para crear un nuevo medicamento dinámicamente
    function crearMedicamento(index) {
        const medicamentoDiv = document.createElement('div');
        medicamentoDiv.className = 'medicamento-item border-2 border-gray-300 rounded-lg p-4 bg-gray-50';
        medicamentoDiv.style.position = 'relative'; // Para overlay
        medicamentoDiv.style.overflow = 'hidden'; // Para contener el overlay
        
        medicamentoDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-medium text-gray-800">Medicamento ${index + 1}</h4>
                <button type="button" class="btn-remover-medicamento text-red-500 hover:text-red-700" onclick="removerMedicamento(this)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-red-600">Medicamento *</label>
                    <select name="medicamentos[${index}][medicamento]" class="w-full border border-gray-300 rounded-md p-2">
                        <option value="">Seleccione un medicamento</option>
                        ${medicamentosDB.map(med => `
                            <option value="${med.id_medicamento}">
                                ${med.nombre} - ${med.presentacion}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-red-600">Dosis *</label>
                    <input type="text" name="medicamentos[${index}][dosis]" class="w-full border border-gray-300 rounded-md p-2" 
                           placeholder="Ej: 500mg, 1 tableta">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-red-600">Frecuencia *</label>
                    <select name="medicamentos[${index}][frecuencia]" class="w-full border border-gray-300 rounded-md p-2">
                        <option value="">Seleccione frecuencia</option>
                        ${frecuenciasDB.map(frec => `
                            <option value="${frec.id_frecuencia}">${frec.descripcion}</option>
                        `).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-red-600">Duración *</label>
                    <select name="medicamentos[${index}][duracion]" class="w-full border border-gray-300 rounded-md p-2">
                        <option value="">Seleccione duración</option>
                        <option value="3 días">3 días</option>
                        <option value="5 días">5 días</option>
                        <option value="7 días">7 días</option>
                        <option value="10 días">10 días</option>
                        <option value="14 días">14 días</option>
                        <option value="21 días">21 días</option>
                        <option value="30 días">30 días</option>
                        <option value="60 días">60 días</option>
                        <option value="90 días">90 días</option>
                        <option value="Continuo">Tratamiento continuo</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Instrucciones Especiales</label>
                <textarea name="medicamentos[${index}][instrucciones]" rows="2" 
                         class="w-full border border-gray-300 rounded-md p-2" 
                         placeholder="Ej: Tomar con abundante agua, evitar alcohol, etc."></textarea>
            </div>
        `;
        
        return medicamentoDiv;
    }
    
    // Función para remover medicamento
    window.removerMedicamento = function(button) {
        const medicamentoItem = button.closest('.medicamento-item');
        medicamentoItem.remove();
        actualizarNumeracion();
        actualizarBotonesRemover();
        
        // Después de remover, verificar si hay medicamentos sin overlay que necesiten desbloquearse
        const medicamentosRestantes = document.querySelectorAll('.medicamento-item');
        if (medicamentosRestantes.length >= 1) {
            // Buscar el último medicamento que NO tenga overlay (es decir, que sea nuevo y no guardado)
            let ultimoMedicamentoSinOverlay = null;
            for (let i = medicamentosRestantes.length - 1; i >= 0; i--) {
                const medicamento = medicamentosRestantes[i];
                const tieneOverlay = medicamento.querySelector('.medicamento-overlay');
                if (!tieneOverlay && medicamento.dataset.bloqueado === 'true') {
                    ultimoMedicamentoSinOverlay = medicamento;
                    break;
                }
            }
            
            // Solo desbloquear si encontramos un medicamento nuevo sin overlay
            if (ultimoMedicamentoSinOverlay) {
                desbloquearMedicamentoSilencioso(ultimoMedicamentoSinOverlay);
            }
        }
        
        // Guardar estado después de remover
        setTimeout(() => {
            guardarDatosEnSesion();
        }, 100);
    };
    
    // Función auxiliar para desbloquear sin confirmación
    function desbloquearMedicamentoSilencioso(bloque) {
        // Remover el overlay
        const overlay = bloque.querySelector('.medicamento-overlay');
        if (overlay) {
            overlay.remove();
        }
        
        // Habilitar todos los campos
        const campos = bloque.querySelectorAll('select, input, textarea');
        campos.forEach(campo => {
            campo.disabled = false;
            campo.classList.remove('bg-gray-100', 'cursor-not-allowed');
            campo.classList.add('bg-white');
        });
        
        // Marcar como desbloqueado
        bloque.dataset.bloqueado = 'false';
        bloque.onclick = null;
        bloque.classList.remove('cursor-pointer');
    }
    
    // Función para actualizar numeración
    function actualizarNumeracion() {
        const items = document.querySelectorAll('.medicamento-item');
        items.forEach((item, index) => {
            // Actualizar título en vista editable
            const titulo = item.querySelector('h4');
            if (titulo) {
                titulo.textContent = `Medicamento ${index + 1}`;
            }
            
            // Actualizar título en vista bloqueada si existe
            const tituloBloqueado = item.querySelector('.vista-bloqueada h4');
            if (tituloBloqueado) {
                tituloBloqueado.textContent = `Medicamento ${index + 1}`;
            }
            
            // Actualizar los atributos name de todos los campos para mantener secuencia correcta
            const campos = item.querySelectorAll('input, select, textarea');
            campos.forEach(campo => {
                if (campo.name && campo.name.includes('medicamentos[')) {
                    // Extraer el tipo de campo (medicamento, dosis, frecuencia, etc.)
                    const matches = campo.name.match(/medicamentos\[\d+\]\[(\w+)\]/);
                    if (matches) {
                        const tipoCampo = matches[1];
                        campo.name = `medicamentos[${index}][${tipoCampo}]`;
                    }
                }
                
                // También actualizar campos medicamentos_visual
                if (campo.name && campo.name.includes('medicamentos_visual[')) {
                    const matches = campo.name.match(/medicamentos_visual\[\d+\]\[(\w+)\]/);
                    if (matches) {
                        const tipoCampo = matches[1];
                        campo.name = `medicamentos_visual[${index}][${tipoCampo}]`;
                    }
                }
            });
            
            // Actualizar data-medicamento-index para medicamentos bloqueados
            if (item.dataset.bloqueado === 'true') {
                item.dataset.medicamentoIndex = index;
            }
            

        });
    }
    
    // Función para mostrar/ocultar botones de remover
    function actualizarBotonesRemover() {
        const items = document.querySelectorAll('.medicamento-item');
        const botones = document.querySelectorAll('.btn-remover-medicamento');
        
        botones.forEach(boton => {
            if (items.length > 1) {
                boton.classList.remove('hidden');
            } else {
                boton.classList.add('hidden');
            }
        });
    }
    
    // Función para validar campos obligatorios y finalizar consulta
    const btnValidarFinalizar = document.getElementById('btn-validar-finalizar');
    if (btnValidarFinalizar) {
        btnValidarFinalizar.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Verificar si se puede editar
            if (esSoloLectura) {
                if (esAtendida) {
                    Swal.fire({
                        title: "Cita Atendida",
                        text: "Esta cita ya fue atendida. No se puede finalizar nuevamente.",
                        icon: "info",
                        confirmButtonText: "Entendido"
                    });
                }
                return;
            }
            
            // Verificar campos obligatorios
            const camposObligatorios = [];
            
            // Verificar síntomas reportados
            const sintomas = document.getElementById('sintomas_reportados');
            if (!sintomas || !sintomas.value.trim()) {
                camposObligatorios.push('Síntomas Reportados');
            }
            
            // Verificar exploración física (ya no es obligatorio)
            // const exploracion = document.getElementById('exploracion_fisica');
            // if (!exploracion || !exploracion.value.trim()) {
            //     camposObligatorios.push('Exploración Física');
            // }
            
            // Verificar diagnóstico
            const diagnostico = document.getElementById('diagnostico');
            if (!diagnostico || !diagnostico.value.trim()) {
                camposObligatorios.push('Diagnóstico');
            }
            
            // Verificar indicaciones
            const indicaciones = document.querySelector('textarea[name="indicaciones"]');
            if (!indicaciones || !indicaciones.value.trim()) {
                camposObligatorios.push('Indicaciones');
            }
            
            // Verificar medicamentos si hay al menos uno con datos
            const medicamentosSelects = document.querySelectorAll('select[name*="[medicamento]"]');
            const medicamentosHidden = document.querySelectorAll('input[type="hidden"][name*="[medicamento]"]');
            let tieneAlgunMedicamento = false;
            let medicamentosIncompletos = false;
            
            // Verificar medicamentos activos (no bloqueados) - Método más directo
            medicamentosSelects.forEach((select) => {
                if (select.value && select.value.trim() !== '') {
                    tieneAlgunMedicamento = true;
                    
                    // Obtener el índice del medicamento desde el name
                    const nameMatch = select.name.match(/medicamentos\[(\d+)\]\[medicamento\]/);
                    if (nameMatch) {
                        const index = nameMatch[1];
                        
                        // Verificar que este medicamento esté completo
                        const dosis = document.querySelector(`input[name="medicamentos[${index}][dosis]"]`);
                        const frecuencia = document.querySelector(`select[name="medicamentos[${index}][frecuencia]"]`);
                        const duracion = document.querySelector(`select[name="medicamentos[${index}][duracion]"]`);
                        
                        if (!dosis || !dosis.value.trim() || !frecuencia || !frecuencia.value || !duracion || !duracion.value) {
                            medicamentosIncompletos = true;
                        }
                    }
                }
            });
            
            // Verificar medicamentos bloqueados (campos hidden)
            const indicesHiddenProcesados = new Set();
            medicamentosHidden.forEach((hiddenField) => {
                if (hiddenField.value && hiddenField.value.trim() !== '') {
                    const nameMatch = hiddenField.name.match(/medicamentos\[(\d+)\]\[medicamento\]/);
                    if (nameMatch) {
                        const index = nameMatch[1];
                        
                        // Evitar procesar el mismo índice múltiples veces
                        if (indicesHiddenProcesados.has(index)) {
                            return;
                        }
                        indicesHiddenProcesados.add(index);
                        
                        tieneAlgunMedicamento = true;
                        
                        // Para medicamentos hidden (guardados), asumir que están completos
                        // Ya que vienen de la base de datos
                        const dosisHidden = document.querySelector(`input[type="hidden"][name="medicamentos[${index}][dosis]"]`);
                        const frecuenciaHidden = document.querySelector(`input[type="hidden"][name="medicamentos[${index}][frecuencia]"]`);
                        const duracionHidden = document.querySelector(`input[type="hidden"][name="medicamentos[${index}][duracion]"]`);
                        
                        // Solo marcar como incompleto si realmente faltan datos críticos
                        if (!dosisHidden || !dosisHidden.value || !frecuenciaHidden || !frecuenciaHidden.value || !duracionHidden || !duracionHidden.value) {
                            medicamentosIncompletos = true;
                        }
                    }
                }
            });
            
            // Verificación adicional: buscar medicamentos con overlay (guardados de la BD)
            const medicamentosConOverlay = document.querySelectorAll('.medicamento-item .medicamento-overlay');
            if (medicamentosConOverlay.length > 0) {
                tieneAlgunMedicamento = true;
                // Los medicamentos con overlay se consideran válidos (vienen de la BD)
            }
            
            if (medicamentosIncompletos) {
                camposObligatorios.push('Datos completos de medicamentos seleccionados');
            }
            
            // Si hay campos faltantes, mostrar error
            if (camposObligatorios.length > 0) {
                Swal.fire({
                    title: "Campos obligatorios faltantes",
                    html: `Debe completar los siguientes campos:<br><br><ul class="text-left">${camposObligatorios.map(campo => `<li>• ${campo}</li>`).join('')}</ul>`,
                    icon: "warning",
                    confirmButtonText: "Entendido"
                });
                return;
            }
            
            // Si todo está completo, confirmar finalización
            Swal.fire({
                title: "¿Finalizar la consulta?",
                text: "Esto guardará todos los datos de la consulta y marcará la cita como atendida",
                icon: "question",
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: "Finalizar Consulta",
                denyButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Limpiar datos temporales antes de enviar
                    sessionStorage.removeItem('consulta_temporal_{{ $cita["id"] }}');
                    // Enviar el formulario principal
                    document.getElementById('form-consulta-completa').submit();
                }
            });
        });
    }
    
    // Persistir datos en sessionStorage para mantener los valores al navegar entre pestañas
    const form = document.getElementById('form-consulta-completa');
    if (form) {
        // Cargar datos guardados al cargar la página
        setTimeout(() => {
            cargarDatosGuardados();
        }, 100);
        
        // Guardar datos cuando cambien los campos (con debounce)
        let timeoutId;
        
        // Eventos para campos existentes
        form.addEventListener('input', function(e) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                guardarDatosEnSesion();
            }, 300);
        });
        
        form.addEventListener('change', function(e) {
            guardarDatosEnSesion();
        });
        
        // Guardar antes de navegar a otra pestaña
        document.querySelectorAll('a[href*="?tab="]').forEach(link => {
            link.addEventListener('click', function(e) {
                guardarDatosEnSesion();
            });
        });
    }
    
    function guardarDatosEnSesion() {
        if (!form) return;
        
        const data = {};
        
        // Obtener todos los campos del formulario
        const todosLosCampos = form.querySelectorAll('input, textarea, select');
        
        todosLosCampos.forEach(campo => {
            if (campo.name && campo.name.trim() !== '') {
                // Solo guardar si tiene valor
                if (campo.value && campo.value.trim() !== '') {
                    data[campo.name] = campo.value;
                }
            }
        });
        

        sessionStorage.setItem('consulta_temporal_{{ $cita["id"] }}', JSON.stringify(data));
    }
    
    function cargarDatosGuardados() {
        const datosGuardados = sessionStorage.getItem('consulta_temporal_{{ $cita["id"] }}');
        
        if (datosGuardados) {
            try {
                const data = JSON.parse(datosGuardados);
                
                
                // Restaurar todos los campos
                Object.keys(data).forEach(fieldName => {
                    const elemento = document.querySelector(`[name="${fieldName}"]`);
                    if (elemento && data[fieldName]) {
                        elemento.value = data[fieldName];
                        
                    }
                });
                
                // Manejar medicamentos dinámicos especialmente
                manejarMedicamentosDinamicos(data);
                
            } catch (e) {
                
                sessionStorage.removeItem('consulta_temporal_{{ $cita["id"] }}');
            }
        }
    }
    
    function manejarMedicamentosDinamicos(data) {
        // Buscar datos de medicamentos guardados
        const medicamentosEnData = {};
        Object.keys(data).forEach(key => {
            if (key.startsWith('medicamentos[')) {
                const matches = key.match(/medicamentos\[(\d+)\]\[(\w+)\]/);
                if (matches) {
                    const index = parseInt(matches[1]);
                    const campo = matches[2];
                    if (!medicamentosEnData[index]) {
                        medicamentosEnData[index] = {};
                    }
                    medicamentosEnData[index][campo] = data[key];
                }
            }
        });
        
        const container = document.getElementById('medicamentos-container');
        if (!container) return;
        
        const medicamentosExistentes = container.querySelectorAll('.medicamento-item');
        const maxIndex = Math.max(...Object.keys(medicamentosEnData).map(k => parseInt(k)), -1);
        
        // Si tenemos más medicamentos guardados que los actuales, crear los faltantes
        for (let i = medicamentosExistentes.length; i <= maxIndex; i++) {
            if (medicamentosEnData[i] && Object.keys(medicamentosEnData[i]).length > 0) {
                const nuevoMedicamento = crearMedicamento(i);
                container.appendChild(nuevoMedicamento);
                contadorMedicamentos = Math.max(contadorMedicamentos, i + 1);
            }
        }
        
        // Actualizar botones después de crear medicamentos
        actualizarBotonesRemover();
        
        // Ahora restaurar valores en todos los medicamentos
        Object.keys(medicamentosEnData).forEach(index => {
            const medicamentoData = medicamentosEnData[index];
            Object.keys(medicamentoData).forEach(campo => {
                const elemento = document.querySelector(`[name="medicamentos[${index}][${campo}]"]`);
                if (elemento) {
                    elemento.value = medicamentoData[campo];
                    
                }
            });
        });
        
        // Si hay más de un medicamento, bloquear todos excepto el último
        const todosMedicamentos = container.querySelectorAll('.medicamento-item');
        if (todosMedicamentos.length > 1) {
            // Bloquear todos los medicamentos EXCEPTO el último
            for (let i = 0; i < todosMedicamentos.length - 1; i++) {
                const medicamento = todosMedicamentos[i];
                // Aplicar bloqueo usando el nuevo sistema de overlay
                medicamento.dataset.bloqueado = 'true';
                medicamento.onclick = function() { confirmarDesbloqueoMedicamento(this); };
                medicamento.classList.add('cursor-pointer');
                
                const overlay = crearOverlayBloqueo(i + 1);
                medicamento.appendChild(overlay);
                
                const campos = medicamento.querySelectorAll('select, input, textarea');
                campos.forEach(campo => {
                    campo.disabled = true;
                    campo.classList.add('bg-gray-100', 'cursor-not-allowed');
                    campo.classList.remove('bg-white');
                });
            }
        }
    }
    
    // ==================== NAVEGACIÓN DE PESTAÑAS ====================
    // Variable global para el tab activo (siempre inicia en detalle)
    let tabActivo = 'detalle';
    
    function configurarNavegacionPestanas() {
        // Configurar listeners para los enlaces de pestañas
        const enlaces = document.querySelectorAll('.tab-link');
        
        
        enlaces.forEach((link, index) => {
            const tabName = link.getAttribute('data-tab');
            
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const nuevaTab = this.getAttribute('data-tab');
                
                
                if (nuevaTab) {
                    cambiarPestana(nuevaTab);
                } else {
                    
                }
            });
        });
        
        // Mostrar la pestaña inicial (siempre detalle) y configurar estado visual inicial
        mostrarPestana(tabActivo);
        actualizarEstadoVisualPestanas(tabActivo);
    }
    
    function actualizarEstadoVisualPestanas(tabActiva) {
        document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.remove('bg-white', 'shadow');
            if (link.getAttribute('data-tab') === tabActiva) {
                link.classList.add('bg-white', 'shadow');
            }
        });
    }
    
    function cambiarPestana(nuevaTab) {
        
        
        // Guardar datos actuales antes de cambiar
        guardarDatosEnSesion();
        
        // Actualizar la pestaña activa
        tabActivo = nuevaTab;
        
        // Actualizar la navegación visual
        actualizarEstadoVisualPestanas(nuevaTab);
        
        // Mostrar el contenido correspondiente
        mostrarPestana(nuevaTab);
        
        
    }
    
    function mostrarPestana(tab) {
        // Ocultar todas las secciones
        const secciones = [
            'seccion-detalle',
            'seccion-apuntes', 
            'seccion-diagnostico',
            'seccion-examenes',
            'seccion-receta',
            'seccion-indicaciones'
        ];
        
        secciones.forEach(seccionId => {
            const elemento = document.getElementById(seccionId);
            if (elemento) {
                elemento.style.display = 'none';
            }
        });
        
        // Mostrar la sección correspondiente
        const seccionActiva = document.getElementById(`seccion-${tab}`);
        if (seccionActiva) {
            seccionActiva.style.display = 'block';
            
        } else {
            
        }
    }
    
    // Configurar navegación al cargar la página
    configurarNavegacionPestanas();
    
    // Función para aplicar modo solo lectura
    function aplicarModoSoloLectura() {
        // Deshabilitar botones de agregar medicamento
        const btnAgregar = document.getElementById('btn-agregar-medicamento');
        if (btnAgregar) {
            btnAgregar.style.display = 'none';
        }
        
        // Deshabilitar botones de finalizar consulta
        const btnFinalizar = document.getElementById('btn-validar-finalizar');
        if (btnFinalizar) {
            btnFinalizar.style.display = 'none';
        }
        
        // Deshabilitar funciones de modificación de medicamentos
        const medicamentos = document.querySelectorAll('.medicamento-item');
        medicamentos.forEach(medicamento => {
            // Remover eventos de click para desbloqueo
            medicamento.onclick = null;
            medicamento.classList.remove('cursor-pointer');
            
            // Si es atendida, mostrar overlay diferente (solo vista)
            if (esAtendida) {
                const overlay = medicamento.querySelector('.medicamento-overlay');
                if (overlay) {
                    const button = overlay.querySelector('button');
                    if (button) {
                        button.style.display = 'none';
                    }
                    const texto = overlay.querySelector('p');
                    if (texto) {
                        texto.textContent = 'Medicamento guardado - Solo lectura';
                    }
                }
            }
            
            // Deshabilitar botones de remover
            const btnRemover = medicamento.querySelector('.btn-remover-medicamento');
            if (btnRemover) {
                btnRemover.style.display = 'none';
            }
        });
        
        // Deshabilitar todas las funciones de edición dinámicas
        window.confirmarDesbloqueoMedicamento = function() {
            if (esAtendida) {
                Swal.fire({
                    title: "Cita Atendida",
                    text: "Esta cita ya fue atendida. Solo puede visualizar la información.",
                    icon: "info",
                    confirmButtonText: "Entendido"
                });
            }
        };
        
        window.removerMedicamento = function() {
            // No hacer nada en modo solo lectura
        };
    }
    
    // Función para validar en tiempo real los campos obligatorios
    function configurarValidacionTiempoReal() {
        // Agregar listeners a todos los medicamentos existentes y futuros
        document.addEventListener('input', function(e) {
            if (e.target.matches('select[name*="medicamentos"], input[name*="medicamentos"]')) {
                validarCampoMedicamento(e.target);
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.matches('select[name*="medicamentos"]')) {
                validarCampoMedicamento(e.target);
            }
        });
    }
    
    function validarCampoMedicamento(campo) {
        // No validar campos visuales deshabilitados (medicamentos bloqueados)
        if (campo.disabled || campo.name.includes('medicamentos_visual')) {
            return;
        }
        
        const medicamentoItem = campo.closest('.medicamento-item');
        if (!medicamentoItem) return;
        
        // Remover clases de error previas
        campo.classList.remove('border-red-500', 'border-red-400', 'border-green-500', 'border-green-400');
        campo.classList.add('border-gray-300');
        
        // Validar si el campo está vacío (para campos obligatorios)
        const esObligatorio = campo.name.includes('[medicamento]') || 
                             campo.name.includes('[dosis]') || 
                             campo.name.includes('[frecuencia]') || 
                             campo.name.includes('[duracion]');
        
        if (esObligatorio && (!campo.value || campo.value.trim() === '')) {
            campo.classList.remove('border-gray-300');
            campo.classList.add('border-red-500', 'border-2');
        } else if (campo.value && campo.value.trim() !== '') {
            campo.classList.remove('border-gray-300');
            campo.classList.add('border-green-500', 'border-2');
        }
        
        // Actualizar el estado visual del medicamento completo
        actualizarEstadoVisualMedicamento(medicamentoItem);
    }
    
    function actualizarEstadoVisualMedicamento(medicamentoItem) {
        if (medicamentoItem.dataset.bloqueado === 'true') {
            return; // No actualizar medicamentos bloqueados
        }
        
        const selectMedicamento = medicamentoItem.querySelector('select[name*="[medicamento]"]');
        const inputDosis = medicamentoItem.querySelector('input[name*="[dosis]"]');
        const selectFrecuencia = medicamentoItem.querySelector('select[name*="[frecuencia]"]');
        const selectDuracion = medicamentoItem.querySelector('select[name*="[duracion]"]');
        
        const estaCompleto = selectMedicamento?.value && 
                           inputDosis?.value?.trim() && 
                           selectFrecuencia?.value && 
                           selectDuracion?.value;
        
        const titulo = medicamentoItem.querySelector('h4');
        
        // Limpiar TODOS los indicadores previos
        const indicadorCompleto = titulo?.querySelector('.indicador-completo');
        const indicadorParcial = titulo?.querySelector('.indicador-parcial');
        if (indicadorCompleto) {
            indicadorCompleto.remove();
        }
        if (indicadorParcial) {
            indicadorParcial.remove();
        }
        
        // Remover todas las clases de borde previas
        medicamentoItem.classList.remove('border-gray-200', 'border-yellow-400', 'border-green-400', 'border-2', 'border-3');
        
        if (estaCompleto) {
            // Agregar indicador de completo
            titulo.innerHTML += ' <span class="indicador-completo text-green-600 text-sm font-semibold">✓ Completo</span>';
            medicamentoItem.classList.add('border-green-400', 'border-3');
        } else {
            // Verificar si tiene algunos campos llenos
            const tieneAlgunCampo = selectMedicamento?.value || 
                                  inputDosis?.value?.trim() || 
                                  selectFrecuencia?.value || 
                                  selectDuracion?.value;
            
            if (tieneAlgunCampo) {
                medicamentoItem.classList.add('border-yellow-400', 'border-3');
                // Agregar indicador de parcialmente completo
                titulo.innerHTML += ' <span class="indicador-parcial text-yellow-600 text-sm font-semibold">⚠️ Incompleto</span>';
            } else {
                medicamentoItem.classList.add('border-gray-300', 'border-2');
            }
        }
    }
    
    // Configurar validación al cargar la página
    configurarValidacionTiempoReal();
});
</script>
