<?php
defined('ABSPATH') || exit;

/** @var WC_Order $order */
$order = wc_get_order($order_id);
?>
<h2 class="text-xl font-semibold text-gray-900 mb-4">
   Pedido #<?php echo esc_html($order->get_order_number()); ?>
</h2>

<p class="text-sm text-gray-600 mb-6">
   Status: <span class="font-medium text-gray-900"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span> •
   Criado em: <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?>
</p>

<div class="rounded-2xl ring-1 ring-gray-200 bg-white shadow-sm p-6 space-y-6">
   <?php do_action('woocommerce_view_order', $order_id); ?>
</div>