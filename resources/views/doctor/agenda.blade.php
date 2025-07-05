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
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                <!-- Selector de Vista -->
                <div class="flex rounded-lg border border-gray-300 bg-white">
                    <button type="button" 
                            class="px-4 py-2 text-sm font-medium rounded-l-lg {{ request('vista', 'dia') === 'dia' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                            onclick="cambiarVista('dia')">
                        Día
                    </button>
                    <button type="button" 
                            class="px-4 py-2 text-sm font-medium border-x {{ request('vista', 'dia') === 'semana' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                            onclick="cambiarVista('semana')">
                        Semana
                    </button>
                    <button type="button" 
                            class="px-4 py-2 text-sm font-medium rounded-r-lg {{ request('vista', 'dia') === 'mes' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                            onclick="cambiarVista('mes')">
                        Mes
                    </button>
                </div>

                <!-- Selector de Fecha -->
                <form method="GET" action="{{ route('doctor.agenda') }}" id="fechaForm">
                    <input type="hidden" name="vista" value="{{ request('vista', 'dia') }}" id="vistaInput">
                    <input type="date" name="fecha" value="{{ $fecha }}" 
                           class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-lg px-4 py-2 bg-white"
                           onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>

    <!-- Navegación de fechas -->
    <div class="p-6 border-b flex items-center justify-between">
        @php
            $vista = request('vista', 'dia');
            $fechaActual = \Carbon\Carbon::parse($fecha);
            
            if ($vista === 'dia') {
                $fechaAnterior = $fechaActual->copy()->subDay()->format('Y-m-d');
                $fechaSiguiente = $fechaActual->copy()->addDay()->format('Y-m-d');
                $textoFecha = $fechaActual->format('d/m/Y');
            } elseif ($vista === 'semana') {
                $inicioSemana = $fechaActual->copy()->startOfWeek();
                $finSemana = $fechaActual->copy()->endOfWeek();
                $fechaAnterior = $inicioSemana->copy()->subWeek()->format('Y-m-d');
                $fechaSiguiente = $inicioSemana->copy()->addWeek()->format('Y-m-d');
                $textoFecha = $inicioSemana->format('d/m') . ' - ' . $finSemana->format('d/m/Y');
            } else { // mes
                $inicioMes = $fechaActual->copy()->startOfMonth();
                $fechaAnterior = $inicioMes->copy()->subMonth()->format('Y-m-d');
                $fechaSiguiente = $inicioMes->copy()->addMonth()->format('Y-m-d');
                
                // Meses en español
                $mesesEspanol = [
                    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                ];
                $mesNumero = $fechaActual->month;
                $año = $fechaActual->year;
                $textoFecha = ucfirst($mesesEspanol[$mesNumero]) . ' ' . $año;
            }
        @endphp
        
        <button type="button" class="px-3 py-2 bg-white rounded-md hover:bg-gray-300 border" 
                onclick="window.location.href='{{ route('doctor.agenda', ['fecha' => $fechaAnterior, 'vista' => $vista]) }}'">
            &lt; Anterior
        </button>
        <p class="text-lg font-semibold text-center">{{ $textoFecha }}</p>
        <button type="button" class="px-3 py-2 bg-white rounded-md hover:bg-gray-300 border" 
                onclick="window.location.href='{{ route('doctor.agenda', ['fecha' => $fechaSiguiente, 'vista' => $vista]) }}'">
            Siguiente &gt;
        </button>
    </div>
    <!-- Contenido de la agenda -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            @php $vista = request('vista', 'dia'); @endphp
            
            @if($vista === 'dia')
                <!-- Vista por día -->
                <h2 class="text-xl font-bold text-gray-800 mb-4">Horario de Consulta - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h2>
                @php
                    $horarios = [];
                    
                    // Turno mañana: 7:00 AM - 12:00 PM (cada 30 minutos)
                    $horaInicio = new DateTime('07:00');
                    $horaFin = new DateTime('12:00');
                    while ($horaInicio < $horaFin) {
                        $horarios[] = $horaInicio->format('H:i');
                        $horaInicio->add(new DateInterval('PT30M'));
                    }
                    
                    // Turno tarde: 2:00 PM - 7:00 PM (cada 30 minutos) 
                    $horaInicio = new DateTime('14:00');
                    $horaFin = new DateTime('19:00');
                    while ($horaInicio < $horaFin) {
                        $horarios[] = $horaInicio->format('H:i');
                        $horaInicio->add(new DateInterval('PT30M'));
                    }
                @endphp
                
                <div class="space-y-1">
                    @foreach($horarios as $hora)
                        @php
                            $citasHora = $citas->filter(function($cita) use ($hora) {
                                return substr($cita['hora'], 0, 5) === $hora;
                            });
                        @endphp
                        
                        <div class="grid grid-cols-[120px_1fr] gap-4 py-3 border-b last:border-0">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="font-medium">
                                    @php
                                        $horaFormateada = date('g:i A', strtotime($hora));
                                        $esMañana = $hora < '12:00';
                                    @endphp
                                    <span class="{{ $esMañana ? 'text-blue-700' : 'text-orange-700' }}">{{ $horaFormateada }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                @if($citasHora->count() > 0)
                                    @foreach($citasHora as $cita)
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-3 rounded-md bg-yellow-50 border border-yellow-200">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium">{{ $cita['paciente']['nombre'] }} {{ $cita['paciente']['apellidos'] }}</span>
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $cita['estado'] === 'agendada' ? 'bg-green-100 text-green-800' : ($cita['estado'] === 'completada' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ ucfirst($cita['estado']) }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500">{{ $cita['motivo'] }}</p>
                                            </div>
                                            <a href="/doctor/citas/{{ $cita['id'] }}" 
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

            @elseif($vista === 'semana')
                <!-- Vista por semana -->
                @php
                    $fechaActual = \Carbon\Carbon::parse($fecha);
                    $inicioSemana = $fechaActual->copy()->startOfWeek();
                    $diasSemana = [];
                    for ($i = 0; $i < 7; $i++) {
                        $diasSemana[] = $inicioSemana->copy()->addDays($i);
                    }
                @endphp
                
                <h2 class="text-xl font-bold text-gray-800 mb-4">Vista Semanal</h2>
                
                <div class="grid grid-cols-8 gap-2 mb-4">
                    <div class="font-semibold text-gray-600 text-center py-2">Hora</div>
                    @foreach($diasSemana as $dia)
                        @php
                            $nombresDias = [
                                'Monday' => 'Lun',
                                'Tuesday' => 'Mar', 
                                'Wednesday' => 'Mié',
                                'Thursday' => 'Jue',
                                'Friday' => 'Vie',
                                'Saturday' => 'Sáb',
                                'Sunday' => 'Dom'
                            ];
                            $diaEnEspanol = $nombresDias[$dia->format('l')] ?? $dia->format('D');
                        @endphp
                        <div class="font-semibold text-gray-600 text-center py-2 {{ $dia->isToday() ? 'bg-green-100 rounded' : '' }}">
                            <div>{{ $diaEnEspanol }}</div>
                            <div class="text-sm">{{ $dia->format('d/m') }}</div>
                        </div>
                    @endforeach
                </div>
                
                @php 
                    // Horarios reales del consultorio
                    $horariosMañana = [];
                    $horariosTarde = [];
                    
                    // Turno mañana: 7:00 AM - 12:00 PM (cada 30 minutos)
                    $horaInicio = new DateTime('07:00');
                    $horaFin = new DateTime('12:00');
                    while ($horaInicio < $horaFin) {
                        $horariosMañana[] = $horaInicio->format('H:i');
                        $horaInicio->add(new DateInterval('PT30M'));
                    }
                    
                    // Turno tarde: 2:00 PM - 7:00 PM (cada 30 minutos) 
                    $horaInicio = new DateTime('14:00');
                    $horaFin = new DateTime('19:00');
                    while ($horaInicio < $horaFin) {
                        $horariosTarde[] = $horaInicio->format('H:i');
                        $horaInicio->add(new DateInterval('PT30M'));
                    }
                    
                    $todosHorarios = array_merge($horariosMañana, $horariosTarde);
                @endphp
                
                @foreach($todosHorarios as $hora)
                    <div class="grid grid-cols-8 gap-2 border-b py-2">
                        <div class="text-sm font-medium text-gray-600 text-center py-1">
                            @php
                                $horaFormateada = date('g:i A', strtotime($hora));
                                $esMañana = $hora < '12:00';
                            @endphp
                            <span class="{{ $esMañana ? 'text-blue-700' : 'text-orange-700' }}">{{ $horaFormateada }}</span>
                        </div>
                        @foreach($diasSemana as $dia)
                            @php
                                $citasDia = $citas->filter(function($cita) use ($dia, $hora) {
                                    return $cita['fecha'] === $dia->format('Y-m-d') && substr($cita['hora'], 0, 5) === $hora;
                                });
                            @endphp
                            <div class="min-h-[80px] border rounded p-1 {{ $citasDia->count() > 0 ? 'bg-yellow-50 cursor-pointer hover:bg-yellow-100' : '' }}" 
                                 @if($citasDia->count() > 0) onclick="@if($citasDia->count() == 1) mostrarModalCita({{ json_encode($citasDia->first()) }}) @else mostrarModalHorario('{{ $hora }}', '{{ $dia->format('Y-m-d') }}', {{ json_encode($citasDia->toArray()) }}) @endif" @endif>
                                @if($citasDia->count() > 0)
                                    @foreach($citasDia as $cita)
                                        <div class="text-xs p-2 rounded mb-1 flex items-center">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2 flex-shrink-0"></div>
                                            <div class="font-bold text-gray-900 truncate">{{ $cita['paciente']['nombre'] }}</div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach

            @else
                <!-- Vista por mes -->
                @php
                    $fechaActual = \Carbon\Carbon::parse($fecha);
                    $inicioMes = $fechaActual->copy()->startOfMonth()->startOfWeek();
                    $finMes = $fechaActual->copy()->endOfMonth()->endOfWeek();
                    $diasMes = [];
                    $fechaTemp = $inicioMes->copy();
                    while ($fechaTemp <= $finMes) {
                        $diasMes[] = $fechaTemp->copy();
                        $fechaTemp->addDay();
                    }
                    
                    // Meses en español para el título
                    $mesesEspanol = [
                        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                    ];
                    $mesNumero = $fechaActual->month;
                    $año = $fechaActual->year;
                    $tituloMes = ucfirst($mesesEspanol[$mesNumero]) . ' ' . $año;
                @endphp
                
                <h2 class="text-xl font-bold text-gray-800 mb-4">Vista Mensual - {{ $tituloMes }}</h2>
                
                <!-- Encabezados de días -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dia)
                        <div class="font-semibold text-gray-600 text-center py-2">{{ $dia }}</div>
                    @endforeach
                </div>
                
                <!-- Calendario -->
                <div class="grid grid-cols-7 gap-2">
                    @foreach($diasMes as $dia)
                        @php
                            $citasDia = $citas->filter(function($cita) use ($dia) {
                                return $cita['fecha'] === $dia->format('Y-m-d');
                            });
                            $esMesActual = $dia->month === $fechaActual->month;
                        @endphp
                        <div class="border rounded-lg p-2 min-h-[120px] {{ $esMesActual ? 'bg-white' : 'bg-gray-50' }} {{ $dia->isToday() ? 'ring-2 ring-green-500' : '' }} {{ $esMesActual && $citasDia->count() > 0 ? 'bg-yellow-50 cursor-pointer hover:bg-yellow-100' : '' }}" 
                             @if($esMesActual && $citasDia->count() > 0) onclick="mostrarModalDia('{{ $dia->format('Y-m-d') }}', {{ json_encode($citasDia->values()->toArray()) }})" @endif>
                            <div class="font-medium {{ $esMesActual ? 'text-gray-900' : 'text-gray-400' }} mb-2">
                                {{ $dia->format('d') }}
                            </div>
                            @if($esMesActual && $citasDia->count() > 0)
                                @foreach($citasDia->take(3) as $cita)
                                    <div class="text-xs p-1 rounded mb-1 flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 flex-shrink-0"></div>
                                        <div class="font-bold text-gray-900 truncate">{{ $cita['paciente']['nombre'] }}</div>
                                    </div>
                                @endforeach
                                @if($citasDia->count() > 3)
                                    <div class="text-xs text-gray-500 text-center mt-1">
                                        +{{ $citasDia->count() - 3 }} más
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para mostrar detalles de cita -->
    <div id="modalCita" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Detalles de la Cita</h3>
                    <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="contenidoModal" class="space-y-3">
                    <!-- Contenido dinámico -->
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button onclick="cerrarModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cerrar
                    </button>
                    <a id="btnVerDetalle" href="#" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Ver Detalle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar múltiples citas del día -->
    <div id="modalDia" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Citas del Día</h3>
                    <button onclick="cerrarModalDia()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="contenidoModalDia" class="space-y-2 max-h-96 overflow-y-auto">
                    <!-- Contenido dinámico -->
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="cerrarModalDia()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cambiarVista(vista) {
            document.getElementById('vistaInput').value = vista;
            document.getElementById('fechaForm').submit();
        }

        function mostrarModalCita(cita) {
            const modal = document.getElementById('modalCita');
            const contenido = document.getElementById('contenidoModal');
            const btnVerDetalle = document.getElementById('btnVerDetalle');
            
            // Estado con colores
            let estadoClass = '';
            if (cita.estado === 'agendada') {
                estadoClass = 'bg-green-100 text-green-800';
            } else if (cita.estado === 'completada') {
                estadoClass = 'bg-blue-100 text-blue-800';
            } else {
                estadoClass = 'bg-red-100 text-red-800';
            }
            
            contenido.innerHTML = `
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paciente:</label>
                        <div class="text-lg font-semibold text-gray-900">${cita.paciente.nombre} ${cita.paciente.apellidos}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora:</label>
                        <div class="text-gray-900">${cita.hora}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo:</label>
                        <div class="text-gray-900">${cita.motivo}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                        <span class="inline-block px-3 py-1 text-sm rounded-full ${estadoClass}">
                            ${cita.estado.charAt(0).toUpperCase() + cita.estado.slice(1)}
                        </span>
                    </div>
                </div>
            `;
            
            btnVerDetalle.href = `/doctor/citas/${cita.id}`;
            modal.classList.remove('hidden');
        }

        function mostrarModalHorario(hora, fecha, citas) {
            if (citas.length === 0) return; // No mostrar modal si no hay citas
            
            const modal = document.getElementById('modalDia');
            const contenido = document.getElementById('contenidoModalDia');
            
            // Formatear la hora
            const horaFormateada = new Date('1970-01-01T' + hora + ':00').toLocaleTimeString('es-ES', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            // Formatear la fecha
            const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Cambiar el título del modal
            modal.querySelector('h3').textContent = `Citas - ${fechaFormateada} a las ${horaFormateada}`;
            
            let citasHtml = '';
            citas.forEach(cita => {
                let estadoClass = '';
                if (cita.estado === 'agendada') {
                    estadoClass = 'bg-green-100 text-green-800';
                } else if (cita.estado === 'completada') {
                    estadoClass = 'bg-blue-100 text-blue-800';
                } else {
                    estadoClass = 'bg-red-100 text-red-800';
                }
                
                citasHtml += `
                    <div class="border rounded-lg p-3 bg-gray-50 hover:bg-gray-100 cursor-pointer" 
                         onclick="mostrarModalCita(${JSON.stringify(cita).replace(/"/g, '&quot;')})">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-gray-900">${cita.paciente.nombre} ${cita.paciente.apellidos}</div>
                                <div class="text-sm text-gray-600">${cita.motivo}</div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full ${estadoClass}">
                                ${cita.estado.charAt(0).toUpperCase() + cita.estado.slice(1)}
                            </span>
                        </div>
                    </div>
                `;
            });
            
            contenido.innerHTML = citasHtml;
            modal.classList.remove('hidden');
        }

        function mostrarModalDia(fecha, citas) {
            
            const modal = document.getElementById('modalDia');
            const contenido = document.getElementById('contenidoModalDia');
            
            // Formatear la fecha
            const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Cambiar el título del modal
            modal.querySelector('h3').textContent = `Citas del ${fechaFormateada}`;
            
            let citasHtml = '';
            citas.forEach(cita => {
                let estadoClass = '';
                if (cita.estado === 'agendada') {
                    estadoClass = 'bg-green-100 text-green-800';
                } else if (cita.estado === 'completada') {
                    estadoClass = 'bg-blue-100 text-blue-800';
                } else {
                    estadoClass = 'bg-red-100 text-red-800';
                }
                
                citasHtml += `
                    <div class="border rounded-lg p-3 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-gray-900">${cita.hora} - ${cita.paciente.nombre} ${cita.paciente.apellidos}</div>
                                <div class="text-sm text-gray-600">${cita.motivo}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full ${estadoClass}">
                                    ${cita.estado.charAt(0).toUpperCase() + cita.estado.slice(1)}
                                </span>
                                <a href="/doctor/citas/${cita.id}" class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                    Ver detalle
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            contenido.innerHTML = citasHtml;
            modal.classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('modalCita').classList.add('hidden');
        }

        function cerrarModalDia() {
            document.getElementById('modalDia').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modalCita = document.getElementById('modalCita');
            const modalDia = document.getElementById('modalDia');
            if (event.target === modalCita) {
                cerrarModal();
            }
            if (event.target === modalDia) {
                cerrarModalDia();
            }
        }
    </script>
</div>
@endsection
