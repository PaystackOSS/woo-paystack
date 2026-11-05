<?php
/**
 * PHPUnit bootstrap. Loads only the classes that run without WordPress.
 */

define( 'ABSPATH', __DIR__ . '/' );

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
require_once dirname( __DIR__ ) . '/includes/class-wc-paystack-webhook-signature.php';
