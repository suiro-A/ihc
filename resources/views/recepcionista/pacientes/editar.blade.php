
@extends('layouts.app')

@section('title', 'Editar Paciente')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('recepcionista.pacientes.buscar') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Paciente</h1>
            <p class="text-gray-600">Modifique la información del paciente</p>
        </div>
    </div>

    <form action="{{ route('recepcionista.pacientes.actualizar', $paciente['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 space-y-8">
                <!-- Información Personal -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Información Personal</h3>
                    <p class="text-gray-600 mb-4">Actualice los datos personales del paciente</p>
                    <!-- Primera fila: Nombre y Apellidos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombres</label>
                            <input type="text" name="nombre" id="nombre" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('nombre', $paciente['nombre']) }}"
                                   placeholder="Nombres">
                        </div>
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('apellidos', $paciente['apellidos']) }}"
                                   placeholder="Apellidos">
                        </div>
                    </div>
                    <!-- Segunda fila: DNI y Fecha de Nacimiento -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700">DNI/Documento</label>
                            <input type="text" name="dni" id="dni" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('dni', $paciente['dni']) }}"
                                   placeholder="ingrese el DNI o documento">
                        </div>
                        <div>
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('fecha_nacimiento', $paciente['fecha_nacimiento']) }}">
                        </div>
                    </div>
                    <!-- Tercera fila: Género -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="genero" class="block text-sm font-medium text-gray-700">Género</label>
                            <select name="genero" id="genero" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccionar</option>
                                <option value="masculino" {{ old('genero', $paciente['genero']) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="femenino" {{ old('genero', $paciente['genero']) == 'femenino' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Información de Contacto</h3>
                    <p class="text-gray-600 mb-4">Datos de contacto del paciente</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('telefono', $paciente['telefono']) }}"
                                   placeholder="123456789">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="email"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('email', $paciente['email']) }}"
                                   placeholder="ejemplo@correo.com">
                        </div>
                    </div>
                </div>

                <!-- Información Médica -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Información Médica</h3>
                    <p class="text-gray-600 mb-4">Datos médicos relevantes</p>
                    <div class="space-y-4">
                        <div>
                            <label for="alergias" class="block text-sm font-medium text-gray-700">Alergias</label>
                            <input type="text" name="alergias" id="alergias"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('alergias', $paciente['alergias'] ?? '') }}"
                                   placeholder="Medicamentos, alimentos, etc.">
                        </div>
                        <div>
                            <label for="enfermedades_cronicas" class="block text-sm font-medium text-gray-700">Enfermedades Crónicas</label>
                            <input type="text" name="enfermedades_cronicas" id="enfermedades_cronicas"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('enfermedades_cronicas', $paciente['enfermedades_cronicas'] ?? '') }}"
                                   placeholder="Diabetes, hipertensión, etc.">
                        </div>
                        <div>
                            <label for="medicacion_actual" class="block text-sm font-medium text-gray-700">Medicación Actual</label>
                            <input type="text" name="medicacion_actual" id="medicacion_actual"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   value="{{ old('medicacion_actual', $paciente['medicacion_actual'] ?? '') }}"
                                   placeholder="Medicamentos que toma actualmente">
                        </div>
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                      placeholder="Información adicional relevante">{{ old('observaciones', $paciente['observaciones'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-between items-center pt-6">
                    <a href="{{ route('recepcionista.pacientes.buscar') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Actualizar Paciente
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
