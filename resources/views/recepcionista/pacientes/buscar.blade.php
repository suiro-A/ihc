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
        <div class="p-6">
            {{-- Formulario de búsqueda --}}
            <form method="GET" action="{{ route('recepcionista.pacientes.buscar') }}" class="mb-4">
                <div class="relative">
                    <input
                        type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        class="w-full pl-10 pr-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Buscar por nombre o DNI...">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                        </svg>
                    </span>
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">DNI</th>
                            <th class="px-4 py-2 text-left">Edad</th>
                            <th class="px-4 py-2 text-left">Teléfono</th>
                            <th class="px-4 py-2 text-left">Correo</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody id="pacientes-tbody">
                        @foreach($allPacientes as $paciente)
                            <tr>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->nombres }} {{ $paciente->apellidos }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->dni }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->edad ?? '-' }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->telefono }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->correo }}</td>
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
            <div id="paginacion-info"></div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.querySelector('input[name="buscar"]');
    const tbody = document.getElementById('pacientes-tbody');
    const filas = Array.from(tbody.querySelectorAll('tr'));
    const filasPorPagina = 5;
    let paginaActual = 1;

    function filtrarYPaginar() {
        let filtro = buscador.value.toLowerCase();
        let filasFiltradas = filas.filter(fila => {
            let nombre = fila.cells[0]?.innerText.toLowerCase() || '';
            let dni = fila.cells[1]?.innerText.toLowerCase() || '';
            return nombre.includes(filtro) || dni.includes(filtro);
        });

        filas.forEach(fila => fila.style.display = 'none');
        let inicio = (paginaActual - 1) * filasPorPagina;
        let fin = inicio + filasPorPagina;
        filasFiltradas.slice(inicio, fin).forEach(fila => fila.style.display = '');

        actualizarPaginacion(filasFiltradas.length);
    }

    function actualizarPaginacion(totalFiltradas) {
        let totalPaginas = Math.ceil(totalFiltradas / filasPorPagina);
        let paginacionInfo = document.getElementById('paginacion-info');
        if (!paginacionInfo) return;

        let inicio = totalFiltradas === 0 ? 0 : ((paginaActual - 1) * filasPorPagina) + 1;
        let fin = Math.min(paginaActual * filasPorPagina, totalFiltradas);

        let texto = `<div>Mostrando ${inicio} a ${fin} de ${totalFiltradas} registros</div>`;

        let botones = '';
        if (totalPaginas > 1) {
            botones += `<button class="mx-1 px-2 py-1 rounded ${paginaActual === 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-gray-200'}" 
                onclick="cambiarPagina(${paginaActual - 1})" ${paginaActual === 1 ? 'disabled' : ''}>Anterior</button>`;
            // botones += `<span class="mx-2">Página ${paginaActual} de ${totalPaginas}</span>`;
            botones += `<button class="mx-1 px-2 py-1 rounded ${paginaActual === totalPaginas ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-gray-200'}" 
                onclick="cambiarPagina(${paginaActual + 1})" ${paginaActual === totalPaginas ? 'disabled' : ''}>Siguiente</button>`;
        }

        paginacionInfo.className = 'flex items-center justify-between mt-4 text-sm text-gray-500';
        paginacionInfo.innerHTML = `<div>${texto}</div><div>${botones}</div>`;
    }

    // Nueva función global para los botones
    window.cambiarPagina = function(num) {
        paginaActual = num;
        filtrarYPaginar();
    }

    buscador.addEventListener('keyup', function() {
        paginaActual = 1;
        filtrarYPaginar();
    });

    // Inicializa mostrando la primera página
    filtrarYPaginar();
});
</script>