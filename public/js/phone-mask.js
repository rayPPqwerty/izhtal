(function () {
    'use strict';
    function getDigits(value) {
        var d = String(value || '').replace(/\D/g, '');
        if (d.charAt(0) === '8') d = '7' + d.slice(1);
        if (d && d.charAt(0) !== '7') d = '7' + d;
        return d.slice(0, 11);
    }
    function formatPhone(digits) {
        if (!digits) return '';
        var r = '+7';
        if (digits.length > 1)  r += ' (' + digits.slice(1, 4);
        if (digits.length > 4)  r += ') ' + digits.slice(4, 7);
        if (digits.length > 7)  r += '-' + digits.slice(7, 9);
        if (digits.length > 9)  r += '-' + digits.slice(9, 11);
        return r;
    }
    function applyMask(input) {
        var digits = getDigits(input.value);
        var next = formatPhone(digits);
        if (next !== input.value) input.value = next;
    }
    function attach(input) {
        if (!input || input.dataset.phoneMask === '1') return;
        input.dataset.phoneMask = '1';
        input.setAttribute('inputmode', 'tel');
        input.setAttribute('autocomplete', 'tel');
        input.setAttribute('maxlength', '18');
        input.addEventListener('focus', function () { if (!getDigits(input.value)) input.value = '+7 '; });
        input.addEventListener('blur', function () { if (getDigits(input.value).length <= 1) input.value = ''; });
        input.addEventListener('input', function () { applyMask(input); });
        input.addEventListener('paste', function () { setTimeout(function () { applyMask(input); }, 0); });
        input.addEventListener('keydown', function (e) {
            if (e.key !== 'Backspace') return;
            var atEnd = input.selectionStart === input.value.length;
            if (!atEnd) return;
            var last = input.value.slice(-1);
            if (last && !/\d/.test(last)) {
                e.preventDefault();
                var digits = getDigits(input.value).slice(0, -1);
                input.value = formatPhone(digits);
            }
        });
        if (input.value) applyMask(input);
    }
    function init() { document.querySelectorAll('input[type="tel"]').forEach(attach); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
    window.PhoneMask = { attach: attach, attachAll: init };
})();