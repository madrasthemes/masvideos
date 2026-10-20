<?php
/**
 * Class MasVideos_Email file.
 *
 * @package MasVideos\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MasVideos_Email', false ) ) {
	return;
}

/**
 * Email Class
 *
 * MasVideos Email Class which is extended by specific email template classes to add emails to MasVideos
 *
 * @class       MasVideos_Email
 * @version     1.0.0
 * @package     MasVideos/Classes/Emails
 * @extends     MasVideos_Settings_API
 */
class MasVideos_Email {

	/**
	 * Email method ID.
	 *
	 * @var String
	 */
	public $id;

	/**
	 * Email method title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * 'yes' if the method is enabled.
	 *
	 * @var string yes, no
	 */
	public $enabled;

	/**
	 * Description for the email.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Default heading.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @var string
	 */
	public $heading = '';

	/**
	 * Default subject.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @var string
	 */
	public $subject = '';

	/**
	 * Plain text template path.
	 *
	 * @var string
	 */
	public $template_plain;

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;

	/**
	 * Template path.
	 *
	 * @var string
	 */
	public $template_base;

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient;

	/**
	 * Object this email is for, for example a user, product, or email.
	 *
	 * @var object|bool
	 */
	public $object;

	/**
	 * Mime boundary (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary;

	/**
	 * Mime boundary header (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary_header;

	/**
	 * True when email is being sent.
	 *
	 * @var bool
	 */
	public $sending;

	/**
	 * True when the email notification is sent manually only.
	 *
	 * @var bool
	 */
	protected $manual = false;

	/**
	 * True when the email notification is sent to users.
	 *
	 * @var bool
	 */
	protected $user_email = false;

	/**
	 *  List of preg* regular expression patterns to search for,
	 *  used in conjunction with $plain_replace.
	 *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	 *
	 *  @var array $plain_search
	 *  @see $plain_replace
	 */
	public $plain_search = array(
		"/\r/",                                                  // Non-legal carriage return.
		'/&(nbsp|#0*160);/i',                                    // Non-breaking space.
		'/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i', // Double quotes.
		'/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',               // Single quotes.
		'/&gt;/i',                                               // Greater-than.
		'/&lt;/i',                                               // Less-than.
		'/&#0*38;/i',                                            // Ampersand.
		'/&amp;/i',                                              // Ampersand.
		'/&(copy|#0*169);/i',                                    // Copyright.
		'/&(trade|#0*8482|#0*153);/i',                           // Trademark.
		'/&(reg|#0*174);/i',                                     // Registered.
		'/&(mdash|#0*151|#0*8212);/i',                           // mdash.
		'/&(ndash|minus|#0*8211|#0*8722);/i',                    // ndash.
		'/&(bull|#0*149|#0*8226);/i',                            // Bullet.
		'/&(pound|#0*163);/i',                                   // Pound sign.
		'/&(euro|#0*8364);/i',                                   // Euro sign.
		'/&(dollar|#0*36);/i',                                   // Dollar sign.
		'/&[^&\s;]+;/i',                                         // Unknown/unhandled entities.
		'/[ ]{2,}/',                                             // Runs of spaces, post-handling.
	);

	/**
	 *  List of pattern replacements corresponding to patterns searched.
	 *
	 *  @var array $plain_replace
	 *  @see $plain_search
	 */
	public $plain_replace = array(
		'',                                             // Non-legal carriage return.
		' ',                                            // Non-breaking space.
		'"',                                            // Double quotes.
		"'",                                            // Single quotes.
		'>',                                            // Greater-than.
		'<',                                            // Less-than.
		'&',                                            // Ampersand.
		'&',                                            // Ampersand.
		'(c)',                                          // Copyright.
		'(tm)',                                         // Trademark.
		'(R)',                                          // Registered.
		'--',                                           // mdash.
		'-',                                            // ndash.
		'*',                                            // Bullet.
		'£',                                            // Pound sign.
		'EUR',                                          // Euro sign. € ?.
		'$',                                            // Dollar sign.
		'',                                             // Unknown/unhandled entities.
		' ',                                             // Runs of spaces, post-handling.
	);

	/**
	 * Strings to find/replace in subjects/headings.
	 *
	 * @var array
	 */
	protected $placeholders = array();

	/**
	 * Strings to find in subjects/headings.
	 *
	 * @deprecated 3.2.0 in favour of placeholders
	 * @var array
	 */
	public $find = array();

