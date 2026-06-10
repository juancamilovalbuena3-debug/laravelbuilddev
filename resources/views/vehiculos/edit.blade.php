<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Editar Vehículo') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6">

        {{-- Errores generales --}}
        @if ($errors->has('general'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong>Error:</strong> {{ $errors->first('general') }}
            </div>
        @endif

        <form action="{{ route('vehiculos.update', $vehiculo->id) }}" method="POST" enctype="multipart/form-data" id="form-vehiculo">
            @csrf
            @method('PUT')

            <!-- Tipo -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
                    <option value="Carro" {{ old('tipo', $vehiculo->tipo) == 'Carro' ? 'selected' : '' }}>Carro</option>
                    <option value="Moto" {{ old('tipo', $vehiculo->tipo) == 'Moto' ? 'selected' : '' }}>Moto</option>
                </select>
            </div>

            <!-- Marca -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="marca">Marca</label>
                <input type="text" name="marca" id="marca" value="{{ old('marca', $vehiculo->marca) }}"
                       readonly
                       placeholder="Haz clic para seleccionar la marca"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 border-gray-300 bg-gray-50 cursor-pointer">
                <p class="text-red-600 text-sm mt-1" id="error-marca"></p>
            </div>

            <!-- Modelo -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="modelo">Modelo</label>
                <input type="text" name="modelo" id="modelo" value="{{ old('modelo', $vehiculo->modelo) }}"
                       maxlength="4"
                       inputmode="numeric"
                       pattern="\d{4}"
                       placeholder="Ej: 2024"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 border-gray-300">
                <p class="text-red-600 text-sm mt-1" id="error-modelo"></p>
            </div>

            <!-- Precio -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="precio">Precio</label>
                <input type="number" name="precio" id="precio" value="{{ old('precio', $vehiculo->precio) }}"
                       min="1000000"
                       max="9999999999"
                       class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 border-gray-300">
                <p class="text-red-600 text-sm mt-1" id="error-precio"></p>
            </div>

            <!-- Descripción -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4"
                          maxlength="500"
                          class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">{{ old('descripcion', $vehiculo->descripcion) }}</textarea>
                <p class="text-xs text-gray-400 text-right mt-1">
                    <span id="contador-desc">0</span>/500 caracteres
                </p>
                <p class="text-red-600 text-sm mt-1" id="error-descripcion"></p>
            </div>

            <!-- Imagen -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="imagen">Imagen</label>
                @if($vehiculo->imagen)
                    <img src="{{ asset('storage/vehiculos/'.$vehiculo->imagen) }}" alt="Imagen" class="w-32 h-auto rounded mb-2">
                @endif
                <input type="file" name="imagen" id="imagen" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
            </div>

            <!-- Botones -->
            <div class="flex space-x-4">
                <button type="button" onclick="validarYEnviar()"
                        class="bg-white hover:bg-gray-200 text-black font-semibold px-4 py-2 rounded flex-1 border">
                    Actualizar
                </button>
                <a href="{{ route('dashboard') }}"
                   class="bg-white hover:bg-gray-200 text-black font-semibold px-4 py-2 rounded flex-1 border">
                   Cancelar
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ── Marcas estrictamente separadas por tipo ───────────────
        const marcasCarros = [
            'Toyota','Chevrolet','Ford','Honda','Nissan','Hyundai','Kia','Mazda',
            'Volkswagen','VW','Renault','Fiat','Peugeot','Citroen','Seat','Skoda',
            'BMW','Mercedes','Mercedes Benz','Audi','Volvo','Jeep','Dodge','Ram',
            'Chrysler','Cadillac','Buick','GMC','Lincoln','Tesla','Subaru','Mitsubishi',
            'Suzuki','Isuzu','Daewoo','Ssangyong','Chery','Great Wall','JAC','BYD',
            'Geely','Haval','DFSK','Zotye','Foton','Hafei','Brilliance','Changan',
            'Porsche','Ferrari','Lamborghini','Maserati','Alfa Romeo','Lancia',
            'Land Rover','Range Rover','Jaguar','Mini','Bentley','Rolls Royce',
            'Aston Martin','McLaren','Bugatti','Lexus','Infiniti','Acura','Genesis',
            'Rivian','Lucid','Polestar','Dacia','Lada','Opel','Vauxhall','Holden',
            'Saab','Pontiac','Oldsmobile','Hummer','Saturn','Scion','Trabant'
        ];

        const marcasMotos = [
            'Bajaj','Yamaha','Kawasaki','KTM','Ducati','Triumph','Harley',
            'Harley Davidson','Royal Enfield','Benelli','Aprilia','BMW Motorrad',
            'Husqvarna','Norton','Indian','Moto Guzzi','MV Agusta','Bimota',
            'Energica','Zero Motorcycles','AKT','Hero','TVS','Pulsar','Lifan',
            'Loncin','Zongshen','CFMoto','Kymco','SYM','Italika','Veloci','Auteco',
            'Jianshe','Skygo','Bera','Ranger','Corven','Zanella','Mondial','Motomel',
            'Beta','Guerrero','Gilera','Cuxi','Wanxin','Yumbo','Forza','Dinamo',
            'Skyjet','Leopard','Condor','IGM','AKT Motos','Auteco Mobility',
            'Vespa','Piaggio','Derbi','Rieju','Sherco','GasGas','Husaberg',
            'Ossa','Montesa','Bultaco','Raider','Duke'
        ];

        // ── Helpers ──────────────────────────────────────────────
        function setError(inputId, errorId, mensaje) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);
            if (mensaje) {
                input.classList.add('border-red-500', 'bg-red-50');
                input.classList.remove('border-gray-300');
                error.textContent = mensaje;
            } else {
                input.classList.remove('border-red-500', 'bg-red-50');
                input.classList.add('border-gray-300');
                error.textContent = '';
            }
        }

        // ── Bloqueo de espacios ───────────────────────────────────
        ['modelo', 'precio'].forEach(function(id) {
            document.getElementById(id).addEventListener('keydown', function(e) {
                if (e.key === ' ') e.preventDefault();
            });
        });

        // descripcion: bloquear espacio solo al inicio
        document.getElementById('descripcion').addEventListener('keydown', function(e) {
            if (e.key === ' ' && this.selectionStart === 0) e.preventDefault();
        });

        // descripcion: limpiar espacio inicial si el usuario pega texto
        document.getElementById('descripcion').addEventListener('input', function() {
            if (this.value.startsWith(' ')) {
                const pos = this.selectionStart - 1;
                this.value = this.value.trimStart();
                const newPos = Math.max(0, pos);
                this.setSelectionRange(newPos, newPos);
            }
        });

        // ── Dropdown de marca con SweetAlert según tipo ──────────
        const marcaInput = document.getElementById('marca');
        const tipoSelect = document.getElementById('tipo');

        function listaPorTipo(tipo) {
            return tipo === 'Moto' ? marcasMotos : marcasCarros;
        }

        function marcaEsValidaParaTipo(marca, tipo) {
            if (!marca) return false;
            const lista      = listaPorTipo(tipo);
            const listaLower = lista.map(m => m.toLowerCase());
            return listaLower.includes(marca.toLowerCase());
        }

        function abrirDropdownMarca() {
            const tipo     = tipoSelect.value;
            const lista    = listaPorTipo(tipo);
            const opciones = lista.reduce((acc, m) => { acc[m] = m; return acc; }, {});

            const valorActual = marcaEsValidaParaTipo(marcaInput.value, tipo)
                ? marcaInput.value
                : '';

            Swal.fire({
                title: `Seleccionar marca de ${tipo}`,
                input: 'select',
                inputOptions: opciones,
                inputValue: valorActual,
                showCancelButton: true,
                confirmButtonText: 'Seleccionar',
                cancelButtonText: 'Cancelar',
                inputPlaceholder: 'Selecciona una marca',
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    if (marcaEsValidaParaTipo(result.value, tipo)) {
                        marcaInput.value = result.value;
                        setError('marca', 'error-marca', '');
                    } else {
                        marcaInput.value = '';
                        setError('marca', 'error-marca',
                            `La marca seleccionada no es válida para un ${tipo}.`);
                    }
                }
            });
        }

        marcaInput.addEventListener('click', abrirDropdownMarca);

        // Al cambiar el tipo, limpiar la marca si no corresponde al nuevo tipo
        tipoSelect.addEventListener('change', function () {
            const tipo = this.value;
            if (marcaInput.value && !marcaEsValidaParaTipo(marcaInput.value, tipo)) {
                marcaInput.value = '';
                setError('marca', 'error-marca',
                    `La marca seleccionada no corresponde a un ${tipo}. Por favor selecciona de nuevo.`);
            } else if (marcaInput.value) {
                setError('marca', 'error-marca', '');
            }
        });

        // ── Bloquear caracteres no numéricos en "modelo" ─────────
        document.getElementById('modelo').addEventListener('keydown', function (e) {
            const teclaControl = ['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'].includes(e.key);
            if (teclaControl) return;
            if (!/[0-9]/.test(e.key)) e.preventDefault();
        });

        document.getElementById('modelo').addEventListener('paste', function (e) {
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^\d{1,4}$/.test(texto)) e.preventDefault();
        });

        // ── Contador descripción ──────────────────────────────────
        const descTextarea = document.getElementById('descripcion');
        const contadorDesc = document.getElementById('contador-desc');
        contadorDesc.textContent = descTextarea.value.length;
        descTextarea.addEventListener('input', function () {
            contadorDesc.textContent = this.value.length;
        });

        // ── Validaciones individuales ─────────────────────────────
        function validarMarca() {
            const valor = marcaInput.value.trim();
            const tipo  = tipoSelect.value;

            if (!valor) {
                setError('marca', 'error-marca', 'La marca es obligatoria.');
                return false;
            }
            if (!marcaEsValidaParaTipo(valor, tipo)) {
                setError('marca', 'error-marca',
                    `Esa marca no corresponde a un ${tipo}. Por favor selecciona del listado.`);
                return false;
            }
            const tipoContrario = tipo === 'Carro' ? 'Moto' : 'Carro';
            if (marcaEsValidaParaTipo(valor, tipoContrario)) {
                setError('marca', 'error-marca',
                    `Esa marca aparece en ambos tipos. Por favor selecciona nuevamente para confirmar el tipo.`);
                return false;
            }
            setError('marca', 'error-marca', '');
            return true;
        }

        function validarModelo() {
            const valor = document.getElementById('modelo').value.trim();
            if (!valor)               { setError('modelo', 'error-modelo', 'El año del modelo es obligatorio.');        return false; }
            if (!/^\d+$/.test(valor)) { setError('modelo', 'error-modelo', 'El año debe contener solo números.');       return false; }
            if (valor.length !== 4)   { setError('modelo', 'error-modelo', 'El año debe tener exactamente 4 dígitos.'); return false; }
            const anio = parseInt(valor);
            const hoy  = new Date().getFullYear();
            if (anio < 1900)    { setError('modelo', 'error-modelo', 'El año no puede ser anterior a 1900.');    return false; }
            if (anio > hoy + 1) { setError('modelo', 'error-modelo', `El año no puede ser mayor a ${hoy + 1}.`); return false; }
            setError('modelo', 'error-modelo', '');
            return true;
        }

        function validarPrecio() {
            const str   = document.getElementById('precio').value.trim();
            const valor = parseFloat(str);
            if (!str)                       { setError('precio', 'error-precio', 'El precio es obligatorio.');                return false; }
            if (isNaN(valor) || valor <= 0) { setError('precio', 'error-precio', 'El precio debe ser mayor a cero.');         return false; }
            if (valor < 5000000)            { setError('precio', 'error-precio', 'El precio mínimo permitido es $5,000,000.'); return false; }
            if (valor > 9999999999)         { setError('precio', 'error-precio', 'El precio ingresado es demasiado alto.');   return false; }
            setError('precio', 'error-precio', '');
            return true;
        }

        function validarDescripcion() {
            const valor = document.getElementById('descripcion').value.trim();
            if (valor.length > 500) {
                setError('descripcion', 'error-descripcion', 'La descripción no puede superar los 500 caracteres.');
                return false;
            }
            setError('descripcion', 'error-descripcion', '');
            return true;
        }

        // ── Listeners en tiempo real ──────────────────────────────
        document.getElementById('modelo').addEventListener('input',      validarModelo);
        document.getElementById('precio').addEventListener('input',      validarPrecio);
        document.getElementById('descripcion').addEventListener('input', validarDescripcion);

        function validarYEnviar() {
            const m  = validarMarca();
            const mo = validarModelo();
            const p  = validarPrecio();
            const d  = validarDescripcion();
            if (m && mo && p && d) {
                document.getElementById('form-vehiculo').submit();
            }
        }

        // Mostrar errores del servidor al volver con back()
        @if ($errors->has('marca'))
            setError('marca',  'error-marca',  '{{ $errors->first('marca') }}');
        @endif
        @if ($errors->has('modelo'))
            setError('modelo', 'error-modelo', '{{ $errors->first('modelo') }}');
        @endif
        @if ($errors->has('precio'))
            setError('precio', 'error-precio', '{{ $errors->first('precio') }}');
        @endif
    </script>
</x-app-layout>
