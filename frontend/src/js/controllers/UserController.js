import UserModel from "../models/UserModel.js";
import UserService from "../services/UserService.js";

class UserController {
    createUser (data) {
        const loginButton = document.getElementById('login');

        const userModelInstance = new UserModel();
        if (!userModelInstance.validateUserData(data)) {
            return;
        } 

        const UserServiceInstance = new UserService;
        UserServiceInstance.requestToCreateUser(data)
        .then(data => {
            if (data.status === 'success') {
                loginButton.click();
            }
        })
        
    }
}

export default UserController;