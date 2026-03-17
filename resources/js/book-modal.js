export function initBookingModal() {

    const floatingBtn = document.getElementById("floating-book");
    const footer = document.querySelector("footer");

    if (!floatingBtn || !footer) return;

    window.addEventListener("scroll", () => {

        const footerTop = footer.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;

        if (footerTop < windowHeight) {
            floatingBtn.classList.remove("show");
            return;
        }
        if (window.scrollY > 150) {
            floatingBtn.classList.add("show");
        } else {
            floatingBtn.classList.remove("show");
        }
    });
}