<?php

/**
 * Template da Loja (WooCommerce) — layout com filtros à esquerda e ordenação no topo
 * Coloque este arquivo como `archive-product.php` no seu tema.
 */

defined('ABSPATH') || exit;

get_header('shop');

/** Esconde o "Mostrando X resultados" do hook padrão */
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

   <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
      <h1 class="text-4xl font-extrabold tracking-tight text-purple-800 mb-6">
         <?php woocommerce_page_title(); ?>
      </h1>
   <?php endif; ?>

   <?php do_action('woocommerce_before_main_content'); ?>

   <div class="grid grid-cols-12 lg:grid-cols-12 gap-8">

      <!-- TOPBAR (ordenacao) — ocupa a linha inteira acima dos filtros e produtos -->
      <!-- <div class="lg:col-span-12 mb-2 flex items-center justify-start">
         <span class="mr-3 text-sm text-gray-600">Ordenação</span>
         <div class="rs-order w-full sm:w-72">
            <?php woocommerce_catalog_ordering(); ?>
         </div>
      </div> -->

      <!-- SIDEBAR (Filtros) -->
      <aside class="col-span-12 lg:col-span-3">
         <div class="rounded-3xl bg-white ring-1 ring-purple-200/70 shadow-sm p-5">
            <div class="flex items-center justify-between">
               <h3 class="text-base font-semibold text-gray-900">Categorias de produto</h3>
               <?php
               // link "limpar filtros" (remove somente nosso parâmetro customizado de categorias)
               $clean_url = remove_query_arg('rs_cats');
               ?>
               <a href="<?php echo esc_url($clean_url); ?>"
                  class="text-sm font-semibold text-purple-700 hover:text-purple-900">Limpar filtros</a>
            </div>

            <?php
            // Categorias raiz (ajuste "parent" se quiser exibir tudo)
            $selected = array_filter(array_map('sanitize_text_field', explode(',', (string)($_GET['rs_cats'] ?? ''))));
            $terms = get_terms([
               'taxonomy'   => 'product_cat',
               'hide_empty' => true,
               'parent'     => 0, // mude para null se quiser todas
            ]);
            ?>

            <form method="get" class="mt-4 space-y-2" id="rs-filter-form">
               <?php foreach ($terms as $t) : ?>
                  <?php
                  $checked = in_array($t->slug, $selected, true);
                  $count   = (int) $t->count;
                  ?>
                  <label class="flex items-center gap-3 rounded-xl px-3 py-2 ring-1 ring-gray-200 hover:ring-purple-300 transition">
                     <input
                        type="checkbox"
                        name="rs_cats[]"
                        value="<?php echo esc_attr($t->slug); ?>"
                        <?php checked($checked); ?>
                        class="h-4 w-4 rounded border-gray-300 text-purple-700 focus:ring-purple-600">
                     <span class="text-sm text-gray-800">
                        <?php echo esc_html($t->name); ?>
                        <span class="text-gray-400">(<?php echo $count; ?>)</span>
                     </span>
                  </label>

                  <?php
                  // filhos (nível 2) – opcional
                  $children = get_terms([
                     'taxonomy'   => 'product_cat',
                     'hide_empty' => true,
                     'parent'     => $t->term_id,
                  ]);
                  if (!empty($children)) :
                  ?>
                     <div class="ml-6 mt-1 space-y-1">
                        <?php foreach ($children as $c) :
                           $child_checked = in_array($c->slug, $selected, true);
                        ?>
                           <label class="flex items-center gap-3 rounded-lg px-3 py-1 ring-1 ring-gray-100 hover:ring-purple-200 transition">
                              <input
                                 type="checkbox"
                                 name="rs_cats[]"
                                 value="<?php echo esc_attr($c->slug); ?>"
                                 <?php checked($child_checked); ?>
                                 class="h-4 w-4 rounded border-gray-300 text-purple-700 focus:ring-purple-600">
                              <span class="text-sm text-gray-700">
                                 <?php echo esc_html($c->name); ?>
                                 <span class="text-gray-400">(<?php echo (int) $c->count; ?>)</span>
                              </span>
                           </label>
                        <?php endforeach; ?>
                     </div>
                  <?php endif; ?>
               <?php endforeach; ?>

               <?php
               // preserva outros parâmetros (busca, paginação, ordenação, etc.)
               foreach ($_GET as $k => $v) {
                  if ($k === 'rs_cats') continue;
                  if (is_array($v)) {
                     foreach ($v as $vv) {
                        echo '<input type="hidden" name="' . esc_attr($k) . '[]" value="' . esc_attr($vv) . '">';
                     }
                  } else {
                     echo '<input type="hidden" name="' . esc_attr($k) . '" value="' . esc_attr($v) . '">';
                  }
               }
               ?>
               <button type="submit" class="hidden">Aplicar</button>
            </form>

            <script>
               // auto-submit ao marcar/desmarcar checkbox
               (function() {
                  const form = document.getElementById('rs-filter-form');
                  if (form) form.addEventListener('change', () => form.submit());
               })();
            </script>
         </div>
      </aside>

      <!-- LISTA DE PRODUTOS -->
      <div class="col-span-12 lg:col-span-9 space-y-4">
         <?php if (woocommerce_product_loop()) : ?>
            <?php woocommerce_product_loop_start(); ?>

            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-6">
               <?php while (have_posts()) : the_post(); ?>
                  <?php wc_get_template_part('content', 'product'); ?>
               <?php endwhile; ?>
            </div>

            <?php woocommerce_product_loop_end(); ?>

            <!-- Paginação -->
            <div class="mt-6">
               <?php woocommerce_pagination(); ?>
            </div>
         <?php else : ?>
            <?php do_action('woocommerce_no_products_found'); ?>
         <?php endif; ?>
      </div>
   </div>

   <?php do_action('woocommerce_after_main_content'); ?>
</div>

<!-- Estilo do seletor de ordenação (não quebra o WooCommerce) -->
<style>
   .rs-order .woocommerce-ordering {
      position: relative;
      width: 100%;
      margin: 0
   }

   .rs-order .woocommerce-ordering select {
      -webkit-appearance: none;
      appearance: none;
      width: 100%;
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: .625rem 2rem .625rem .75rem;
      font-size: .875rem;
      color: #111827;
      outline: 0;
      cursor: pointer
   }

   .rs-order .woocommerce-ordering::after {
      content: "";
      position: absolute;
      right: .75rem;
      top: 50%;
      width: .5rem;
      height: .5rem;
      border-right: 2px solid #6b7280;
      border-bottom: 2px solid #6b7280;
      transform: translateY(-60%) rotate(45deg);
      pointer-events: none
   }

   .rs-order .woocommerce-ordering select:focus {
      box-shadow: 0 0 0 4px rgba(147, 51, 234, .12);
      border-color: #c4b5fd
   }
</style>

<?php get_footer('shop'); ?>