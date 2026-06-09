document.addEventListener('DOMContentLoaded', function () {

    function blockSpaces(input) {
        var interval = null;

        function limpiar() {
            var pos = input.selectionStart;
            var cleaned = input.value.replace(/\s/g, '');
            if (cleaned !== input.value) {
                input.value = cleaned;
                try {
                    input.selectionStart = input.selectionEnd = Math.max(0, pos - 1);
                } catch(e) {}
            }
        }

        // Todos los eventos posibles
        input.addEventListener('keydown', function (e) {
            if (e.key === ' ' || e.code === 'Space' || e.keyCode === 32) {
                e.preventDefault();
            }
        });

        input.addEventListener('keyup', limpiar);
        input.addEventListener('input', limpiar);
        input.addEventListener('compositionend', limpiar);
        input.addEventListener('change', limpiar);

        // Intervalo activo solo cuando el input tiene foco (clave para Android)
        input.addEventListener('focus', function () {
            interval = setInterval(limpiar, 100);
        });

        input.addEventListener('blur', function () {
            clearInterval(interval);
            limpiar();
        });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text');
            var cleaned = pasted.replace(/\s/g, '');
            var start = input.selectionStart;
            var end = input.selectionEnd;
            input.value = input.value.substring(0, start) + cleaned + input.value.substring(end);
            input.selectionStart = input.selectionEnd = start + cleaned.length;
        });
    }

    document.querySelectorAll('input[type="email"], input[type="password"]').forEach(function (input) {
        blockSpaces(input);
    });

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('input[type="email"], input[type="password"]').forEach(function (input) {
                input.value = input.value.trim();
            });
        });
    });

});
