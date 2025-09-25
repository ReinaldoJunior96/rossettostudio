<?php

/**
 * Single Product – vitrine clean (Tailwind)
 */
defined('ABSPATH') || exit;

/** @var WC_Product $product */
global $product;
if (empty($product)) return;

// Garante JS de variações para atualizar preço/estoque dinamicamente
if ($product->is_type('variable')) {
   wp_enqueue_script('wc-add-to-cart-variation');
}

get_header('shop');
?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
   <?php do_action('woocommerce_before_main_content'); ?>

   <div id="product-<?php the_ID(); ?>" <?php wc_product_class('grid grid-cols-1 lg:grid-cols-12 gap-8', $product); ?>>

      <!-- GALERIA (esquerda) -->
      <div class="lg:col-span-6">
         <div class="rounded-2xl ring-1 ring-purple-200/60 bg-white p-4">
            <?php
            /**
             * Hook: woocommerce_before_single_product_summary
             * (sale flash + gallery padrão do Woo)
             */
            do_action('woocommerce_before_single_product_summary');
            ?>
         </div>

         <!-- Thumbs em fila bonitinha (o Woo já injeta, só estilizamos via CSS abaixo) -->
      </div>

      <!-- RESUMO (direita) -->
      <div class="lg:col-span-6">
         <!-- Título -->
         <h1 class="text-3xl font-extrabold tracking-tight text-purple-800 leading-tight">
            <?php the_title(); ?>
         </h1>

         <!-- Rating + estoque -->
         <div class="mt-2 flex items-center gap-3">
            <?php wc_get_template('single-product/rating.php'); ?>
            <?php if ($product->get_stock_status() === 'instock') : ?>
               <span class="text-sm text-green-600 font-medium">Em estoque</span>
            <?php else : ?>
               <span class="text-sm text-rose-600 font-medium">Indisponível</span>
            <?php endif; ?>
         </div>

         <!-- Preço -->
         <div class="mt-3 text-2xl font-bold text-gray-900">
            <?php woocommerce_template_single_price(); ?>
         </div>

         <!-- Bloco Descrição curto -->
         <div class="mt-5 rounded-2xl ring-1 ring-purple-200/70 bg-white shadow-sm p-5">
            <h2 class="text-xl font-bold text-purple-700 mb-2">Descrição</h2>
            <div class="h-px bg-purple-100 mb-3"></div>
            <div class="prose max-w-none text-gray-700">
               <?php
               $short = apply_filters('woocommerce_short_description', $post->post_excerpt);
               if (!empty($short)) {
                  echo wp_kses_post($short);
               } else {
                  the_content();
               }
               ?>
            </div>
         </div>

         <!-- Variações / Comprar -->
         <div class="mt-6 rounded-2xl ring-1 ring-purple-200/70 bg-white shadow-sm p-5">
            <?php
            if ($product->is_type('variable')) :
               // formulário padrão (selects + qty + botão). Vamos “chipar” com JS abaixo.
               woocommerce_template_single_add_to_cart();
            elseif ($product->is_type('simple')) :
               $min = max(1, (int) $product->get_min_purchase_quantity());
               $max = $product->get_max_purchase_quantity();
            ?>
               <form class="cart flex flex-col gap-4" method="post" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>">
                  <div class="flex items-center gap-4">
                     <div>
                        <label class="block text-sm font-semibold mb-1">Quantidade</label>
                        <?php
                        woocommerce_quantity_input(
                           [
                              'input_value' => $min,
                              'min_value'   => $min,
                              'max_value'   => ($max !== '' && $max !== null) ? $max : false,
                              'classes'     => ['w-24 text-center rounded-lg border border-purple-300 h-11'],
                           ],
                           $product,
                           true
                        );
                        ?>
                     </div>
                     <button type="submit"
                        class="flex-1 h-11 rounded-xl bg-green-500 text-white font-semibold hover:bg-green-600 transition single_add_to_cart_button">
                        Adicionar ao carrinho
                     </button>
                  </div>
                  <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
               </form>
            <?php
            else :
               do_action('woocommerce_' . $product->get_type() . '_add_to_cart');
            endif;
            ?>
         </div>

         <!-- (Opcional) metas: categorias/tags/SKU -->
         <?php /* woocommerce_template_single_meta(); */ ?>
      </div>
   </div>

   <?php do_action('woocommerce_after_main_content'); ?>
</div>

<!-- ESTILOS finos: galeria + variações em chips + botão -->
<style>
   /* Galeria: imagem principal com borda suave e thumbs em pills */
   .woocommerce-product-gallery {
      --tw-ring-color: rgba(196, 181, 253, .6);
   }

   .woocommerce-product-gallery__wrapper img {
      border-radius: 1rem;
      /* rounded-2xl */
   }

   .flex-control-thumbs {
      display: grid !important;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: .5rem;
      margin-top: .75rem;
   }

   .flex-control-thumbs li {
      list-style: none;
   }

   .flex-control-thumbs img {
      border-radius: .75rem;
      /* rounded-xl */
      border: 1px solid #e9d5ff;
      /* purple-200 */
      padding: .25rem;
      background: #fff;
      transition: box-shadow .15s ease, transform .15s ease;
   }

   .flex-control-thumbs img.flex-active,
   .flex-control-thumbs img:hover {
      box-shadow: 0 0 0 3px rgba(147, 51, 234, .18);
      transform: translateY(-1px);
   }

   /* Esconde a tabela nativa de variações, mas mantém acessível para o Woo */
   .variations_form table.variations {
      position: absolute;
      left: -9999px;
      width: 1px;
      height: 1px;
      overflow: hidden;
   }

   /* Preço que muda ao escolher variação */
   .single_variation .price {
      font-size: 1.5rem;
      line-height: 2rem;
      font-weight: 700;
      color: #111827;
   }

   /* Linha qty + botão gerada pelo Woo */
   .variations_form .single_variation_wrap {
      margin-top: .5rem;
      margin-bottom: 1rem;
   }
