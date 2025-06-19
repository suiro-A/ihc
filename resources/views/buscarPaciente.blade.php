{{-- <h1>BUSCAR</h1> --}}

@extends('layouts.app')


<!-- @section('title', 'Buscar Pacientes') -->
@section('scripts')
    @vite(['resources/js/paciente.js']) 
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.bootstrap5.css">
<!-- Botones CSS -->
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

{{-- Ajax --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css">
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Buscar Paciente</h1>
        <p class="text-gray-600">Busque pacientes por nombre o DNI</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold">Pacientes Registrados</h3>
                <p class="text-gray-600">Busque y gestione los pacientes de la clínica</p>
            </div>
            {{-- Botón Registrar Paciente eliminado --}}
        </div>
        <div class="p-6">
            {{-- Formulario de búsqueda --}}

            {{-- {{ route('recepcionista.pacientes.buscar') }} --}}
            {{-- <form method="GET" action="#" class="mb-4"> --}}
                {{-- <div class="relative">
                    <input
                        type="text"
                        id="buscar"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        class="w-full pl-10 pr-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Buscar por nombre o DNI...">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                        </svg>
                    </span>
                </div> --}}
            {{-- </form> --}}
            <div class="overflow-x-auto">

                <div class="card">
                    <div class="card-body">


                        <table class="min-w-full divide-y divide-gray-200 display" id="example">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellidos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Nacimiento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sexo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            {{-- <tbody class="bg-white divide-y divide-gray-200" id="pacientes-body"> --}}
                            {{-- <tbody class="bg-white divide-y divide-gray-200">

                                @foreach ($pacientes as $paciente)
                                    <tr>
                                        
                                        
                                            
                                        
                                        <td style="text-align: center;">{{$paciente->id_paciente}}</td>
                                        <td style="text-align: center;">{{$paciente->nombres}}</td>
                                        <td style="text-align: center;">{{$paciente->apellidos}}</td>
                                        <td style="text-align: center;">{{$paciente->dni}}</td>
                                        <td style="text-align: center;">{{$paciente->fecha_nac}}</td>
                                        <td style="text-align: center;">{{$paciente->sexo}}</td>
                                        <td style="text-align: center;">{{$paciente->telefono}}</td>
                                        <td style="text-align: center;">{{$paciente->correo}}</td>
                                        <td style="text-align: center;">
                                            <a style="color: blue" href="{{route('paciente.edit',["id"=>$paciente->id_paciente])}}">editar</a>
                                            
                                            <button class="text-red-600 hover:text-red-900" onclick="eliminarPaciente({{$paciente->id_paciente}})">Eliminar</button>
                                            <a style="color: red" href="{{route('paciente.destroy',["id"=>$paciente->id_paciente])}}">Eliminar</a>
                                        </td>


                                    </tr>

                                @endforeach

                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection

@section('js')

<script>
    new DataTable('#example', {
        responsive: true,
        // processing: true,
        serverSide: true,
dom: 'frtipB', // Botones al inicio
    buttons: [ 'excel', 'pdf', 'print'],
        ajax: '{{ route("prueba.ajax") }}',
        columns: [
            { data: 'id_paciente' },
            { data: 'nombres' },
            { data: 'apellidos' },
            { data: 'dni' },
            { data: 'fecha_nac' },
            { data: 'sexo' },
            { data: 'telefono' },
            { data: 'correo' },
            {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `
                
                
                    <a href="/paciente/${row.id_paciente}/edit" class="btn btn-sm btn-warning">Editar</a>
                    <button class="btn btn-sm btn-danger" onclick="eliminarPaciente(${row.id_paciente})">Eliminar</button>
                `;
            }
        }
        ],
        language: {
            // url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
             "aria": {
        "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
        }
    }
        }
    });
</script>
@endsection




