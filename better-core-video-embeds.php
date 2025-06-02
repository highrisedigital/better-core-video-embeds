<?php
/*
Plugin Name: Better Core Video Embeds
Description: A plugin which enhances the core video embeds for Youtube and Vimeo videos by not loading unnecessary scripts until they are needed.
Requires at least: 6.0
Requires PHP: 7.0
Version: 1.3.7
Author: Highrise Digital
Author URI: https://highrise.digital/
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: better-core-video-embeds
*/

// define variable for path to this plugin file.
define( 'HD_BCVE_LOCATION', dirname( __FILE__ ) );
define( 'HD_BCVE_LOCATION_URL', plugins_url( '', __FILE__ ) );
define( 'HD_BCVE_VERSION', '1.3.7' );

/**
 * Function to run on plugins load.
 */
function hd_bcve_plugins_loaded() {

	$locale = apply_filters( 'plugin_locale', get_locale(), 'better-core-video-embeds' );
	load_textdomain( 'better-core-video-embeds', WP_LANG_DIR . '/better-core-video-embeds/better-core-video-embeds-' . $locale . '.mo' );
	load_plugin_textdomain( 'better-core-video-embeds', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );

}

add_action( 'plugins_loaded', 'hd_bcve_plugins_loaded' );

/**
 * Enqueues the frontend JS for the plugin.
 */
function hd_bcve_register_enqueue_scripts() {

	// register the script for the front end.
	wp_register_script(
		'better-core-video-embeds-js',
		HD_BCVE_LOCATION_URL . '/assets/js/better-core-video-embeds.min.js',
		false,
		HD_BCVE_VERSION,
		true
	);

}

add_action( 'init', 'hd_bcve_register_enqueue_scripts' );

/**
 * Add the front end JS to the core/embed block metadata.
 *
 * @param array $metadata The block metadata.
 * @return array $metadata The block metadata.
 */
function hd_bcve_filter_embed_metadata( $metadata ) {

	// if this is the core/embed block.
	if ( 'core/embed' === $metadata['name'] ) {

		$metadata['viewScript'] = array_merge(
			(array) ( $metadata['viewScript'] ?? array() ),
			['better-core-video-embeds-js']
		);

	}

	// return the metadata.
	return $metadata;

}

add_filter( 'block_type_metadata', 'hd_bcve_filter_embed_metadata' );

/**
 * Enqueues the block editor JS for the plugin.
 */
function hd_bcve_enqueue_block_editor_assets() {

	// get the assets file.
	$asset_file = include( plugin_dir_path( __FILE__ ) . '/build/index.asset.php' );

	// enqueue the block editor script.
	wp_enqueue_script(
		'hd-bcve-block-editor-js',
		HD_BCVE_LOCATION_URL . '/build/index.js',
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);

}

add_action( 'enqueue_block_editor_assets', 'hd_bcve_enqueue_block_editor_assets' );

/**
 * Register a stylesheet for this block.
 */
function hd_bcve_register_block_style() {

	// register the block styles.
	wp_register_style(
		'better-core-video-embeds-styles',
		HD_BCVE_LOCATION_URL . '/assets/css/better-core-video-embeds.min.css',
		[],
		HD_BCVE_VERSION
	);

	// enqueue the registered block style.
	wp_enqueue_block_style(
		'core/embed',
		[
			'handle' => 'better-core-video-embeds-styles',
		]
	);

}

add_action( 'init', 'hd_bcve_register_block_style' );

/**
 * Filters the code embed block output for improved performance on Youtube videos.
 *
 * @param string   $block_content The block content.
 * @param array    $block         The full block, including name and attributes.
 * @param WP_Block $instance      The block instance.
 *
 * @return string  $block_content The block content.
 */
