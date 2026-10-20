<?php
/**
 * Exception used to intercept wp_redirect()/exit in handlers under test.
 *
 * The production handlers call wp_safe_redirect( ... ) then exit on success. During
 * tests we hook the `wp_redirect` filter and throw this to unwind the call before
 * header()/exit run, so we can assert on the redirect target without terminating the
 * PHPUnit process and WITHOUT changing production security behaviour.
 *
 * It extends Error (not Exception) on purpose: several handlers perform their success
 * redirect inside a `try { ... } catch ( Exception $e )` block. Extending Error means
 * this interception is NOT swallowed by those catch blocks, so it faithfully reaches
 * the test, while production behaviour is completely untouched.
 *
 * @package MasVideos/Tests
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Redirect_Exception extends Error {

	/**
	 * The redirect location captured from the handler.
	 *
	 * @var string
	 */
	public $location;

	/**
	 * Constructor.
	 *
	 * @param string $location Redirect URL.
	 */
	public function __construct( $location ) {
		$this->location = (string) $location;
		parent::__construct( 'Intercepted redirect to: ' . $this->location );
	}
}
