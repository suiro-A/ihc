@extends('layouts.app')

<!-- @section('title', 'Registrar Paciente') -->

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
            <h1 class="text-3xl font-bold text-gray-900">Registrar Paciente</h1>
            <p class="text-gray-600">Complete el formulario para registrar un nuevo paciente</p>
        </div>
    </div>

    <form action="{{ route('recepcionista.pacientes.guardar') }}" method="POST">
        @csrf
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 space-y-8">
                <!-- Información Personal -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Información Personal</h3>
                    <p class="text-gray-600 mb-4">Ingrese los datos personales del paciente</p>
                    <!-- Primera fila: Nombre y Apellidos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres</label>
                            <input type="text" name="nombres" id="nombres" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Nombres">
                        </div>
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Apellidos">
                        </div>
                    </div>
                    <!-- Segunda fila: DNI y Fecha de Nacimiento -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700">DNI/Documento</label>
                            <input type="text" name="dni" id="dni" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="ingrese el DNI o documento">
                        </div>
                        <div>
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    <!-- Tercera fila: Edad y Género -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="genero" class="block text-sm font-medium text-gray-700">Género</label>
                            <select name="genero" id="genero" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccionar</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
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
                                   placeholder="123456789">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="email"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                            <label for="alergia" class="block text-sm font-medium text-gray-700">Alergia</label>
                            <select name="alergia" id="alergia" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccionar alergia...</option>
                                @foreach($alergias as $alergia)
                                    <option value="{{ $alergia->id_alergia }}">{{ $alergia->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="cronica" class="block text-sm font-medium text-gray-700">Enfermedad Crónica</label>
                            <select name="cronica" id="cronica" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccionar enfermedad crónica...</option>
                                @foreach($cronicas as $cronica)
                                    <option value="{{ $cronica->id_enfermedad }}">{{ $cronica->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="medicamento" class="block text-sm font-medium text-gray-700">Medicamento</label>
                            <select name="medicamento" id="medicamento" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccionar medicamento...</option>
                                @foreach($medicamentos as $medicamento)
                                    <option value="{{ $medicamento->id_medicamento }}">{{ $medicamento->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                      placeholder="Información adicional relevante"></textarea>
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
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Registrar Paciente
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
