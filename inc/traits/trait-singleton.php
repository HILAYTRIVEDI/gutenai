<?php
/**
 * Singleton Trait for ensuring a single instance of a class.
 *
 * This trait enforces the Singleton pattern, ensuring that a class can only have one
 * instance throughout the application lifecycle. It prevents direct instantiation,
 * cloning, and allows access to the single instance via `get_instance()`.
 *
 * @package GutenAI
 */

namespace GutenAI\Inc\Traits;

trait Singleton {

	/**
	 * Protected constructor to prevent direct object creation.
	 *
	 * The constructor is kept protected to restrict external instantiation.
	 */
	protected function __construct() {
		// Prevent direct instantiation.
	}

	/**
	 * Prevent object cloning to maintain singleton instance integrity.
	 */
	final protected function __clone() {
		// Prevent cloning of the instance.
	}

	/**
	 * Returns a single instance of the class.
	 *
	 * If an instance does not already exist, it creates one and stores it in a static variable.
	 * This method ensures that only one instance of the class exists throughout the application.
	 *
	 * @return static The singleton instance of the class.
	 */
	final public static function get_instance() {
		static $instance = array();

		$called_class = get_called_class();

		if ( ! isset( $instance[ $called_class ] ) ) {
			$instance[ $called_class ] = new $called_class();

			/**
			 * Fires when a singleton instance is initialized.
			 *
			 * Developers can hook into this action to perform additional setup when
			 * a singleton instance is created.
			 *
			 * @param string $called_class The fully qualified class name.
			 */
			do_action( sprintf( 'gutenai_singleton_init_%s', $called_class ) );  // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		}

		return $instance[ $called_class ];
	}
}
