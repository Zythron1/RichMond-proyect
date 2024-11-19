// -------------------------------  LOGIN  -------------------------------
import LoginController from "./frontend/src/js/controllers/LoginController.js";
import UserController from "./frontend/src/js/controllers/UserController.js";
import UserView from "./frontend/src/js/views/UserView.js";
import Homepage from "./frontend/src/js/controllers/HomepageController.js";
import ProductsController from "./frontend/src/js/controllers/ProductsController.js";
import ProductView from "./frontend/src/js/views/ProductView.js";

const HomepageInstance = new Homepage;
const UserControllerInstance = new UserController();
const UserViewInstance = new UserView();
const ProductsControllerInstance = new ProductsController();
const ProductsViewInstance = new ProductView;


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
    // Mostrar productos por categoría
    const categoriesButton = [
        document.getElementById('outfit'),
        document.getElementById('jeans'),
        document.getElementById('pants'),
        document.getElementById('t-shirts'),
        document.getElementById('shirts'),
        document.getElementById('sweatshirts'),
        document.getElementById('accessories')
    ];

    categoriesButton.forEach(category => {
        category.addEventListener('click', () => {
            localStorage.setItem('selectedCategory', category.getAttribute('category'));
        })
    })
}


// -------------------------------  PÁGINA PRINCIPAL  -------------------------------
// -------------------------------  PÁGINA DE PRODCUTOS  -------------------------------

if (window.location.href === 'http://localhost:3000/frontend/src/html/index.html' || window.location.href === 'http://localhost:3000/frontend/src/html/products.html' || window.location.pathname === '/frontend/src/html/product.html') {
    // Renderizar productos en la bolsa de compra
    const shoppingBagProducts = JSON.parse(localStorage.getItem('shoppingBagProducts'));
    if (shoppingBagProducts) {
        UserViewInstance.renderProductInShoppingBag(shoppingBagProducts);
    }


    // Eliminar productos de la bolsa de compra
    const productsContainer = document.getElementById('shopping-bag-product'); 

    productsContainer.addEventListener('click', (event) => {
        const button = event.target.closest('.product-item__icons-button');;

        if (button) {
            const productId = button.dataset.productId;
            
            ProductsControllerInstance.deleteProductShoppingBag(localStorage.getItem('userId'), parseInt(productId));
        }
    });



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
    const companyInformation1 = document.getElementById('company-information1');
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


// -------------------------------  PÁGINA DE PRODCUTOS  -------------------------------
if (window.location.href === 'http://localhost:3000/frontend/src/html/products.html') {
    let limit = 5;
    let offset = 0;
    
    if (localStorage.getItem('selectedCategory') == null) {
        localStorage.setItem('selectedCategory', 0);
    } 

    ProductsControllerInstance.loadProducts(localStorage.getItem('selectedCategory'), limit, offset);



    const buttonToLoadMoreProducts = document.getElementById('button-to-load-more-products');
    buttonToLoadMoreProducts.addEventListener('click', () => {
        offset += 5;
        ProductsControllerInstance.loadMoreProducts(localStorage.getItem('selectedCategory'), limit, offset);
    })



    const productCatalog = document.getElementById('product-catalog');

    
    productCatalog.addEventListener('click', event => {
        
        const product = event.target.closest('.product-catalog__product');
        
        
        if (product) {
            const productId = product.dataset.productId;
            const productImg = product.dataset.productImg;
            const productName = product.dataset.productName;
            const productPrice = product.dataset.productPrice;


            let productdata = {
                'productId': productId,
                'productImg': productImg,
                'ProductName': productName,
                'productPrice': productPrice 
            };

            sessionStorage.setItem('productData', JSON.stringify(productdata));
            window.location.href = `http://localhost:3000/frontend/src/html/product.html?product=${productId}`;
        }

    })

} 


if (window.location.pathname === `/frontend/src/html/product.html`) {
    // Renderizar el producto escogido.
    let productData = JSON.parse(sessionStorage.getItem('productData'));
    const urlParams = new URLSearchParams(window.location.search)
    const productId = urlParams.get('product');

    if (!productData) {
        ProductsControllerInstance.loadProduct(productId);
    }

    productData = JSON.parse(localStorage.getItem('product'));

    // toggle de botón de información del producto
    const productDiv = document.getElementById('product');

    productDiv.addEventListener('click', event => {
        const target = event.target;
        
        if (target.closest('.product__button-to-add')) { 
            
        } else if (target.closest('.product__details-toggle')) {
            const productDetails = document.getElementById('product-details');
            HomepageInstance.openCloseSection(productDetails);

        } else if (target.closest('.product__shipping-toggle')) {
            const shippingContent = document.getElementById('shipping-content');
            HomepageInstance.openCloseSection(shippingContent);

        } else if (target.closest('.product__returns-toggle')) {
            const returnsContent = document.getElementById('returns-content');
            HomepageInstance.openCloseSection(returnsContent);

        } else {
            return;
        }
    });
}