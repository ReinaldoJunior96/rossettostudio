<?php
defined('ABSPATH') || exit;
get_header('shop');
?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

   <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
      <h1 class="text-3xl font-bold text-purple-700 mb-6">
         <?php woocommerce_page_title(); ?>
      </h1>
   <?php endif; ?>

   <?php do_action('woocommerce_before_main_content'); ?>

   <!-- Topbar: contagem + ordenação -->
   <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="text-sm text-gray-600">
         <?php woocommerce_result_count(); ?>
      </div>
      <div class="min-w-[220px]">
         <?php woocommerce_catalog_ordering(); ?>
      </div>
   </div>

   <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

      <!-- SIDEBAR -->
      <aside class="lg:col-span-3">
         <div class="rounded-2xl ring-1 ring-purple-200 bg-white p-5 space-y-6">

            <!-- Filtros ativos -->
            <div>
               <h3 class="text-sm font-semibold text-gray-900 mb-3">Filtros ativos</h3>
               <div class="text-sm">
                  <?php the_widget('WC_Widget_Layered_Nav_Filters', [], [
                     'before_widget' => '<div class="active-filters">',
                     'after_widget'  => '</div>',
                  ]); ?>
               </div>
            </div>

            <!-- Busca -->
            <div>
               <h3 class="text-sm font-semibold text-gray-900 mb-3">Buscar</h3>
               <?php get_product_search_form(); ?>
            </div>

            <!-- Categorias -->
            <div>
               <h3 class="text-sm font-semibold text-gray-900 mb-3">Categorias</h3>
               <?php the_widget('WC_Widget_Product_Categories', [
                  'count'         => 1,
                  'hierarchical'  => 1,
                  'show_children_only' => 0,
                  'hide_empty'    => 1,
               ], [
                  'before_widget' => '<div class="text-sm">',
                  'after_widget'  => '</div>',
               ]); ?>
            </div>

            <!-- Preço -->
            <div>
               <h3 class="text-sm font-semibold text-gray-900 mb-3">Preço</h3>
               <?php the_widget('WC_Widget_Price_Filter', [], [
                  'before_widget' => '<div class="text-sm">',
                  'after_widget'  => '</div>',
               ]); ?>
            </div>

            <!-- Cor -->
            <div>
               <h3 class="text-sm font-semibold text-gray-900 mb-3">Cor</h3>
               <?php the_widget('WC_Widget_Layered_Nav', [
                  'attribute'  => 'pa_color',   // ajuste se seu slug for outro
                  'query_type' => 'or',
               ], [
                  'before_widget' => '<div class="text-sm">',
                  'after_widget'  => '</div>',
               ]); ?>
            </div>

            <!-- Tamanho -->
            <div>
               <h3 class="text-sm font-semibold text-gray-900 mb-3">Tamanho</h3>
               <?php the_widget('WC_Widget_Layered_Nav', [
                  'attribute'  => 'pa_size',
                  'query_type' => 'or',
               ], [
                  'before_widget' => '<div class="text-sm">',
                  'after_widget'  => '</div>',
               ]); ?>
            </div>

            <!-- Material -->
            <div>
               <h3 class="text-sm font-semibold text-gray-900 mb-3">Material</h3>
               <?php the_widget('WC_Widget_Layered_Nav', [
                  'attribute'  => 'pa_material',
                  'query_type' => 'or',
               ], [
                  'before_widget' => '<div class="text-sm">',
                  'after_widget'  => '</div>',
               ]); ?>
            </div>

            <!-- Limpar filtros -->
            <div class="pt-2">
               <?php
               // link "limpar" (remove parâmetros de filtro/ordenação/página)
               $base_url = get_permalink(wc_get_page_id('shop'));
               ?>
               <a href="<?php echo esc_url($base_url); ?>"
                  class="inline-flex items-center gap-2 text-sm font-semibold text-purple-700 hover:text-purple-900">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Limpar filtros
               </a>
            </div>

         </div>
      </aside>

      <!-- LISTA -->
      <div class="lg:col-span-9">
         <?php do_action('woocommerce_before_shop_loop'); ?>

         <?php if (woocommerce_product_loop()) : ?>
            <?php woocommerce_product_loop_start(); ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-6">
               <?php while (have_posts()) : the_post(); ?>
                  <?php wc_get_template_part('content', 'product'); ?>
               <?php endwhile; ?>
            </div>
            <?php woocommerce_product_loop_end(); ?>

            <?php do_action('woocommerce_after_shop_loop'); ?>
         <?php else : ?>
            <?php do_action('woocommerce_no_products_found'); ?>
         <?php endif; ?>

      </div>
   </div>

   <?php do_action('woocommerce_after_main_content'); ?>

</div>

<?php get_footer('shop'); ?>