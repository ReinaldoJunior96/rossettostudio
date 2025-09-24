<?php
defined('ABSPATH') || exit;
global $product;

$post_thumbnail_id = $product->get_image_id();
$attachment_ids    = $product->get_gallery_image_ids();
$has_image         = (bool) $post_thumbnail_id;

if ($has_image) {
   $main_full  = wp_get_attachment_image_src($post_thumbnail_id, 'full');   // [url,w,h]
   $main_large = wp_get_attachment_image_src($post_thumbnail_id, 'large');  // [url,w,h]
}
?>

<div class="woocommerce-product-gallery images w-full"
   data-columns="4"
   data-product_id="<?php echo esc_attr($product->get_id()); ?>">

   <div class="grid grid-cols-[72px_1fr] gap-4 items-start">

      <!-- Thumbs -->
      <div class="flex flex-col gap-2">
         <?php
         // Principal primeiro
         if ($has_image) :
            echo sprintf(
               '<button type="button" class="js-thumb group relative focus:outline-none"
              data-full="%1$s" data-full-w="%2$d" data-full-h="%3$d"
              data-large="%4$s" data-large-w="%5$d" data-large-h="%6$d">
              %7$s
              <span class="absolute inset-0 rounded-xl ring-2 ring-transparent group-[.is-active]:ring-purple-500 pointer-events-none"></span>
           </button>',
               esc_url($main_full[0]),
               (int)$main_full[1],
               (int)$main_full[2],
               esc_url($main_large[0]),
               (int)$main_large[1],
               (int)$main_large[2],
               wp_get_attachment_image($post_thumbnail_id, 'thumbnail', false, [
                  'class' => 'w-16 h-16 object-cover rounded-xl ring-1 ring-purple-200 hover:ring-purple-400 transition'
               ])
            );
         endif;

         // Restante da galeria
         if ($attachment_ids) :
            foreach ($attachment_ids as $aid) :
               $f  = wp_get_attachment_image_src($aid, 'full');
               $lg = wp_get_attachment_image_src($aid, 'large');
               echo sprintf(
                  '<button type="button" class="js-thumb group relative focus:outline-none"
                data-full="%1$s" data-full-w="%2$d" data-full-h="%3$d"
                data-large="%4$s" data-large-w="%5$d" data-large-h="%6$d">
                %7$s
                <span class="absolute inset-0 rounded-xl ring-2 ring-transparent group-[.is-active]:ring-purple-500 pointer-events-none"></span>
             </button>',
                  esc_url($f[0]),
                  (int)$f[1],
                  (int)$f[2],
                  esc_url($lg[0]),
                  (int)$lg[1],
                  (int)$lg[2],
                  wp_get_attachment_image($aid, 'thumbnail', false, [
                     'class' => 'w-16 h-16 object-cover rounded-xl ring-1 ring-purple-200 hover:ring-purple-400 transition'
                  ])
               );
            endforeach;
         endif;
         ?>
      </div>

      <!-- Imagem grande -->
      <figure class="woocommerce-product-gallery__wrapper rounded-2xl ring-1 ring-gray-200 bg-white p-4 flex items-center justify-center w-auto ">
         <div class="woocommerce-product-gallery__image">
            <?php if ($has_image) : ?>
               <a href="<?php echo esc_url($main_full[0]); ?>"
                  class="js-main-link"
                  data-large_image="<?php echo esc_url($main_full[0]); ?>"
                  data-large_image_width="<?php echo (int)$main_full[1]; ?>"
                  data-large_image_height="<?php echo (int)$main_full[2]; ?>">
                  <?php
                  echo wp_get_attachment_image($post_thumbnail_id, 'large', false, [
                     'class'                      => 'js-main-img wp-post-image object-contain max-h-[540px] rounded-xl',
                     'data-large_image'           => esc_url($main_full[0]),
                     'data-large_image_width'     => (int)$main_full[1],
                     'data-large_image_height'    => (int)$main_full[2],
                  ]);
                  ?>
               </a>
            <?php else : ?>
               <img class="object-contain max-h-[540px] rounded-xl" src="<?php echo esc_url(wc_placeholder_img_src('large')); ?>" alt="" />
            <?php endif; ?>
         </div>
      </figure>

   </div>
</div>