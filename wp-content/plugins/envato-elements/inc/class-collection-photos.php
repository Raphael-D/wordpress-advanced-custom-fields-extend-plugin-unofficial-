<?php
/**
 * Collections: Collection_Photos class
 *
 * This class is used to manage collections of Elements photos.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Photo Collection management.
 *
 * @since 0.0.2
 *
 * @see Collection
 */
class Collection_Photos extends Collection {

	const IMPORT_OPTIONS_NAME = 'envato_elements_photo_imports';
	const IMPORT_WIDTH = 2000;

	private $imported_images;

	/**
	 * Collection_Photos constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->category        = 'photos';
		$this->imported_images = [];
		$this->get_imported_images();
		add_action( 'deleted_post', [ $this, 'check_for_deleted_images' ] );
	}

	/**
	 * Returns a list of imported images.
	 * Using options rather than bogging down the post meta table.
	 */
	public function get_imported_images() {
		$this->imported_images = get_option( self::IMPORT_OPTIONS_NAME, [] );
		if ( ! is_array( $this->imported_images ) ) {
			$this->imported_images = [];
		}

		return $this->imported_images;
	}

	/**
	 * Do this expensive search from time to time to ensure our list is up to date.
	 */
	public function check_for_deleted_images() {
		$image_ids = $this->get_imported_images();
		foreach ( $image_ids as $photo_id => $image_id ) {
			$still_exists = false;
			$media_item   = get_post( $image_id );
			if ( $media_item && ! is_wp_error( $media_item ) && $media_item->ID ) {
				$fullsize_path = get_attached_file( $media_item->ID );
				if ( is_file( $fullsize_path ) ) {
					$still_exists = true;
				}
			}
			if ( ! $still_exists ) {
				unset( $image_ids[ $photo_id ] );
				update_option( self::IMPORT_OPTIONS_NAME, $image_ids );
			}
		}
	}

