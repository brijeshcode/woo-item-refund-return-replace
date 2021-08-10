<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

if (isset($_POST['request_item_action'])) {
	if (in_array($_POST['request_item_action'], ['exchange', 'cancel', 'refund']) ) {
		$message = phoe_create_customer_order_item_request($_POST['request_item_action']);
		echo '<div class="woocommerce-Message woocommerce-Message--'.$message['status'].' woocommerce-'.$message['status'].'"> '.$message['message']	.'</div>';
	}else{
	}
}
defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
				<?php
					$valid = apply_filters( 'phoe_woo_order_item_action', true , $order) ;
				?>
				<?php if ( is_array($valid) && !empty($valid)) : ?>

					<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Action', 'woocommerce' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>

					<?php if ( apply_filters( 'phoe_woo_order_item_action', true, $order ) ) : ?>

						<?php //if($order->get_status() == 'processing' && sizeof( $order->get_items() ) > 1 && !is_page('checkout')){//processing?>
					<?php endif; ?>
						<td> <!-- action td --> </td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
?>
<!-- Modal -->


<?php if ($order->get_status() == 'completed'): ?>
<?php require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))).'partials/phoe-refund-reason-model.php'; ?>
<?php require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))).'partials/phoe-exchange-reason-model.php'; ?>
<?php else: ?>
<?php require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))).'partials/phoe-cancel-reason-model.php'; ?>
<?php endif ?>

<style type="text/css">
tags {
    background: #d9edf7;
    padding: 5px;
    margin: 5px 4px 9px;
    font-size: 20px;
    font-weight: bolder;
    display: inline-block;
    cursor: pointer;
    border-radius: 2px 10px 9px 2px;
    box-shadow: 1px 1px 5px 1px #988a8a;
}
.reason-items{
	display: none;
}
</style>

<script type="text/javascript">

	function show_my_reasons(type, tag){
		jQuery('.reason-items').hide();
		jQuery('.selected-reason').text(tag);
		jQuery('.'+ type).show();
	}

	function add_request_data(order_id, item_id, actionType){
		jQuery('.order_id').val(order_id);
		jQuery('.order_item_id').val(item_id);
		jQuery('#request_item_action').val(actionType);
	}
</script>
<?php