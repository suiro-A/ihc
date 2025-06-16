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
                            <th class="px-4 py-2 text-left">Última cita</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pacientes as $paciente)
                            <tr>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->nombres }} {{ $paciente->apellidos }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->dni }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->edad ?? '-' }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->telefono }}</td>
                                <td class="px-4 py-2 text-left align-middle">{{ $paciente->correo }}</td>
                                <td class="px-4 py-2 text-left align-middle">
                                    {{ $paciente->ultima_cita ? $paciente->ultima_cita : 'Sin citas' }}
                                </td>
                                <td class="px-4 py-2 text-center align-middle space-x-1">
                                    <a href="{{ route('recepcionista.pacientes.editar', $paciente->id_paciente) }}" 
                                           class="inline-flex items-center px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-gray-500">No se encontraron pacientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-4 text-sm text-gray-500">
                @php
                $from = ($pacientes->currentPage() - 1) * $pacientes->perPage() + 1;
                $to = $from + $pacientes->count() - 1;
                @endphp
                    <div class="flex items-center space-x-2">
                        Mostrando {{ $from }} a {{ $to }} registros
                    </div>
                    {{ $pacientes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
