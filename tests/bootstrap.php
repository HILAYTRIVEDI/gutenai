<?php
/**
 * PHPUnit Bootstrap file
 *
 * @package GutenAI
 */

// Load Composer Autoloader.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Define Plugin Path Constants.
define( 'GUTENAI_PLUGIN_PATH', dirname( __DIR__ ) . '/' );
define( 'GUTENAI_PLUGIN_FILE', GUTENAI_PLUGIN_PATH . '/guten-ai.php' );

// Load the Plugin.
require_once GUTENAI_PLUGIN_FILE;
