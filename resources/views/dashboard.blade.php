<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800 leading-tight">
            {{ __('Motrix') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex">

                <!-- Sidebar -->
                <aside class="w-64 bg-gray-100/90 border-r p-6">
                    <h2 class="text-lg font-bold mb-4">Panel</h2>
                    <ul class="space-y-2">
                        <li><a href="{{ route('dashboard') }}" class="text-black font-semibold">Inicio</a></li>

                        {{-- Solo admin --}}
                        @if(auth()->user()->role === 'admin')
                            <li><a href="{{ route('empleados.index') }}" class="text-black font-semibold">Empleados</a></li>
                        @endif

                        {{-- Todos los roles --}}
                        <li><a href="{{ route('carros') }}" class="text-black font-semibold">Carros</a></li>
                        <li><a href="{{ route('motos') }}" class="text-black font-semibold">Motos</a></li>

                        {{-- Admin y empleado --}}
                        @if(in_array(auth()->user()->role, ['admin', 'empleado']))
                            <li><a href="{{ route('vender') }}" class="text-black font-semibold">Vender Vehículo</a></li>
                            <li><a href="{{ route('reportes') }}" class="text-black font-semibold">Reportes</a></li>
                        @endif

                        {{-- Todos los roles --}}
                        <li><a href="{{ route('configuracion') }}" class="text-black font-semibold">Configuración</a></li>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 mt-2 font-semibold">Cerrar sesión</button>
                        </form>
                    </ul>
                </aside>

                <!-- Contenido principal -->
                <main class="flex-1 p-6">

                    {{-- ✅ MENSAJE DE ÉXITO --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 flex items-center space-x-3 p-5 bg-yellow-50 border border-yellow-300 rounded-xl shadow text-yellow-800">
                            <span class="text-2xl">⚠️</span>
                            <div>
                                <p class="font-semibold text-base">{{ session('error') }}</p>
                                <p class="text-sm mt-1">Asegúrate de ejecutar: <code class="bg-yellow-100 px-2 py-0.5 rounded font-mono">python main.py</code></p>
                            </div>
                        </div>
                    @endif

                    <h1 class="text-2xl font-bold mb-4">Bienvenido a Motrix</h1>
                    <p class="mb-4 font-medium">"Tu aventura empieza con Motrix".</p>

                    <!-- Contenedor con transición de opacidad -->
                    <div class="mb-6 w-full max-h-64 overflow-hidden rounded-xl shadow-lg bg-gray-900">
                        <img id="imagen-dashboard" 
                             src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80" 
                             alt="Vehículos Motrix" 
                             class="w-full h-64 object-cover object-center transform hover:scale-103 transition-all duration-700 ease-in-out opacity-100">
                    </div>

                    <div class="mt-8 bg-gray-100 p-6 rounded-lg shadow text-center">
                        <h3 class="text-lg font-semibold mb-2">Panel Principal</h3>
                        <p class="text-gray-700">
                            Desde aquí puedes navegar a las secciones de empleados, vehículos, configuración y más.
                        </p>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Script de carrusel automático (Cada 2 segundos) -->
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
                // Desvanecer imagen actual (Efecto fade out)
                imgElement.classList.add('opacity-0');
                
                setTimeout(() => {
                    // Cambiar al siguiente índice de la lista
                    index = (index + 1) % imagenes.length;
                    imgElement.src = imagenes[index];
                    
                    // Volver a mostrar (Efecto fade in)
                    imgElement.classList.remove('opacity-0');
                }, 400); // Pequeña pausa a mitad de camino para que la URL cambie en lo oscuro
            }
            
            // Ejecutar el ciclo de cambio estricto cada 2000 milisegundos (2 segundos)
            setInterval(cambiarImagen, 2000);
        });
    </script>
</x-app-layout>