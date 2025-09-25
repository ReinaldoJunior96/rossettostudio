<?php
defined('ABSPATH') || exit;
global $product;

if (empty($product)) {
   return;
}
?>

<section class="flex justify-center">
   <section class="margin-site pb-10">
      <div id="product-<?php the_ID(); ?>" class="w-full">
         <h1 class="text-2xl font-bold text-purple-default leading-tight max-w-lg pt-10">
            <?php the_title(); ?>
         </h1>

         <div class="flex items-start gap-10">
            <!-- Galeria -->
            <div class="flex flex-col w-1/2">
               <?php do_action('woocommerce_before_single_product_summary'); ?>
            </div>

            <!-- Info -->
            <div class="flex-1 flex flex-col">
               <!-- Descrição -->
               <div class="max-w-3xl mt-4">
                  <h2 class="text-2xl font-bold text-purple-700 mb-2">Descrição aaa</h2>
                  <div class="flex items-center gap-2">
                     <?php wc_get_template('single-product/rating.php'); ?>
                     <span class="text-gray-500 text-sm align-middle">4.5 (67 avaliações)</span>
                  </div>
                  <hr class="my-2">
                  <div class="text-gray-700 leading-relaxed"><?php the_content(); ?></div>
               </div>

               <?php
               // ---------- FORMULÁRIO ----------
               if ($product->is_type('variable')) :
                  wp_enqueue_script('wc-add-to-cart-variation'); // JS nativo de variações
                  woocommerce_template_single_price();
                  // imprime o formulário (seletores de variação + qty + botão)
                  woocommerce_template_single_add_to_cart();
               elseif ($product->is_type('simple')) :
                  $min = max(1, (int) $product->get_min_purchase_quantity());
                  $max = $product->get_max_purchase_quantity();
               ?>
                  <form class="flex flex-col items-start gap-4 mt-6"
                     action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
                     method="post" enctype="multipart/form-data">

                     <div class="flex flex-col gap-1">
                        <span class="font-semibold">Quantidade</span>
                        <?php
                        woocommerce_quantity_input(
                           [
                              'input_value' => $min,
                              'min_value'   => $min,
                              'max_value'   => ($max !== '' && $max !== null) ? $max : false,
                              'classes'     => ['w-20', 'text-center', 'rounded', 'border', 'border-purple-300'],
                           ],
                           $product,
                           true
                        );
                        ?>
                     </div>

                     <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">

                     <button type="submit"
                        class="single_add_to_cart_button button alt w-full px-6 py-3 rounded-lg bg-green-500 text-white font-bold text-lg flex items-center justify-center gap-2 hover:bg-green-600 transition">
                        Adicionar ao carrinho <i class="fa fa-shopping-cart"></i>
                     </button>
                  </form>
               <?php else :
                  do_action('woocommerce_' . $product->get_type() . '_add_to_cart');
               endif; ?>
            </div>
         </div>
      </div>
   </section>
</section>

<!-- TRANSFORMA SELECTs EM BOTÕES (Tailwind) -->
<script>
   document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('.variations_form');
      if (!form) return;

      // Esconde a tabela de selects (mantém acessível)
      const table = form.querySelector('table.variations');
      if (table) table.classList.add('sr-only');

      // Mapeia cores comuns -> cor de fundo (para pa_cor)
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

      // Cria grupos de botões para cada select
      form.querySelectorAll('select').forEach((sel) => {
         const row = sel.closest('tr');
         const label = row ? row.querySelector('label') : null;
         const title = (label ? label.textContent : sel.name)
            .replace('attribute_', '')
            .replace('pa_', '')
            .trim();

         // Wrapper (título + botões)
         const wrap = document.createElement('div');
         wrap.className = 'mb-4';
         const h = document.createElement('div');
         h.className = 'text-sm font-semibold mb-2';
         h.textContent = title.charAt(0).toUpperCase() + title.slice(1);
         const group = document.createElement('div');
         group.className = 'flex flex-wrap gap-2';

         // Cria botão para cada opção (ignora vazio)
         Array.from(sel.options).forEach((opt) => {
            if (!opt.value) return;

            const btn = document.createElement('button');
            btn.type = 'button';

            const isColor = sel.name.includes('pa_cor');
            if (isColor) {
               btn.className = 'w-9 h-9 rounded-full border border-purple-300 ring-0 focus:outline-none';
               const key = opt.text.toLowerCase().trim();
               const bg = colorMap[key];
               if (bg) {
                  btn.style.background = bg;
                  // borda para cores claras
                  if (bg === '#ffffff' || bg === '#f9fafb') btn.style.boxShadow = 'inset 0 0 0 1px #e5e7eb';
               } else {
                  // sem mapeamento -> mostra texto
                  btn.className = 'px-3 py-1 rounded-lg border border-purple-300 text-sm text-purple-700 hover:bg-purple-50';
                  btn.textContent = opt.text;
               }
            } else {
               btn.className = 'px-3 py-1 rounded-lg border border-purple-300 text-sm text-purple-700 hover:bg-purple-50';
               btn.textContent = opt.text;
            }

            const activate = () => {
               group.querySelectorAll('button').forEach(b => {
                  b.classList.remove('!bg-purple-600', '!text-white', '!border-purple-600', 'ring-2', 'ring-purple-500');
               });
               if (isColor) {
                  btn.classList.add('ring-2', 'ring-purple-500');
               } else {
                  btn.classList.add('!bg-purple-600', '!text-white', '!border-purple-600');
               }
            };

            // estado inicial
            if (opt.selected) activate();

            // clique -> seta select e dispara change do Woo
            btn.addEventListener('click', () => {
               sel.value = opt.value;
               sel.dispatchEvent(new Event('change', {
                  bubbles: true
               }));
               activate();
            });

            group.appendChild(btn);
         });

         wrap.appendChild(h);
         wrap.appendChild(group);
         // insere antes da tabela escondida
         if (table && table.parentNode) table.parentNode.insertBefore(wrap, table);
      });

      // Estiliza quantidade e botão do form de variações
      const qty = form.querySelector('.quantity .qty');
      if (qty) qty.classList.add('w-20', 'text-center', 'rounded', 'border', 'border-purple-300');

      const atc = form.querySelector('.single_add_to_cart_button');
      if (atc) atc.classList.add('w-full', 'px-6', 'py-3', 'rounded-lg', 'bg-green-500', 'text-white', 'font-bold', 'text-lg', 'hover:bg-green-600', 'transition', 'mt-4');
   });
</script>