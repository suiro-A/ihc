@extends('layouts.app')

<!-- @section('title', 'Buscar Pacientes') -->

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

        {{-- ! Inicio de tabla de busqueda --}}

     
        <div class="p-0">
            <div class="overflow-x-auto">
                <div class="card">
                    <div class="card-body">
                            <table id="example" class="min-w-full divide-y divide-gray-200 display">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allPacientes as $paciente)
                                        <tr>
                                            <td class="px-6 py-4 text-center text-sm text-gray-700">{{$paciente->nombres}}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-700">{{$paciente->dni}}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-700">{{$paciente->edad}}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-700">{{$paciente->telefono}}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-700">{{$paciente->correo}}</td>
                                            <td class="px-4 py-2 text-center align-middle space-x-1">
                                                <a href="{{ route('recepcionista.pacientes.editar', $paciente->id_paciente) }}" 
                                                        class="inline-flex items-center px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                                        <img src="{{ asset('icons/paciente_editar.png') }}" alt="Ícono de editar paciente" class="w-8 h-8 inline-block mr-4">
                                                        Editar
                                                </a>
                                            </td>
                                        </tr>
                                        
                                    @endforeach

                                </tbody>
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
    responsive:true,        
     autoWidth: false,
    language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
 
        },
        
        columnDefs: [
            {
                targets: -1,      // Última columna (el botón)
                orderable: false, // Desactiva ordenamiento
                searchable: false // (opcional) evita que entre en el buscador
            }
        ]
        ,
    dom: '<"row mb-3"<"col-9"f><"col-3"l>>t<"row mt-6"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>'
    });

    document.addEventListener('DOMContentLoaded', function () {

    
        const searchInput = document.querySelector('#dt-search-0');
        if (searchInput) {
            searchInput.placeholder = 'Buscar por Nombre, Dni o Correo...';
            // searchInput.style.border = '2px solid #4CAF50';
        }
  
});

// document.addEventListener('DOMContentLoaded', function() {
//     const buscador = document.querySelector('input[name="buscar"]');
//     const tbody = document.getElementById('pacientes-tbody');
//     const filas = Array.from(tbody.querySelectorAll('tr'));
//     const filasPorPagina = 5;
//     let paginaActual = 1;

//     function filtrarYPaginar() {
//         let filtro = buscador.value.toLowerCase();
//         let filasFiltradas = filas.filter(fila => {
//             let nombre = fila.cells[0]?.innerText.toLowerCase() || '';
//             let dni = fila.cells[1]?.innerText.toLowerCase() || '';
//             return nombre.includes(filtro) || dni.includes(filtro);
//         });

//         filas.forEach(fila => fila.style.display = 'none');
//         let inicio = (paginaActual - 1) * filasPorPagina;
//         let fin = inicio + filasPorPagina;
//         filasFiltradas.slice(inicio, fin).forEach(fila => fila.style.display = '');

//         actualizarPaginacion(filasFiltradas.length);
//     }

//     function actualizarPaginacion(totalFiltradas) {
//         let totalPaginas = Math.ceil(totalFiltradas / filasPorPagina);
//         let paginacionInfo = document.getElementById('paginacion-info');
//         if (!paginacionInfo) return;

//         let inicio = totalFiltradas === 0 ? 0 : ((paginaActual - 1) * filasPorPagina) + 1;
//         let fin = Math.min(paginaActual * filasPorPagina, totalFiltradas);

//         let texto = `<div>Mostrando ${inicio} a ${fin} de ${totalFiltradas} registros</div>`;

//         let botones = '';
//         if (totalPaginas > 1) {
//             botones += `<button class="mx-1 px-2 py-1 rounded ${paginaActual === 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-gray-200'}" 
//                 onclick="cambiarPagina(${paginaActual - 1})" ${paginaActual === 1 ? 'disabled' : ''}>Anterior</button>`;
//             // botones += `<span class="mx-2">Página ${paginaActual} de ${totalPaginas}</span>`;
//             botones += `<button class="mx-1 px-2 py-1 rounded ${paginaActual === totalPaginas ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-gray-200'}" 
//                 onclick="cambiarPagina(${paginaActual + 1})" ${paginaActual === totalPaginas ? 'disabled' : ''}>Siguiente</button>`;
//         }

//         paginacionInfo.className = 'flex items-center justify-between mt-4 text-sm text-gray-500';
//         paginacionInfo.innerHTML = `<div>${texto}</div><div>${botones}</div>`;
//     }

//     // Nueva función global para los botones
//     window.cambiarPagina = function(num) {
//         paginaActual = num;
//         filtrarYPaginar();
//     }

//     buscador.addEventListener('keyup', function() {
//         paginaActual = 1;
//         filtrarYPaginar();
//     });

//     // Inicializa mostrando la primera página
//     filtrarYPaginar();
// });



</script>

@endsection