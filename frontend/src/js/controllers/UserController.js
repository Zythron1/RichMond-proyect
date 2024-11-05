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
                window.location.href = 'index.html';
            }
        })
    }

    passwordRecovery (data) {
        if (!UserModelInstance.validateUserData(data)) {
            return;
        }

        UserServiceInstance.requestToPasswordRecovery(data);
    }
}

export default UserController;