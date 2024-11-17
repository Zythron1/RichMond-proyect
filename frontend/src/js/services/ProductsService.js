class ProductsService {

    async requestToLoadProducts(categoryId, limit, offset) {
        return fetch(`http://localhost:3000/product/${categoryId}/${limit}/${offset}`, {
            method: 'GET'
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


    async requestToDeleteProductShoppingBag(userId, productId) {
        return fetch(`http://localhost:3000/bagProduct/${userId}/${productId}/delete`, {
            method: 'DELETE'
        })
            .then(response => {
                if (response.status === 204) {
                    alert('Producto eliminado');
                    return { status: 'success' };
                    
                } else if (response.status >= 400) {
                    return response.json().then(data => {
                        alert(data.message);
                        throw new Error(data.messageToDeveloper || 'Error desconocido');
                    });

                } else {
                    throw new Error('Respuesta inesperada del servidor.');
                }
            })
            .catch(error => {
                console.warn('Error en la petición: ' + error.message);
                return { status: 'error', message: error.message };
            });
    }


}

export default ProductsService;