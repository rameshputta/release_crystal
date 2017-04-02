<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Melany
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function melany_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'melany_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 */
function melany_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'melany_body_classes' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function melany_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'melany_enhanced_image_navigation', 10, 2 );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 */
function melany_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'melany' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'melany_wp_title', 10, 2 );

/**
 * Fix "category tag" bad value error
 */
function add_nofollow_cat( $text ) {
	$text = str_replace( 'rel="category tag"', "", $text );
	return $text;
}
add_filter( 'the_category', 'add_nofollow_cat' );

/**
 * Getter function for Featured Content Plugin.
 *
 * @since 1.1.0
 *
 * @return array An array of WP_Post objects.
 */
function melany_get_featured_posts() {
	/**
	 * Filter the featured posts to return in Melany.
	 *
	 * @since 1.1.0
	 *
	 * @param array|bool $posts Array of featured posts, otherwise false.
	 */
	return apply_filters( 'melany_get_featured_posts', array() );
}

/**
 * A helper conditional function that returns a boolean value.
 *
 * @since 1.1.0
 *
 * @return bool Whether there are featured posts.
 */
function melany_has_featured_posts() {
	return ! is_paged() && (bool) melany_get_featured_posts();
}