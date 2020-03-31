<?php
/**
 * Adds settings to the permalinks admin settings page
 *
 * @class       MasVideos_Admin_Permalink_Settings
 * @category    Admin
 * @package     MasVideos/Admin
 * @version     1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'MasVideos_Admin_Permalink_Settings', false ) ) {
	return new MasVideos_Admin_Permalink_Settings();
}

/**
 * MasVideos_Admin_Permalink_Settings Class.
 */
class MasVideos_Admin_Permalink_Settings {

	/**
	 * Permalink settings.
	 *
	 * @var array
	 */
	private $permalinks = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		$this->settings_init();
		$this->settings_save();
	}

	/**
	 * Init our settings.
	 */
	public function settings_init() {
		add_settings_section( 'masvideos-permalink', '', array( $this, 'settings' ), 'permalink' );

		add_settings_field(
			'masvideos_movie_genre_slug',
			__( 'Movie genre base', 'masvideos' ),
			array( $this, 'movie_genre_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_movie_tag_slug',
			__( 'Movie tag base', 'masvideos' ),
			array( $this, 'movie_tag_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_movie_attribute_slug',
			__( 'Movie attribute base', 'masvideos' ),
			array( $this, 'movie_attribute_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_video_category_slug',
			__( 'Video category base', 'masvideos' ),
			array( $this, 'video_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_video_tag_slug',
			__( 'Video tag base', 'masvideos' ),
			array( $this, 'video_tag_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_video_attribute_slug',
			__( 'Video attribute base', 'masvideos' ),
			array( $this, 'video_attribute_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_tv_show_genre_slug',
			__( 'TV Show genre base', 'masvideos' ),
			array( $this, 'tv_show_genre_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_tv_show_tag_slug',
			__( 'TV Show tag base', 'masvideos' ),
			array( $this, 'tv_show_tag_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_tv_show_attribute_slug',
			__( 'TV Show attribute base', 'masvideos' ),
			array( $this, 'tv_show_attribute_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_person_category_slug',
			__( 'Person category base', 'masvideos' ),
			array( $this, 'person_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_person_tag_slug',
			__( 'Person tag base', 'masvideos' ),
			array( $this, 'person_tag_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'masvideos_person_attribute_slug',
			__( 'Person attribute base', 'masvideos' ),
			array( $this, 'person_attribute_slug_input' ),
			'permalink',
			'optional'
		);

		$this->permalinks = masvideos_get_permalink_structure();
	}

	/**
	 * Show a slug input box.
	 */
	public function movie_genre_slug_input() {
		?>
		<input name="masvideos_movie_genre_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['movie_genre_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'movie-genre', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function movie_tag_slug_input() {
		?>
		<input name="masvideos_movie_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['movie_tag_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'movie-tag', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function movie_attribute_slug_input() {
		?>
		<input name="masvideos_movie_attribute_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['movie_attribute_base'] ); ?>" /><code>/attribute-name/attribute/</code>
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function video_category_slug_input() {
		?>
		<input name="masvideos_video_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['video_category_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'video-category', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function video_tag_slug_input() {
		?>
		<input name="masvideos_video_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['video_tag_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'video-tag', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function video_attribute_slug_input() {
		?>
		<input name="masvideos_video_attribute_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['video_attribute_base'] ); ?>" /><code>/attribute-name/attribute/</code>
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function tv_show_genre_slug_input() {
		?>
		<input name="masvideos_tv_show_genre_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['tv_show_genre_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'tv-show-genre', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function tv_show_tag_slug_input() {
		?>
		<input name="masvideos_tv_show_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['tv_show_tag_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'tv-show-tag', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function tv_show_attribute_slug_input() {
		?>
		<input name="masvideos_tv_show_attribute_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['tv_show_attribute_base'] ); ?>" /><code>/attribute-name/attribute/</code>
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function person_category_slug_input() {
		?>
		<input name="masvideos_person_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['person_category_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'person-category', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function person_tag_slug_input() {
		?>
		<input name="masvideos_person_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['person_tag_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'person-tag', 'slug', 'masvideos' ); ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function person_attribute_slug_input() {
		?>
		<input name="masvideos_person_attribute_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks['person_attribute_base'] ); ?>" /><code>/attribute-name/attribute/</code>
		<?php
	}

	/**
	 * Show the settings.
	 */
	public function settings() {
		echo '<h2>' . esc_html__( 'Movies Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your movie URLs here. For example, using <code>movie</code> would make your movie links like <code>%smovie/sample-movie/</code>. This setting affects movie URLs only, not things such as movie categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$movie_page_id = masvideos_get_page_id( 'movie' );
		$movie_base_slug    = urldecode( ( $movie_page_id > 0 && get_post( $movie_page_id ) ) ? get_page_uri( $movie_page_id ) : _x( 'movie', 'default-slug', 'masvideos' ) );
		$movie_base = _x( 'movie', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $movie_base_slug ),
			2 => '/' . trailingslashit( $movie_base_slug ) . trailingslashit( '%movie_genre%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th><label><input name="movie_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="masvideostog" <?php checked( $structures[0], $this->permalinks['movie_base'] ); ?> /> <?php esc_html_e( 'Default', 'masvideos' ); ?></label></th>
					<td><code class="default-example"><?php echo esc_html( home_url() ); ?>/?movie=sample-movie</code> <code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $movie_base ); ?>/sample-movie/</code></td>
				</tr>
				<?php if ( $movie_page_id ) : ?>
					<tr>
						<th><label><input name="movie_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="masvideostog" <?php checked( $structures[1], $this->permalinks['movie_base'] ); ?> /> <?php esc_html_e( 'Movie base', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $movie_base_slug ); ?>/sample-movie/</code></td>
					</tr>
					<tr>
						<th><label><input name="movie_permalink" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="masvideostog" <?php checked( $structures[2], $this->permalinks['movie_base'] ); ?> /> <?php esc_html_e( 'Movie base with genre', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $movie_base_slug ); ?>/movie-genre/sample-movie/</code></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><label><input name="movie_permalink" id="masvideos_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $this->permalinks['movie_base'], $structures, true ), false ); ?> />
						<?php esc_html_e( 'Custom base', 'masvideos' ); ?></label></th>
					<td>
						<input name="movie_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['movie_base'] ? trailingslashit( $this->permalinks['movie_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		echo '<h2>' . esc_html__( 'Movie Playlist Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your movie-playlist URLs here. For example, using <code>movie-playlist</code> would make your movie-playlist links like <code>%smovie-playlist/sample-movie-playlist/</code>. This setting affects movie-playlist URLs only, not things such as movie-playlist categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$movie_playlist_page_id = masvideos_get_page_id( 'movie-playlist' );
		$movie_playlist_base_slug    = urldecode( ( $movie_playlist_page_id > 0 && get_post( $movie_playlist_page_id ) ) ? get_page_uri( $movie_playlist_page_id ) : _x( 'movie-playlist', 'default-slug', 'masvideos' ) );
		$movie_playlist_base = _x( 'movie-playlist', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $movie_playlist_base_slug ),
			2 => '/' . trailingslashit( $movie_playlist_base_slug ) . trailingslashit( '%movie_playlist_cat%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Movie Playlist base', 'masvideos' ); ?></th>
					<td>
						<input name="movie_playlist_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['movie_playlist_base'] ? trailingslashit( $this->permalinks['movie_playlist_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		echo '<h2>' . esc_html__( 'Videos Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your video URLs here. For example, using <code>video</code> would make your video links like <code>%svideo/sample-video/</code>. This setting affects video URLs only, not things such as video categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$video_page_id = masvideos_get_page_id( 'video' );
		$video_base_slug    = urldecode( ( $video_page_id > 0 && get_post( $video_page_id ) ) ? get_page_uri( $video_page_id ) : _x( 'video', 'default-slug', 'masvideos' ) );
		$video_base = _x( 'video', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $video_base_slug ),
			2 => '/' . trailingslashit( $video_base_slug ) . trailingslashit( '%video_cat%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th><label><input name="video_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="masvideostog" <?php checked( $structures[0], $this->permalinks['video_base'] ); ?> /> <?php esc_html_e( 'Default', 'masvideos' ); ?></label></th>
					<td><code class="default-example"><?php echo esc_html( home_url() ); ?>/?video=sample-video</code> <code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $video_base ); ?>/sample-video/</code></td>
				</tr>
				<?php if ( $video_page_id ) : ?>
					<tr>
						<th><label><input name="video_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="masvideostog" <?php checked( $structures[1], $this->permalinks['video_base'] ); ?> /> <?php esc_html_e( 'Video base', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $video_base_slug ); ?>/sample-video/</code></td>
					</tr>
					<tr>
						<th><label><input name="video_permalink" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="masvideostog" <?php checked( $structures[2], $this->permalinks['video_base'] ); ?> /> <?php esc_html_e( 'Video base with category', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $video_base_slug ); ?>/video-category/sample-video/</code></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><label><input name="video_permalink" id="masvideos_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $this->permalinks['video_base'], $structures, true ), false ); ?> />
						<?php esc_html_e( 'Custom base', 'masvideos' ); ?></label></th>
					<td>
						<input name="video_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['video_base'] ? trailingslashit( $this->permalinks['video_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		echo '<h2>' . esc_html__( 'Video Playlist Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your video-playlist URLs here. For example, using <code>video-playlist</code> would make your video-playlist links like <code>%svideo-playlist/sample-video-playlist/</code>. This setting affects video-playlist URLs only, not things such as video-playlist categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$video_playlist_page_id = masvideos_get_page_id( 'video-playlist' );
		$video_playlist_base_slug    = urldecode( ( $video_playlist_page_id > 0 && get_post( $video_playlist_page_id ) ) ? get_page_uri( $video_playlist_page_id ) : _x( 'video-playlist', 'default-slug', 'masvideos' ) );
		$video_playlist_base = _x( 'video-playlist', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $video_playlist_base_slug ),
			2 => '/' . trailingslashit( $video_playlist_base_slug ) . trailingslashit( '%video_playlist_cat%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Video Playlist base', 'masvideos' ); ?></th>
					<td>
						<input name="video_playlist_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['video_playlist_base'] ? trailingslashit( $this->permalinks['video_playlist_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		echo '<h2>' . esc_html__( 'TV Shows Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your tv-show URLs here. For example, using <code>tv-show</code> would make your tv-show links like <code>%stv-show/sample-tv-show/</code>. This setting affects tv_show URLs only, not things such as tv_show categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$tv_show_page_id = masvideos_get_page_id( 'tv_show' );
		$tv_show_base_slug    = urldecode( ( $tv_show_page_id > 0 && get_post( $tv_show_page_id ) ) ? get_page_uri( $tv_show_page_id ) : _x( 'tv-show', 'default-slug', 'masvideos' ) );
		$tv_show_base = _x( 'tv-show', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $tv_show_base_slug ),
			2 => '/' . trailingslashit( $tv_show_base_slug ) . trailingslashit( '%tv_show_genre%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th><label><input name="tv_show_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="masvideostog" <?php checked( $structures[0], $this->permalinks['tv_show_base'] ); ?> /> <?php esc_html_e( 'Default', 'masvideos' ); ?></label></th>
					<td><code class="default-example"><?php echo esc_html( home_url() ); ?>/?tv_show=sample-tv-show</code> <code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $tv_show_base ); ?>/sample-tv-show/</code></td>
				</tr>
				<?php if ( $tv_show_page_id ) : ?>
					<tr>
						<th><label><input name="tv_show_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="masvideostog" <?php checked( $structures[1], $this->permalinks['tv_show_base'] ); ?> /> <?php esc_html_e( 'TV Show base', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $tv_show_base_slug ); ?>/sample-tv-show/</code></td>
					</tr>
					<tr>
						<th><label><input name="tv_show_permalink" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="masvideostog" <?php checked( $structures[2], $this->permalinks['tv_show_base'] ); ?> /> <?php esc_html_e( 'TV Show base with genre', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $tv_show_base_slug ); ?>/tv-show-genre/sample-tv-show/</code></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><label><input name="tv_show_permalink" id="masvideos_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $this->permalinks['tv_show_base'], $structures, true ), false ); ?> />
						<?php esc_html_e( 'Custom base', 'masvideos' ); ?></label></th>
					<td>
						<input name="tv_show_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['tv_show_base'] ? trailingslashit( $this->permalinks['tv_show_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		echo '<h2>' . esc_html__( 'TV Show Playlist Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your tv-show-playlist URLs here. For example, using <code>tv-show-playlist</code> would make your tv-show-playlist links like <code>%stv-show-playlist/sample-tv-show-playlist/</code>. This setting affects tv-show-playlist URLs only, not things such as tv-show-playlist categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$tv_show_playlist_page_id = masvideos_get_page_id( 'tv-show-playlist' );
		$tv_show_playlist_base_slug    = urldecode( ( $tv_show_playlist_page_id > 0 && get_post( $tv_show_playlist_page_id ) ) ? get_page_uri( $tv_show_playlist_page_id ) : _x( 'tv-show-playlist', 'default-slug', 'masvideos' ) );
		$tv_show_playlist_base = _x( 'tv-show-playlist', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $tv_show_playlist_base_slug ),
			2 => '/' . trailingslashit( $tv_show_playlist_base_slug ) . trailingslashit( '%tv_show_playlist_cat%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'TV Show Playlist base', 'masvideos' ); ?></th>
					<td>
						<input name="tv_show_playlist_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['tv_show_playlist_base'] ? trailingslashit( $this->permalinks['tv_show_playlist_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		echo '<h2>' . esc_html__( 'Episode Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your episode URLs here. For example, using <code>episode</code> would make your episode links like <code>%sepisode/sample-episode/</code>. This setting affects episode URLs only, not things such as episode categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$episode_page_id = masvideos_get_page_id( 'episode' );
		$episode_base_slug    = urldecode( ( $episode_page_id > 0 && get_post( $episode_page_id ) ) ? get_page_uri( $episode_page_id ) : _x( 'episode', 'default-slug', 'masvideos' ) );
		$episode_base = _x( 'episode', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $episode_base_slug ),
			2 => '/' . trailingslashit( $episode_base_slug ) . trailingslashit( '%episode_genre%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Episode base', 'masvideos' ); ?></th>
					<td>
						<input name="episode_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['episode_base'] ? trailingslashit( $this->permalinks['episode_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		echo '<h2>' . esc_html__( 'Persons Permalink Settings', 'masvideos' ) . '</h2>';
		/* translators: %s: Home URL */
		echo wp_kses_post( wpautop( sprintf( __( 'If you like, you may enter custom structures for your person URLs here. For example, using <code>person</code> would make your person links like <code>%sperson/sample-person/</code>. This setting affects person URLs only, not things such as person categories.', 'masvideos' ), esc_url( home_url( '/' ) ) ) ) );

		$person_page_id = masvideos_get_page_id( 'person' );
		$person_base_slug    = urldecode( ( $person_page_id > 0 && get_post( $person_page_id ) ) ? get_page_uri( $person_page_id ) : _x( 'person', 'default-slug', 'masvideos' ) );
		$person_base = _x( 'person', 'default-slug', 'masvideos' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $person_base_slug ),
			2 => '/' . trailingslashit( $person_base_slug ) . trailingslashit( '%person_cat%' ),
		);
		?>
		<table class="form-table masvideos-permalink-structure">
			<tbody>
				<tr>
					<th><label><input name="person_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="masvideostog" <?php checked( $structures[0], $this->permalinks['person_base'] ); ?> /> <?php esc_html_e( 'Default', 'masvideos' ); ?></label></th>
					<td><code class="default-example"><?php echo esc_html( home_url() ); ?>/?person=sample-person</code> <code class="non-default-example"><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $person_base ); ?>/sample-person/</code></td>
				</tr>
				<?php if ( $person_page_id ) : ?>
					<tr>
						<th><label><input name="person_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="masvideostog" <?php checked( $structures[1], $this->permalinks['person_base'] ); ?> /> <?php esc_html_e( 'Person base', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $person_base_slug ); ?>/sample-person/</code></td>
					</tr>
					<tr>
						<th><label><input name="person_permalink" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="masvideostog" <?php checked( $structures[2], $this->permalinks['person_base'] ); ?> /> <?php esc_html_e( 'Person base with category', 'masvideos' ); ?></label></th>
						<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $person_base_slug ); ?>/person-category/sample-person/</code></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><label><input name="person_permalink" id="masvideos_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $this->permalinks['person_base'], $structures, true ), false ); ?> />
						<?php esc_html_e( 'Custom base', 'masvideos' ); ?></label></th>
					<td>
						<input name="person_permalink_structure" id="masvideos_permalink_structure" type="text" value="<?php echo esc_attr( $this->permalinks['person_base'] ? trailingslashit( $this->permalinks['person_base'] ) : '' ); ?>" class="regular-text code"> <span class="description"><?php esc_html_e( 'Enter a custom base to use. A base must be set or WordPress will use default instead.', 'masvideos' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php wp_nonce_field( 'masvideos-permalinks', 'masvideos-permalinks-nonce' ); ?>
		<script type="text/javascript">
			jQuery( function() {
				jQuery('input.masvideostog').change(function() {
					jQuery( $(this).parents('.masvideos-permalink-structure').find('#masvideos_permalink_structure') ).val( jQuery( this ).val() );
				});
				jQuery('.permalink-structure input').change(function() {
					jQuery('.masvideos-permalink-structure').find('code.non-default-example, code.default-example').hide();
					if ( jQuery(this).val() ) {
						jQuery('.masvideos-permalink-structure code.non-default-example').show();
						jQuery('.masvideos-permalink-structure input').removeAttr('disabled');
					} else {
						jQuery('.masvideos-permalink-structure code.default-example').show();
						jQuery('.masvideos-permalink-structure input:eq(0)').click();
						jQuery('.masvideos-permalink-structure input').attr('disabled', 'disabled');
					}
				});
				jQuery('.permalink-structure input:checked').change();
				jQuery('#masvideos_permalink_structure').focus( function(){
					jQuery('#masvideos_custom_selection').click();
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Save the settings.
	 */
	public function settings_save() {
		if ( ! is_admin() ) {
			return;
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'], $_POST['masvideos_movie_genre_slug'], $_POST['masvideos_movie_tag_slug'], $_POST['masvideos_movie_attribute_slug'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );
			$permalinks['movie_genre_base']  = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_movie_genre_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['movie_tag_base']       = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_movie_tag_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['movie_attribute_base'] = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_movie_attribute_slug'] ) ); // WPCS: input var ok, sanitization ok.

			// Generate movie base.
			$movie_base = isset( $_POST['movie_permalink'] ) ? masvideos_clean( wp_unslash( $_POST['movie_permalink'] ) ) : ''; // WPCS: input var ok, sanitization ok.

			if ( 'custom' === $movie_base ) {
				if ( isset( $_POST['movie_permalink_structure'] ) ) { // WPCS: input var ok.
					$movie_base = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( wp_unslash( $_POST['movie_permalink_structure'] ) ) ) ); // WPCS: input var ok, sanitization ok.
				} else {
					$movie_base = '/';
				}

				// This is an invalid base structure and breaks pages.
				if ( '/%movie_genre%/' === trailingslashit( $movie_base ) ) {
					$movie_base = '/' . _x( 'movie', 'slug', 'masvideos' ) . $movie_base;
				}
			} elseif ( empty( $movie_base ) ) {
				$movie_base = _x( 'movie', 'slug', 'masvideos' );
			}

			$permalinks['movie_base'] = masvideos_sanitize_permalink( $movie_base );

			// Movie base may require verbose page rules if nesting pages.
			$movie_page_id   = masvideos_get_page_id( 'movie' );
			$movie_permalink = ( $movie_page_id > 0 && get_post( $movie_page_id ) ) ? get_page_uri( $movie_page_id ) : _x( 'movie', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );

			// Generate movie-playlist base.
			$movie_playlist_base = isset( $_POST['movie_playlist_permalink_structure'] ) ? masvideos_clean( wp_unslash( $_POST['movie_playlist_permalink_structure'] ) ) : _x( 'movie-playlist', 'slug', 'masvideos' ); // WPCS: input var ok, sanitization ok.

			$permalinks['movie_playlist_base'] = masvideos_sanitize_permalink( $movie_playlist_base );

			// Movie Playlist base may require verbose page rules if nesting pages.
			$movie_playlist_page_id   = masvideos_get_page_id( 'movie-playlist' );
			$movie_playlist_permalink = ( $movie_playlist_page_id > 0 && get_post( $movie_playlist_page_id ) ) ? get_page_uri( $movie_playlist_page_id ) : _x( 'movie-playlist', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'], $_POST['masvideos_video_category_slug'], $_POST['masvideos_video_tag_slug'], $_POST['masvideos_video_attribute_slug'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );
			$permalinks['video_category_base']  = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_video_category_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['video_tag_base']       = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_video_tag_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['video_attribute_base'] = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_video_attribute_slug'] ) ); // WPCS: input var ok, sanitization ok.

			// Generate video base.
			$video_base = isset( $_POST['video_permalink'] ) ? masvideos_clean( wp_unslash( $_POST['video_permalink'] ) ) : ''; // WPCS: input var ok, sanitization ok.

			if ( 'custom' === $video_base ) {
				if ( isset( $_POST['video_permalink_structure'] ) ) { // WPCS: input var ok.
					$video_base = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( wp_unslash( $_POST['video_permalink_structure'] ) ) ) ); // WPCS: input var ok, sanitization ok.
				} else {
					$video_base = '/';
				}

				// This is an invalid base structure and breaks pages.
				if ( '/%video_genre%/' === trailingslashit( $video_base ) ) {
					$video_base = '/' . _x( 'video', 'slug', 'masvideos' ) . $video_base;
				}
			} elseif ( empty( $video_base ) ) {
				$video_base = _x( 'video', 'slug', 'masvideos' );
			}

			$permalinks['video_base'] = masvideos_sanitize_permalink( $video_base );

			// Video base may require verbose page rules if nesting pages.
			$video_page_id   = masvideos_get_page_id( 'video' );
			$video_permalink = ( $video_page_id > 0 && get_post( $video_page_id ) ) ? get_page_uri( $video_page_id ) : _x( 'video', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );

			// Generate video-playlist base.
			$video_playlist_base = isset( $_POST['video_playlist_permalink_structure'] ) ? masvideos_clean( wp_unslash( $_POST['video_playlist_permalink_structure'] ) ) : _x( 'video-playlist', 'slug', 'masvideos' ); // WPCS: input var ok, sanitization ok.

			$permalinks['video_playlist_base'] = masvideos_sanitize_permalink( $video_playlist_base );

			// Video Playlist base may require verbose page rules if nesting pages.
			$video_playlist_page_id   = masvideos_get_page_id( 'video-playlist' );
			$video_playlist_permalink = ( $video_playlist_page_id > 0 && get_post( $video_playlist_page_id ) ) ? get_page_uri( $video_playlist_page_id ) : _x( 'video-playlist', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'], $_POST['masvideos_tv_show_genre_slug'], $_POST['masvideos_tv_show_tag_slug'], $_POST['masvideos_tv_show_attribute_slug'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );
			$permalinks['tv_show_genre_base']  = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_tv_show_genre_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['tv_show_tag_base']       = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_tv_show_tag_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['tv_show_attribute_base'] = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_tv_show_attribute_slug'] ) ); // WPCS: input var ok, sanitization ok.

			// Generate tv show base.
			$tv_show_base = isset( $_POST['tv_show_permalink'] ) ? masvideos_clean( wp_unslash( $_POST['tv_show_permalink'] ) ) : ''; // WPCS: input var ok, sanitization ok.

			if ( 'custom' === $tv_show_base ) {
				if ( isset( $_POST['tv_show_permalink_structure'] ) ) { // WPCS: input var ok.
					$tv_show_base = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( wp_unslash( $_POST['tv_show_permalink_structure'] ) ) ) ); // WPCS: input var ok, sanitization ok.
				} else {
					$tv_show_base = '/';
				}

				// This is an invalid base structure and breaks pages.
				if ( '/%tv_show_genre%/' === trailingslashit( $tv_show_base ) ) {
					$tv_show_base = '/' . _x( 'tv-show', 'slug', 'masvideos' ) . $tv_show_base;
				}
			} elseif ( empty( $tv_show_base ) ) {
				$tv_show_base = _x( 'tv-show', 'slug', 'masvideos' );
			}

			$permalinks['tv_show_base'] = masvideos_sanitize_permalink( $tv_show_base );

			// TV Show base may require verbose page rules if nesting pages.
			$tv_show_page_id   = masvideos_get_page_id( 'tv_show' );
			$tv_show_permalink = ( $tv_show_page_id > 0 && get_post( $tv_show_page_id ) ) ? get_page_uri( $tv_show_page_id ) : _x( 'tv-show', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );

			// Generate tv-show-playlist base.
			$tv_show_playlist_base = isset( $_POST['tv_show_playlist_permalink_structure'] ) ? masvideos_clean( wp_unslash( $_POST['tv_show_playlist_permalink_structure'] ) ) : _x( 'tv-show-playlist', 'slug', 'masvideos' ); // WPCS: input var ok, sanitization ok.

			$permalinks['tv_show_playlist_base'] = masvideos_sanitize_permalink( $tv_show_playlist_base );

			// TV Show Playlist base may require verbose page rules if nesting pages.
			$tv_show_playlist_page_id   = masvideos_get_page_id( 'tv-show-playlist' );
			$tv_show_playlist_permalink = ( $tv_show_playlist_page_id > 0 && get_post( $tv_show_playlist_page_id ) ) ? get_page_uri( $tv_show_playlist_page_id ) : _x( 'tv-show-playlist', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );

			// Generate episode base.
			$episode_base = isset( $_POST['episode_permalink_structure'] ) ? masvideos_clean( wp_unslash( $_POST['episode_permalink_structure'] ) ) : _x( 'episode', 'slug', 'masvideos' ); // WPCS: input var ok, sanitization ok.

			$permalinks['episode_base'] = masvideos_sanitize_permalink( $episode_base );

			// Episode base may require verbose page rules if nesting pages.
			$episode_page_id   = masvideos_get_page_id( 'episode' );
			$episode_permalink = ( $episode_page_id > 0 && get_post( $episode_page_id ) ) ? get_page_uri( $episode_page_id ) : _x( 'episode', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page.
		if ( isset( $_POST['permalink_structure'], $_POST['masvideos-permalinks-nonce'], $_POST['masvideos_person_category_slug'], $_POST['masvideos_person_tag_slug'], $_POST['masvideos_person_attribute_slug'] ) && wp_verify_nonce( wp_unslash( $_POST['masvideos-permalinks-nonce'] ), 'masvideos-permalinks' ) ) { // WPCS: input var ok, sanitization ok.
			masvideos_switch_to_site_locale();

			$permalinks                   = (array) get_option( 'masvideos_permalinks', array() );
			$permalinks['person_category_base']  = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_person_category_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['person_tag_base']       = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_person_tag_slug'] ) ); // WPCS: input var ok, sanitization ok.
			$permalinks['person_attribute_base'] = masvideos_sanitize_permalink( wp_unslash( $_POST['masvideos_person_attribute_slug'] ) ); // WPCS: input var ok, sanitization ok.

			// Generate person base.
			$person_base = isset( $_POST['person_permalink'] ) ? masvideos_clean( wp_unslash( $_POST['person_permalink'] ) ) : ''; // WPCS: input var ok, sanitization ok.

			if ( 'custom' === $person_base ) {
				if ( isset( $_POST['person_permalink_structure'] ) ) { // WPCS: input var ok.
					$person_base = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', trim( wp_unslash( $_POST['person_permalink_structure'] ) ) ) ); // WPCS: input var ok, sanitization ok.
				} else {
					$person_base = '/';
				}

				// This is an invalid base structure and breaks pages.
				if ( '/%person_genre%/' === trailingslashit( $person_base ) ) {
					$person_base = '/' . _x( 'person', 'slug', 'masvideos' ) . $person_base;
				}
			} elseif ( empty( $person_base ) ) {
				$person_base = _x( 'person', 'slug', 'masvideos' );
			}

			$permalinks['person_base'] = masvideos_sanitize_permalink( $person_base );

			// Person base may require verbose page rules if nesting pages.
			$person_page_id   = masvideos_get_page_id( 'person' );
			$person_permalink = ( $person_page_id > 0 && get_post( $person_page_id ) ) ? get_page_uri( $person_page_id ) : _x( 'person', 'default-slug', 'masvideos' );

			update_option( 'masvideos_permalinks', $permalinks );
			masvideos_restore_locale();
		}
	}
}

return new MasVideos_Admin_Permalink_Settings();