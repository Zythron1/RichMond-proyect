class UserService {

    async requestToCreateUser (userData) {
    return fetch('http://localhost:3000/user', {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'error') {
            throw new Error(data.message);
        }
        alert(data.message);
        return data;
    })
    .catch(error => {
        console.error('Error al hacer la petición: ' + error);
        alert('Hubo un error al procesar la solicitud. Inténtalo de nuevo.');
        return null;
    });
}


}

export default UserService;