function hd_bcve_render_core_embed_block( $block_content, $block, $instance ) {

	// if the provider slug name is empty.
	if ( empty( $block['attrs']['providerNameSlug'] ) ) {
		return $block_content;
	}

	// if for some reason there is no embed URL.
	if ( empty( $block['attrs']['url'] ) ) {
		return $block_content;
	}

	// create a default video id, url and thumbnail url.
	$video_id = '';
	$thumbnail_url = '';

	// if we have a thumbnail ID attribute.
	if ( ! empty( $block['attrs']['thumbnailId'] ) ) {

		// get the thumbnail URL from the thumbnail ID.
		$thumbnail_url = wp_get_attachment_image_url( $block['attrs']['thumbnailId'], 'full' );

	}

	// grab the video id.
	$video_url = $block['attrs']['url'];
	$parsed_video_url = parse_url( $video_url );

	// switch based on the host.
	switch( $parsed_video_url['host'] ) {

		// for standard youtube URLs
		case 'www.youtube.com':
		case 'youtube.com':

			if ( empty( $parsed_video_url['query'] ) ) {

				// if we have a path.
				if ( empty( $parsed_video_url['path'] ) ) {
					return $block_content;
				}

				// remove live/embed path and trailing slash.
				$video_id = str_replace( array( 'live/', 'embed/', '/' ), '', $parsed_video_url['path'] );

			} else {

				// parse the query part of the URL into its arguments.
				parse_str( $parsed_video_url['query'], $video_url_query_args );

				// if we cannot find a youtube video id.
				if ( empty( $video_url_query_args['v'] ) ) {
					return $block_content;
				}

				// set the video id to the v query arg.
				$video_id = $video_url_query_args['v'];
			}

			// if we don't have a custom thumbnail.
			if ( empty( $thumbnail_url ) ) {

				// get the youtube thumbnail url.
				$thumbnail_url = hd_bcve_get_youtube_video_thumbnail_url( $video_id );

			}

			// break out the switch.
			break;

		// for youtube short urls.
		case 'youtu.be':

			// if we have a path.
			if ( empty( $parsed_video_url['path'] ) ) {
				return $block_content;
			}

			// remove the preceeding slash.
			$video_id = str_replace( '/', '', $parsed_video_url['path'] );

			// if we don't have a custom thumbnail.
			if ( empty( $thumbnail_url ) ) {

				// get the youtube thumbnail url.
				$thumbnail_url = hd_bcve_get_youtube_video_thumbnail_url( $video_id );

			}

			// break out the switch.
			break;

		// for vimeo urls.
		case 'vimeo.com':
		case 'www.vimeo.com':

			// if we have a path.
			if ( empty( $parsed_video_url['path'] ) ) {
				return $block_content;
			}

			// remove the preceeding slash.
			$video_id = str_replace( '/', '', $parsed_video_url['path'] );

			// if we don't have a custom thumbnail.
			if ( empty( $thumbnail_url ) ) {

				// get the vimeo thumbnail url for this video.
				$thumbnail_url = hd_bcve_get_vimeo_video_thumbnail_url( $video_id );

			}

			// break out the switch.
			break;

		// for vimeo urls.
		case 'www.dailymotion.com':
		case 'dailymotion.com':

			// if we have a path.
			if ( empty( $parsed_video_url['path'] ) ) {
				return $block_content;
			}

			// remove the preceeding slash.
			$video_id = str_replace( '/video/', '', $parsed_video_url['path'] );

			// if we don't have a custom thumbnail.
			if ( empty( $thumbnail_url ) ) {

				// get the vimeo thumbnail url for this video.
				$thumbnail_url = hd_bcve_get_dailymotion_video_thumbnail_url( $video_id );

			}

			// break out the switch.
			break;

	}

	// if we don't have a video id.
	if ( '' === $video_id ) {
		return $block_content;
	}

	// if we don't have a video thumbnail url.
	if ( '' === $thumbnail_url ) {
		return $block_content;
	}

	// create an array of classes to add to the placeholder image wrapper.
	$wrapper_classes = [
		'wp-block-image',
		'hd-bcve-wrapper',
		'hd-bcve-wrapper-js',
		'is--' . $block['attrs']['providerNameSlug'],
	];

	// if we have classNames on the embed block.
	if ( ! empty( $block['attrs']['className'] ) ) {

		// explode the className string into array.
		$class_names = explode( ' ', $block['attrs']['className'] );

		// merge the class names into the figures classes array.
		$wrapper_classes = array_merge( $wrapper_classes, $class_names );

	}

	// if the embed block has an alignment.
	if ( ! empty( $block['attrs']['align'] ) ) {

		// add the alignment class to the figure classes.
		$wrapper_classes[] = 'align' . $block['attrs']['align'];

	}

	// allow the classes to be filtered.
	$wrapper_classes = apply_filters( 'hd_bcve_wrapper_classes', $wrapper_classes, $block, $video_id, $thumbnail_url );

	/**
	 * Lets grab the video caption.
	 */

	// set a default video caption.
	$video_caption = '';

	/**
	 * Perform a regular expression match on the block content to find the caption.
	 * Should match any figcaption tag with any attributes and any content.
	 */
  	preg_match( '/<figcaption(?:\s+.*?)?>(.*?)<\/figcaption>/s', $block_content, $match );

	// if we have a match.
	if ( ! empty( $match[1] ) ) {

		// set the caption to the text content of the matched element - the video caption.
		$video_caption = $match[1];

	}

	// buffer the output as we need to return not echo.
	ob_start();

	/**
	 * Fires and action to which the new block markup is added too.
	 *
	 * @hooked hd_bvce_open_markup_figure_element - 10
	 * @hooked hd_bcve_add_video_play_button - 20
	 * @hooked hd_bcve_add_video_thumbnail_markup - 30
	 * @hooked hd_bcve_add_video_caption_markup - 35
	 * @hooked hd_bvce_close_markup_figure_element - 40
	 * @hooked hd_bcve_add_original_embed_template - 50
	 */
	do_action( 'hd_bcve_video_thumbnail_markup', $block, $video_id, $thumbnail_url, $wrapper_classes, $video_caption );

	// return the new block markup.
	return ob_get_clean();

}

