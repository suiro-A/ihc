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
                                    <p class="font-medium">{{ $paciente['nombre'] }} {{ $paciente['apellidos'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $paciente['dni'] }}</p>
                                </div>
                                <a href="{{ route('recepcionista.citas.agendar') }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800">Cambiar</a>
                            </div>
                        </div>
                        <input type="hidden" name="paciente_id" value="{{ $paciente['id'] }}">
                    @else
                        <div>
                            <label for="paciente_id" class="block text-sm font-medium text-gray-700">Seleccionar Paciente</label>
                            <select name="paciente_id" id="paciente_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccione un paciente</option>
                                @foreach($pacientes as $pac)
                                    <option value="{{ $pac['id'] }}">{{ $pac['nombre'] }} {{ $pac['apellidos'] }} - {{ $pac['dni'] }}</option>
                                @endforeach
                            </select>
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
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                        <select name="doctor_id" id="doctor_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Seleccione doctor</option>
                            @foreach($doctores as $doctor)
                                <option value="{{ $doctor['id'] }}">{{ $doctor['name'] }} - {{ $doctor['especialidad'] ?? 'Medicina General' }}</option>
                            @endforeach
                        </select>
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
@endsection
