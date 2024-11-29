class ProductView {

    constructor() {
        this.products = [];
        this.product = [];
    }


    renderProduct (product) {
        const sliderContainerDiv = document.getElementById('slider-container');
        const productDiv = document.getElementById('product');
        sliderContainerDiv.innerHTML = '';
        productDiv.innerHTML = '';

        const { product_name, product_description, price, stock, image_url, product_id } = product;

        sliderContainerDiv.innerHTML = `
        <img src="/frontend/src/assets/fotos/productos/camiseta.jpg" alt="${product_name}">
            <img src="/frontend/src/assets/fotos/productos/${image_url}" alt="${product_name}">
            <img src="/frontend/src/assets/fotos/productos/camiseta.jpg" alt="${product_name}">
        `;

        productDiv.innerHTML = `
            <h3 class="product__name">${product_name}</h3>
            <p class="product__price">${price}</p>
            <p class="product__size">Talla única</p>

            <button type="button" class="product__button-to-add transparent" data-product-stock="${stock}" data-product-id="${product_id}" >Agregar a la bolsa</button>

            <!-- Detalles del producto -->
            <div class="product__details">
                <button type="button" class="product__details-toggle" id="product-details-toggle" aria-expanded="true"
                    aria-controls="product-details">
                    Detalles del producto
                </button>
                <div class="product__details-content" id="product-details">
                    <p class="product__details-content-title">${product_description}.</p>
                </div>
            </div>

            <!-- Envíos -->
            <div class="product__shipping">
                <button type="button" class="product__shipping-toggle" id="shipping-toggle" aria-expanded="false" aria-controls="shipping-content">Envíos
                </button>
                <div class="product__shipping-content hidden" id="shipping-content">
                    <p class="product__shipping-content-title">
                        <b>Envíos a todos los destinos por compras iguales o mayores a $199.000.</b> <br><br>
                        El tiempo estimado de entrega varía según la ubicación, pero generalmente es de 3 a 5 días hábiles. <br><br>
                        Los envíos se realizan <b>de lunes a viernes</b>, y se notificará al cliente cuando su pedido esté en camino. <br><br>
                        Si tu compra es inferior a $199.000, podrás elegir entre diferentes opciones de envío con costo adicional. <br><br><br>
                        Si es ropa de <b>SALE</b> el valor de envío depende tu ubicación a partir de $10.000
                    </p>
                </div>
            </div>

            <!-- Devoluciones y garantías -->
            <div class="product__returns"> 
                <button type="button" class="product__returns-toggle" id="returns-toggle" aria-expanded="false"
                    aria-controls="returns-content">
                    Devoluciones y garantías
                </button>
                <div class="product__returns-content hidden" id="returns-content">
                    <p class="product__returns-content-title">
                    <b>Devoluciones de ropa:</b> Si no quedas completamente satisfecho con tu compra, ofrecemos un plazo de 30 días para realizar devoluciones. El producto debe estar en su estado original, sin haber sido utilizado y con el embalaje intacto.  (te compartiremos un código con el saldo a favor para tu próxima compra en el sitio web).
                    Las devoluciones son gratuitas si se realizan dentro de este plazo y el producto cumple con los requisitos establecidos.<br><br>

                    <b>Garantías:</b> En cuanto a la garantía, todos nuestros productos cuentan con una garantía mínima de 6 meses contra defectos de fabricación.<br><br>

                    <b>Devolución de dinero:</b> Tienes un plazo de 3 días hábiles a partir de la recepción de tu pedido para solicitar la devolución del dinero.<br><br><br>

                    Por razones de higiene y seguridad, no se aceptan devoluciones ni reembolsos para los siguientes productos: bodies, bañadores (parte inferior), boxers, tapabocas, shopping bags, medias, toallas, pañoletas, panties, coletas. Sin embargo, estos productos cuentan con garantía en caso de defectos de fabricación.
                    </p>
                </div>
            </div>
        `;
    }


    renderProducts(products) {
        const productsContainer = document.getElementById('product-catalog');
        productsContainer.innerHTML = '';

        products.forEach(product => {
            const { product_name, price, image_url, product_id } = product;
            if (!(product_name && price && image_url)) {
                console.warn("El producto tiene propiedades faltantes:", product);
                return;
            }

            const productDiv = document.createElement('div');
            productDiv.classList.add('product-catalog__product');
            productDiv.setAttribute('data-product-id', product_id);
            productDiv.setAttribute('data-product-img', image_url);
            productDiv.setAttribute('data-product-name', product_name);
            productDiv.setAttribute('data-product-price', price);

            productDiv.innerHTML = `
                <img src="/frontend/src/assets/fotos/productos/${product.image_url}" alt="${product.product_name}"  class="product__img"/>
                <h3  class="product__name" >${product.product_name}</h3>
                <p  class="product__price">Precio: $${product.price}</p>
            `;

            productsContainer.appendChild(productDiv);
        });
    }
}

export default ProductView;