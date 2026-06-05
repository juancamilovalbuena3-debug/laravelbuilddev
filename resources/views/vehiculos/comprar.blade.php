<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 tracking-tight">Comprar Vehículo</h2>
    </x-slot>

    @php
        $disponibles = $disponibles ?? 50;
        $stockMaximo = $stockMaximo ?? 50;
    @endphp

    <div class="max-w-4xl mx-auto p-6 mt-6 space-y-8">

        {{-- ALERTA DE STOCK --}}
        @if($disponibles <= 0)
            <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50 text-red-800 text-sm font-semibold shadow-sm flex items-center gap-2">
                🚫 <span>Este vehículo ya no está disponible. Se agotaron las {{ $stockMaximo }} unidades.</span>
            </div>
        @elseif($disponibles <= 10)
            <div class="p-4 rounded-xl border-l-4 border-yellow-400 bg-yellow-50 text-yellow-800 text-sm font-medium shadow-sm flex items-center gap-2">
                ⚠️ <span>Solo quedan <strong>{{ $disponibles }}</strong> unidad(es) disponibles. ¡No pierdas tu oportunidad!</span>
            </div>
        @endif

        <!-- Sección Declaración Jurada -->
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200 transition-all hover:shadow-xl" style="font-family: 'Times New Roman', serif;">
            <div class="text-center mb-6">
                <p class="text-sm text-gray-500"><span id="fecha-hoy"></span></p>
                <h2 class="text-xl font-bold uppercase mt-2 text-gray-900">Declaración Jurada de Medio de Pago</h2>
                <p class="text-sm mt-1 text-gray-700">Señores: <strong>SUPERINTENDENCIA NACIONAL DE LOS REGISTROS PÚBLICOS</strong></p>
                <p class="text-sm text-gray-700">Registro de Propiedad Vehicular</p>
            </div>
            <p class="text-sm mb-4 text-gray-800">
                La empresa <strong>Motrix</strong>, en su calidad de empresa <strong>Vendedora</strong>,
                y el comprador abajo indicado, declaramos la compra del vehículo:
            </p>
            <div class="overflow-hidden rounded-lg border border-black mb-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black">
                            <th class="border-r border-black px-4 py-3 text-left">Vehículo</th>
                            <th class="border-r border-black px-4 py-3 text-left">Cantidad</th>
                            <th class="border-r border-black px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Importe Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border-r border-black px-4 py-3 font-medium">{{ $vehiculo['nombre'] }}</td>
                            <td class="border-r border-black px-4 py-3" id="resumen-cantidad-doc">1</td>
                            <td class="border-r border-black px-4 py-3" id="fecha-tabla"></td>
                            <td class="px-4 py-3 font-bold" id="resumen-total-doc">${{ number_format($vehiculo['precio']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-sm font-bold uppercase mb-1 text-gray-900">Forma de Cancelación:</p>
            <p class="text-sm mb-6 text-gray-800">La empresa compradora efectuará la cancelación mediante el siguiente medio de pago indicado abajo.</p>

            <div class="flex flex-wrap gap-4 mt-4">
                <button type="button" onclick="descargarPDF()"
                        class="bg-white hover:bg-gray-50 text-gray-800 font-semibold px-6 py-2.5 rounded-lg shadow-sm border border-gray-300 transition-all text-sm flex items-center gap-2 hover:shadow-md">
                    📄 Descargar PDF
                </button>
                <button type="button" onclick="descargarCSV()"
                        class="bg-white hover:bg-gray-50 text-gray-800 font-semibold px-6 py-2.5 rounded-lg shadow-sm border border-gray-300 transition-all text-sm flex items-center gap-2 hover:shadow-md">
                    📊 Descargar CSV
                </button>
            </div>
        </div>

        <!-- Formulario de Compra -->
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
            <h3 class="text-xl font-bold mb-6 text-gray-800 border-b pb-3">📋 Datos de la Compra</h3>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg text-sm shadow-sm">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Si está agotado mostrar mensaje en lugar del formulario --}}
            @if($disponibles <= 0)
                <div class="text-center py-10">
                    <div class="text-6xl mb-4">🚫</div>
                    <h3 class="text-xl font-bold text-red-700 mb-2">Vehículo agotado</h3>
                    <p class="text-gray-600 mb-6">No quedan unidades disponibles de <strong>{{ $vehiculo['nombre'] }}</strong>.</p>
                    <a href="{{ $tipo === 'moto' ? route('motos') : route('carros') }}"
                       class="bg-black text-white font-semibold px-8 py-3 rounded-xl shadow hover:bg-gray-800 transition-colors">
                        ← Ver otros vehículos
                    </a>
                </div>
            @else

            <form id="formCompra" method="POST"
                  action="{{ $tipo === 'moto' ? route('motos.comprar', $vehiculo['id']) : route('carros.comprar', $vehiculo['id']) }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre completo</label>
                        <input type="text" name="nombre_comprador" id="inp_nombre" required
                               value="{{ auth()->user()->name }}"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-nombre">⚠ Ingresa tu nombre completo (solo letras).</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-nombre">✔ Nombre válido.</p>
                    </div>

                    <!-- Documento -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Número de documento (CC/NIT)</label>
                        <input type="text" name="documento" id="inp_doc" required placeholder="Ej: 1234567890"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-doc">⚠ Ingresa un número de documento válido (6 a 15 dígitos).</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-doc">✔ Documento válido.</p>
                    </div>

                    <!-- ── FECHA DE NACIMIENTO (NUEVO) ────────────────────── -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="inp_nacimiento" required
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-nacimiento">⚠ Debes ser mayor de 18 años para realizar una compra.</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-nacimiento">✔ Edad verificada.</p>
                    </div>
                    <!-- ── FIN FECHA DE NACIMIENTO ───────────────────────── -->

                    <!-- Cantidad -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200 shadow-sm">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">🔢 Cantidad de unidades</label>
                        <div class="flex items-center gap-4">
                            <button type="button" onclick="cambiarCantidad(-1)"
                                    class="w-10 h-10 rounded-full bg-white border-2 border-blue-300 text-blue-700 font-bold text-xl flex items-center justify-center hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm select-none">
                                −
                            </button>
                            <input type="number" name="cantidad" id="inp_cantidad"
                                   value="1" min="1" max="{{ $disponibles }}"
                                   oninput="actualizarResumen()"
                                   class="w-20 text-center text-xl font-bold border-2 border-blue-300 rounded-xl px-2 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm" />
                            <button type="button" onclick="cambiarCantidad(1)"
                                    class="w-10 h-10 rounded-full bg-white border-2 border-blue-300 text-blue-700 font-bold text-xl flex items-center justify-center hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm select-none">
                                +
                            </button>
                        </div>
                        <p class="text-xs mt-2 {{ $disponibles <= 10 ? 'text-yellow-600 font-semibold' : 'text-gray-500' }}">
                            Disponibles: <strong>{{ $disponibles }}</strong> de {{ $stockMaximo }} unidades.
                            @if($disponibles <= 10) ¡Pocas unidades! @endif
                        </p>
                    </div>

                    <!-- Color -->
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Color deseado</label>
                        <div class="flex flex-wrap gap-3 mb-2">
                            <button type="button" onclick="seleccionarColor('Blanco', this)" class="color-swatch w-8 h-8 rounded-full border border-gray-300 shadow-sm bg-white transition-all hover:scale-110 focus:outline-none" title="Blanco"></button>
                            <button type="button" onclick="seleccionarColor('Negro', this)" class="color-swatch w-8 h-8 rounded-full border border-gray-400 shadow-sm bg-black transition-all hover:scale-110 focus:outline-none" title="Negro"></button>
                            <button type="button" onclick="seleccionarColor('Gris', this)" class="color-swatch w-8 h-8 rounded-full border border-gray-400 shadow-sm bg-gray-400 transition-all hover:scale-110 focus:outline-none" title="Gris"></button>
                            <button type="button" onclick="seleccionarColor('Rojo', this)" class="color-swatch w-8 h-8 rounded-full border border-gray-400 shadow-sm bg-red-600 transition-all hover:scale-110 focus:outline-none" title="Rojo"></button>
                            <button type="button" onclick="seleccionarColor('Azul', this)" class="color-swatch w-8 h-8 rounded-full border border-gray-400 shadow-sm bg-blue-600 transition-all hover:scale-110 focus:outline-none" title="Azul"></button>
                            <button type="button" onclick="seleccionarColor('Plata', this)" class="color-swatch w-8 h-8 rounded-full border border-gray-400 shadow-sm bg-slate-300 transition-all hover:scale-110 focus:outline-none" title="Plata"></button>
                            <button type="button" onclick="seleccionarColor('Verde', this)" class="color-swatch w-8 h-8 rounded-full border border-gray-400 shadow-sm bg-green-600 transition-all hover:scale-110 focus:outline-none" title="Verde"></button>
                        </div>
                        <select name="color" id="inp_color" required class="w-full border border-gray-300 bg-white rounded-lg px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-colors pointer-events-none text-gray-600">
                            <option value="">Seleccione un color (usa los círculos arriba)</option>
                            <option value="Blanco">⬜ Blanco</option>
                            <option value="Negro">⬛ Negro</option>
                            <option value="Gris">🩶 Gris</option>
                            <option value="Rojo">🟥 Rojo</option>
                            <option value="Azul">🟦 Azul</option>
                            <option value="Plata">🪙 Plata</option>
                            <option value="Verde">🟩 Verde</option>
                        </select>
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-color">⚠ Selecciona un color usando los círculos de arriba.</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-color">✔ Color seleccionado.</p>
                    </div>

                    <!-- Método de pago -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Método de pago</label>
                        <select name="metodo_pago" id="metodo_pago" required
                                class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm"
                                onchange="mostrarCampos(this.value); validarMetodo();">
                            <option value="">Seleccione método</option>
                            <option value="Efectivo">💵 Efectivo</option>
                            <option value="Transferencia">🏦 Transferencia bancaria</option>
                            <option value="Tarjeta">💳 Tarjeta de crédito/débito</option>
                            <option value="Cuotas">📅 A cuotas</option>
                        </select>
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-metodo">⚠ Selecciona un método de pago.</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-metodo">✔ Método de pago seleccionado.</p>
                    </div>

                    <!-- Banco (transferencia) -->
                    <div id="campo-banco" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Banco</label>
                        <input type="text" name="banco" id="inp_banco" placeholder="Ej: Bancolombia"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-banco">⚠ Ingresa el nombre del banco.</p>
                    </div>

                    <!-- Cuotas -->
                    <div id="campo-cuotas" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Número de cuotas</label>
                        <select name="cuotas" id="inp_cuotas" class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm">
                            <option value="6">6 cuotas</option>
                            <option value="12">12 cuotas</option>
                            <option value="24">24 cuotas</option>
                            <option value="36">36 cuotas</option>
                            <option value="48">48 cuotas</option>
                            <option value="60">60 cuotas</option>
                        </select>
                    </div>

                    <!-- Tarjeta número -->
                    <div id="campo-tarjeta-numero" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Número de tarjeta</label>
                        <input type="text" name="tarjeta_numero" id="inp_tarjeta_num"
                               placeholder="1234 5678 9012 3456" maxlength="19"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm font-mono"
                               oninput="formatearTarjeta(this)" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-tarjeta-num">⚠ Ingresa los 16 dígitos de tu tarjeta.</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-tarjeta-num">✔ Número de tarjeta válido.</p>
                    </div>

                    <!-- Tarjeta nombre -->
                    <div id="campo-tarjeta-nombre" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre en la tarjeta</label>
                        <input type="text" name="tarjeta_nombre" id="inp_tarjeta_nom"
                               placeholder="Ej: CAMILO VALBUENA"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm uppercase" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-tarjeta-nom">⚠ Ingresa el nombre tal como aparece en la tarjeta.</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-tarjeta-nom">✔ Nombre válido.</p>
                    </div>

                    <!-- Tarjeta vencimiento -->
                    <div id="campo-tarjeta-venc" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de vencimiento</label>
                        <input type="text" name="tarjeta_vencimiento" id="inp_tarjeta_venc"
                               placeholder="MM/AA" maxlength="5"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm text-center"
                               oninput="formatearVencimiento(this)" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-tarjeta-venc">⚠ Formato MM/AA — la tarjeta no puede estar vencida.</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-tarjeta-venc">✔ Fecha válida.</p>
                    </div>

                    <!-- CVV -->
                    <div id="campo-tarjeta-cvv" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">CVV</label>
                        <input type="password" name="tarjeta_cvv" id="inp_tarjeta_cvv"
                               placeholder="***" maxlength="4"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm text-center" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-tarjeta-cvv">⚠ El CVV debe tener 3 o 4 dígitos.</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-tarjeta-cvv">✔ CVV válido.</p>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono de contacto</label>
                        <input type="tel" name="telefono" id="inp_tel" required placeholder="Ej: 3001234567"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-tel">⚠ Ingresa un teléfono colombiano válido (10 dígitos, empieza por 3).</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-tel">✔ Teléfono válido.</p>
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Dirección de entrega</label>
                        <input type="text" name="direccion" id="inp_dir" required placeholder="Ej: Calle 123 # 45-67"
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="err-dir">⚠ Ingresa una dirección válida (mínimo 8 caracteres).</p>
                        <p class="text-green-600 text-xs mt-1 hidden" id="ok-dir">✔ Dirección válida.</p>
                    </div>

                </div>

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Observaciones adicionales</label>
                    <textarea name="observaciones" id="inp_obs" rows="3"
                              placeholder="Accesorios adicionales, preferencias especiales..."
                              class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm"></textarea>
                </div>

                <!-- Resumen -->
                <div class="mt-8 p-5 bg-blue-50/50 rounded-xl border border-blue-100 shadow-sm">
                    <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Resumen de la Orden
                    </h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <p class="text-gray-600">Vehículo:</p>
                        <p class="font-semibold text-gray-900 text-right">{{ $vehiculo['nombre'] }}</p>

                        <p class="text-gray-600">Tipo:</p>
                        <p class="font-semibold text-gray-900 text-right">{{ ucfirst($tipo) }}</p>

                        <p class="text-gray-600">Precio unitario:</p>
                        <p class="font-semibold text-gray-900 text-right" id="resumen-precio-unit">
                            ${{ number_format($vehiculo['precio']) }} COP
                        </p>

                        <p class="text-gray-600">Disponibles:</p>
                        <p class="font-semibold text-right {{ $disponibles <= 10 ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $disponibles }} unidad(es)
                        </p>

                        <p class="text-gray-600">Cantidad:</p>
                        <p class="font-semibold text-gray-900 text-right" id="resumen-cantidad">1</p>

                        <p class="text-gray-600 font-bold border-t border-blue-200 pt-2 mt-1">Total a pagar:</p>
                        <p class="font-bold text-green-700 text-right text-lg border-t border-blue-200 pt-2 mt-1" id="resumen-total">
                            ${{ number_format($vehiculo['precio']) }} COP
                        </p>
                    </div>
                </div>

                <!-- Firma -->
                <div class="mt-8 border border-gray-200 rounded-xl p-5 bg-gray-50 shadow-sm">
                    <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">✍️ Firma del Comprador</h4>
                    <p class="text-sm text-gray-500 mb-3">Firme en el recuadro inferior con el mouse o su dedo.</p>
                    <div class="bg-white p-1 border border-gray-300 rounded-lg shadow-inner" id="firma-box">
                        <canvas id="firmaCanvas" width="600" height="150" class="w-full cursor-crosshair rounded"></canvas>
                    </div>
                    <input type="hidden" name="firma_comprador" id="firma_comprador" />
                    <p class="text-red-500 text-xs mt-2 hidden" id="err-firma">⚠ Por favor firma en el recuadro antes de continuar.</p>
                    <p class="text-green-600 text-xs mt-2 hidden" id="ok-firma">✔ Firma registrada.</p>
                    <div class="mt-3 flex justify-end">
                        <button type="button" onclick="limpiarFirma()"
                                class="bg-white hover:bg-gray-100 text-gray-700 font-medium border border-gray-300 px-4 py-1.5 rounded-lg text-sm shadow-sm transition-colors flex items-center gap-1">
                            🗑️ Limpiar firma
                        </button>
                    </div>
                </div>

                <!-- Firma Centrada -->
                <div class="mt-8 flex justify-center text-center text-sm text-gray-600 border-t border-gray-200 pt-6">
                    <div class="w-64">
                        <div class="border-b-2 border-gray-800 mb-2 h-8 mx-4"></div>
                        <p class="font-bold text-gray-900">CONCESIONARIO</p>
                        <p>Motrix S.A.S.</p>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap gap-4 justify-center md:justify-start">
                    <button type="button" onclick="intentarConfirmar()"
                            class="bg-black hover:bg-gray-800 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg transition-transform hover:scale-105 flex items-center gap-2">
                        ✅ Confirmar Compra
                    </button>
                    <a href="{{ url()->previous() }}"
                       class="bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-3.5 rounded-xl shadow-md border border-gray-300 transition-colors flex items-center gap-2">
                        ❌ Cancelar
                    </a>
                </div>

            </form>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="modalConfirmacion" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div id="modalContenido" class="bg-white p-8 rounded-2xl shadow-2xl max-w-sm w-full mx-4 text-center transform transition-transform duration-300 scale-95">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">¿Confirmar compra?</h3>
            <p class="text-sm text-gray-600 mb-2">
                Vehículo: <strong>{{ $vehiculo['nombre'] }}</strong>
            </p>
            <p class="text-sm text-gray-600 mb-1">
                Cantidad: <strong id="modal-cantidad">1</strong> unidad(es)
            </p>
            <p class="text-sm text-gray-600 mb-6">
                Total: <strong class="text-green-700" id="modal-total">${{ number_format($vehiculo['precio']) }} COP</strong>
            </p>
            <div class="flex flex-col gap-3">
                <button type="button" onclick="ejecutarCompra()" class="w-full px-4 py-3 bg-black text-white hover:bg-gray-800 rounded-xl font-bold shadow-md transition-all hover:shadow-lg">
                    Sí, realizar compra
                </button>
                <button type="button" onclick="cerrarModal()" class="w-full px-4 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors">
                    Revisar de nuevo
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const precioUnitario = {{ $vehiculo['precio'] }};
        const maxDisponibles = {{ $disponibles }};

        const hoy = new Date().toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('fecha-hoy').textContent   = hoy;
        document.getElementById('fecha-tabla').textContent = hoy;

        // Fijar fecha máxima del input de nacimiento = hoy - 18 años
        (function() {
            const inp = document.getElementById('inp_nacimiento');
            if (!inp) return;
            const hoyDate  = new Date();
            const max18    = new Date(hoyDate.getFullYear() - 18, hoyDate.getMonth(), hoyDate.getDate());
            inp.max = max18.toISOString().split('T')[0];   // no permite seleccionar fecha futura a 18 años atrás
            inp.min = '1900-01-01';
        })();

        function formatearNumero(num) {
            return new Intl.NumberFormat('es-CO').format(num);
        }

        // ── Utilidad mensajes ─────────────────────────────────────
        function mostrarError(idErr, idOk) {
            document.getElementById(idErr).classList.remove('hidden');
            document.getElementById(idOk).classList.add('hidden');
        }
        function mostrarOk(idErr, idOk) {
            document.getElementById(idErr).classList.add('hidden');
            document.getElementById(idOk).classList.remove('hidden');
        }
        function limpiarMensaje(idErr, idOk) {
            document.getElementById(idErr).classList.add('hidden');
            document.getElementById(idOk).classList.add('hidden');
        }

        // ── Resaltar campo ────────────────────────────────────────
        function marcarCampo(el, esValido) {
            if (esValido) {
                el.classList.remove('border-red-400', 'bg-red-50');
                el.classList.add('border-green-400');
            } else {
                el.classList.remove('border-green-400');
                el.classList.add('border-red-400', 'bg-red-50');
            }
        }

        // ══════════════════════════════════════════════════════════
        // VALIDACIONES INDIVIDUALES
        // ══════════════════════════════════════════════════════════

        function validarNombre() {
            const el  = document.getElementById('inp_nombre');
            const val = el.value.trim();
            const ok  = val.length >= 3 && /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-']+$/.test(val);
            marcarCampo(el, ok);
            ok ? mostrarOk('err-nombre','ok-nombre') : mostrarError('err-nombre','ok-nombre');
            return ok;
        }

        function validarDocumento() {
            const el  = document.getElementById('inp_doc');
            const val = el.value.trim().replace(/\s/g, '');
            const ok  = /^\d{6,15}$/.test(val);
            marcarCampo(el, ok);
            ok ? mostrarOk('err-doc','ok-doc') : mostrarError('err-doc','ok-doc');
            return ok;
        }

        // ── VALIDACIÓN FECHA DE NACIMIENTO (NUEVA) ───────────────
        function validarNacimiento() {
            const el  = document.getElementById('inp_nacimiento');
            const val = el.value;
            let ok = false;
            if (val) {
                const nacimiento = new Date(val);
                const hoyDate    = new Date();
                // Calcular edad exacta en años
                let edad = hoyDate.getFullYear() - nacimiento.getFullYear();
                const m  = hoyDate.getMonth() - nacimiento.getMonth();
                if (m < 0 || (m === 0 && hoyDate.getDate() < nacimiento.getDate())) edad--;
                ok = edad >= 18 && edad <= 120;
            }
            marcarCampo(el, ok);
            ok ? mostrarOk('err-nacimiento','ok-nacimiento') : mostrarError('err-nacimiento','ok-nacimiento');
            return ok;
        }
        // ── FIN VALIDACIÓN FECHA DE NACIMIENTO ───────────────────

        function validarColor() {
            const val = document.getElementById('inp_color').value;
            const ok  = val !== '';
            ok ? mostrarOk('err-color','ok-color') : mostrarError('err-color','ok-color');
            return ok;
        }

        function validarMetodo() {
            const val = document.getElementById('metodo_pago').value;
            const ok  = val !== '';
            ok ? mostrarOk('err-metodo','ok-metodo') : mostrarError('err-metodo','ok-metodo');
            return ok;
        }

        function validarBanco() {
            const el  = document.getElementById('inp_banco');
            const val = el.value.trim();
            const visible = !document.getElementById('campo-banco').classList.contains('hidden');
            if (!visible) return true;
            const ok = val.length >= 3;
            marcarCampo(el, ok);
            document.getElementById('err-banco').classList.toggle('hidden', ok);
            return ok;
        }

        function validarTarjetaNum() {
            const el  = document.getElementById('inp_tarjeta_num');
            const val = el.value.replace(/\s/g, '');
            const visible = !document.getElementById('campo-tarjeta-numero').classList.contains('hidden');
            if (!visible) return true;
            const ok = /^\d{16}$/.test(val);
            marcarCampo(el, ok);
            ok ? mostrarOk('err-tarjeta-num','ok-tarjeta-num') : mostrarError('err-tarjeta-num','ok-tarjeta-num');
            return ok;
        }

        function validarTarjetaNom() {
            const el  = document.getElementById('inp_tarjeta_nom');
            const val = el.value.trim();
            const visible = !document.getElementById('campo-tarjeta-nombre').classList.contains('hidden');
            if (!visible) return true;
            const ok = val.length >= 3 && /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(val);
            marcarCampo(el, ok);
            ok ? mostrarOk('err-tarjeta-nom','ok-tarjeta-nom') : mostrarError('err-tarjeta-nom','ok-tarjeta-nom');
            return ok;
        }

        function validarTarjetaVenc() {
            const el  = document.getElementById('inp_tarjeta_venc');
            const val = el.value.trim();
            const visible = !document.getElementById('campo-tarjeta-venc').classList.contains('hidden');
            if (!visible) return true;
            let ok = false;
            if (/^\d{2}\/\d{2}$/.test(val)) {
                const [mm, aa] = val.split('/').map(Number);
                const ahora = new Date();
                const expira = new Date(2000 + aa, mm - 1, 1);
                ok = mm >= 1 && mm <= 12 && expira > ahora;
            }
            marcarCampo(el, ok);
            ok ? mostrarOk('err-tarjeta-venc','ok-tarjeta-venc') : mostrarError('err-tarjeta-venc','ok-tarjeta-venc');
            return ok;
        }

        function validarCvv() {
            const el  = document.getElementById('inp_tarjeta_cvv');
            const val = el.value.trim();
            const visible = !document.getElementById('campo-tarjeta-cvv').classList.contains('hidden');
            if (!visible) return true;
            const ok = /^\d{3,4}$/.test(val);
            marcarCampo(el, ok);
            ok ? mostrarOk('err-tarjeta-cvv','ok-tarjeta-cvv') : mostrarError('err-tarjeta-cvv','ok-tarjeta-cvv');
            return ok;
        }

        function validarTelefono() {
            const el  = document.getElementById('inp_tel');
            const val = el.value.trim().replace(/\s/g, '');
            const ok  = /^3\d{9}$/.test(val);
            marcarCampo(el, ok);
            ok ? mostrarOk('err-tel','ok-tel') : mostrarError('err-tel','ok-tel');
            return ok;
        }

        function validarDireccion() {
            const el  = document.getElementById('inp_dir');
            const val = el.value.trim();
            const ok  = val.length >= 8;
            marcarCampo(el, ok);
            ok ? mostrarOk('err-dir','ok-dir') : mostrarError('err-dir','ok-dir');
            return ok;
        }

        let firmaRealizada = false;
        function validarFirma() {
            const ok = firmaRealizada;
            const box = document.getElementById('firma-box');
            box.classList.toggle('border-red-400', !ok);
            box.classList.toggle('border-green-400', ok);
            ok ? mostrarOk('err-firma','ok-firma') : mostrarError('err-firma','ok-firma');
            return ok;
        }

        // ── Listeners en tiempo real ──────────────────────────────
        document.getElementById('inp_nombre').addEventListener('input', validarNombre);
        document.getElementById('inp_doc').addEventListener('input', validarDocumento);
        document.getElementById('inp_nacimiento').addEventListener('change', validarNacimiento); // ← NUEVO
        document.getElementById('inp_tel').addEventListener('input', validarTelefono);
        document.getElementById('inp_dir').addEventListener('input', validarDireccion);
        document.getElementById('inp_banco').addEventListener('input', validarBanco);
        document.getElementById('inp_tarjeta_num').addEventListener('input', validarTarjetaNum);
        document.getElementById('inp_tarjeta_nom').addEventListener('input', validarTarjetaNom);
        document.getElementById('inp_tarjeta_venc').addEventListener('input', validarTarjetaVenc);
        document.getElementById('inp_tarjeta_cvv').addEventListener('input', validarCvv);

        // ── Validar todo antes de abrir el modal ──────────────────
        function validarTodo() {
            const n  = validarNombre();
            const d  = validarDocumento();
            const na = validarNacimiento();   // ← NUEVO
            const c  = validarColor();
            const m  = validarMetodo();
            const b  = validarBanco();
            const tn = validarTarjetaNum();
            const tno= validarTarjetaNom();
            const tv = validarTarjetaVenc();
            const tc = validarCvv();
            const t  = validarTelefono();
            const dir= validarDireccion();
            const f  = validarFirma();
            return n && d && na && c && m && b && tn && tno && tv && tc && t && dir && f;
        }

        // ── Cantidad ──────────────────────────────────────────────
        function cambiarCantidad(delta) {
            const inp = document.getElementById('inp_cantidad');
            if (!inp) return;
            let val = parseInt(inp.value) || 1;
            val = Math.min(maxDisponibles, Math.max(1, val + delta));
            inp.value = val;
            actualizarResumen();
        }

        function actualizarResumen() {
            const inp = document.getElementById('inp_cantidad');
            if (!inp) return;
            let cantidad = parseInt(inp.value) || 1;
            if (cantidad < 1)              { cantidad = 1;              inp.value = 1; }
            if (cantidad > maxDisponibles) { cantidad = maxDisponibles; inp.value = maxDisponibles; }
            const total    = precioUnitario * cantidad;
            const totalStr = '$' + formatearNumero(total) + ' COP';
            document.getElementById('resumen-cantidad').textContent     = cantidad;
            document.getElementById('resumen-total').textContent        = totalStr;
            document.getElementById('resumen-cantidad-doc').textContent = cantidad;
            document.getElementById('resumen-total-doc').textContent    = '$' + formatearNumero(total);
            document.getElementById('modal-cantidad').textContent       = cantidad;
            document.getElementById('modal-total').textContent          = totalStr;
        }

        const inpCantidad = document.getElementById('inp_cantidad');
        if (inpCantidad) inpCantidad.addEventListener('input', actualizarResumen);

        // ── Color ─────────────────────────────────────────────────
        function seleccionarColor(color, btnElement) {
            document.getElementById('inp_color').value = color;
            document.querySelectorAll('.color-swatch').forEach(el =>
                el.classList.remove('ring-4', 'ring-blue-400', 'scale-110'));
            btnElement.classList.add('ring-4', 'ring-blue-400', 'scale-110');
            validarColor();
        }

        // ── Modal ─────────────────────────────────────────────────
        function intentarConfirmar() {
            if (!validarTodo()) {
                const primerError = document.querySelector('[id^="err-"]:not(.hidden)');
                if (primerError) primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            document.getElementById('firma_comprador').value = canvas.toDataURL('image/png');
            actualizarResumen();
            const modal    = document.getElementById('modalConfirmacion');
            const contenido = document.getElementById('modalContenido');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                contenido.classList.remove('scale-95');
                contenido.classList.add('scale-100');
            }, 10);
        }

        function cerrarModal() {
            const modal    = document.getElementById('modalConfirmacion');
            const contenido = document.getElementById('modalContenido');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            contenido.classList.remove('scale-100');
            contenido.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function ejecutarCompra() {
            document.getElementById('firma_comprador').value = canvas.toDataURL('image/png');
            document.getElementById('formCompra').submit();
        }

        // ── Campos dinámicos de pago ──────────────────────────────
        function mostrarCampos(valor) {
            document.getElementById('campo-banco').classList.toggle('hidden', valor !== 'Transferencia');
            document.getElementById('campo-cuotas').classList.toggle('hidden', valor !== 'Cuotas');
            const esTarjeta = valor === 'Tarjeta';
            document.getElementById('campo-tarjeta-numero').classList.toggle('hidden', !esTarjeta);
            document.getElementById('campo-tarjeta-nombre').classList.toggle('hidden', !esTarjeta);
            document.getElementById('campo-tarjeta-venc').classList.toggle('hidden',   !esTarjeta);
            document.getElementById('campo-tarjeta-cvv').classList.toggle('hidden',    !esTarjeta);
        }

        function formatearTarjeta(input) {
            let val = input.value.replace(/\D/g, '').substring(0, 16);
            input.value = val.replace(/(.{4})/g, '$1 ').trim();
        }

        function formatearVencimiento(input) {
            let val = input.value.replace(/\D/g, '').substring(0, 4);
            if (val.length >= 3) val = val.substring(0,2) + '/' + val.substring(2);
            input.value = val;
        }

        // ── getDatos / PDF / CSV ──────────────────────────────────
        function getDatos() {
            const cantidad = parseInt(document.getElementById('inp_cantidad')?.value) || 1;
            const total    = precioUnitario * cantidad;
            return {
                vehiculo:  '{{ $vehiculo['nombre'] }}',
                precio:    '${{ number_format($vehiculo['precio']) }} COP',
                total:     '$' + formatearNumero(total) + ' COP',
                cantidad:  cantidad,
                tipo:      '{{ ucfirst($tipo) }}',
                fecha:     hoy,
                nombre:    document.getElementById('inp_nombre')?.value    || '',
                documento: document.getElementById('inp_doc')?.value       || '',
                color:     document.getElementById('inp_color')?.value     || '',
                metodo:    document.getElementById('metodo_pago')?.value   || '',
                telefono:  document.getElementById('inp_tel')?.value       || '',
                direccion: document.getElementById('inp_dir')?.value       || '',
                obs:       document.getElementById('inp_obs')?.value       || '',
            };
        }

        function descargarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const d   = getDatos();
            doc.setFontSize(14); doc.setFont('times', 'bold');
            doc.text('DECLARACIÓN JURADA DE MEDIO DE PAGO', 105, 20, { align: 'center' });
            doc.setFontSize(10); doc.setFont('times', 'normal');
            doc.text('Señores: SUPERINTENDENCIA NACIONAL DE LOS REGISTROS PÚBLICOS', 105, 28, { align: 'center' });
            doc.text('Registro de Propiedad Vehicular', 105, 34, { align: 'center' });
            doc.text(`Fecha: ${d.fecha}`, 14, 44);
            doc.setFont('times', 'bold'); doc.text('Datos del Vehículo:', 14, 54);
            doc.setFont('times', 'normal');
            doc.text(`Vehículo: ${d.vehiculo}`, 14, 62);
            doc.text(`Precio unitario: ${d.precio}`, 14, 70);
            doc.text(`Cantidad: ${d.cantidad} unidad(es)`, 14, 78);
            doc.text(`Total: ${d.total}`, 14, 86);
            doc.text(`Tipo: ${d.tipo}`, 14, 94);
            doc.setFont('times', 'bold'); doc.text('Datos del Comprador:', 14, 106);
            doc.setFont('times', 'normal');
            doc.text(`Nombre: ${d.nombre}`, 14, 114);
            doc.text(`Documento: ${d.documento}`, 14, 122);
            doc.text(`Teléfono: ${d.telefono}`, 14, 130);
            doc.text(`Dirección: ${d.direccion}`, 14, 138);
            doc.text(`Color deseado: ${d.color}`, 14, 146);
            doc.text(`Método de pago: ${d.metodo}`, 14, 154);
            if (d.obs) {
                doc.setFont('times', 'bold'); doc.text('Observaciones:', 14, 166);
                doc.setFont('times', 'normal');
                doc.text(doc.splitTextToSize(d.obs, 180), 14, 174);
            }
            const firmaData = document.getElementById('firma_comprador').value;
            if (firmaData) doc.addImage(firmaData, 'PNG', 120, 200, 60, 20);
            doc.line(14, 220, 90, 220); doc.line(120, 220, 196, 220);
            doc.text('CONCESIONARIO - Motrix S.A.S.', 14, 226);
            doc.text('COMPRADOR - Firma y Huella', 120, 226);
            doc.save(`compra_${d.vehiculo.replace(/ /g,'_')}.pdf`);
        }

        function descargarCSV() {
            const d = getDatos();
            const rows = [
                ['Campo','Valor'],['Fecha',d.fecha],['Vehículo',d.vehiculo],
                ['Precio unitario',d.precio],['Cantidad',d.cantidad],['Total',d.total],
                ['Tipo',d.tipo],['Nombre comprador',d.nombre],
                ['Documento',d.documento],['Color deseado',d.color],
                ['Método de pago',d.metodo],['Teléfono',d.telefono],
                ['Dirección',d.direccion],['Observaciones',d.obs],
            ];
            const csv  = rows.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href = url; a.download = `compra_${d.vehiculo.replace(/ /g,'_')}.csv`;
            a.click(); URL.revokeObjectURL(url);
        }

        // ── Firma ─────────────────────────────────────────────────
        const canvas  = document.getElementById('firmaCanvas');
        const ctx     = canvas ? canvas.getContext('2d') : null;
        let firmando  = false;

        if (canvas) {
            canvas.addEventListener('mousedown', e => { firmando = true; ctx.beginPath(); ctx.moveTo(e.offsetX, e.offsetY); });
            canvas.addEventListener('mousemove', e => {
                if (!firmando) return;
                ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000';
                ctx.lineTo(e.offsetX, e.offsetY); ctx.stroke();
            });
            canvas.addEventListener('mouseup', () => {
                firmando = false;
                firmaRealizada = true;
                guardarFirma();
                mostrarOk('err-firma', 'ok-firma');
                document.getElementById('firma-box').classList.remove('border-red-400');
                document.getElementById('firma-box').classList.add('border-green-400');
            });
            canvas.addEventListener('mouseleave', () => { firmando = false; });
            canvas.addEventListener('touchstart', e => {
                e.preventDefault(); firmando = true;
                const t = e.touches[0]; const r = canvas.getBoundingClientRect();
                ctx.beginPath(); ctx.moveTo(t.clientX - r.left, t.clientY - r.top);
            });
            canvas.addEventListener('touchmove', e => {
                e.preventDefault(); if (!firmando) return;
                const t = e.touches[0]; const r = canvas.getBoundingClientRect();
                ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000';
                ctx.lineTo(t.clientX - r.left, t.clientY - r.top); ctx.stroke();
            });
            canvas.addEventListener('touchend', () => {
                firmando = false;
                firmaRealizada = true;
                guardarFirma();
                mostrarOk('err-firma', 'ok-firma');
                document.getElementById('firma-box').classList.remove('border-red-400');
                document.getElementById('firma-box').classList.add('border-green-400');
            });
        }

        function guardarFirma() {
            if (canvas) document.getElementById('firma_comprador').value = canvas.toDataURL('image/png');
        }

        function limpiarFirma() {
            if (ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('firma_comprador').value = '';
            firmaRealizada = false;
            limpiarMensaje('err-firma', 'ok-firma');
            document.getElementById('firma-box').classList.remove('border-green-400', 'border-red-400');
        }
    </script>

</x-app-layout>