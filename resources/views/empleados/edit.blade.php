<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Empleado') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                               readonly
                               class="border p-2 rounded w-full border-gray-300 bg-gray-50 cursor-pointer">
                        <p class="text-red-600 text-sm mt-1" id="error-puesto">
                            @error('puesto'){{ $message }}@enderror
                        </p>
                    </div>

                    <!-- Salario -->
                    <div>
                        <label class="block font-semibold mb-1">Salario</label>
                        <input type="number" step="0.01" name="salario" id="salario"
                               value="{{ old('salario', $empleado->salario) }}"
                               min="1750905"
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
                        <input type="text" name="email" id="email_empleado"
                               value="{{ old('email', $empleado->email) }}"
                               maxlength="100"
                               autocomplete="off"
                               placeholder="Ej: juan@gmail.com"
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

        // ── Validaciones individuales ────────────────────────────
        function validarNombre() {
            const valor = document.getElementById('nombre').value.trim();
            if (!valor)            { setError('nombre', 'error-nombre', 'El nombre es obligatorio.');                      return false; }
            if (valor.length < 2)  { setError('nombre', 'error-nombre', 'El nombre debe tener al menos 2 caracteres.');    return false; }
            if (valor.length > 80) { setError('nombre', 'error-nombre', 'El nombre no puede superar los 80 caracteres.');  return false; }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(valor)) {
                setError('nombre', 'error-nombre', 'El nombre solo puede contener letras.');
                return false;
            }
            setError('nombre', 'error-nombre', ''); return true;
        }

        function validarPuesto() {
            const valor = document.getElementById('puesto').value.trim();
            if (!valor) { setError('puesto', 'error-puesto', 'El puesto es obligatorio.'); return false; }
            setError('puesto', 'error-puesto', ''); return true;
        }

        function validarSalario() {
            const str   = document.getElementById('salario').value.trim();
            const valor = parseFloat(str);
            if (!str)                        { setError('salario', 'error-salario', 'El salario es obligatorio.');                              return false; }
            if (isNaN(valor) || valor <= 0)  { setError('salario', 'error-salario', 'El salario debe ser un número positivo.');                 return false; }
            if (valor < 1750905)             { setError('salario', 'error-salario', 'El salario mínimo permitido es de $1.750.905 (SMLMV 2026).'); return false; }
            if (valor > 999999999)           { setError('salario', 'error-salario', 'El salario ingresado es demasiado alto.');                 return false; }
            setError('salario', 'error-salario', ''); return true;
        }

        function validarEmail() {
            const valor = document.getElementById('email_empleado').value.trim();
            if (!valor) {
                setError('email_empleado', 'error-email', 'El email es obligatorio.');
                return false;
            }
            if (!activeSuffix) {
                setError('email_empleado', 'error-email', 'Selecciona un dominio del listado (ej: @gmail.com).');
                return false;
            }
            setError('email_empleado', 'error-email', ''); return true;
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
        document.getElementById('salario').addEventListener('input', validarSalario);
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── NOMBRE ──────────────────────────────────────────────────────────
        const nombreInput = document.getElementById('nombre');
        if (nombreInput) {
            nombreInput.addEventListener('keydown', function (e) {
                if ((e.key === ' ' || e.code === 'Space') && this.value.length === 0) {
                    e.preventDefault();
                }
            });

            nombreInput.addEventListener('input', function () {
                const pos = this.selectionStart;
                let cleaned = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü ]/g, '');
                cleaned = cleaned.replace(/  +/g, ' ');
                cleaned = cleaned.replace(/^ /, '');
                if (this.value !== cleaned) {
                    this.value = cleaned;
                    this.selectionStart = this.selectionEnd = Math.min(pos, cleaned.length);
                }
            });

            nombreInput.addEventListener('paste', function (e) {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text');
                let cleaned = pasted.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü ]/g, '').replace(/  +/g, ' ').trim();
                const start = this.selectionStart;
                const end   = this.selectionEnd;
                this.value  = this.value.substring(0, start) + cleaned + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + cleaned.length;
            });
        }

        // ── PUESTO (readonly + dropdown SweetAlert) ──────────────────────────
        const puestoInput = document.getElementById('puesto');
        if (puestoInput) {
            const PUESTOS = [
                'Empleado',
                'Empleada'
            ];

            puestoInput.addEventListener('click', function () {
                Swal.fire({
                    title: 'Seleccionar Puesto',
                    input: 'select',
                    inputOptions: PUESTOS.reduce((acc, p) => { acc[p] = p; return acc; }, {}),
                    inputValue: this.value || '',
                    showCancelButton: true,
                    confirmButtonText: 'Seleccionar',
                    cancelButtonText: 'Cancelar',
                    inputPlaceholder: 'Selecciona un puesto',
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        puestoInput.value = result.value;
                        setError('puesto', 'error-puesto', '');
                    }
                });
            });
        }

        // ── EMAIL ────────────────────────────────────────────────────────────
        const emailInput = document.getElementById('email_empleado');
        if (emailInput) {

            const DOMAINS = [
                '@gmail.com',
                '@hotmail.com',
                '@outlook.com',
                '@yahoo.com',
                '@mail.com',
                '@icloud.com',
                '@protonmail.com',
                '@proton.me',
            ];

            const dropdown = document.createElement('ul');
            dropdown.style.cssText = `
                position: absolute;
                background: #fff;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                box-shadow: 0 4px 16px rgba(0,0,0,0.10);
                list-style: none;
                margin: 2px 0 0 0;
                padding: 4px 0;
                width: ${emailInput.offsetWidth}px;
                z-index: 9999;
                display: none;
                font-family: inherit;
                font-size: 0.95em;
            `;
            emailInput.parentElement.style.position = 'relative';
            emailInput.parentElement.appendChild(dropdown);

            // Detectar si el valor actual ya tiene un dominio válido
            const valorActual = emailInput.value;
            DOMAINS.forEach(function (d) {
                if (valorActual.endsWith(d)) {
                    window.activeSuffix = d;
                }
            });
            if (typeof window.activeSuffix === 'undefined') window.activeSuffix = null;

            function getLocalPart(val) {
                const atIdx = val.indexOf('@');
                return atIdx !== -1 ? val.substring(0, atIdx) : val;
            }

            function showDropdown(localPart) {
                dropdown.innerHTML = '';
                DOMAINS.forEach(function (domain) {
                    const li = document.createElement('li');
                    li.textContent = localPart + domain;
                    li.style.cssText = `
                        padding: 8px 14px;
                        cursor: pointer;
                        color: #374151;
                        transition: background 0.15s;
                    `;
                    li.addEventListener('mouseenter', function () { this.style.background = '#f3f4f6'; });
                    li.addEventListener('mouseleave', function () { this.style.background = ''; });
                    li.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        selectDomain(domain, localPart);
                    });
                    dropdown.appendChild(li);
                });
                dropdown.style.width = emailInput.offsetWidth + 'px';
                dropdown.style.display = 'block';
            }

            function hideDropdown() {
                dropdown.style.display = 'none';
            }

            function selectDomain(domain, localPart) {
                window.activeSuffix = domain;
                emailInput.value = localPart + domain;
                const pos = localPart.length;
                emailInput.focus();
                emailInput.selectionStart = emailInput.selectionEnd = pos;
                hideDropdown();
            }

            function suffixStart() {
                if (!window.activeSuffix) return -1;
                const idx = emailInput.value.lastIndexOf(window.activeSuffix);
                return idx !== -1 ? idx : -1;
            }

            emailInput.addEventListener('keydown', function (e) {
                if (e.key === ' ' || e.code === 'Space') {
                    e.preventDefault();
                    return;
                }

                const pos      = this.selectionStart;
                const selEnd   = this.selectionEnd;
                const sfxStart = suffixStart();

                if (sfxStart === -1) return;

                if (e.key === 'Backspace' || e.key === 'Delete') {
                    if (pos <= sfxStart && selEnd <= sfxStart) return;
                    if (pos === sfxStart && selEnd === sfxStart) {
                        window.activeSuffix = null;
                        return;
                    }
                    e.preventDefault();
                    return;
                }

                if (pos > sfxStart || selEnd > sfxStart) {
                    if (e.key.length === 1) e.preventDefault();
                }
            });

            emailInput.addEventListener('input', function () {
                const val = this.value.replace(/\s/g, '');
                if (this.value !== val) this.value = val;

                if (window.activeSuffix) {
                    if (!this.value.endsWith(window.activeSuffix)) {
                        const local = getLocalPart(this.value);
                        this.value = local + window.activeSuffix;
                    }
                    hideDropdown();
                    return;
                }

                const atIdx = this.value.indexOf('@');
                if (atIdx !== -1) {
                    const local = this.value.substring(0, atIdx);
                    const typed = this.value.substring(atIdx);
                    const matches = DOMAINS.filter(d => d.startsWith(typed));
                    if (matches.length > 0) {
                        showDropdown(local);
                    } else {
                        hideDropdown();
                    }
                } else {
                    hideDropdown();
                }
            });

            emailInput.addEventListener('paste', function (e) {
                e.preventDefault();
                let pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\s/g, '');

                if (window.activeSuffix) {
                    if (pasted.includes('@')) pasted = pasted.split('@')[0];
                    const sfxStart = suffixStart();
                    const local    = sfxStart !== -1 ? this.value.substring(0, sfxStart) : '';
                    const start    = Math.min(this.selectionStart, sfxStart !== -1 ? sfxStart : local.length);
                    const end      = Math.min(this.selectionEnd,   sfxStart !== -1 ? sfxStart : local.length);
                    this.value = local.substring(0, start) + pasted + local.substring(end) + window.activeSuffix;
                    this.selectionStart = this.selectionEnd = start + pasted.length;
                } else {
                    const start = this.selectionStart;
                    const end   = this.selectionEnd;
                    this.value  = this.value.substring(0, start) + pasted + this.value.substring(end);
                    this.selectionStart = this.selectionEnd = start + pasted.length;
                    const atIdx = this.value.indexOf('@');
                    if (atIdx !== -1) showDropdown(this.value.substring(0, atIdx));
                }
            });

            emailInput.addEventListener('click', function () {
                const sfxStart = suffixStart();
                if (sfxStart !== -1 && this.selectionStart > sfxStart) {
                    this.selectionStart = this.selectionEnd = sfxStart;
                }
            });

            emailInput.addEventListener('blur', function () {
                setTimeout(hideDropdown, 150);
            });

            emailInput.addEventListener('focus', function () {
                const atIdx = this.value.indexOf('@');
                if (atIdx !== -1 && !window.activeSuffix) {
                    showDropdown(this.value.substring(0, atIdx));
                }
            });
        }

    });
    </script>

</x-app-layout>
