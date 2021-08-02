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
	if ($_POST['request_item_action'] == 'cancel') {
		phoe_customer_order_item_request('cancel');
	}elseif($_POST['request_item_action'] == 'refund'){
		phoe_customer_order_item_request('refund');
	}elseif($_POST['request_item_action'] == 'exchange'){
		phoe_customer_order_item_request('exchange');
	}else{
		// lol invalid action.
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
				<?php if ( apply_filters( 'phoe_woo_order_item_action', true , $order) ) : ?>
				<?php //if($order->get_status() == 'processing' && sizeof( $order->get_items() ) > 1 && !is_page('checkout')){//processing?>
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
						<td> <!-- action td --> </td>
					<?php endif; ?>
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
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Please Select Reason</h4>
      </div>
      <form method="post">
	<?php wp_nonce_field('phoe_order_item_action_cancel', 'phoe_order_item_action_cancel_nonce_field'); ?>

      	<input type="hidden" name="order_id" value="<?= $order_id; ?>">
      	<input type="hidden" name="item_id" class="order_item_id">
	      <div class="modal-body">
	      	 <?php
	      	 	if ($order->get_status() == 'processing') {
	      	 		$type = 'cancel' ;
	      	 	}elseif($order->get_status() == 'completed'){
	      	 		$type = 'exchange';
	      	 	}
	      	 	$reasons = get_item_reasons($type);
	      	 ?>
	      	 <div>
	      	 	<?php foreach ($reasons as $key => $value): ?>
	      	 		<tags onclick="show_my_reasons('<?= sanitize_title($value['tag']); ?>' , '<?= $value['tag'] ?>')"><?= $value['tag'] ?></tags>
	      	 	<?php endforeach ?>
	      	 </div>
	      	<hr>
	      	<h2 class="selected-reason"></h2>
	        <ul class="list-group">
	        	<?php foreach ($reasons as $key => $value): ?>
	 				<?php foreach (explode(',', $value['reasons']) as $reasonVal): ?>
	        		<li class="list-group-item reason-items <?= sanitize_title($value['tag']) ?> ">
	        			<label> <input type="radio" name="reason" value="<?= $value['tag'].' | '.$reasonVal ?>"><?= $reasonVal ?></label>
	        		</li>
	 				<?php endforeach ?>

	        	<?php endforeach ?>
	        </ul>
	      </div>
	      <div class="modal-footer">
	      	<input type="hidden" name="request_item_action" id="request_item_action" value="cancel">
	        <button type="submit" class="btn btn-primary" value="cancel_request">Submit</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
      </form>
    </div>

  </div>
</div>


<?php if ($order->get_status() == 'completed'): ?>

<div id="refund" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Please Select Reason</h4>
      </div>
      <form method="post">
	<?php wp_nonce_field('phoe_order_item_action_cancel', 'phoe_order_item_action_cancel_nonce_field'); ?>

      	<input type="hidden" name="order_id" value="<?= $order_id; ?>">
      	<input type="hidden" name="item_id" class="order_item_id">
	      <div class="modal-body">
	      	 <?php
	      	 	$type = 'refund';
	      	 	$reasons = get_item_reasons($type);
	      	 ?>
	      	 <div>
	      	 	<?php foreach ($reasons as $key => $value): ?>
	      	 		<tags onclick="show_my_reasons('<?= sanitize_title($value['tag']); ?>' , '<?= $value['tag'] ?>')"><?= $value['tag'] ?></tags>
	      	 	<?php endforeach ?>
	      	 </div>
	      	<hr>
	      	<h2 class="selected-reason"></h2>
	        <ul class="list-group">
	        	<?php foreach ($reasons as $key => $value): ?>
	 				<?php foreach (explode(',', $value['reasons']) as $reasonVal): ?>
	        		<li class="list-group-item reason-items <?= sanitize_title($value['tag']) ?> ">
	        			<label> <input type="radio" name="reason" value="<?= $value['tag'].' | '.$reasonVal ?>"><?= $reasonVal ?></label>
	        		</li>
	 				<?php endforeach ?>

	        	<?php endforeach ?>
	        </ul>
	      </div>
	      <div class="modal-footer">
	      	<input type="hidden" name="request_item_action" value="refund">
	        <button type="submit" class="btn btn-primary" value="cancel_request">Submit</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
      </form>
    </div>

  </div>
</div>

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

	function add_request_data(item_id, actionType){
		jQuery('.order_item_id').val(item_id);
		jQuery('#request_item_action').val(actionType);
	}
</script>
<?php