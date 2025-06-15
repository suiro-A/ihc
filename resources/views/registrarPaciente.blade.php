 {{-- Asegúrate de tener un layout base --}}
 {{-- @extends('layouts.app') --}}

{{-- @section('content') --}}

<head>

        <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        @vite(['resources/js/app.js']) {{-- JS global --}}
        @yield('scripts')              {{-- Scripts específicos de la vista --}}
</head>


<div class="container mt-5">
    <h3 class="text-center mb-4">Registrar Usuario</h3>
{{-- {{ route('usuarios.store') }} --}}
    <form action="{{ route('registrarPaciente') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" name="nombres" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" name="apellidos" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="dni" class="form-label">DNI</label>
                <input type="text" name="dni" class="form-control" maxlength="8" required>
            </div>

            <div class="col-md-4">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nac" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label for="sexo" class="form-label">Sexo</label>
                <select name="sexo" class="form-select" required>
                    <option value="">Seleccionar</option>
                    <option value="1">Masculino</option>
                    <option value="0">Femenino</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" maxlength="9" required>
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Registrar</button>
            <a href="{{ route('inicio') }}" class="btn btn-secondary">Cancelar</a>
            {{-- {{ route('usuarios.index') }} --}}
        </div>
    </form>
</div>
{{-- @endsection --}}
