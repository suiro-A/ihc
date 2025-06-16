@extends('layouts.app')

<!-- @section('title', 'Agendar Cita') -->

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('recepcionista.citas.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Agendar Cita</h1>
            <p class="text-gray-600">Complete el formulario para agendar una nueva cita</p>
        </div>
    </div>

    <form action="{{ route('recepcionista.citas.guardar') }}" method="POST">
        @csrf
        <div class="grid gap-6 md:grid-cols-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Información del Paciente</h3>
                    <p class="text-gray-600">Seleccione el paciente para la cita</p>
                </div>
                <div class="p-6 space-y-4">
                    @if($paciente)
                        <div class="p-4 border rounded-md bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium">{{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
                                    <p class="text-sm text-gray-500">{{ $paciente->dni }}</p>
                                </div>
                                <a href="{{ route('recepcionista.citas.agendar') }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800">Cambiar</a>
                            </div>
                        </div>
                        <input type="hidden" name="paciente_id" value="{{ $paciente->id_paciente }}">
                    @else
                        <div>
                            <label for="buscar_paciente" class="block text-sm font-medium text-gray-700">Buscar Paciente</label>
                            <input type="text" id="buscar_paciente" placeholder="Nombre o DNI"
                                   class="mt-1 mb-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <input type="hidden" name="paciente_id" id="paciente_id">
                            <ul id="lista_pacientes" class="border rounded-md bg-white shadow max-h-48 overflow-y-auto hidden">
                                @foreach($pacientes as $pac)
                                    <li class="px-4 py-2 cursor-pointer hover:bg-green-100"
                                        data-id="{{ $pac->id_paciente }}">
                                        {{ $pac->nombres }} {{ $pac->apellidos }} - {{ $pac->dni }}
                                    </li>
                                @endforeach
                            </ul>
                            <div id="paciente_seleccionado" class="mt-2 text-green-700 font-semibold"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500">¿No encuentra al paciente?</p>
                            <a href="{{ route('recepcionista.pacientes.registrar') }}" 
                               class="inline-flex items-center px-3 py-1 border border-gray-300 rounded text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Registrar Nuevo
                            </a>
                        </div>
                    @endif

                    <div>
                        <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo de la Consulta</label>
                        <textarea name="motivo" id="motivo" rows="4" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                  placeholder="Describa el motivo de la consulta"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Información de la Cita</h3>
                    <p class="text-gray-600">Seleccione doctor, fecha y hora</p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="buscar_doctor" class="block text-sm font-medium text-gray-700">Buscar Doctor</label>
                        <input type="text" id="buscar_doctor" placeholder="Nombre o Especialidad"
                               class="mt-1 mb-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <input type="hidden" name="doctor_id" id="doctor_id">
                        <ul id="lista_doctores" class="border rounded-md bg-white shadow max-h-48 overflow-y-auto hidden">
                            @foreach($doctores as $medico)
                                <li class="px-4 py-2 cursor-pointer hover:bg-green-100"
                                    data-id="{{ $medico->id_usuario }}">
                                    {{ $medico->usuario->nombres }} {{ $medico->usuario->apellidos }} - {{ $medico->especialidadNombre->nombre ?? 'Sin especialidad' }}
                                </li>
                            @endforeach
                        </ul>
                        <div id="doctor_seleccionado" class="mt-2 text-green-700 font-semibold"></div>
                    </div>

                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" name="fecha" id="fecha" required min="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label for="hora" class="block text-sm font-medium text-gray-700">Hora</label>
                        <select name="hora" id="hora" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Seleccione una hora</option>
                            <option value="09:00">09:00</option>
                            <option value="09:30">09:30</option>
                            <option value="10:00">10:00</option>
                            <option value="10:30">10:30</option>
                            <option value="11:00">11:00</option>
                            <option value="11:30">11:30</option>
                            <option value="12:00">12:00</option>
                            <option value="12:30">12:30</option>
                            <option value="15:00">15:00</option>
                            <option value="15:30">15:30</option>
                            <option value="16:00">16:00</option>
                            <option value="16:30">16:30</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('recepcionista.citas.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                Agendar Cita
            </button>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('buscar_paciente');
    const lista = document.getElementById('lista_pacientes');
    const items = Array.from(lista.querySelectorAll('li'));
    const hiddenInput = document.getElementById('paciente_id');
    const seleccionado = document.getElementById('paciente_seleccionado');

    input.addEventListener('input', function() {
        const filtro = input.value.toLowerCase();
        let hayResultados = false;
        items.forEach(function(item) {
            if (item.textContent.toLowerCase().includes(filtro) && filtro.length > 0) {
                item.style.display = '';
                hayResultados = true;
            } else {
                item.style.display = 'none';
            }
        });
        lista.classList.toggle('hidden', !hayResultados);
    });

    items.forEach(function(item) {
        item.addEventListener('click', function() {
            hiddenInput.value = item.dataset.id;
            seleccionado.textContent = "Paciente seleccionado: " + item.textContent;
            lista.classList.add('hidden');
            input.value = '';
        });
    });

    // Opcional: ocultar lista si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!lista.contains(e.target) && e.target !== input) {
            lista.classList.add('hidden');
        }
    });

    // Doctor search tipo lista
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
        });
    });

    // Ocultar lista si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!listaDoctor.contains(e.target) && e.target !== inputDoctor) {
            listaDoctor.classList.add('hidden');
        }
    });

});
</script>
@endsection
