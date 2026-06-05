<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Compra;
use PDF;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class VehiculoController extends Controller
{
    protected $pythonUrl;

    // Stock máximo por tipo de vehículo
    const STOCK_MAX_CARRO = 50;
    const STOCK_MAX_MOTO  = 50;

    public function __construct()
    {
        $this->pythonUrl = env('PYTHON_MICROSERVICE_URL', 'http://127.0.0.1:8080');
    }

    /**
     * Calcula las unidades disponibles restando las vendidas al stock máximo.
     */
    protected function calcularDisponibles(string $nombreVehiculo, string $tipo): array
    {
        $stockMaximo = $tipo === 'moto' ? self::STOCK_MAX_MOTO : self::STOCK_MAX_CARRO;

        $vendidos = Compra::where('tipo', $tipo)
            ->where('vehiculo', $nombreVehiculo)
            ->sum('cantidad');

        $disponibles = max(0, $stockMaximo - (int) $vendidos);

        return [
            'stockMaximo'  => $stockMaximo,
            'vendidos'     => (int) $vendidos,
            'disponibles'  => $disponibles,
        ];
    }

    public function carros()
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/productos");
            $carros = $response->json();
        } catch (\Exception $e) {
            $carros = [];
        }
        return view('vehiculos.carros', compact('carros'));
    }

    public function motos()
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/motos");
            $motos = $response->json();
        } catch (\Exception $e) {
            $motos = [];
        }
        return view('vehiculos.motos', compact('motos'));
    }

    public function verDetalleCarro($id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/producto/{$id}");
            $producto = $response->json();
        } catch (\Exception $e) {
            $producto = null;
        }
        if (!$producto || isset($producto['error'])) {
            return redirect()->route('carros')->with('error', 'Vehículo no encontrado.');
        }
        return view('vehiculos.detalle', compact('producto'));
    }

    public function verDetalleMoto($id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/moto/{$id}");
            $moto = $response->json();
        } catch (\Exception $e) {
            $moto = null;
        }
        if (!$moto || isset($moto['error'])) {
            return redirect()->route('motos')->with('error', 'Moto no encontrada.');
        }
        return view('vehiculos.detalle_moto', compact('moto'));
    }

    public function formComprarCarro($id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/producto/{$id}");
            $data = $response->json();
        } catch (\Exception $e) {
            return redirect()->route('carros')->with('error', 'No se pudo conectar con el microservicio.');
        }

        $vehiculo = isset($data['producto']) ? $data['producto'] : $data;

        $vehiculo['nombre'] = ($vehiculo['nombre'] ?? null)
            ?: trim(($vehiculo['marca'] ?? '') . ' ' . ($vehiculo['modelo'] ?? 'Vehículo'));
        $vehiculo['precio'] = (float) ($vehiculo['precio'] ?? 0);
        $vehiculo['id']     = $vehiculo['id'] ?? $id;

        $stock = $this->calcularDisponibles($vehiculo['nombre'], 'carro');

        if ($stock['disponibles'] <= 0) {
            return redirect()->route('carros')
                ->with('error', "❌ {$vehiculo['nombre']} ya no está disponible. Se agotaron las {$stock['stockMaximo']} unidades.");
        }

        return view('vehiculos.comprar', [
            'vehiculo'    => $vehiculo,
            'tipo'        => 'carro',
            'disponibles' => $stock['disponibles'],
            'stockMaximo' => $stock['stockMaximo'],
        ]);
    }

    public function formComprarMoto($id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/moto/{$id}");
            $data = $response->json();
        } catch (\Exception $e) {
            return redirect()->route('motos')->with('error', 'No se pudo conectar con el microservicio.');
        }

        $vehiculo = isset($data['producto']) ? $data['producto'] : $data;

        $vehiculo['nombre'] = ($vehiculo['nombre'] ?? null)
            ?: trim(($vehiculo['marca'] ?? '') . ' ' . ($vehiculo['modelo'] ?? 'Moto'));
        $vehiculo['precio'] = (float) ($vehiculo['precio'] ?? 0);
        $vehiculo['id']     = $vehiculo['id'] ?? $id;

        $stock = $this->calcularDisponibles($vehiculo['nombre'], 'moto');

        if ($stock['disponibles'] <= 0) {
            return redirect()->route('motos')
                ->with('error', "❌ {$vehiculo['nombre']} ya no está disponible. Se agotaron las {$stock['stockMaximo']} unidades.");
        }

        return view('vehiculos.comprar', [
            'vehiculo'    => $vehiculo,
            'tipo'        => 'moto',
            'disponibles' => $stock['disponibles'],
            'stockMaximo' => $stock['stockMaximo'],
        ]);
    }

    public function comprarCarro(Request $request, $id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/producto/{$id}");
            $data = $response->json();
            $vehiculo = isset($data['producto']) ? $data['producto'] : $data;
            $nombreVehiculo = ($vehiculo['nombre'] ?? null)
                ?: trim(($vehiculo['marca'] ?? '') . ' ' . ($vehiculo['modelo'] ?? 'Vehículo'));
        } catch (\Exception $e) {
            $nombreVehiculo = null;
        }

        $stockDisponible = $nombreVehiculo
            ? $this->calcularDisponibles($nombreVehiculo, 'carro')['disponibles']
            : self::STOCK_MAX_CARRO;

        $maxCompra = min(50, $stockDisponible);

        $request->validate([
            'nombre_comprador' => 'required|string',
            'documento'        => 'required|string',
            'color'            => 'required|string',
            'metodo_pago'      => 'required|string',
            'telefono'         => 'required|string',
            'direccion'        => 'required|string',
            'cantidad'         => "required|integer|min:1|max:{$maxCompra}",
        ]);

        try {
            $response = Http::timeout(5)->post("{$this->pythonUrl}/comprar/{$id}", [
                'cantidad'      => $request->cantidad      ?? 1,
                'comprador'     => $request->nombre_comprador,
                'documento'     => $request->documento,
                'telefono'      => $request->telefono,
                'color'         => $request->color,
                'metodo_pago'   => $request->metodo_pago,
                'direccion'     => $request->direccion,
                'observaciones' => $request->observaciones ?? '',
            ]);
            $resultado = $response->json();
        } catch (\Exception $e) {
            return redirect()->route('carros')->with('error', 'No se pudo conectar con el microservicio Python.');
        }

        if (!$resultado || isset($resultado['error'])) {
            return redirect()->route('carros')->with('error', $resultado['error'] ?? 'Error desconocido en el microservicio.');
        }

        $nombreVehiculo = $resultado['vehiculo'] ?? 'Carro';
        $precioUnitario = (float) ($resultado['precio_unitario'] ?? 0);
        $cantidad       = max(1, (int) ($resultado['cantidad'] ?? $request->cantidad ?? 1));

        Compra::create([
            'user_id'          => auth()->id(),
            'vehiculo'         => $nombreVehiculo,
            'precio_unitario'  => $precioUnitario,
            'cantidad'         => $cantidad,
            'tipo'             => 'carro',
            'color'            => $request->color,
            'metodo_pago'      => $request->metodo_pago,
            'banco'            => $request->banco,
            'cuotas'           => $request->cuotas,
            'nombre_comprador' => $request->nombre_comprador,
            'documento'        => $request->documento,
            'telefono'         => $request->telefono,
            'direccion'        => $request->direccion,
            'observaciones'    => $request->observaciones,
            'firma_comprador'  => $request->firma_comprador,
        ]);

        $destino = auth()->user()->role === 'usuario' ? 'dashboard' : 'reportes';
        return redirect()->route($destino)
            ->with('success', '✅ Compra registrada — ' . $nombreVehiculo .
                ' | Color: ' . $request->color .
                ' | Pago: ' . $request->metodo_pago .
                ' | $' . number_format($precioUnitario * $cantidad));
    }

    public function comprarMoto(Request $request, $id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/moto/{$id}");
            $data = $response->json();
            $vehiculo = isset($data['producto']) ? $data['producto'] : $data;
            $nombreVehiculo = ($vehiculo['nombre'] ?? null)
                ?: trim(($vehiculo['marca'] ?? '') . ' ' . ($vehiculo['modelo'] ?? 'Moto'));
        } catch (\Exception $e) {
            $nombreVehiculo = null;
        }

        $stockDisponible = $nombreVehiculo
            ? $this->calcularDisponibles($nombreVehiculo, 'moto')['disponibles']
            : self::STOCK_MAX_MOTO;

        $maxCompra = min(50, $stockDisponible);

        $request->validate([
            'nombre_comprador' => 'required|string',
            'documento'        => 'required|string',
            'color'            => 'required|string',
            'metodo_pago'      => 'required|string',
            'telefono'         => 'required|string',
            'direccion'        => 'required|string',
            'cantidad'         => "required|integer|min:1|max:{$maxCompra}",
        ]);

        try {
            $response = Http::timeout(5)->post("{$this->pythonUrl}/comprar-moto/{$id}", [
                'cantidad'      => $request->cantidad      ?? 1,
                'comprador'     => $request->nombre_comprador,
                'documento'     => $request->documento,
                'telefono'      => $request->telefono,
                'color'         => $request->color,
                'metodo_pago'   => $request->metodo_pago,
                'direccion'     => $request->direccion,
                'observaciones' => $request->observaciones ?? '',
            ]);
            $resultado = $response->json();
        } catch (\Exception $e) {
            return redirect()->route('motos')->with('error', 'No se pudo conectar con el microservicio Python.');
        }

        if (!$resultado || isset($resultado['error'])) {
            return redirect()->route('motos')->with('error', $resultado['error'] ?? 'Error desconocido en el microservicio.');
        }

        $nombreVehiculo = $resultado['vehiculo'] ?? 'Moto';
        $precioUnitario = (float) ($resultado['precio_unitario'] ?? 0);
        $cantidad       = max(1, (int) ($resultado['cantidad'] ?? $request->cantidad ?? 1));

        Compra::create([
            'user_id'          => auth()->id(),
            'vehiculo'         => $nombreVehiculo,
            'precio_unitario'  => $precioUnitario,
            'cantidad'         => $cantidad,
            'tipo'             => 'moto',
            'color'            => $request->color,
            'metodo_pago'      => $request->metodo_pago,
            'banco'            => $request->banco,
            'cuotas'           => $request->cuotas,
            'nombre_comprador' => $request->nombre_comprador,
            'documento'        => $request->documento,
            'telefono'         => $request->telefono,
            'direccion'        => $request->direccion,
            'observaciones'    => $request->observaciones,
            'firma_comprador'  => $request->firma_comprador,
        ]);

        $destino = auth()->user()->role === 'usuario' ? 'dashboard' : 'reportes';
        return redirect()->route($destino)
            ->with('success', '✅ Compra registrada — ' . $nombreVehiculo .
                ' | Color: ' . $request->color .
                ' | Pago: ' . $request->metodo_pago .
                ' | $' . number_format($precioUnitario * $cantidad));
    }

    public function reportes()
    {
        try {
            $response = Http::timeout(5)->get("{$this->pythonUrl}/reporte");
            $reporte  = $response->json();
            $error    = null;
        } catch (\Exception $e) {
            $reporte = [];
            $error   = 'No se pudo conectar con el microservicio Python.';
        }

        $compras = Compra::orderBy('created_at', 'desc')
            ->when(request('busqueda'), fn($q, $v) => $q->where('nombre_comprador', 'like', "%{$v}%")
                ->orWhere('documento', 'like', "%{$v}%"))
            ->when(request('vehiculo'), fn($q, $v) => $q->where('vehiculo', 'like', "%{$v}%"))
            ->get();

        $stockCarros = Compra::where('tipo', 'carro')
            ->selectRaw('vehiculo, SUM(cantidad) as total')
            ->groupBy('vehiculo')
            ->orderByDesc('total')
            ->get();

        $stockMotos = Compra::where('tipo', 'moto')
            ->selectRaw('vehiculo, SUM(cantidad) as total')
            ->groupBy('vehiculo')
            ->orderByDesc('total')
            ->get();

        $topMes = Compra::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('vehiculo, SUM(cantidad) as total')
            ->groupBy('vehiculo')
            ->orderByDesc('total')
            ->first();

        $statsLaravel = [
            'total_ventas'      => $compras->sum('cantidad'),
            'total_dinero'      => $compras->sum(fn($c) => $c->precio_unitario * $c->cantidad),
            'total_carros'      => $compras->where('tipo', 'carro')->sum('cantidad'),
            'total_motos'       => $compras->where('tipo', 'moto')->sum('cantidad'),
            'top_vehiculo_mes'  => $topMes->vehiculo ?? 'Ninguno',
            'top_vehiculo_cant' => $topMes->total ?? 0,
            'stock_carros'      => $stockCarros,
            'stock_motos'       => $stockMotos,
        ];

        return view('vehiculos.reportes', compact('reporte', 'error', 'compras', 'statsLaravel'));
    }

    public function descargarCompraPdf($id)
    {
        $compra = Compra::findOrFail($id);
        $pdf    = PDF::loadView('vehiculos.compra_pdf', compact('compra'));
        return $pdf->stream("compra_{$compra->vehiculo}.pdf");
    }

    public function eliminarCompra($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->delete();

        try {
            Http::timeout(5)->delete("{$this->pythonUrl}/compras/{$id}");
        } catch (\Exception $e) {
            // Si Python falla, igual redirige con éxito parcial
        }

        return redirect()->route('reportes')->with('success', '✅ Compra eliminada correctamente.');
    }

    public function create()
    {
        return view('empleados.vender');
    }

    public function store(Request $request)
    {
        try {
            $respuesta = Http::timeout(5)->post("{$this->pythonUrl}/validar/vehiculo", [
                'tipo'        => $request->tipo,
                'marca'       => $request->marca,
                'modelo'      => $request->modelo,
                'precio'      => $request->precio,
                'descripcion' => $request->descripcion,
            ]);

            $resultado = $respuesta->json();

            if (!($resultado['valido'] ?? false)) {
                return back()
                    ->withErrors($resultado['errores'] ?? ['general' => 'Error al validar los datos del vehículo.'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            $request->validate([
                'tipo'        => 'required|string',
                'marca'       => 'required|string|min:2|max:80',
                'modelo'      => 'required|string|min:2|max:80',
                'precio'      => 'required|numeric|min:1000000',
                'descripcion' => 'nullable|string|max:500',
                'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'tipo.required'    => 'El tipo de vehículo es obligatorio.',
                'marca.required'   => 'La marca es obligatoria.',
                'marca.min'        => 'La marca debe tener al menos 2 caracteres.',
                'marca.max'        => 'La marca no puede superar los 80 caracteres.',
                'modelo.required'  => 'El modelo es obligatorio.',
                'modelo.min'       => 'El modelo debe tener al menos 2 caracteres.',
                'modelo.max'       => 'El modelo no puede superar los 80 caracteres.',
                'precio.required'  => 'El precio es obligatorio.',
                'precio.numeric'   => 'El precio debe ser un número válido.',
                'precio.min'       => 'El precio mínimo permitido es de $1,000,000.',
                'descripcion.max'  => 'La descripción no puede superar los 500 caracteres.',
                'imagen.image'     => 'El archivo debe ser una imagen.',
                'imagen.mimes'     => 'La imagen debe ser de tipo jpeg, png, jpg o gif.',
                'imagen.max'       => 'La imagen no puede superar los 2MB.',
            ]);
        }

        $vehiculo = new Vehiculo($request->only(['tipo', 'marca', 'modelo', 'precio', 'descripcion']));

        if ($request->hasFile('imagen')) {
            $file     = $request->file('imagen');
            $filename = $file->getClientOriginalName();
            $file->storeAs('public/vehiculos', $filename);
            $vehiculo->imagen = $filename;
        }

        $vehiculo->save();

        try {
            $tipo     = strtolower($request->tipo);
            $endpoint = ($tipo === 'moto') ? '/motos/crear' : '/productos/crear';

            $payload = [
                'nombre'      => $request->marca . ' ' . $request->modelo,
                'marca'       => $request->marca,
                'modelo'      => $request->modelo,
                'precio'      => (float) $request->precio,
                'descripcion' => $request->descripcion ?? '',
                'tipo'        => $tipo,
                'imagen'      => $vehiculo->imagen
                                    ? 'images/' . $vehiculo->imagen
                                    : 'images/default.jpg',
                'transmision' => 'Automático',
                'combustible' => 'Gasolina',
                'id_laravel'  => $vehiculo->id,
            ];

            Http::timeout(5)->post("{$this->pythonUrl}{$endpoint}", $payload);

        } catch (\Exception $e) {
            // Si Python falla, el vehículo igual queda en BD Laravel
        }

        return redirect()->route('empleados.index', ['tab' => 'vehiculos'])
            ->with('success', 'Vehículo publicado con éxito 🚗🏍️');
    }

    public function edit($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        return view('vehiculos.edit', compact('vehiculo'));
    }

    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        try {
            $respuesta = Http::timeout(5)->post("{$this->pythonUrl}/validar/vehiculo", [
                'tipo'        => $request->tipo,
                'marca'       => $request->marca,
                'modelo'      => $request->modelo,
                'precio'      => $request->precio,
                'descripcion' => $request->descripcion,
            ]);

            $resultado = $respuesta->json();

            if (!($resultado['valido'] ?? false)) {
                return back()
                    ->withErrors($resultado['errores'] ?? ['general' => 'Error al validar los datos del vehículo.'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            $request->validate([
                'tipo'        => 'required|string',
                'marca'       => 'required|string|min:2|max:80',
                'modelo'      => 'required|string|min:2|max:80',
                'precio'      => 'required|numeric|min:1000000',
                'descripcion' => 'nullable|string|max:500',
                'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'tipo.required'    => 'El tipo de vehículo es obligatorio.',
                'marca.required'   => 'La marca es obligatoria.',
                'marca.min'        => 'La marca debe tener al menos 2 caracteres.',
                'marca.max'        => 'La marca no puede superar los 80 caracteres.',
                'modelo.required'  => 'El modelo es obligatorio.',
                'modelo.min'       => 'El modelo debe tener al menos 2 caracteres.',
                'modelo.max'       => 'El modelo no puede superar los 80 caracteres.',
                'precio.required'  => 'El precio es obligatorio.',
                'precio.numeric'   => 'El precio debe ser un número válido.',
                'precio.min'       => 'El precio mínimo permitido es de $1,000,000.',
                'descripcion.max'  => 'La descripción no puede superar los 500 caracteres.',
                'imagen.image'     => 'El archivo debe ser una imagen.',
                'imagen.mimes'     => 'La imagen debe ser de tipo jpeg, png, jpg o gif.',
                'imagen.max'       => 'La imagen no puede superar los 2MB.',
            ]);
        }

        $vehiculo->fill($request->only(['tipo', 'marca', 'modelo', 'precio', 'descripcion']));

        if ($request->hasFile('imagen')) {
            if ($vehiculo->imagen && Storage::exists('public/vehiculos/' . $vehiculo->imagen)) {
                Storage::delete('public/vehiculos/' . $vehiculo->imagen);
            }
            $file     = $request->file('imagen');
            $filename = $file->getClientOriginalName();
            $file->storeAs('public/vehiculos', $filename);
            $vehiculo->imagen = $filename;
        }

        $vehiculo->save();

        return redirect()->route('empleados.index', ['tab' => 'vehiculos'])->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        if ($vehiculo->imagen && Storage::exists('public/vehiculos/' . $vehiculo->imagen)) {
            Storage::delete('public/vehiculos/' . $vehiculo->imagen);
        }

        try {
            $tipo     = strtolower($vehiculo->tipo);
            $endpoint = ($tipo === 'moto') ? '/motos/eliminar' : '/productos/eliminar';
            Http::timeout(5)->delete("{$this->pythonUrl}{$endpoint}/{$id}");
        } catch (\Exception $e) {
            // Si Python falla, igual eliminamos de Laravel
        }

        $vehiculo->delete();

        return redirect()->route('empleados.index', ['tab' => 'vehiculos'])
            ->with('success', 'Vehículo eliminado correctamente.');
    }

    // ✅ ÚNICO CAMBIO: index ahora responde JSON cuando viene de API
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $vehiculos = Vehiculo::all();
            return response()->json($vehiculos);
        }
        return redirect()->route('empleados.index', $request->only(['busqueda', 'tipo']));
    }

    public function exportPdf(Request $request)
    {
        try {
            $params = [];
            if ($request->filled('busqueda')) $params['busqueda'] = $request->busqueda;
            if ($request->filled('tipo'))     $params['tipo']     = $request->tipo;

            $url      = 'http://127.0.0.1:8080/vehiculos/export/pdf';
            $response = Http::timeout(10)->get($url, $params);

            if ($response->failed()) {
                return back()->with('error', 'Error al generar el PDF desde el microservicio.');
            }

            $filename = 'vehiculos_' . now()->format('Ymd_His') . '.html';
            return response($response->body(), 200)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Microservicio no disponible: ' . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $params = [];
            if ($request->filled('busqueda')) $params['busqueda'] = $request->busqueda;
            if ($request->filled('tipo'))     $params['tipo']     = $request->tipo;

            $url      = 'http://127.0.0.1:8080/vehiculos/export/csv';
            $response = Http::timeout(10)->get($url, $params);

            if ($response->failed()) {
                return back()->with('error', 'Error al generar el CSV desde el microservicio.');
            }

            $filename = 'vehiculos_' . now()->format('Ymd_His') . '.csv';
            return response($response->body(), 200)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Microservicio no disponible: ' . $e->getMessage());
        }
    }
}