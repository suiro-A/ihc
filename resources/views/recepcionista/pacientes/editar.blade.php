@extends('layouts.app')

@section('title', 'Editar Paciente')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="#" onclick="window.history.back(); return false;" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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

    <form action="{{ route('recepcionista.pacientes.actualizar', $paciente->id_paciente) }}" method="POST">
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
                            <input type="text" name="nombres" id="nombres" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                                   value="{{ old('nombres', $paciente->nombres) }}"
                                   placeholder="Ingrese Nombres">
                        </div>
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                                   value="{{ old('apellidos', $paciente->apellidos) }}"
                                   placeholder="Ingrese Apellidos">
                        </div>
                    </div>
                    <!-- Segunda fila: DNI y Fecha de Nacimiento -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700">DNI/Documento</label>
                            <input type="text" name="dni" id="dni" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                                   value="{{ old('dni', $paciente->dni) }}"
                                   placeholder="Ingrese DNI o documento">
                        </div>
                        <div>
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                                   value="{{ old('fecha_nacimiento', $paciente->fecha_nac ? $paciente->fecha_nac->format('Y-m-d') : '') }}"
                                   placeholder="Ingrese Fecha de Nacimiento">
                        </div>
                    </div>
                    <!-- Tercera fila: Género -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="genero" class="block text-sm font-medium text-gray-700">Género</label>
                            <select name="genero" id="genero" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2">
                                <option value="">Seleccionar</option>
                                <option value="masculino" {{ old('genero', $paciente->sexo ? 'masculino' : 'femenino') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="femenino" {{ old('genero', $paciente->sexo ? 'masculino' : 'femenino') == 'femenino' ? 'selected' : '' }}>Femenino</option>
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
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                                   value="{{ old('telefono', $paciente->telefono) }}"
                                   placeholder="Ingrese Teléfono">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="email"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                                   value="{{ old('email', $paciente->correo) }}"
                                   placeholder="Ingrese Correo Electrónico">
                        </div>
                    </div>
                </div>

                <!-- Información Médica -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Información Médica</h3>
                    <p class="text-gray-600 mb-4">Datos médicos relevantes</p>
                    <div class="space-y-4">
                        {{-- Alergias --}}
                        <div>
                            <label for="alergias" class="block text-sm font-medium text-gray-700">Alergias</label>
                            <select name="alergias[]" id="alergias" class="mi-select text-sm text-left align-middle" multiple style="width: 100%">
                                @foreach($alergias as $alergia)
                                    <option value="{{ $alergia->id_alergia }}"
                                        {{ in_array($alergia->id_alergia, $alergiasSeleccionadas ?? []) ? 'selected' : '' }}>
                                        {{ $alergia->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Enfermedades Crónicas --}}
                        <div>
                            <label for="cronicas" class="block text-sm font-medium text-gray-700">Enfermedades Crónicas</label>
                            <select name="cronicas[]" id="cronicas" class="mi-select text-sm text-left align-middle" multiple style="width: 100%">
                                @foreach($cronicas as $cronica)
                                    <option value="{{ $cronica->id_enfermedad }}"
                                        {{ in_array($cronica->id_enfermedad, $cronicasSeleccionadas ?? []) ? 'selected' : '' }}>
                                        {{ $cronica->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Medicamentos --}}
                        <div>
                            <label for="medicamentos" class="block text-sm font-medium text-gray-700">Medicamentos</label>
                            <select name="medicamentos[]" id="medicamentos" class="mi-select text-sm text-left align-middle" multiple style="width: 100%">
                                @foreach($medicamentos as $medicamento)
                                    <option value="{{ $medicamento->id_medicamento }}"
                                        {{ in_array($medicamento->id_medicamento, $medicamentosSeleccionados ?? []) ? 'selected' : '' }}>
                                        {{ $medicamento->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                      placeholder="Información adicional relevante">{{ old('observaciones', $paciente['observaciones'] ?? '') }}</textarea>
                        </div> -->
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-between items-center pt-6">
                    <a href="{{ route('recepcionista.pacientes.buscar') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-base text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-green-700">
                            <img src="{{ asset('icons/paciente_editar.png') }}" alt="Actualizar Paciente" class="w-8 h-8 mr-3">
                            Actualizar Paciente
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
