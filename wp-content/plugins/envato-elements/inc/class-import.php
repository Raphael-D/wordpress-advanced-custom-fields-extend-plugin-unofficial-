<?php
/**
 * Envato Elements:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles importing our custom designs.
 *
 * Class Import
 */
class Import extends Base {

	public $logs = [];
	public $errors = [];
	private $_import_ids = false; // todo: move these to options table.
	private $_import_id_namespace = 'all';
	private $local_image_files = [];

	public function import_data( $template, $import_settings ) {

		$imported_data = [
			'post_id' => false,
			'media'   => [],
		];

		set_time_limit( 600 );

		$this->_import_id_namespace = ! empty( $import_settings['transient_namespace'] ) ? $import_settings['transient_namespace'] : 'all';
		$this->log( "Setting transient namespace: " . $this->_import_id_namespace );

		if ( $template ) {

			if ( ! empty( $template['media'] ) ) {

				// see if we can download the zip with all our thumbs first.
				$temporary_zip_file      = false;
				$temporary_zip_folder    = false;
				$this->local_image_files = [];
				if ( ! empty( $template['media_zip'] ) ) {

					$this->log( "Downloading ZIP " . $template['media_zip'] );

					if ( class_exists( '\ZipArchive' ) ) {
						$wp_upload_dir = wp_upload_dir();
						$temp_path     = $wp_upload_dir['basedir'] . '/elements';
						wp_mkdir_p( $temp_path );
						$this->log( " extracting to " . $temp_path );
						$temporary_name = 'template-' . (int) $template['post_id'] . '-' . time() . '';

						require_once( ABSPATH . '/wp-admin/includes/file.php' );
						require_once( ABSPATH . '/wp-admin/includes/media.php' );
						require_once( ABSPATH . '/wp-admin/includes/image.php' );

						$temporary_zip_file = download_url( $template['media_zip'], 30 );
						if ( ! is_wp_error( $temporary_zip_file ) ) {

							// Download and save worked, see if we can extract these images ready for import.
							$temporary_zip_folder = $temp_path . '/' . $temporary_name . '/';
							wp_mkdir_p( $temporary_zip_folder );
							$this->log( " extracting to " . $temporary_zip_folder );

							$zip = new \ZipArchive();
							$zip->open( $temporary_zip_file );
							$zip->extractTo( $temporary_zip_folder );
							$zip->close();
							$this->log( " removing temp zip " . $temporary_zip_file );
							unlink( $temporary_zip_file );

							$temp_files = scandir( $temporary_zip_folder );
							if($temp_files && is_array($temp_files)) {
								$file_names = array_diff( $temp_files, [ '.', '..' ] );
							}else{
								$file_names = [];
							}
							$this->log( " got these files: " . implode( ', ', $file_names ) );

							foreach ( $file_names as $file_name ) {
								$this->local_image_files[ $file_name ] = $temporary_zip_folder . $file_name;
							}


						} else {
							$this->error( "Failed to download zip file: " . $temporary_zip_file->get_error_message() );
							// bad response
						}

					} else {
						// no zip source
						$this->error( "No zip found" );
					}

				}

				// import attachments first before the actual content that will use them.
				foreach ( $template['media'] as $data ) {
					// See if we have a local file for this
					if ( ! empty( $data['url'] ) ) {
						$result = $this->_process_post_data( 'attachment', $data );
						if ( $result && ! is_wp_error( $result ) ) {
							$imported_data['media'][] = $result;
						}
					}
				}

				if ( $temporary_zip_folder ) {
					// todo: remove entire folder.
					$temporary_zip_folder = false;
				}
				unset( $template['media'] );
			}
			if ( ! isset( $template['meta'] ) ) {
				$template['meta'] = [];
			}
			if ( ! empty( $import_settings['post_meta'] ) ) {
				$template['meta'] = array_merge( $import_settings['post_meta'], $template['meta'] );
			}
			$result = $this->_process_post_data( ! empty( $import_settings['post_type'] ) ? $import_settings['post_type'] : 'page', $template );
			if ( $result && ! is_wp_error( $result ) ) {
				// this should be a post ID of the imported template ID.
				$imported_data['post_id'] = $result;
			}

			$this->_handle_post_orphans();

		}

		$imported_data['log']   = $this->logs;
		$imported_data['error'] = $this->errors;

		return $imported_data;

	}

