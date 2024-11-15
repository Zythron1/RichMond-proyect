class ProductsService {

    async requestToLoadProducts(categoryId, limit, offset) {
        return fetch(`http://localhost:3000/product/${categoryId}/${limit}/${offset}`, {
            method: 'GET',
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    alert(data.message);
                    throw new Error(data.messageToDeveloper); 
                }

                return data;
            })
            .catch(error => {
                console.error('Error en la petición: ' + error);
                return { 'status': 'error'}; 
            });
    }
    
}

export default ProductsService;