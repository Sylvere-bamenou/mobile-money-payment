<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Quitter si accès direct
}

class Mobile_Money_API {

    // URL de base de l'API SCKALER
    private static $base_url = 'https://pay.sckaler.cloud/api';

    /**
     * Effectue une requête vers l'API SCKALER.
     *
     * @param string $method Méthode HTTP (GET, POST, PUT, DELETE).
     * @param string $endpoint Point de terminaison de l'API.
     * @param array|null $body Corps de la requête (optionnel).
     * @param string|null $token Jeton d'authentification (optionnel).
     * @return array Réponse de l'API sous forme de tableau associatif.
     */
    private static function make_request( $method, $endpoint, $body = null, $token = null ) {
        $url = self::$base_url . $endpoint;
        $args = array(
            'method'  => $method,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => $token ? 'Bearer ' . $token : '',
            ),
            'body' => $body ? json_encode( $body ) : null,
            'timeout' => 45, // Délai d'attente en secondes
        );

        $response = wp_remote_request( $url, $args );

        // Vérifie les erreurs de la requête
        if ( is_wp_error( $response ) ) {
            return array( 'error' => true, 'message' => $response->get_error_message() );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        // Vérifie le code de statut HTTP
        if ( $status_code >= 200 && $status_code < 300 ) {
            return json_decode( $response_body, true );
        } else {
            return array( 'error' => true, 'status_code' => $status_code, 'response_body' => $response_body );
        }
    }

    /**
     * Génère un jeton d'authentification.
     *
     * @param string $email Email de l'utilisateur.
     * @param string $password Mot de passe de l'utilisateur.
     * @param string|null $accountName Nom du compte (optionnel).
     * @return array|string Jeton d'authentification ou détails de l'erreur.
     */
    public static function generate_token( $email, $password, $accountName = null ) {
        $body = array(
            'email' => $email,
            'password' => $password,
        );

        if ( $accountName ) {
            $body['accountName'] = $accountName;
        }

        $response = self::make_request( 'POST', '/token', $body );

        if ( isset( $response['token'] ) ) {
            return $response['token'];
        } else {
            return $response; // Retourne les détails de l'erreur
        }
    }

    /**
     * Envoie une demande de paiement.
     *
     * @param string $provider Fournisseur de services mobiles.
     * @param string $country Code du pays.
     * @param string $tel Numéro de téléphone.
     * @param float $amount Montant à collecter.
     * @param string $description Description de la transaction.
     * @param string $currency Devise de la transaction.
     * @param string $token Jeton d'authentification.
     * @return array Réponse de l'API.
     */
    public static function send_payment_request( $provider, $country, $tel, $amount, $description, $currency, $token ) {
        $body = array(
            'provider' => $provider,
            'country' => $country,
            'tel' => $tel,
            'amount' => $amount,
            'description' => $description,
            'currency' => $currency,
        );

        return self::make_request( 'POST', '/collection', $body, $token );
    }

    /**
     * Confirme un paiement.
     *
     * @param string $transaction_id Identifiant de la transaction.
     * @param string $confirmation_code Code de confirmation envoyé au client.
     * @param string $token Jeton d'authentification.
     * @return array Réponse de l'API.
     */
    public static function confirm_payment( $transaction_id, $confirmation_code, $token ) {
        $body = array(
            'transaction_id' => $transaction_id,
            'confirmation_code' => $confirmation_code,
        );

        return self::make_request( 'PUT', '/collection/confirm', $body, $token );
    }

    /**
     * Initialise une demande de décaissement.
     *
     * @param string $provider Fournisseur de services mobiles.
     * @param string $country Code du pays.
     * @param string $tel Numéro de téléphone.
     * @param float $amount Montant à transférer.
     * @param string $description Description de la transaction.
     * @param string $currency Devise de la transaction.
     * @param string $token Jeton d'authentification.
     * @return array Réponse de l'API.
     */
    public static function initiate_disbursement( $provider, $country, $tel, $amount, $description, $currency, $token ) {
        $body = array(
            'provider' => $provider,
            'country' => $country,
            'tel' => $tel,
            'amount' => $amount,
            'description' => $description,
            'currency' => $currency,
        );

        return self::make_request( 'POST', '/disbursement', $body, $token );
    }

    /**
     * Obtient le statut d'une transaction.
     *
     * @param string $transaction_id Identifiant de la transaction.
     * @param string $token Jeton d'authentification.
     * @return array Réponse de l'API.
     */
    public static function get_transaction_status( $transaction_id, $token ) {
        return self::make_request( 'GET', '/transaction/status/' . $transaction_id, null, $token );
    }

    /**
     * Récupère la liste des pays pris en charge.
     *
     * @return array Réponse de l'API.
     */
    public static function get_supported_countries() {
        return self::make_request( 'GET', '/data/countries' );
    }

    /**
     * Récupère la liste des fournisseurs pris en charge.
     *
     * @return array Réponse de l'API.
     */
    public static function get_supported_providers() {
        return self::make_request( 'GET', '/data/providers' );
    }

}
?>
