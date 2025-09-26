// assets/js/checkout-shipping.js
(function ($) {
  function recalcCheckout() {
    $(document.body).trigger('update_checkout');
  }

  function handleCalcSubmit(e) {
    if (e) e.preventDefault();

    var $form = $(this).closest('form.woocommerce-shipping-calculator');
    if (!$form.length) return;

    var cep = ($form.find('input[name="calc_shipping_postcode"]').val() || '').replace(/\D/g, '');
    if (cep.length !== 8) {
      alert('Informe um CEP válido com 8 dígitos.');
      return;
    }

    // Copia CEP para billing/shipping do checkout
    var $b = $('#billing_postcode');
    var $s = $('#shipping_postcode');
    if ($b.length) $b.val(cep).trigger('change');
    if ($s.length) $s.val(cep).trigger('change');

    // País BR se vazio
    $('#billing_country, #shipping_country').each(function () {
      if (!$(this).val()) $(this).val('BR').trigger('change');
    });

    // Desabilita botão durante o cálculo
    var $btn = $form.find('button, input[type="submit"], input[type="button"]').first();
    var oldTxt = $btn.is('button') ? $btn.text() : $btn.val();
    $btn.prop('disabled', true);
    if ($btn.is('button')) $btn.text('Calculando...');
    else $btn.val('Calculando...');

    // Dispara recálculo
    recalcCheckout();

    // Reabilita quando o Woo terminar
    $(document.body).one('updated_checkout', function () {
      $btn.prop('disabled', false);
      if ($btn.is('button')) $btn.text(oldTxt);
      else $btn.val(oldTxt);

      // UX: rola para a área de frete / resumo
      var $target = $('#order_review, .woocommerce-checkout-review-order').first();
      if ($target.length) $('html,body').animate({ scrollTop: $target.offset().top - 60 }, 300);
    });
  }

  $(function () {
    // 1) Intercepta SUBMIT do form
    $(document).on('submit', 'form.woocommerce-shipping-calculator', handleCalcSubmit);

    // 1.1) Intercepta CLIQUE no botão da calculadora, caso não seja submit
    $(document).on('click', 'form.woocommerce-shipping-calculator button, form.woocommerce-shipping-calculator input[type="button"], form.woocommerce-shipping-calculator input[type="submit"]', handleCalcSubmit);

    // 2) Trocar método de frete => recalc
    $(document).on('change', 'input[name^="shipping_method["]', function () {
      recalcCheckout();
    });

    // 3) Se já houver CEP, força um cálculo inicial
    setTimeout(function () {
      var hasCEP = ($('#shipping_postcode').val() || $('#billing_postcode').val() || '').replace(/\D/g, '').length === 8;
      if (hasCEP) recalcCheckout();
    }, 120);

    // DEBUG opcional (descomente pra ver no console)
    // console.log('[checkout-shipping.js] carregado');
  });
})(jQuery);
