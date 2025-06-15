<!DOCTYPE html>
<html lang="es" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- <title>@yield('title', 'Sistema de Gestión Clínica')</title> -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen" x-data="{ open: true }">
    <!-- Sidebar fijo -->
    <aside 
        class="fixed top-0 left-0 bg-white border-r flex flex-col justify-between"
        :class="open ? 'w-64' : 'w-24'"
        style="height: 100vh; z-index: 40;"
    >
        <div>
            <div 
                class="flex items-center px-4 py-6 border-b"
                :class="open ? 'justify-between' : 'justify-center'"
            >
                <div class="flex items-center space-x-3" x-show="open">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-lg">
                        {{ strtoupper(substr(session('user_data')['name'] ?? 'U', 0, 1)) }}
                    </div>
                    <div class="transition-all duration-200">
                        <div class="font-semibold text-gray-900 whitespace-nowrap">{{ session('user_data')['name'] ?? 'Usuario' }}</div>
                        <div class="text-xs text-gray-500 capitalize">{{ session('user_data')['role'] ?? '' }}</div>
                    </div>
                </div>
                <div x-show="!open" class="flex items-center justify-center w-full">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-lg">
                        {{ strtoupper(substr(session('user_data')['name'] ?? 'U', 0, 1)) }}
                    </div>
                </div>
                <button 
                    @click="open = !open" 
                    class="p-2 rounded-full hover:bg-gray-100 transition ml-0"
                    aria-label="Toggle Sidebar"
                >
                    <span class="material-icons" x-text="open ? 'chevron_left' : 'chevron_right'"></span>
                </button>
            </div>
            <nav class="mt-6">
                <ul>
                    @php $role = session('user_data')['role'] ?? null; @endphp

                    @if($role === 'administrativo')
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('admin.dashboard') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >dashboard</span>
                                <span x-show="open" class="transition-all duration-200">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.usuarios.index') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('admin.usuarios.*') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >group</span>
                                <span x-show="open" class="transition-all duration-200">Gestión de Usuarios</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.disponibilidad.index') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('admin.disponibilidad.*') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >schedule</span>
                                <span x-show="open" class="transition-all duration-200">Definir Horarios</span>
                            </a>
                        </li>
                    @elseif($role === 'recepcionista')
                        <li>
                            <a href="{{ route('recepcionista.dashboard') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('recepcionista.dashboard') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >dashboard</span>
                                <span x-show="open" class="transition-all duration-200">Dashboard</span>
                            </a>
                        </li>

                        <!-- Paciente con submenú -->
                        <li x-data="{ pacienteOpen: false }">
                            <button type="button"
                                @click="pacienteOpen = !pacienteOpen"
                                class="flex items-center w-full py-3 text-gray-700 hover:bg-green-50 focus:outline-none
                                    {{ request()->routeIs('recepcionista.pacientes.*') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >people</span>
                                <span x-show="open" class="transition-all duration-200">Paciente</span>
                                <span class="material-icons ml-auto" x-show="open" x-text="pacienteOpen ? 'expand_less' : 'expand_more'"></span>
                            </button>
                            <ul x-show="pacienteOpen" x-transition class="pl-12" x-cloak>
                                <li>
                                    <a href="{{ route('recepcionista.pacientes.registrar') }}"
                                        class="flex items-center py-2 text-gray-700 hover:bg-green-50
                                            {{ request()->routeIs('recepcionista.pacientes.registrar') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                    >
                                        <span class="material-icons mr-2 text-base">person_add</span>
                                        <span>Registrar Paciente</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('recepcionista.pacientes.buscar') }}"
                                        class="flex items-center py-2 text-gray-700 hover:bg-green-50
                                            {{ request()->routeIs('recepcionista.pacientes.buscar') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                    >
                                        <span class="material-icons mr-2 text-base">search</span>
                                        <span>Buscar Paciente</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('recepcionista.citas.index') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('recepcionista.citas.index') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >event</span>
                                <span x-show="open" class="transition-all duration-200">Citas</span>
                            </a>
                        </li>
                    </ul>
                    @elseif($role === 'doctor')
                        <li>
                            <a href="{{ route('doctor.dashboard') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('doctor.dashboard') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >dashboard</span>
                                <span x-show="open" class="transition-all duration-200">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('doctor.agenda') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('doctor.agenda') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >schedule</span>
                                <span x-show="open" class="transition-all duration-200">Agenda</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('doctor.historial.index') }}"
                                class="flex items-center py-3 text-gray-700 hover:bg-green-50
                                    {{ request()->routeIs('doctor.historial.index') ? 'bg-green-100 font-bold text-green-700' : '' }}"
                                :class="open ? 'px-4 justify-start' : 'justify-center'"
                            >
                                <span class="material-icons"
                                    :class="open ? 'mr-3' : ''"
                                >history</span>
                                <span x-show="open" class="transition-all duration-200">Historial Clínico</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        <div class="px-4 py-4 border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center text-red-600 hover:text-red-800 w-full"
                    :class="open ? 'justify-start' : 'justify-center'"
                >
                    <span class="material-icons"
                        :class="open ? 'mr-2' : ''"
                    >logout</span>
                    <span x-show="open" class="transition-all duration-200">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Contenido principal con margen izquierdo igual al ancho del sidebar -->
    <div :class="open ? 'ml-64' : 'ml-24'">
        <main class="p-8 min-h-screen">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    <!-- Material Icons CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
