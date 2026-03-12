document.addEventListener("DOMContentLoaded", () => {
  // Add micro-animations to forms
  const inputs = document.querySelectorAll(".form-group input");
  inputs.forEach((input) => {
    input.addEventListener("focus", () => {
      input.parentElement.classList.add("focused");
    });
    input.addEventListener("blur", () => {
      input.parentElement.classList.remove("focused");
    });
  });

  // Handle alert dismissal
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
});
