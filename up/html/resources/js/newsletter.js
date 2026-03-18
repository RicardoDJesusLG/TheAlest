$( document ).ready(function() {
  $(document).on("keypress", "#footerNewsletterEmail", function(e) {
       if (e.which == 13) {
        $('#footerNewsletterEmailMsg').html('...');

        $.post( "/index.php", { footerNewsletterEmail: $( "#footerNewsletterEmail" ).val() })
          .done(function( data ) {
            if (data == 'ok') {
              $('#footerNewsletterEmail').val('');
              $('#footerNewsletterEmailMsg').html('Thank You For Subscribing');
            } else {
              $('#footerNewsletterEmailMsg').html(data);
            }
          });
       }
  });

  $(document).on("click", "#footerNewsletterSubmit", function(e) {
      $('#footerNewsletterEmailMsg').html('...');

      $.post( "/index.php", { footerNewsletterEmail: $( "#footerNewsletterEmail" ).val() })
        .done(function( data ) {
          if (data == 'ok') {
            $('#footerNewsletterEmail').val('');
            $('#footerNewsletterEmailMsg').html('Thank You For Subscribing');
          } else {
            $('#footerNewsletterEmailMsg').html(data);
          }
        });
  });
});