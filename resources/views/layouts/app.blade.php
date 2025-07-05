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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Estilos para asegurar que el contenido no choque con la sidebar -->
    <style>
        /* Asegurar que el contenido principal siempre tenga margen suficiente */
        #main-content {
            margin-left: 256px; /* Margen fijo para sidebar siempre abierta */
        }
        
        /* Media query para dispositivos móviles */
        @media (max-width: 768px) {
            #main-content {
                margin-left: 0 !important;
                padding-top: 60px; /* Espacio para sidebar móvil */
            }
        }
        
        /* Asegurar que la sidebar esté siempre al frente */
        aside {
            z-index: 50 !important;
        }
    </style>

    <!-- Scripts -->
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen">
    <!-- Incluir navegación vertical -->
    @include('layouts.navigation')

    <!-- Contenido principal con margen izquierdo para el sidebar -->
    <div id="main-content" 
         class="min-h-screen bg-gray-50">
        <main class="p-8">
            @if(session('success'))
                <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(function() {
                        var msg = document.getElementById('success-message');
                        if(msg) msg.style.display = 'none';
                    }, 4000);
                </script>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.mi-select').select2({
            placeholder: "Selecciona una o más opciones",
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
           }
        });
    });
    </script>

@if (session('swal'))
    <script>
        Swal.fire(@json(session('swal')));
    </script>
@elseif (session('pacienteCreate'))
    <script>
        Swal.fire(@json(session('pacienteCreate')));
    </script>
@endif
</body>

</html>
