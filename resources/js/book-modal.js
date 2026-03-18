export function initBookingModal() {

    const floatingBtn = document.getElementById("floating-book");

    if (!floatingBtn) return;

    // Mostrar u ocultar botón al hacer scroll
    window.addEventListener("scroll", () => {
        if (window.scrollY > 150) {
            floatingBtn.classList.add("show");
        } else {
            floatingBtn.classList.remove("show");
        }
    });

}