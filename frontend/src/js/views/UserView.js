class UserView {

    renderProductInShoppingBag(products) {
        const productsContainer = document.getElementById('shopping-bag-product');

        if (!productsContainer) {
            console.error("El contenedor de productos no se encuentra en el DOM.");
            return;
        }

        productsContainer.innerHTML = '';

        products.forEach(product => {

            const { product_name, price, quantity, image_url, product_id } = product;
            if (!(product_name && price && quantity && image_url && product_id)) {
                console.warn("El producto le faltan propiedades:", product);
                return;
            }

            const productDiv = document.createElement('div');
            productDiv.classList.add('shopping-bag-product__product-item');

            productDiv.innerHTML = `
                <div class="product-item__img-container">
                    <img src="/frontend/src/assets/fotos/productos/${image_url}" alt="${product_name}" class="img-container__img">
                </div>
                <div class="product-item__details-container">
                    <h4 class="details-container__name">${product_name}</h4>
                    <p class="details-container__price">Precio: $${price}</p>
                    <p class="details-container__quantity">Cantidad: ${quantity}</p>

                    <button type="button" class="product-item__icons-button transparent button-opacity-hover" data-product-id="${product_id}" data-orlando="654" aria-label="Borrar producto bolsa de compra">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 50 50">
                            <rect width="30" height="30" fill="none" />
                            <path fill="white" d="M20 18h2v16h-2zm4 0h2v16h-2zm4 0h2v16h-2zm-16-6h26v2H12zm18 0h-2v-1c0-.6-.4-1-1-1h-4c-.6 0-1 .4-1 1v1h-2v-1c0-1.7 1.3-3 3-3h4c1.7 0 3 1.3 3 3z" />
                            <path fill="white" d="M31 40H19c-1.6 0-3-1.3-3.2-2.9l-1.8-24l2-.2l1.8 24c0 .6.6 1.1 1.2 1.1h12c.6 0 1.1-.5 1.2-1.1l1.8-24l2 .2l-1.8 24C34 38.7 32.6 40 31 40" />
                        </svg>
                    </button>
                </div>
                    `;

            productsContainer.appendChild(productDiv);
        });
    }

}

export default UserView;