(function($){
  function onlyDigits(s){ return (s||'').replace(/\D+/g,''); }
  function validCEP(s){ return /^\d{8}$/.test(onlyDigits(s)); }

  function injectCalcButton(){
    var $field = $('#billing_postcode');
    if(!$field.length) return;
    if($field.parent().find('.rs-calc-frete').length) return;

    var $btn = $('<button type="button" class="rs-calc-frete" style="margin-left:.5rem;padding:.5rem .75rem;border-radius:.5rem;background:#6b21a8;color:#fff;font-weight:600;">Calcular frete</button>');
    $field.after($btn);

    $btn.on('click', function(){
      var cep = onlyDigits($field.val() || '');
      if(!validCEP(cep)){
        alert('Informe um CEP válido (8 dígitos).');
        $field.focus();
        return;
      }
      $('#billing_postcode').val(cep).trigger('change');
      $('#shipping_postcode').val(cep).trigger('change');

      if(!$('#billing_country').val()){ $('#billing_country').val('BR').trigger('change'); }
      if(!$('#shipping_country').val()){ $('#shipping_country').val('BR').trigger('change'); }

      $(document.body).trigger('update_checkout');
      if ($('.woocommerce-checkout-review-order').length){
        $('html,body').animate({scrollTop: $('.woocommerce-checkout-review-order').offset().top - 40}, 300);
      }
    });
  }

  $(function(){
    // Força um primeiro update (caso o cliente tenha dados salvos)
    setTimeout(function(){ $(document.body).trigger('update_checkout'); }, 80);

    injectCalcButton();
    $(document.body).on('updated_checkout', injectCalcButton);

    // Digitou CEP e saiu do campo: tenta calcular
    $(document).on('change', '#billing_postcode, #shipping_postcode', function(){
      var cep = onlyDigits($(this).val());
      if(validCEP(cep)){
        $('#billing_postcode').val(cep);
        $('#shipping_postcode').val(cep);
        if(!$('#billing_country').val()){ $('#billing_country').val('BR').trigger('change'); }
        if(!$('#shipping_country').val()){ $('#shipping_country').val('BR').trigger('change'); }
        $(document.body).trigger('update_checkout');
      }
    });

    // Trocou método de envio: atualiza total
    $(document).on('change', 'input[name^="shipping_method"]', function(){
      $(document.body).trigger('update_checkout');
    });

    // Se a calculadora nativa aparecer, intercepta submit
    $(document).on('submit', 'form.woocommerce-shipping-calculator', function(e){
      e.preventDefault();
      var cep = onlyDigits($(this).find('input[name="calc_shipping_postcode"]').val()||'');
      if(!validCEP(cep)){ alert('Informe um CEP válido (8 dígitos).'); return; }
      $('#billing_postcode,#shipping_postcode').val(cep).trigger('change');
      if(!$('#billing_country').val()){ $('#billing_country').val('BR').trigger('change'); }
      if(!$('#shipping_country').val()){ $('#shipping_country').val('BR').trigger('change'); }
      $(document.body).trigger('update_checkout');
    });
  });
})(jQuery);
