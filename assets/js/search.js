// assets/js/search.js
(function () {
  const input = document.getElementById('product-search');
  const box   = document.getElementById('product-search-results');
  if (!input || !box || typeof ProductSearch === 'undefined') return;

  let lastTerm = '';
  let timer = null;

  const hideBox = () => {
    box.classList.add('hidden');
    box.innerHTML = '';
  };

  const showBox = () => {
    box.classList.remove('hidden');
  };

  const renderItems = (items) => {
    if (!items || !items.length) {
      box.innerHTML = `
        <div class="p-4 text-sm text-gray-500">Nenhum produto encontrado.</div>
      `;
      return;
    }

    const html = items.map(item => `
      <a href="${item.url}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition">
        <img src="${item.image}" alt="" class="w-12 h-12 rounded-lg object-cover ring-1 ring-gray-200" />
        <div class="min-w-0">
          <div class="text-sm font-medium text-gray-900 truncate">${item.title}</div>
          <div class="text-sm text-gray-600">${item.price_html || ''}</div>
        </div>
      </a>
    `).join('');

    box.innerHTML = html;
  };

 const search = async (term) => {
  try {
    const body = new URLSearchParams({
      action: 'search_products',
      nonce: ProductSearch.nonce,
      q: term
    });

    const res = await fetch(ProductSearch.ajax_url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body
    });

    const data = await res.json();
    if (data && data.success) {
      renderItems(data.data.items || []);
      showBox();
    } else {
      hideBox();
    }
  } catch (e) {
    hideBox();
  }
};

  const onInput = (ev) => {
    const term = ev.target.value.trim();
    if (term.length < 2) { hideBox(); return; }
    if (term === lastTerm) return;
    lastTerm = term;

    clearTimeout(timer);
    timer = setTimeout(() => search(term), 200); // debounce 200ms
  };

  // Fechar ao clicar fora
  document.addEventListener('click', (e) => {
    if (!box.contains(e.target) && e.target !== input) {
      hideBox();
    }
  });

  // ESC para fechar
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') hideBox();
  });

  input.addEventListener('input', onInput);
})();
