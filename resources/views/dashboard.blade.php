<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800 leading-tight">
            {{ __('Motrix') }}
        </h2>
    </x-slot>

    <div class="py-4 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <!-- Mobile nav -->
                <div class="block sm:hidden bg-gray-100 border-b px-4 py-3" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full font-bold text-base">
                        <span>Panel</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="mt-3 space-y-2 pb-2">
                        <li><a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Inicio</a></li>

                        @if(auth()->user()->role === 'admin')
                            <li><a href="{{ route('empleados.index') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Empleados</a></li>
                        @endif

                        <li><a href="{{ route('carros') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Carros</a></li>
                        <li><a href="{{ route('motos') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Motos</a></li>

                        @if(in_array(auth()->user()->role, ['admin', 'empleado']))
                            <li><a href="{{ route('vender') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Vender Vehículo</a></li>
                            <li><a href="{{ route('reportes') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Reportes</a></li>
                        @endif

                        <li><a href="{{ route('configuracion') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Configuración</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 px-3 rounded bg-red-50 border border-red-200 text-red-600 font-semibold text-sm">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="flex">
                    <!-- Sidebar desktop -->
                    <aside class="hidden sm:block w-56 bg-gray-100/90 border-r p-6 shrink-0">
                        <h2 class="text-lg font-bold mb-4">Panel</h2>
                        <ul class="space-y-2">
                            <li><a href="{{ route('dashboard') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Inicio</a></li>

                            @if(auth()->user()->role === 'admin')
                                <li><a href="{{ route('empleados.index') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Empleados</a></li>
                            @endif

                            <li><a href="{{ route('carros') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Carros</a></li>
                            <li><a href="{{ route('motos') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Motos</a></li>

                            @if(in_array(auth()->user()->role, ['admin', 'empleado']))
                                <li><a href="{{ route('vender') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Vender Vehículo</a></li>
                                <li><a href="{{ route('reportes') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Reportes</a></li>
                            @endif

                            <li><a href="{{ route('configuracion') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Configuración</a></li>
                            <li class="pt-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-red-600 font-semibold text-sm hover:underline">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </aside>

                    <!-- Contenido principal -->
                    <main class="flex-1 p-4 sm:p-6 min-w-0">

                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 flex items-start space-x-3 p-4 bg-yellow-50 border border-yellow-300 rounded-xl shadow text-yellow-800">
                                <span class="text-xl mt-0.5">⚠️</span>
                                <div>
                                    <p class="font-semibold text-sm">{{ session('error') }}</p>
                                    <p class="text-xs mt-1">Asegúrate de ejecutar: <code class="bg-yellow-100 px-2 py-0.5 rounded font-mono">python main.py</code></p>
                                </div>
                            </div>
                        @endif

                        <h1 class="text-xl sm:text-2xl font-bold mb-2">Bienvenido a Motrix</h1>
                        <p class="mb-4 font-medium text-sm sm:text-base">"Tu aventura empieza con Motrix".</p>

                        <!-- Carrusel -->
                        <div class="mb-6 w-full overflow-hidden rounded-xl shadow-lg bg-gray-900" style="height: 200px;">
                            <img id="imagen-dashboard"
                                 src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80"
                                 alt="Vehículos Motrix"
                                 class="w-full h-full object-cover object-center transition-opacity duration-400 ease-in-out opacity-100">
                        </div>

                        <div class="mt-6 bg-gray-100 p-4 sm:p-6 rounded-lg shadow text-center">
                            <h3 class="text-base sm:text-lg font-semibold mb-2">Panel Principal</h3>
                            <p class="text-gray-700 text-sm">
                                Desde aquí puedes navegar a las secciones de empleados, vehículos, configuración y más.
                            </p>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const imagenes = [
                'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1449426468159-d96dbf08f19f?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1580273916550-e323be2ae537?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1609630875171-b1321377ee65?auto=format&fit=crop&w=1200&q=80'
            ];

            let index = 0;
            const imgElement = document.getElementById('imagen-dashboard');

            function cambiarImagen() {
                imgElement.classList.add('opacity-0');
                setTimeout(() => {
                    index = (index + 1) % imagenes.length;
                    imgElement.src = imagenes[index];
                    imgElement.classList.remove('opacity-0');
                }, 400);
            }

            setInterval(cambiarImagen, 2000);
        });
    </script>
</x-app-layout>
