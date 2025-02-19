<?php
/**
 * REST API for GutenAI Plugin.
 * 
 * Registers REST API routes for the GutenAI plugin to fetch keywords from the Dandelion API.
 *
 * @package GutenAI
 */

namespace GutenAI\Inc;

use GutenAI\Inc\Traits\Singleton;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GutenAI_API
 *
 * Registers REST API routes for the GutenAI plugin.
 */
class GutenAI_API {

	use Singleton;

	/**
	 * Construct method.
	 * 
	 * Initializes the class and sets up WordPress hooks.
	 * 
	 * @return void
	 */
	protected function __construct() {
		$this->register();
	}

	/**
	 * Register API routes.
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			'guten-ai/v1',
			'/keywords',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'fetch_keywords' ),
				'permission_callback' => '__return_true', // Specifically set to allow public access.
				'args'                => array(
					'text' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Fetch keywords from the Dandelion API.
	 *
	 * @param WP_REST_Request $request The API request object.
	 * @return WP_REST_Response The response object containing keywords.
	 */
	public function fetch_keywords( WP_REST_Request $request ) {
		$content  = $request->get_param( 'text' );
		$is_cache = $request->get_param( 'cache' );

		if ( empty( $content ) ) {
			return new WP_REST_Response( array( 'error' => __( 'No content provided.', 'gutenai' ) ), 400 );
		}

		// Set unique cache key for the request.
		$guten_ai_cache_key = 'guten_ai_keywords_' . md5( $content );

		// Check if cached data exists.
		$cached_data = get_transient( $guten_ai_cache_key );

		if ( $cached_data && $is_cache ) {
			return new WP_REST_Response( $cached_data, 200 );
		}

		// Fetch analysis from Dandelion API.
		$response = $this->fetch_dandelion_analysis( $content );

		if ( is_wp_error( $response ) ) {
			return new WP_REST_Response( array( 'error' => $response->get_error_message() ), 500 );
		}

		if ( empty( $response['annotations'] ) ) {
			return new WP_REST_Response( array( 'error' => __( 'No keywords found.', 'gutenai' ) ), 404 );
		}

		set_transient( $guten_ai_cache_key, $response, HOUR_IN_SECONDS );

		return new WP_REST_Response(
			array(
				'success'   => true,
				'keywords'  => $response['annotations'],
			),
			200
		);
	}

	/**
	 * Fetches keyword analysis from the Dandelion API.
	 *
	 * @param string $text The text content to analyze.
	 * @return array|WP_Error API response or WP_Error.
	 */
	private function fetch_dandelion_analysis( $text ) {
		$api_key = get_option( 'guten_ai_dandelion_api_key' );

		if ( empty( $api_key ) ) {
			return new WP_Error( 'missing_api_key', __( 'Dandelion API key is not set.', 'gutenai' ), array( 'status' => 500 ) );
		}

		$chunk_size      = 400;
		$text_chunks     = str_split( $text, $chunk_size );
		$merged_keywords = array();

		foreach ( $text_chunks as $chunk ) {
			$url = 'https://api.dandelion.eu/datatxt/nex/v1/?text=' . urlencode( $chunk ) . '&token=' . $api_key . '&lang=en';

			$response = wp_safe_remote_get( $url, array( 'timeout' => 2 ) ); // Added timeout for stability.

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! empty( $data['annotations'] ) ) {
				foreach ( $data['annotations'] as $annotation ) {
					$merged_keywords[] = array(
						'keyword'    => $annotation['spot'] ?? '',
						'confidence' => $annotation['confidence'] ?? 0,
						'uri'        => $annotation['uri'] ?? '',
					);
				}
			}
		}

		return array( 'annotations' => $merged_keywords );
	}
}
