// assets/js/cart-shipping.js
document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  if (!document.body.classList.contains('woocommerce-cart')) return;

  const AJAX_URL = (window.WooShip && WooShip.ajax_url) || '/wp-admin/admin-ajax.php';
  const KS = { OPTS: 'woo_ship_opts', SELECTED: 'woo_ship_selected', CEP: 'woo_ship_cep' };

  const form       = document.getElementById('shipping-form');
  const cepInput   = document.getElementById('calculadora-frete');
  const optionsBox = document.getElementById('shipping-options');
  const calcBtn    = document.getElementById('btn-calcular-frete');
  const limparBtn  = document.getElementById('limpar-frete');

  if (!form || !cepInput || !optionsBox) return;

  const isValidCep = v => /^\d{8}$/.test(String(v || '').replace(/\D/g, ''));
  const toNumber   = v => Number(String(v).replace(',', '.'));

  const setLoading = (loading) => {
    if (!calcBtn) return;
    calcBtn.disabled = !!loading;
    calcBtn.style.opacity = loading ? '0.7' : '1';
    calcBtn.style.pointerEvents = loading ? 'none' : 'auto';
  };

  async function refreshCartTotalsBox() {
    try {
      const r = await fetch(AJAX_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ action: 'tail_cart_totals' })
      });
      const data = await r.json();
      if (data?.success && data?.data?.html) {
        const box = document.querySelector('.cart_totals');
        if (box) box.outerHTML = data.data.html;
      } else {
        location.reload();
      }
    } catch {
      location.reload();
    }
  }

  // storage helpers
  const saveOptions  = (opts) => sessionStorage.setItem(KS.OPTS, JSON.stringify(opts || []));
  const loadOptions  = () => { try { return JSON.parse(sessionStorage.getItem(KS.OPTS) || '[]'); } catch { return []; } };
  const saveSelected = (rid) => sessionStorage.setItem(KS.SELECTED, String(rid || ''));
  const loadSelected = () => sessionStorage.getItem(KS.SELECTED) || '';
  const saveCep      = (cep) => sessionStorage.setItem(KS.CEP, String(cep || ''));
  const loadCep      = () => sessionStorage.getItem(KS.CEP) || '';

  async function chooseRate(rate_id) {
    if (!rate_id) return;
    saveSelected(rate_id);
    await fetch(AJAX_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: new URLSearchParams({ action: 'set_shipping_method', rate_id })
    }).then(r => r.json());
    await refreshCartTotalsBox();
  }

  function renderOptions(options, preselectedRateId) {
    optionsBox.innerHTML = '';
    if (!Array.isArray(options) || options.length === 0) return;

    // definir mais barata
    let cheapest = null;
    options.forEach(o => {
      const c = toNumber(o.cost);
      if (Number.isFinite(c)) {
        if (!cheapest || c < cheapest.cost) cheapest = { id: o.id, cost: c };
      }
    });

    options.forEach((o, idx) => {
      const id = `opt-${idx}`;
      const wrap = document.createElement('label');
      wrap.className = 'flex items-center gap-2 mt-2 cursor-pointer';

      const radio = document.createElement('input');
      radio.type = 'radio';
      radio.name = 'shipping_choice';
      radio.value = o.id; // rate_id
      radio.id = id;
      radio.className = 'text-purple-700 focus:ring-purple-500';

      const span = document.createElement('span');
      span.textContent = o.label;

      wrap.appendChild(radio);
      wrap.appendChild(span);
      optionsBox.appendChild(wrap);

      const shouldCheck = preselectedRateId
        ? (o.id === preselectedRateId)
        : (cheapest && o.id === cheapest.id);

      if (shouldCheck) {
        radio.checked = true;
        radio.defaultChecked = true;
        setTimeout(() => chooseRate(o.id), 0); // aplica escolhida
      }
    });
  }

  // bootstrap (CEP e opções anteriores)
  (function initFromStorage(){
    const cep = loadCep();
    if (cep) cepInput.value = cep;
    const opts = loadOptions();
    const selected = loadSelected();
    if (opts.length) renderOptions(opts, selected);
  })();

  // calcular
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const cep = String(cepInput.value || '').replace(/\D/g, '');
    if (!isValidCep(cep)) {
      alert('Informe um CEP válido (8 dígitos).');
      return;
    }
    saveCep(cep);
    setLoading(true);
    optionsBox.innerHTML = '<div class="text-sm text-gray-500">Calculando frete…</div>';

    try {
      const r = await fetch(AJAX_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ action: 'calculate_shipping', cep })
      });
      const data = await r.json();
      if (!data?.success) {
        optionsBox.innerHTML = '';
        alert(data?.data?.message || 'Erro ao calcular frete.');
        return;
      }
      const options = data.data.options || [];
      saveOptions(options);
      const selected = loadSelected();
      renderOptions(options, selected);
      await refreshCartTotalsBox();
    } catch (err) {
      console.error(err);
      optionsBox.innerHTML = '';
      alert('Erro ao calcular frete. Tente novamente.');
    } finally {
      setLoading(false);
    }
  });

  // troca de opção
  optionsBox.addEventListener('change', async (e) => {
    const t = e.target;
    if (t && t.type === 'radio' && t.name === 'shipping_choice') {
      await chooseRate(t.value);
    }
  });

  // limpar
  if (limparBtn) {
    limparBtn.addEventListener('click', async () => {
      sessionStorage.removeItem(KS.OPTS);
      sessionStorage.removeItem(KS.SELECTED);
      await fetch(AJAX_URL, {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:new URLSearchParams({ action:'clear_shipping_method' })
      });
      optionsBox.innerHTML = '';
      await refreshCartTotalsBox();
    });
  }
});
