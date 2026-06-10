<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agregar Empleado') }}
        </h2>
    </x-slot>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Éxito --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Empleado agregado correctamente',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @endif

    {{-- Error general --}}
    @if ($errors->has('general'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ $errors->first('general') }}'
                });
            });
        </script>
    @endif

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-md rounded">
                <h2 class="text-lg font-semibold mb-4">Nuevo Empleado</h2>

                <form id="formEmpleado" action="{{ route('empleados.store') }}" method="POST">
                    @csrf

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label class="block font-medium mb-1">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                            class="w-full border px-3 py-2 rounded {{ $errors->has('nombre') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                        @error('nombre')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Puesto --}}
                    <div class="mb-4">
                        <label class="block font-medium mb-1">Puesto:</label>
                        <input type="text" id="puesto" name="puesto" value="{{ old('puesto') }}"
                            readonly
                            class="w-full border px-3 py-2 rounded bg-gray-50 cursor-pointer {{ $errors->has('puesto') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                        @error('puesto')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Salario --}}
                    <div class="mb-4">
                        <label class="block font-medium mb-1">Salario:</label>
                        <input type="number" step="0.01" min="1700000" name="salario" value="{{ old('salario') }}"
                            class="w-full border px-3 py-2 rounded {{ $errors->has('salario') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                        @error('salario')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block font-medium mb-1">Email:</label>
                        <input type="text" id="email_empleado" name="email" value="{{ old('email') }}"
                            autocomplete="off"
                            class="w-full border px-3 py-2 rounded {{ $errors->has('email') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex space-x-4">
                        <button type="button" onclick="confirmarEnvio()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Guardar
                        </button>

                        <a href="{{ route('empleados.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmarEnvio() {
            Swal.fire({
                title: '¿Guardar empleado?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Guardando...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('formEmpleado').submit();
                }
            });
        }
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

            nombreInput.closest('form').addEventListener('submit', function (e) {
                const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü]+( [A-Za-zÁÉÍÓÚáéíóúÑñÜü]+)*$/;
                if (!soloLetras.test(nombreInput.value.trim())) {
                    e.preventDefault();
                    nombreInput.setCustomValidity('Solo letras y espacios simples entre palabras.');
                    nombreInput.reportValidity();
                } else {
                    nombreInput.setCustomValidity('');
                }
            });
        }

        // ── PUESTO (solo opciones predefinidas, readonly + dropdown SweetAlert) ──
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
                    customClass: { input: 'swal2-select' }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        puestoInput.value = result.value;
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

            let activeSuffix = null;

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
                activeSuffix = domain;
                emailInput.value = localPart + domain;
                const pos = localPart.length;
                emailInput.focus();
                emailInput.selectionStart = emailInput.selectionEnd = pos;
                hideDropdown();
            }

            function suffixStart() {
                if (!activeSuffix) return -1;
                const idx = emailInput.value.lastIndexOf(activeSuffix);
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
                        activeSuffix = null;
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

                if (activeSuffix) {
                    if (!this.value.endsWith(activeSuffix)) {
                        const local = getLocalPart(this.value);
                        this.value = local + activeSuffix;
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

                if (activeSuffix) {
                    if (pasted.includes('@')) pasted = pasted.split('@')[0];
                    const sfxStart = suffixStart();
                    const local    = sfxStart !== -1 ? this.value.substring(0, sfxStart) : '';
                    const start    = Math.min(this.selectionStart, sfxStart !== -1 ? sfxStart : local.length);
                    const end      = Math.min(this.selectionEnd,   sfxStart !== -1 ? sfxStart : local.length);
                    this.value = local.substring(0, start) + pasted + local.substring(end) + activeSuffix;
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
                if (atIdx !== -1 && !activeSuffix) {
                    showDropdown(this.value.substring(0, atIdx));
                }
            });
        }

    });
    </script>

</x-app-layout>
