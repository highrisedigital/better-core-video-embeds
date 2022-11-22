<?php
/*
Plugin Name: Better Core Video Embeds
Description: A plugin which enhances the core video embeds for Youtube and Vimeo videos by not loading unnecessary scripts until they are needed.
Requires at least: 6.0
Requires PHP: 7.0
Version: 1.0
Author: Highrise Digital
Author URI: https://highrise.digital/
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: better-core-video-embeds
*/

// define variable for path to this plugin file.
define( 'HD_BCVE_LOCATION', dirname( __FILE__ ) );
define( 'HD_BCVE_LOCATION_URL', plugins_url( '', __FILE__ ) );

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
 * Enqueus the frontend JS for the plugin.
 */
function hd_bcve_enqueue_scripts() {
	
	// only if the page has a core embed block present.
	if ( has_block( 'core/embed' ) ) {

		// enqueue the front end script to invoking the video embed on image click.
		wp_enqueue_script(
			'hd_youtube_embed',
			HD_BCVE_LOCATION_URL . '/assets/js/better-core-video-embeds.js',
			false,
			false,
			true
		);

	}
}

add_action( 'wp_enqueue_scripts', 'hd_bcve_enqueue_scripts' );

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

	// if the provider slug name is empty or not youtube.
	if ( empty( $block['attrs']['providerNameSlug'] ) || 'youtube' !== $block['attrs']['providerNameSlug'] ) {
		return $block_content;
	}

	// if for some reason there is no embed URL.
	if ( empty( $block['attrs']['url'] ) ) {
		return $block_content;
	}

	// create a default video id, url and thumbnail url.
	$video_id = '';
	$poster_img_url = '';

	// grab the video id.
	$video_url = $block['attrs']['url'];
	$parsed_video_url = parse_url( $video_url );

	// if the host is 'www.youtube.com'.
	if ( 'www.youtube.com' === $parsed_video_url['host'] ) {
		
		// parse the query part of the URL into its arguments.
		parse_str( $parsed_video_url['query'], $video_url_query_args );

		// if we cannot find a youtube video id.
		if ( empty( $video_url_query_args['v'] ) ) {
			return $block_content;
		}

		// set the video id to the v query arg.
		$video_id = $video_url_query_args['v'];

	} elseif ( 'youtu.be' === $parsed_video_url['host'] ) {

		// if we have a path.
		if ( empty( $parsed_video_url['path'] ) ) {
			return $block_content;
		}

		// remove the preceeding slash.
		$video_id = str_replace( '/', '', $parsed_video_url['path'] );

	}

	// if we don't have a video id.
	if ( '' === $video_id ) {
		return $block_content;
	}

	// create an array of classes to add to the placeholder image figure.
	$figure_classes = [
		'wp-block-image',
		'hd-bcve-wrapper',
	];

	// if we have classNames on the embed block.
	if ( ! empty( $block['attrs']['className'] ) ) {

		// explode the className string into array.
		$class_names = explode( ' ', $block['attrs']['className'] );

		// merge the class names into the figures classes array.
		$figure_classes = array_merge( $figure_classes, $class_names );

	}

	// if the embed block has an alignment.
	if ( ! empty( $block['attrs']['align'] ) ) {

		// add the alignment class to the figure classes.
		$figure_classes[] = 'align' . $block['attrs']['align'];

	}

	// set a var for the thumbnail image name.
	$img_name = 'mqdefault';

	// check if there is a max res image available.
	$max_res_img = wp_remote_get(
		'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/maxresdefault.jpg'
	);

	// if the request to the hi res image errors or returns anything other than a http 200 response code.
	if ( ( ! is_wp_error( $max_res_img )) && ( 200 === wp_remote_retrieve_response_code( $max_res_img ) ) ) {

		// set the name as max res.
		$img_name = 'maxresdefault';

	}

	// buffer the output as we need to return not echo.
	ob_start();

	?>

	<figure class="<?php echo esc_attr( implode( ' ', apply_filters( 'hd_bcve_wrapper_classes', $figure_classes, $block ) ) ); ?>" data-id="<?php echo esc_attr( $video_id ); ?>">
		<div class="play-button"></div>
		<img style="height: auto; width: 100%;" loading="lazy" class="hd-bcve-thumbnail" src="https://img.youtube.com/vi/<?php echo esc_attr( $video_id ); ?>/<?php echo esc_attr( $img_name ); ?>.jpg" />
	</figure>

	<template id="hd-bcve-embed-html-<?php echo esc_attr( $video_id ); ?>">
		<?php echo $block['innerHTML']; ?>
	</template>

	<?php

	// return the new block markup.
	return ob_get_clean();

}

add_filter( 'render_block_core/embed', 'hd_bcve_render_core_embed_block', 10, 3 );
