import ProductsService from "http://localhost:3000/frontend/src/js/services/ProductsService.js";
import ProductView from "http://localhost:3000/frontend/src/js/views/ProductView.js";

const ProductsServiceInstance = new ProductsService;
const ProductsViewInstance = new ProductView;

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
    

}

export default ProductsController;