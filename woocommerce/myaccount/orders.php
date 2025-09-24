<?php
defined('ABSPATH') || exit;

$orders = $customer_orders ?? array(); // compat
$has_orders = ! empty($orders);
$columns = wc_get_account_orders_columns();
?>

<h2 class="text-xl font-semibold text-gray-900 mb-4">Meus pedidos</h2>

<?php if ($has_orders) : ?>
   <div class="overflow-hidden rounded-2xl ring-1 ring-gray-200 bg-white shadow-sm">
      <table class="min-w-full text-sm">
         <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
               <?php foreach ($columns as $column_id => $column_name) : ?>
                  <th class="px-4 py-3 text-left"><?php echo esc_html($column_name); ?></th>
               <?php endforeach; ?>
            </tr>
         </thead>
         <tbody class="divide-y divide-gray-100">
            <?php foreach ($orders as $customer_order) :
               $order = wc_get_order($customer_order);
               $item_count = $order ? $order->get_item_count() : 0;
            ?>
               <tr>
                  <?php foreach ($columns as $column_id => $column_name) : ?>
                     <td class="px-4 py-3">
                        <?php
                        if ('order-number' === $column_id) {
                           echo '<a class="text-purple-700 font-medium hover:underline" href="' . esc_url($order->get_view_order_url()) . '">'
                              . esc_html($order->get_order_number()) . '</a>';
                        } elseif ('order-date' === $column_id) {
                           echo esc_html(wc_format_datetime($order->get_date_created()));
                        } elseif ('order-status' === $column_id) {
                           echo esc_html(wc_get_order_status_name($order->get_status()));
                        } elseif ('order-total' === $column_id) {
                           /* translators: 1: formatted order total 2: total order items */
                           printf(
                              _n('%1$s para %2$s item', '%1$s para %2$s itens', $item_count, 'woocommerce'),
                              $order->get_formatted_order_total(),
                              $item_count
                           );
                        } elseif ('order-actions' === $column_id) {
                           $actions = wc_get_account_orders_actions($order);
                           if (! empty($actions)) {
                              foreach ($actions as $key => $action) {
                                 echo '<a href="' . esc_url($action['url']) . '" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium ring-1 ring-gray-300 hover:bg-gray-50 mr-2">'
                                    . esc_html($action['name']) . '</a>';
                              }
                           }
                        }
                        ?>
                     </td>
                  <?php endforeach; ?>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
   </div>

   <?php do_action('woocommerce_before_account_orders_pagination'); ?>

   <?php if (1 < ($customer_orders->max_num_pages ?? 1)) : ?>
      <nav class="mt-4 flex items-center justify-between text-sm">
         <?php if (! empty($customer_orders->query_vars['paged']) && 1 !== $customer_orders->query_vars['paged']) : ?>
            <a class="text-purple-700 hover:underline" href="<?php echo esc_url(wc_get_endpoint_url('orders', $customer_orders->query_vars['paged'] - 1)); ?>"><?php esc_html_e('Anterior', 'woocommerce'); ?></a>
         <?php endif; ?>
         <?php if (($customer_orders->max_num_pages ?? 1) !== ($customer_orders->query_vars['paged'] ?? 1)) : ?>
            <a class="ml-auto text-purple-700 hover:underline" href="<?php echo esc_url(wc_get_endpoint_url('orders', $customer_orders->query_vars['paged'] + 1)); ?>"><?php esc_html_e('Próxima', 'woocommerce'); ?></a>
         <?php endif; ?>
      </nav>
   <?php endif; ?>

<?php else : ?>
   <div class="rounded-xl bg-purple-50 text-purple-800 px-4 py-3">
      Você ainda não fez nenhum pedido.
   </div>
<?php endif; ?>