	/**
	 * Get photo collections from Elements API.
	 *
	 * @return array
	 */
	public function get_remote_collections( $search = [] ) {

		$max_pages = 50;

		$api_parameters = [
			'type' => 'photos',
			'page' => empty( $search['pg'] ) || (int) $search['pg'] < 1 || (int) $search['pg'] > $max_pages ? 1 : (int) $search['pg'],
		];

		foreach (
			[
				'search_terms' => 'text',
				'orientation'  => 'orientation',
				'background'   => 'background',
				'colors'       => 'colors',
				'tags'         => 'tag',
			] as $api_key => $our_key
		) {
			if ( ! empty( $search[ $our_key ] ) && strlen( trim( $search[ $our_key ] ) ) > 0 ) {
				$api_parameters[ $api_key ] = sanitize_text_field( trim( $search[ $our_key ] ) );
			}
		}

		$api_response = [
			'data'   => [],
			'status' => 0,
			'meta'   => [],
		];

//		print_r($search);
//		echo '/extensions/search?' . http_build_query( $api_parameters );exit;
		$data = Elements_API::get_instance()->api_call( '/extensions/search?' . http_build_query( $api_parameters ) );
//		echo json_encode($data,true);exit;
		// Todo: handle changing in API results and display a 'please update plugin' message.
		if ( ! is_wp_error( $data ) && is_array( $data ) && ! empty( $data['results']['search_query_result']['search_payload'] ) ) {
			$items = $data['results']['search_query_result']['search_payload']['items'];
			// Get our filtered photo responses:
			$api_response['data']['results'] = [];
			foreach ( $items as $photo ) {
				$photo = $this->filter_photo( $photo );
				if ( $photo ) {
					$api_response['data']['results'][] = $photo;
				}
			}
			// Extract pagination details:
			// Todo: handle changing in API results and display a 'please update plugin' message.
			$api_response['data']['total_results'] = min( $data['results']['search_query_result']['search_payload']['total_hits'], $max_pages * $data['results']['per_page'] );
			$api_response['data']['per_page']      = $data['results']['per_page'];
			$api_response['data']['page_number']   = $data['results']['current_page'];

			$api_response['status'] = 1;
			$aggregations_cache     = Options::get_instance()->get( 'aggregations_' . $api_parameters['type'], false, true );
			if ( ! is_array( $aggregations_cache ) ) {
				$aggregations_cache = [];
			}
			$total_items      = Options::get_instance()->get( 'photo_count', 0, true );
			$this_total_items = floor( $data['results']['search_query_result']['search_payload']['total_hits'] / 10000 ) * 10000;
			if ( $this_total_items > $total_items ) {
				Options::get_instance()->set( 'photo_count', $this_total_items, true );
				$total_items = $this_total_items;
			}
			$api_response['meta']['totalItemCount'] = number_format( $total_items, 0 );
			$api_response['meta']['aggregations']   = $aggregations_cache;
			if ( ! empty( $data['results']['search_query_result']['search_payload']['aggregations'] ) ) {
				$aggregations = $data['results']['search_query_result']['search_payload']['aggregations'];
				// We cache a list of all aggregations locally, so the UI always shows all available options.
				foreach ( [ 'orientation', 'background', 'colors' ] as $aggregation ) {
					if ( ! empty( $aggregations[ $aggregation ]['buckets'] ) ) {
						if ( ! isset( $aggregations_cache[ $aggregation ] ) ) {
							$aggregations_cache[ $aggregation ] = [];
						}
						foreach ( $aggregations[ $aggregation ]['buckets'] as $bucket ) {
							if ( ! empty( $bucket['key'] ) ) {
								$aggregations_cache[ $aggregation ][ $bucket['key'] ] = [
									'label'     => $bucket['key'],
									'color'     => ( $aggregation === 'colors' ) ? strtolower( $bucket['key'] ) : false,
									'colorDark' => ( $aggregation === 'colors' && ( strtolower( $bucket['key'] ) === 'white' || strtolower( $bucket['key'] ) === 'yellow' ) ),
									'available' => false,
								];
							}
						}
					}
				}

				if ( ! empty( $aggregations_cache['colors'] ) ) {
					$desired_color_order = array_flip( [
						'Pink',
						'Blue',
						'Red',
						'Purple',
						'Orange',
						'Brown',
						'Yellow',
						'Black',
						'Green',
						'Grey',
						'Teal',
						'White',
					] );
					uksort( $aggregations_cache['colors'], function ( $a, $b ) use ( $desired_color_order ) {
						return $desired_color_order[ $a ] > $desired_color_order[ $b ] ? 1 : - 1;
					} );
				}
				Options::get_instance()->set( 'aggregations_' . $api_parameters['type'], $aggregations_cache, true );

				// Work out which aggregations are available in this request.
				$available_aggregations = $aggregations_cache;
				foreach ( $available_aggregations as $aggregation => $buckets ) {
					foreach ( $buckets as $bucket_name => $bucket_settings ) {
						foreach ( $aggregations[ $aggregation ]['buckets'] as $available_bucket ) {
							$available_aggregations[ $aggregation ][ $available_bucket['key'] ]['available'] = true;
							$available_aggregations[ $aggregation ][ $available_bucket['key'] ]['count']     = $available_bucket['doc_count'];
						}
					}
				}
				// What tags are available for active search:
				$available_aggregations['tags'] = [];
				if ( ! empty( $aggregations['tags'] ) && ! empty( $aggregations['tags']['buckets'] ) ) {
					foreach ( $aggregations['tags']['buckets'] as $bucket ) {
						$available_aggregations['tags'][ $bucket['key'] ] = [
							'label'     => $bucket['key'],
							'available' => true,
							'count'     => $bucket['doc_count'],
						];
					}
				}
				$api_response['meta']['aggregations'] = $available_aggregations;

			}

			if ( ! empty( $search['photoId'] ) ) {
				$api_response['openItem'] = [ 'photoId' => $search['photoId'] ];
			}
		} else {
			$api_error_codes = Elements_API::get_instance()->extract_errors( $data );
			if ( $api_error_codes ) {
				$api_response = array_merge( $api_response, $api_error_codes );
			}
		}

		return $api_response;
	}

	public function get_media_url( $attachment_id ) {
		return admin_url( 'upload.php' );
//		return admin_url( 'upload.php?item=' . (int) $attachment_id );
		//return admin_url( 'post.php?post=' . (int) $attachment_id . '&action=edit' );
	}

