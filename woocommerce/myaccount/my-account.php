<?php
defined('ABSPATH') || exit;
do_action('woocommerce_before_account_navigation');
?>
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
   <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      <aside class="lg:col-span-3">
         <?php do_action('woocommerce_account_navigation'); ?>
      </aside>

      <main class="lg:col-span-9">
         <div class="rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm p-6">
            <?php do_action('woocommerce_account_content'); ?>
         </div>
      </main>
   </div>
</div>