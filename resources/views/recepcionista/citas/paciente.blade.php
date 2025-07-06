@extends('layouts.app')

@section('title', 'Historial de Citas - ' . $paciente['nombre'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('recepcionista.pacientes.buscar') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Historial de Citas</h1>
            </div>
            <p class="text-gray-600">
                Paciente: {{ $paciente['nombre'] }} {{ $paciente['apellidos'] }} | DNI: {{ $paciente['dni'] }}
            </p>
        </div>
        <a href="{{ route('recepcionista.citas.agendar', ['paciente' => $paciente['id']]) }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Agendar Nueva Cita
        </a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Citas del Paciente</h3>
            <p class="text-gray-600">Historial completo de citas</p>
        </div>
        <div class="p-6">
            <!-- Buscador -->
            <form method="GET" class="mb-4">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                    placeholder="Buscar por motivo..."
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-200">
            </form>
            <!-- Tabla de citas -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($citas as $cita)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($cita['fecha'])->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $cita['hora'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $cita['doctor']['name'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $cita['doctor']['especialidad'] ?? 'Medicina General' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $cita['motivo'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $estado = $cita['estado'];
                                        $clase = $estado === 'Agendada' ? 'bg-green-100 text-green-800' : ($estado === 'Atendida' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800');
                                        $texto = $estado === 'Agendada' ? 'Agendada' : ($estado === 'Atendida' ? 'Atendida' : 'Ausente');
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $clase }}">
                                        {{ $texto }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if($cita['estado'] === 'Agendada')
                                        <a href="{{ route('recepcionista.citas.editar', $cita['id']) }}" class="inline-flex items-center text-gray-600 hover:text-yellow-600" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">No hay citas registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación y resumen -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4">
                <div class="text-sm text-gray-500">
                    Mostrando {{ method_exists($citas, 'firstItem') ? $citas->firstItem() : 1 }} 
                    a {{ method_exists($citas, 'lastItem') ? $citas->lastItem() : count($citas) }} 
                    de {{ method_exists($citas, 'total') ? $citas->total() : count($citas) }} registros
                </div>
                <div>
                    @if(method_exists($citas, 'links'))
                        {{ $citas->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
