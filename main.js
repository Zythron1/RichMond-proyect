// -------------------------------  LOGIN  -------------------------------
import LoginController from "./frontend/src/js/controllers/LoginController.js";
import UserController from "./frontend/src/js/controllers/UserController.js";
import UserView from "./frontend/src/js/views/UserView.js";
import Homepage from "./frontend/src/js/controllers/HomepageController.js";

const HomepageInstance = new Homepage;
const UserControllerInstance = new UserController();
const UserViewInstance = new UserView();


if (window.location.href === 'http://localhost:3000/frontend/src/html/logIn.html') {
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
}



// -------------------------------  PÁGINA PRINCIPAL  -------------------------------


if (window.location.href === 'http://localhost:3000/frontend/src/html/index.html') {
    const shoppingBagProducts = JSON.parse(localStorage.getItem('shoppingBagProducts'));
    if (shoppingBagProducts) {
        UserViewInstance.showProductInShoppingBag(shoppingBagProducts);
    }



    // Abrir menú y Cerrar menú
    const menuButton = document.getElementById('open-menu-button');
    const buttonToCloseMenu = document.getElementById('close-menu-button');
    const menu = document.getElementById('menu');

    menuButton.addEventListener('click', () => {
        HomepageInstance.openCloseSection(menu);
    });

    buttonToCloseMenu.addEventListener('click', () => {
        HomepageInstance.openCloseSection(menu);
    });



    // Desplegar y cerra lista de productos-categorias (Mujer - hombre)
    const productsListButtonWoman = document.getElementById('products-list-button-woman');
    const productsListButtonMan = document.getElementById('products-list-button-man');
    
    productsListButtonWoman.addEventListener('click', () => {
        const womanProductsList = document.getElementById('woman-products-list');
        HomepageInstance.openCloseSection(womanProductsList);
    });

    productsListButtonMan.addEventListener('click', () => {
        const manProductsList = document.getElementById('man-products-list');
        HomepageInstance.openCloseSection(manProductsList);
    })



    // Abrir y cerrar sección del perfil
    const profileButton = document.getElementById('profile-button');
    const profile = document.getElementById('profile');
    const accesorio = document.getElementById('accesorio');

    profileButton.addEventListener('click', () => {
        HomepageInstance.openCloseSection(accesorio);
        HomepageInstance.openCloseSection(profile);
    });



    // Abrir y cerrar bolsa de compras
    const openShoppingBagButton = document.getElementById('open-shopping-bag-button');
    const closeShoppingBagButton = document.getElementById('close-shopping-bag-button');
    const shoppingBag = document.getElementById('shopping-bag');

    openShoppingBagButton.addEventListener('click', () => {
        HomepageInstance.openCloseSection(shoppingBag);
    });

    closeShoppingBagButton.addEventListener('click', () => {
        HomepageInstance.openCloseSection(shoppingBag);
    });


    // Abrir y cerrar información de la compañía
    const companyInformationButton1 = document.getElementById('company-information__button1');
    const companyInformationButton2 = document.getElementById('company-information__button2');
    const companyInformation1  = document.getElementById('company-information1');
    const companyInformation2 = document.getElementById('company-information2');
    
    companyInformationButton1.addEventListener('click', () => {
        HomepageInstance.openCloseSection(companyInformation1);
    });
    
    companyInformationButton2.addEventListener('click', () => {
        HomepageInstance.openCloseSection(companyInformation2);
    });



    // Cerrar sesión
    const logoutButtons = [
        document.getElementById('profile-logout-button'),
        document.getElementById('menu-logout-button')
    ];

    logoutButtons.forEach(button => {
        button.addEventListener('click', () => {
            UserControllerInstance.logout();
        });
    });

    

}