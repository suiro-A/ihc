@extends('layouts.app')

@section('title', 'Definir Horarios')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Definir Horarios</h1>
        <p class="text-gray-600">Configure los turnos de atención de los médicos por mes</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Información de Turnos</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="p-4 border rounded-lg bg-blue-50">
                    <h4 class="font-semibold text-blue-900">Turno Mañana</h4>
                    <p class="text-blue-700">7:00 AM - 12:00 PM</p>
                    <p class="text-sm text-blue-600">10 citas de 30 minutos cada una</p>
                </div>
                <div class="p-4 border rounded-lg bg-orange-50">
                    <h4 class="font-semibold text-orange-900">Turno Tarde</h4>
                    <p class="text-orange-700">2:00 PM - 7:00 PM</p>
                    <p class="text-sm text-orange-600">10 citas de 30 minutos cada una</p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.disponibilidad.guardar') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                            <select name="doctor_id" id="doctor_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="">Seleccione un doctor</option>
                                @foreach($doctores as $doctor)
                                    <option value="{{ $doctor->id_usuario }}">
                                        {{ $doctor->nombres }} {{ $doctor->apellidos }} - {{ $doctor->medico->especialidadNombre->nombre ?? 'Medicina General' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="mes_anio" class="block text-sm font-medium text-gray-700">Mes y Año</label>
                            <input type="month" name="mes_anio" id="mes_anio" required 
                                   min="{{ date('Y-m') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Turnos Disponibles</label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="border rounded-lg p-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="turnos[]" value="manana" 
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">Turno Mañana</div>
                                        <div class="text-sm text-gray-500">7:00 AM - 12:00 PM</div>
                                        <div class="text-xs text-gray-400">Lunes a Viernes</div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="border rounded-lg p-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="turnos[]" value="tarde" 
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">Turno Tarde</div>
                                        <div class="text-sm text-gray-500">2:00 PM - 7:00 PM</div>
                                        <div class="text-xs text-gray-400">Lunes a Viernes</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="window.history.back()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Guardar Disponibilidad
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Disponibilidad Actual -->
    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Disponibilidad Configurada</h3>
            <p class="text-gray-600">Horarios actualmente definidos para los doctores</p>
        </div>
        <div class="p-6">
            @php
                $agrupadas = collect($disponibilidades)->groupBy(function($item) {
                    return $item->id_usuario . '-' . $item->anio . '-' . $item->mes;
                });
            @endphp

            <div class="space-y-4">
                @forelse($agrupadas as $grupo)
                    @php
                        $primero = $grupo->first();
                        $turnos = $grupo->pluck('turno')->unique();
                    @endphp
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium">
                                    {{ $primero->nombres }} {{ $primero->apellidos }}
                                    <span class="text-xs text-gray-400 ml-2">({{ $primero->especialidad }})</span>
                                </h4>
                            </div>
                            <div class="text-right flex flex-col items-end">
                                <div class="flex gap-2 mb-1">
                                    @foreach($turnos as $turno)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ strtolower($turno) == 'mañana' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-orange-100 text-orange-800 border border-orange-300' }}">
                                            {{ ucfirst($turno) }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500">
                                    @php
                                        \Carbon\Carbon::setLocale('es');
                                    @endphp
                                    {{ \Carbon\Carbon::create($primero->anio, $primero->mes)->translatedFormat('F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No hay disponibilidad configurada.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
