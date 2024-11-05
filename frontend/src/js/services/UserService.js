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
            console.error('Error en la petición. ' + error);
            alert('Hubo un error al procesar la solicitud. Inténtalo de nuevo.');
            return null;
        });
    }

    
    async requestToLogin(userData) {
        return fetch('http://localhost:3000/user/login', {
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
            localStorage.setItem('authToken', data.token);
            alert(data.message);
            return data;
        })
        .catch(error => {
            console.error('Error en la petición. ' + error);
            alert(error);
            return null;
        })
    }
    

    
    requestToPasswordRecovery (userData) {
        fetch('http://localhost:3000/user/resetPassword', {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(userData)
        })
        .then(response => response.text())
        .then(text => {
            console.log("Respuesta en texto: " + text);
            const data = JSON.parse(text);

            if (data.error === 'error') {
                throw new Error(data.message);
            }

            alert(data.message);
            
        })
        .catch(error => {
            console.error('Error al hacer la petición ' + error);
            alert('Hubo un error al procesar la solicitud. Inténtalo de nuevo.');
            
        })
    }
    
    
    /*
    requestToPasswordRecovery(userData) {
        fetch('http://localhost:3000/user/resetPassword', {
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
        })
        .catch(error => {
            console.error('Error en la petición. ' + error);
            alert(error);
        })
    }
    */
}

export default UserService;