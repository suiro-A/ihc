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

    <form action="{{ route('recepcionista.citas.actualizar', $cita['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Información del Paciente -->
            <div class="bg-white rounded-lg shadow p-6 flex flex-col gap-4">
                <h3 class="text-xl font-semibold mb-2">Información del Paciente</h3>
                <p class="text-gray-500 mb-4">Paciente para esta cita</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="font-semibold text-gray-900 text-base">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</div>
                    <div class="text-gray-500 text-sm">{{ $cita['paciente']['dni'] }}</div>
                </div>
                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">Motivo de la Consulta</label>
                    <textarea name="motivo" id="motivo" rows="3"
                        class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        required>{{ old('motivo', $cita['motivo']) }}</textarea>
                    @error('motivo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado de la Cita</label>
                    <select name="estado" id="estado"
                        class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        required>
                        <option value="agendada" {{ old('estado', $cita['estado']) === 'agendada' ? 'selected' : '' }}>Agendada</option>
                        <option value="completada" {{ old('estado', $cita['estado']) === 'completada' ? 'selected' : '' }}>Atendida</option>
                        <option value="cancelada" {{ old('estado', $cita['estado']) === 'cancelada' ? 'selected' : '' }}>Ausente</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- Información de la Cita -->
            <div class="bg-white rounded-lg shadow p-6 flex flex-col gap-4">
                <h3 class="text-xl font-semibold mb-2">Información de la Cita</h3>
                <p class="text-gray-500 mb-4">Modifique doctor, fecha y hora</p>
                <div>
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                    <select name="doctor_id" id="doctor_id"
                        class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        required>
                        <option value="">Seleccione un doctor...</option>
                        @foreach($doctores as $doctor)
                            <option value="{{ $doctor['id'] }}" {{ old('doctor_id', $cita['doctor_id']) == $doctor['id'] ? 'selected' : '' }}>
                                {{ $doctor['name'] }} - {{ $doctor['especialidad'] ?? 'Medicina General' }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" name="fecha" id="fecha"
                        class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        value="{{ old('fecha', $cita['fecha']) }}" required>
                    @error('fecha')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="hora" class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                    <select name="hora" id="hora"
                        class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        required>
                        <option value="">Seleccione una hora...</option>
                        @for($hour = 8; $hour <= 17; $hour++)
                            @for($minute = 0; $minute < 60; $minute += 30)
                                @php
                                    $time = sprintf('%02d:%02d', $hour, $minute);
                                @endphp
                                <option value="{{ $time }}" {{ old('hora', $cita['hora']) === $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endfor
                        @endfor
                    </select>
                    @error('hora')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
            </div>
        </div>
        <!-- Botones de acción -->
        <div class="flex justify-end gap-4 mt-8">
            <a href="{{ route('recepcionista.citas.index') }}"
               class="px-6 py-2 rounded-md border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-2 rounded-md bg-green-600 text-white font-semibold hover:bg-green-700">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