</style>

<script>
   document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('.variations_form');
      if (!form) return;

      // Onde vamos renderizar os chips
      const host = document.createElement('div');
      host.className = 'space-y-4';
      form.insertBefore(host, form.firstElementChild);

      // Mapa de cores => bolinha
      const colorMap = {
         'preto': '#000000',
         'branco': '#ffffff',
         'vermelho': '#ef4444',
         'azul': '#3b82f6',
         'amarelo': '#f59e0b',
         'roxo': '#8b5cf6',
         'verde': '#22c55e',
         'rosa': '#ec4899',
         'cinza': '#6b7280',
         'lilas': '#a78bfa',
         'marrom': '#92400e',
         'laranja': '#f97316'
      };

      // Para cada select (atributo)
      // ...
      form.querySelectorAll('select').forEach((sel) => {
         const row = sel.closest('tr');
         const label = row ? row.querySelector('label') : null;

         // nome do campo: attribute_pa_cor, attribute_cor, attribute_tamanho, etc.
         const name = sel.getAttribute('name') || '';
         const slug = name.replace(/^attribute_/, ''); // remove "attribute_"
         const rawTitle = (label ? label.textContent : slug).trim();
         const title = rawTitle.replace(/^pa_/, '');

         // trata "cor" tanto como taxonomia quanto atributo custom; aceita "color" tb
         const isColor = /(^|[_-])(pa_)?cor($|[_-])|color/i.test(slug) || /cor|color/i.test(rawTitle);

         // Wrapper (título + chips)
         const wrap = document.createElement('div');
         wrap.innerHTML = `
    <div class="text-sm font-semibold mb-1">${title.charAt(0).toUpperCase() + title.slice(1)}</div>
    <div class="flex flex-wrap gap-2"></div>
  `;
         const group = wrap.querySelector('div:last-child');

         Array.from(sel.options).forEach((opt) => {
            if (!opt.value) return;

            const btn = document.createElement('button');
            btn.type = 'button';

            if (isColor) {
               // bolinha
               btn.className = 'w-9 h-9 rounded-full border border-purple-300 bg-white';
               const key = opt.text.toLowerCase().trim();
               const bg = ({
                  'preto': '#000000',
                  'branco': '#ffffff',
                  'vermelho': '#ef4444',
                  'azul': '#3b82f6',
                  'amarelo': '#f59e0b',
                  'roxo': '#8b5cf6',
                  'verde': '#22c55e',
                  'rosa': '#ec4899',
                  'cinza': '#6b7280',
                  'lilas': '#a78bfa',
                  'marrom': '#92400e',
                  'laranja': '#f97316'
               })[key];

               if (bg) {
                  btn.style.background = bg;
                  if (bg === '#ffffff') btn.style.boxShadow = 'inset 0 0 0 1px #e5e7eb';
               } else {
                  // se não reconhecer a cor pelo nome, cai para chip com texto
                  btn.className = 'px-3 py-1 rounded-full border border-purple-300 text-sm text-purple-700 bg-white hover:bg-purple-50';
                  btn.textContent = opt.text;
               }
            } else {
               // chip de texto
               btn.className = 'px-3 py-1 rounded-full border border-purple-300 text-sm text-purple-700 bg-white hover:bg-purple-50';
               btn.textContent = opt.text;
            }

            const activate = () => {
               group.querySelectorAll('button').forEach(b => b.classList.remove('ring-2', 'ring-purple-500', '!bg-purple-600', '!text-white', '!border-purple-600'));
               if (isColor) btn.classList.add('ring-2', 'ring-purple-500');
               else btn.classList.add('!bg-purple-600', '!text-white', '!border-purple-600');
            };

            if (opt.selected) activate();

            btn.addEventListener('click', () => {
               sel.value = opt.value;
               sel.dispatchEvent(new Event('change', {
                  bubbles: true
               }));
               activate();
            });

            group.appendChild(btn);
         });

         form.insertBefore(wrap, form.firstElementChild);
      });
      // ...

      // Qty + botão (renderizado pelo Woo)
      const qty = form.querySelector('.quantity .qty');
      if (qty) qty.classList.add('w-24', 'text-center', 'rounded-lg', 'border', 'border-purple-300', 'h-11');

      const atc = form.querySelector('.single_add_to_cart_button');
      if (atc) atc.classList.add('h-11', 'rounded-xl', 'bg-green-500', 'text-white', 'font-semibold', 'hover:bg-green-600', 'transition', 'flex-1');
   });
</script>

<?php get_footer('shop'); ?>