add_filter( 'render_block_core/embed', 'hd_bcve_render_core_embed_block', 10, 3 );

/**
 * Return the youtube video thumbnail url.
 *
 * @param string  $video_id The ID of the video.
 * @return string $url      The URL of the thumbnail or an empty string if no URL found.
 */
function hd_bcve_get_youtube_video_thumbnail_url( $video_id = '' ) {

	// if we have no video id.
	if ( '' === $video_id ) {
		return '';
	}

	// get the URL from the transient.
	$image_url = get_transient( 'hd_bcve_' . $video_id );

	// if we don't have a transient.
	if ( false === $image_url ) {

		// set the normal image url.
		$image_url = 'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/mqdefault.jpg';

		// check if there is a max res image available.
		$max_res_img = wp_remote_get(
			'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/maxresdefault.jpg'
		);

		// if the request to the hi res image doesn't errors and returns a http 200 response code.
		if ( ( ! is_wp_error( $max_res_img ) ) && ( 200 === wp_remote_retrieve_response_code( $max_res_img ) ) ) {

			// set the name as max res.
			$image_url = 'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/maxresdefault.jpg';

		}

		// set the transient, storing the image url.
		set_transient( 'hd_bcve_' . $video_id, $image_url, DAY_IN_SECONDS );

	}

	// return the thumbnail url.
	return apply_filters( 'hd_bcve_youtube_video_thumbnail_url', $image_url, $video_id );

}

/**
 * Return the vimeo video thumbnail url.
 *
 * @param string  $video_id The ID of the video.
 * @return string $url      The URL of the thumbnail or an empty string if no URL found.
 */
