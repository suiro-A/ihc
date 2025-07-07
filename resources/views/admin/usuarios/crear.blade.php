@extends('layouts.app')

<!-- @section('title', 'Crear Usuario') -->

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
        <h1 class="text-3xl font-bold text-gray-900">Crear Usuario</h1>
        <p class="text-gray-600">Complete el formulario para crear un nuevo usuario</p>
      </div>
    </div>

    <form action="{{ route('admin.usuarios.guardar') }}" method="POST">
      @csrf

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
                <input type="text" name="nombres" id="nombres" required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="Nombre" oninput="validateNombres(this)">
                <div id="nombres-error" class="text-red-500 text-sm mt-1 hidden">Solo se permiten letras y espacios</div>
              </div>
              <div>
                <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="Apellidos" oninput="validateApellidos(this)">
                <div id="apellidos-error" class="text-red-500 text-sm mt-1 hidden">Solo se permiten letras y espacios</div>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
              {{-- <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                   placeholder="ejemplo@clinica.com">
                        </div> --}}
              <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="tel" name="telefono" id="telefono"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="123456789" oninput="validateTelefono(this)" maxlength="9">
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
                <input type="email" name="email" id="email" required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  placeholder="ejemplo@clinica.com" onblur="validateEmail(this)">
                <div id="email-error" class="text-red-500 text-sm mt-1 hidden"></div>
                <div id="email-loading" class="text-blue-500 text-sm mt-1 hidden">Verificando correo...</div>
              </div>
              <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="role" id="role" required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2">
                  <option value="">Seleccionar rol</option>
                  @foreach ($roles as $rol)
                    <option value="{{ $rol->id_rol }}">{{ $rol->rol }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" id="password" required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  oninput="validatePassword(this)">
                <div id="password-requirements" class="text-sm mt-1 hidden">
                  <div class="text-gray-600">La contraseña debe tener:</div>
                  <div id="req-length" class="text-red-500">• Mínimo 6 caracteres</div>
                  <div id="req-upper" class="text-red-500">• Al menos 1 mayúscula</div>
                  <div id="req-lower" class="text-red-500">• Al menos 1 minúscula</div>
                  <div id="req-number" class="text-red-500">• Al menos 1 número</div>
                </div>
              </div>
              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                  oninput="validatePasswordConfirmation(this)">
                <div id="password-confirmation-error" class="text-red-500 text-sm mt-1 hidden">Las contraseñas no coinciden</div>
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
                class="mt-1 mb-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2">
                <option value="">Seleccionar especialidad</option>
                @foreach ($especialidades as $especialidad)
                  <option value="{{ $especialidad->id_especialidad }}">{{ $especialidad->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="colegiatura" class="block text-sm font-medium text-gray-700">Número de colegiatura</label>
              <input type="text" name="colegiatura" id="colegiatura" maxlength="6"
                oninput="validateColegiatura(this)"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 px-2"
                placeholder="123456">
              <div id="colegiatura-error" class="text-red-500 text-sm mt-1 hidden">Solo se permiten 6 números</div>
            </div>
          </div>

          <!-- Botones al final del bloque blanco, alineados a los extremos -->
          <div class="flex justify-between items-center pt-6">
            <a href="{{ route('admin.usuarios.index') }}"
              class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50 min-w-[140px] h-10">
              Cancelar
            </a>
            <button type="submit"
              class="inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 min-w-[140px] h-10">
              <img src="{{ asset('icons/usuario_agregar.png') }}" alt="Crear" class="w-8 h-8 mr-2">
              Crear Usuario
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

    // Validación de email (verificar si existe en la base de datos)
    function validateEmail(input) {
      const errorDiv = document.getElementById('email-error');
      const loadingDiv = document.getElementById('email-loading');
      
      if (input.value.trim() === '') return;
      
      // Validar formato de email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(input.value)) {
        errorDiv.textContent = 'Formato de correo inválido';
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
        return;
      }
      
      // Verificar en la base de datos
      loadingDiv.classList.remove('hidden');
      errorDiv.classList.add('hidden');
      
      fetch('{{ route("admin.usuarios.verificar-email") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email: input.value })
      })
      .then(response => response.json())
      .then(data => {
        loadingDiv.classList.add('hidden');
        if (data.exists) {
          errorDiv.textContent = 'Este correo ya está registrado';
          errorDiv.classList.remove('hidden');
          input.classList.add('border-red-500');
        } else {
          errorDiv.classList.add('hidden');
          input.classList.remove('border-red-500');
        }
      })
      .catch(error => {
        loadingDiv.classList.add('hidden');
        console.error('Error:', error);
      });
    }

    // Validación de contraseña
    function validatePassword(input) {
      const password = input.value;
      const requirementsDiv = document.getElementById('password-requirements');
      const lengthReq = document.getElementById('req-length');
      const upperReq = document.getElementById('req-upper');
      const lowerReq = document.getElementById('req-lower');
      const numberReq = document.getElementById('req-number');
      
      // Mostrar/ocultar requisitos según si hay contenido
      if (password.length === 0) {
        requirementsDiv.classList.add('hidden');
        return;
      } else {
        requirementsDiv.classList.remove('hidden');
      }
      
      // Validar longitud mínima
      if (password.length >= 6) {
        lengthReq.classList.remove('text-red-500');
        lengthReq.classList.add('text-green-500');
      } else {
        lengthReq.classList.remove('text-green-500');
        lengthReq.classList.add('text-red-500');
      }
      
      // Validar mayúscula
      if (/[A-Z]/.test(password)) {
        upperReq.classList.remove('text-red-500');
        upperReq.classList.add('text-green-500');
      } else {
        upperReq.classList.remove('text-green-500');
        upperReq.classList.add('text-red-500');
      }
      
      // Validar minúscula
      if (/[a-z]/.test(password)) {
        lowerReq.classList.remove('text-red-500');
        lowerReq.classList.add('text-green-500');
      } else {
        lowerReq.classList.remove('text-green-500');
        lowerReq.classList.add('text-red-500');
      }
      
      // Validar número
      if (/[0-9]/.test(password)) {
        numberReq.classList.remove('text-red-500');
        numberReq.classList.add('text-green-500');
      } else {
        numberReq.classList.remove('text-green-500');
        numberReq.classList.add('text-red-500');
      }
      
      // Validar confirmación si existe valor
      const confirmInput = document.getElementById('password_confirmation');
      if (confirmInput.value) {
        validatePasswordConfirmation(confirmInput);
      }
    }

    // Validación de confirmación de contraseña
    function validatePasswordConfirmation(input) {
      const password = document.getElementById('password').value;
      const confirmPassword = input.value;
      const errorDiv = document.getElementById('password-confirmation-error');
      
      // Solo validar si ambos campos tienen contenido
      if (password.length === 0 || confirmPassword.length === 0) {
        errorDiv.classList.add('hidden');
        input.classList.remove('border-red-500');
        return;
      }
      
      if (confirmPassword !== password) {
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
      } else {
        errorDiv.classList.add('hidden');
        input.classList.remove('border-red-500');
      }
    }

    // Mostrar/ocultar información profesional según el rol
    document.getElementById('role').addEventListener('change', function() {
      const infoProfesional = document.getElementById('info-profesional');
      if (this.value === '1') {
        infoProfesional.classList.remove('hidden');
      } else {
        infoProfesional.classList.add('hidden');
      }
    });

    // Validación del formulario antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
      const requiredFields = ['nombres', 'apellidos', 'email', 'password', 'password_confirmation', 'role'];
      let hasErrors = false;
      
      requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
          hasErrors = true;
          input.classList.add('border-red-500');
        }
      });
      
      // Validar que las contraseñas coincidan
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('password_confirmation').value;
      if (password !== confirmPassword) {
        hasErrors = true;
        document.getElementById('password-confirmation-error').classList.remove('hidden');
      }
      
      // Validar fortaleza de contraseña
      if (password.length < 6 || !/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password)) {
        hasErrors = true;
        alert('La contraseña no cumple con los requisitos mínimos');
      }
      
      if (hasErrors) {
        e.preventDefault();
        alert('Por favor, corrija los errores en el formulario');
      }
    });
  </script>
@endsection
