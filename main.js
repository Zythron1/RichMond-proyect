// -------------------------------  LOGIN  -------------------------------
import LoginController from "./frontend/src/js/controllers/LoginController.js";
import UserController from "./frontend/src/js/controllers/UserController.js";

const UserControllerInstance = new UserController();

// Eventos cambio de formulario entre iniciar sesión y crear cuenta.
const registerButton = document.getElementById('register-button');
const loginButton = document.getElementById('login-button');

registerButton.addEventListener('click', () => {
    LoginController.toggleFormsLoginCreate();
});
loginButton.addEventListener('click', () => {
    LoginController.toggleFormsLoginCreate();
});


// Eventos cambio de formulario entre iniciar sesión y recuperar cuenta.
const passwordRecoveryButton = document.getElementById('password-recovery-button');
const loginButtonFromRecovery = document.getElementById('login-button-from-recovery');

passwordRecoveryButton.addEventListener('click', () => {
    LoginController.toggleFormsLoginRecovery();
});
loginButtonFromRecovery.addEventListener('click', () => {
    LoginController.toggleFormsLoginRecovery();
});


// Eventos cambio de formulario entre recuperar cuenta y crear cuenta.
const registerButtonFromRecovery = document.getElementById('register-button-from-recovery');

registerButtonFromRecovery.addEventListener('click', () => {
    LoginController.toggleFormsRecoveryCreate();
});


// Crear nuevo usuario
const registerForm = document.getElementById('register-form');
registerForm.addEventListener('submit', e => {
    e.preventDefault();

    const userName = document.getElementById('name-create').value;
    const email = document.getElementById('email-create').value;
    const password = document.getElementById('password-create').value;
    const loginButton = document.getElementById('login');

    let data = {
        'userName': userName,
        'emailAddress': email,
        'userPassword': password
    };
    UserControllerInstance.createUser(data);
});


// Iniciar sesión
const loginForm = document.getElementById('login-form');
loginForm.addEventListener('submit', e => {
    e.preventDefault();

    const emailLogin = document.getElementById('email-login').value;
    const passwordLogin = document.getElementById('password-login').value;

    let data = {
        'userName': 'Shanks Ace',
        'emailAddress': emailLogin,
        'userPassword': passwordLogin
    };

    UserControllerInstance.login(data);
});


// Recuperar cuenta
const passwordRecoveryForm = document.getElementById('password-recovery-form');
passwordRecoveryForm.addEventListener('submit', () => {
    const recoveryEmail = document.getElementById('recovery-email').value;

    let data = {
        'userName': 'Shanks Ace',
        'emailAddress': recoveryEmail,
        'userPassword': 'ShanksAce1'
    };

    UserControllerInstance.passwordRecovery(data);
});