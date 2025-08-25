<?php
/**
 * Autoloader for the GutenAI plugin.
 *
 * This file dynamically loads required classes and traits based on their namespace,
 * ensuring an organized and scalable code structure.
 *
 * @package GutenAI
 */

namespace GutenAI\Inc\Helpers;

/**
 * Autoloader function.
 *
 * Dynamically loads classes and traits based on the namespace structure,
 * following WordPress and PHP best practices.
 *
 * @param string $resource Fully qualified namespace of the class/trait.
 *
 * @return void
 */
function autoloader( $resource = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.resourceFound
	// Define the plugin's root namespace.
	$namespace_root = 'GutenAI\\';

	// Ensure the resource is properly formatted.
	$resource = trim( $resource, '\\' );

	// Validate the resource name.
	if ( empty( $resource ) || strpos( $resource, '\\' ) === false || strpos( $resource, $namespace_root ) !== 0 ) {
		return;
	}

	// Remove root namespace from the resource.
	$relative_path = str_replace( $namespace_root, '', $resource );
	$path_segments = explode( '\\', str_replace( '_', '-', strtolower( $relative_path ) ) );

	// Ensure the path segments contain at least two elements (e.g., Inc\ClassName).
	if ( count( $path_segments ) < 2 ) {
		return;
	}

	// Set the default directory and class file name format.
	$directory = 'classes';
	$file_name = sprintf( 'class-%s.php', trim( strtolower( $path_segments[1] ) ) );

	// Handle special cases for different namespace segments.
	if ( 'inc' === $path_segments[0] ) {
		switch ( $path_segments[1] ) {
			case 'traits':
				$directory = 'traits';
				$file_name = sprintf( 'trait-%s.php', trim( strtolower( $path_segments[2] ) ) );
				break;
			default:
			   $directory = 'classes';
			   $file_name = sprintf( 'class-%s.php', trim( strtolower( $path_segments[1] ) ) );
				break;
		}
	}

	// Construct the full file path.
	$resource_path = sprintf( '%s/inc/%s/%s', untrailingslashit( GUTENAI_PLUGIN_PATH ), $directory, $file_name );


	if ( 'tests' === $path_segments[0] ) {

		switch ( $path_segments[1] ) {
			case 'testcase':
				$directory = 'php';
				$file_name = 'testcase';
				break;
			default:
				break;
		}

		// Create resource path for `TestCase` class as it is not in `tests/php/inc` directory.
		$resource_path = sprintf( '%s/tests/php/%s.php', untrailingslashit( GUTENAI_PLUGIN_PATH ), $file_name );

	}

	$resource_path_valid = validate_file( $resource_path );

	// Load the file if it exists.
	if ( ! empty( $resource_path ) && file_exists( $resource_path ) ) {
		require_once $resource_path;
	}
}

// Register the autoloader function.
spl_autoload_register( '\\GutenAI\\Inc\\Helpers\\autoloader' );
