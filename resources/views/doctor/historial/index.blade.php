@extends('layouts.app')

<!-- @section('title', 'Historial Clínico') -->

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Historial Clínico</h1>
        <p class="text-gray-600">Consulte el historial médico de los pacientes</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Pacientes</h3>
            <p class="text-gray-600">Seleccione un paciente para ver su historial clínico</p>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <input type="text" id="buscador-pacientes" placeholder="Buscar por nombre o DNI..." class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
            </div>
            <div class="overflow-x-auto">
                <table id="tabla-pacientes" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Cita</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pacientes as $paciente)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $paciente['nombre'] }} {{ $paciente['apellidos'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $paciente['dni'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \App\Services\DataService::getEdadPaciente($paciente['fecha_nacimiento']) }} años</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $paciente['telefono'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $paciente['ultima_cita'] ? $paciente['ultima_cita']['fecha'] : 'Sin citas' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('doctor.historial.paciente', $paciente['id']) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Ver Historial
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No hay pacientes con historial disponible
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('buscador-pacientes').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tabla-pacientes tbody tr');
        filas.forEach(function(fila) {
            let nombre = fila.cells[0].innerText.toLowerCase();
            let dni = fila.cells[1].innerText.toLowerCase();
            if (nombre.includes(filtro) || dni.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
</script>
@endsection
