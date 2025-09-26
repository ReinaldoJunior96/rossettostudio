// assets/js/checkout-shipping.js
(function ($) {
  $(function () {
    /* ===== Botão "Calcular frete" (recarrega a página) ===== */
    var $billingPostcode = $('#billing_postcode');
    if ($billingPostcode.length && !$('#rs-calc-reload').length) {
      var $btn = $('<button type="button" id="rs-calc-reload" class="button" style="margin-left:8px">Calcular frete</button>');
      $billingPostcode.after($btn);

      $btn.on('click', function () {
        var cep = ($billingPostcode.val() || '').replace(/\D/g, '');
        if (cep.length !== 8) {
          alert('Informe um CEP válido com 8 dígitos.');
          $billingPostcode.focus();
          return;
        }
        // espelha no shipping e garante país BR (opcional)
        $('#shipping_postcode').val(cep).trigger('change');
        $('#billing_country, #shipping_country').each(function () {
          if (!$(this).val()) $(this).val('BR').trigger('change');
        });

        // pequeno atraso para o WC salvar CEP e recarrega
        setTimeout(function () { location.reload(); }, 300);
      });
    }

    /* ===== Recalcular total ao trocar o método de frete ===== */
    let updating = false;
    function refreshTotals() {
      if (updating) return;
      updating = true;
      $(document.body).trigger('update_checkout');
    }

    // quando trocar SEDEX/PAC, etc.
    $(document).on('change', 'input[name^="shipping_method["]', function () {
      alert("aaaaa")
      refreshTotals();
    });

    // libera flag quando o Woo terminar de atualizar
    $(document.body).on('updated_checkout', function () {
      updating = false;
    });
  });
})(jQuery);
