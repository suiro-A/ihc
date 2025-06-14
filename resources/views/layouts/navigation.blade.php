<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('login') }}">
                        <span class="text-xl font-bold text-green-600">Sistema Clínica</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        @if(auth()->user()->hasRole('doctor'))
                            <x-nav-link :href="route('doctor.dashboard')" :active="request()->routeIs('doctor.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('doctor.agenda')" :active="request()->routeIs('doctor.agenda')">
                                {{ __('Agenda') }}
                            </x-nav-link>
                            <x-nav-link :href="route('doctor.historial.index')" :active="request()->routeIs('doctor.historial.*')">
                                {{ __('Historial') }}
                            </x-nav-link>
                        @elseif(auth()->user()->hasRole('recepcionista'))
                            <x-nav-link :href="route('recepcionista.dashboard')" :active="request()->routeIs('recepcionista.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('recepcionista.pacientes.buscar')" :active="request()->routeIs('recepcionista.pacientes.*')">
                                {{ __('Pacientes') }}
                            </x-nav-link>
                            <x-nav-link :href="route('recepcionista.citas.index')" :active="request()->routeIs('recepcionista.citas.*')">
                                {{ __('Citas') }}
                            </x-nav-link>
                        @elseif(auth()->user()->hasRole('administrativo'))
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.usuarios.index')" :active="request()->routeIs('admin.usuarios.*')">
                                {{ __('Usuarios') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.disponibilidad.index')" :active="request()->routeIs('admin.disponibilidad.*')">
                                {{ __('Disponibilidad') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                                {{ __('Roles') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
            <div class="pt-2 pb-3 space-y-1">
                @if(auth()->user()->hasRole('doctor'))
                    <x-responsive-nav-link :href="route('doctor.dashboard')" :active="request()->routeIs('doctor.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('doctor.agenda')" :active="request()->routeIs('doctor.agenda')">
                        {{ __('Agenda') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('doctor.historial.index')" :active="request()->routeIs('doctor.historial.*')">
                        {{ __('Historial') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->hasRole('recepcionista'))
                    <x-responsive-nav-link :href="route('recepcionista.dashboard')" :active="request()->routeIs('recepcionista.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('recepcionista.pacientes.buscar')" :active="request()->routeIs('recepcionista.pacientes.*')">
                        {{ __('Pacientes') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('recepcionista.citas.index')" :active="request()->routeIs('recepcionista.citas.*')">
                        {{ __('Citas') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->hasRole('administrativo'))
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.usuarios.index')" :active="request()->routeIs('admin.usuarios.*')">
                        {{ __('Usuarios') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.disponibilidad.index')" :active="request()->routeIs('admin.disponibilidad.*')">
                        {{ __('Disponibilidad') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Cerrar Sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
