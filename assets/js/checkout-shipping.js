// === Troca de frete -> chama o endpoint update_shipping_method e atualiza totais ===
(function ($) {
  if (typeof wc_checkout_params === 'undefined') return;

  function updateShippingAndTotals() {
    var $form = $('form.checkout');

    var data = {
      security: wc_checkout_params.update_shipping_method_nonce,
      post_data: $form.serialize() // manda o form junto (endereço, etc.)
    };

    // inclui o método escolhido (ou todos, se houver múltiplos pacotes)
    $('input[name^="shipping_method["]').each(function () {
      if (this.checked) data[this.name] = this.value; // ex: shipping_method[0] = "superfrete:SEDEX"
    });

    $.ajax({
      type: 'POST',
      url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_shipping_method'),
      data: data,
      success: function (resp) {
        // substitui fragmentos (order review, etc.)
        if (resp && resp.fragments) {
          $.each(resp.fragments, function (selector, html) {
            $(selector).replaceWith(html);
          });
        }
        // pede um refresh padrão do checkout (pagamentos, total final)
        $(document.body).trigger('update_checkout');
      }
    });
  }

  // quando trocar SEDEX/PAC
  $(document).on('change', 'input[name^="shipping_method["]', updateShippingAndTotals);
})(jQuery);
