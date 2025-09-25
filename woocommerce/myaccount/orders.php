<?php
defined('ABSPATH') || exit;

/**
 * Normaliza a fonte de dados:
 * - Woo costuma enviar um objeto paginado com ->orders
 * - Alguns hooks/templates podem enviar um array de IDs/objetos
 */
$orders_source = $customer_orders ?? [];
$orders = [];

// Objeto paginado padrão do Woo (tem ->orders)
if (is_object($orders_source) && isset($orders_source->orders) && is_array($orders_source->orders)) {
   $orders = $orders_source->orders;
}
// Já veio um array de pedidos/IDs
elseif (is_array($orders_source)) {
   $orders = $orders_source;
}

$columns    = wc_get_account_orders_columns();
$has_orders = ! empty($orders);
?>

<h2 class="text-xl font-semibold text-gray-900 mb-4"><?php esc_html_e('Meus pedidos', 'woocommerce'); ?></h2>

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

               // $customer_order pode ser ID, WC_Order ou stdClass com ->id
               if ($customer_order instanceof WC_Order) {
                  $order = $customer_order;
               } elseif (is_numeric($customer_order)) {
                  $order = wc_get_order($customer_order);
               } elseif (is_object($customer_order) && isset($customer_order->id)) {
                  $order = wc_get_order($customer_order->id);
               } else {
                  $order = false;
               }

               if (! $order) {
                  // pular entradas inválidas para evitar fatal
                  continue;
               }

               $item_count = $order->get_item_count();
            ?>
               <tr>
                  <?php foreach ($columns as $column_id => $column_name) : ?>
                     <td class="px-4 py-3">
                        <?php
                        switch ($column_id) {
                           case 'order-number':
                              printf(
                                 '<a class="text-purple-700 font-medium hover:underline" href="%1$s">%2$s</a>',
                                 esc_url($order->get_view_order_url()),
                                 esc_html($order->get_order_number())
                              );
                              break;

                           case 'order-date':
                              $date = $order->get_date_created();
                              echo $date ? esc_html(wc_format_datetime($date)) : '&mdash;';
                              break;

                           case 'order-status':
                              echo esc_html(wc_get_order_status_name($order->get_status()));
                              break;

                           case 'order-total':
                              printf(
                                 /* translators: 1: formatted order total 2: total order items */
                                 _n('%1$s para %2$s item', '%1$s para %2$s itens', $item_count, 'woocommerce'),
                                 wp_kses_post($order->get_formatted_order_total()),
                                 intval($item_count)
                              );
                              break;

                           case 'order-actions':
                              $actions = wc_get_account_orders_actions($order);
                              if (! empty($actions)) {
                                 foreach ($actions as $key => $action) {
                                    printf(
                                       '<a href="%1$s" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium ring-1 ring-gray-300 hover:bg-gray-50 mr-2">%2$s</a>',
                                       esc_url($action['url']),
                                       esc_html($action['name'])
                                    );
                                 }
                              }
                              break;

                           default:
                              /**
                               * Permite que colunas personalizadas (ex: rastreio) renderizem conteúdo.
                               * Ex.: add_action( 'woocommerce_my_account_my_orders_column_rastreio', function( $order ) { ... } );
                               */
                              do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order);
                              break;
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

   <?php if (is_object($orders_source) && ! empty($orders_source->max_num_pages) && $orders_source->max_num_pages > 1) : ?>
      <nav class="mt-4 flex items-center justify-between text-sm">
         <?php
         $current = isset($orders_source->query_vars['paged']) ? (int) $orders_source->query_vars['paged'] : 1;

         if ($current > 1) :
            $prev = wc_get_endpoint_url('orders', $current - 1);
         ?>
            <a class="text-purple-700 hover:underline" href="<?php echo esc_url($prev); ?>"><?php esc_html_e('Anterior', 'woocommerce'); ?></a>
         <?php endif; ?>

         <?php if ($current < (int) $orders_source->max_num_pages) :
            $next = wc_get_endpoint_url('orders', $current + 1);
         ?>
            <a class="ml-auto text-purple-700 hover:underline" href="<?php echo esc_url($next); ?>"><?php esc_html_e('Próxima', 'woocommerce'); ?></a>
         <?php endif; ?>
      </nav>
   <?php endif; ?>

<?php else : ?>
   <div class="rounded-xl bg-purple-50 text-purple-800 px-4 py-3">
      <?php esc_html_e('Você ainda não fez nenhum pedido.', 'woocommerce'); ?>
   </div>
<?php endif; ?>