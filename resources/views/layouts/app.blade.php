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

        /* Estilos para el selector de tamaño de letra */
        .font-size-btn {
            border-color: #d1d5db;
            color: #6b7280;
        }

        .font-size-btn.active {
            border-color: #10b981;
            background-color: #d1fae5;
            color: #047857;
        }

        /* Clases para diferentes tamaños de letra */
        .font-small, html.font-small body {
            font-size: 0.875rem !important; /* 14px */
            line-height: 1.25rem !important;
        }

        .font-small h1, html.font-small h1 { font-size: 1.5rem !important; }
        .font-small h2, html.font-small h2 { font-size: 1.25rem !important; }
        .font-small h3, html.font-small h3 { font-size: 1.125rem !important; }
        .font-small .text-lg, html.font-small .text-lg { font-size: 1rem !important; }
        .font-small .text-xl, html.font-small .text-xl { font-size: 1.125rem !important; }
        .font-small .text-2xl, html.font-small .text-2xl { font-size: 1.25rem !important; }
        .font-small .text-3xl, html.font-small .text-3xl { font-size: 1.5rem !important; }
        
        /* Selectores para tablas en font-small */
        .font-small table, html.font-small table { font-size: 0.875rem !important; }
        .font-small th, html.font-small th { font-size: 0.875rem !important; }
        .font-small td, html.font-small td { font-size: 0.875rem !important; }
        .font-small input, html.font-small input { font-size: 0.875rem !important; }
        .font-small button, html.font-small button { font-size: 0.875rem !important; }

        .font-normal, html.font-normal body {
            font-size: 1rem !important; /* 16px - tamaño por defecto */
            line-height: 1.5rem !important;
        }

        /* Selectores para tablas en font-normal */
        .font-normal table, html.font-normal table { font-size: 1rem !important; }
        .font-normal th, html.font-normal th { font-size: 1rem !important; }
        .font-normal td, html.font-normal td { font-size: 1rem !important; }
        .font-normal input, html.font-normal input { font-size: 1rem !important; }
        .font-normal button, html.font-normal button { font-size: 1rem !important; }

        .font-large, html.font-large body {
            font-size: 1.125rem !important; /* 18px */
            line-height: 1.75rem !important;
        }

        .font-large h1, html.font-large h1 { font-size: 2.25rem !important; }
        .font-large h2, html.font-large h2 { font-size: 1.875rem !important; }
        .font-large h3, html.font-large h3 { font-size: 1.5rem !important; }
        .font-large .text-lg, html.font-large .text-lg { font-size: 1.25rem !important; }
        .font-large .text-xl, html.font-large .text-xl { font-size: 1.5rem !important; }
        .font-large .text-2xl, html.font-large .text-2xl { font-size: 1.875rem !important; }
        .font-large .text-3xl, html.font-large .text-3xl { font-size: 2.25rem !important; }
        
        /* Selectores para tablas en font-large */
        .font-large table, html.font-large table { font-size: 1.125rem !important; }
        .font-large th, html.font-large th { font-size: 1.125rem !important; }
        .font-large td, html.font-large td { font-size: 1.125rem !important; }
        .font-large input, html.font-large input { font-size: 1.125rem !important; }
        .font-large button, html.font-large button { font-size: 1.125rem !important; }
    </style>

    <!-- Scripts -->
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    
    <!-- Script para aplicar tamaño de letra ANTES de que se renderice la página -->
    <script>
        // Aplicar tamaño de letra inmediatamente para evitar parpadeo
        (function() {
            const savedSize = localStorage.getItem('fontSize') || 'normal';
            document.documentElement.classList.add('font-' + savedSize);
        })();
    </script>
    
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

        // Cargar el tamaño de letra guardado
        loadFontSize();
    });

    // Función para cambiar el tamaño de letra
    function changeFontSize(size) {
        // Remover clases de tamaño anteriores del html y body
        document.documentElement.classList.remove('font-small', 'font-normal', 'font-large');
        document.body.classList.remove('font-small', 'font-normal', 'font-large');
        
        // Agregar la nueva clase al html (para persistencia) y body (para compatibilidad)
        document.documentElement.classList.add('font-' + size);
        document.body.classList.add('font-' + size);
        
        // Actualizar botones activos
        document.querySelectorAll('.font-size-btn').forEach(btn => {
            btn.classList.remove('active', 'border-green-500', 'bg-green-50', 'text-green-700');
            btn.classList.add('border-gray-300', 'text-gray-600');
        });
        
        const activeBtn = document.getElementById('font-' + size);
        if (activeBtn) {
            activeBtn.classList.add('active', 'border-green-500', 'bg-green-50', 'text-green-700');
            activeBtn.classList.remove('border-gray-300', 'text-gray-600');
        }
        
        // Actualizar indicador
        const indicator = document.getElementById('font-indicator');
        if (indicator) {
            const sizeNames = {
                'small': 'Pequeña',
                'normal': 'Normal',
                'large': 'Grande'
            };
            indicator.textContent = sizeNames[size] || 'Normal';
        }
        
        // Guardar preferencia en localStorage
        localStorage.setItem('fontSize', size);
    }

    // Función para cargar el tamaño de letra guardado
    function loadFontSize() {
        const savedSize = localStorage.getItem('fontSize') || 'normal';
        // Solo actualizar los botones y el indicador, la clase ya está aplicada en el html
        
        // Asegurar que body también tenga la clase
        document.body.classList.remove('font-small', 'font-normal', 'font-large');
        document.body.classList.add('font-' + savedSize);
        
        // Actualizar botones
        document.querySelectorAll('.font-size-btn').forEach(btn => {
            btn.classList.remove('active', 'border-green-500', 'bg-green-50', 'text-green-700');
            btn.classList.add('border-gray-300', 'text-gray-600');
        });
        
        const activeBtn = document.getElementById('font-' + savedSize);
        if (activeBtn) {
            activeBtn.classList.add('active', 'border-green-500', 'bg-green-50', 'text-green-700');
            activeBtn.classList.remove('border-gray-300', 'text-gray-600');
        }
        
        // Actualizar indicador
        const indicator = document.getElementById('font-indicator');
        if (indicator) {
            const sizeNames = {
                'small': 'Pequeña',
                'normal': 'Normal',
                'large': 'Grande'
            };
            indicator.textContent = sizeNames[savedSize] || 'Normal';
        }
    }

    // Función para resetear el tamaño de letra al hacer logout
    function resetFontSizeOnLogout() {
        // Limpiar la preferencia guardada
        localStorage.removeItem('fontSize');
        
        // Resetear al tamaño normal
        document.documentElement.classList.remove('font-small', 'font-normal', 'font-large');
        document.body.classList.remove('font-small', 'font-normal', 'font-large');
        document.documentElement.classList.add('font-normal');
        document.body.classList.add('font-normal');
        
        return true; // Permitir que el formulario se envíe
    }
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
