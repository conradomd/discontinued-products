<?php
/**
 * Plugin Name: Discontinued Products
 * Description: Add discontinued stock status.
 * Author: Conrado Diorio
 * Version: 1.0
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Add new stock status options
function filter_woocommerce_product_stock_status_options( $status ) {
    // Add new statuses
    $status['discontinued'] = __( 'Discontinued', 'woocommerce' );

    return $status;
}
add_filter( 'woocommerce_product_stock_status_options', 'filter_woocommerce_product_stock_status_options', 10, 1 );

// Availability text
function filter_woocommerce_get_availability_text( $availability, $product ) {
    // Get stock status
    switch( $product->get_stock_status() ) {
        case 'discontinued':
            $availability = __( 'Discontinued', 'woocommerce' );
        break;
    }

    return $availability; 
}
add_filter( 'woocommerce_get_availability_text', 'filter_woocommerce_get_availability_text', 10, 2 );

// Availability CSS class
function filter_woocommerce_get_availability_class( $class, $product ) {
    // Get stock status
    switch( $product->get_stock_status() ) {
        case 'discontinued':
            $class = 'out-of-stock';
        break;
        
    }

    return $class;
}
add_filter( 'woocommerce_get_availability_class', 'filter_woocommerce_get_availability_class', 10, 2 );

// Admin stock html
function filter_woocommerce_admin_stock_html( $stock_html, $product ) {
    // Simple
    if ( $product->is_type( 'simple' ) ) {
        // Get stock status
        $product_stock_status = $product->get_stock_status();
    // Variable
    } elseif ( $product->is_type( 'variable' ) ) {
        foreach( $product->get_visible_children() as $variation_id ) {
            // Get product
            $variation = wc_get_product( $variation_id );
            
            // Get stock status
            $product_stock_status = $variation->get_stock_status();
            
        }
    }
    
    // Stock status
    switch( $product_stock_status ) {
        case 'discontinued':
            $stock_html = '<mark class="pre-order" style="background:transparent none;color:#33ccff;font-weight:700;line-height:1;">' . __( 'Discontinued', 'woocommerce' ) . '</mark>';
        break;
    }
 
    return $stock_html;
}
add_filter( 'woocommerce_admin_stock_html', 'filter_woocommerce_admin_stock_html', 10, 2 );

// Disable Add Cart Button for Discontinued Productos
function filter_is_purchasable_callback( $purchasable, $product ) {
	
    if ( $product->get_stock_status() === 'discontinued' ) {
        return false;
    }

    return $purchasable;
}
add_filter('woocommerce_is_purchasable', 'filter_is_purchasable_callback', 10, 2 );
add_filter('woocommerce_variation_is_purchasable', 'filter_is_purchasable_callback', 10, 2 );

// Show message for single product 
function action_woocommerce_simple_add_to_cart() {
	
	global $product;
	
	if ( $product->get_stock_status() === 'discontinued' ) {

		echo '<span class="out-of-stock"><i class="fas fa-exclamation-triangle"></i> <b>Discontinued Product</b></span>';
		
	}
	
	return;

}
add_action( 'woocommerce_simple_add_to_cart', 'action_woocommerce_simple_add_to_cart', 10, 0); 
