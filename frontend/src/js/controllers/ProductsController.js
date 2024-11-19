import ProductsService from "../services/ProductsService.js";
import ProductView from "../views/ProductView.js";
import UserView from "../views/UserView.js";

const ProductsServiceInstance = new ProductsService;
const ProductsViewInstance = new ProductView;
const UserViewInstance = new UserView;

class ProductsController {

    loadProduct (productId) {
        ProductsServiceInstance.requestToLoadProduct(productId)
            .then(data => {
                if (data.status === 'success') {
                    ProductsViewInstance.product = data.product;
                    ProductsViewInstance.renderProduct(ProductsViewInstance.product);
                    localStorage.setItem('product', JSON.stringify(data.product));
                }
            });

    }


    loadProducts (categoryId, limit, offset) {

        ProductsServiceInstance.requestToLoadProducts(categoryId, limit, offset)
            .then(data => {
                if (data.status === 'success') {
                    ProductsViewInstance.products = data.products;
                    ProductsViewInstance.renderProducts(ProductsViewInstance.products);
                    return;
                }
            });
    }


    loadMoreProducts (categoryId, limit, offset) {
            
        ProductsServiceInstance.requestToLoadProducts(categoryId, limit, offset)
            .then(data => {
                if (data.status === 'success') {
                    ProductsViewInstance.products = [...ProductsViewInstance.products, ...data.products];
                    ProductsViewInstance.renderProducts(ProductsViewInstance.products);
                    return;
                }
            });
        
    }

    deleteProductShoppingBag (userId, productId) {

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