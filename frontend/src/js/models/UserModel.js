class UserModel {

    validateUserData (data) {
        const nameRegex = /^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ]+(?:\s[A-Za-zÁÉÍÓÚáéíóúÜüÑñ]+)*$/;
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;

        if (!nameRegex.test(data.userName)) {
            alert('Nombre de usuario no válido');
            return false;
        } else if (!emailRegex.test(data.emailAddress)) {
            alert('Email no váliddssssso')
            return false;
        } else if (!passwordRegex.test(data.userPassword)) {
            alert('Contraseña no válida. Min 8 caracteres una letra mayúscula, minúscula y un número.')
            return false;
        } else {
            return true;
        }
    }
}

export default UserModel;