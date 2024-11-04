// -------------------------------  LOGIN  -------------------------------
import LoginController from "./frontend/src/js/controllers/LoginController.js";
import UserController from "./frontend/src/js/controllers/UserController.js";

const UserControllerInstance = new UserController();

// Cambio de formulario entre iniciar sesión y crear cuenta
const registerButton = document.getElementById('register');
const loginButton = document.getElementById('login');

registerButton.addEventListener('click', () => {
    LoginController.toggleFormsVisibility();
})
loginButton.addEventListener('click', () => {
    LoginController.toggleFormsVisibility();
})


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
})


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
})