(function () {
    'use strict';

    var toggle = document.querySelector('.fpsml-measurement-reset-toggle');
    var confirmation = document.getElementById('fpsml-measurement-reset-confirm');
    var cancel = document.querySelector('.fpsml-measurement-reset-cancel');

    if (!toggle || !confirmation) {
        return;
    }

    toggle.addEventListener('click', function () {
        confirmation.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        var resetButton = confirmation.querySelector('button[type="submit"]');
        if (resetButton) {
            resetButton.focus();
        }
    });

    if (cancel) {
        cancel.addEventListener('click', function () {
            confirmation.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            toggle.focus();
        });
    }
}());
