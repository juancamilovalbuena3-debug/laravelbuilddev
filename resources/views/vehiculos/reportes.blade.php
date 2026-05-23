<x-app-layout>

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

            <!-- TARJETAS DE STATS -->
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

            <!-- FILTROS -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-gray-700 text-base mb-4">🔍 Filtrar Reportes y Ventas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Buscar Comprador</label>
                        <input type="text" 
                               id="buscarInput"
                               value="{{ request('buscar') }}" 
                               placeholder="Nombre, CC o Documento..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Vehículo / Modelo</label>
                        <input type="text" 
                               id="vehiculoInput"
                               value="{{ request('vehiculo') }}" 
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

            <!-- GRÁFICAS -->
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

            <!-- TABLA -->
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
        // DATOS INICIALES DE LARAVEL
        const ventasPorMes   = @json($reporte['ventas_por_mes'] ?? []);
        const carrosVendidos = @json($reporte['ventas_por_vehiculo_carros'] ?? null) ?? {};
        const motosVendidas  = @json($reporte['ventas_por_vehiculo_motos'] ?? null) ?? {};
        const totalCarros = {{ $statsLaravel['total_carros'] ?? 0 }};
        const totalMotos  = {{ $statsLaravel['total_motos'] ?? 0 }};

        const API_URL = 'http://localhost:8080';
        let chartInstances = {};

        // Inicializar gráficas
        document.addEventListener('DOMContentLoaded', () => {
            actualizarGraficasInicial();
        });

        // GRÁFICAS CON DATOS INICIALES DE LARAVEL
        function actualizarGraficasInicial() {
            // 📈 Ventas por mes (línea)
            actualizarChart('ventasChart', {
                type: 'line',
                data: {
                    labels: ventasPorMes.map(v => v.mes),
                    datasets: [{ 
                        label: 'Ventas', 
                        data: ventasPorMes.map(v => v.cantidad), 
                        borderColor: '#3b82f6', 
                        backgroundColor: 'rgba(59,130,246,0.1)', 
                        borderWidth: 3, 
                        tension: 0.3, 
                        fill: true 
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });

            // 🥧 Carros vs Motos (pie)
            actualizarChart('tipoChart', {
                type: 'pie',
                data: {
                    labels: ['Carros', 'Motos'],
                    datasets: [{ 
                        data: [totalCarros, totalMotos], 
                        backgroundColor: ['#3b82f6', '#ec4899'] 
                    }]
                },
                options: { responsive: true }
            });

            // 💰 Dinero por mes (barras)
            actualizarChart('dineroChart', {
                type: 'bar',
                data: {
                    labels: ventasPorMes.map(v => v.mes),
                    datasets: [{ 
                        label: 'Precio venta', 
                        data: ventasPorMes.map(v => v.total), 
                        backgroundColor: '#10b981' 
                    }]
                },
                options: { responsive: true }
            });

            // 🔥 Vehículos vendidos (barras horizontales)
            const todosVehiculos = { ...carrosVendidos, ...motosVendidas };
            actualizarChart('vehiculosChart', {
                type: 'bar',
                data: {
                    labels: Object.keys(todosVehiculos),
                    datasets: [{ 
                        label: 'Vehículos vendidos', 
                        data: Object.values(todosVehiculos), 
                        backgroundColor: '#f59e0b' 
                    }]
                },
                options: { responsive: true, indexAxis: 'y' }
            });
        }

        // FUNCIÓN PARA APLICAR FILTROS
        async function aplicarFiltros() {
            const busqueda = document.getElementById('buscarInput').value;
            const vehiculo = document.getElementById('vehiculoInput').value;

            try {
                const params = new URLSearchParams();
                if (busqueda.trim()) params.append('busqueda', busqueda.trim());
                if (vehiculo.trim()) params.append('vehiculo', vehiculo.trim());

                const url = `${API_URL}/reporte${params.toString() ? '?' + params.toString() : ''}`;
                
                const response = await fetch(url);
                if (!response.ok) throw new Error(`Error HTTP ${response.status}`);
                
                const data = await response.json();

                // Actualizar tarjetas
                document.getElementById('totalVentas').textContent = data.total_ventas || 0;
                document.getElementById('totalDinero').textContent = 
                    '$' + (data.total_dinero || 0).toLocaleString('es-CO');
                document.getElementById('totalCarros').textContent = data.total_carros || 0;
                document.getElementById('totalMotos').textContent = data.total_motos || 0;
                
                const masVendido = data.mas_vendido || 'Ninguno';
                document.getElementById('masVendido').textContent = masVendido;
                document.getElementById('masVendido').title = masVendido;
                
                let cantMasVendido = 0;
                if (data.mas_vendido) {
                    cantMasVendido = (data.ventas_por_vehiculo_carros?.[data.mas_vendido] || 0) + 
                                     (data.ventas_por_vehiculo_motos?.[data.mas_vendido] || 0);
                }
                document.getElementById('masVendidoCant').textContent = `(${cantMasVendido} uds)`;

                // Actualizar gráficas con datos filtrados
                actualizarGraficasFiltradasDesdeAPI(data);

                // Actualizar tabla con datos filtrados
                actualizarTablaDesdeAPI(data.listado || []);

            } catch (error) {
                console.error('Error:', error);
                alert('Error al aplicar filtros: ' + error.message);
            }
        }

        // LIMPIAR FILTROS
        function limpiarFiltros() {
            document.getElementById('buscarInput').value = '';
            document.getElementById('vehiculoInput').value = '';
            
            // Restaurar datos originales
            document.getElementById('totalVentas').textContent = totalCarros + totalMotos;
            document.getElementById('totalCarros').textContent = totalCarros;
            document.getElementById('totalMotos').textContent = totalMotos;
            
            actualizarGraficasInicial();
            
            // Restaurar tabla original
            location.reload();
        }

        // ACTUALIZAR GRÁFICAS DESDE API
        function actualizarGraficasFiltradasDesdeAPI(data) {
            const ventasPorMesFiltrado = data.ventas_por_mes || [];

            // 📈 Ventas por mes (línea)
            actualizarChart('ventasChart', {
                type: 'line',
                data: {
                    labels: ventasPorMesFiltrado.map(v => v.mes),
                    datasets: [{
                        label: 'Ventas',
                        data: ventasPorMesFiltrado.map(v => v.cantidad),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });

            // 🥧 Carros vs Motos (pie)
            actualizarChart('tipoChart', {
                type: 'pie',
                data: {
                    labels: ['Carros', 'Motos'],
                    datasets: [{
                        data: [data.total_carros || 0, data.total_motos || 0],
                        backgroundColor: ['#3b82f6', '#ec4899']
                    }]
                },
                options: { responsive: true }
            });

            // 💰 Dinero por mes (barras)
            actualizarChart('dineroChart', {
                type: 'bar',
                data: {
                    labels: ventasPorMesFiltrado.map(v => v.mes),
                    datasets: [{
                        label: 'Precio venta',
                        data: ventasPorMesFiltrado.map(v => v.total),
                        backgroundColor: '#10b981'
                    }]
                },
                options: { responsive: true }
            });

            // 🔥 Vehículos vendidos (barras horizontales)
            const todosVehiculos = {
                ...(data.ventas_por_vehiculo_carros || {}),
                ...(data.ventas_por_vehiculo_motos || {})
            };
            actualizarChart('vehiculosChart', {
                type: 'bar',
                data: {
                    labels: Object.keys(todosVehiculos).length > 0 ? Object.keys(todosVehiculos) : ['Sin datos'],
                    datasets: [{
                        label: 'Vehículos vendidos',
                        data: Object.values(todosVehiculos).length > 0 ? Object.values(todosVehiculos) : [0],
                        backgroundColor: '#f59e0b'
                    }]
                },
                options: { responsive: true, indexAxis: 'y' }
            });
        }

        // ACTUALIZAR TABLA DESDE API
        function actualizarTablaDesdeAPI(listado) {
            const container = document.getElementById('tablaContainer');
            document.getElementById('resultCount').textContent = listado.length;

            if (!listado || listado.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-lg">📭 No hay compras registradas con los filtros aplicados.</p>
                        <p class="text-sm mt-1">Intenta limpiar los filtros o realizar una búsqueda distinta.</p>
                    </div>
                `;
                return;
            }

            let html = `
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
                                <th class="p-2 border">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            listado.forEach(c => {
                const precioUnitario = Math.floor(c.precio / (c.cantidad || 1));
                const importeTotal = c.precio;
                
                html += `
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-2 border text-center">${c.id_compra}</td>
                        <td class="p-2 border font-semibold">${c.vehiculo}</td>
                        <td class="p-2 border text-center">${c.tipo ? c.tipo.charAt(0).toUpperCase() + c.tipo.slice(1) : '-'}</td>
                        <td class="p-2 border text-center font-bold bg-gray-50/50">${c.cantidad || 1}</td>
                        <td class="p-2 border text-center">$${precioUnitario.toLocaleString('es-CO')}</td>
                        <td class="p-2 border text-green-600 font-bold text-center bg-green-50/20">$${importeTotal.toLocaleString('es-CO')}</td>
                        <td class="p-2 border">${c.comprador || '-'}</td>
                        <td class="p-2 border text-center">${c.documento || '-'}</td>
                        <td class="p-2 border text-center">${c.telefono || '-'}</td>
                        <td class="p-2 border text-center">${c.color || '-'}</td>
                        <td class="p-2 border text-center">${c.metodo_pago || '-'}</td>
                        <td class="p-2 border">${c.direccion || '-'}</td>
                        <td class="p-2 border text-center">${c.fecha || '-'}</td>
                        <td class="p-2 border text-center">
                            <a href="{{ route('compras.pdf', '') }}/${c.id_compra}" target="_blank"
                                class="bg-white hover:bg-gray-100 text-black border border-gray-300 px-3 py-1 rounded text-xs shadow">
                                📄 Ver PDF
                            </a>
                        </td>
                        <td class="p-2 border text-center">
                            <form action="{{ route('compras.eliminar', '') }}/${c.id_compra}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('¿Eliminar esta compra?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs shadow">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;
        }

        // FUNCIÓN PARA ACTUALIZAR CHARTS
        function actualizarChart(canvasId, config) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            if (chartInstances[canvasId]) {
                chartInstances[canvasId].destroy();
            }
            chartInstances[canvasId] = new Chart(ctx, config);
        }
    </script>

</x-app-layout>