<?php

class OrderUtils extends HookCatcher {
	static $kindList = ['product', 'store', 'tag'];
	static $kindCodes;

	public static function zInit($actions_filters) {
		parent::zInit($actions_filters);

		self::$kindCodes = array_flip(JooseOrderUtils::$kindList);
	}

	/**
	 *
	 * @param WC_Order|WP_Post|int $order
	 * @param string $typeReturn
	 */
	static function get_order_kind($order, $typeReturn = null) {
	//	if (is_array($order)) {
	//		fore
	//	}

		$object_id = $order;

		if ($order instanceof WC_Order) $object_id = $order->id;
		elseif ($order instanceof WP_Post) $object_id = $order->ID;

		if (empty($object_id)) return '';

		$kindName = get_metadata('post', $object_id, '_order_kind', true);

		if ($typeReturn == 'name')	return $kindName ? $kindName : self::$kindList[0];

		return isset(self::$kindCodes[$kindName]) ? self::$kindCodes[$kindName] : 0;
	}

	public static function woocommerce_register_post_type_shop_order($arrParams) {
		$arrParams['supports'] = array_merge($arrParams['supports'], array( 'author', 'editor', 'wpcom-markdown' ));

		return $arrParams;
	}

	/**
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public static function woocommerce_process_shop_order_meta($post_id, $post) {
		if (isset($_POST['order_kind'])) {
			$kind = absint( $_POST['order_kind']);
			$kind = isset(self::$kindList[$kind]) ? self::$kindList[$kind] : self::$kindList[0];

			update_post_meta( $post_id, '_order_kind', $kind );
		}
	}

	public static function add_meta_boxes() {
		// \wp-content\plugins\woocommerce\includes\admin\class-wc-admin-meta-boxes.php
		remove_meta_box( 'woocommerce-order-items', 'shop_order', 'normal' );
		remove_meta_box( 'woocommerce-order-downloads', 'shop_order', 'normal' );

		remove_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Items::save', 10);
		remove_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Downloads::save', 30);
		remove_action( 'woocommerce_process_shop_coupon_meta', 'WC_Meta_Box_Coupon_Data::save', 10 );
	}

	public static function joose_admin_order_data_after_shipping_address(WC_Order $order) {
// \wp-content\plugins\woocommerce\includes\admin\meta-boxes\class-wc-meta-box-order-data.php

		$content = '';
		if ($order) {
			$content = $order->post->post_content;
		}
	?>
	  <div class="order_data_column">
		<h3>Содержание заявки</h3>
		<?= wpautop(esc_html($content)) ?>
	  </div>
	<?php
	}

	public static function joose_admin_order_kind(WC_Order $order) {
// 198: \wp-content\plugins\woocommerce\includes\admin\meta-boxes\class-wc-meta-box-order-data.php
	?>
		<p class="form-field form-field-wide wc-order-kind"><label for="order_kind"><?php _e( 'Order kind:', 'joose-rate' ) ?></label>
		<select id="order_kind" name="order_kind" class="wc-enhanced-select">
			<?php
				foreach ( self::$kindList as $kind => $kind_name ) {
					$kind_name = __($kind_name, 'joose-rate');
					echo '<option value="' . esc_attr( $kind ) . '" ' . selected( $kind, self::get_order_kind($order), false ) . '>' . esc_html( $kind_name ) . '</option>';
				}
			?>
		</select></p>
		<?php
	}

	public static function manage_shop_order_posts_columns($existing_columns) {
		unset($existing_columns['order_items']);
		unset($existing_columns['billing_address']);
		unset($existing_columns['shipping_address']);
		unset($existing_columns['order_total']);

		$result = array();

		foreach ($existing_columns as $key => $value) {
			if ($key == 'order_status') {
				$result['order_type'] = __('Order type', 'joose-rate');
			}
			$result[$key] = $value;
		}

		return $result;
	}

	public static function manage_shop_order_posts_custom_column($column_name, $post_ID) {
		if ($column_name == 'order_type') {
			echo self::get_order_kind($post_ID, 'name');
		}
	}

	public static function woocommerce_resend_order_emails_available($order_actions) {
		$order_actions = array_filter($order_actions, function ($elem) {
			return $elem == 'customer_invoice' || $elem == 'customer_refunded_order' ? null : $elem;
		});

		return $order_actions;
	}
}

OrderUtils::zinit(
[
	'woocommerce_register_post_type_shop_order' => null,
	'woocommerce_admin_billing_fields' => ['__return_empty_array', 20],
	'woocommerce_admin_shipping_fields' => ['__return_empty_array', 20],
	'add_meta_boxes' => 50,
	'joose_admin_order_data_after_shipping_address' => null,
	'joose_admin_order_kind' => null,
	'manage_shop_coupon_posts_columns' => ['__return_empty_array', 20],
	'manage_shop_order_posts_columns' => 20,
	'manage_shop_order_posts_custom_column' => [10, 2],
	'woocommerce_resend_order_emails_available' => [20],
	'woocommerce_process_shop_order_meta' => [40, 2],
]);
