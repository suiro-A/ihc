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
                                    @if($paciente['ultima_cita'])
                                        <div class="text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($paciente['ultima_cita']['fecha'])->format('d/m/Y') }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Sin citas</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('doctor.historial.paciente', $paciente['id']) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                        <img src="{{ asset('icons/ver_historial.png') }}" alt="Ver Historial Completo" class="w-8 h-8 mr-2">
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
