let password = document.querySelector('#password');
let repeated = document.querySelector('#password-repeat');
let errText = document.querySelector('#repeated-error');

password.addEventListener('change', isEqual);
repeated.addEventListener('change', isEqual);

function isEqual() {
    if (password.value !== repeated.value
        && (password.value !== '' && repeated.value !== '')) {
        errText.textContent = "Passwords are not equal";
        password.classList.add('red-border');
        repeated.classList.add('red-border');
    } else {
        errText.textContent = "";
    }
}