$(document).on("click", ".open-puDelete", function () {
	$('#idDelete').val($(this).data('id'));
    $('#puDelete').modal('show');
});