	/**
	 * Filter our list of available photos.
	 *
	 * @param array $api_data Raw API data.
	 *
	 * @return array
	 */
	public function filter_photo( $api_data ) {

		// We calculate the image dimensions at a max2000 width for display in the front end.
		if ( ! empty( $api_data['item_attributes']['dimensions'] ) ) {
			// Grab out the thumbnail and large preview images so we can display within template.
			$api_data['categorySlug'] = 'photos';
			$api_data['uuid']         = $api_data['humane_id'];
			$api_data['photoId']      = $api_data['humane_id'];
			$api_data['itemImported'] = ! empty( $this->imported_images[ $api_data['humane_id'] ] );
			if ( $api_data['itemImported'] ) {
				$api_data['itemImportedUrl'] = $this->get_media_url( $this->imported_images[ $api_data['humane_id'] ] );
			}

			$api_data['displayWidth']      = self::IMPORT_WIDTH;
			$api_data['displayHeight']     = floor( ( self::IMPORT_WIDTH / $api_data['item_attributes']['dimensions']['width'] ) * $api_data['item_attributes']['dimensions']['height'] );
			$api_data['aspectRatio']       = round( $api_data['displayWidth'] / $api_data['displayHeight'], 2 );
			$api_data['aspectRatioHeight'] = ( $api_data['displayHeight'] / $api_data['displayWidth'] ) * 100;
			$thumbWidth                    = 300;
			$gridheight                    = 240;
			$api_data['imageThumb']        = [
				// todo: large image size for panoramics / certain aspect ratios.
				'src'        => $this->get_imgix_url( $api_data['cover_image'], 'w' . $thumbWidth ),
				'width'      => $thumbWidth,
				'height'     => floor( ( $thumbWidth / intval( $api_data['item_attributes']['dimensions']['width'] ) ) * intval( $api_data['item_attributes']['dimensions']['height'] ) ),
				'gridHeight' => $gridheight,
				'gridWidth'  => floor( ( $gridheight / intval( $api_data['item_attributes']['dimensions']['height'] ) ) * intval( $api_data['item_attributes']['dimensions']['width'] ) ),
			];
			$thumbWidth                    = 900;
			$api_data['imageLarge']        = [
				'src'    => $this->get_imgix_url( $api_data['cover_image'], 'w' . $thumbWidth ),
				'width'  => $thumbWidth,
				'height' => floor( ( $thumbWidth / intval( $api_data['item_attributes']['dimensions']['width'] ) ) * intval( $api_data['item_attributes']['dimensions']['height'] ) )
			];

			return $api_data;
		} else {
			return false;
		}

	}

	private function get_imgix_url( $data, $size = 'w300' ) {
		$url = 'https://' . $data['imgix_subdomain'];
		if ( strpos( $data['imgix_subdomain'], 'imgix.net' ) === false ) {
			$url .= '-0.imgix.net';
		}
		$url .= '/' . $data['id'];
		$url .= '?' . $data['imgix_queries'][ $size ];

		return $url;
	}

