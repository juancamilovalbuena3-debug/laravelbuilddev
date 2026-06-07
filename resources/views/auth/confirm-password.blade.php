<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>
        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>
        <x-validation-errors class="mb-4" />
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>
            <div class="flex justify-end mt-4">
                <x-button class="ms-4">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('password');
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
});
</script>