function hd_bcve_get_vimeo_video_thumbnail_url( $video_id = '' ) {

	// if we have no video id.
	if ( '' === $video_id ) {
		return '';
	}

	// get the URL from the transient.
	$image_url = get_transient( 'hd_bcve_' . $video_id );

	// if we don't have a transient.
	if ( false === $image_url ) {

		// get the video details from the api.
		$video_details = wp_remote_get(
			'https://vimeo.com/api/v2/video/' . esc_attr( $video_id ) . '.json'
		);

		// if the request to the hi res image errors or returns anything other than a http 200 response code.
		if ( ( is_wp_error( $video_details )) && ( 200 !== wp_remote_retrieve_response_code( $video_details ) ) ) {
			return '';
		}

		// grab the body of the response.
		$video_details = json_decode(
			wp_remote_retrieve_body(
				$video_details
			)
		);

		// get the image url from the json.
		$image_url = $video_details[0]->thumbnail_large;

		// set the transient, storing the image url.
		set_transient( 'hd_bcve_' . $video_id, $image_url, DAY_IN_SECONDS );

	}

	// return the url.
	return apply_filters( 'hd_bcve_vimeo_video_thumbnail_url', $image_url, $video_id );

}

/**
 * Return the dailymotion video thumbnail url.
 *
 * @param string  $video_id The ID of the video.
 * @return string $url      The URL of the thumbnail or an empty string if no URL found.
 */
function hd_bcve_get_dailymotion_video_thumbnail_url( $video_id = '' ) {

	// if we have no video id.
	if ( '' === $video_id ) {
		return '';
	}

	// get the URL from the transient.
	$image_url = get_transient( 'hd_bcve_' . $video_id );

	// if we don't have a transient.
	if ( false === $image_url ) {

		// get the video details from the api.
		$video_details = wp_remote_get(
			'https://api.dailymotion.com/video/' . $video_id . '?fields=thumbnail_url'
		);

		// if the request to the hi res image errors or returns anything other than a http 200 response code.
		if ( ( is_wp_error( $video_details )) && ( 200 !== wp_remote_retrieve_response_code( $video_details ) ) ) {
			return '';
		}

		// grab the body of the response.
		$video_details = json_decode(
			wp_remote_retrieve_body(
				$video_details
			)
		);

		// get the image url from the json.
		$image_url = $video_details->thumbnail_url;

		// set the transient, storing the image url.
		set_transient( 'hd_bcve_' . $video_id, $image_url, DAY_IN_SECONDS );

	}

	// return the url.
	return apply_filters( 'hd_bcve_dailymotion_video_thumbnail_url', $image_url, $video_id );

}

/**
 * Creates a escaping function to allowed certain HTML for embed content.
 * Needed for when echoing the innerblock HTML.
 *
 * @param array An array of HTML elements allowed.
 */
function hd_bcve_allowed_innerblock_html() {

	/**
	 * Return the allowed html
	 * These are the elements in the rendered embed block for supported videos.
	 * This also includes everything you can add to an embed caption.
	 * Therefore we need to allow these to keep the same structure.
	 */
	return [
		'iframe'     => [
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		],
		'figure' => [
			'class' => true,
		],
		'figcaption' => [
			'class' => true,
		],
		'div'       => [
			'class' => true,
		],
		'a'         => [
			'class'      => true,
			'href'       => true,
			'data-type'  => true,
		],
		'strong'    => [],
		'em'        => [],
		'sub'       => [],
		'sup'       => [],
		's'         => [],
		'kbd'       => [],
		'img'       => [
			'class' => true,
			'style' => true,
			'src'   => true,
			'alt'   => true,
		],
		'code'      => [],
		'mark'      => [
			'style' => true,
			'class' => true,
		],
	];

}

/**
 * Adds the opening figure element to the thumbnail markup.
 *
 * @param array  $block           The block array.
 * @param string $video_id        The ID of the embedded video.
 * @param string $thumbnail_url   The URL of the video thumbnail.
 * @param array  $wrapper_classes An array of CSS classes to add to the wrapper.
 */
function hd_bvce_open_markup_figure_element( $block, $video_id, $thumbnail_url, $wrapper_classes ) {

	?>
	<figure class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-id="<?php echo esc_attr( $video_id ); ?>">
	<?php

}

add_action( 'hd_bcve_video_thumbnail_markup', 'hd_bvce_open_markup_figure_element', 10, 4 );

/**
 * Adds the play button div to the markup.
 *
 * @param array  $block           The block array.
 * @param string $video_id        The ID of the embedded video.
 * @param string $thumbnail_url   The URL of the video thumbnail.
 * @param array  $wrapper_classes An array of CSS classes to add to the wrapper.
 */
