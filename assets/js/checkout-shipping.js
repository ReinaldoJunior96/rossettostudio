// assets/js/checkout-shipping.js
(function ($) {
  // Quando trocar SEDEX/PAC, salva escolha na sessão e atualiza os totais
  $(document).on('change', 'input[name^="shipping_method["]', function () {
    var $input = $(this);
    if (!$input.is(':checked')) return;

    // shipping_method[0] -> extrai o índice do pacote
    var name = $input.attr('name');           // ex: "shipping_method[0]"
    var idx  = (name.match(/\[(\d+)\]/) || [])[1] || 0;

    $.post(
      // usa o admin-ajax do Woo no front
      (window.wc_checkout_params && window.wc_checkout_params.ajax_url) || window.ajaxurl || '/wp-admin/admin-ajax.php',
      {
        action: 'rs_set_shipping_method',
        method: $input.val(),                 // ex: "superfrete:SEDEX"
        index:  parseInt(idx, 10)
      }
    ).always(function () {
      // pede o recálculo/refresh padrão (fragmentos + total)
      $(document.body).trigger('update_checkout');
    });
  });
})(jQuery);
