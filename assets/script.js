const passwordButtons = document.querySelectorAll('.password-toggle');

passwordButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        const passwordInput = button.parentElement.querySelector('input');
        const showPassword = passwordInput.type === 'password';

        passwordInput.type = showPassword ? 'text' : 'password';
        button.setAttribute('aria-pressed', showPassword ? 'true' : 'false');
        button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
        button.setAttribute('title', showPassword ? 'Hide password' : 'Show password');
    });
});
