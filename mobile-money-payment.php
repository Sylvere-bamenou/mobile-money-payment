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

// Vérifier si WooCommerce est actif
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// Inclure les fichiers nécessaires
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-money-api.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-money-payment.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

	// Initialiser la passerelle de paiement
	add_action( 'plugins_loaded', 'init_mobile_money_payment_gateway' );

	function init_mobile_money_payment_gateway() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

		class WC_Gateway_Mobile_Money extends WC_Payment_Gateway {
			public function __construct() {
				$this->id                 = 'mobile_money';
				$this->icon               = ''; 
				$this->has_fields         = true;
				$this->method_title       = __( 'Paiement Mobile Money', 'mobile-money-payment' );
				$this->method_description = __( 'Permet les paiements via Mobile Money en utilisant SCKALER.', 'mobile-money-payment' );

				$this->init_form_fields();
				$this->init_settings();

				$this->title       = $this->get_option( 'title' );
				$this->description = $this->get_option( 'description' );
				$this->api_token   = $this->get_option( 'api_token' );

				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
				add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
				add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'check_response' ) );

				// Enqueue scripts and styles
				add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			}

			public function init_form_fields() {
				$this->form_fields = array(
					'enabled' => array(
						'title'   => __( 'Activer/Désactiver', 'mobile-money-payment' ),
						'type'    => 'checkbox',
						'label'   => __( 'Activer la passerelle de paiement Mobile Money', 'mobile-money-payment' ),
						'default' => 'yes',
					),
					'title' => array(
						'title'       => __( 'Titre', 'mobile-money-payment' ),
						'type'        => 'text',
						'description' => __( 'Ce titre est affiché lors du paiement.', 'mobile-money-payment' ),
						'default'     => __( 'Paiement Mobile Money', 'mobile-money-payment' ),
						'desc_tip'    => true,
					),
					'description' => array(
						'title'       => __( 'Description', 'mobile-money-payment' ),
						'type'        => 'textarea',
						'description' => __( 'Cette description est affichée lors du paiement.', 'mobile-money-payment' ),
						'default'     => __( 'Payez via Mobile Money en utilisant SCKALER.', 'mobile-money-payment' ),
					),
					'api_token' => array(
						'title'       => __( 'Token API', 'mobile-money-payment' ),
						'type'        => 'text',
						'description' => __( 'Entrez votre token API SCKALER.', 'mobile-money-payment' ),
						'default'     => '',
					),
				);
			}

			public function process_payment( $order_id ) {
				$order = wc_get_order( $order_id );

				$payment_data = array(
					'provider'    => sanitize_text_field( $_POST['mobile_money_provider'] ),
					'country'     => sanitize_text_field( $_POST['mobile_money_country'] ),
					'tel'         => sanitize_text_field( $_POST['mobile_money_phone'] ),
					'amount'      => $order->get_total(),
					'description' => 'Commande #' . $order->get_order_number(),
					'currency'    => get_woocommerce_currency(),
				);

				$response = Mobile_Money_API::send_payment_request( $this->api_token, $payment_data );

				if ( is_wp_error( $response ) ) {
					wc_add_notice( 'Erreur de paiement: ' . $response->get_error_message(), 'error' );
					error_log( 'Erreur de paiement: ' . $response->get_error_message() ); // Log l'erreur pour le débogage
					return;
				}

				if ( isset( $response['status'] ) && $response['status'] !== 'PENDING' ) {
					$error_message = isset( $response['msg'] ) ? $response['msg'] : 'Erreur inconnue';
					wc_add_notice( 'Erreur de paiement: ' . $error_message, 'error' );
					error_log( 'Erreur de paiement: ' . print_r( $response, true ) ); // Log la réponse complète pour le débogage
					return;
				}

				$order->payment_complete();
				$order->reduce_order_stock();
				WC()->cart->empty_cart();

				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}

			public function payment_fields() {
				include plugin_dir_path( __FILE__ ) . 'templates/payment-form.php';
			}

			public function validate_fields() {
				if ( empty( $_POST['mobile_money_country'] ) ) {
					wc_add_notice( __( 'Veuillez sélectionner un pays.', 'mobile-money-payment' ), 'error' );
					return false;
				}
				if ( empty( $_POST['mobile_money_provider'] ) ) {
					wc_add_notice( __( 'Veuillez sélectionner un réseau mobile.', 'mobile-money-payment' ), 'error' );
					return false;
				}
				if ( empty( $_POST['mobile_money_phone'] ) ) {
					wc_add_notice( __( 'Veuillez entrer votre numéro de téléphone.', 'mobile-money-payment' ), 'error' );
					return false;
				}
				return true;
			}

			public function payment_scripts() {
				if ( ! is_checkout() ) return;

				wp_enqueue_script( 'mobile-money-payment-js', plugins_url( '/assets/js/scripts.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
				wp_enqueue_style( 'mobile-money-payment-css', plugins_url( '/assets/css/styles.css', __FILE__ ), array(), '1.0.0' );

				// Localiser le script pour passer des données à JavaScript
				wp_localize_script( 'mobile-money-payment-js', 'mobileMoneyPaymentData', array(
					'countries' => Mobile_Money_API::get_supported_countries(),
					'providers' => Mobile_Money_API::get_supported_providers(),
					'selectProviderText' => __( 'Sélectionnez un fournisseur', 'mobile-money-payment' )
				) );
			}
		}

		function add_mobile_money_gateway( $methods ) {
			$methods[] = 'WC_Gateway_Mobile_Money';
			return $methods;
		}

		add_filter( 'woocommerce_payment_gateways', 'add_mobile_money_gateway' );
	}
}
?>
