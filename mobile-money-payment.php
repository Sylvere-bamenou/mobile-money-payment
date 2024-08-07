<?php
/*
Plugin Name: Paiement Mobile Money
Plugin URI: 
Description: Une passerelle de paiement WooCommerce personnalisée pour les paiements Mobile Money utilisant l'API SCKALER.
Version: 1.0.0
Author: 
Author URI: 
Text Domain: mobile-money-payment
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Quitter si accès direct
}

// Vérifier si WooCommerce est actif et chargé
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	add_action( 'plugins_loaded', 'init_mobile_money_payment_gateway', 11 );

	function init_mobile_money_payment_gateway() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

		// Inclure les fichiers nécessaires
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-money-api.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-money-payment.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

		function add_mobile_money_gateway( $methods ) {
			$methods[] = 'WC_Gateway_Mobile_Money';
			return $methods;
		}

		add_filter( 'woocommerce_payment_gateways', 'add_mobile_money_gateway' );
	}
} else {
	add_action( 'admin_notices', 'woocommerce_not_active_notice' );
}

function woocommerce_not_active_notice() {
	echo '<div class="error"><p>' . __( 'Le plugin Paiement Mobile Money nécessite WooCommerce pour être activé.', 'mobile-money-payment' ) . '</p></div>';
}
?>
