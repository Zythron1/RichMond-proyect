import ProductsService from "../services/ProductsService.js";
import ProductView from "../views/ProductView.js";
import UserView from "../views/UserView.js";

const ProductsServiceInstance = new ProductsService;
const ProductsViewInstance = new ProductView;
const UserViewInstance = new UserView;

class ProductsController {

    loadProducts (categoryId, limit, offset) {

        ProductsServiceInstance.requestToLoadProducts(categoryId, limit, offset)
            .then(data => {
                if (data.status === 'success') {
                    ProductsViewInstance.renderProducts(data.products);
                    return;
                }
            });
    }


    deleteProductShoppingBag(userId, productId) {

        ProductsServiceInstance.requestToDeleteProductShoppingBag(userId, productId)
            .then(data => {
                if (data.status === 'success') {
                    const shoppingBagProducts = JSON.parse(localStorage.getItem('shoppingBagProducts'));

                    const updateProducts = shoppingBagProducts.filter(product => product.product_id !== productId);

                    localStorage.setItem('shoppingBagProducts', JSON.stringify(updateProducts));

                    UserViewInstance.renderProductInShoppingBag(updateProducts);
                }
            });
    }

}

export default ProductsController;