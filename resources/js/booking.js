// Cargar HTML
fetch("../../book-modal.html")
    .then(res => res.text())
    .then(html => {
        document.getElementById("modal-loader").innerHTML = html;

        // Cargar CSS dinámicamente
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = "./resources/css/book-modal.css";
        document.head.appendChild(link);

        // Cargar script del modal
        import("./book-modal.js").then(module => {
            module.initBookingModal();
        });
    });
