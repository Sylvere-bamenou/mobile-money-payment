<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Quitter si accès direct
}

class Mobile_Money_API {

	/**
	 * Make a request to the SCKALER API.
	 */
	private static function make_request( $method, $endpoint, $body = null, $token = null ) {
		$url = 'https://pay.sckaler.cloud/api' . $endpoint;
		$args = array(
			'method'  => $method,
			'headers' => array(
				'Content-Type' => 'application/json',
			),
		);

		if ( $token ) {
			$args['headers']['Authorization'] = 'Bearer ' . $token;
		}

		if ( $body ) {
			$args['body'] = json_encode( $body );
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		return json_decode( $body, true );
	}

	/**
	 * Generate an authorization token.
	 */
	public static function generate_token( $email, $password, $accountName = null ) {
		$body = array(
			'email'    => $email,
			'password' => $password,
		);

		if ( $accountName ) {
			$body['accountName'] = $accountName;
		}

		return self::make_request( 'POST', '/token', $body );
	}

	/**
	 * Initialize a payment request.
	 */
	public static function send_payment_request( $token, $payment_data ) {
		return self::make_request( 'POST', '/collection', $payment_data, $token );
	}

	/**
	 * Confirm a payment with a confirmation code.
	 */
	public static function confirm_payment( $token, $transaction_id, $confirmation_code ) {
		$body = array(
			'transaction_id'     => $transaction_id,
			'confirmation_code'  => $confirmation_code,
		);

		return self::make_request( 'PUT', '/collection/confirm', $body, $token );
	}

	/**
	 * Initiate a disbursement request.
	 */
	public static function initiate_disbursement( $token, $disbursement_data ) {
		return self::make_request( 'POST', '/disbursement', $disbursement_data, $token );
	}

	/**
	 * Get the status of a transaction.
	 */
	public static function get_transaction_status( $token, $transaction_id ) {
		return self::make_request( 'GET', '/transaction/status/' . $transaction_id, null, $token );
	}

	/**
	 * Get the list of supported countries.
	 */
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

		// Assuming API returns array of country codes
		$country_names = array(
			'BJ' => 'Bénin',
			'CI' => 'Côte d\'Ivoire',
			'BF' => 'Burkina Faso',
			'ML' => 'Mali',
			'SN' => 'Sénégal',
		);

		$supported_countries = array();
		foreach ( $countries as $country_code ) {
			if ( isset( $country_names[ $country_code ] ) ) {
				$supported_countries[ $country_code ] = $country_names[ $country_code ];
			}
		}

		return $supported_countries;
	}

	/**
	 * Get the list of supported providers.
	 */
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
			if ( isset( $provider['country'] ) && isset( $provider['name'] ) ) {
				$providers_by_country[ $provider['country'] ][] = $provider['name'];
			}
		}

		return $providers_by_country;
	}
}
?>
