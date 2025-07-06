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
                    <form method="POST" action="{{ route('doctor.citas.receta', $cita['id']) }}">
                        @csrf
                        <div class="space-y-4">
                            <div class="border rounded p-4">
                                <h4 class="font-medium mb-3">Medicamento 1</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Medicamento</label>
                                        <input type="text" name="medicamentos[0][nombres]" class="w-full border rounded-md p-2" placeholder="Paracetamol">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Dosis</label>
                                        <input type="text" name="medicamentos[0][dosis]" class="w-full border rounded-md p-2" placeholder="500mg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Frecuencia</label>
                                        <input type="text" name="medicamentos[0][frecuencia]" class="w-full border rounded-md p-2" placeholder="Cada 8 horas">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Duración</label>
                                        <input type="text" name="medicamentos[0][duracion]" class="w-full border rounded-md p-2" placeholder="7 días">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Guardar
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
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
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
