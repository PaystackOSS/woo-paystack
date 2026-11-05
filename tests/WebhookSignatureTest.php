<?php
/**
 * Tests for WC_Paystack_Webhook_Signature.
 *
 * The headers below were produced by Paystack's own signer (`t=<t>,v1=<key id>:<hex>`, each hex an
 * HMAC-SHA512 over `<t>.<body>` keyed with the whole `whsec_` string), not by this plugin, so a
 * mistake shared by the verifier and the test cannot hide.
 */

use PHPUnit\Framework\TestCase;

class WebhookSignatureTest extends TestCase {

	const BODY = '{"event":"charge.success","data":{"reference":"123_1730000000","amount":500000,"currency":"NGN","domain":"test"}}';

	const T = 1793894400;

	const SECRET_A = 'whsec_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

	const SECRET_B = 'whsec_bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb';

	const SK = 'sk_test_cccccccccccccccccccccccccccccccccccccccc';

	const RK = 'rk_test_cccccccccccccccccccccccccccccccccccccccc';

	/** Signed with SECRET_A only. */
	const HEADER_ONE = 't=1793894400,v1=whk_0a1b2c3d:6e77742cdfba46b095cd0abab9b2b6531c7729010a1a6312cc155850aaa790a6e0bfc11c4d6139539639606184c5f0e4fc2df4296c5f2d8afa0ec375bbb616de';

	/** Signed with SECRET_A then SECRET_B, as during a rotation. */
	const HEADER_TWO = 't=1793894400,v1=whk_0a1b2c3d:6e77742cdfba46b095cd0abab9b2b6531c7729010a1a6312cc155850aaa790a6e0bfc11c4d6139539639606184c5f0e4fc2df4296c5f2d8afa0ec375bbb616de,v1=whk_9f8e7d6c:0df9a5a38ecd42e03c8ddbe18314fc4075117a1e7901870a46adf0275cd901b358989be7e2eca3809d5aaeda8f655084da2f9f9d6d4dc83ff84a1e78758e63e4';

	/** X-Paystack-Signature: HMAC-SHA512 over the body keyed with SK. */
	const LEGACY = '63dabf32a3e7dcaa497075bc5cf0fe5e39aa2b844c01972c533670b1880c5a1f11fa508f9c6d4e27a54e961a53e1943a19c84c89545533440acad09898fb23d8';

	private static function verify( $paystack_signature, $x_paystack_signature, $signing_secret, $api_key, $now = self::T, $body = self::BODY ) {
		return WC_Paystack_Webhook_Signature::verify( $body, $paystack_signature, $x_paystack_signature, $signing_secret, $api_key, $now );
	}

	private static function refused( $reason ) {
		return array(
			'valid'  => false,
			'scheme' => null,
			'reason' => $reason,
		);
	}

	private static function accepted( $scheme ) {
		return array(
			'valid'  => true,
			'scheme' => $scheme,
			'reason' => null,
		);
	}

	public function test_accepts_a_valid_signature_with_one_v1_entry() {
		$this->assertSame(
			self::accepted( WC_Paystack_Webhook_Signature::SCHEME_SIGNING_SECRET ),
			self::verify( self::HEADER_ONE, '', self::SECRET_A, self::RK )
		);
	}

	public function test_accepts_a_rotation_where_only_the_second_v1_entry_matches() {
		$this->assertSame(
			self::accepted( WC_Paystack_Webhook_Signature::SCHEME_SIGNING_SECRET ),
			self::verify( self::HEADER_TWO, '', self::SECRET_B, self::RK )
		);
	}

	public function test_accepts_a_timestamp_at_the_edge_of_the_tolerance() {
		$this->assertTrue( self::verify( self::HEADER_ONE, '', self::SECRET_A, self::RK, self::T + 300 )['valid'] );
		$this->assertTrue( self::verify( self::HEADER_ONE, '', self::SECRET_A, self::RK, self::T - 300 )['valid'] );
	}

