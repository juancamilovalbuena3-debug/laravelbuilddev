<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            margin: 30px 40px;
            color: #111;
            line-height: 1.5;
        }

        /* ── Encabezado ── */
        .logo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .logo-header .empresa {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .logo-header .fecha-doc {
            font-size: 10px;
            color: #555;
            text-align: right;
        }

        h1 {
            text-align: center;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 8px 0 4px;
        }
        .centro { text-align: center; margin: 2px 0; }
        .intro   { margin: 12px 0 8px; font-size: 11px; }

        /* ── Tablas generales ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }
        th {
            background: #e8e8e8;
            font-weight: bold;
            text-align: left;
            padding: 6px 9px;
            border: 1px solid #555;
        }
        td {
            padding: 6px 9px;
            border: 1px solid #555;
            vertical-align: top;
        }
        td.center { text-align: center; }
        td.bold   { font-weight: bold; }
        td.money  { font-weight: bold; color: #1a5e1a; }

        /* ── Sección títulos ── */
        .seccion {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            background: #f5f5f5;
            border-left: 4px solid #333;
            padding: 4px 8px;
            margin: 14px 0 4px;
        }

        /* ── Tabla de dos columnas (clave/valor) ── */
        table.kv th { width: 35%; }

        /* ── Alertas inline ── */
        .badge {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-gray  { background: #e2e3e5; color: #383d41; }
        .badge-blue  { background: #cce5ff; color: #004085; }

        /* ── Sección de firmas ── */
        .firmas {
            margin-top: 50px;
            display: flex;
            gap: 30px;
        }
        .firma-bloque {
            flex: 1;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 8px;
        }
        .firma-img {
            width: 180px;
            height: 60px;
            display: block;
            margin: 0 auto 6px;
            object-fit: contain;
        }
        .firma-linea {
            border-top: 1px solid #000;
            width: 75%;
            margin: 6px auto 4px;
        }

        /* ── Pie de página ── */
        .footer {
            margin-top: 24px;
            border-top: 1px solid #ccc;
            padding-top: 6px;
            font-size: 9px;
            color: #777;
            text-align: center;
        }

        /* Evitar corte de tabla entre páginas */
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

    {{-- ── ENCABEZADO ── --}}
    <div class="logo-header">
        <div class="empresa">Motrix S.A.S.</div>
        <div class="fecha-doc">
            Documento generado el<br>
            <strong>{{ now()->format('d \d\e F \d\e Y') }}</strong>
        </div>
    </div>

    <h1>Declaración Jurada de Medio de Pago</h1>
    <p class="centro">Señores: <strong>SUPERINTENDENCIA NACIONAL DE LOS REGISTROS PÚBLICOS</strong></p>
    <p class="centro">Registro de Propiedad Vehicular</p>

    <p class="intro">
        La empresa <strong>Motrix S.A.S.</strong>, en su calidad de empresa <strong>Vendedora</strong>,
        y el comprador abajo indicado, declaramos bajo juramento que la transferencia del vehículo
        descrito a continuación se realizó mediante el medio de pago indicado en el presente documento,
        en cumplimiento de la normativa vigente sobre registros públicos vehiculares.
    </p>

    {{-- ── 1. DATOS DEL VEHÍCULO ── --}}
    <div class="seccion">1. Datos del Vehículo</div>
    <table>
        <thead>
            <tr>
                <th>Vehículo</th>
                <th>Tipo</th>
                <th>Fecha de compra</th>
                <th class="center">Cantidad</th>
                <th>Precio unitario</th>
                <th>Importe total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="bold">{{ $compra->vehiculo }}</td>
                <td>
                    <span class="badge badge-blue">{{ ucfirst($compra->tipo) }}</span>
                </td>
                <td>{{ $compra->created_at->format('d/m/Y H:i') }}</td>
                <td class="center bold">{{ $compra->cantidad ?? 1 }}</td>
                <td>${{ number_format($compra->precio_unitario ?? $compra->precio, 0, ',', '.') }} COP</td>
                <td class="money">
                    ${{ number_format(($compra->precio_unitario ?? $compra->precio) * ($compra->cantidad ?? 1), 0, ',', '.') }} COP
                </td>
            </tr>
        </tbody>
    </table>

    @if($compra->color)
    <table class="kv" style="margin-top:4px;">
        <tr>
            <th>Color solicitado</th>
            <td>{{ $compra->color }}</td>
        </tr>
    </table>
    @endif

    {{-- ── 2. FORMA DE CANCELACIÓN ── --}}
    <div class="seccion">2. Forma de Cancelación</div>
    <table class="kv">
        <tr>
            <th>Método de pago</th>
            <td>
                <span class="badge badge-green">{{ $compra->metodo_pago ?? '-' }}</span>
            </td>
        </tr>

        @if($compra->metodo_pago === 'Transferencia' && $compra->banco)
        <tr>
            <th>Banco</th>
            <td>{{ $compra->banco }}</td>
        </tr>
        @endif

        @if($compra->metodo_pago === 'Cuotas' && $compra->cuotas)
        <tr>
            <th>Número de cuotas</th>
            <td>
                {{ $compra->cuotas }} cuotas
                @php
                    $total = ($compra->precio_unitario ?? $compra->precio) * ($compra->cantidad ?? 1);
                    $cuotaVal = $total / $compra->cuotas;
                @endphp
                — aprox. ${{ number_format($cuotaVal, 0, ',', '.') }} COP/cuota
            </td>
        </tr>
        @endif

        @if($compra->metodo_pago === 'Tarjeta')
            @if($compra->tarjeta_numero ?? null)
            <tr>
                <th>Número de tarjeta</th>
                <td>**** **** **** {{ substr(str_replace(' ', '', $compra->tarjeta_numero), -4) }}</td>
            </tr>
            @endif
            @if($compra->tarjeta_nombre ?? null)
            <tr>
                <th>Titular de la tarjeta</th>
                <td>{{ strtoupper($compra->tarjeta_nombre) }}</td>
            </tr>
            @endif
            @if($compra->tarjeta_vencimiento ?? null)
            <tr>
                <th>Vencimiento</th>
                <td>{{ $compra->tarjeta_vencimiento }}</td>
            </tr>
            @endif
        @endif

        <tr>
            <th>Total cancelado</th>
            <td class="money" style="font-size:13px;">
                ${{ number_format(($compra->precio_unitario ?? $compra->precio) * ($compra->cantidad ?? 1), 0, ',', '.') }} COP
            </td>
        </tr>
    </table>

    {{-- ── 3. DATOS DEL COMPRADOR ── --}}
    <div class="seccion">3. Datos del Comprador</div>
    <table class="kv">
        <tr>
            <th>Nombre completo</th>
            <td class="bold">{{ $compra->nombre_comprador ?? '-' }}</td>
        </tr>
        <tr>
            <th>Número de documento (CC/NIT)</th>
            <td>{{ $compra->documento ?? '-' }}</td>
        </tr>
        @if($compra->fecha_nacimiento ?? null)
        <tr>
            <th>Fecha de nacimiento</th>
            <td>
                {{ \Carbon\Carbon::parse($compra->fecha_nacimiento)->format('d/m/Y') }}
                ({{ \Carbon\Carbon::parse($compra->fecha_nacimiento)->age }} años)
            </td>
        </tr>
        @endif
        <tr>
            <th>Teléfono de contacto</th>
            <td>{{ $compra->telefono ?? '-' }}</td>
        </tr>
        <tr>
            <th>Dirección de entrega</th>
            <td>{{ $compra->direccion ?? '-' }}</td>
        </tr>
        @if($compra->observaciones)
        <tr>
            <th>Observaciones</th>
            <td>{{ $compra->observaciones }}</td>
        </tr>
        @endif
    </table>

    {{-- ── 4. DECLARACIÓN ── --}}
    <div class="seccion">4. Declaración</div>
    <p style="font-size:10px; margin:8px 0; text-align:justify;">
        Los firmantes declaramos bajo juramento que la información consignada en el presente documento
        es verídica y que la operación de compraventa del vehículo <strong>{{ $compra->vehiculo }}</strong>
        fue realizada de manera legal y voluntaria, mediante el medio de pago
        <strong>{{ $compra->metodo_pago ?? 'indicado' }}</strong>, por un valor total de
        <strong>${{ number_format(($compra->precio_unitario ?? $compra->precio) * ($compra->cantidad ?? 1), 0, ',', '.') }} COP</strong>.
        Este documento tiene validez legal ante la Superintendencia Nacional de los Registros Públicos.
    </p>

    {{-- ── 5. FIRMAS ── --}}
    <div class="seccion">5. Firmas</div>

    <table style="margin-top: 20px; border: none;">
        <tr>
            {{-- Firma concesionario --}}
            <td style="width:50%; text-align:center; border:none; padding: 10px 20px;">
                @if(file_exists(public_path('images/firma_motrix.png')))
                    <img src="{{ public_path('images/firma_motrix.png') }}"
                         style="width:160px; height:55px; display:block; margin:0 auto 6px; object-fit:contain;" />
                @else
                    <div style="height:55px;"></div>
                @endif
                <div style="border-top:2px solid #000; width:75%; margin:6px auto 6px;"></div>
                <p style="margin:2px 0;"><strong>CONCESIONARIO</strong></p>
                <p style="margin:2px 0;">Motrix S.A.S.</p>
                <p style="margin:2px 0; font-size:9px; color:#555;">NIT: 900.XXX.XXX-X</p>
            </td>

            {{-- Firma comprador --}}
            <td style="width:50%; text-align:center; border:none; padding: 10px 20px;">
                @if($compra->firma_comprador)
                    <img src="{{ $compra->firma_comprador }}"
                         style="width:160px; height:55px; display:block; margin:0 auto 6px; object-fit:contain;" />
                @else
                    <div style="height:55px;"></div>
                @endif
                <div style="border-top:2px solid #000; width:75%; margin:6px auto 6px;"></div>
                <p style="margin:2px 0;"><strong>COMPRADOR</strong></p>
                <p style="margin:2px 0;">{{ $compra->nombre_comprador ?? '' }}</p>
                <p style="margin:2px 0; font-size:9px; color:#555;">
                    CC/NIT: {{ $compra->documento ?? '' }}
                    @if($compra->fecha_nacimiento ?? null)
                        — Nac: {{ \Carbon\Carbon::parse($compra->fecha_nacimiento)->format('d/m/Y') }}
                    @endif
                </p>
                <p style="margin:2px 0; font-size:9px; color:#555;">Firma y Huella</p>
            </td>
        </tr>
    </table>

    {{-- ── PIE DE PÁGINA ── --}}
    <div class="footer">
        Documento generado automáticamente por el sistema Motrix S.A.S. —
        Compra registrada el {{ $compra->created_at->format('d/m/Y \a \l\a\s H:i:s') }} —
        ID de transacción: #{{ str_pad($compra->id, 6, '0', STR_PAD_LEFT) }}
    </div>

</body>
</html>