@extends('layouts.app')

<!-- @section('title', 'Crear Usuario') -->

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Crear Usuario</h1>
            <p class="text-gray-600">Complete el formulario para crear un nuevo usuario</p>
        </div>
    </div>

    <form action="{{ route('admin.usuarios.guardar') }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 space-y-8">
                <!-- Información Personal -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Información Personal</h3>
                    <p class="text-gray-600 mb-4">Ingrese los datos personales del usuario</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Nombre">
                        </div>
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Apellidos">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="ejemplo@clinica.com">
                        </div>
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="123456789">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                            <input type="text" name="dni" id="dni" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="DNI">
                        </div>
                        <div>
                            <label for="edad" class="block text-sm font-medium text-gray-700">Edad</label>
                            <input type="number" name="edad" id="edad" min="0" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="Edad">
                        </div>
                    </div>
                </div>

                <!-- Información de Cuenta -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Información de Cuenta</h3>
                    <p class="text-gray-600 mb-4">Configure los datos de acceso al sistema</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="email" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                placeholder="ejemplo@clinica.com">
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select name="role" id="role" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccionar rol</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol['name'] }}">{{ $rol['display_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input type="password" name="password" id="password" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Información Profesional SOLO para doctor -->
                <div id="info-profesional" class="hidden">
                    <h3 class="text-lg font-semibold mb-2">Información Profesional</h3>
                    <p class="text-gray-600 mb-4">Datos profesionales del usuario</p>
                    <div>
                        <label for="especialidad" class="block text-sm font-medium text-gray-700">Especialidad</label>
                        <select name="especialidad" id="especialidad"
                                class="mt-1 mb-4 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Seleccionar especialidad</option>
                            <option value="Medicina General">Medicina General</option>
                            <option value="Cardiología">Cardiología</option>
                            <option value="Pediatría">Pediatría</option>
                            <option value="Dermatología">Dermatología</option>
                            <option value="Traumatología">Traumatología</option>
                            <option value="Ginecología">Ginecología</option>
                            <option value="Neurología">Neurología</option>
                        </select>
                    </div>
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="2"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                  placeholder="Información adicional relevante"></textarea>
                    </div>
                </div>

                <!-- Botones al final del bloque blanco, alineados a los extremos -->
                <div class="flex justify-between items-center pt-6">
                    <a href="{{ route('admin.usuarios.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Crear Usuario
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const infoProfesional = document.getElementById('info-profesional');
    if (this.value === 'doctor') {
        infoProfesional.classList.remove('hidden');
    } else {
        infoProfesional.classList.add('hidden');
    }
});
</script>
@endsection