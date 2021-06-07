const password = document.querySelector('input[name=password]');
const confirm = document.querySelector('input[name=confirm]');

function validatePassword(e) {
    if (password.value === confirm.value) {
        confirm.setCustomValidity('');
    } else {
        confirm.setCustomValidity('Passwords do not match');
    }
}

password.onchange = validatePassword;
confirm.onchange = validatePassword;

// https://stackoverflow.com/questions/21727317/how-to-check-confirm-password-field-in-form-without-reloading-page