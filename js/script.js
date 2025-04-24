document.addEventListener('DOMContentLoaded', function () {
    document.querySelector(".idis_login_li")?.classList.add("active");
    const passwordInput = document.getElementById('password-input');
    const passwordIcon = document.getElementById('pass');

    passwordIcon.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            this.classList.add('view');
        } else {
            passwordInput.type = 'password';
            this.classList.remove('view')
        }
    });
});