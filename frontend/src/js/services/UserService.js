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
            return {'status': 'error'};
        });
    }


    async requestToLogin (userData) {
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

            return data;
        })
        .catch(error => {
            console.error('Error en la petición. ' + error);
            alert(error);
            return {'status': 'error'};
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


    async requestToLogout () {
        return fetch('http://localhost:3000/user/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'  
            },
            body: JSON.stringify({'ShanksAce': 'ShanksAce'})
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
            console.error('Error en la petición. ' + error);
            return {'status': 'error'};
        })
    }
    
    /*
    requestToLogout () {
        fetch('http://localhost:3000/user/logout', {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ "ShanksAce1": 'ShanksAce1'})
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
    */

}

export default UserService;