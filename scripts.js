document.addEventListener('DOMContentLoaded', () => {
    const signUpButton = document.getElementById('signUpButton');
    const loginButton = document.getElementById('loginButton');

    // Event listener untuk tombol Sign Up
    signUpButton.addEventListener('click', () => {
        console.log('Sign Up button clicked'); // Debugging
        window.location.href = 'signup.php';
    });

    if (!loginButton) {
        console.error('Login button not found');
        return;
    }

    loginButton.addEventListener('click', () => {
        console.log('Login button clicked'); // Debugging
        window.location.href = 'login.php'; // Ubah dengan path absolut jika perlu
    });
});
