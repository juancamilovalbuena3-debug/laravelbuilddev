<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Actualizar contraseña') }}
    </x-slot>
    <x-slot name="description">
        {{ __('Asegúrate de que tu cuenta use una contraseña larga y aleatoria para mantenerse segura.') }}
    </x-slot>
    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Contraseña actual') }}" />
            <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('Nueva contraseña') }}" />
            <x-input id="password" type="password" class="mt-1 block w-full" wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>
    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>
        <x-button>
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function applyNoSpace(input) {
        if (!input) return;
        input.addEventListener('keydown', function (e) {
            if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
            }
        });
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text');
            var cleaned = pasted.replace(/\s/g, '');
            var start = this.selectionStart;
            var end = this.selectionEnd;
            this.value = this.value.substring(0, start) + cleaned + this.value.substring(end);
            this.selectionStart = this.selectionEnd = start + cleaned.length;
        });
    }

    applyNoSpace(document.getElementById('current_password'));
    applyNoSpace(document.getElementById('password'));
    applyNoSpace(document.getElementById('password_confirmation'));

    document.addEventListener('livewire:navigated', function () {
        applyNoSpace(document.getElementById('current_password'));
        applyNoSpace(document.getElementById('password'));
        applyNoSpace(document.getElementById('password_confirmation'));
    });
});
</script>