	public function _wp_slash_objects( $value ) {
		return is_string( $value ) ? addslashes( $value ) : $value;
	}

	public function stop_thumb_resizing( $sizes ) {
		$new_sizes = [];
		if ( $sizes ) {
			if ( ! empty( $sizes['thumbnail'] ) ) {
				$new_sizes['thumbnail'] = $sizes['thumbnail'];
			}
			if ( ! empty( $sizes['medium'] ) ) {
				$new_sizes['medium'] = $sizes['medium'];
			}
			if ( ! empty( $sizes['large'] ) ) {
				$new_sizes['large'] = $sizes['large'];
			}
		}

		return $new_sizes;
	}

	public function stop_wp_smush_image() {
		return false;
	}

	private function _process_post_data( $post_type, $post_data, $delayed = 0, $debug = false ) {

		$post_id = false;
		if ( ! is_array( $post_data ) ) {
			return false;
		}
		if ( ! isset( $post_data['post_id'] ) ) {
			$post_data['post_id'] = 0;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$this->log( " Processing $post_type " . $post_data['post_id'] );
		$original_post_data = $post_data;

		if ( $debug ) {
			echo "HERE\n";
		}
		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		if ( ! isset( $post_data['post_status'] ) ) {
			$post_data['post_status'] = 'publish';
		}
		if ( empty( $post_data['post_title'] ) && empty( $post_data['post_name'] ) ) {
			// this is menu items
			$post_data['post_name'] = $post_data['post_id'];
		}

		$post_data['post_type'] = $post_type;

		$post_parent = isset( $post_data['post_parent'] ) ? (int) $post_data['post_parent'] : 0;
		if ( $post_data['post_id'] && $post_parent ) {
			// if we already know the parent, map it to the new local ID
			if ( $this->_imported_post_id( $post_parent ) ) {
				$post_data['post_parent'] = $this->_imported_post_id( $post_parent );
				// otherwise record the parent for later
			} else {
				$this->_post_orphans( intval( $post_data['post_id'] ), $post_parent );
				$post_data['post_parent'] = 0;
			}
		}

		// give them all todays post date
		$post_data['post_date_gmt'] = current_time( 'mysql', 1 );
		$post_data['post_date']     = current_time( 'mysql', 0 );

		switch ( $post_type ) {
			case 'attachment':
				// import media via url
				if ( ! empty( $post_data['guid'] ) ) {

					// check if this has already been imported.
					$remote_url = ! empty( $post_data['url'] ) ? $post_data['url'] : $post_data['guid'];

					global $wpdb;
					$existing_image = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_envato_image_source' AND  meta_value = %s LIMIT 1", $remote_url ), ARRAY_A );
					if ( $existing_image && ! empty( $existing_image[0]['post_id'] ) ) {

						$this->log( "Found existing image " . $existing_image[0]['post_id'] . " for a search on $remote_url " );
						$post_id = $existing_image[0]['post_id'];

					} else {

						$this->log( "No existing image found for $remote_url " );

						$post_data['upload_date'] = date( 'Y/m', strtotime( $post_data['post_date_gmt'] ) );
						$upload                   = $this->_fetch_remote_file( $remote_url, $post_data );

						if ( ! is_array( $upload ) || is_wp_error( $upload ) ) {
							$this->error( "Failed to get $remote_url for some reason " );

							return $upload;
						}

						$info = wp_check_filetype( $upload['file'] );
						if ( $info ) {
							$post_data['post_mime_type'] = $info['type'];
						} else {
							$this->error( "Failed to get image data for " . $upload['file'] );

							return false;
						}

						$post_data['guid'] = $upload['url'];

						// as per wp-admin/includes/upload.php
						$post_id = wp_insert_attachment( $post_data, $upload['file'] );
						if ( $post_id ) {

							$this->log( "Converted image into attachment $post_id " );

							// Disable all thumbnail generation
							add_filter( 'intermediate_image_sizes_advanced', [ $this, 'stop_thumb_resizing' ], 99, 1 );
							add_filter( 'wp_smush_image', [ $this, 'stop_wp_smush_image' ], 99, 1 );
							$attachment_meta = wp_generate_attachment_metadata( $post_id, $upload['file'] );
							wp_update_attachment_metadata( $post_id, $attachment_meta );
							remove_filter( 'intermediate_image_sizes_advanced', [ $this, 'stop_thumb_resizing' ], 99 );
							remove_filter( 'wp_smush_image', [ $this, 'stop_wp_smush_image' ], 99 );

							/*if ( ! empty( $upload['possible_thumbnails'] ) ) {
								add_filter( 'intermediate_image_sizes_advanced', [ $this, 'stop_thumb_resizing' ], 99, 1 );
								$attachment_meta = wp_generate_attachment_metadata( $post_id, $upload['file'] );

								$regenerate_certain_sizes = [];

								$editor = wp_get_image_editor( $upload['file'] );
								if ( ! is_wp_error( $editor ) ) {

									$current_thumb_size = $editor->get_size();
									if ( $current_thumb_size && $current_thumb_size['width'] > 0 && $current_thumb_size['height'] > 0 ) {

										$current_thumb_extension = strtolower( pathinfo( $upload['file'], PATHINFO_EXTENSION ) );
										$current_thumb_basename  = wp_basename( $upload['file'], ".$current_thumb_extension" );

										if ( empty( $attachment_meta['sizes'] ) ) {
											$attachment_meta['sizes'] = [];
										}
										foreach ( $this->registered_image_sizes as $size_name => $size_details ) {
											$found_image_size = false;
											$dims             = image_resize_dimensions( $current_thumb_size['width'], $current_thumb_size['height'], $size_details['width'], $size_details['height'], false );
											if ( $dims ) {
												list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;
												if ( $dst_w && $dst_h ) {
													foreach ( $upload['possible_thumbnails'] as $possible_thumbnail ) {
														if ( $possible_thumbnail['width'] === $dst_w && $possible_thumbnail['height'] === $dst_h ) {
															// move the file across into the right location:
															$thumb_folder        = dirname( $upload['file'] );
															$new_thumb_file_name = $current_thumb_basename . '-' . $dst_w . 'x' . $dst_h . '.jpg';
															if ( copy( $possible_thumbnail['path'], $thumb_folder . '/' . $new_thumb_file_name ) ) {

																$this->log("Used ZIP asset for ${dst_w}x${dst_h} thumbnail ");

																$attachment_meta['sizes'][ $size_name ] = [
																	'file'      => $new_thumb_file_name,
																	'width'     => $dst_w,
																	'height'    => $dst_h,
																	'mime-type' => 'image/jpeg',
																];
																$found_image_size                       = true;
															}

														}
													}
												}
											}
											if ( ! $found_image_size ) {
												$this->log("No ZIP asset found for size $size_name, generating manually ");
												$regenerate_certain_sizes[ $size_name ] = $size_details;
											}
										}
									}

								}

								// This generates any missing thumb sizes from the provided zip.
								if ( $regenerate_certain_sizes ) {
									$generated_meta_sizes = $editor->multi_resize( $regenerate_certain_sizes );
									if ( $generated_meta_sizes ) {
										$attachment_meta['sizes'] = array_merge( $attachment_meta['sizes'], $generated_meta_sizes );
									}
								}

								wp_update_attachment_metadata( $post_id, $attachment_meta );
								remove_filter( 'intermediate_image_sizes_advanced', [ $this, 'stop_thumb_resizing' ], 99 );

							} else {
								// otherwise we generate all the things normally.
								$this->log("No ZIP assets found at all, generating them all.");
								$attachment_meta = wp_generate_attachment_metadata( $post_id, $upload['file'] );
								wp_update_attachment_metadata( $post_id, $attachment_meta );

							}*/

							// remap resized image URLs, works by stripping the extension and remapping the URL stub.
							if ( preg_match( '!^image/!', $info['type'] ) ) {
								$parts = pathinfo( $remote_url );
								$name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

								$parts_new = pathinfo( $upload['url'] );
								$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

								$this->_imported_post_id( $parts['dirname'] . '/' . $name, $parts_new['dirname'] . '/' . $name_new );
							}
						}
					}

					if ( $post_id ) {

						$this->_imported_post_id( $remote_url, wp_get_attachment_url( $post_id ) );
						$this->_imported_post_id( $post_data['guid'], wp_get_attachment_url( $post_id ) );

						if ( ! empty( $post_data['meta'] ) ) {
							foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
								if ( '_wp_attached_file' !== $meta_key && ! empty( $meta_val ) ) {
									update_post_meta( $post_id, $meta_key, map_deep( $meta_val, [ $this, '_wp_slash_objects' ] ) );
								}
							}
						}

						$this->_imported_post_id( $post_data['post_id'], $post_id );
					}
				}
				break;
			default:
				// work out if we have to delay this post insertion
				if ( ! empty( $post_data['meta'] ) && is_array( $post_data['meta'] ) ) {

					foreach ( [ '_fl_builder_data', '_fl_builder_data_settings' ] as $serialize_key ) {
						if ( ! empty( $post_data['meta'][ $serialize_key ] ) ) {
							$post_data['meta'][ $serialize_key ] = maybe_unserialize( $post_data['meta'][ $serialize_key ] );
						}
					}

					// fix for double json encoded stuff:
					foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
						if ( is_string( $meta_val ) && strlen( $meta_val ) && '[' === $meta_val[0] ) {
							$test_json = json_decode( $meta_val, true );
							if ( is_array( $test_json ) ) {
								$post_data['meta'][ $meta_key ] = $test_json;
							}
						}
					}

					array_walk_recursive( $post_data['meta'], [ $this, '_elementor_id_import' ] );
				}

				$post_data['post_content'] = $this->_parse_gallery_shortcode_content( $post_data['post_content'] );

				// we have to fix up all the visual composer inserted image ids
				$replace_post_id_keys = [
					'parallax_image',
					'dtbwp_row_image_top',
					'dtbwp_row_image_bottom',
					'image',
					'item', // vc grid
					'post_id',
				];
				foreach ( $replace_post_id_keys as $replace_key ) {
					if ( preg_match_all( '# ' . $replace_key . '="(\d+)"#', $post_data['post_content'], $matches ) ) {
						foreach ( $matches[0] as $match_id => $string ) {
							$new_id = $this->_imported_post_id( $matches[1][ $match_id ] );
							if ( $new_id ) {
								$post_data['post_content'] = str_replace( $string, ' ' . $replace_key . '="' . $new_id . '"', $post_data['post_content'] );
							} else {
								$this->error( 'Unable to find POST replacement for ' . $replace_key . '="' . $matches[1][ $match_id ] . '" in content.' );
								if ( $delayed ) {
									// already delayed, unable to find this meta value, insert it anyway.
								} else {

									$this->error( 'Adding ' . $post_data['post_id'] . ' to delay listing.' );

									return false;
								}
							}
						}
					}
				}

				if ( ! empty( $post_data['Update_Existing_ID'] ) ) {
					$existing = get_post( $post_data['Update_Existing_ID'] );
					if ( $existing && $existing->post_type === $post_type ) {
						$post_data['ID'] = $post_data['Update_Existing_ID'];
						unset( $post_data['Update_Existing_ID'] );
						$post_id = wp_update_post( $post_data, true );
					} else {
						die( 'Missmatch in post type' );
					}
				} else {
					$post_id = wp_insert_post( $post_data, true );
				}
				if ( ! is_wp_error( $post_id ) && $post_id > 0 ) {
					$this->_imported_post_id( $post_data['post_id'], $post_id );
					// add/update post meta
					if ( ! empty( $post_data['meta'] ) ) {
						foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
							// if the post has a featured image, take note of this in case of remap
							if ( '_thumbnail_id' === $meta_key ) {
								// find this inserted id and use that instead.
								$inserted_id = $this->_imported_post_id( intval( $meta_val ) );
								if ( $inserted_id ) {
									$meta_val = $inserted_id;
								}
							}
							update_post_meta( $post_id, $meta_key, map_deep( $meta_val, [ $this, '_wp_slash_objects' ] ) );
						}
					}

					if ( ! empty( $post_data['meta']['_elementor_data'] ) || ! empty( $post_data['meta']['_elementor_css'] ) ) {
						$this->elementor_post( $post_id );
					}

					// Trigger post save so any plugins etc..that hook in can do their thing.
					// We might not need the elementor css thing above
					wp_update_post(
						[
							'ID' => $post_id,
						]
					);
				}

