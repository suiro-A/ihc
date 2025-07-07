@extends('layouts.app')

@section('content')
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
      <a href="{{ route('admin.usuarios.index') }}"
        href="#"
        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        onclick="event.preventDefault(); window.history.back();">
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

      <!-- Campos ocultos para prevenir autocompletado no deseado -->
      <input type="text" name="fake_email" id="fake_email" style="display:none;" autocomplete="username" tabindex="-1">
      <input type="password" name="fake_password" autocomplete="new-password" style="display:none;" tabindex="-1">

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
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="Ingrese Nombre" oninput="validateNombres(this)">
                <div id="nombres-error" class="text-red-500 text-sm mt-1 hidden">Solo se permiten letras y espacios</div>
              </div>
              <div>
                <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" required value="{{ old('apellidos', $usuario->apellidos ?? '') }}"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="Ingrese Apellidos" oninput="validateApellidos(this)">
                <div id="apellidos-error" class="text-red-500 text-sm mt-1 hidden">Solo se permiten letras y espacios</div>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
              <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" value="{{ old('telefono', $usuario->telefono ?? '') }}"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="Ingrese Teléfono" oninput="validateTelefono(this)" maxlength="9">
                <div id="telefono-error" class="text-red-500 text-sm mt-1 hidden">Solo se permiten números (máximo 9 dígitos)</div>
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
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="Ingrese Correo Electrónico">
              </div>
              <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="role" id="role" required disabled
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2">
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
                class="mt-1 mb-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2">
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
              <input type="text" name="colegiatura" id="colegiatura" maxlength="6"
                oninput="validateColegiatura(this)"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                placeholder="Ingrese Número de Colegiatura" value="{{ old('colegiatura', $usuario->medico?->num_colegiatura) }}">
              <div id="colegiatura-error" class="text-red-500 text-sm mt-1 hidden">Solo se permiten 6 números</div>
            </div>
          </div>

          <!-- Botones -->
          <div class="flex justify-between items-center pt-6">
            <a href="{{ route('admin.usuarios.index') }}"
              class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50 min-w-[140px] h-10">
              Cancelar
            </a>
            <button type="submit"
              class="inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 min-w-[140px] h-10">
              <img src="{{ asset('icons/paciente_editar.png') }}" alt="Guardar" class="w-8 h-8 mr-2">
              Guardar Cambios
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <script>
    // Validación de nombres (solo letras y espacios)
    function validateNombres(input) {
      const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
      const errorDiv = document.getElementById('nombres-error');
      
      if (!regex.test(input.value)) {
        input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
      } else {
        errorDiv.classList.add('hidden');
        input.classList.remove('border-red-500');
      }
    }

    // Validación de apellidos (solo letras y espacios)
    function validateApellidos(input) {
      const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
      const errorDiv = document.getElementById('apellidos-error');
      
      if (!regex.test(input.value)) {
        input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
      } else {
        errorDiv.classList.add('hidden');
        input.classList.remove('border-red-500');
      }
    }

    // Validación de teléfono (solo números, máximo 9)
    function validateTelefono(input) {
      const regex = /^[0-9]*$/;
      const errorDiv = document.getElementById('telefono-error');
      
      if (!regex.test(input.value)) {
        input.value = input.value.replace(/[^0-9]/g, '');
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
      } else {
        errorDiv.classList.add('hidden');
        input.classList.remove('border-red-500');
      }
    }

    // Validación de colegiatura (solo 6 números)
    function validateColegiatura(input) {
      const regex = /^[0-9]*$/;
      const errorDiv = document.getElementById('colegiatura-error');
      
      if (!regex.test(input.value)) {
        input.value = input.value.replace(/[^0-9]/g, '');
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
      } else if (input.value.length > 6) {
        input.value = input.value.substring(0, 6);
        errorDiv.textContent = 'Máximo 6 números';
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
      } else {
        errorDiv.classList.add('hidden');
        input.classList.remove('border-red-500');
      }
    }

    // Mostrar/ocultar información profesional según el rol
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

    // Validación del formulario antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
      const requiredFields = ['nombres', 'apellidos', 'email'];
      let hasErrors = false;
      
      requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
          hasErrors = true;
          input.classList.add('border-red-500');
        }
      });
      
      if (hasErrors) {
        e.preventDefault();
        alert('Por favor, corrija los errores en el formulario');
      }
    });
  </script>
@endsection
