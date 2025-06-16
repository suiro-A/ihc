@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestionar Citas</h1>
            <p class="text-gray-600">Administre las citas médicas</p>
        </div>
        <a href="{{ route('recepcionista.citas.agendar') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
            <img src="{{ asset('icons/cita_me_agregar.png') }}" alt="Ícono de citas" class="w-8 h-8 inline-block mr-2">
            Agendar Nueva Cita
        </a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Citas Médicas</h3>
            <p class="text-gray-600">Visualice y gestione todas las citas</p>
        </div>
        <div class="p-6">
            {{-- Filtros de estado --}}
            <div class="inline-flex rounded-md shadow-sm mb-4 bg-gray-50 border">
                @php
                    $estadoActual = request('estado', 'todas');
                    $estados = [
                        'todas' => 'Todas',
                        'agendada' => 'Agendadas',
                        'completada' => 'Atendidas',
                        'cancelada' => 'Ausentes'
                    ];
                @endphp
                @foreach($estados as $key => $label)
                    <form method="GET" class="contents">
                        <input type="hidden" name="estado" value="{{ $key }}">
                        <input type="hidden" name="buscar" value="{{ request('buscar') }}">
                        <input type="hidden" name="fecha" value="{{ request('fecha') }}">
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium border-0 rounded-none focus:z-10
                            {{ $estadoActual == $key ? 'bg-white text-green-700 font-bold' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                            {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>
            {{-- Buscador --}}
            <form method="GET" class="mb-4 flex items-center gap-2">
                <input type="hidden" name="estado" value="{{ request('estado', 'todas') }}">
                <div class="relative w-full">
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                        class="w-full pl-10 pr-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Buscar por paciente...">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                        </svg>
                    </span>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Buscar</button>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($citas as $cita)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $cita['doctor']['name'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $cita['doctor']['especialidad'] ?? 'Medicina General' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($cita['fecha'])->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $cita['hora'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estado = $cita['estado'];
                                        $clase = $estado === 'agendada' ? 'bg-green-100 text-green-800' : ($estado === 'completada' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800');
                                        $texto = $estado === 'agendada' ? 'Agendada' : ($estado === 'completada' ? 'Atendida' : 'Ausente');
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $clase }}">
                                        {{ $texto }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($cita['estado'] === 'agendada')
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('recepcionista.citas.editar', $cita['id']) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700" title="Editar">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No hay citas registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-500">
                Mostrando {{ count($citas) }} {{ Str::plural('registro', count($citas)) }}
            </div>
        </div>
    </div>
</div>
@endsection
