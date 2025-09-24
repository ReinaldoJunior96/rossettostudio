// assets/js/shipping.js
document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  const AJAX_URL =
    (window.WooShip && WooShip.ajax_url) ||
    (window.ProductSearch && ProductSearch.ajax_url) ||
    '/wp-admin/admin-ajax.php';

  const KS = { OPTS: 'woo_ship_opts', SELECTED: 'woo_ship_selected', CEP: 'woo_ship_cep' };

  const form       = document.getElementById('shipping-form');
  const cepInput   = document.getElementById('calculadora-frete');
  const optionsBox = document.getElementById('shipping-options');
  const calcBtn    = document.getElementById('btn-calcular-frete');
  const limparBtn  = document.getElementById('limpar-frete');

  if (!form || !cepInput || !optionsBox) return;

  const isValidCep = (v) => /^\d{8}$/.test(String(v || '').replace(/\D/g, ''));
  const toNumber   = (v) => Number(String(v).replace(',', '.'));

  const setLoading = (loading) => {
    if (!calcBtn) return;
    calcBtn.disabled = !!loading;
    calcBtn.style.opacity = loading ? '0.7' : '1';
    calcBtn.style.pointerEvents = loading ? 'none' : 'auto';
  };

  // === NOVO: atualiza o box .cart_totals sem reload ===
  async function refreshCartTotalsBox() {
    try {
      const resp = await fetch(AJAX_URL, {
        method: 'POST',
        headers: { 'Content-Type':'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'tail_cart_totals' })
      });
      const data = await resp.json();
      if (data && data.success && data.data && data.data.html) {
        const box = document.querySelector('.cart_totals');
        if (box) box.outerHTML = data.data.html; // substitui o bloco inteiro
      } else {
        console.warn('[shipping.js] tail_cart_totals sem HTML, recarregando…');
        location.reload();
      }
    } catch (e) {
      console.warn('[shipping.js] erro em tail_cart_totals, recarregando…', e);
      location.reload();
    }
  }

  // storage helpers
  const saveOptions  = (opts) => sessionStorage.setItem(KS.OPTS, JSON.stringify(opts || []));
  const loadOptions  = () => { try { return JSON.parse(sessionStorage.getItem(KS.OPTS) || '[]'); } catch { return []; } };
  const saveSelected = (cost) => sessionStorage.setItem(KS.SELECTED, String(cost));
  const loadSelected = () => toNumber(sessionStorage.getItem(KS.SELECTED));
  const saveCep      = (cep) => sessionStorage.setItem(KS.CEP, String(cep || ''));
  const loadCep      = () => sessionStorage.getItem(KS.CEP) || '';

  async function applyShippingCost(cost) {
    const n = toNumber(cost);
    if (!Number.isFinite(n) || n < 0) return;
    saveSelected(n);
    try {
      await fetch(AJAX_URL, {
        method: 'POST',
        headers: { 'Content-Type':'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'set_shipping_cost', cost: String(n) })
      }).then(r => r.json());
    } finally {
      // SEM reload: atualiza o Resumo
      await refreshCartTotalsBox();
    }
  }

  function renderOptions(options, preselectedCost) {
    optionsBox.innerHTML = '';
    if (!Array.isArray(options) || options.length === 0) return null;

    let cheapestIdx = 0;
    let cheapestVal = Infinity;
    options.forEach((o, i) => {
      const c = toNumber(o.cost);
      if (Number.isFinite(c) && c < cheapestVal) { cheapestVal = c; cheapestIdx = i; }
    });

    options.forEach((option, idx) => {
      const id    = `opt-${option.id || 'ship'}-${idx}`;
      const cost  = toNumber(option.cost);

      const label = document.createElement('label');
      label.className = 'flex items-center gap-2 mt-2';

      const radio = document.createElement('input');
      radio.type = 'radio';
      radio.name = 'shipping_choice';
      radio.value = String(cost);
      radio.id = id;
      radio.className = 'text-purple-700 focus:ring-purple-500';

      const span = document.createElement('span');
      span.textContent = option.label;

      label.appendChild(radio);
      label.appendChild(span);
      optionsBox.appendChild(label);

      const shouldCheck = Number.isFinite(preselectedCost)
        ? (Math.abs(cost - preselectedCost) < 0.0001)
        : (idx === cheapestIdx);

      if (shouldCheck) {
        radio.checked = true;
        radio.defaultChecked = true;
        radio.setAttribute('checked', 'checked');
      }
    });

    return { cheapest: { index: cheapestIdx, cost: cheapestVal } };
  }

  // Bootstrap: repõe opções salvas (se houver)
  (function bootstrapFromStorage(){
    const opts = loadOptions();
    const selected = loadSelected();
    const cep = loadCep();
    if (cep) cepInput.value = cep;
    if (opts.length) renderOptions(opts, Number.isFinite(selected) ? selected : undefined);
  })();

  // SUBMIT: calcular e aplicar (mostrando e atualizando Resumo SEM reload)
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const cep = String(cepInput.value || '').replace(/\D/g, '');
    if (!isValidCep(cep)) {
      alert('Por favor, insira um CEP válido (8 dígitos).');
      return;
    }

    saveCep(cep);
    setLoading(true);
    optionsBox.innerHTML = '<div class="text-sm text-gray-500">Carregando opções...</div>';

    try {
      const resp = await fetch(AJAX_URL + '?action=calculate_shipping', {
        method: 'POST',
        headers: { 'Content-Type':'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ cep })
      });
      const result = await resp.json();

      if (!result || !result.success) {
        optionsBox.innerHTML = '';
        alert(result?.data?.message || 'Erro ao calcular frete.');
        return;
      }

      const options = result.data.options || [];
      saveOptions(options);

      const selectedBefore = loadSelected();
      const { cheapest } = renderOptions(options, Number.isFinite(selectedBefore) ? selectedBefore : undefined);

      // Se não havia frete aplicado ainda, aplica a mais barata e já atualiza Resumo
      if (!Number.isFinite(selectedBefore) && cheapest && Number.isFinite(cheapest.cost)) {
        await applyShippingCost(cheapest.cost);
      } else {
        // Se já havia frete, apenas atualiza o Resumo (ex.: recalcular depois de mudar CEP)
        await refreshCartTotalsBox();
      }
    } catch (err) {
      console.error('[shipping.js] Erro ao calcular frete:', err);
      optionsBox.innerHTML = '';
      alert('Erro ao calcular frete. Tente novamente.');
    } finally {
      setLoading(false);
    }
  });

  // Trocar de opção: aplica na hora e atualiza Resumo
  optionsBox.addEventListener('change', (e) => {
    const t = e.target;
    if (t && t.type === 'radio' && t.name === 'shipping_choice') {
      const val = toNumber(t.value);
      if (Number.isFinite(val)) applyShippingCost(val);
    }
  });

  // Limpar frete: zera sessão + storage e atualiza Resumo
  if (limparBtn) {
    limparBtn.addEventListener('click', async () => {
      try {
        sessionStorage.removeItem(KS.OPTS);
        sessionStorage.removeItem(KS.SELECTED);
        await fetch(AJAX_URL, {
          method: 'POST',
          headers: { 'Content-Type':'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ action: 'clear_shipping_cost' })
        });
      } finally {
        await refreshCartTotalsBox();
        optionsBox.innerHTML = ''; // some com as opções
      }
    });
  }

  console.debug('[shipping.js] pronto.');
});