	/**
	 * Import a template via the REST api
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function rest_process_import( $request ) {

		$photo_id                            = $request->get_param( 'itemId' );  // photo humane_id
		$import_options['photo_name']        = $request->get_param( 'photoName' );  // photo name, used for naming our local file.
		$import_options['photo_description'] = $request->get_param( 'photoDescription' );  // photo description, used for seo.
		$import_options['import_type']       = $request->get_param( 'importType' );

		$result = $this->import_photo_by_id( $photo_id, $import_options );

		if ( ! empty( $result['status'] ) && ! empty( $result['attachment_id'] ) ) {
			$result['updateData'] = [
				'itemImported'    => true,
				'itemImportedUrl' => $this->get_media_url( $result['attachment_id'] ),
			];
		}

		return new \WP_REST_Response( $result, 200 );

	}


	public function import_photo_by_id( $photo_id, $import_options = [] ) {

		$file_name = '';
		if ( ! empty( $import_options['photo_name'] ) ) {
			$file_name = preg_replace( '#[^a-z0-9]+#', '-', basename( strtolower( sanitize_text_field( $import_options['photo_name'] ) ) ) ) . '.jpg';
		}
		if ( ! $file_name ) {
			$file_name = 'elements-' . $photo_id . '.jpg';
		}
		$file_description = 'Envato Elements: ' . $file_name;
		if ( ! empty( $import_options['photo_description'] ) ) {
			$file_description = $import_options['photo_description'];
		}

		$result = [
			'status'   => false,
			'category' => $this->category,
			'photoId'  => $photo_id,
		];

		if ( $photo_id ) {

			// Check it's not already imported.
			if ( ! empty( $this->imported_images[ $photo_id ] ) ) {
				// check it's still in the database and the file still exists.
				$media_item = get_post( $this->imported_images[ $photo_id ] );
				if ( $media_item && ! is_wp_error( $media_item ) && $media_item->ID ) {
					$fullsize_path = get_attached_file( $media_item->ID );
					if ( is_file( $fullsize_path ) ) {
						// still exists! don't import again.
						$result['status']        = true;
						$result['attachment_id'] = $media_item->ID;
					}
				}
			}

			if ( ! $result['status'] ) {

				API::get_instance()->api_call( 'v2/photos/download/' . $photo_id, [
					'import_type' => $import_options['import_type']
				] );

				// Grab a copy of the photo from the Elements API, using our token.
				// Return a status json error if the user doesn't have a valid license.
				// (they shouldn't see this button anyways)
				$data = Elements_API::get_instance()->api_call( '/extensions/item/' . $photo_id . '/download', 'POST', [
					'project_name'   => Options::get_instance()->get( 'project_name', get_bloginfo( 'name' ) ),
					// todo: this might need to be per WordPress user, rather than a site wide setting.
					'extension_type' => 'envato-wp',
				] );
				if ( $data && ! is_wp_error( $data ) && ! empty( $data['download_urls'][ 'max' . self::IMPORT_WIDTH ] ) ) {
					$temporary_image_name = wp_tempnam( $file_name );
					wp_safe_remote_get( $data['download_urls'][ 'max' . self::IMPORT_WIDTH ], array(
						'timeout'  => 15,
						'stream'   => true,
						'filename' => $temporary_image_name
					) );
					if ( $temporary_image_name && $file_data = file_get_contents( $temporary_image_name ) ) {
						$upload = wp_upload_bits( $file_name, 0, $file_data );
						if ( $upload && ! is_wp_error( $upload ) && empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
							$info      = wp_check_filetype( $upload['file'] );
							$post_data = [
								'post_title'   => sanitize_text_field( ! empty( $import_options['photo_name'] ) ? $import_options['photo_name'] : $file_name ),
								'post_excerpt' => sanitize_text_field( $file_description ),
								'post_content' => sanitize_text_field( $file_description ),
							];
							if ( $info ) {
								$post_data['post_mime_type'] = $info['type'];
							}
							$attachment_id = wp_insert_attachment( $post_data, $upload['file'] );
							if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
								$attachment_meta = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
								wp_update_attachment_metadata( $attachment_id, $attachment_meta );
								$result['status']        = true;
								$result['attachment_id'] = $attachment_id;
								// Update list of imported images.
								$this->get_imported_images();
								$this->imported_images[ $photo_id ] = $attachment_id;
								update_option( self::IMPORT_OPTIONS_NAME, $this->imported_images );
								update_post_meta( $attachment_id, 'envato_elements', $photo_id );
								update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( ! empty( $import_options['photo_name'] ) ? $import_options['photo_name'] : '' ) );
							}
						}
					} else {
						//echo 'Temp file not found ' . $temporary_image_name;
					}
					@unlink( $temporary_image_name );
				} else {

					// Confirm elements status:
					License::get_instance()->verify_elements_token();

					$api_error_codes = Elements_API::get_instance()->extract_errors( $data );
					if ( $api_error_codes ) {
						$result = array_merge( $result, $api_error_codes );
					}

				}
			}

		}

		return $result;
	}


	/**
	 * Create a page from a template via the REST API
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function rest_process_insert( $request ) {

		$photo_id                            = $request->get_param( 'itemId' );
		$import_options                      = [];
		$import_options['photo_name']        = $request->get_param( 'photoName' );  // photo name, used for naming our local file.
		$import_options['photo_description'] = $request->get_param( 'photoDescription' );  // photo description, used for seo.
		$import_options['import_type']       = $request->get_param( 'importType' );

		$result = $this->import_photo_by_id( $photo_id, $import_options );

		if ( ! empty( $result['status'] ) && ! empty( $result['attachment_id'] ) ) {


			$result['attachmentData'] = wp_prepare_attachment_for_js( $result['attachment_id'] );

			if ( Elementor::get_instance()->is_deep_integration_enabled() ) {
				$result['data'] = [
					'content' => [
						[
							'id'       => \Elementor\Utils::generate_random_string(),
							'elType'   => 'section',
							'settings' => [],
							'isInner'  => false,
							'elements' => [
								[
									'id'       => \Elementor\Utils::generate_random_string(),
									'elType'   => 'column',
									'elements' => [
										[
											'id'         => \Elementor\Utils::generate_random_string(),
											'elType'     => 'widget',
											'settings'   => [
												'image'      => [
													'url' => wp_get_attachment_url( $result['attachment_id'] ),
													'id'  => $result['attachment_id'],
												],
												'image_size' => 'full',
											],
											'widgetType' => 'image'
										]
									],
									'isInner'  => false
								],
							]
						]
					]
				];
			}
		}

		return new \WP_REST_Response( $result, 200 );

	}

}
