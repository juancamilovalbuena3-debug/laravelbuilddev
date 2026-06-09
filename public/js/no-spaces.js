document.addEventListener('DOMContentLoaded', function () {
    function blockSpaces(input) {
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

        input.addEventListener('input', function () {
            var pos = this.selectionStart;
            var cleaned = this.value.replace(/\s/g, '');
            if (cleaned !== this.value) {
                this.value = cleaned;
                this.selectionStart = this.selectionEnd = Math.max(0, pos - 1);
            }
        });

        input.addEventListener('compositionend', function () {
            var pos = this.selectionStart;
            var cleaned = this.value.replace(/\s/g, '');
            if (cleaned !== this.value) {
                this.value = cleaned;
                this.selectionStart = this.selectionEnd = Math.max(0, pos - 1);
            }
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
