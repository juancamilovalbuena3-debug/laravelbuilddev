<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800 leading-tight">
            {{ __('Gestión de Empleados y Vehículos') }}
        </h2>
    </x-slot>

    <div class="py-4 md:py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <!-- ── MOBILE: Botón hamburguesa ── -->
                <div class="flex items-center justify-between p-4 border-b md:hidden" x-data="{ open: false }">
                    <span class="font-bold text-lg">Panel</span>
                    <button @click="open = !open" class="p-2 rounded-md border border-gray-300 focus:outline-none" aria-label="Menú">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Menú móvil desplegable -->
                    <div x-show="open" x-transition class="absolute top-16 left-0 right-0 z-50 bg-white shadow-lg border-b p-4">
                        <ul class="space-y-3">
                            <li><a href="{{ route('dashboard') }}" class="block text-black font-semibold py-2 border-b border-gray-100">Inicio</a></li>
                            <li><a href="{{ route('empleados.index') }}" class="block text-black font-semibold py-2 border-b border-gray-100">Empleados</a></li>
                            <li><a href="{{ route('carros') }}" class="block text-black font-semibold py-2 border-b border-gray-100">Carros</a></li>
                            <li><a href="{{ route('motos') }}" class="block text-black font-semibold py-2 border-b border-gray-100">Motos</a></li>
                            <li><a href="{{ route('vender') }}" class="block text-black font-semibold py-2 border-b border-gray-100">Vender Vehículo</a></li>
                            <li><a href="{{ route('configuracion') }}" class="block text-black font-semibold py-2 border-b border-gray-100">Configuración</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-red-600 font-semibold py-2">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ── LAYOUT PRINCIPAL ── -->
                <div class="flex flex-col md:flex-row">

                    <!-- Sidebar (solo desktop) -->
                    <aside class="hidden md:block w-64 bg-gray-100/90 border-r p-6 flex-shrink-0">
                        <h2 class="text-lg font-bold mb-4">Panel</h2>
                        <ul class="space-y-2">
                            <li><a href="{{ route('dashboard') }}" class="text-black font-semibold">Inicio</a></li>
                            <li><a href="{{ route('empleados.index') }}" class="text-black font-semibold">Empleados</a></li>
                            <li><a href="{{ route('carros') }}" class="text-black font-semibold">Carros</a></li>
                            <li><a href="{{ route('motos') }}" class="text-black font-semibold">Motos</a></li>
                            <li><a href="{{ route('vender') }}" class="text-black font-semibold">Vender Vehículo</a></li>
                            <li><a href="{{ route('configuracion') }}" class="text-black font-semibold">Configuración</a></li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-red-600 mt-2 font-semibold">Cerrar sesión</button>
                            </form>
                        </ul>
                    </aside>

                    <!-- Contenido principal -->
                    <main class="flex-1 p-4 md:p-6 min-w-0">
                        <h1 class="text-xl md:text-2xl font-bold mb-4 md:mb-6">Gestión de Empleados y Vehículos</h1>

                        @if(session('error'))
                            <div class="mb-6 flex items-start space-x-3 p-4 bg-yellow-50 border border-yellow-300 rounded-xl shadow text-yellow-800">
                                <span class="text-2xl flex-shrink-0">⚠️</span>
                                <div>
                                    <p class="font-semibold text-sm md:text-base">{{ session('error') }}</p>
                                    <p class="text-xs md:text-sm mt-1">Asegúrate de ejecutar: <code class="bg-yellow-100 px-2 py-0.5 rounded font-mono">python main.py</code></p>
                                </div>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg text-sm md:text-base">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Pestañas -->
                        <div x-data="{ tab: '{{ request('tab') === 'vehiculos' ? 'vehiculos' : 'empleados' }}' }">
                            <div class="flex space-x-4 border-b mb-4 md:mb-6 overflow-x-auto">
                                <button
                                    @click="tab = 'empleados'"
                                    :class="tab === 'empleados' ? 'border-b-2 border-black text-black font-semibold' : 'text-gray-600 hover:text-black'"
                                    class="pb-2 transition whitespace-nowrap text-sm md:text-base px-1">
                                    Empleados
                                </button>
                                <button
                                    @click="tab = 'vehiculos'"
                                    :class="tab === 'vehiculos' ? 'border-b-2 border-black text-black font-semibold' : 'text-gray-600 hover:text-black'"
                                    class="pb-2 transition whitespace-nowrap text-sm md:text-base px-1">
                                    Vehículos
                                </button>
                            </div>

                            <!-- ══ TAB EMPLEADOS ══ -->
                            <div x-show="tab === 'empleados'">
                                <!-- Cabecera + exportar -->
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                                    <h2 class="text-lg md:text-xl font-semibold">Lista de Empleados</h2>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('empleados.export.pdf', ['busqueda' => request('tab') === 'empleados' ? request('busqueda') : '']) }}"
                                           class="bg-white hover:bg-gray-200 text-black px-3 py-2 rounded shadow text-xs md:text-sm font-semibold border transition">
                                            Exportar PDF
                                        </a>
                                        <a href="{{ route('empleados.export.csv', ['busqueda' => request('tab') === 'empleados' ? request('busqueda') : '']) }}"
                                           class="bg-white hover:bg-gray-200 text-black px-3 py-2 rounded shadow text-xs md:text-sm font-semibold border transition">
                                            Exportar CSV
                                        </a>
                                    </div>
                                </div>

                                <!-- Buscador empleados -->
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
                                    <form method="GET" action="{{ route('empleados.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                        <input type="hidden" name="tab" value="empleados">
                                        <input type="text" name="busqueda" value="{{ request('tab') !== 'vehiculos' ? request('busqueda') : '' }}"
                                               placeholder="Buscar por nombre o correo"
                                               class="border rounded px-3 py-2 w-full sm:w-56 md:w-64 focus:ring-black focus:border-black text-sm">
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="flex-1 sm:flex-none bg-white hover:bg-gray-200 text-black px-4 py-2 rounded shadow font-semibold border transition text-sm">
                                                Buscar
                                            </button>
                                            <a href="{{ route('empleados.index') }}"
                                               class="flex-1 sm:flex-none text-center bg-white hover:bg-gray-200 text-black px-4 py-2 rounded shadow font-semibold border transition text-sm">
                                                Limpiar
                                            </a>
                                        </div>
                                    </form>
                                    <a href="{{ route('empleados.create') }}"
                                       class="text-center bg-white hover:bg-gray-200 text-black px-4 py-2 rounded shadow font-semibold border transition text-sm whitespace-nowrap">
                                        + Agregar Empleado
                                    </a>
                                </div>

                                @if($empleados->count() > 0)
                                    <!-- VISTA TABLA (md+) -->
                                    <div class="hidden md:block overflow-x-auto">
                                        <table class="table-fixed w-full border bg-white shadow rounded text-gray-800 text-sm">
                                            <colgroup>
                                                <col class="w-1/5">
                                                <col class="w-1/5">
                                                <col class="w-1/6">
                                                <col class="w-1/4">
                                                <col class="w-1/5">
                                            </colgroup>
                                            <thead class="bg-gray-300">
                                                <tr>
                                                    <th class="px-4 py-2 border text-left">Nombre</th>
                                                    <th class="px-4 py-2 border text-left">Cargo</th>
                                                    <th class="px-4 py-2 border text-left">Salario</th>
                                                    <th class="px-4 py-2 border text-left">Correo</th>
                                                    <th class="px-4 py-2 border text-left">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($empleados as $empleado)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border px-4 py-2 truncate">{{ $empleado->nombre }}</td>
                                                        <td class="border px-4 py-2 truncate">{{ $empleado->puesto }}</td>
                                                        <td class="border px-4 py-2">${{ number_format($empleado->salario, 2) }}</td>
                                                        <td class="border px-4 py-2 truncate max-w-0" title="{{ $empleado->email }}">{{ $empleado->email }}</td>
                                                        <td class="border px-4 py-2">
                                                            <div class="flex space-x-2">
                                                                <a href="{{ route('empleados.edit', $empleado->id) }}"
                                                                   class="bg-white hover:bg-gray-200 text-black px-3 py-1 rounded shadow font-semibold border transition text-xs">
                                                                    Editar
                                                                </a>
                                                                <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" onclick="return confirm('¿Eliminar este empleado?')"
                                                                            class="bg-white hover:bg-gray-200 text-black px-3 py-1 rounded shadow font-semibold border transition text-xs">
                                                                        Eliminar
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- VISTA TARJETAS (mobile) -->
                                    <div class="md:hidden space-y-3">
                                        @foreach($empleados as $empleado)
                                            <div class="bg-white border rounded-xl shadow-sm p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <p class="font-bold text-gray-900">{{ $empleado->nombre }}</p>
                                                        <p class="text-sm text-gray-500">{{ $empleado->puesto }}</p>
                                                    </div>
                                                    <span class="text-sm font-semibold text-gray-800">${{ number_format($empleado->salario, 2) }}</span>
                                                </div>
                                                <p class="text-xs text-gray-500 truncate mb-3">{{ $empleado->email }}</p>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('empleados.edit', $empleado->id) }}"
                                                       class="flex-1 text-center bg-white hover:bg-gray-200 text-black px-3 py-2 rounded shadow font-semibold border transition text-sm">
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('¿Eliminar este empleado?')"
                                                                class="w-full bg-white hover:bg-gray-200 text-black px-3 py-2 rounded shadow font-semibold border transition text-sm">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4">{{ $empleados->links() }}</div>
                                @else
                                    @if(request('busqueda') && request('tab') !== 'vehiculos')
                                        <div class="text-center py-12 text-gray-500 px-4">
                                            <p class="text-lg md:text-xl">No se encontraron empleados con "<strong>{{ request('busqueda') }}</strong>".</p>
                                            <p class="text-sm mt-2">Intenta con otro nombre o correo.</p>
                                            <a href="{{ route('empleados.index') }}" class="inline-block mt-4 text-blue-600 underline text-sm">Limpiar búsqueda</a>
                                        </div>
                                    @else
                                        <div class="text-center py-12 text-gray-500 px-4">
                                            <p class="text-lg md:text-xl">No hay empleados registrados aún.</p>
                                            <p class="text-sm mt-2">Haz clic en <strong>+ Agregar Empleado</strong> para comenzar.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- ══ TAB VEHÍCULOS ══ -->
                            <div x-show="tab === 'vehiculos'">
                                <!-- Cabecera + exportar -->
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                                    <h2 class="text-lg md:text-xl font-semibold">Vehículos Publicados</h2>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('vehiculos.export.pdf', ['busqueda' => request('busqueda'), 'tipo' => request('tipo')]) }}"
                                           class="bg-white hover:bg-gray-200 text-black px-3 py-2 rounded shadow text-xs md:text-sm font-semibold border transition">
                                            Exportar PDF
                                        </a>
                                        <a href="{{ route('vehiculos.export.csv', ['busqueda' => request('busqueda'), 'tipo' => request('tipo')]) }}"
                                           class="bg-white hover:bg-gray-200 text-black px-3 py-2 rounded shadow text-xs md:text-sm font-semibold border transition">
                                            Exportar CSV
                                        </a>
                                    </div>
                                </div>

                                <!-- Buscador vehículos -->
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-4 md:mb-6">
                                    <form method="GET" action="{{ route('empleados.index') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto flex-wrap">
                                        <input type="hidden" name="tab" value="vehiculos">
                                        <input type="text" name="busqueda" value="{{ request('tab') === 'vehiculos' ? request('busqueda') : '' }}"
                                               placeholder="Buscar por marca o modelo"
                                               class="border rounded px-3 py-2 w-full sm:w-44 focus:ring-black focus:border-black text-sm">
                                        <select name="tipo" class="border rounded px-3 py-2 focus:ring-black focus:border-black text-sm">
                                            <option value="">Todos los tipos</option>
                                            <option value="carro" {{ request('tipo') == 'carro' ? 'selected' : '' }}>Carros</option>
                                            <option value="moto" {{ request('tipo') == 'moto' ? 'selected' : '' }}>Motos</option>
                                        </select>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="flex-1 sm:flex-none bg-white hover:bg-gray-200 text-black px-4 py-2 rounded shadow font-semibold border transition text-sm">
                                                Buscar
                                            </button>
                                            <a href="{{ route('empleados.index', ['tab' => 'vehiculos']) }}"
                                               class="flex-1 sm:flex-none text-center bg-white hover:bg-gray-200 text-black px-4 py-2 rounded shadow font-semibold border transition text-sm">
                                                Limpiar
                                            </a>
                                        </div>
                                    </form>
                                    <a href="{{ route('vehiculos.create') }}"
                                       class="text-center bg-white hover:bg-gray-200 text-black px-4 py-2 rounded shadow font-semibold border transition text-sm whitespace-nowrap self-start">
                                        + Agregar Vehículo
                                    </a>
                                </div>

                                @if($vehiculos->count() > 0)
                                    <!-- VISTA TABLA (lg+) -->
                                    <div class="hidden lg:block overflow-x-auto">
                                        <table class="table-fixed w-full border bg-white shadow rounded text-gray-800 text-sm">
                                            <colgroup>
                                                <col class="w-12">
                                                <col class="w-20">
                                                <col class="w-16">
                                                <col class="w-1/6">
                                                <col class="w-16">
                                                <col class="w-1/6">
                                                <col class="w-1/5">
                                                <col class="w-1/5">
                                            </colgroup>
                                            <thead class="bg-gray-300">
                                                <tr>
                                                    <th class="px-3 py-2 border text-left">ID</th>
                                                    <th class="px-3 py-2 border text-left">Imagen</th>
                                                    <th class="px-3 py-2 border text-left">Tipo</th>
                                                    <th class="px-3 py-2 border text-left">Marca</th>
                                                    <th class="px-3 py-2 border text-left">Modelo</th>
                                                    <th class="px-3 py-2 border text-left">Precio</th>
                                                    <th class="px-3 py-2 border text-left">Descripción</th>
                                                    <th class="px-3 py-2 border text-left">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($vehiculos as $vehiculo)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border px-3 py-2">{{ $vehiculo->id }}</td>
                                                        <td class="border px-3 py-2">
                                                            @if($vehiculo->imagen)
                                                                <img src="{{ asset('images/'.$vehiculo->imagen) }}" alt="Imagen" class="w-16 h-12 object-cover rounded">
                                                            @else
                                                                <span class="text-gray-400 text-xs">Sin imagen</span>
                                                            @endif
                                                        </td>
                                                        <td class="border px-3 py-2">{{ $vehiculo->tipo }}</td>
                                                        <td class="border px-3 py-2 truncate max-w-0" title="{{ $vehiculo->marca }}">{{ $vehiculo->marca }}</td>
                                                        <td class="border px-3 py-2">{{ $vehiculo->modelo }}</td>
                                                        <td class="border px-3 py-2">${{ number_format($vehiculo->precio, 2) }}</td>
                                                        <td class="border px-3 py-2 truncate max-w-0" title="{{ $vehiculo->descripcion }}">{{ $vehiculo->descripcion }}</td>
                                                        <td class="border px-3 py-2">
                                                            <div class="flex space-x-2">
                                                                <a href="{{ route('vehiculos.edit', $vehiculo->id) }}"
                                                                   class="bg-white hover:bg-gray-200 text-black px-2 py-1 rounded shadow font-semibold border transition text-xs">
                                                                    Editar
                                                                </a>
                                                                <form action="{{ route('vehiculos.destroy', $vehiculo->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" onclick="return confirm('¿Eliminar este vehículo?')"
                                                                            class="bg-white hover:bg-gray-200 text-black px-2 py-1 rounded shadow font-semibold border transition text-xs">
                                                                        Eliminar
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- VISTA TARJETAS (mobile/tablet) -->
                                    <div class="lg:hidden space-y-3">
                                        @foreach($vehiculos as $vehiculo)
                                            <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
                                                <div class="flex gap-3 p-3">
                                                    @if($vehiculo->imagen)
                                                        <img src="{{ asset('images/'.$vehiculo->imagen) }}" alt="Imagen" class="w-20 h-16 object-cover rounded-lg flex-shrink-0">
                                                    @else
                                                        <div class="w-20 h-16 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center">
                                                            <span class="text-gray-400 text-xs">Sin img</span>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <p class="font-bold text-gray-900 truncate">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                                                                <p class="text-xs text-gray-500 capitalize">{{ $vehiculo->tipo }} · ID {{ $vehiculo->id }}</p>
                                                            </div>
                                                            <span class="text-sm font-bold text-gray-800 whitespace-nowrap ml-2">${{ number_format($vehiculo->precio, 2) }}</span>
                                                        </div>
                                                        @if($vehiculo->descripcion)
                                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $vehiculo->descripcion }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex border-t">
                                                    <a href="{{ route('vehiculos.edit', $vehiculo->id) }}"
                                                       class="flex-1 text-center bg-white hover:bg-gray-100 text-black py-2 font-semibold border-r transition text-sm">
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('vehiculos.destroy', $vehiculo->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('¿Eliminar este vehículo?')"
                                                                class="w-full text-center bg-white hover:bg-gray-100 text-black py-2 font-semibold transition text-sm">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4">{{ $vehiculos->links() }}</div>
                                @else
                                    @if(request('busqueda') || request('tipo'))
                                        <div class="text-center py-12 text-gray-500 px-4">
                                            <p class="text-lg md:text-xl">🔍 No se encontraron vehículos
                                                @if(request('busqueda')) con "<strong>{{ request('busqueda') }}</strong>"@endif
                                                @if(request('tipo')) de tipo "<strong>{{ request('tipo') }}</strong>"@endif.
                                            </p>
                                            <p class="text-sm mt-2">Intenta con otra marca, modelo o tipo.</p>
                                            <a href="{{ route('empleados.index', ['tab' => 'vehiculos']) }}" class="inline-block mt-4 text-blue-600 underline text-sm">Limpiar búsqueda</a>
                                        </div>
                                    @else
                                        <div class="text-center py-12 text-gray-500 px-4">
                                            <p class="text-lg md:text-xl">🚗 No hay vehículos publicados aún.</p>
                                            <p class="text-sm mt-2">Haz clic en <strong>+ Agregar Vehículo</strong> para comenzar.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>
