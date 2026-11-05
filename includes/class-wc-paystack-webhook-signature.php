<?php
/**
 * Paystack webhook signature verification.
 *
 * Kept free of WordPress and WooCommerce calls so it can be unit tested on its own.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verifies the `Paystack-Signature` header (webhook signing secret) and, while the store still
 * holds a secret key, the legacy `X-Paystack-Signature` header.
 */
class WC_Paystack_Webhook_Signature {

	/**
	 * Verified with the `Paystack-Signature` header and a `whsec_` signing secret.
	 */
	const SCHEME_SIGNING_SECRET = 'signing_secret';

	/**
	 * Verified with the legacy `X-Paystack-Signature` header and an `sk_` secret key.
	 */
	const SCHEME_SECRET_KEY = 'secret_key';

	/**
	 * How far, in seconds, the signature timestamp may be from the server's clock.
	 */
	const TOLERANCE = 300;

	/**
	 * Verify a webhook request.
	 *
	 * When a signing secret is set, only `Paystack-Signature` is accepted, so a leaked secret key
	 * cannot be used to forge webhooks. Without one, `X-Paystack-Signature` is checked against the
	 * key only when it is a secret key: Paystack never signs with a restricted key.
	 *
	 * @param string $raw_body             The request body, exactly as received.
	 * @param string $paystack_signature   The `Paystack-Signature` header, or ''.
	 * @param string $x_paystack_signature The `X-Paystack-Signature` header, or ''.
	 * @param string $signing_secret       The `whsec_` signing secret for the active mode, or ''.
	 * @param string $api_key              The secret or restricted key for the active mode, or ''.
	 * @param int    $now                  Current Unix time.
	 *
	 * @return array{valid: bool, scheme: string|null, reason: string|null}
	 */
	public static function verify( $raw_body, $paystack_signature, $x_paystack_signature, $signing_secret, $api_key, $now ) {

		if ( '' !== $signing_secret ) {
			return self::verify_signing_secret( $raw_body, $paystack_signature, $signing_secret, $now );
		}

		if ( 0 !== strpos( $api_key, 'sk_' ) ) {
			return self::fail( 'no webhook signing secret is configured, and the API key is not a secret key' );
		}

		if ( '' === $x_paystack_signature ) {
			return self::fail( 'missing X-Paystack-Signature header' );
		}

		if ( ! hash_equals( hash_hmac( 'sha512', $raw_body, $api_key ), strtolower( $x_paystack_signature ) ) ) {
			return self::fail( 'X-Paystack-Signature does not match' );
		}

		return array(
			'valid'  => true,
			'scheme' => self::SCHEME_SECRET_KEY,
			'reason' => null,
		);
	}

	/**
	 * Verify `Paystack-Signature`: `t=<unix seconds>,v1=<key id>:<hex>[,v1=<key id>:<hex>]`, where each
	 * hex value is HMAC-SHA512 over `<t>.<raw body>` keyed with the whole `whsec_` string. During a
	 * secret rotation Paystack sends one `v1` entry per live secret.
	 *
	 * @param string $raw_body       The request body.
	 * @param string $header         The `Paystack-Signature` header.
	 * @param string $signing_secret The `whsec_` signing secret.
	 * @param int    $now            Current Unix time.
	 *
	 * @return array{valid: bool, scheme: string|null, reason: string|null}
	 */
	private static function verify_signing_secret( $raw_body, $header, $signing_secret, $now ) {

		if ( '' === $header ) {
			return self::fail( 'missing Paystack-Signature header' );
		}

		$timestamp  = null;
		$signatures = array();

		foreach ( explode( ',', $header ) as $part ) {
			$pair = explode( '=', trim( $part ), 2 );

			if ( 2 !== count( $pair ) ) {
				continue;
			}

			if ( 't' === $pair[0] ) {
				$timestamp = $pair[1];
			} elseif ( 'v1' === $pair[0] ) {
				$colon        = strrpos( $pair[1], ':' );
				$signatures[] = false === $colon ? $pair[1] : substr( $pair[1], $colon + 1 );
			}
		}

		if ( null === $timestamp || ! preg_match( '/\A\d+\z/', $timestamp ) || empty( $signatures ) ) {
			return self::fail( 'malformed Paystack-Signature header' );
		}

		if ( abs( $now - (int) $timestamp ) > self::TOLERANCE ) {
			return self::fail( 'Paystack-Signature timestamp is outside the ' . self::TOLERANCE . ' second tolerance' );
		}

		$expected = hash_hmac( 'sha512', $timestamp . '.' . $raw_body, $signing_secret );

		foreach ( $signatures as $signature ) {
			if ( hash_equals( $expected, strtolower( $signature ) ) ) {
				return array(
					'valid'  => true,
					'scheme' => self::SCHEME_SIGNING_SECRET,
					'reason' => null,
				);
			}
		}

		return self::fail( 'Paystack-Signature does not match' );
	}

	/**
	 * A failed verification.
	 *
	 * @param string $reason Why the request was refused. Never includes the body or a secret.
	 *
	 * @return array{valid: bool, scheme: string|null, reason: string|null}
	 */
	private static function fail( $reason ) {
		return array(
			'valid'  => false,
			'scheme' => null,
			'reason' => $reason,
		);
	}
}
