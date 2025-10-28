document.addEventListener("DOMContentLoaded", () => {
    const passwordInput = document.getElementById("password");
    const toggleButton = document.getElementById("togglePassword");
    const eye = toggleButton?.querySelector(".icon-eye");
    const eyeOff = toggleButton?.querySelector(".icon-eye-off");

    if (!passwordInput || !toggleButton) return;

    toggleButton.addEventListener("click", () => {
        const show = passwordInput.type === "password";
        passwordInput.type = show ? "text" : "password";

        toggleButton.setAttribute("aria-pressed", show);
        toggleButton.setAttribute("aria-label", show ? "Nascondi password" : "Mostra password");
        toggleButton.title = show ? "Nascondi password" : "Mostra password";

        if (eye && eyeOff) {
            eye.style.display = show ? "none" : "inline";
            eyeOff.style.display = show ? "inline" : "none";
        }
    });
});
