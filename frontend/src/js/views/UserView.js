class UserView {
    showProductInShoppingBag(products) {
        const productsContainer = document.getElementById('products-container');

        if (!productsContainer) {
            console.error("El contenedor de productos no se encuentra en el DOM.");
            return;
        }

        productsContainer.innerHTML = '';


        products.forEach(product => {

            const { product_name, price, quantity, image_url } = product;
            if (!product_name || !price || !quantity || !image_url) {
                console.warn("El producto tiene propiedades faltantes:", product);
                return;
            }

            const productDiv = document.createElement('div');
            productDiv.classList.add('product-item');

            productDiv.innerHTML = `
            <div class="product-image">
                <img src="${image_url}" alt="${product_name}">
            </div>
            <div class="product-details">
                <h4>${product_name}</h4>
                <p>Precio: $${price}</p>
                <p>Cantidad: ${quantity}</p>
            </div>
        `;

            productsContainer.appendChild(productDiv);
        });
    }



}

export default UserView;