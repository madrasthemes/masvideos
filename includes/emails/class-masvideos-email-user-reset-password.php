<?php
/**
 * Class MasVideos_Email_User_Reset_Password file.
 *
 * @package MasVideos\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'MasVideos_Email_User_Reset_Password', false ) ) :

	/**
	 * User Reset Password.
	 *
	 * An email sent to the user when they reset their password.
	 *
	 * @class       MasVideos_Email_User_Reset_Password
	 * @version     1.0.0
	 * @package     MasVideos/Classes/Emails
	 * @extends     MasVideos_Email
	 */
	class MasVideos_Email_User_Reset_Password extends MasVideos_Email {

		/**
		 * User ID.
		 *
		 * @var integer
		 */
		public $user_id;

		/**
		 * User login name.
		 *
		 * @var string
		 */
		public $user_login;

		/**
		 * User email.
		 *
		 * @var string
		 */
		public $user_email;

		/**
		 * Reset key.
		 *
		 * @var string
		 */
		public $reset_key;

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id             = 'user_reset_password';
			$this->user_email = true;

			$this->title       = __( 'Reset password', 'masvideos' );
			$this->description = __( 'User "reset password" emails are sent when users reset their passwords.', 'masvideos' );

			$this->template_html  = 'emails/user-reset-password.php';
			$this->template_plain = 'emails/plain/user-reset-password.php';

			// Trigger.
			add_action( 'masvideos_reset_password_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Password Reset Request for {site_title}', 'masvideos' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Password Reset Request', 'masvideos' );
		}

		/**
		 * Trigger.
		 *
		 * @param string $user_login User login.
		 * @param string $reset_key Password reset key.
		 */
		public function trigger( $user_login = '', $reset_key = '' ) {
			$this->setup_locale();

			if ( $user_login && $reset_key ) {
				$this->object     = get_user_by( 'login', $user_login );
				$this->user_id    = $this->object->ID;
				$this->user_login = $user_login;
				$this->reset_key  = $reset_key;
				$this->user_email = stripslashes( $this->object->user_email );
				$this->recipient  = $this->user_email;
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return masvideos_get_template_html(
				$this->template_html,
				array(
					'email_heading'      => $this->get_heading(),
					'user_id'            => $this->user_id,
					'user_login'         => $this->user_login,
					'reset_key'          => $this->reset_key,
					'blogname'           => $this->get_blogname(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
				)
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return masvideos_get_template_html(
				$this->template_plain,
				array(
					'email_heading'      => $this->get_heading(),
					'user_id'            => $this->user_id,
					'user_login'         => $this->user_login,
					'reset_key'          => $this->reset_key,
					'blogname'           => $this->get_blogname(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
				)
			);
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.7.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for reading.', 'masvideos' );
		}
	}

endif;

return new MasVideos_Email_User_Reset_Password();