function hd_bcve_add_video_play_button( $block, $video_id, $thumbnail_url, $wrapper_classes ) {

	?>
	<button class="play-button" aria-label="<?php echo esc_attr_x( 'Play video', 'Video play button', 'better-core-video-embeds' ); ?>"></button>
	<?php

}

add_action( 'hd_bcve_video_thumbnail_markup', 'hd_bcve_add_video_play_button', 20, 4 );

/**
 * Adds the video thumbnail markup output.
 *
 * @param array  $block           The block array.
 * @param string $video_id        The ID of the embedded video.
 * @param string $thumbnail_url   The URL of the video thumbnail.
 * @param array  $wrapper_classes An array of CSS classes to add to the wrapper.
 */
function hd_bcve_add_video_thumbnail_markup( $block, $video_id, $thumbnail_url, $wrapper_classes ) {

	// get the image ID from the URL.
	$imageid = attachment_url_to_postid( $thumbnail_url );

	// if we have an image ID, therefore the image is a local image.
	if ( ! empty( $imageid ) ) {

		// output the image.
		echo wp_get_attachment_image(
			$imageid,
			'full',
			false,
			[
				'class'   => 'hd-bcve-thumbnail',
				'loading' => 'lazy',
			]
		);

	} else {
		?>
		<img loading="lazy" class="hd-bcve-thumbnail" alt="" src="<?php echo esc_url( $thumbnail_url ); ?>" />
		<?php
	}

}

add_action( 'hd_bcve_video_thumbnail_markup', 'hd_bcve_add_video_thumbnail_markup', 30, 4 );

/**
 * Adds the video thumbnail markup output.
 *
 * @param array  $block           The block array.
 * @param string $video_id        The ID of the embedded video.
 * @param string $thumbnail_url   The URL of the video thumbnail.
 * @param array  $wrapper_classes An array of CSS classes to add to the wrapper.
 * @param string $video_caption   The caption of the video or an empty string.
 */
function hd_bcve_add_video_caption_markup( $block, $video_id, $thumbnail_url, $wrapper_classes, $video_caption ) {

	// if we have no caption available.
	if ( '' === $video_caption ) {
		return;
	}

	?>
	<figcaption class="wp-element-caption">
		<?php echo wp_kses( $video_caption, hd_bcve_allowed_innerblock_html() ); ?>
	</figcaption>
	<?php

}

add_action( 'hd_bcve_video_thumbnail_markup', 'hd_bcve_add_video_caption_markup', 35, 5 );

/**
 * Adds the closing figure element to the thumbnail markup.
 *
 * @param array  $block           The block array.
 * @param string $video_id        The ID of the embedded video.
 * @param string $thumbnail_url   The URL of the video thumbnail.
 * @param array  $wrapper_classes An array of CSS classes to add to the wrapper.
 */
function hd_bcve_close_markup_figure_element( $block, $video_id, $thumbnail_url, $wrapper_classes ) {

	?>
	</figure>
	<?php

}

add_action( 'hd_bcve_video_thumbnail_markup', 'hd_bcve_close_markup_figure_element', 40, 4 );

/**
 * Adds the original block markup to the template element.
 * This is used when the item is cloned when the thumbnail is clicked.
 *
 * @param array  $block           The block array.
 * @param string $video_id        The ID of the embedded video.
 * @param string $thumbnail_url   The URL of the video thumbnail.
 * @param array  $wrapper_classes An array of CSS classes to add to the wrapper.
 */
function hd_bcve_add_original_embed_template( $block, $video_id, $thumbnail_url, $wrapper_classes ) {

	?>
	<template id="hd-bcve-embed-html-<?php echo esc_attr( $video_id ); ?>">
		<?php echo wp_kses( $block['innerHTML'], hd_bcve_allowed_innerblock_html() ); ?>
	</template>
	<?php

}

add_action( 'hd_bcve_video_thumbnail_markup', 'hd_bcve_add_original_embed_template', 50, 4 );
