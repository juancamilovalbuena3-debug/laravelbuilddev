<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $response = Http::get('http://127.0.0.1:8080/reporte');
        
        if ($response->failed()) {
            return view('reportes.index', [
                'compras' => collect(),
                'statsLaravel' => ['total_ventas' => 0, 'total_dinero' => 0, 'total_carros' => 0, 'total_motos' => 0, 'top_vehiculo_mes' => 'Ninguno', 'top_vehiculo_cant' => 0],
                'error' => 'No se pudo conectar con el servidor.',
                'reporte' => []
            ]);
        }

        $data = $response->json();
        
        // 1. Convertir a colección
        $compras = collect($data['compras'] ?? [])->map(fn($item) => (object) $item);

        // 2. FILTROS (Se aplican sobre la colección)
        if ($request->filled('buscar')) {
            $busqueda = strtolower($request->buscar);
            $compras = $compras->filter(fn($c) => 
                str_contains(strtolower($c->nombre_comprador ?? ''), $busqueda) || 
                str_contains(strtolower($c->documento ?? ''), $busqueda)
            );
        }

        if ($request->filled('vehiculo')) {
            $vehiculo = strtolower($request->vehiculo);
            $compras = $compras->filter(fn($c) => 
                str_contains(strtolower($c->vehiculo ?? ''), $vehiculo)
            );
        }

        // 3. CÁLCULOS DINÁMICOS BASADOS EN EL FILTRO
        $totalDinero = $compras->sum(fn($c) => (float)($c->precio_unitario ?? $c->precio ?? 0) * (int)($c->cantidad ?? 1));
        $totalCarros = $compras->where('tipo', 'carro')->sum('cantidad');
        $totalMotos = $compras->where('tipo', 'moto')->sum('cantidad');
        
        // Agrupación para el "Top del mes"
        $contadorVehiculosMes = $compras->filter(fn($c) => !empty($c->created_at) && Carbon::parse($c->created_at)->isCurrentMonth())
            ->groupBy('vehiculo')
            ->map->sum('cantidad');

        $statsLaravel = [
            'total_ventas' => $compras->count(),
            'total_dinero' => $totalDinero,
            'total_carros' => $totalCarros,
            'total_motos'  => $totalMotos,
            'top_vehiculo_mes' => $contadorVehiculosMes->sortDesc()->keys()->first() ?? 'Ninguno',
            'top_vehiculo_cant' => $contadorVehiculosMes->max() ?? 0
        ];

        // 4. RECONSTRUIR EL ARRAY 'reporte' PARA LOS GRÁFICOS
        // Aquí filtramos los datos de los gráficos para que se ajusten a la tabla
        $nuevoReporte = [
            'ventas_por_fecha' => $compras->groupBy(fn($c) => Carbon::parse($c->created_at)->format('M Y'))->map->count(),
            'vehiculos_mas_vendidos' => $compras->groupBy('vehiculo')->map->sum('cantidad'),
            // Ajusta estas llaves según cómo las llames en tu archivo JS/Blade
        ];

        return view('reportes.index', [
            'compras' => $compras, // La tabla se actualizará automáticamente
            'statsLaravel' => $statsLaravel, // Las tarjetas superiores se actualizarán
            'reporte' => $nuevoReporte, // Los gráficos ahora usan datos filtrados
            'error' => null
        ]);
    }
}