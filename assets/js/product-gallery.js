document.addEventListener('DOMContentLoaded', () => {
  const mainLink = document.querySelector('.js-main-link');
  const mainImg  = document.querySelector('.js-main-img');
  const thumbs   = document.querySelectorAll('.js-thumb');

  if (!mainLink || !mainImg || !thumbs.length) return;

  // marca a primeira como ativa
  thumbs[0].classList.add('is-active');

  function swapMain(btn) {
    const full   = btn.dataset.full;                // URL original (para lightbox)
    const fullW  = parseInt(btn.dataset.fullW || '0', 10);
    const fullH  = parseInt(btn.dataset.fullH || '0', 10);
    const large  = btn.dataset.large || full;       // URL para exibir na <img>

    // Preload para evitar flicker
    const img = new Image();
    img.onload = () => {
      // 1) Troca a imagem exibida
      mainImg.src = large;
      // Importante: limpa srcset/sizes para o browser não usar variantes antigas
      mainImg.removeAttribute('srcset');
      mainImg.removeAttribute('sizes');
      mainImg.setAttribute('loading', 'eager');
      mainImg.setAttribute('decoding', 'async');

      // 2) Atualiza atributos que o lightbox/zoom usam
      if (full) {
        mainLink.href = full;
        mainLink.setAttribute('data-large_image', full);
        if (fullW) mainLink.setAttribute('data-large_image_width', String(fullW));
        if (fullH) mainLink.setAttribute('data-large_image_height', String(fullH));

        mainImg.setAttribute('data-large_image', full);
        if (fullW) mainImg.setAttribute('data-large_image_width', String(fullW));
        if (fullH) mainImg.setAttribute('data-large_image_height', String(fullH));
      }

      // 3) Destaque da thumb ativa
      thumbs.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
    };

    // dispara preload
    img.src = large;
  }

  thumbs.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      swapMain(btn);
    });
  });
});
