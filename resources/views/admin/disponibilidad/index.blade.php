@extends('layouts.app')

<!-- @section('title', 'Definir Horarios') -->

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Definir Horarios</h1>
        <p class="text-gray-600">Configure los horarios de atención de los médicos</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Seleccionar Doctor</h3>
            <p class="text-gray-600">Elija el doctor para configurar su disponibilidad</p>
        </div>
        <div class="p-6">
            <div>
                <label for="doctor" class="block text-sm font-medium text-gray-700">Doctor</label>
                <select id="doctor" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Seleccione un doctor</option>
                    @foreach($doctores as $doctor)
                        <option value="{{ $doctor['id'] }}">{{ $doctor['name'] }} - {{ $doctor['especialidad'] ?? 'Medicina General' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div id="horarios-section" class="mt-6 bg-white rounded-lg shadow hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Configurar Horarios</h3>
            <p class="text-gray-600">Defina los horarios de atención por fecha</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.disponibilidad.guardar') }}" method="POST">
                @csrf
                <input type="hidden" name="doctor_id" id="selected_doctor_id">
                
                <div class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                            <input type="date" id="fecha" min="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700">Hora de inicio</label>
                            <select id="hora_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="08:00">08:00</option>
                                <option value="09:00">09:00</option>
                                <option value="10:00">10:00</option>
                                <option value="11:00">11:00</option>
                                <option value="12:00">12:00</option>
                                <option value="13:00">13:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                                <option value="17:00">17:00</option>
                            </select>
                        </div>
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700">Hora de fin</label>
                            <select id="hora_fin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="12:00">12:00</option>
                                <option value="13:00">13:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                                <option value="17:00">17:00</option>
                                <option value="18:00">18:00</option>
                                <option value="19:00">19:00</option>
                                <option value="20:00">20:00</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" id="agregar-horario" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar Horario
                    </button>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Horarios configurados</label>
                        <div id="horarios-lista" class="space-y-2">
                            <p class="text-gray-500">No hay horarios configurados</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Guardar Horarios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let horariosConfigurados = [];

document.getElementById('doctor').addEventListener('change', function() {
    const horariosSection = document.getElementById('horarios-section');
    const selectedDoctorId = document.getElementById('selected_doctor_id');
    
    if (this.value) {
        horariosSection.classList.remove('hidden');
        selectedDoctorId.value = this.value;
    } else {
        horariosSection.classList.add('hidden');
    }
});

document.getElementById('agregar-horario').addEventListener('click', function() {
    const fecha = document.getElementById('fecha').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (!fecha) {
        alert('Seleccione una fecha para el horario.');
        return;
    }
    
    const horario = {
        fecha: fecha,
        hora_inicio: horaInicio,
        hora_fin: horaFin
    };
    
    horariosConfigurados.push(horario);
    actualizarListaHorarios();
    
    // Limpiar fecha
    document.getElementById('fecha').value = '';
});

function actualizarListaHorarios() {
    const lista = document.getElementById('horarios-lista');
    
    if (horariosConfigurados.length === 0) {
        lista.innerHTML = '<p class="text-gray-500">No hay horarios configurados</p>';
        return;
    }
    
    lista.innerHTML = horariosConfigurados.map((horario, index) => `
        <div class="flex justify-between items-center p-4 border rounded-md">
            <div>
                <p class="font-medium">${new Date(horario.fecha).toLocaleDateString('es-ES')}</p>
                <p class="text-sm text-gray-500">${horario.hora_inicio} - ${horario.hora_fin}</p>
            </div>
            <button type="button" onclick="eliminarHorario(${index})" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <input type="hidden" name="horarios[${index}][fecha]" value="${horario.fecha}">
            <input type="hidden" name="horarios[${index}][hora_inicio]" value="${horario.hora_inicio}">
            <input type="hidden" name="horarios[${index}][hora_fin]" value="${horario.hora_fin}">
        </div>
    `).join('');
}

function eliminarHorario(index) {
    horariosConfigurados.splice(index, 1);
    actualizarListaHorarios();
}
</script>
@endsection