				break;
		}

		return $post_id;
	}

	public function log( $message ) {
		$this->logs[] = $message;
	}

	public function error( $message ) {
		$this->log( 'Error: ' . $message );
	}

	private function _imported_post_id( $original_id = false, $new_id = false ) {
		if ( is_array( $original_id ) || is_object( $original_id ) ) {
			return false;
		}
		$this->_import_ids = get_transient( 'envatoelementspostids' );
		if ( ! is_array( $this->_import_ids ) ) {
			$this->_import_ids = [];
		}
		if ( ! isset( $this->_import_ids[ $this->_import_id_namespace ] ) ) {
			$this->_import_ids[ $this->_import_id_namespace ] = [];
		}
		if ( $original_id && $new_id ) {
			if ( ! isset( $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] ) ) {
				$this->log( 'Insert old ID ' . $original_id . ' as new ID: ' . $new_id );
			} elseif ( $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] !== $new_id ) {
				$this->error( 'Replacement OLD ID ' . $original_id . ' changed from ' . $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] . ' to new ID: ' . $new_id );
			}
			$this->_import_ids[ $this->_import_id_namespace ][ $original_id ] = $new_id;
			set_transient( 'envatoelementspostids', $this->_import_ids, 60 * 60 * 24 ); // todo: save to options in a collection namespace.
		} elseif ( $original_id && isset( $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] ) ) {
			if ( is_numeric( $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] ) && intval( $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] ) === $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] ) {
				// we're doing a post ID, make sure it still exists before returning it
				// ie if someone deleted it.
				$existing = get_post( $this->_import_ids[ $this->_import_id_namespace ][ $original_id ] );
				if ( ! $existing || ( 'inherit' !== $existing->post_status && 'publish' !== $existing->post_status && 'draft' !== $existing->post_status ) ) {
					return false;
				}
			} else {
				// ensure the media file still exists based on guid.
			}

			return $this->_import_ids[ $this->_import_id_namespace ][ $original_id ];
		} elseif ( false === $original_id ) {
			return $this->_import_ids[ $this->_import_id_namespace ];
		}

		return false;
	}

	private function _post_orphans( $original_id = false, $missing_parent_id = false ) {
		$post_ids = get_transient( 'envatoelementspostorphans' );
		if ( ! is_array( $post_ids ) ) {
			$post_ids = [];
		}
		if ( $missing_parent_id ) {
			$post_ids[ $original_id ] = $missing_parent_id;
			set_transient( 'envatoelementspostorphans', $post_ids, 60 * 60 );
		} elseif ( $original_id && isset( $post_ids[ $original_id ] ) ) {
			return $post_ids[ $original_id ];
		} elseif ( false === $original_id ) {
			return $post_ids;
		}

		return false;
	}

	private function _fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url
		$file_name = basename( $url );
		$upload    = false;

		$this->log( "Importing $file_name " );
		if ( $file_name && ! empty( $this->local_image_files[ $file_name ] ) && file_exists( $this->local_image_files[ $file_name ] ) && filesize( $this->local_image_files[ $file_name ] ) > 10 ) {
			$this->log( "Found $file_name in ZIP cache" );

			$file_data = file_get_contents( $this->local_image_files[ $file_name ] );
			$upload    = wp_upload_bits( $file_name, 0, $file_data, $post['upload_date'] );
			if ( ! $upload || $upload['error'] ) {
				return new \WP_Error( 'upload_dir_error', $upload['error'] );
			}

			$file_ext               = strtolower( pathinfo( $this->local_image_files[ $file_name ], PATHINFO_EXTENSION ) );
			$file_name_no_extension = wp_basename( $file_name, ".$file_ext" );

			$upload['possible_thumbnails'] = [];
			foreach ( $this->local_image_files as $possible_thumb_name => $possible_thumb_path ) {

				if ( preg_match( '#' . preg_quote( $file_name_no_extension, '#' ) . '-(\d+)x(\d+)\.\w{3}#', $possible_thumb_name, $matches ) ) {
					$upload['possible_thumbnails'][] = [
						'name'   => $possible_thumb_name,
						'path'   => $possible_thumb_path,
						'width'  => (int) $matches[1],
						'height' => (int) $matches[2],
					];
				}
			}

			return $upload;
		}

		$this->log( " $file_name NOT found in ZIP cache, downloading separately. " );

		if ( ! $upload || $upload['error'] ) {
			// get placeholder file in the upload dir with a unique, sanitized filename
			$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
			if ( $upload['error'] ) {
				$this->error( "Failed to create temp file " . $upload['error'] );

				return new \WP_Error( 'upload_dir_error', $upload['error'] );
			}

			// fetch the remote url and write it to the placeholder file
			// $headers = wp_get_http( $url, $upload['file'] );
			$max_size = (int) apply_filters( 'import_attachment_size_limit', 0 );

			// we check if this file is uploaded locally in the source folder.
			$this->log( "Downloading $url " );
			$response = wp_safe_remote_get(
				$url, [
					'timeout'    => 6,
					'sslverify'  => false, // Some hosts require this unfortunately :(
					'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
				]
			);
			if ( is_array( $response ) && ! empty( $response['body'] ) && 200 === intval( $response['response']['code'] ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				$headers = $response['headers'];
				WP_Filesystem();
				global $wp_filesystem;
				$wp_filesystem->put_contents( $upload['file'], $response['body'] );
			} else {
				// required to download file failed.
				unlink( $upload['file'] );

				return new \WP_Error( 'import_file_error', esc_html__( 'Remote server did not respond', 'envato-elements' ) );
			}

			$filesize = filesize( $upload['file'] );

			if ( isset( $headers['content-length'] ) && intval( $filesize ) !== intval( $headers['content-length'] ) ) {
				unlink( $upload['file'] );

				return new \WP_Error( 'import_file_error', esc_html__( 'Remote file is incorrect size', 'envato-elements' ) );
			}

			if ( 0 === $filesize ) {
				unlink( $upload['file'] );

				return new \WP_Error( 'import_file_error', esc_html__( 'Zero size file downloaded', 'envato-elements' ) );
			}

			if ( ! empty( $max_size ) && $filesize > $max_size ) {
				unlink( $upload['file'] );

				// translators: %s is file size limit (e.g. 100MB)
				return new \WP_Error( 'import_file_error', sprintf( esc_html__( 'Remote file is too large, limit is %s', 'envato-elements' ), size_format( $max_size ) ) );
			}
		}

		return $upload;
	}

	private function _parse_gallery_shortcode_content( $content ) {
		// we have to format the post content. rewriting images and gallery stuff
		$replace      = $this->_imported_post_id();
		$urls_replace = [];
		foreach ( $replace as $key => $val ) {
			if ( $key && $val && ! is_numeric( $key ) && ! is_numeric( $val ) ) {
				$urls_replace[ $key ] = $val;
			}
		}
		if ( $urls_replace ) {
			uksort( $urls_replace, [ &$this, 'cmpr_strlen' ] );
			foreach ( $urls_replace as $from_url => $to_url ) {
				$content = str_replace( $from_url, $to_url, $content );
			}
		}
		if ( preg_match_all( '#\[gallery[^\]]*\]#', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match( '#ids="([^"]+)"#', $string, $ids_matches ) ) {
					$ids = explode( ',', $ids_matches[1] );
					foreach ( $ids as $key => $val ) {
						$new_id = $val ? $this->_imported_post_id( $val ) : false;
						if ( ! $new_id ) {
							unset( $ids[ $key ] );
						} else {
							$ids[ $key ] = $new_id;
						}
					}
					$new_ids = implode( ',', $ids );
					$content = str_replace( $ids_matches[0], 'ids="' . $new_ids . '"', $content );
				}
			}
		}
		// contact form 7 id fixes.
		if ( preg_match_all( '#\[contact-form-7[^\]]*\]#', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match( '#id="(\d+)"#', $string, $id_match ) ) {
					$new_id = $this->_imported_post_id( $id_match[1] );
					if ( $new_id ) {
						$content = str_replace( $id_match[0], 'id="' . $new_id . '"', $content );
					} else {
						// no imported ID found. remove this entry.
						$content = str_replace( $matches[0], '(insert contact form here)', $content );
					}
				}
			}
		}

		return $content;
	}

	public function elementor_post( $post_id = false ) {

		// regenrate the CSS for this Elementor post
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$post_css = new \Elementor\Core\Files\CSS\Post( $post_id );
			$post_css->update();
		}

	}


	// return the difference in length between two strings
	private function _handle_post_orphans() {
		$orphans = $this->_post_orphans();
		foreach ( $orphans as $original_post_id => $original_post_parent_id ) {
			if ( $original_post_parent_id ) {
				if ( $this->_imported_post_id( $original_post_id ) && $this->_imported_post_id( $original_post_parent_id ) ) {
					$post_data                = [];
					$post_data['ID']          = $this->_imported_post_id( $original_post_id );
					$post_data['post_parent'] = $this->_imported_post_id( $original_post_parent_id );
					wp_update_post( $post_data );
					$this->_post_orphans( $original_post_id, 0 ); // ignore future
				}
			}
		}
	}

	public function cmpr_strlen( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}

	private function _elementor_id_import( &$item, $key ) {
		if ( 'id' === $key && ! empty( $item ) && is_numeric( $item ) ) {
			// check if this has been imported before
			$this->log( " - mapping ID $item " );
			$new_meta_val = $this->_imported_post_id( $item );
			if ( $new_meta_val ) {
				$this->log( "  --- mapped to $new_meta_val " );
				$item = $new_meta_val;
			} else {
				$this->log( "  --- Failed to map ID :( " );
			}
		}
		if ( ( 'page' === $key || 'page_id' === $key ) && ! empty( $item ) ) {

			if ( false !== strpos( $item, 'p.' ) ) {
				$new_id = str_replace( 'p.', '', $item );
				// check if this has been imported before
				$new_meta_val = $this->_imported_post_id( $new_id );
				if ( $new_meta_val ) {
					$item = 'p.' . $new_meta_val;
				}
			} elseif ( is_numeric( $item ) ) {
				// check if this has been imported before
				$new_meta_val = $this->_imported_post_id( $item );
				if ( $new_meta_val ) {
					$item = $new_meta_val;
				}
			}
		}
		if ( 'post_id' === $key && ! empty( $item ) && is_numeric( $item ) ) {
			// check if this has been imported before
			$new_meta_val = $this->_imported_post_id( $item );
			if ( $new_meta_val ) {
				$item = $new_meta_val;
			}
		}
		if ( 'url' === $key && ! empty( $item ) ) {
			// check if this has been imported before
			$new_meta_val = $this->_imported_post_id( $item );
			if ( $new_meta_val ) {
				$item = $new_meta_val;
			}
		}
		if ( ( 'shortcode' === $key || 'editor' === $key ) && ! empty( $item ) ) {
			// we have to fix the [contact-form-7 id=133] shortcode issue.
			$item = $this->_parse_gallery_shortcode_content( $item );

		}
	}


	/**
	 * Check if a given request has access to update a setting
	 *
	 * @param\ WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|bool
	 */
	public function rest_permission_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * This registers all our WP REST API endpoints for the react front end
	 *
	 * @param $namespace
	 */

	public function init_rest_endpoints( $namespace ) {

		$endpoints = [
			'/import/status'    => [
				\WP_REST_Server::READABLE => 'rest_get_imports',
			],
			'/import/get_pages' => [
				\WP_REST_Server::READABLE => 'rest_get_pages',
			],
		];

		foreach ( $endpoints as $endpoint => $details ) {
			foreach ( $details as $method => $callback ) {
				register_rest_route(
					$namespace, $endpoint, [
						[
							'methods'             => $method,
							'callback'            => [ $this, $callback ],
							'permission_callback' => [ $this, 'rest_permission_check' ],
							'args'                => [],
						],
					]
				);
			}
		}

	}

	/**
	 * Import a template via the REST api
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function rest_get_imports( $request ) {

		$result = [
			'data' => [
				'count'                => 0,
				'pending_import_count' => 0,
				'pending_insert_count' => 0,
				'importing'            => false,
				'inserting'            => false,
				'imports'              => [],
			],
		];

		// For now we just process 1 template per request. We apply a lock on a template using a post meta field to help reduce double ups if two tabs are open.
		$all_templates = CPT_Kits::get_instance()->get_imported_templates();
		if ( $all_templates ) {
			$pending_import_count = 0;
			$pending_insert_count = 0;

			// Only process one template at a time.
			$next_to_process    = false;
			$processing_already = 0;
			$processing_items   = [];
			$meta_lock_key      = 'elements_import_lock';
			$meta_lock_timeout  = '2 minutes';

			foreach ( $all_templates as $template_id => $template ) {
				if ( ! $template['imported'] ) {
					$pending_import_count ++;
					$lock_status = get_post_meta( $template['ID'], $meta_lock_key, true );
					if ( $lock_status && $lock_status >= strtotime( '-' . $meta_lock_timeout ) ) {
						$processing_already ++;
						$template['time']   = $lock_status - strtotime( '-' . $meta_lock_timeout );
						$processing_items[] = $template;
					} elseif ( ! $next_to_process ) {
						$next_to_process = $template;
					}
				}
				if ( ! empty( $template['inserted'] ) ) {
					// array of insert requests (pending or completed)
					foreach ( $template['inserted'] as $insert ) {
						if ( ! $insert['completed'] ) {
							$pending_insert_count ++;
						}
					}
				}
			}

			if ( $next_to_process && ! $processing_already ) {
				update_post_meta( $next_to_process['ID'], $meta_lock_key, time() );
			}

			$result['data']['count']                   = count( $all_templates );
			$result['data']['processing_import_count'] = $processing_already;
			$result['data']['processing_imports']      = $processing_items;
			$result['data']['pending_import_count']    = $pending_import_count;
			$result['data']['pending_insert_count']    = $pending_insert_count;
			$result['data']['importing']               = ! ! $processing_already;
			$result['data']['inserting']               = ! ! $pending_insert_count;
			$result['data']['imports']                 = $all_templates;
			$result['data']['next_to_process']         = $next_to_process;
		}

		return new \WP_REST_Response( $result, 200 );

	}

	private function _get_nested_pages( $parent = 0, $pages, $nested = 0 ) {
		$return = [];
		foreach ( $pages as $page ) {
			if ( $parent === $page->post_parent ) {
				$return[] = [
					'ID'         => $page->ID,
					'post_title' => str_repeat( '- ', $nested ) . $page->post_title,
				];
				// find nested children.
				$return = array_merge( $return, $this->_get_nested_pages( $page->ID, $pages, $nested + 1 ) );
			}
		}

		return $return;
	}

	/**
	 * Get a list of pages for inserting content on.
	 *
	 * @param \WP_REST_Request $request Template ID we wish to insert on
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function rest_get_pages( $request ) {

		$result = [
			'pageList' => [],
		];

		$pages              = get_pages();
		$result['pageList'] = $this->_get_nested_pages( 0, $pages );

		return new \WP_REST_Response( $result, 200 );

	}


}

