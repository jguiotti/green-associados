<?php
/**
 * Green Core Theme bootstrap.
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/site-layout.php';
require_once get_template_directory() . '/inc/seo-yoast.php';
require_once get_template_directory() . '/inc/polylang-switcher.php';

function green_core_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'green-core-theme' ),
			'footer'  => __( 'Menu de rodapé', 'green-core-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'green_core_theme_setup' );

function green_core_theme_assets() {
	wp_enqueue_style(
		'green-core-theme-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'green-core-theme-icons',
		'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'green-core-theme-style',
		get_stylesheet_uri(),
		array( 'green-core-theme-fonts', 'green-core-theme-icons' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'green-core-theme-enhancements',
		get_template_directory_uri() . '/js/theme-enhancements.js',
		array(),
		(string) filemtime( get_template_directory() . '/js/theme-enhancements.js' ),
		true
	);

	wp_localize_script(
		'green-core-theme-enhancements',
		'greenCoreTheme',
		array(
			'homeUrl' => trailingslashit( home_url( '/' ) ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'green_core_theme_assets' );

function green_core_theme_body_class( $classes ) {
	$classes[] = 'green-core-theme';
	return $classes;
}
add_filter( 'body_class', 'green_core_theme_body_class' );

function green_core_theme_disable_wp_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'green_core_theme_disable_wp_emoji' );

