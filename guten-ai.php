<?php
/**
 * Plugin Name:       GutenAI
 * Description:       AI-powered Gutenberg block for content suggestions.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Hilay Trivedi
 * Author URI:        https://github.com/HILAYTRIVEDI
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gutenai
 *
 * @package GutenAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
if ( ! defined( 'GUTENAI_VERSION' ) ) {
	define( 'GUTENAI_VERSION', '0.1.0' );
}

if ( ! defined( 'GUTENAI_PLUGIN_FILE' ) ) {
	define( 'GUTENAI_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'GUTENAI_PLUGIN_PATH' ) ) {
	define( 'GUTENAI_PLUGIN_PATH', plugin_dir_path( GUTENAI_PLUGIN_FILE ) );
}

if ( ! defined( 'GUTENAI_PLUGIN_URL' ) ) {
	define( 'GUTENAI_PLUGIN_URL', plugin_dir_url( GUTENAI_PLUGIN_FILE ) );
}

/**
 * Autoload plugin dependencies.
 *
 * This function ensures that the autoloader is included and loaded properly.
 * It prevents redundant loading by using a static variable `$loaded`.
 *
 * @return bool True if autoload is successful, false otherwise.
 */
function gutenai_autoload(): bool {
	static $loaded;

	if ( wp_validate_boolean( $loaded ) ) {
		return $loaded;
	}

	$autoload_file = GUTENAI_PLUGIN_PATH . 'inc/helpers/autoloader.php';

	if ( file_exists( $autoload_file ) && is_readable( $autoload_file ) ) {
		require_once $autoload_file;
		$loaded = true;
		return $loaded;
	}

	$loaded = false;
	return $loaded;
}

/**
 * Ensure the plugin does not load if the autoloader is missing.
 */
if ( ! gutenai_autoload() ) {
	return;
}

/**
 * Initializes the GutenAI plugin and registers the Gutenberg block.
 *
 * - Checks if the main plugin class `GutenAI\Inc\Plugin` exists before instantiating it.
 * - Registers the custom Gutenberg block.
 *
 * @return void
 */
function guten_ai_block_init(): void {
	if ( class_exists( 'GutenAI\Inc\Plugin' ) ) {
		GutenAI\Inc\Plugin::get_instance();
	}

	register_block_type( GUTENAI_PLUGIN_PATH . 'build/guten-ai' );
}

guten_ai_block_init();