	public function test_refuses_a_signature_from_another_secret() {
		$this->assertSame(
			self::refused( 'Paystack-Signature does not match' ),
			self::verify( self::HEADER_ONE, '', self::SECRET_B, self::RK )
		);
	}

	public function test_refuses_a_tampered_body() {
		$this->assertSame(
			self::refused( 'Paystack-Signature does not match' ),
			self::verify( self::HEADER_ONE, '', self::SECRET_A, self::RK, self::T, str_replace( '500000', '500', self::BODY ) )
		);
	}

	public function test_refuses_a_stale_or_future_timestamp() {
		$reason = 'Paystack-Signature timestamp is outside the 300 second tolerance';

		$this->assertSame( self::refused( $reason ), self::verify( self::HEADER_ONE, '', self::SECRET_A, self::RK, self::T + 301 ) );
		$this->assertSame( self::refused( $reason ), self::verify( self::HEADER_ONE, '', self::SECRET_A, self::RK, self::T - 301 ) );
	}

	public function test_refuses_a_header_with_a_moved_timestamp() {
		$header = str_replace( 't=1793894400', 't=1793894401', self::HEADER_ONE );

		$this->assertSame(
			self::refused( 'Paystack-Signature does not match' ),
			self::verify( $header, '', self::SECRET_A, self::RK )
		);
	}

	/**
	 * @dataProvider malformed_headers
	 */
	public function test_refuses_a_malformed_header( $header ) {
		$this->assertSame(
			self::refused( 'malformed Paystack-Signature header' ),
			self::verify( $header, '', self::SECRET_A, self::RK )
		);
	}

	public function malformed_headers() {
		return array(
			'no timestamp'      => array( 'v1=whk_0a1b2c3d:6e77' ),
			'no v1 entry'       => array( 't=1793894400' ),
			'non-numeric t'     => array( 't=abc,v1=whk_0a1b2c3d:6e77' ),
			'not key=value'     => array( 'garbage' ),
			'legacy hex itself' => array( self::LEGACY ),
		);
	}

	public function test_refuses_a_missing_header_when_a_signing_secret_is_set() {
		$this->assertSame(
			self::refused( 'missing Paystack-Signature header' ),
			self::verify( '', '', self::SECRET_A, self::RK )
		);
	}

	public function test_ignores_a_valid_x_paystack_signature_when_a_signing_secret_is_set() {
		$this->assertSame(
			self::refused( 'missing Paystack-Signature header' ),
			self::verify( '', self::LEGACY, self::SECRET_A, self::SK )
		);
	}

	public function test_accepts_x_paystack_signature_with_a_secret_key_and_no_signing_secret() {
		$this->assertSame(
			self::accepted( WC_Paystack_Webhook_Signature::SCHEME_SECRET_KEY ),
			self::verify( '', self::LEGACY, '', self::SK )
		);
	}

	public function test_refuses_a_wrong_x_paystack_signature() {
		$this->assertSame(
			self::refused( 'X-Paystack-Signature does not match' ),
			self::verify( '', str_repeat( '0', 128 ), '', self::SK )
		);
	}

	public function test_refuses_a_missing_x_paystack_signature_with_a_secret_key() {
		$this->assertSame(
			self::refused( 'missing X-Paystack-Signature header' ),
			self::verify( '', '', '', self::SK )
		);
	}

	public function test_refuses_every_webhook_with_a_restricted_key_and_no_signing_secret() {
		$reason = 'no webhook signing secret is configured, and the API key is not a secret key';

		// A restricted key never signs anything, so even an HMAC computed with it is refused.
		$signed_with_rk = hash_hmac( 'sha512', self::BODY, self::RK );

		$this->assertSame( self::refused( $reason ), self::verify( '', $signed_with_rk, '', self::RK ) );
		$this->assertSame( self::refused( $reason ), self::verify( self::HEADER_ONE, '', '', self::RK ) );
	}
}
