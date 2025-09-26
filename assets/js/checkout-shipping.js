// assets/js/checkout-shipping.js
(function ($) {
  $(function () {

    // --- A) Botão inline ao lado do CEP (billing) ---
    var $billingPostcode = $('#billing_postcode');
    if ($billingPostcode.length && !$('#rs-calc-inline').length) {
      var $btn = $('<button type="button" id="rs-calc-inline" class="button" style="margin-left:8px">Calcular frete</button>');
      $billingPostcode.after($btn);

      $btn.on('click', function () {
        var cep = ($billingPostcode.val() || '').replace(/\D/g, '');
        if (cep.length !== 8) {
          alert('Informe um CEP válido com 8 dígitos.');
          $billingPostcode.focus();
          return;
        }

        // espelha para shipping e garante país BR
        $('#shipping_postcode').val(cep).trigger('change');
        $('#billing_country, #shipping_country').each(function () {
          if (!$(this).val()) $(this).val('BR').trigger('change');
        });

        // força recálculo
        $(document.body).trigger('update_checkout');

        // rola até a área do pedido
        var $target = $('#order_review, .woocommerce-checkout-review-order').first();
        if ($target.length) $('html,body').animate({ scrollTop: $target.offset().top - 60 }, 300);
      });
    }

    // --- B) Intercepta a calculadora (form) — botão dentro da caixinha ---
    $(document).on('submit', 'form.woocommerce-shipping-calculator', function (e) {
      e.preventDefault();
      var $form = $(this);
      var cep = ($form.find('input[name="calc_shipping_postcode"]').val() || '').replace(/\D/g, '');
      if (cep.length !== 8) {
        alert('Informe um CEP válido com 8 dígitos.');
        return;
      }
      $('#billing_postcode').val(cep).trigger('change');
      $('#shipping_postcode').val(cep).trigger('change');
      $('#billing_country, #shipping_country').each(function () {
        if (!$(this).val()) $(this).val('BR').trigger('change');
      });
      $(document.body).trigger('update_checkout');

      var $target = $('#order_review, .woocommerce-checkout-review-order').first();
      if ($target.length) $('html,body').animate({ scrollTop: $target.offset().top - 60 }, 300);
    });

    // --- C) Trocar método de frete => recalc ---
    $(document).on('change', 'input[name^="shipping_method["]', function () {
      $(document.body).trigger('update_checkout');
    });

    // --- D) Se já tem CEP, faz um cálculo leve ao carregar ---
    setTimeout(function () {
      var hasCEP = ($('#shipping_postcode').val() || $('#billing_postcode').val() || '').replace(/\D/g, '').length === 8;
      if (hasCEP) $(document.body).trigger('update_checkout');
    }, 150);

  });
})(jQuery);
