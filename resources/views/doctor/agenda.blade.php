@extends('layouts.app')

@section('title', 'Agenda Médica')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Agenda Médica</h1>
                <p class="text-sm text-gray-600">Gestione sus citas y horarios</p>
            </div>
            <form method="GET" action="{{ route('doctor.agenda') }}">
                <input type="date" name="fecha" value="{{ $fecha }}" 
                       class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-lg px-4 py-2 bg-white"
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="p-6 border-b flex items-center justify-between">
        <button type="button" class="px-3 py-2 bg-white rounded-md hover:bg-gray-300" 
                onclick="window.location.href='{{ route('doctor.agenda', ['fecha' => \Carbon\Carbon::parse($fecha)->subDay()->format('Y-m-d')]) }}'">
            &lt; Anterior
        </button>
        <p class="text-lg font-semibold text-center">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        <button type="button" class="px-3 py-2 bg-white rounded-md hover:bg-gray-300" 
                onclick="window.location.href='{{ route('doctor.agenda', ['fecha' => \Carbon\Carbon::parse($fecha)->addDay()->format('Y-m-d')]) }}'">
            Siguiente &gt;
        </button>
    </div>
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Horario de Consulta</h2>
            @php
                $horarios = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
            @endphp
            
            <div class="space-y-1">
                @foreach($horarios as $hora)
                    @php
                        $citasHora = $citas->filter(function($cita) use ($hora) {
                            return substr($cita['hora'], 0, 5) === $hora;
                        });
                    @endphp
                    
                    <div class="grid grid-cols-[80px_1fr] gap-4 py-3 border-b last:border-0">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ $hora }}</span>
                        </div>
                        <div class="space-y-2">
                            @if($citasHora->count() > 0)
                                @foreach($citasHora as $cita)
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-3 rounded-md bg-gray-50">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</span>
                                                <span class="px-2 py-1 text-xs rounded-full {{ $cita['estado'] === 'agendada' ? 'bg-green-100 text-green-800' : ($cita['estado'] === 'completada' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($cita['estado']) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500">{{ $cita['motivo'] }}</p>
                                        </div>
                                        <a href="{{ route('doctor.citas.detalle', $cita['id']) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                            Ver detalle
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-3 rounded-md border border-dashed text-center text-gray-500">
                                    Disponible
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
