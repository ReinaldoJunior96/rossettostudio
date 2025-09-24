<?php
defined('ABSPATH') || exit;

$items = wc_get_account_menu_items();
?>
<nav class="rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm">
   <ul class="divide-y divide-gray-100">
      <?php foreach ($items as $endpoint => $label) :
         $url   = wc_get_account_endpoint_url($endpoint);
         $is_on = ($endpoint === 'dashboard')
            ? (! is_wc_endpoint_url())
            : is_wc_endpoint_url($endpoint);
      ?>
         <li>
            <a href="<?php echo esc_url($url); ?>"
               class="block px-4 py-3 text-sm font-medium
                  <?php echo $is_on
                     ? 'bg-purple-50 text-purple-700'
                     : 'text-gray-700 hover:bg-gray-50'; ?>">
               <?php echo esc_html($label); ?>
            </a>
         </li>
      <?php endforeach; ?>
   </ul>
</nav>