	/**
	 * Strings to replace in subjects/headings.
	 *
	 * @deprecated 3.2.0 in favour of placeholders
	 * @var array
	 */
	public $replace = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Find/replace.
		$this->placeholders = array_merge(
			array(
				'{site_title}'   => $this->get_blogname(),
				'{site_address}' => wp_parse_url( home_url(), PHP_URL_HOST ),
				'{site_url}'     => wp_parse_url( home_url(), PHP_URL_HOST ),
			),
			$this->placeholders
		);

		// Default template base if not declared in child constructor.
		if ( is_null( $this->template_base ) ) {
			$this->template_base = MasVideos()->plugin_path() . '/templates/';
		}

		$this->email_type = $this->get_option( 'email_type' );
		$this->enabled    = $this->get_option( 'enabled' );

		add_action( 'phpmailer_init', array( $this, 'handle_multipart' ) );
	}

	/**
	 * Handle multipart mail.
	 *
	 * @param  PHPMailer $mailer PHPMailer object.
	 * @return PHPMailer
	 */
	public function handle_multipart( $mailer ) {
		if ( $this->sending && 'multipart' === $this->get_email_type() ) {
			$mailer->AltBody = wordwrap( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) )
			);
			$this->sending   = false;
		}
		return $mailer;
	}

	/**
	 * Format email string.
	 *
	 * @param mixed $string Text to replace placeholders in.
	 * @return string
	 */
	public function format_string( $string ) {
		$find    = array_keys( $this->placeholders );
		$replace = array_values( $this->placeholders );

		// If using legacy find replace, add those to our find/replace arrays first. @todo deprecate in 4.0.0.
		$find    = array_merge( (array) $this->find, $find );
		$replace = array_merge( (array) $this->replace, $replace );

		// Take care of blogname which is no longer defined as a valid placeholder.
		$find[]    = '{blogname}';
		$replace[] = $this->get_blogname();

		// If using the older style filters for find and replace, ensure the array is associative and then pass through filters. @todo deprecate in 4.0.0.
		if ( has_filter( 'masvideos_email_format_string_replace' ) || has_filter( 'masvideos_email_format_string_find' ) ) {
			$legacy_find    = $this->find;
			$legacy_replace = $this->replace;

			foreach ( $this->placeholders as $find => $replace ) {
				$legacy_key                    = sanitize_title( str_replace( '_', '-', trim( $find, '{}' ) ) );
				$legacy_find[ $legacy_key ]    = $find;
				$legacy_replace[ $legacy_key ] = $replace;
			}

			$string = str_replace( apply_filters( 'masvideos_email_format_string_find', $legacy_find, $this ), apply_filters( 'masvideos_email_format_string_replace', $legacy_replace, $this ), $string );
		}

		/**
		 * Filter for main find/replace.
		 *
		 */
		return apply_filters( 'masvideos_email_format_string', str_replace( $find, $replace, $string ), $this );
	}

	/**
	 * Set the locale to the store locale for user emails to make sure emails are in the store language.
	 */
	public function setup_locale() {
		if ( $this->is_user_email() && apply_filters( 'masvideos_email_setup_locale', true ) ) {
			masvideos_switch_to_site_locale();
		}
	}

	/**
	 * Restore the locale to the default locale. Use after finished with setup_locale.
	 */
	public function restore_locale() {
		if ( $this->is_user_email() && apply_filters( 'masvideos_email_restore_locale', true ) ) {
			masvideos_restore_locale();
		}
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return $this->subject;
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return $this->heading;
	}

	/**
	 * Default content to show below main email content.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_default_additional_content() {
		return '';
	}

	/**
	 * Return content from the additional_content field.
	 *
	 * Displayed above the footer.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_additional_content() {
		$content = $this->get_option( 'additional_content', '' );

		return apply_filters( 'masvideos_email_additional_content_' . $this->id, $this->format_string( $content ), $this->object, $this );
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return apply_filters( 'masvideos_email_subject_' . $this->id, $this->format_string( $this->get_option( 'subject', $this->get_default_subject() ) ), $this->object, $this );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_heading() {
		return apply_filters( 'masvideos_email_heading_' . $this->id, $this->format_string( $this->get_option( 'heading', $this->get_default_heading() ) ), $this->object, $this );
	}

	/**
	 * Get valid recipients.
	 *
	 * @return string
	 */
	public function get_recipient() {
		$recipient  = apply_filters( 'masvideos_email_recipient_' . $this->id, $this->recipient, $this->object, $this );
		$recipients = array_map( 'trim', explode( ',', $recipient ) );
		$recipients = array_filter( $recipients, 'is_email' );
		return implode( ', ', $recipients );
	}

	/**
	 * Get email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

		if ( $this->get_from_address() && $this->get_from_name() ) {
			$header .= 'Reply-to: ' . $this->get_from_name() . ' <' . $this->get_from_address() . ">\r\n";
		}

		return apply_filters( 'masvideos_email_headers', $header, $this->id, $this->object, $this );
	}

	/**
	 * Get email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {
		return apply_filters( 'masvideos_email_attachments', array(), $this->id, $this->object, $this );
	}

	/**
	 * Return email type.
	 *
	 * @return string
	 */
	public function get_email_type() {
		return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
	}

	/**
	 * Get email content type.
	 *
	 * @param string $default_content_type Default wp_mail() content type.
	 * @return string
	 */
	public function get_content_type( $default_content_type = '' ) {
		switch ( $this->get_email_type() ) {
			case 'html':
				$content_type = 'text/html';
				break;
			case 'multipart':
				$content_type = 'multipart/alternative';
				break;
			default:
				$content_type = 'text/plain';
				break;
		}

		return apply_filters( 'masvideos_email_content_type', $content_type, $this, $default_content_type );
	}

	/**
	 * Return the email's title
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'masvideos_email_title', $this->title, $this );
	}

	/**
	 * Return the email's description
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'masvideos_email_description', $this->description, $this );
	}

	/**
	 * Proxy to parent's get_option and attempt to localize the result using gettext.
	 *
	 * @param string $key Option key.
	 * @param mixed  $empty_value Value to use when option is empty.
	 * @return string
	 */
	public function get_option( $key, $empty_value = null ) {
		$option_name = "masvideos_{$this->id}_{$key}";
		$value = get_option( $option_name, $empty_value );
		return apply_filters( 'masvideos_email_get_option', $value, $this, $value, $key, $empty_value );
	}

	/**
	 * Checks if this email is enabled and will be sent.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return apply_filters( 'masvideos_email_enabled_' . $this->id, 'yes' === $this->enabled, $this->object, $this );
	}

	/**
	 * Checks if this email is manually sent
	 *
	 * @return bool
	 */
	public function is_manual() {
		return $this->manual;
	}

	/**
	 * Checks if this email is user focussed.
	 *
	 * @return bool
	 */
	public function is_user_email() {
		return $this->user_email;
	}

	/**
	 * Get WordPress blog name.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Get email content.
	 *
	 * @return string
	 */
	public function get_content() {
		$this->sending = true;

		if ( 'plain' === $this->get_email_type() ) {
			$email_content = wordwrap( preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) ), 70 );
		} else {
			$email_content = $this->get_content_html();
		}

		return $email_content;
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
	 *
	 * @version 4.0.0
	 * @param string|null $content Content that will receive inline styles.
	 * @return string
	 */
	public function style_inline( $content ) {
		if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ), true ) ) {
			ob_start();
			masvideos_get_template( 'emails/email-styles.php' );
			$css = apply_filters( 'masvideos_email_styles', ob_get_clean(), $this );

			$emogrifier_class = 'Pelago\\Emogrifier';

			if ( $this->supports_emogrifier() && class_exists( $emogrifier_class ) ) {
				try {
					$emogrifier = new $emogrifier_class( $content, $css );

					do_action( 'masvideos_emogrifier', $emogrifier, $this );

					$content    = $emogrifier->emogrify();
					$html_prune = \Pelago\Emogrifier\HtmlProcessor\HtmlPruner::fromHtml( $content );
					$html_prune->removeElementsWithDisplayNone();
					$content    = $html_prune->render();
				} catch ( Exception $e ) {
					// $logger = wc_get_logger();
					// $logger->error( $e->getMessage(), array( 'source' => 'emogrifier' ) );
				}
			} else {
				$content = '<style type="text/css">' . $css . '</style>' . $content;
			}
		}

		return $content;
	}

	/**
	 * Return if emogrifier library is supported.
	 *
	 * @version 4.0.0
	 * @since 3.5.0
	 * @return bool
	 */
	protected function supports_emogrifier() {
		return class_exists( 'DOMDocument' );
	}

	/**
	 * Get the email content in plain text format.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return '';
	}

	/**
	 * Get the email content in HTML format.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return '';
	}

	/**
	 * Get the from name for outgoing emails.
	 *
	 * @param string $from_name Default wp_mail() name associated with the "from" email address.
	 * @return string
	 */
	public function get_from_name( $from_name = '' ) {
		$from_name = apply_filters( 'masvideos_email_from_name', get_option( 'masvideos_email_from_name' ), $this, $from_name );
		return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @param string $from_email Default wp_mail() email address to send from.
	 * @return string
	 */
	public function get_from_address( $from_email = '' ) {
		$from_email = apply_filters( 'masvideos_email_from_address', get_option( 'masvideos_email_from_address' ), $this, $from_email );
		return sanitize_email( $from_email );
	}

	/**
	 * Send an email.
	 *
	 * @param string $to Email to.
	 * @param string $subject Email subject.
	 * @param string $message Email message.
	 * @param string $headers Email headers.
	 * @param array  $attachments Email attachments.
	 * @return bool success
	 */
	public function send( $to, $subject, $message, $headers, $attachments ) {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$message              = apply_filters( 'masvideos_mail_content', $this->style_inline( $message ) );
		$mail_callback        = apply_filters( 'masvideos_mail_callback', 'wp_mail', $this );
		$mail_callback_params = apply_filters( 'masvideos_mail_callback_params', array( $to, $subject, $message, $headers, $attachments ), $this );
		$return               = $mail_callback( ...$mail_callback_params );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		return $return;
	}

	/**
	 * Get template.
	 *
	 * @param  string $type Template type. Can be either 'template_html' or 'template_plain'.
	 * @return string
	 */
	public function get_template( $type ) {
		$type = basename( $type );

		if ( 'template_html' === $type ) {
			return $this->template_html;
		} elseif ( 'template_plain' === $type ) {
			return $this->template_plain;
		}
		return '';
	}

	/**
	 * Resolve a template path and confirm it is contained within an allowed base directory.
	 *
	 * Containment is verified on canonical, symlink-resolved paths (via realpath()) using a
	 * separator-aware comparison, so symlinked files, symlinked parent directories, sibling-prefix
	 * directories (e.g. "masvideos-evil" next to "masvideos"), traversal sequences, stream wrappers
	 * and malformed paths are all rejected. It fails closed: if either canonical path cannot be
	 * resolved, it returns false and the caller must abort rather than fall back to a string check.
	 *
	 * For a not-yet-created destination the parent directory is resolved canonically and the
	 * traversal-free basename appended, so new theme-override files can still be created safely.
	 *
	 * @since 1.3.5
	 * @param string $file     Absolute path to the template file (may or may not exist yet).
	 * @param string $base_dir Absolute path to the allowed base directory.
	 * @return string|false Canonical, normalized path when contained; false otherwise.
	 */
	protected function resolve_contained_template_path( $file, $base_dir ) {
		$file     = (string) $file;
		$base_dir = (string) $base_dir;

		if ( '' === $file || '' === $base_dir ) {
			return false;
		}

		// Reject NUL bytes, stream wrappers and other obviously malformed destinations outright.
		if ( false !== strpos( $file, "\0" ) || preg_match( '#^[a-zA-Z][a-zA-Z0-9+.\-]*://#', $file ) || false !== strpos( $file, '://' ) ) {
			return false;
		}

		// Outer trust anchor: the active theme directory. Resolving containment against this (rather
		// than only against the template sub-directory) means a template directory that is itself a
		// symlink out of the theme — whether on disk or introduced via the masvideos_template_directory
		// / masvideos_template_path filters — is rejected instead of silently moving the boundary.
		$theme_real = realpath( get_stylesheet_directory() );
		if ( false === $theme_real ) {
			return false;
		}
		$theme_real = untrailingslashit( wp_normalize_path( $theme_real ) );

		// Canonical (symlink-resolved) boundary. Fail closed if it cannot be resolved, or if it
		// resolves outside the active theme.
		$base_real = realpath( $base_dir );
		if ( false === $base_real ) {
			return false;
		}
		$base_real = untrailingslashit( wp_normalize_path( $base_real ) );
		if ( 0 !== strpos( $base_real . '/', $theme_real . '/' ) ) {
			return false;
		}

		// Canonical destination: resolve an existing file directly (following symlinks); for a
		// file to be created, resolve its parent and append the sanitized basename.
		if ( file_exists( $file ) ) {
			$target_real = realpath( $file );
		} else {
			$parent_real = realpath( dirname( $file ) );
			$target_real = ( false === $parent_real ) ? false : $parent_real . '/' . basename( $file );
		}
		if ( false === $target_real ) {
			return false;
		}
		$target_real = wp_normalize_path( $target_real );

		// Separator-aware containment: the target must sit strictly inside the boundary directory.
		if ( 0 !== strpos( $target_real, $base_real . '/' ) ) {
			return false;
		}

		return $target_real;
	}

	/**
	 * Confirm a (possibly not-yet-existing) destination can be created without escaping the active theme.
	 *
	 * resolve_contained_template_path() fails closed when a parent directory does not exist yet, which
	 * would block legitimate first-time creation of a theme email-override directory. This companion
	 * check is used *before* any wp_mkdir_p(): it rejects NUL bytes, stream wrappers, traversal and
	 * absolute escapes, then walks up to the nearest *existing* ancestor, resolves it canonically and
	 * requires it to live inside the canonical theme directory — so directory creation can never follow
	 * a symlinked ancestor out of the theme. The strict resolver is still run again after creation and
	 * immediately before the write.
	 *
	 * @since 1.3.5
	 * @param string $file Absolute destination path (may not exist yet).
	 * @return bool True when creation is safely contained within the active theme.
	 */
	protected function template_path_is_creatable_within_theme( $file ) {
		$file = (string) $file;

		if ( '' === $file || false !== strpos( $file, "\0" ) || preg_match( '#^[a-zA-Z][a-zA-Z0-9+.\-]*://#', $file ) || false !== strpos( $file, '://' ) ) {
			return false;
		}

		$theme_real = realpath( get_stylesheet_directory() );
		if ( false === $theme_real ) {
			return false;
		}
		$theme_real = untrailingslashit( wp_normalize_path( $theme_real ) );

		$file_n = wp_normalize_path( $file );

		// Reject any traversal component outright.
		if ( '..' === $file_n || 0 === strpos( $file_n, '../' ) || false !== strpos( $file_n, '/../' ) || '/..' === substr( $file_n, -3 ) ) {
			return false;
		}

		// Walk up to the nearest existing ancestor and resolve it canonically (follows symlinks).
		$probe = $file_n;
		while ( ! file_exists( $probe ) && dirname( $probe ) !== $probe ) {
			$probe = dirname( $probe );
		}
		$anchor = realpath( $probe );
		if ( false === $anchor ) {
			return false;
		}
		$anchor = untrailingslashit( wp_normalize_path( $anchor ) );

		// The nearest existing ancestor must be the theme directory itself or inside it: this is what
		// prevents wp_mkdir_p() from creating directories through a symlink that escapes the theme.
		if ( $anchor !== $theme_real && 0 !== strpos( $anchor . '/', $theme_real . '/' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Save the email templates.
	 *
	 * @since 2.4.0
	 * @param string $template_code Template code.
	 * @param string $template_path Template path.
	 */
	protected function save_template( $template_code, $template_path ) {
		if ( current_user_can( 'edit_themes' ) && ! empty( $template_code ) && ! empty( $template_path ) ) {
			$saved = false;
			$base  = get_stylesheet_directory() . '/' . MasVideos()->template_path() . $template_path;
			$code  = wp_unslash( $template_code );

			// Resolve the destination canonically and confirm it is contained within the theme's MAS
			// Videos template directory (symlink-, wrapper- and traversal-safe). The editor only ever
			// rewrites an existing override file, so a missing/unresolvable target fails closed here.
			$allowed_dir = get_stylesheet_directory() . '/' . MasVideos()->template_path();
			$file        = $this->resolve_contained_template_path( $base, $allowed_dir );

			if ( $file && file_exists( $file ) ) {
				// Write through WP_Filesystem (direct method only; no credential prompt). To avoid
				// truncating the live template on a failed/partial write, the new content is written to
				// a temporary file in the same directory and then moved into place; the original is only
				// replaced once the full write has succeeded. Existing permissions are carried over.
				$wp_filesystem = masvideos_wp_filesystem();
				if ( $wp_filesystem && $wp_filesystem->is_writable( $file ) ) {
					$dir   = dirname( $file );
					$perms = $wp_filesystem->getchmod( $file );
					$mode  = ( is_string( $perms ) && '' !== $perms && '0' !== $perms ) ? intval( $perms, 8 ) : false;
					$tmp   = $dir . '/masvideos-email-tmpl-' . wp_generate_password( 12, false ) . '.tmp';

					if ( $wp_filesystem->put_contents( $tmp, $code, $mode ) ) {
						// move() with overwrite swaps the fully-written temp file over the original; on the
						// same filesystem this is a rename, so readers never see a partially written file.
						if ( $wp_filesystem->move( $tmp, $file, true ) ) {
							$saved = true;
						} else {
							$wp_filesystem->delete( $tmp );
						}
					} elseif ( $wp_filesystem->exists( $tmp ) ) {
						$wp_filesystem->delete( $tmp );
					}
				}
			}

			if ( ! $saved ) {
				// Pass a known error code (not free text); the reader maps it to a fixed message (MV-16).
				$redirect = add_query_arg( 'masvideos_error', 'template_file_not_writable' );
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}

	/**
	 * Get the template file in the current theme.
	 *
	 * @param  string $template Template name.
	 *
	 * @return string
	 */
	public function get_theme_template_file( $template ) {
		return get_stylesheet_directory() . '/' . apply_filters( 'masvideos_template_directory', 'masvideos', $template ) . '/' . $template;
	}

	/**
	 * Move template action.
	 *
	 * @param string $template_type Template type.
	 */
	protected function move_template_action( $template_type ) {
		if ( ! current_user_can( 'edit_themes' ) ) {
			return;
		}
		$template = $this->get_template( $template_type );
		if ( ! empty( $template ) ) {
			$theme_file  = $this->get_theme_template_file( $template );
			$allowed_dir = get_stylesheet_directory() . '/' . apply_filters( 'masvideos_template_directory', 'masvideos', $template );

			// Validate the destination boundary BEFORE creating any directory, so a traversal/symlink/
			// filter-escaped path can never cause wp_mkdir_p() to create folders outside the theme.
			if ( ! $this->template_path_is_creatable_within_theme( $theme_file ) ) {
				return;
			}

			if ( wp_mkdir_p( dirname( $theme_file ) ) && ! file_exists( $theme_file ) ) {

				// Re-validate the full canonical path now that the directory exists and immediately
				// before the copy (strict symlink-/separator-aware containment within the theme dir).
				$dest = $this->resolve_contained_template_path( $theme_file, $allowed_dir );
				if ( ! $dest ) {
					return;
				}

				// Locate template file.
				$core_file     = $this->template_base . $template;
				$template_file = apply_filters( 'masvideos_locate_core_template', $core_file, $template, $this->template_base, $this->id );

				// Copy the core template into the theme through WP_Filesystem (no raw PHP writes).
				$wp_filesystem = masvideos_wp_filesystem();
				if ( $wp_filesystem && $wp_filesystem->exists( $template_file ) && $wp_filesystem->copy( $template_file, $dest, false, FS_CHMOD_FILE ) ) {

					/**
					 * Action hook fired after copying email template file.
					 *
					 * @param string $template_type The copied template type
					 * @param string $email The email object
					 */
					do_action( 'masvideos_copy_email_template', $template_type, $this );

					?>
					<div class="updated">
						<p><?php echo esc_html__( 'Template file copied to theme.', 'masvideos' ); ?></p>
					</div>
					<?php
				}
			}
		}
	}

	/**
	 * Delete template action.
	 *
	 * @param string $template_type Template type.
	 */
	protected function delete_template_action( $template_type ) {
		if ( ! current_user_can( 'edit_themes' ) ) {
			return;
		}
		$template = $this->get_template( $template_type );

		if ( $template ) {
			if ( ! empty( $template ) ) {
				$theme_file  = $this->get_theme_template_file( $template );
				$allowed_dir = get_stylesheet_directory() . '/' . apply_filters( 'masvideos_template_directory', 'masvideos', $template );

				// Only delete a file that canonically resolves inside the theme template directory
				// (rejects symlink escapes and traversal); the resolved path is what gets deleted.
				$target = $this->resolve_contained_template_path( $theme_file, $allowed_dir );

				if ( $target && file_exists( $target ) ) {
					wp_delete_file( $target );

					/**
					 * Action hook fired after deleting template file.
					 *
					 * @param string $template The deleted template type
					 * @param string $email The email object
					 */
					do_action( 'masvideos_delete_email_template', $template_type, $this );
					?>
					<div class="updated">
						<p><?php echo esc_html__( 'Template file deleted from theme.', 'masvideos' ); ?></p>
					</div>
					<?php
				}
			}
		}
	}
}
