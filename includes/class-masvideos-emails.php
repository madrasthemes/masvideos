<?php
/**
 * Transactional Emails Controller
 *
 * MasVideos Emails Class which handles the sending on transactional emails and email templates. This class loads in available emails.
 *
 * @package MasVideos/Classes/Emails
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * Emails class.
 */
class MasVideos_Emails {

	/**
	 * Array of email notification classes
	 *
	 * @var MasVideos_Email[]
	 */
	public $emails = array();

	/**
	 * The single instance of the class
	 *
	 * @var MasVideos_Emails
	 */
	protected static $_instance = null;

	/**
	 * Background emailer class.
	 *
	 * @var MasVideos_Background_Emailer
	 */
	protected static $background_emailer = null;

	/**
	 * Main MasVideos_Emails Instance.
	 *
	 * Ensures only one instance of MasVideos_Emails is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return MasVideos_Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'masvideos' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'masvideos' ), '1.0.0' );
	}

	/**
	 * Hook in all transactional emails.
	 */
	public static function init_transactional_emails() {
		$email_actions = apply_filters(
			'masvideos_email_actions',
			array(
				'masvideos_created_user',
			)
		);

		if ( apply_filters( 'masvideos_defer_transactional_emails', false ) ) {
			// self::$background_emailer = new MasVideos_Background_Emailer();

			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'queue_transactional_email' ), 10, 10 );
			}
		} else {
			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'send_transactional_email' ), 10, 10 );
			}
		}
	}

	/**
	 * Queues transactional email so it's not sent in current request if enabled,
	 * otherwise falls back to send now.
	 *
	 * @param mixed ...$args Optional arguments.
	 */
	public static function queue_transactional_email( ...$args ) {
		// if ( is_a( self::$background_emailer, 'MasVideos_Background_Emailer' ) ) {
		// 	self::$background_emailer->push_to_queue(
		// 		array(
		// 			'filter' => current_filter(),
		// 			'args'   => func_get_args(),
		// 		)
		// 	);
		// } else {
			self::send_transactional_email( ...$args );
		// }
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 *
	 * @internal
	 *
	 * @param string $filter Filter name.
	 * @param array  $args Email args (default: []).
	 */
	public static function send_queued_transactional_email( $filter = '', $args = array() ) {
		if ( apply_filters( 'masvideos_allow_send_queued_transactional_email', true, $filter, $args ) ) {
			self::instance(); // Init self so emails exist.

			do_action_ref_array( $filter . '_notification', $args );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 *
	 * @internal
	 *
	 * @param array $args Email args (default: []).
	 */
	public static function send_transactional_email( $args = array() ) {
		try {
			$args = func_get_args();
			self::instance(); // Init self so emails exist.
			do_action_ref_array( current_filter() . '_notification', $args );
		} catch ( Exception $e ) {
			$error  = 'Transactional email triggered fatal error for callback ' . current_filter();
			// $logger = wc_get_logger();
			// $logger->critical(
			// 	$error . PHP_EOL,
			// 	array(
			// 		'source' => 'transactional-emails',
			// 	)
			// );
			if ( Constants::is_true( 'WP_DEBUG' ) ) {
				trigger_error( $error, E_USER_WARNING ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			}
		}
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 */
	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks.
		add_action( 'masvideos_email_header', array( $this, 'email_header' ) );
		add_action( 'masvideos_email_footer', array( $this, 'email_footer' ) );

		// Hooks for sending emails during store events.
		add_action( 'masvideos_created_user_notification', array( $this, 'user_new_account' ), 10, 3 );

		// Hook for replacing {site_title} in email-footer.
		add_filter( 'masvideos_email_footer_text', array( $this, 'replace_placeholders' ) );

		// Let 3rd parties unhook the above via this hook.
		do_action( 'masvideos_email', $this );
	}

	/**
	 * Init email classes.
	 */
	public function init() {
		// Include email classes.
		include_once dirname( __FILE__ ) . '/emails/class-masvideos-email.php';

		$this->emails['MasVideos_Email_User_Reset_Password']   = include 'emails/class-masvideos-email-user-reset-password.php';
		$this->emails['MasVideos_Email_User_New_Account']      = include 'emails/class-masvideos-email-user-new-account.php';

		$this->emails = apply_filters( 'masvideos_email_classes', $this->emails );
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 *
	 * @return MasVideos_Email[]
	 */
	public function get_emails() {
		return $this->emails;
	}

	/**
	 * Get from name for email.
	 *
	 * @return string
	 */
	public function get_from_name() {
		return wp_specialchars_decode( get_option( 'masvideos_email_from_name' ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 *
	 * @return string
	 */
	public function get_from_address() {
		return sanitize_email( get_option( 'masvideos_email_from_address' ) );
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading Heading for the email.
	 */
	public function email_header( $email_heading ) {
		masvideos_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		masvideos_get_template( 'emails/email-footer.php' );
	}

	/**
	 * Replace placeholder text in strings.
	 *
	 * @since  3.7.0
	 * @param  string $string Email footer text.
	 * @return string         Email footer text with any replacements done.
	 */
	public function replace_placeholders( $string ) {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );

		return str_replace(
			array(
				'{site_title}',
				'{site_address}',
				'{site_url}',
				'{masvideos}',
				'{MasVideos}',
			),
			array(
				$this->get_blogname(),
				$domain,
				$domain,
				'<a href="https://wordpress.org/plugins/masvideos">MasVideos</a>',
				'<a href="https://wordpress.org/plugins/masvideos">MasVideos</a>',
			),
			$string
		);
	}

	/**
	 * Wraps a message in the masvideos mail template.
	 *
	 * @param string $email_heading Heading text.
	 * @param string $message       Email message.
	 * @param bool   $plain_text    Set true to send as plain text. Default to false.
	 *
	 * @return string
	 */
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer.
		ob_start();

		do_action( 'masvideos_email_header', $email_heading, null );

		echo wpautop( wptexturize( $message ) ); // WPCS: XSS ok.

		do_action( 'masvideos_email_footer', null );

		// Get contents.
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Send the email.
	 *
	 * @param mixed  $to          Receiver.
	 * @param mixed  $subject     Email subject.
	 * @param mixed  $message     Message.
	 * @param string $headers     Email headers (default: "Content-Type: text/html\r\n").
	 * @param string $attachments Attachments (default: "").
	 * @return bool
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
		// Send.
		$email = new MasVideos_Email();
		return $email->send( $to, $subject, $message, $headers, $attachments );
	}

	/**
	 * User new account welcome email.
	 *
	 * @param int   $user_id        User ID.
	 * @param array $new_user_data  New user data.
	 * @param bool  $password_generated If password is generated.
	 */
	public function user_new_account( $user_id, $new_user_data = array(), $password_generated = false ) {
		if ( ! $user_id ) {
			return;
		}

		$user_pass = ! empty( $new_user_data['user_pass'] ) ? $new_user_data['user_pass'] : '';

		$email = $this->emails['MasVideos_Email_User_New_Account'];
		$email->trigger( $user_id, $user_pass, $password_generated );
	}

	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	private function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
}
