import ProductsService from "../services/ProductService.js";
import ProductView from "../views/ProductView.js";
import UserView from "../views/UserView.js";
import ProductModel from "../models/ProductModel.js";

const ProductsServiceInstance = new ProductsService;
const ProductsViewInstance = new ProductView;
const UserViewInstance = new UserView;
const ProductModelInstance = new ProductModel;

class ProductsController {

    loadProductPartial (productId, productData) {
        ProductsServiceInstance.requestToLoadProductPartial(productId)
            .then(data => {
                if (data.status === 'success') {
                    let product = {...productData, ...data.product}

                    ProductsViewInstance.product = product;
                    ProductsViewInstance.renderProduct(ProductsViewInstance.product);

                    localStorage.setItem('product', JSON.stringify(product));
                }
            });
    }


    loadProductFull (productId) {
        ProductsServiceInstance.requestToLoadProductFull(productId)
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


    addToShoppingBag(productId, userId, stock) {

        if (!ProductModelInstance.verifyStock(stock)) {
            alert('No tenemos stock en este momento.');
            return;
        }

        ProductsServiceInstance.requestToAddToShoppingBag(productId, userId)
            .then(data => {
                if (data.status === 'success') {
                    localStorage.setItem('shoppingBagProducts', JSON.stringify(data.products));

                    UserViewInstance.renderProductInShoppingBag(data.products);
                }
            })
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