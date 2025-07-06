
@extends('layouts.app')

@section('title', 'Editar Cita')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('recepcionista.citas.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Cita</h1>
            <p class="text-gray-600">Modifique los detalles de la cita</p>
        </div>
    </div>

    <form action="{{ route('recepcionista.citas.actualizar', $cita['id']) }}" method="POST" id="form-editar">
        @csrf
        @method('PUT')
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Información del Paciente -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Información del Paciente</h3>
                    <p class="text-gray-600">Paciente para esta cita</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="p-4 border rounded-md bg-gray-50">
                        <div class="font-medium">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</div>
                        <p class="text-sm text-gray-500">{{ $cita['paciente']['dni'] }}</p>
                    </div>
                    
                    <div>
                        <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo de la Consulta</label>
                        <textarea name="motivo" id="motivo" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            required>{{ old('motivo', $cita['motivo']) }}</textarea>
                        @error('motivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700">Estado de la Cita</label>
                        <select name="estado" id="estado"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            required>
                            <option value="Agendada" {{ old('estado', $cita['estado']) === 'Agendada' ? 'selected' : '' }}>Agendada</option>
                            <option value="Atendida" {{ old('estado', $cita['estado']) === 'Atendida' ? 'selected' : '' }}>Atendida</option>
                            <option value="Ausente" {{ old('estado', $cita['estado']) === 'Ausente' ? 'selected' : '' }}>Ausente</option>
                        </select>
                        @error('estado')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información de la Cita -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Información de la Cita</h3>
                    <p class="text-gray-600">Seleccione doctor y fecha</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="buscar_doctor" class="block text-sm font-medium text-gray-700">Buscar Doctor</label>
                        <input type="text" id="buscar_doctor" placeholder="Nombre o Especialidad"
                               class="mt-1 mb-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <input type="hidden" name="doctor_id" id="doctor_id" value="{{ old('doctor_id', $cita['doctor_id']) }}">
                        <ul id="lista_doctores" class="border rounded-md bg-white shadow max-h-48 overflow-y-auto hidden">
                            @foreach($doctores as $medico)
                                <li class="px-4 py-2 cursor-pointer hover:bg-green-100"
                                    data-id="{{ $medico['id'] }}">
                                    {{ $medico['name'] }} - {{ $medico['especialidad'] }}
                                </li>
                            @endforeach
                        </ul>
                        <div id="doctor_seleccionado" class="mt-2 text-green-700 font-semibold">
                            @php
                                $doctorSeleccionado = collect($doctores)->firstWhere('id', old('doctor_id', $cita['doctor_id']));
                            @endphp
                            @if($doctorSeleccionado)
                                Doctor seleccionado: {{ $doctorSeleccionado['name'] }} - {{ $doctorSeleccionado['especialidad'] }}
                            @endif
                        </div>
                        @error('doctor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" name="fecha" id="fecha" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                               value="{{ old('fecha', $cita['fecha']) }}">
                        @error('fecha')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Horarios Disponibles -->
            <div class="bg-white rounded-lg shadow flex flex-col">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Horarios Disponibles</h3>
                    <p class="text-gray-600">Seleccione el nuevo horario</p>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-center">
                    <div id="horarios-container" class="hidden">
                        <input type="hidden" name="hora" id="hora_seleccionada" value="{{ old('hora', $cita['hora']) }}">
                        <!-- Los turnos se generarán dinámicamente -->
                        <div id="turnos-dinamicos"></div>
                    </div>
                    <div id="mensaje-seleccionar" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Seleccione un doctor y fecha para ver los horarios disponibles</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('recepcionista.citas.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" id="btn-actualizar"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Actualizar Cita
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de disponibilidad y citas desde el servidor
    const disponibilidades = @json($disponibilidades ?? []);
    const citasExistentes = @json($citasExistentes ?? []);
    const citaActualId = {{ $cita['id'] }};

    const inputDoctor = document.getElementById('buscar_doctor');
    const listaDoctor = document.getElementById('lista_doctores');
    const itemsDoctor = Array.from(listaDoctor.querySelectorAll('li'));
    const hiddenDoctor = document.getElementById('doctor_id');
    const doctorSeleccionado = document.getElementById('doctor_seleccionado');

    inputDoctor.addEventListener('input', function() {
        const filtro = inputDoctor.value.toLowerCase();
        let hayResultados = false;
        itemsDoctor.forEach(function(item) {
            if (item.textContent.toLowerCase().includes(filtro) && filtro.length > 0) {
                item.style.display = '';
                hayResultados = true;
            } else {
                item.style.display = 'none';
            }
        });
        listaDoctor.classList.toggle('hidden', !hayResultados);
    });

    itemsDoctor.forEach(function(item) {
        item.addEventListener('click', function() {
            hiddenDoctor.value = item.dataset.id;
            doctorSeleccionado.textContent = "Doctor seleccionado: " + item.textContent;
            listaDoctor.classList.add('hidden');
            inputDoctor.value = '';
            actualizarHorarios();
        });
    });

    document.addEventListener('click', function(e) {
        if (!listaDoctor.contains(e.target) && e.target !== inputDoctor) {
            listaDoctor.classList.add('hidden');
        }
    });

    // Verificar disponibilidad cuando se selecciona doctor o fecha
    const doctorIdInput = document.getElementById('doctor_id');
    const fechaInput = document.getElementById('fecha');

    doctorIdInput.addEventListener('change', actualizarHorarios);
    fechaInput.addEventListener('change', actualizarHorarios);

    function actualizarHorarios() {
        const doctorId = doctorIdInput.value;
        const fecha = fechaInput.value;
        
        if (!doctorId || !fecha) {
            ocultarHorarios();
            return;
        }

        // Crear fecha correctamente para evitar problemas de zona horaria
        const [anio, mes, dia] = fecha.split('-');
        const fechaObj = new Date(anio, mes - 1, dia);
        const diaSemana = fechaObj.getDay();
        
        // Verificar si es fin de semana (Sábado = 6, Domingo = 0)
        if (diaSemana === 0 || diaSemana === 6) {
            mostrarMensaje('Los doctores no atienden los fines de semana');
            return;
        }

        // Verificar disponibilidad del doctor para ese mes/año
        const disponibilidadDoctor = disponibilidades[doctorId];
        
        if (!disponibilidadDoctor) {
            mostrarMensaje('El doctor no tiene disponibilidad configurada');
            return;
        }

        // Filtrar turnos disponibles para el mes/año
        const turnosDisponibles = disponibilidadDoctor.filter(disp => 
            disp.mes == parseInt(mes) && disp.anio == parseInt(anio)
        );

        if (turnosDisponibles.length === 0) {
            mostrarMensaje('El doctor no tiene disponibilidad para este mes');
            return;
        }

        // Obtener citas ocupadas para esta fecha (excluyendo la cita actual)
        const citasOcupadasFecha = citasExistentes[doctorId] && citasExistentes[doctorId][fecha] 
            ? citasExistentes[doctorId][fecha]
                .filter(cita => cita.id_cita !== citaActualId) // Excluir cita actual
                .map(cita => cita.hora_inicio)
            : [];

        mostrarHorariosDisponibles(turnosDisponibles, citasOcupadasFecha);
    }

    function mostrarHorariosDisponibles(turnos, citasOcupadas) {
        const horariosContainer = document.getElementById('horarios-container');
        const mensajeSeleccionar = document.getElementById('mensaje-seleccionar');
        const turnosDinamicos = document.getElementById('turnos-dinamicos');
        
        horariosContainer.classList.remove('hidden');
        mensajeSeleccionar.classList.add('hidden');
        turnosDinamicos.innerHTML = '';

        // Ordenar turnos: mañana primero, tarde después
        const turnosOrdenados = turnos.sort((a, b) => {
            const orden = { 'manana': 1, 'mañana': 1, 'tarde': 2 };
            return (orden[a.descripcion.toLowerCase()] || 3) - (orden[b.descripcion.toLowerCase()] || 3);
        });

        turnosOrdenados.forEach(turno => {
            const turnoDiv = document.createElement('div');
            turnoDiv.className = 'mb-6';
            
            const titulo = document.createElement('h4');
            const esTurnoManana = turno.descripcion.toLowerCase().includes('mañana') || turno.descripcion.toLowerCase().includes('manana');
            const colorClase = esTurnoManana ? 'text-blue-900' : 'text-orange-900';
            
            titulo.className = `font-medium ${colorClase} mb-3 flex items-center`;
            titulo.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                ${turno.descripcion} (${formatearHora(turno.hora_inicio)} - ${formatearHora(turno.hora_fin)})
            `;
            
            const horariosGrid = document.createElement('div');
            horariosGrid.className = 'grid grid-cols-2 gap-2';
            
            // Generar horarios para este turno
            generarHorariosTurno(horariosGrid, turno.hora_inicio, turno.hora_fin, citasOcupadas);
            
            turnoDiv.appendChild(titulo);
            turnoDiv.appendChild(horariosGrid);
            turnosDinamicos.appendChild(turnoDiv);
        });
    }

    function generarHorariosTurno(container, horaInicio, horaFin, citasOcupadas) {
        const inicio = new Date(`2000-01-01 ${horaInicio}`);
        const fin = new Date(`2000-01-01 ${horaFin}`);
        
        let horaActual = new Date(inicio);
        
        while (horaActual < fin) {
            const horaString = horaActual.toTimeString().substring(0, 5);
            
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'p-2 text-sm border rounded-md transition-colors';
            button.textContent = formatearHora(horaString);
            button.dataset.hora = horaString;
            
            // Verificar si es la hora actual de la cita
            const horaActualCita = document.getElementById('hora_seleccionada').value;
            
            if (citasOcupadas.includes(horaString)) {
                button.className += ' bg-red-100 text-red-800 border-red-200 cursor-not-allowed';
                button.disabled = true;
                button.innerHTML = formatearHora(horaString) + '<br><span class="text-xs">Ocupado</span>';
            } else {
                button.className += ' border-gray-300 hover:border-green-500 hover:bg-green-50 hover:text-green-700';
                button.addEventListener('click', () => seleccionarHora(horaString, button));
                
                // Marcar como seleccionado si es la hora actual
                if (horaString === horaActualCita) {
                    button.className = button.className.replace(/border-gray-300 hover:border-green-500 hover:bg-green-50 hover:text-green-700/, 'bg-green-500 text-white border-green-500');
                }
            }
            
            container.appendChild(button);
            
            // Avanzar 30 minutos
            horaActual.setMinutes(horaActual.getMinutes() + 30);
        }
    }

    function seleccionarHora(hora, button) {
        // Remover selección anterior
        document.querySelectorAll('[data-hora]').forEach(btn => {
            if (!btn.disabled) {
                btn.className = btn.className.replace(/bg-green-500 text-white border-green-500/, 'border-gray-300 hover:border-green-500 hover:bg-green-50 hover:text-green-700');
            }
        });
        
        // Seleccionar nuevo horario
        button.className = button.className.replace(/border-gray-300 hover:border-green-500 hover:bg-green-50 hover:text-green-700/, 'bg-green-500 text-white border-green-500');
        
        // Guardar hora seleccionada
        document.getElementById('hora_seleccionada').value = hora;
    }

    function ocultarHorarios() {
        const horariosContainer = document.getElementById('horarios-container');
        const mensajeSeleccionar = document.getElementById('mensaje-seleccionar');
        
        horariosContainer.classList.add('hidden');
        mensajeSeleccionar.classList.remove('hidden');
    }

    function mostrarMensaje(mensaje) {
        const mensajeSeleccionar = document.getElementById('mensaje-seleccionar');
        mensajeSeleccionar.innerHTML = `
            <svg class="w-12 h-12 mx-auto mb-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <p class="text-yellow-600">${mensaje}</p>
        `;
        ocultarHorarios();
    }

    function formatearHora(hora) {
        const [horas, minutos] = hora.split(':');
        const horaNum = parseInt(horas);
        const ampm = horaNum >= 12 ? 'PM' : 'AM';
        const hora12 = horaNum > 12 ? horaNum - 12 : (horaNum === 0 ? 12 : horaNum);
        return `${hora12}:${minutos} ${ampm}`;
    }

    // Cargar horarios iniciales si ya hay doctor y fecha seleccionados
    if (doctorIdInput.value && fechaInput.value) {
        actualizarHorarios();
    }
});
</script>
@endsection
