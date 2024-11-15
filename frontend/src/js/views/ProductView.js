class ProductView {

    renderProducts(products) {
        const productsContainer = document.getElementById('product-catalog');
        productsContainer.innerHTML = '';

        products.forEach(product => {
            const {product_name, price, image_url} = product;
            if (!(product_name && price && image_url)) {
                console.warn("El producto tiene propiedades faltantes:", product);
                return;
            }

            const productDiv = document.createElement('div');
            productDiv.classList.add('product-catalog__product');

            productDiv.innerHTML = `
                <img src="${product.image_url}" alt="${product.product_name}" class="product__img"/>
                <h3 class="product__name">${product.product_name}</h3>
                <p class="product__price">Precio: $${product.price}</p>
            `;

            productsContainer.appendChild(productDiv);
        });
    }

}

export default ProductView;