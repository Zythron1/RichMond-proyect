class LoginController {
    
    static toggleFormsLoginCreate () {
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
    
    static toggleFormsLoginRecovery () {
        const loginForm = document.getElementById('login-form');
        const passwordRecoveryForm = document.getElementById('password-recovery-form');

        const isPasswordRecoveryHidden = passwordRecoveryForm.classList.contains('hidden');

        if (isPasswordRecoveryHidden) {
            loginForm.classList.remove('visible');
            loginForm.classList.add('hidden');
            passwordRecoveryForm.classList.remove('hidden');
            passwordRecoveryForm.classList.add('visible');
        } else {
            loginForm.classList.remove('hidden');
            loginForm.classList.add('visible');
            passwordRecoveryForm.classList.remove('visible');
            passwordRecoveryForm.classList.add('hidden');
        }
    }

    static toggleFormsRecoveryCreate () {
        const registerForm = document.getElementById('register-form');
        const passwordRecoveryForm = document.getElementById('password-recovery-form');

        const isPasswordRecoveryHidden = passwordRecoveryForm.classList.contains('hidden');

        if (isPasswordRecoveryHidden) {
            registerForm.classList.remove('visible');
            registerForm.classList.add('hidden');
            passwordRecoveryForm.classList.remove('hidden');
            passwordRecoveryForm.classList.add('visible');
        } else {
            registerForm.classList.remove('hidden');
            registerForm.classList.add('visible');
            passwordRecoveryForm.classList.remove('visible');
            passwordRecoveryForm.classList.add('hidden');
        }
    }
}


export default LoginController;