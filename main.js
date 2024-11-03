// -------------------------------  LOGIN  -------------------------------
import LoginController from "./frontend/src/js/controllers/LoginController.js";
import UserController from "./frontend/src/js/controllers/UserController.js";

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
    const UserControllerInstance = new UserController();
    UserControllerInstance.createUser(data);
})

// Iniciar sesión
