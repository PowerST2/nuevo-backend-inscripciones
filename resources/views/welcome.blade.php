<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sistema de Inscripción - UNI</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Poppins', sans-serif;
            }
        </style>
    </head>
    <body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen antialiased">

        <!-- Header -->
        <header class="absolute top-0 left-0 right-0 z-50">
            <div class="container mx-auto px-6 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-[#7D0633] rounded-lg flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">UNI</span>
                        </div>
                        <div>
                            <h1 class="text-[#1A1A1A] font-bold text-lg leading-tight">Universidad Nacional<br>de Ingeniería</h1>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <nav class="flex items-center space-x-4">
                            @auth
                                <a href="{{ url('/admin') }}"
                                   class="px-6 py-2.5 bg-white text-[#1A1A1A] font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 border border-gray-200">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('filament.admin.auth.login') }}"
                                   class="px-6 py-2.5 text-[#1A1A1A] font-medium hover:text-[#7D0633] transition-colors duration-200">
                                    Iniciar Sesión
                                </a>
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative min-h-screen flex items-center justify-center px-6 py-24">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-[#7D0633] opacity-5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#C9A227] opacity-10 rounded-full blur-3xl"></div>

            <div class="container mx-auto max-w-6xl relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">

                    <!-- Left Column - Content -->
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <div class="inline-block px-4 py-2 bg-[#7D0633]/10 text-[#7D0633] rounded-full text-sm font-semibold">
                                Proceso de Admisión 2025
                            </div>
                            <h2 class="text-5xl lg:text-6xl font-bold text-[#1A1A1A] leading-tight">
                                Sistema de
                                <span class="text-[#7D0633]">Inscripción</span>
                            </h2>
                            <p class="text-xl text-gray-600 leading-relaxed">
                                Bienvenido al sistema de inscripción para el examen de admisión de la Universidad Nacional de Ingeniería
                            </p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-[#7D0633]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-[#7D0633]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-[#1A1A1A] mb-1">Proceso Rápido y Seguro</h3>
                                    <p class="text-gray-600 text-sm">Completa tu inscripción en pocos pasos</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-[#C9A227]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-[#1A1A1A] mb-1">Soporte Disponible</h3>
                                    <p class="text-gray-600 text-sm">Asistencia durante todo el proceso</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-[#7D0633]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-[#7D0633]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-[#1A1A1A] mb-1">Seguimiento en Tiempo Real</h3>
                                    <p class="text-gray-600 text-sm">Revisa el estado de tu inscripción en cualquier momento</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            @auth
                                <a href="{{ url('/admin') }}"
                                   class="inline-flex items-center justify-center px-8 py-4 bg-[#7D0633] text-white font-semibold rounded-xl shadow-lg hover:bg-[#5D0426] hover:shadow-xl transition-all duration-200">
                                    <span>Ir al Panel</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('filament.admin.auth.login') }}"
                                   class="inline-flex items-center justify-center px-8 py-4 bg-[#7D0633] text-white font-semibold rounded-xl shadow-lg hover:bg-[#5D0426] hover:shadow-xl transition-all duration-200">
                                    <span>Acceder al Sistema</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            @endauth

                            <a href="#informacion"
                               class="inline-flex items-center justify-center px-8 py-4 bg-white text-[#7D0633] font-semibold rounded-xl shadow-lg hover:shadow-xl border-2 border-[#7D0633] transition-all duration-200">
                                <span>Más Información</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column - Visual Element -->
                    <div class="relative lg:block">
                        <div class="relative">
                            <!-- Main Card -->
                            <div class="bg-white rounded-2xl shadow-2xl p-8 relative z-10 border border-gray-100">
                                <div class="space-y-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-[#7D0633] to-[#5D0426] rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-xl text-[#1A1A1A]">Examen de Admisión</h3>
                                            <p class="text-gray-500 text-sm">Proceso 2025-I</p>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                            <span class="text-gray-600 font-medium">Modalidad</span>
                                            <span class="font-semibold text-[#1A1A1A]">Ordinario / CEPRE</span>
                                        </div>

                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                            <span class="text-gray-600 font-medium">Fecha de Examen</span>
                                            <span class="font-semibold text-[#1A1A1A]">Por confirmar</span>
                                        </div>

                                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#7D0633]/10 to-[#C9A227]/10 rounded-xl border-2 border-[#7D0633]/20">
                                            <span class="text-[#1A1A1A] font-medium">Estado</span>
                                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                                Inscripciones Abiertas
                                            </span>
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-gray-200">
                                        <p class="text-sm text-gray-500 text-center">
                                            ¿Necesitas ayuda? Contacta con nosotros
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Decorative elements -->
                            <div class="absolute -top-6 -right-6 w-32 h-32 bg-[#C9A227] rounded-2xl -z-10 transform rotate-12"></div>
                            <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-[#7D0633]/20 rounded-2xl -z-10 transform -rotate-12"></div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-gray-200 bg-white/50 backdrop-blur-sm">
            <div class="container mx-auto px-6 py-8">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                    <div class="text-sm text-gray-600">
                        © {{ date('Y') }} Universidad Nacional de Ingeniería. Todos los derechos reservados.
                    </div>
                    <div class="flex items-center space-x-6">
                        <a href="#" class="text-sm text-gray-600 hover:text-[#7D0633] transition-colors duration-200">
                            Términos y Condiciones
                        </a>
                        <a href="#" class="text-sm text-gray-600 hover:text-[#7D0633] transition-colors duration-200">
                            Política de Privacidad
                        </a>
                        <a href="#" class="text-sm text-gray-600 hover:text-[#7D0633] transition-colors duration-200">
                            Soporte
                        </a>
                    </div>
                </div>
            </div>
        </footer>

    </body>
</html>

