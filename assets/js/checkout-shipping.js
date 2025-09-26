// === Força a atualização do resumo ao trocar o método de frete ===
(function ($) {
  function hardRefreshOrderReview() {
    var $form = $('form.checkout');

    // Monta o payload esperado pelo Woo
    var data = {
      security: wc_checkout_params.update_order_review_nonce,
      country:   $('#billing_country').val(),
      state:     $('#billing_state').val(),
      postcode:  $('#billing_postcode').val(),
      s_country: $('#shipping_country').val(),
      s_state:   $('#shipping_state').val(),
      s_postcode:$('#shipping_postcode').val(),
      has_full_address: true,
      post_data: $form.serialize()
    };

    $.ajax({
      type: 'POST',
      url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_order_review'),
      data: data,
      success: function (resp) {
        if (resp && resp.fragments) {
          // Substitui os fragmentos retornados (inclui totais e métodos)
          $.each(resp.fragments, function (selector, html) {
            $(selector).replaceWith(html);
          });
          $(document.body).trigger('updated_checkout');
        } else {
          // fallback
          $(document.body).trigger('update_checkout');
        }
      }
    });
  }

  // Troca de método de frete => força refresh
  $(document).on('change', 'input[name^="shipping_method["]', hardRefreshOrderReview);
})(jQuery);
