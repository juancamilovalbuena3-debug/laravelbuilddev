<x-app-layout>
    @php
        // --- INICIO DE RECÁLCULO DINÁMICO ---
        $ventasRealesCarros = [];
        $ventasRealesMotos = [];
        $totalCarrosReales = 0;
        $totalMotosReales = 0;

        if(isset($compras) && is_iterable($compras)) {
            foreach($compras as $c) {
                $cant = $c->cantidad ?? 1;
                $tipo = strtolower($c->tipo ?? '');
                $vehiculo = $c->vehiculo ?? 'Desconocido';

                if($tipo === 'carro' || $tipo === 'carros') {
                    $ventasRealesCarros[$vehiculo] = ($ventasRealesCarros[$vehiculo] ?? 0) + $cant;
                    $totalCarrosReales += $cant;
                } else {
                    $ventasRealesMotos[$vehiculo] = ($ventasRealesMotos[$vehiculo] ?? 0) + $cant;
                    $totalMotosReales += $cant;
                }
            }
        }

        $reporte['ventas_por_vehiculo_carros'] = $ventasRealesCarros;
        $reporte['ventas_por_vehiculo_motos']  = $ventasRealesMotos;
        $statsLaravel['total_carros']          = $totalCarrosReales;
        $statsLaravel['total_motos']           = $totalMotosReales;
        $statsLaravel['total_ventas']          = $totalCarrosReales + $totalMotosReales;

        $todosReales = array_merge($ventasRealesCarros, $ventasRealesMotos);
        if (!empty($todosReales)) {
            arsort($todosReales);
            $topVehiculo = array_key_first($todosReales);
            $statsLaravel['top_vehiculo_mes']  = $topVehiculo;
            $statsLaravel['top_vehiculo_cant'] = $todosReales[$topVehiculo];
        }
        // --- FIN DE RECÁLCULO DINÁMICO ---
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 Dashboard de Reportes
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(isset($error) && $error)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ $error }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white shadow rounded-lg p-5 text-center border-b-4 border-blue-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Total Unidades Vendidas</h3>
                    <p class="text-2xl font-bold text-blue-600 mt-2" id="totalVentas">{{ $statsLaravel['total_ventas'] }}</p>
                </div>
                <div class="bg-white shadow rounded-lg p-5 text-center border-b-4 border-green-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Total Dinero</h3>
                    <p class="text-2xl font-bold text-green-600 mt-2" id="totalDinero">${{ number_format($statsLaravel['total_dinero'] ?? 0) }}</p>
                </div>
                <div class="bg-white shadow rounded-lg p-5 text-center border-b-4 border-indigo-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Carros Vendidos</h3>
                    <p class="text-2xl font-bold text-indigo-600 mt-2" id="totalCarros">{{ $statsLaravel['total_carros'] ?? 0 }}</p>
                </div>
                <div class="bg-white shadow rounded-lg p-5 text-center border-b-4 border-purple-500">
                    <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Motos Vendidas</h3>
                    <p class="text-2xl font-bold text-purple-600 mt-2" id="totalMotos">{{ $statsLaravel['total_motos'] ?? 0 }}</p>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 shadow rounded-lg p-5 text-center border-b-4 border-amber-500 relative overflow-hidden">
                    <span class="absolute top-1 right-2 text-xl">🔥</span>
                    <h3 class="text-amber-800 text-xs font-semibold uppercase tracking-wider">Top Este Mes</h3>
                    <p class="text-lg font-bold text-amber-950 mt-2 truncate" id="masVendido" title="{{ $statsLaravel['top_vehiculo_mes'] ?? 'Ninguno' }}">
                        {{ $statsLaravel['top_vehiculo_mes'] ?? 'Ninguno' }}
                    </p>
                    <span class="text-xs text-amber-700 font-semibold block mt-1" id="masVendidoCant">
                        ({{ $statsLaravel['top_vehiculo_cant'] ?? 0 }} uds)
                    </span>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-gray-700 text-base mb-4">🔍 Filtrar Reportes y Ventas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Buscar Comprador</label>
                        <input type="text" id="buscarInput" value="{{ request('buscar') }}"
                               placeholder="Nombre, CC o Documento..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Vehículo / Modelo</label>
                        <input type="text" id="vehiculoInput" value="{{ request('vehiculo') }}"
                               placeholder="Ej: Mazda 3, KTM, Yamaha..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="aplicarFiltros()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow transition">
                            Aplicar Filtros
                        </button>
                        <button type="button" onclick="limpiarFiltros()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition text-center">
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold mb-4">📈 Ventas por fecha</h3>
                    <canvas id="ventasChart"></canvas>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold mb-4">🥧 Carros vs Motos</h3>
                    <canvas id="tipoChart"></canvas>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold mb-4">💰 Dinero por venta</h3>
                    <canvas id="dineroChart"></canvas>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold mb-4">🔥 Vehículos vendidos</h3>
                    <canvas id="vehiculosChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold text-base mb-4 text-indigo-700">🚗 Disponibilidad Carros <span class="text-xs text-gray-400 font-normal">(máx. 50 por referencia)</span></h3>
                    @if(isset($reporte['ventas_por_vehiculo_carros']) && count($reporte['ventas_por_vehiculo_carros']) > 0)
                        <div class="space-y-3">
                            @foreach($reporte['ventas_por_vehiculo_carros'] as $vehiculo => $total)
                                @php
                                    $pct  = min(100, round(($total / 50) * 100));
                                    $disp = max(0, 50 - $total);
                                    $bar  = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-indigo-500');
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-semibold text-gray-700 truncate max-w-[60%]">{{ $vehiculo }}</span>
                                        <span class="text-gray-500">{{ $total }} vendidos — <strong class="{{ $disp <= 10 ? 'text-red-600' : 'text-green-700' }}">{{ $disp }} disp.</strong></span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="{{ $bar }} h-2 rounded-full" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Sin ventas registradas.</p>
                    @endif
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="font-semibold text-base mb-4 text-purple-700">🏍️ Disponibilidad Motos <span class="text-xs text-gray-400 font-normal">(máx. 50 por referencia)</span></h3>
                    @if(isset($reporte['ventas_por_vehiculo_motos']) && count($reporte['ventas_por_vehiculo_motos']) > 0)
                        <div class="space-y-3">
                            @foreach($reporte['ventas_por_vehiculo_motos'] as $vehiculo => $total)
                                @php
                                    $pct  = min(100, round(($total / 50) * 100));
                                    $disp = max(0, 50 - $total);
                                    $bar  = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-purple-500');
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-semibold text-gray-700 truncate max-w-[60%]">{{ $vehiculo }}</span>
                                        <span class="text-gray-500">{{ $total }} vendidos — <strong class="{{ $disp <= 10 ? 'text-red-600' : 'text-green-700' }}">{{ $disp }} disp.</strong></span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="{{ $bar }} h-2 rounded-full" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Sin ventas registradas.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">📄 Compras registradas con documento</h3>
                    <span class="text-xs font-bold px-2.5 py-1 bg-gray-100 text-gray-800 rounded-full">
                        Resultados: <span id="resultCount">{{ count($compras) }}</span>
                    </span>
                </div>

                <div id="tablaContainer">
                    @if(isset($compras) && count($compras) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full border text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-2 border">ID</th>
                                        <th class="p-2 border">Vehículo</th>
                                        <th class="p-2 border">Tipo</th>
                                        <th class="p-2 border">Cant.</th>
                                        <th class="p-2 border">P. Unitario</th>
                                        <th class="p-2 border">Importe Total</th>
                                        <th class="p-2 border">Comprador</th>
                                        <th class="p-2 border">Documento</th>
                                        <th class="p-2 border">Teléfono</th>
                                        <th class="p-2 border">Color</th>
                                        <th class="p-2 border">Método Pago</th>
                                        <th class="p-2 border">Dirección</th>
                                        <th class="p-2 border">Fecha</th>
                                        <th class="p-2 border">PDF</th>
                                        @if(auth()->user()->role === 'admin')
                                            <th class="p-2 border">Eliminar</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($compras as $c)
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="p-2 border text-center">{{ $c->id }}</td>
                                            <td class="p-2 border font-semibold">{{ $c->vehiculo }}</td>
                                            <td class="p-2 border text-center">{{ ucfirst($c->tipo) }}</td>
                                            <td class="p-2 border text-center font-bold bg-gray-50/50">{{ $c->cantidad ?? 1 }}</td>
                                            <td class="p-2 border text-center">${{ number_format($c->precio_unitario ?? 0) }}</td>
                                            <td class="p-2 border text-green-600 font-bold text-center bg-green-50/20">
                                                ${{ number_format(($c->precio_unitario ?? 0) * ($c->cantidad ?? 1)) }}
                                            </td>
                                            <td class="p-2 border">{{ $c->nombre_comprador ?? '-' }}</td>
                                            <td class="p-2 border text-center">{{ $c->documento ?? '-' }}</td>
                                            <td class="p-2 border text-center">{{ $c->telefono ?? '-' }}</td>
                                            <td class="p-2 border text-center">{{ $c->color ?? '-' }}</td>
                                            <td class="p-2 border text-center">{{ $c->metodo_pago ?? '-' }}</td>
                                            <td class="p-2 border">{{ $c->direccion ?? '-' }}</td>
                                            <td class="p-2 border text-center">{{ $c->created_at ? $c->created_at->format('Y-m-d H:i') : '-' }}</td>
                                            <td class="p-2 border text-center">
                                                <a href="{{ route('compras.pdf', $c->id) }}" target="_blank"
                                                   class="bg-white hover:bg-gray-100 text-black border border-gray-300 px-3 py-1 rounded text-xs shadow">
                                                    📄 Ver PDF
                                                </a>
                                            </td>
                                            @if(auth()->user()->role === 'admin')
                                                <td class="p-2 border text-center">
                                                    <form action="{{ route('compras.eliminar', $c->id) }}" method="POST"
                                                          onsubmit="return confirm('¿Eliminar esta compra?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs shadow">
                                                            🗑️ Eliminar
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-lg">📭 No hay compras registradas con los filtros aplicados.</p>
                            <p class="text-sm mt-1">Intenta limpiar los filtros o realizar una búsqueda distinta.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const PDF_BASE_URL      = '{{ rtrim(url("/compras"), "/") }}';
        const ELIMINAR_BASE_URL = '{{ rtrim(url("/compras"), "/") }}';
        const CSRF_TOKEN        = '{{ csrf_token() }}';

        const ventasPorMes   = @json($reporte['ventas_por_mes'] ?? []);
        const carrosVendidos = @json($reporte['ventas_por_vehiculo_carros'] ?? null) ?? {};
        const motosVendidas  = @json($reporte['ventas_por_vehiculo_motos'] ?? null) ?? {};
        const totalCarros    = {{ $statsLaravel['total_carros'] ?? 0 }};
        const totalMotos     = {{ $statsLaravel['total_motos'] ?? 0 }};
        const API_URL        = 'http://localhost:8080';
        const IS_ADMIN       = {{ auth()->user()->role === 'admin' ? 'true' : 'false' }};

        let chartInstances = {};

        document.addEventListener('DOMContentLoaded', () => {
            actualizarGraficasInicial();

            // ── Bloqueo de espacios en inputs de filtro ───────────
            // buscarInput y vehiculoInput: bloquear espacio solo al inicio
            ['buscarInput', 'vehiculoInput'].forEach(function(id) {
                const el = document.getElementById(id);

                el.addEventListener('keydown', function(e) {
                    if (e.key === ' ' && this.selectionStart === 0) e.preventDefault();
                });

                el.addEventListener('input', function() {
                    if (this.value.startsWith(' ')) {
                        const pos = this.selectionStart - 1;
                        this.value = this.value.trimStart();
                        const newPos = Math.max(0, pos);
                        this.setSelectionRange(newPos, newPos);
                    }
                });
            });
        });

        function actualizarGraficasInicial() {
            actualizarChart('ventasChart', {
                type: 'line',
                data: {
                    labels: ventasPorMes.map(v => v.mes),
                    datasets: [{ label: 'Ventas', data: ventasPorMes.map(v => v.cantidad), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', borderWidth: 3, tension: 0.3, fill: true }]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });

            actualizarChart('tipoChart', {
                type: 'pie',
                data: {
                    labels: ['Carros', 'Motos'],
                    datasets: [{ data: [totalCarros, totalMotos], backgroundColor: ['#3b82f6', '#ec4899'] }]
                },
                options: { responsive: true }
            });

            actualizarChart('dineroChart', {
                type: 'bar',
                data: {
                    labels: ventasPorMes.map(v => v.mes),
                    datasets: [{ label: 'Precio venta', data: ventasPorMes.map(v => v.total), backgroundColor: '#10b981' }]
                },
                options: { responsive: true }
            });

            const todosVehiculos = { ...carrosVendidos, ...motosVendidas };
            actualizarChart('vehiculosChart', {
                type: 'bar',
                data: {
                    labels: Object.keys(todosVehiculos),
                    datasets: [{ label: 'Vehículos vendidos', data: Object.values(todosVehiculos), backgroundColor: '#f59e0b' }]
                },
                options: { responsive: true, indexAxis: 'y' }
            });
        }

        function aplicarFiltros() {
            const busqueda = document.getElementById('buscarInput').value.trim();
            const vehiculo = document.getElementById('vehiculoInput').value.trim();
            const params = new URLSearchParams();
            if (busqueda) params.append('busqueda', busqueda);
            if (vehiculo) params.append('vehiculo', vehiculo);
            window.location.href = '/reportes?' + params.toString();
        }

        function limpiarFiltros() {
            window.location.href = '/reportes';
        }

        function actualizarChart(canvasId, config) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            if (chartInstances[canvasId]) chartInstances[canvasId].destroy();
            chartInstances[canvasId] = new Chart(ctx, config);
        }
    </script>

</x-app-layout>
