<!-- Sidebar vertical -->
<aside 
    class="fixed top-0 left-0 w-64 bg-white border-r flex flex-col justify-between"
    style="height: 100vh; z-index: 40;"
>
        <div>
            <!-- Header del sidebar -->
            <div class="flex items-center px-4 py-6 border-b">
                <!-- Información del usuario -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-lg">
                        {{ strtoupper(substr(session('user_data')['name'] ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ session('user_data')['name'] ?? 'Usuario' }}</div>
                        <div class="text-xs text-gray-500 capitalize">{{ session('user_data')['role'] ?? '' }}</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6">

                <ul class="space-y-1">
                    @php $role = session('user_data')['role'] ?? null; @endphp
                    @if(session('user_data'))
                        @if($role === 'doctor')
                            <li>
                                <a href="{{ route('doctor.dashboard') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('doctor.dashboard') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <!-- Icono SVG para Dashboard -->
                                    <svg class="w-7 h-7 flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('doctor.agenda') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('doctor.agenda') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <img src="{{ asset('icons/horario.png') }}" alt="Agenda" class="w-7 h-7 flex-shrink-0 mr-3">
                                    <span>Agenda</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('doctor.historial.index') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('doctor.historial.*') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <img src="{{ asset('icons/historial.png') }}" alt="Historial" class="w-7 h-7 flex-shrink-0 mr-3">
                                    <span>Historial</span>
                                </a>
                            </li>
                        @elseif($role === 'recepcionista')
                            <li>
                                <a href="{{ route('recepcionista.dashboard') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('recepcionista.dashboard') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <!-- Icono SVG para Dashboard -->
                                    <svg class="w-7 h-7 flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            
                            <!-- Submenu de Pacientes -->
                            <li>
                                <div class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors cursor-pointer
                                           {{ request()->routeIs('recepcionista.pacientes.*') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                           px-4 justify-between"
                                     onclick="toggleSubmenu(this)">
                                    <div class="flex items-center">
                                        <img src="{{ asset('icons/paciente.png') }}" alt="Pacientes" class="w-7 h-7 flex-shrink-0 mr-3">
                                        <span>Pacientes</span>
                                    </div>
                                    <!-- Flecha -->
                                    <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200 submenu-arrow" 
                                         fill="none" 
                                         stroke="currentColor" 
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                
                                <!-- Submenu items (inicialmente oculto) -->
                                <div class="submenu bg-gray-50 border-l-4 border-green-200" style="display: none;">
                                    <a href="{{ route('recepcionista.pacientes.buscar') }}" 
                                       class="flex items-center py-3 pl-8 pr-4 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors border-b border-gray-200
                                              {{ request()->routeIs('recepcionista.pacientes.buscar') ? 'bg-green-100 text-green-800 font-medium' : '' }}">
                                        <img src="{{ asset('icons/paciente_buscar.png') }}" alt="Buscar" class="w-7 h-7 mr-3">
                                        <span>Buscar Pacientes</span>
                                    </a>
                                    <a href="{{ route('recepcionista.pacientes.registrar') }}" 
                                       class="flex items-center py-3 pl-8 pr-4 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors
                                              {{ request()->routeIs('recepcionista.pacientes.registrar') ? 'bg-green-100 text-green-800 font-medium' : '' }}">
                                        <img src="{{ asset('icons/paciente_agregar.png') }}" alt="Agregar" class="w-7 h-7 mr-3">
                                        <span>Agregar Paciente</span>
                                    </a>
                                </div>
                            </li>
                            
                            <li>
                                <a href="{{ route('recepcionista.citas.index') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('recepcionista.citas.*') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <img src="{{ asset('icons/cita_me_agregar.png') }}" alt="Citas" class="w-7 h-7 flex-shrink-0 mr-3">
                                    <span>Citas</span>
                                </a>
                            </li>
                        @elseif($role === 'administrativo')
                            <li>
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('admin.dashboard') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <!-- Icono SVG para Dashboard -->
                                    <svg class="w-7 h-7 flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.usuarios.index') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('admin.usuarios.*') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <img src="{{ asset('icons/usuario.png') }}" alt="Usuarios" class="w-7 h-7 flex-shrink-0 mr-3">
                                    <span>Usuarios</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.disponibilidad.index') }}" 
                                   class="flex items-center py-3 text-gray-700 hover:bg-green-50 transition-colors
                                          {{ request()->routeIs('admin.disponibilidad.*') ? 'bg-green-100 font-bold text-green-700 border-r-4 border-green-500' : '' }}
                                          px-4 justify-start"
                                >
                                    <img src="{{ asset('icons/definir_horario.png') }}" alt="Disponibilidad" class="w-7 h-7 flex-shrink-0 mr-3">
                                    <span>Disponibilidad</span>
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </nav>
        </div>

        <!-- Selector de Tamaño de Letra -->
        <div class="px-4 py-3 border-t border-gray-200">
            <div class="mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tamaño de Letra</span>
            </div>
            <div class="flex items-center justify-between space-x-2">
                <!-- Letra Pequeña -->
                <button 
                    onclick="changeFontSize('small')" 
                    id="font-small"
                    class="flex items-center justify-center w-8 h-8 rounded-md border-2 transition-all duration-200 hover:bg-gray-50 font-size-btn"
                    title="Letra pequeña"
                >
                    <span class="text-xs font-semibold">A</span>
                </button>

                <!-- Letra Normal (por defecto) -->
                <button 
                    onclick="changeFontSize('normal')" 
                    id="font-normal"
                    class="flex items-center justify-center w-8 h-8 rounded-md border-2 transition-all duration-200 hover:bg-gray-50 font-size-btn border-green-500 bg-green-50 text-green-700"
                    title="Letra normal"
                >
                    <span class="text-sm font-semibold">A</span>
                </button>

                <!-- Letra Grande -->
                <button 
                    onclick="changeFontSize('large')" 
                    id="font-large"
                    class="flex items-center justify-center w-8 h-8 rounded-md border-2 transition-all duration-200 hover:bg-gray-50 font-size-btn"
                    title="Letra grande"
                >
                    <span class="text-base font-semibold">A</span>
                </button>
            </div>

            <!-- Indicador visual del tamaño actual -->
            <div class="mt-2 text-center">
                <span id="font-indicator" class="text-xs text-gray-500">Normal</span>
            </div>
        </div>

        <!-- Footer del sidebar con logout -->
        <div class="px-4 py-4 border-t">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit"
                    onclick="resetFontSizeOnLogout()"
                    class="flex items-center text-red-600 hover:text-red-800 w-full transition-colors px-0 justify-start"
                >
                    <svg class="w-7 h-7 flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

<script>
function toggleSubmenu(element) {
    const submenu = element.nextElementSibling;
    const arrow = element.querySelector('.submenu-arrow');
    
    if (submenu.style.display === 'none' || submenu.style.display === '') {
        submenu.style.display = 'block';
        arrow.style.transform = 'rotate(90deg)';
    } else {
        submenu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}

// Si estamos en una página de pacientes, abrir el submenu automáticamente
document.addEventListener('DOMContentLoaded', function() {
    @if(request()->routeIs('recepcionista.pacientes.*'))
        const pacientesSubmenu = document.querySelector('.submenu');
        const pacientesArrow = document.querySelector('.submenu-arrow');
        if (pacientesSubmenu && pacientesArrow) {
            pacientesSubmenu.style.display = 'block';
            pacientesArrow.style.transform = 'rotate(90deg)';
        }
    @endif
});
</script>
