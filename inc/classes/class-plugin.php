<?php
/**
 * Plugin manifest class.
 *
 * This class serves as the main entry point for initializing all necessary components
 * of the GutenAI plugin. It follows the Singleton pattern to ensure only one instance
 * is loaded throughout the execution.
 *
 * @package GutenAI
 */

namespace GutenAI\Inc;

use GutenAI\Inc\Traits\Singleton;
use GutenAI\Inc\GutenAI_API;
use GutenAI\Inc\GutenAI_Admin_Menu;

/**
 * Class Plugin
 *
 * Manages the initialization of core plugin components.
 */
class Plugin {

	use Singleton;

	/**
	 * Private constructor to enforce the Singleton pattern.
	 * Initializes the required plugin classes.
	 */
	protected function __construct() {
		$this->initialize_classes();
	}

	/**
	 * Initializes all core classes required for the plugin to function.
	 *
	 * This method ensures that the main components of the plugin,
	 * such as the REST API handler (`GutenAI_API`) and the admin menu (`GutenAI_Admin_Menu`),
	 * are properly instantiated.
	 *
	 * If additional components need to be initialized in the future, they can be added here.
	 *
	 * @return void
	 */
	private function initialize_classes() {
		GutenAI_API::get_instance();     
		GutenAI_Admin_Menu::get_instance();
	}
}
