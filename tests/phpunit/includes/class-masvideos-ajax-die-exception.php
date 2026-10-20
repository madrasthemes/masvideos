<?php
/**
 * Throwable used to intercept wp_die()/die in AJAX handlers under test.
 *
 * AJAX handlers end with wp_send_json*()/wp_die(); in a real AJAX request wp_die() calls die(),
 * which cannot be caught by a handler's own `try { ... } catch ( Exception $e )`. To reproduce
 * that faithfully in tests (and capture the JSON a handler emitted) the AJAX die handler throws
 * this instead of calling die().
 *
 * It extends Error (not Exception) on purpose: several handlers wrap their success path in
 * `try { ... wp_send_json_success(); } catch ( Exception $e ) { wp_send_json_error(); }`. If the
 * interception were an Exception it would be swallowed by that catch and the handler would then
 * emit a SECOND (error) JSON envelope — which never happens in production, where die() is final.
 *
 * @package MasVideos/Tests
 */

defined( 'ABSPATH' ) || exit;

class MasVideos_Ajax_Die_Exception extends Error {
}
