import UserModel from "../models/UserModel.js";
import UserService from "../services/UserService.js";

const UserModelInstance = new UserModel();
const UserServiceInstance = new UserService;

class UserController {
    createUser (data) {
        const loginButton = document.getElementById('login-button');

        if (!UserModelInstance.validateUserData(data)) {
            return;
        } 

        UserServiceInstance.requestToCreateUser(data)
        .then(data => {
            if (data.status === 'success') {
                loginButton.click();
            }
        })
        
    }

    login (data) {
        if (!UserModelInstance.validateUserData(data)) {
            return;
        }

        UserServiceInstance.requestToLogin(data)
            .then(data => {
                if (data.status === 'success') {
                localStorage.setItem('userId', data.userId);
                alert(data.message);
                window.location.href = 'http://localhost:3000/frontend/src/html/index.html';
            }
        });
        return;
    }

    logout() {
        let userId = localStorage.getItem('userId');

        if (!userId) {
            alert('No tienes una sessión abierta');
            return;
        } 

        UserServiceInstance.requestToLogout()
        .then(data => {
            if (data.status === 'succes') {
                localStorage.removeItem('userId');
                alert(data.message);
            }
        });
    }

    passwordRecovery (data) {
        if (!UserModelInstance.validateUserData(data)) {
            return;
        }

        UserServiceInstance.requestToPasswordRecovery(data);
    }
}

export default UserController;