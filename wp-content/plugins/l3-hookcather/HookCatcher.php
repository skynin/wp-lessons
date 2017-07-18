<?php

/**
 * Description of HookCatcher
 *
 * @author Skynin
 */
class HookCatcher {
	/**
	 * Example array:
	[
	'manage_shop_coupon_posts_columns' => ['__return_empty_array', 20],
	'woocommerce_register_post_type_shop_order' => null,
	'manage_shop_order_posts_columns' => 20,
	'manage_shop_order_posts_custom_column' => [10, 2],
	'manage_shop_order_posts_custom_column' => [[$object, 'method'], 20, 3],
	]
	 * @param array $actions_filters
	 */
	public static function zInit($actions_filters) {
		foreach ($actions_filters as $key => $value) {

			$priority = 10;
			$accepted_args = 1;

			if (is_int($value)) {
				$priority = $value;
			}
			elseif (isset($value[0])) {
				$priority = $value[0];
				if (is_string($priority) || is_array($priority)) {

					$call_func = $priority;

					$priority = isset($value[1]) ? $value[1] : 10;
					$accepted_args = isset($value[2]) ? $value[2] : 1;

					add_filter($key, $call_func, $priority, $accepted_args);

					continue;
				}
			}

			if (isset($value[1])) $accepted_args = $value[1];

			add_filter($key, get_called_class() . '::' . $key , $priority, $accepted_args);
		}
	}
}
