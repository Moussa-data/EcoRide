// Validation du formulaire d'inscription EcoRide

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".form-register");
  if (!form) return;

  const passwordInput = document.getElementById("password");
  const confirmInput = document.getElementById("confirm_password");

  const errorBox = document.createElement("div");
  errorBox.style.color = "red";
  errorBox.style.marginTop = "0.5rem";
  errorBox.style.fontSize = "0.9rem";
  form.prepend(errorBox);

  form.addEventListener("submit", (e) => {
    errorBox.textContent = "";

    const password = passwordInput.value.trim();
    const confirm = confirmInput.value.trim();
    const errors = [];

    // 1. Mots de passe identiques ?
    if (password !== confirm) {
      errors.push("Les mots de passe ne correspondent pas.");
    }

    // 2. Longueur minimale
    if (password.length < 8) {
      errors.push("Le mot de passe doit contenir au moins 8 caractères.");
    }

    // 3. Contient au moins un chiffre
    if (!/[0-9]/.test(password)) {
      errors.push("Le mot de passe doit contenir au moins un chiffre.");
    }

    // 4. Contient au moins une majuscule
    if (!/[A-Z]/.test(password)) {
      errors.push("Le mot de passe doit contenir au moins une majuscule.");
    }

    // 5. Contient au moins une minuscule
    if (!/[a-z]/.test(password)) {
      errors.push("Le mot de passe doit contenir au moins une minuscule.");
    }

    if (errors.length > 0) {
      e.preventDefault();
      errorBox.innerHTML = errors.join("<br>");
    }
  });
});
