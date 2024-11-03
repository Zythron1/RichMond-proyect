class LoginController {
    
    static toggleFormsVisibility() {
    const registerForm = document.getElementById('register-form');
    const loginForm = document.getElementById('login-form');
    const imgRegister = document.getElementById('img-register');
    const imgLogin = document.getElementById('img-login');

    const isRegisterHidden = registerForm.classList.contains('hidden');

    if (isRegisterHidden) {
        // Mostrar el formulario de registro
        registerForm.classList.remove('hidden');
        registerForm.classList.add('visible');
        loginForm.classList.remove('visible');
        loginForm.classList.add('hidden');

        imgRegister.classList.remove('hidden');
        imgRegister.classList.add('visible');
        imgLogin.classList.remove('visible');
        imgLogin.classList.add('hidden');
    } else {
        // Mostrar el formulario de inicio de sesión
        loginForm.classList.remove('hidden');
        loginForm.classList.add('visible');
        registerForm.classList.remove('visible');
        registerForm.classList.add('hidden');

        imgRegister.classList.remove('visible');
        imgRegister.classList.add('hidden');
        imgLogin.classList.remove('hidden');
        imgLogin.classList.add('visible');
    }
}

}


export default LoginController;