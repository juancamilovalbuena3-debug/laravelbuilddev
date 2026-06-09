(function () {

    function limpiar(input) {
        var pos = input.selectionStart;
        var cleaned = input.value.replace(/\s/g, '');
        if (cleaned !== input.value) {
            input.value = cleaned;
            try {
                input.selectionStart = input.selectionEnd = Math.max(0, pos - 1);
            } catch (e) {}
        }
    }

    function blockSpaces(input) {
        if (input._noSpaces) return; // evitar duplicados
        input._noSpaces = true;

        var interval = null;

        input.addEventListener('keydown', function (e) {
            if (e.key === ' ' || e.code === 'Space' || e.keyCode === 32) {
                e.preventDefault();
            }
        });

        input.addEventListener('keyup',          function () { limpiar(input); });
        input.addEventListener('input',           function () { limpiar(input); });
        input.addEventListener('compositionend',  function () { limpiar(input); });
        input.addEventListener('change',          function () { limpiar(input); });

        input.addEventListener('focus', function () {
            interval = setInterval(function () { limpiar(input); }, 100);
        });

        input.addEventListener('blur', function () {
            clearInterval(interval);
            limpiar(input);
        });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text');
            var cleaned = pasted.replace(/\s/g, '');
            var start = input.selectionStart;
            var end   = input.selectionEnd;
            input.value = input.value.substring(0, start) + cleaned + input.value.substring(end);
            input.selectionStart = input.selectionEnd = start + cleaned.length;
        });
    }

    // Selector amplio: type, name e id comunes de login/register/perfil
    var SELECTOR = [
        'input[type="email"]',
        'input[type="password"]',
        'input[name="email"]',
        'input[name="password"]',
        'input[name="current_password"]',
        'input[name="password_confirmation"]',
        'input[id="email"]',
        'input[id="password"]',
        'input[id="current_password"]',
        'input[id="password_confirmation"]'
    ].join(',');

    function aplicarATodo() {
        document.querySelectorAll(SELECTOR).forEach(function (input) {
            blockSpaces(input);
        });
    }

    // Aplicar al cargar
    document.addEventListener('DOMContentLoaded', aplicarATodo);

    // Observer para campos que aparezcan después (Livewire, modales, etc.)
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType !== 1) return;
                if (node.matches && node.matches(SELECTOR)) {
                    blockSpaces(node);
                }
                node.querySelectorAll && node.querySelectorAll(SELECTOR).forEach(function (input) {
                    blockSpaces(input);
                });
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    // Limpiar al submit en cualquier formulario
    document.addEventListener('submit', function (e) {
        var form = e.target;
        form.querySelectorAll(SELECTOR).forEach(function (input) {
            input.value = input.value.trim();
        });
    }, true);

})();
