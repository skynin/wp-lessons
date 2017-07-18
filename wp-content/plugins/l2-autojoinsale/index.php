<?php
/*
Plugin Name: Авто присоединение метки
Plugin URI: http://skynin.xyz
Description: Авто присоединение метки sales
Version: 1.0
Author: skynin
Author URI: http://skynin.xyz
*/

add_action('updated_postmeta', function($meta_id, $object_id, $meta_key, $meta_value) {
    /**
     * update_post_meta( $post_id, '_sale_price', $new_sale_price );
     * update_post_meta( $post_id, '_price', $new_sale_price ); || update_post_meta( $post_id, '_price', $new_regular_price );
     */

    static $dataPrev = [];

    if ($meta_key === '_price') {

        if ( isset($dataPrev['_sale_price'][$object_id]) ||
            isset($dataPrev['_sale_price_dates_from'][$object_id]) ) {

            if (empty($dataPrev['_sale_price'][$object_id][1]) ) {
                // remove from action category
                wp_remove_object_terms($object_id, 'sales', 'product_tag');
            }
            else {
                // add to action category
                wp_set_object_terms( $object_id, 'sales', 'product_tag', true );
            }
        }
    }
    elseif ($meta_key === '_sale_price' || $meta_key === '_sale_price_dates_from') {
        $dataPrev[$meta_key][$object_id] = [$meta_id, $meta_value];
    }
}, 10, 4);
