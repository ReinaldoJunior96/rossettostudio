// assets/js/checkout-shipping.js
(function ($) {
  $(function () {
    // Botão "Calcular frete" que APENAS recarrega a página
    var $billingPostcode = $('#billing_postcode');
    if (!$billingPostcode.length || $('#rs-calc-reload').length) return;

    var $btn = $('<button type="button" id="rs-calc-reload" class="button" style="margin-left:8px">Calcular frete</button>');
    $billingPostcode.after($btn);

    $btn.on('click', function () {
      var cep = ($billingPostcode.val() || '').replace(/\D/g, '');
      if (cep.length !== 8) {
        alert('Informe um CEP válido com 8 dígitos.');
        $billingPostcode.focus();
        return;
      }

      // (opcional) espelha no shipping e define país BR
      $('#shipping_postcode').val(cep).trigger('change');
      $('#billing_country, #shipping_country').each(function () {
        if (!$(this).val()) $(this).val('BR').trigger('change');
      });

      // dá um tempinho para o Woo salvar o CEP na sessão e recarrega
      setTimeout(function(){ location.reload(); }, 300);
    });
  });
})(jQuery);
