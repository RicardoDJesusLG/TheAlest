$(document).ready(function(){

    const modal = $('#modalReserva');
    const iframe = $('#reservaIframe');

    // Validación
    if (!modal.length || !iframe.length) return;

    // Cuando abre el modal
    modal.on('show.bs.modal', function () {
        const url = 'https://the-alest.mesa.express/en/reservas/?';
        iframe.attr('src', url);
    });

    // Cuando cierra
    modal.on('hidden.bs.modal', function () {
        iframe.attr('src', 'about:blank');
    });

});