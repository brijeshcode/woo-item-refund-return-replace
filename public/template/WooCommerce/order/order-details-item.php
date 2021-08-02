<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */
$orderStatus = $order->get_status();
$order_id = $order->get_id();
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>
<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">

	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

		echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$qty          = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

		if ( $refunded_qty ) {
			$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
		} else {
			$qty_display = esc_html( $qty );
		}

		echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</strong>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

		wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
		?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</td>

	<?php if ( apply_filters( 'phoe_woo_order_item_action', true, $order ) ) : ?>
	<td class="woocommerce-table__product-action product-action">

		<?php if ( apply_filters( 'phoe_woo_order_item_show_cancel_btn', true, $order, $item_id )): ?>
			<?php
				$req_status = phoe_getItemCancelStatus($order_id, $item_id);
			?>
			<?php if ( $req_status ): ?>
				<?php esc_html_e( $req_status , 'woocommerce' ); ?>

			<?php else: ?>
				<a href="javascript:void(0)" class="button" onclick="add_request_data('<?= $item_id ?>', 'cancel')"><span  data-toggle="modal" data-target="#myModal" ><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></span></a>
				<!-- <a href="?item_id=<?= $item_id; ?>&order_id=<?= $order_id ?>"  class="button"><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></a> -->
			<?php endif ?>
		<?php endif ?>

		<?php if ( apply_filters( 'phoe_woo_order_item_show_refund_btn', true, $order,$item_id )): ?>
				<?php $btnText = 0 ? 'Return' : 'Refund'; ?>
				<a href="javascript:void(0)" class="button" onclick="add_request_data('<?= $item_id ?>', 'refund')"><span  data-toggle="modal" data-target="#refund" ><?php esc_html_e( $btnText, 'woocommerce' ); ?></span></a>
		<?php endif  ?>

		<?php if ( apply_filters( 'phoe_woo_order_item_show_exchange_btn', true, $order,$item_id )): ?>
				<?php $btnText = 0 ? 'Replace' : 'Exchange'; ?>
				<a href="javascript:void(0)" class="button" onclick="add_request_data('<?= $item_id ?>', 'exchange')"><span  data-toggle="modal" data-target="#myModal" ><?php esc_html_e( $btnText, 'woocommerce' ); ?></span></a>

		<?php endif  ?>
	</td>
	<?php endif; ?>
</tr>

<?php if ( $show_purchase_note && $purchase_note ) : ?>

<tr class="woocommerce-table__product-purchase-note product-purchase-note">

	<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
</tr>

<?php endif; ?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">


