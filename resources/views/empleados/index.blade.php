<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black-800 leading-tight">
            {{ __('Gestión de Empleados y Vehículos') }}
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
                        <li><a href="{{ route('empleados.index') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Empleados</a></li>
                        <li><a href="{{ route('carros') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Carros</a></li>
                        <li><a href="{{ route('motos') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Motos</a></li>
                        <li><a href="{{ route('vender') }}" class="block py-2 px-3 rounded bg-white border text-black font-semibold text-sm">Vender Vehículo</a></li>
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
                            <li><a href="{{ route('empleados.index') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Empleados</a></li>
                            <li><a href="{{ route('carros') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Carros</a></li>
                            <li><a href="{{ route('motos') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Motos</a></li>
                            <li><a href="{{ route('vender') }}" class="block py-1.5 text-black font-semibold text-sm hover:underline">Vender Vehículo</a></li>
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
                        <h1 class="text-xl sm:text-2xl font-bold mb-4">Gestión de Empleados y Vehículos</h1>

                        @if(session('error'))
                            <div class="mb-4 flex items-start space-x-3 p-4 bg-yellow-50 border border-yellow-300 rounded-xl shadow text-yellow-800">
                                <span class="text-xl mt-0.5">⚠️</span>
                                <div>
                                    <p class="font-semibold text-sm">{{ session('error') }}</p>
                                    <p class="text-xs mt-1">Asegúrate de ejecutar: <code class="bg-yellow-100 px-2 py-0.5 rounded font-mono">python main.py</code></p>
                                </div>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Pestañas -->
                        <div x-data="{ tab: '{{ request('tab') === 'vehiculos' ? 'vehiculos' : 'empleados' }}' }">
                            <div class="flex space-x-4 border-b mb-5">
                                <button
                                    @click="tab = 'empleados'"
                                    :class="tab === 'empleados' ? 'border-b-2 border-black text-black font-semibold' : 'text-gray-500 hover:text-black'"
                                    class="pb-2 text-sm sm:text-base transition">
                                    Empleados
                                </button>
                                <button
                                    @click="tab = 'vehiculos'"
                                    :class="tab === 'vehiculos' ? 'border-b-2 border-black text-black font-semibold' : 'text-gray-500 hover:text-black'"
                                    class="pb-2 text-sm sm:text-base transition">
                                    Vehículos
                                </button>
                            </div>

                            <!-- ===== TAB EMPLEADOS ===== -->
                            <div x-show="tab === 'empleados'">

                                <!-- Header + exportar -->
                                <div class="flex flex-wrap gap-2 justify-between items-center mb-4">
                                    <h2 class="text-lg font-semibold">Lista de Empleados</h2>
                                    <div class="flex gap-2">
                                        <a href="{{ route('empleados.export.pdf', ['busqueda' => request('tab') === 'empleados' ? request('busqueda') : '']) }}"
                                           class="bg-white hover:bg-gray-100 text-black px-3 py-1.5 rounded shadow text-xs font-semibold border transition">
                                            PDF
                                        </a>
                                        <a href="{{ route('empleados.export.csv', ['busqueda' => request('tab') === 'empleados' ? request('busqueda') : '']) }}"
                                           class="bg-white hover:bg-gray-100 text-black px-3 py-1.5 rounded shadow text-xs font-semibold border transition">
                                            CSV
                                        </a>
                                    </div>
                                </div>

                                <!-- Buscador empleados -->
                                <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:justify-between sm:items-center">
                                    <form method="GET" action="{{ route('empleados.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        <input type="hidden" name="tab" value="empleados">
                                        <input id="busqueda_empleados" type="text" name="busqueda" value="{{ request('tab') !== 'vehiculos' ? request('busqueda') : '' }}"
                                               placeholder="Buscar por nombre o correo"
                                               class="border rounded px-3 py-2 text-sm w-full sm:w-56 focus:ring-black focus:border-black">
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="flex-1 sm:flex-none bg-white hover:bg-gray-100 text-black px-4 py-2 rounded shadow font-semibold border text-sm transition">
                                                Buscar
                                            </button>
                                            <a href="{{ route('empleados.index') }}"
                                               class="flex-1 sm:flex-none text-center bg-white hover:bg-gray-100 text-black px-4 py-2 rounded shadow font-semibold border text-sm transition">
                                                Limpiar
                                            </a>
                                        </div>
                                    </form>
                                    <a href="{{ route('empleados.create') }}"
                                       class="text-center bg-black hover:bg-gray-800 text-white px-4 py-2 rounded shadow font-semibold text-sm transition">
                                        + Agregar Empleado
                                    </a>
                                </div>

                                @if($empleados->count() > 0)
                                    <!-- Tarjetas móvil -->
                                    <div class="block sm:hidden space-y-3">
                                        @foreach($empleados as $empleado)
                                            <div class="border rounded-lg p-4 bg-white shadow-sm">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <p class="font-bold text-base">{{ $empleado->nombre }}</p>
                                                        <p class="text-gray-500 text-sm">{{ $empleado->puesto }}</p>
                                                    </div>
                                                    <span class="text-sm font-semibold bg-gray-100 px-2 py-1 rounded">${{ number_format($empleado->salario, 2) }}</span>
                                                </div>
                                                <p class="text-xs text-gray-500 truncate mb-3">{{ $empleado->email }}</p>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('empleados.edit', $empleado->id) }}"
                                                       class="flex-1 text-center bg-white hover:bg-gray-100 text-black px-3 py-1.5 rounded shadow font-semibold border text-sm transition">
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('¿Eliminar este empleado?')"
                                                                class="w-full bg-white hover:bg-red-50 text-red-600 px-3 py-1.5 rounded shadow font-semibold border border-red-200 text-sm transition">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                    <!-- BOTÓN CREAR ACCESO MÓVIL -->
                                                    <button onclick="abrirModalAcceso({{ $empleado->id }}, '{{ $empleado->email }}')"
                                                            class="flex-1 bg-white hover:bg-green-50 text-green-700 px-3 py-1.5 rounded shadow font-semibold border border-green-200 text-sm transition">
                                                        Acceso
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Tabla desktop -->
                                    <div class="hidden sm:block overflow-x-auto">
                                        <table class="w-full border bg-white shadow rounded text-gray-800 text-sm">
                                            <thead class="bg-gray-200">
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
                                                        <td class="border px-4 py-2">{{ $empleado->nombre }}</td>
                                                        <td class="border px-4 py-2">{{ $empleado->puesto }}</td>
                                                        <td class="border px-4 py-2">${{ number_format($empleado->salario, 2) }}</td>
                                                        <td class="border px-4 py-2 max-w-xs truncate" title="{{ $empleado->email }}">{{ $empleado->email }}</td>
                                                        <td class="border px-4 py-2">
                                                            <div class="flex gap-2">
                                                                <a href="{{ route('empleados.edit', $empleado->id) }}"
                                                                   class="bg-white hover:bg-gray-100 text-black px-3 py-1 rounded shadow font-semibold border text-xs transition">
                                                                    Editar
                                                                </a>
                                                                <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" onclick="return confirm('¿Eliminar este empleado?')"
                                                                            class="bg-white hover:bg-red-50 text-red-600 px-3 py-1 rounded shadow font-semibold border border-red-200 text-xs transition">
                                                                        Eliminar
                                                                    </button>
                                                                </form>
                                                                <!-- BOTÓN CREAR ACCESO DESKTOP -->
                                                                <button onclick="abrirModalAcceso({{ $empleado->id }}, '{{ $empleado->email }}')"
                                                                        class="bg-white hover:bg-green-50 text-green-700 px-3 py-1 rounded shadow font-semibold border border-green-200 text-xs transition">
                                                                    Acceso
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4">{{ $empleados->links() }}</div>
                                @else
                                    @if(request('busqueda') && request('tab') !== 'vehiculos')
                                        <div class="text-center py-12 text-gray-500">
                                            <p class="text-lg">No se encontraron empleados con "<strong>{{ request('busqueda') }}</strong>".</p>
                                            <p class="text-sm mt-2">Intenta con otro nombre o correo.</p>
                                            <a href="{{ route('empleados.index') }}" class="inline-block mt-4 text-blue-600 underline text-sm">Limpiar búsqueda</a>
                                        </div>
                                    @else
                                        <div class="text-center py-12 text-gray-500">
                                            <p class="text-lg">No hay empleados registrados aún.</p>
                                            <p class="text-sm mt-2">Haz clic en <strong>+ Agregar Empleado</strong> para comenzar.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- ===== TAB VEHÍCULOS ===== -->
                            <div x-show="tab === 'vehiculos'">

                                <!-- Header + exportar -->
                                <div class="flex flex-wrap gap-2 justify-between items-center mb-4">
                                    <h2 class="text-lg font-semibold">Vehículos Publicados</h2>
                                    <div class="flex gap-2">
                                        <a href="{{ route('vehiculos.export.pdf', ['busqueda' => request('busqueda'), 'tipo' => request('tipo')]) }}"
                                           class="bg-white hover:bg-gray-100 text-black px-3 py-1.5 rounded shadow text-xs font-semibold border transition">
                                            PDF
                                        </a>
                                        <a href="{{ route('vehiculos.export.csv', ['busqueda' => request('busqueda'), 'tipo' => request('tipo')]) }}"
                                           class="bg-white hover:bg-gray-100 text-black px-3 py-1.5 rounded shadow text-xs font-semibold border transition">
                                            CSV
                                        </a>
                                    </div>
                                </div>

                                <!-- Buscador vehículos -->
                                <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:justify-between sm:items-center">
                                    <form method="GET" action="{{ route('empleados.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        <input type="hidden" name="tab" value="vehiculos">
                                        <input id="busqueda_vehiculos" type="text" name="busqueda" value="{{ request('tab') === 'vehiculos' ? request('busqueda') : '' }}"
                                               placeholder="Buscar por marca o modelo"
                                               class="border rounded px-3 py-2 text-sm w-full sm:w-48 focus:ring-black focus:border-black">
                                        <select name="tipo" class="border rounded px-3 py-2 text-sm focus:ring-black focus:border-black">
                                            <option value="">Todos los tipos</option>
                                            <option value="carro" {{ request('tipo') == 'carro' ? 'selected' : '' }}>Carros</option>
                                            <option value="moto" {{ request('tipo') == 'moto' ? 'selected' : '' }}>Motos</option>
                                        </select>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="flex-1 sm:flex-none bg-white hover:bg-gray-100 text-black px-4 py-2 rounded shadow font-semibold border text-sm transition">
                                                Buscar
                                            </button>
                                            <a href="{{ route('empleados.index', ['tab' => 'vehiculos']) }}"
                                               class="flex-1 sm:flex-none text-center bg-white hover:bg-gray-100 text-black px-4 py-2 rounded shadow font-semibold border text-sm transition">
                                                Limpiar
                                            </a>
                                        </div>
                                    </form>
                                    <a href="{{ route('vehiculos.create') }}"
                                       class="text-center bg-black hover:bg-gray-800 text-white px-4 py-2 rounded shadow font-semibold text-sm transition">
                                        + Agregar Vehículo
                                    </a>
                                </div>

                                @if($vehiculos->count() > 0)
                                    <!-- Tarjetas móvil -->
                                    <div class="block sm:hidden space-y-3">
                                        @foreach($vehiculos as $vehiculo)
                                            <div class="border rounded-lg p-4 bg-white shadow-sm">
                                                <div class="flex gap-3 mb-3">
                                                    @if($vehiculo->imagen)
                                                        <img src="{{ asset('images/'.$vehiculo->imagen) }}" alt="Imagen" class="w-20 h-16 object-cover rounded shrink-0">
                                                    @else
                                                        <div class="w-20 h-16 bg-gray-100 rounded flex items-center justify-center shrink-0">
                                                            <span class="text-gray-400 text-xs">Sin img</span>
                                                        </div>
                                                    @endif
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="text-xs bg-gray-200 px-2 py-0.5 rounded font-medium">{{ $vehiculo->tipo }}</span>
                                                            <span class="text-xs text-gray-400">#{{ $vehiculo->id }}</span>
                                                        </div>
                                                        <p class="font-bold text-sm truncate">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                                                        <p class="text-green-700 font-semibold text-sm">${{ number_format($vehiculo->precio, 2) }}</p>
                                                        <p class="text-xs text-gray-500 truncate mt-1">{{ $vehiculo->descripcion }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('vehiculos.edit', $vehiculo->id) }}"
                                                       class="flex-1 text-center bg-white hover:bg-gray-100 text-black px-3 py-1.5 rounded shadow font-semibold border text-sm transition">
                                                        Editar
                                                    </a>
                                                    <form action="{{ route('vehiculos.destroy', $vehiculo->id) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('¿Eliminar este vehículo?')"
                                                                class="w-full bg-white hover:bg-red-50 text-red-600 px-3 py-1.5 rounded shadow font-semibold border border-red-200 text-sm transition">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Tabla desktop -->
                                    <div class="hidden sm:block overflow-x-auto">
                                        <table class="w-full border bg-white shadow rounded text-gray-800 text-sm">
                                            <thead class="bg-gray-200">
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
                                                        <td class="border px-3 py-2 max-w-[120px] truncate" title="{{ $vehiculo->marca }}">{{ $vehiculo->marca }}</td>
                                                        <td class="border px-3 py-2">{{ $vehiculo->modelo }}</td>
                                                        <td class="border px-3 py-2">${{ number_format($vehiculo->precio, 2) }}</td>
                                                        <td class="border px-3 py-2 max-w-[150px] truncate" title="{{ $vehiculo->descripcion }}">{{ $vehiculo->descripcion }}</td>
                                                        <td class="border px-3 py-2">
                                                            <div class="flex gap-2">
                                                                <a href="{{ route('vehiculos.edit', $vehiculo->id) }}"
                                                                   class="bg-white hover:bg-gray-100 text-black px-3 py-1 rounded shadow font-semibold border text-xs transition">
                                                                    Editar
                                                                </a>
                                                                <form action="{{ route('vehiculos.destroy', $vehiculo->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" onclick="return confirm('¿Eliminar este vehículo?')"
                                                                            class="bg-white hover:bg-red-50 text-red-600 px-3 py-1 rounded shadow font-semibold border border-red-200 text-xs transition">
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
                                    <div class="mt-4">{{ $vehiculos->links() }}</div>
                                @else
                                    @if(request('busqueda') || request('tipo'))
                                        <div class="text-center py-12 text-gray-500">
                                            <p class="text-lg">🔍 No se encontraron vehículos
                                                @if(request('busqueda')) con "<strong>{{ request('busqueda') }}</strong>"@endif
                                                @if(request('tipo')) de tipo "<strong>{{ request('tipo') }}</strong>"@endif.
                                            </p>
                                            <p class="text-sm mt-2">Intenta con otra marca, modelo o tipo.</p>
                                            <a href="{{ route('empleados.index', ['tab' => 'vehiculos']) }}" class="inline-block mt-4 text-blue-600 underline text-sm">Limpiar búsqueda</a>
                                        </div>
                                    @else
                                        <div class="text-center py-12 text-gray-500">
                                            <p class="text-lg">🚗 No hay vehículos publicados aún.</p>
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

    <!-- ===== MODAL CREAR ACCESO ===== -->
    <div id="modalAcceso" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:2rem; border-radius:8px; width:400px; max-width:90%;">
            <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.2rem;">Crear acceso al sistema</h3>

            <div style="margin-bottom:1rem;">
                <label style="font-size:0.85rem; font-weight:600; display:block; margin-bottom:4px;">Correo electrónico</label>
                <input type="email" id="acceso_email"
                    style="width:100%; padding:0.5rem 0.75rem; border:1px solid #d1d5db; border-radius:6px; font-size:0.875rem; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:1.2rem;">
                <label style="font-size:0.85rem; font-weight:600; display:block; margin-bottom:4px;">Contraseña</label>
                <input type="password" id="acceso_password"
                    style="width:100%; padding:0.5rem 0.75rem; border:1px solid #d1d5db; border-radius:6px; font-size:0.875rem; box-sizing:border-box;">
            </div>

            <div id="acceso_error" style="display:none; color:#dc2626; font-size:0.8rem; margin-bottom:0.75rem;"></div>
            <div id="acceso_success" style="display:none; color:#16a34a; font-size:0.8rem; margin-bottom:0.75rem;"></div>

            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                <button onclick="cerrarModalAcceso()"
                    style="padding:0.5rem 1rem; border:1px solid #d1d5db; border-radius:6px; background:white; font-size:0.875rem; font-weight:600; cursor:pointer;">
                    Cancelar
                </button>
                <button onclick="guardarAcceso()"
                    style="padding:0.5rem 1rem; background:#000; color:white; border:none; border-radius:6px; font-size:0.875rem; font-weight:600; cursor:pointer;">
                    Crear acceso
                </button>
            </div>
        </div>
    </div>

    <script>
        // ===== BLOQUEO ESPACIOS AL INICIO (desktop + móvil + Chrome + paste) =====
       function noLeadingSpaces(el) {
    function trim() {
        setTimeout(function() {
            // Sin espacios al inicio
            el.value = el.value.replace(/^\s+/, '');
            // Máximo 3 espacios consecutivos en cualquier parte
            el.value = el.value.replace(/ {4,}/g, '   ');
        }, 0);
    }
    el.addEventListener('input',          trim);
    el.addEventListener('keydown',        trim);
    el.addEventListener('paste',          trim);
    el.addEventListener('compositionend', trim);
}
        document.addEventListener('DOMContentLoaded', function () {
            var emp = document.getElementById('busqueda_empleados');
            var veh = document.getElementById('busqueda_vehiculos');
            if (emp) noLeadingSpaces(emp);
            if (veh) noLeadingSpaces(veh);
        });

        // ===== MODAL ACCESO =====
        let _accesoEmpleadoId = null;

        function abrirModalAcceso(id, email) {
            _accesoEmpleadoId = id;
            document.getElementById('acceso_email').value = email || '';
            document.getElementById('acceso_password').value = '';
            document.getElementById('acceso_error').style.display = 'none';
            document.getElementById('acceso_success').style.display = 'none';
            document.getElementById('modalAcceso').style.display = 'flex';
        }

        function cerrarModalAcceso() {
            document.getElementById('modalAcceso').style.display = 'none';
        }

        async function guardarAcceso() {
            const email = document.getElementById('acceso_email').value.trim();
            const password = document.getElementById('acceso_password').value;
            const errorDiv = document.getElementById('acceso_error');
            const successDiv = document.getElementById('acceso_success');

            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';

            if (!email || !password) {
                errorDiv.textContent = 'Por favor completa todos los campos.';
                errorDiv.style.display = 'block';
                return;
            }

            try {
                const response = await fetch('{{ route("empleados.crearAcceso") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        empleado_id: _accesoEmpleadoId,
                        email: email,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    successDiv.textContent = '✅ Acceso creado correctamente.';
                    successDiv.style.display = 'block';
                    setTimeout(() => cerrarModalAcceso(), 1500);
                } else {
                    errorDiv.textContent = data.message || 'Error al crear el acceso.';
                    errorDiv.style.display = 'block';
                }
            } catch (e) {
                errorDiv.textContent = 'Error de conexión. Inténtalo de nuevo.';
                errorDiv.style.display = 'block';
            }
        }
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>
