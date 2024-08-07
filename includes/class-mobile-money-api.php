<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Quitter si accès direct
}

class Mobile_Money_API {

	public static function send_payment_request( $token, $payment_data ) {
		$response = wp_remote_post( 'https://pay.sckaler.cloud/api/collection', array(
			'body'    => json_encode( $payment_data ),
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $token,
			),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['transaction_id'] ) && $body['status'] === 'PENDING' ) {
			return $body;
		}

		return new WP_Error( 'api_error', __( 'La demande de paiement a échoué.', 'mobile-money-payment' ), $body );
	}

	public static function get_supported_countries() {
		$response = wp_remote_get( 'https://pay.sckaler.cloud/api/data/countries' );

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$countries = json_decode( $body, true );

		if ( ! is_array( $countries ) ) {
			return array();
		}

		return $countries;
	}

	public static function get_supported_providers() {
		$response = wp_remote_get( 'https://pay.sckaler.cloud/api/data/providers' );

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$providers_list = json_decode( $body, true );

		if ( ! is_array( $providers_list ) ) {
			return array();
		}

		$providers_by_country = array();

		foreach ( $providers_list as $provider ) {
			if ( isset( $provider['country_code'] ) && isset( $provider['name'] ) ) {
				$providers_by_country[$provider['country_code']][] = $provider['name'];
			}
		}

		return $providers_by_country;
	}
}
?>
