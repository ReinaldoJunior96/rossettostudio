// assets/js/checkout-shipping.js
(function ($) {
  $(function () {

    // 1) Intercepta o submit da calculadora no CHECKOUT
    $(document).on('submit', 'form.woocommerce-shipping-calculator', function (e) {
      e.preventDefault();

      var $form = $(this);
      var cep = ($form.find('input[name="calc_shipping_postcode"]').val() || '').replace(/\D/g, '');

      if (cep.length !== 8) {
        // feedback simples — troque por sua UI se quiser
        alert('Informe um CEP válido com 8 dígitos.');
        return;
      }

      // Copia CEP para os campos do checkout
      var $b = $('#billing_postcode');
      var $s = $('#shipping_postcode');

      if ($b.length) $b.val(cep).trigger('change');
      if ($s.length) $s.val(cep).trigger('change');

      // Garante país BR (ajuda transportadoras nacionais)
      $('#billing_country, #shipping_country').each(function () {
        if (!$(this).val()) $(this).val('BR').trigger('change');
      });

      // Dispara o recálculo do Woo (tarifas de frete + totais)
      $(document.body).trigger('update_checkout');

      // UX: rola pra seção de frete/resumo após o cálculo
      var $target = $('#order_review, .woocommerce-checkout-review-order').first();
      if ($target.length) {
        $('html,body').animate({ scrollTop: $target.offset().top - 60 }, 300);
      }
    });

    // 2) Trocar o método de frete => recalcula totais
    $(document).on('change', 'input[name^="shipping_method["]', function () {
      $(document.body).trigger('update_checkout');
    });

    // 3) Em alguns temas, CEP já vem preenchido; força um cálculo leve
    setTimeout(function () {
      // Se já existir CEP em billing/shipping, pede atualização
      var hasCEP = ($('#shipping_postcode').val() || $('#billing_postcode').val() || '').replace(/\D/g, '').length === 8;
      if (hasCEP) $(document.body).trigger('update_checkout');
    }, 120);
  });
})(jQuery);
