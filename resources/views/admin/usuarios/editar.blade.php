@extends('layouts.app')

@section('content')
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
      <a href="{{ route('admin.usuarios.index') }}"
        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Volver
      </a>
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Editar Usuario</h1>
        <p class="text-gray-600">Modifique los datos del usuario</p>
      </div>
    </div>

    <form action="{{ route('admin.usuarios.actualizar', $usuario->id_usuario) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 space-y-8">
          <!-- Información Personal -->
          <div>
            <h3 class="text-lg font-semibold mb-2">Información Personal</h3>
            <p class="text-gray-600 mb-4">Ingrese los datos personales del usuario</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="nombres" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombres" id="nombres" required value="{{ old('nombres', $usuario->nombres) }}"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  placeholder="Nombre">
              </div>
              <div>
                <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" required value="{{ old('apellidos', $usuario->apellidos ?? '') }}"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  placeholder="Apellidos">
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
              <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" value="{{ old('telefono', $usuario->telefono ?? '') }}"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  placeholder="123456789">
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
                <input type="email" name="email" id="email" required value="{{ old('email', $usuario->correo) }}"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  placeholder="ejemplo@clinica.com">
              </div>
              <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="role" id="role" required disabled
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                  <option value="">Seleccionar rol</option>
                  @foreach ($roles as $rol)
                    <option value="{{ $rol->id_rol }}" {{ old('role', $usuario->rolNombre->rol) == $rol->rol ? 'selected' : '' }}>
                      {{ $rol->rol }}
                    </option>
                  @endforeach
                </select>
                <input type="hidden" name="role" value="{{ $usuario->rol }}">
              </div>
            </div>
            <!-- Puedes agregar campos de contraseña si quieres permitir cambiarla -->
          </div>

          <!-- Información Profesional SOLO para doctor -->
          <div id="info-profesional" class="{{ old('role', $usuario->rolNombre->rol) == 'doctor' ? '' : 'hidden' }}">
            <h3 class="text-lg font-semibold mb-2">Información Profesional</h3>
            <p class="text-gray-600 mb-4">Datos profesionales del usuario</p>
            <div>
              <label for="especialidad" class="block text-sm font-medium text-gray-700">Especialidad (solo para doctores)</label>
              <select name="especialidad" id="especialidad"
                class="mt-1 mb-4 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                <option value="">Seleccionar especialidad</option>
                @foreach ($especialidades as $especialidad)
                  <option value="{{ $especialidad->id_especialidad }}"
                    {{ old('especialidad', $usuario->medico?->especialidad ?? '') == $especialidad->id_especialidad ? 'selected' : '' }}>
                    {{ $especialidad->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="colegiatura" class="block text-sm font-medium text-gray-700">Número de colegiatura</label>
              <input type="text" name="colegiatura" id="colegiatura" maxlength="10"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                placeholder="Número de colegiatura" value="{{ old('colegiatura', $usuario->medico?->num_colegiatura) }}">
            </div>
          </div>

          <!-- Botones -->
          <div class="flex justify-between items-center pt-6">
            <a href="{{ route('admin.usuarios.index') }}"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
              Cancelar
            </a>
            <button type="submit"
              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
              Guardar Cambios
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const roleSelect = document.getElementById('role');
      const infoProfesional = document.getElementById('info-profesional');

      if (roleSelect.value === '1') {
        infoProfesional.classList.remove('hidden');
      }

      roleSelect.addEventListener('change', function() {
        if (this.value === '1') {
          infoProfesional.classList.remove('hidden');
        } else {
          infoProfesional.classList.add('hidden');
        }
      });
    });
  </script>
@endsection
