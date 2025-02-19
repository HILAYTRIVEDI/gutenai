<?php
/**
 * Handles the registration of the admin menu and settings for the GutenAI plugin.
 *
 * This class adds a menu page to the WordPress admin dashboard where users can
 * configure their Dandelion API key. It ensures proper sanitization and nonce
 * verification for security.
 *
 * @package GutenAI
 */

namespace GutenAI\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GutenAI\Inc\Traits\Singleton;

/**
 * Class GutenAI_Admin_Menu
 *
 * Registers an admin menu and settings for GutenAI.
 */
class GutenAI_Admin_Menu {
	use Singleton;

	/**
	 * Constructor method.
	 *
	 * Initializes the class and sets up WordPress hooks.
	 */
	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Registers WordPress hooks for menu and settings.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Registers the admin menu page.
	 *
	 * Adds a new menu page under the WordPress admin dashboard.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'Guten AI', 'gutenai' ),
			__( 'Guten AI', 'gutenai' ),
			'manage_options',           
			'gutenai',                 
			array( $this, 'render_admin_page' ), 
			'dashicons-admin-site',
			2                           
		);
	}

	/**
	 * Registers plugin settings, sections, and fields.
	 *
	 * Uses WordPress Settings API to store API keys securely.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'guten_ai_settings',
			'guten_ai_dandelion_api_key',
			array(
				'sanitize_callback' => array( $this, 'sanitize_api_key' ),
			)
		);

		add_settings_section(
			'guten_ai_main_section',
			__( 'Dandelion API Settings', 'gutenai' ),
			'__return_false',
			'guten-ai-settings'
		);

		add_settings_field(
			'guten_ai_dandelion_api_key',
			__( 'Dandelion API Key', 'gutenai' ),
			array( $this, 'api_key_field_callback' ),
			'guten-ai-settings',
			'guten_ai_main_section'
		);
	}

	/**
	 * Sanitizes and validates the API key input.
	 *
	 * Ensures proper security by verifying nonce before saving the API key.
	 *
	 * @param string $input The API key input from the form.
	 * @return string The sanitized API key.
	 */
	public function sanitize_api_key( $input ) {
		// Verify nonce security.
		if ( ! isset( $_POST['guten_ai_nonce'] ) || ! wp_verify_nonce( $_POST['guten_ai_nonce'], 'guten_ai_save_api_key' ) ) { // phpcs:ignore
			wp_die( esc_html__( 'Nonce verification failed!', 'gutenai' ) );
		}
		return sanitize_text_field( $input );
	}

	/**
	 * Renders the input field for the API key in the settings page.
	 *
	 * Retrieves the current saved API key and displays it in an input field.
	 *
	 * @return void
	 */
	public function api_key_field_callback() {
		$api_key = get_option( 'guten_ai_dandelion_api_key', '' );
		?>
		<input type="text" name="guten_ai_dandelion_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'Enter your Dandelion API Key.', 'gutenai' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders the admin settings page.
	 *
	 * Displays the settings form where users can enter their API key.
	 *
	 * @return void
	 */
	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Guten AI Settings', 'gutenai' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'guten_ai_settings' );
				do_settings_sections( 'guten-ai-settings' );
				wp_nonce_field( 'guten_ai_save_api_key', 'guten_ai_nonce' );
				submit_button( __( 'Save API Key', 'gutenai' ) );
				?>
			</form>
		</div>
		<?php
	}
}
