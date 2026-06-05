<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Publicar Vehículo en Venta') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-cover bg-center min-h-screen" style="background-image: url('{{ asset('images/logo.jpg') }}');">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 overflow-hidden shadow-xl sm:rounded-lg flex">
                <div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6 w-full">

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->has('general'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Error:</strong> {{ $errors->first('general') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('vender.store') }}"
                          id="form-vehiculo"
                          class="space-y-4"
                          enctype="multipart/form-data">
                        @csrf

                        <!-- Tipo -->
                        <div>
                            <label for="tipo" class="block font-semibold mb-1">Tipo:</label>
                            <select name="tipo" id="tipo" class="border rounded p-2 w-full border-gray-300">
                                <option value="Carro" {{ old('tipo') == 'Carro' ? 'selected' : '' }}>Carro</option>
                                <option value="Moto"  {{ old('tipo') == 'Moto'  ? 'selected' : '' }}>Moto</option>
                            </select>
                        </div>

                        <!-- Marca -->
                        <div>
                            <label for="marca" class="block font-semibold mb-1">Marca:</label>
                            <input type="text" name="marca" id="marca" value="{{ old('marca') }}"
                                   maxlength="80"
                                   placeholder="Ej: Toyota, Chevrolet, Bajaj"
                                   class="border rounded p-2 w-full border-gray-300">
                            <p class="text-red-600 text-sm mt-1" id="error-marca"></p>
                        </div>

                        <!-- Modelo -->
                        <div>
                            <label for="modelo" class="block font-semibold mb-1">Modelo (año):</label>
                            <input type="text" name="modelo" id="modelo" value="{{ old('modelo') }}"
                                   maxlength="4"
                                   inputmode="numeric"
                                   pattern="\d{4}"
                                   placeholder="Ej: 2024"
                                   class="border rounded p-2 w-full border-gray-300">
                            <p class="text-red-600 text-sm mt-1" id="error-modelo"></p>
                        </div>

                        <!-- Precio -->
                        <div>
                            <label for="precio" class="block font-semibold mb-1">Precio:</label>
                            <input type="number" name="precio" id="precio" value="{{ old('precio') }}"
                                   min="5000000"
                                   max="9999999999"
                                   class="border rounded p-2 w-full border-gray-300">
                            <p class="text-red-600 text-sm mt-1" id="error-precio"></p>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block font-semibold mb-1">Descripción:</label>
                            <textarea name="descripcion" id="descripcion" rows="4"
                                      maxlength="500"
                                      class="border rounded p-2 w-full border-gray-300">{{ old('descripcion') }}</textarea>
                            <p class="text-xs text-gray-400 text-right mt-1">
                                <span id="contador-desc">0</span>/500 caracteres
                            </p>
                            <p class="text-red-600 text-sm mt-1" id="error-descripcion"></p>
                        </div>

                        <!-- Imagen -->
                        <div>
                            <label for="imagen" class="block font-semibold mb-1">Imagen del vehículo (opcional):</label>
                            <input type="file" name="imagen" id="imagen" accept="image/*"
                                   class="border rounded p-2 w-full border-gray-300">
                            <!-- Vista previa -->
                            <div id="preview-container" class="hidden mt-3">
                                <img id="preview-imagen" src="" alt="Vista previa"
                                     class="h-40 object-cover rounded-lg border border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <!-- Botón publicar -->
                        <div class="text-center pt-2">
                            <button type="button" onclick="validarYConfirmar()"
                                    class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg text-lg shadow-md transition-all duration-300 transform hover:scale-105 hover:bg-blue-700">
                                🚗 Publicar Vehículo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Modal de Confirmación ── -->
    <div id="modalConfirmar"
         class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div id="modalContenido"
             class="bg-white p-8 rounded-2xl shadow-2xl max-w-sm w-full mx-4 text-center transform transition-transform duration-300 scale-95">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                🚗
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">¿Publicar vehículo?</h3>
            <div class="text-sm text-gray-600 space-y-1 mb-6 text-left bg-gray-50 rounded-xl p-4">
                <p><span class="font-semibold">Tipo:</span> <span id="modal-tipo"></span></p>
                <p><span class="font-semibold">Marca:</span> <span id="modal-marca"></span></p>
                <p><span class="font-semibold">Modelo:</span> <span id="modal-modelo"></span></p>
                <p><span class="font-semibold">Precio:</span> <span id="modal-precio" class="text-green-700 font-bold"></span></p>
            </div>
            <div class="flex flex-col gap-3">
                <button type="button" onclick="confirmarPublicacion()"
                        class="w-full px-4 py-3 bg-blue-600 text-white hover:bg-blue-700 rounded-xl font-bold shadow-md transition-all hover:shadow-lg">
                    ✅ Sí, publicar ahora
                </button>
                <button type="button" onclick="cerrarModal()"
                        class="w-full px-4 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors">
                    ✏️ Seguir editando
                </button>
            </div>
        </div>
    </div>

    <script>
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

        // ── Bloquear números y símbolos en "marca" ───────────────
        document.getElementById('marca').addEventListener('keydown', function (e) {
            const teclaControl = ['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'].includes(e.key);
            if (teclaControl) return;
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]$/.test(e.key)) e.preventDefault();
        });

        document.getElementById('marca').addEventListener('paste', function (e) {
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]+$/.test(texto)) {
                e.preventDefault();
                setError('marca', 'error-marca', 'La marca solo puede contener letras.');
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

        // ── Vista previa de imagen ────────────────────────────────
        document.getElementById('imagen').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-imagen').src = e.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        // ── Marcas válidas ────────────────────────────────────────
        const marcasValidas = [
            'toyota','chevrolet','ford','honda','nissan','hyundai','kia','mazda',
            'volkswagen','vw','renault','fiat','peugeot','citroen','seat','skoda',
            'bmw','mercedes','mercedes benz','audi','volvo','jeep','dodge','ram',
            'chrysler','cadillac','buick','gmc','lincoln','tesla','subaru','mitsubishi',
            'suzuki','isuzu','daewoo','ssangyong','chery','great wall','jac','byd',
            'geely','haval','dfsk','zotye','foton','hafei','brilliance','changan',
            'porsche','ferrari','lamborghini','maserati','alfa romeo','lancia',
            'land rover','range rover','jaguar','mini','bentley','rolls royce',
            'aston martin','mclaren','bugatti','lexus','infiniti','acura','genesis',
            'rivian','lucid','polestar','dacia','lada','opel','vauxhall','holden',
            'saab','pontiac','oldsmobile','hummer','saturn','scion','trabant',
            'bajaj','yamaha','kawasaki','ktm','ducati','triumph','harley',
            'harley davidson','royal enfield','benelli','aprilia','bmw motorrad',
            'husqvarna','norton','indian','moto guzzi','mv agusta','bimota',
            'energica','zero motorcycles','akt','hero','tvs','pulsar','lifan',
            'loncin','zongshen','cfmoto','kymco','sym','italika','veloci','auteco',
            'jianshe','skygo','bera','ranger','corven','zanella','mondial','motomel',
            'beta','guerrero','gilera','cuxi','wanxin','yumbo','forza','dinamo',
            'skyjet','leopard','condor','igm','akt motos','auteco mobility',
            'vespa','piaggio','derbi','rieju','sherco','gasgas','husaberg',
            'ossa','montesa','bultaco','sanglas'
        ];

        // ── Validaciones individuales ─────────────────────────────
        function validarMarca() {
            const valor = document.getElementById('marca').value.trim();
            if (!valor)          { setError('marca', 'error-marca', 'La marca es obligatoria.');                       return false; }
            if (valor.length < 2){ setError('marca', 'error-marca', 'La marca debe tener al menos 2 caracteres.');    return false; }
            if (valor.length > 80){ setError('marca', 'error-marca', 'La marca no puede superar los 80 caracteres.'); return false; }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]+$/.test(valor)) {
                setError('marca', 'error-marca', 'La marca solo puede contener letras.');
                return false;
            }
            if (!marcasValidas.includes(valor.toLowerCase())) {
                setError('marca', 'error-marca', 'Por favor ingresa una marca de vehículo reconocida.');
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
            if (anio < 1900)     { setError('modelo', 'error-modelo', 'El año no puede ser anterior a 1900.');    return false; }
            if (anio > hoy + 1)  { setError('modelo', 'error-modelo', `El año no puede ser mayor a ${hoy + 1}.`); return false; }
            setError('modelo', 'error-modelo', '');
            return true;
        }

        function validarPrecio() {
            const str   = document.getElementById('precio').value.trim();
            const valor = parseFloat(str);
            if (!str)                       { setError('precio', 'error-precio', 'El precio es obligatorio.');                 return false; }
            if (isNaN(valor) || valor <= 0) { setError('precio', 'error-precio', 'El precio debe ser mayor a cero.');          return false; }
            if (valor < 5000000)            { setError('precio', 'error-precio', 'El precio mínimo permitido es $5,000,000.');  return false; }
            if (valor > 9999999999)         { setError('precio', 'error-precio', 'El precio ingresado es demasiado alto.');    return false; }
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
        document.getElementById('marca').addEventListener('input',       validarMarca);
        document.getElementById('modelo').addEventListener('input',      validarModelo);
        document.getElementById('precio').addEventListener('input',      validarPrecio);
        document.getElementById('descripcion').addEventListener('input', validarDescripcion);

        // ── Modal ─────────────────────────────────────────────────
        function validarYConfirmar() {
            const m  = validarMarca();
            const mo = validarModelo();
            const p  = validarPrecio();
            const d  = validarDescripcion();

            if (!m || !mo || !p || !d) return; // hay errores, no abrir modal

            // Rellenar datos del modal
            document.getElementById('modal-tipo').textContent   = document.getElementById('tipo').value;
            document.getElementById('modal-marca').textContent  = document.getElementById('marca').value.trim();
            document.getElementById('modal-modelo').textContent = document.getElementById('modelo').value.trim();

            const precio = parseFloat(document.getElementById('precio').value);
            document.getElementById('modal-precio').textContent =
                '$' + new Intl.NumberFormat('es-CO').format(precio) + ' COP';

            // Mostrar modal con animación
            const modal    = document.getElementById('modalConfirmar');
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
            const modal    = document.getElementById('modalConfirmar');
            const contenido = document.getElementById('modalContenido');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            contenido.classList.remove('scale-100');
            contenido.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function confirmarPublicacion() {
            document.getElementById('form-vehiculo').submit();
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalConfirmar').addEventListener('click', function (e) {
            if (e.target === this) cerrarModal();
        });

        // ── Errores del servidor (Laravel) ────────────────────────
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