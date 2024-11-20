class ProductModel {
    verifyStock (stock) {
        if (stock && stock >= 0) {
            return true;
        } else {
            return false;
        }
    }
}

export default ProductModel;