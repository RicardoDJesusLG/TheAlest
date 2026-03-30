document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("customModal");
    const closeBtn = document.getElementById("closeModal");

    // BOTÓN ORIGINAL (el que ya tenías)
    const openBtn = document.getElementById("openCustomModal");

    // BOTONES NUEVOS (cards)
    const openBtns = document.querySelectorAll(".openCustomModal");

    // Validación básica
    if (!modal || !closeBtn) return;

    // 👉 FUNCIONALIDAD ORIGINAL (NO se rompe)
    if (openBtn) {
        openBtn.addEventListener("click", function(e) {
            e.preventDefault();
            modal.classList.add("open");
        });
    }

    // 👉 FUNCIONALIDAD NUEVA (dinámica)
    openBtns.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();

            const title = btn.dataset.title;
            const text = btn.dataset.text;
            const img = btn.dataset.img;

            // Solo cambia contenido si existen los datos
            if (title) modal.querySelector("h2").textContent = title;
            if (text) modal.querySelector("p").textContent = text;
            if (img) modal.querySelector("img").src = img;

            modal.classList.add("open");
        });
    });

    // Cerrar con botón
    closeBtn.addEventListener("click", function() {
        modal.classList.remove("open");
    });

    // Cerrar al hacer click afuera
    modal.addEventListener("click", function(e) {
        if (e.target === modal) {
            modal.classList.remove("open");
        }
    });

    // Cerrar con ESC
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            modal.classList.remove("open");
        }
    });

});