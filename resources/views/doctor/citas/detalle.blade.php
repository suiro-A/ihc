@extends('layouts.app')

@section('content')
@php
    $tab = request('tab', 'detalle');
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

    <!-- Pestañas Simples -->
    <div class="mb-6">
        <div class="flex bg-gray-100 rounded-lg p-1">
            <a href="?tab=detalle" class="px-4 py-2 rounded-md text-sm {{ $tab == 'detalle' ? 'bg-white shadow' : '' }}">
                Información
            </a>
            <a href="?tab=apuntes" class="px-4 py-2 rounded-md text-sm {{ $tab == 'apuntes' ? 'bg-white shadow' : '' }}">
                Apuntes
                @if($apuntesActual)
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                @endif
            </a>
            <a href="?tab=diagnostico" class="px-4 py-2 rounded-md text-sm {{ $tab == 'diagnostico' ? 'bg-white shadow' : '' }}">
                Diagnóstico
                @if(isset($diagnosticoActual) && $diagnosticoActual)
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                @endif
            </a>
            <a href="?tab=examenes" class="px-4 py-2 rounded-md text-sm {{ $tab == 'examenes' ? 'bg-white shadow' : '' }}">
                Exámenes
            </a>
            <a href="?tab=receta" class="px-4 py-2 rounded-md text-sm {{ $tab == 'receta' ? 'bg-white shadow' : '' }}">
                Receta
                @if($recetaActual && $recetaActual->recetaMedicamentos->count() > 0)
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                @endif
            </a>
            <a href="?tab=indicaciones" class="px-4 py-2 rounded-md text-sm {{ $tab == 'indicaciones' ? 'bg-white shadow' : '' }}">
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
                @if($tab == 'detalle')
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
                        
                        <!-- Botones de Acción Centrados -->
                        <div class="flex gap-4 justify-center pt-6">
                            <form id="form-ausente" method="POST" action="{{ route('doctor.citas.actualizar-estado', $cita['id']) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="Ausente">
                                <button type="button" id="btn-ausente" class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium">
                                    Ausente
                                </button>
                            </form>
                            <a href="?tab=apuntes" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                Iniciar Consulta
                            </a>
                        </div>
                    </div>

                @elseif($tab == 'apuntes')
                    <h3 class="text-lg font-bold mb-4">Apuntes de la Consulta</h3>
                    <form method="POST" action="{{ route('doctor.citas.apuntes', $cita['id']) }}">
                        @csrf
                        <div class="space-y-6">
                            <!-- Síntomas Reportados (CRÍTICO) -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-red-600">Síntomas Reportados *</label>
                                <textarea name="sintomas_reportados" rows="3" 
                                         class="w-full border rounded-md p-2 @error('sintomas_reportados') border-red-500 @enderror" 
                                         placeholder="¿Qué síntomas describe el paciente? Ej: Dolor de cabeza intenso desde hace 3 días..."
                                         >{{ old('sintomas_reportados', $apuntesActual->sintomas_reportados ?? '') }}</textarea>
                                @error('sintomas_reportados')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Exploración Física (CRÍTICO) -->
                            <div>
                                <label class="block text-sm font-medium mb-1 text-red-600">Exploración Física *</label>
                                <textarea name="exploracion_fisica" rows="3" 
                                         class="w-full border rounded-md p-2 @error('exploracion_fisica') border-red-500 @enderror" 
                                         placeholder="Resultados del examen físico. Ej: Rigidez de nuca presente, pupilas reactivas..."
                                         >{{ old('exploracion_fisica', $apuntesActual->exploracion_fisica ?? '') }}</textarea>
                                @error('exploracion_fisica')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                {{ $apuntesActual ? 'Actualizar' : 'Guardar' }}
                            </button>
                            <a href="?tab=diagnostico" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Siguiente →
                            </a>
                        </div>
                    </form>

                @elseif($tab == 'diagnostico')
                    <h3 class="text-lg font-bold mb-4">Diagnóstico</h3>

                    <form method="POST" action="{{ route('doctor.citas.diagnostico', $cita['id']) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-red-600">Diagnóstico *</label>
                                <input type="text" name="diagnostico" 
                                       class="w-full border rounded-md p-2 @error('diagnostico') border-red-500 @enderror"
                                       placeholder="Ej: Hipertensión arterial, Diabetes tipo 2, etc."
                                       value="{{ old('diagnostico', $diagnosticoActual->descripcion ?? '') }}"
                                       maxlength="255">
                                @error('diagnostico')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                {{ $diagnosticoActual ? 'Actualizar' : 'Guardar' }}
                            </button>
                            <a href="?tab=examenes" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Siguiente →
                            </a>
                        </div>
                    </form>

                @elseif($tab == 'examenes')
                    <h3 class="text-lg font-bold mb-4">Exámenes Médicos</h3>
                    <form method="POST" action="{{ route('doctor.citas.examenes', $cita['id']) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Exámenes de Laboratorio</label>
                                <textarea name="examenes_laboratorio" rows="3" class="w-full border rounded-md p-2" placeholder="Hemograma completo, Glucosa en ayunas, etc."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Exámenes de Imágenes</label>
                                <textarea name="examenes_imagenes" rows="3" class="w-full border rounded-md p-2" placeholder="Radiografía de tórax, Ecografía abdominal, etc."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Otros Exámenes</label>
                                <textarea name="otros_examenes" rows="3" class="w-full border rounded-md p-2" placeholder="Electrocardiograma, Espirometría, etc."></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Guardar
                            </button>
                            <a href="?tab=receta" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Siguiente →
                            </a>
                        </div>
                    </form>

                @elseif($tab == 'receta')
                    <h3 class="text-lg font-bold mb-4">Receta Médica</h3>
                    <form method="POST" action="{{ route('doctor.citas.receta', $cita['id']) }}" id="form-receta">
                        @csrf
                        <div id="medicamentos-container" class="space-y-4">
                            @if($recetaActual && $recetaActual->recetaMedicamentos->count() > 0)
                                @foreach($recetaActual->recetaMedicamentos as $index => $recetaMed)
                                    <div class="medicamento-item border rounded-lg p-4 bg-gray-50 relative cursor-pointer medicamento-bloqueado" onclick="confirmarDesbloqueoMedicamento(this)" data-medicamento-index="{{ $index }}" data-bloqueado="true">
                                        <!-- Overlay de bloqueo -->
                                        <div class="medicamento-overlay absolute inset-0 bg-gray-300 bg-opacity-80 rounded-lg flex items-center justify-center z-10">
                                            <div class="text-center p-4 bg-white rounded-lg shadow-lg border-2 border-gray-400">
                                                <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                <p class="text-sm text-gray-700 font-semibold mb-1">🔒 Medicamento Protegido</p>
                                                <p class="text-xs text-gray-600">Click para modificar</p>
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
                                                <select name="medicamentos_visual[{{ $index }}][medicamento]" class="w-full border rounded-md p-2" required disabled data-original="{{ $recetaMed->id_medicamento }}">
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
                                                       placeholder="Ej: 500mg, 1 tableta" value="{{ $recetaMed->dosis }}" required disabled>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1 text-red-600">Frecuencia *</label>
                                                <select name="medicamentos_visual[{{ $index }}][frecuencia]" class="w-full border rounded-md p-2" required disabled>
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
                                                <select name="medicamentos_visual[{{ $index }}][duracion]" class="w-full border rounded-md p-2" required disabled>
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
                                @endforeach
                            @else
                                <!-- Medicamento 1 (inicial) -->
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
                                            <select name="medicamentos[0][medicamento]" class="w-full border rounded-md p-2" required>
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
                                                   placeholder="Ej: 500mg, 1 tableta" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-1 text-red-600">Frecuencia *</label>
                                            <select name="medicamentos[0][frecuencia]" class="w-full border rounded-md p-2" required>
                                                <option value="">Seleccione frecuencia</option>
                                                @foreach($frecuencias as $frecuencia)
                                                    <option value="{{ $frecuencia->id_frecuencia }}">{{ $frecuencia->descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-1 text-red-600">Duración *</label>
                                            <select name="medicamentos[0][duracion]" class="w-full border rounded-md p-2" required>
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
                        </div>

                        <!-- Botón para agregar medicamento -->
                        <div class="mt-4">
                            <button type="button" id="btn-agregar-medicamento" 
                                    class="flex items-center px-4 py-2 border border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400 hover:text-gray-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar otro medicamento
                            </button>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                {{ $recetaActual && $recetaActual->recetaMedicamentos->count() > 0 ? 'Actualizar Receta' : 'Guardar Receta' }}
                            </button>
                            <a href="?tab=indicaciones" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Siguiente →
                            </a>
                        </div>
                    </form>

                @elseif($tab == 'indicaciones')
                    <h3 class="text-lg font-bold mb-4">Indicaciones</h3>
                    <form method="POST" action="{{ route('doctor.citas.indicaciones', $cita['id']) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-red-600">Indicaciones *</label>
                                <textarea name="indicaciones" rows="5" 
                                         class="w-full border rounded-md p-2 @error('indicaciones') border-red-500 @enderror"
                                         placeholder="Escriba las indicaciones y recomendaciones para el paciente..."
                                         >{{ old('indicaciones', $indicacionesActual->descripcion ?? '') }}</textarea>
                                @error('indicaciones')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                {{ $indicacionesActual ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>

                    <!-- Formulario separado FUERA -->
                    <div class="mt-4 flex justify-center">
                        <form id="form-finalizar" method="POST" action="{{ route('doctor.citas.actualizar-estado', $cita['id']) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="estado" value="Atendida">
                            <button type="button" id="btn-finalizar" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Finalizar Consulta ✓
                            </button>
                        </form>
                    </div>
                @endif
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contador según los medicamentos existentes
    let contadorMedicamentos = document.querySelectorAll('.medicamento-item').length;
    
    // Datos de medicamentos y frecuencias desde la base de datos
    const medicamentosDB = @json($medicamentos ?? []);
    const frecuenciasDB = @json($frecuencias ?? []);
    
    // Configurar botones de remover iniciales
    actualizarBotonesRemover();
    
    // Funciones para proteger medicamentos existentes
    window.confirmarDesbloqueoMedicamento = function(bloque) {
        // Solo procesar si está bloqueado
        if (bloque.dataset.bloqueado === 'true') {
            Swal.fire({
                title: "⚠️ Modificar Medicamento",
                text: "Está a punto de modificar un medicamento ya registrado. ¿Está seguro?",
                icon: "warning",
                showDenyButton: true,
                confirmButtonText: "Sí, permitir modificación",
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
        
        // Habilitar todos los campos visuales y cambiar los nombres para que se envíen
        const camposVisuales = bloque.querySelectorAll('select[name*="medicamentos_visual"], input[name*="medicamentos_visual"], textarea[name*="medicamentos_visual"]');
        camposVisuales.forEach(campo => {
            campo.disabled = false;
            campo.classList.remove('bg-gray-100');
            campo.classList.add('bg-white');
            
            // Cambiar el name de visual a normal para que se envíe
            const nuevoName = campo.name.replace('medicamentos_visual', 'medicamentos');
            campo.name = nuevoName;
        });
        
        // Remover los campos hidden ya que ahora usaremos los campos visuales
        const camposHidden = bloque.querySelectorAll('input[type="hidden"][name*="medicamentos"]');
        camposHidden.forEach(campo => campo.remove());
        
        // Marcar como desbloqueado
        bloque.dataset.bloqueado = 'false';
        bloque.onclick = null;
        bloque.classList.remove('cursor-pointer');
        
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
        const container = document.getElementById('medicamentos-container');
        const nuevoMedicamento = crearMedicamento(contadorMedicamentos);
        container.appendChild(nuevoMedicamento);
        contadorMedicamentos++;
        actualizarBotonesRemover();
    });
    
    // Función para crear un nuevo medicamento
    function crearMedicamento(index) {
        const div = document.createElement('div');
        div.className = 'medicamento-item border rounded-lg p-4 bg-gray-50';
        
        // Generar opciones de medicamentos
        let medicamentosOptions = '<option value="">Seleccione un medicamento</option>';
        medicamentosDB.forEach(medicamento => {
            medicamentosOptions += `<option value="${medicamento.id_medicamento}">
                ${medicamento.nombre} - ${medicamento.presentacion}
            </option>`;
        });
        
        // Generar opciones de frecuencias
        let frecuenciasOptions = '<option value="">Seleccione frecuencia</option>';
        frecuenciasDB.forEach(frecuencia => {
            frecuenciasOptions += `<option value="${frecuencia.id_frecuencia}">
                ${frecuencia.descripcion}
            </option>`;
        });
        
        div.innerHTML = `
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
                    <select name="medicamentos[${index}][medicamento]" class="w-full border rounded-md p-2" required>
                        ${medicamentosOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-red-600">Dosis *</label>
                    <input type="text" name="medicamentos[${index}][dosis]" class="w-full border rounded-md p-2" 
                           placeholder="Ej: 500mg, 1 tableta" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-red-600">Frecuencia *</label>
                    <select name="medicamentos[${index}][frecuencia]" class="w-full border rounded-md p-2" required>
                        ${frecuenciasOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-red-600">Duración *</label>
                    <select name="medicamentos[${index}][duracion]" class="w-full border rounded-md p-2" required>
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
                         class="w-full border rounded-md p-2" 
                         placeholder="Ej: Tomar con abundante agua, evitar alcohol, etc."></textarea>
            </div>
        `;
        return div;
    }
    
    // Función para remover medicamento
    window.removerMedicamento = function(button) {
        const medicamentoItem = button.closest('.medicamento-item');
        medicamentoItem.remove();
        actualizarNumeracion();
        actualizarBotonesRemover();
    };
    
    // Función para actualizar numeración
    function actualizarNumeracion() {
        const items = document.querySelectorAll('.medicamento-item');
        items.forEach((item, index) => {
            const titulo = item.querySelector('h4');
            titulo.textContent = `Medicamento ${index + 1}`;
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

    // ...existing code...
    
    // Función para el botón Ausente
    const btnAusente = document.getElementById('btn-ausente');
    if (btnAusente) {
        btnAusente.addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: "¿Realmente quieres marcar la cita como ausente?",
                text: "Esta acción cambiará el estado de la cita",
                icon: "question",
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: "Confirmar",
                denyButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario
                    document.getElementById('form-ausente').submit();
                }
            });
        });
    }
    
    // Función para el botón Finalizar Consulta
    const btnFinalizar = document.getElementById('btn-finalizar');
    if (btnFinalizar) {
        btnFinalizar.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Verificar campos obligatorios
            const tieneApuntes = {{ $apuntesActual ? 'true' : 'false' }};
            const tieneDiagnostico = {{ isset($diagnosticoActual) && $diagnosticoActual ? 'true' : 'false' }};
            const tieneIndicaciones = {{ isset($indicacionesActual) && $indicacionesActual ? 'true' : 'false' }};
            
            const faltantes = [];
            if (!tieneApuntes) faltantes.push('Apuntes');
            if (!tieneDiagnostico) faltantes.push('Diagnóstico');
            if (!tieneIndicaciones) faltantes.push('Indicaciones');
            
            if (faltantes.length > 0) {
                Swal.fire({
                    title: "Faltan campos obligatorios",
                    text: `Debe completar: ${faltantes.join(', ')}`,
                    icon: "warning",
                    confirmButtonText: "Entendido"
                });
                return;
            }
            
            Swal.fire({
                title: "¿Finalizar la consulta?",
                text: "Esto marcará la cita como atendida",
                icon: "question",
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: "Finalizar",
                denyButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario
                    document.getElementById('form-finalizar').submit();
                }
            });
        });
    }
});
</script>
