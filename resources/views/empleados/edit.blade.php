<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Empleado') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow rounded">
                <form action="{{ route('empleados.update', $empleado->id) }}"
                      method="POST"
                      id="form-empleado"
                      class="flex flex-col gap-4">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div>
                        <label class="block font-semibold mb-1">Nombre</label>
                        <input type="text" name="nombre" id="nombre"
                               value="{{ old('nombre', $empleado->nombre) }}"
                               maxlength="80"
                               placeholder="Ej: Juan Pérez"
                               class="border p-2 rounded w-full border-gray-300">
                        <p class="text-red-600 text-sm mt-1" id="error-nombre">
                            @error('nombre'){{ $message }}@enderror
                        </p>
                    </div>

                    <!-- Puesto -->
                    <div>
                        <label class="block font-semibold mb-1">Puesto</label>
                        <input type="text" name="puesto" id="puesto"
                               value="{{ old('puesto', $empleado->puesto) }}"
                               maxlength="100"
                               placeholder="Ej: Desarrollador, Vendedor"
                               class="border p-2 rounded w-full border-gray-300">
                        <p class="text-red-600 text-sm mt-1" id="error-puesto">
                            @error('puesto'){{ $message }}@enderror
                        </p>
                    </div>

                    <!-- Salario -->
                    <div>
                        <label class="block font-semibold mb-1">Salario</label>
                        <input type="number" step="0.01" name="salario" id="salario"
                               value="{{ old('salario', $empleado->salario) }}"
                               min="0"
                               max="999999999"
                               placeholder="Ej: 2500000"
                               class="border p-2 rounded w-full border-gray-300">
                        <p class="text-red-600 text-sm mt-1" id="error-salario">
                            @error('salario'){{ $message }}@enderror
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block font-semibold mb-1">Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $empleado->email) }}"
                               maxlength="100"
                               placeholder="Ej: juan@empresa.com"
                               class="border p-2 rounded w-full border-gray-300">
                        <p class="text-red-600 text-sm mt-1" id="error-email">
                            @error('email'){{ $message }}@enderror
                        </p>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 mt-4">
                        <a href="{{ route('empleados.index') }}"
                           class="bg-white hover:bg-gray-200 text-black font-semibold px-4 py-2 rounded flex-1 border text-center">
                            Cancelar
                        </a>
                        <button type="button" onclick="validarYEnviar()"
                                class="bg-white hover:bg-gray-200 text-black font-semibold px-4 py-2 rounded flex-1 border">
                            Guardar cambios
                        </button>
                    </div>
                </form>
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

        // ── Bloquear números y símbolos en "nombre" y "puesto" ───
        ['nombre', 'puesto'].forEach(function(id) {
            document.getElementById(id).addEventListener('keydown', function (e) {
                const teclaControl = ['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'].includes(e.key);
                if (teclaControl) return;
                if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]$/.test(e.key)) {
                    e.preventDefault();
                }
            });

            document.getElementById(id).addEventListener('paste', function (e) {
                const texto = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]+$/.test(texto)) {
                    e.preventDefault();
                    setError(id, 'error-' + id, 'Este campo solo puede contener letras.');
                }
            });
        });

        // ── Validaciones individuales ────────────────────────────
        function validarNombre() {
            const valor = document.getElementById('nombre').value.trim();
            if (!valor)            { setError('nombre', 'error-nombre', 'El nombre es obligatorio.');                      return false; }
            if (valor.length < 2)  { setError('nombre', 'error-nombre', 'El nombre debe tener al menos 2 caracteres.');    return false; }
            if (valor.length > 80) { setError('nombre', 'error-nombre', 'El nombre no puede superar los 80 caracteres.');  return false; }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]+$/.test(valor)) {
                setError('nombre', 'error-nombre', 'El nombre solo puede contener letras.');
                return false;
            }
            if (/[^aeiouáéíóúüAEIOUÁÉÍÓÚÜ]{5,}/i.test(valor)) {
                setError('nombre', 'error-nombre', 'Por favor ingresa un nombre válido.');
                return false;
            }
            if (/(.)\1{3,}/.test(valor)) {
                setError('nombre', 'error-nombre', 'Por favor ingresa un nombre válido.');
                return false;
            }
            setError('nombre', 'error-nombre', ''); return true;
        }

        function validarPuesto() {
            const valor = document.getElementById('puesto').value.trim();
            if (!valor)             { setError('puesto', 'error-puesto', 'El puesto es obligatorio.');                      return false; }
            if (valor.length < 2)   { setError('puesto', 'error-puesto', 'El puesto debe tener al menos 2 caracteres.');   return false; }
            if (valor.length > 100) { setError('puesto', 'error-puesto', 'El puesto no puede superar los 100 caracteres.'); return false; }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]+$/.test(valor)) {
                setError('puesto', 'error-puesto', 'El puesto solo puede contener letras.');
                return false;
            }
            setError('puesto', 'error-puesto', ''); return true;
        }

        function validarSalario() {
            const str   = document.getElementById('salario').value.trim();
            const valor = parseFloat(str);
            if (!str)                       { setError('salario', 'error-salario', 'El salario es obligatorio.');              return false; }
            if (isNaN(valor) || valor < 0)  { setError('salario', 'error-salario', 'El salario debe ser un número positivo.'); return false; }
            if (valor > 999999999)          { setError('salario', 'error-salario', 'El salario ingresado es demasiado alto.'); return false; }
            setError('salario', 'error-salario', ''); return true;
        }

        function validarEmail() {
            const valor = document.getElementById('email').value.trim();
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!valor) {
                setError('email', 'error-email', 'El email es obligatorio.');
                return false;
            }
            if (valor.length > 100) {
                setError('email', 'error-email', 'El email no puede superar los 100 caracteres.');
                return false;
            }
            if (!regexEmail.test(valor)) {
                setError('email', 'error-email', 'Ingresa un email válido. Ej: juan@empresa.com');
                return false;
            }
            // Validar que el dominio no sea absurdo (mínimo 2 chars después del punto)
            const dominio = valor.split('@')[1];
            if (dominio && dominio.split('.').pop().length < 2) {
                setError('email', 'error-email', 'El dominio del email no es válido.');
                return false;
            }
            setError('email', 'error-email', ''); return true;
        }

        // ── Envío ────────────────────────────────────────────────
        function validarYEnviar() {
            const n = validarNombre();
            const p = validarPuesto();
            const s = validarSalario();
            const e = validarEmail();
            if (n && p && s && e) {
                document.getElementById('form-empleado').submit();
            }
        }

        // ── Listeners en tiempo real ─────────────────────────────
        document.getElementById('nombre').addEventListener('input',  validarNombre);
        document.getElementById('puesto').addEventListener('input',  validarPuesto);
        document.getElementById('salario').addEventListener('input', validarSalario);
        document.getElementById('email').addEventListener('input',   validarEmail);
    </script>
</x-app-layout>