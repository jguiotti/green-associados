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

/**
 * Botão flutuante WhatsApp (cantos fixos configurados no CSS).
 */
function green_core_theme_whatsapp_fixed_button_html() {
	$url = apply_filters( 'green_core_theme_whatsapp_sticky_url', 'https://wa.me/551152413778' );
	$tel = sanitize_text_field( __( 'WhatsApp: (11) 5241-3778', 'green-core-theme' ) );
	echo '<a class="green-wa-fixed" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $tel ) . '">';
	echo '<svg class="green-wa-fixed__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>';
	echo '</a>';
}

function green_core_theme_output_whatsapp_fixed_button() {
	if ( is_admin() ) {
		return;
	}
	green_core_theme_whatsapp_fixed_button_html();
}
add_action( 'wp_footer', 'green_core_theme_output_whatsapp_fixed_button', 5 );

/**
 * Slugs reconhecidos como páginas legais (PT/EN e variantes comuns).
 *
 * @return string[]
 */
function green_core_theme_get_legal_page_slugs() {
	return array(
		'termos-de-uso',
		'termos',
		'privacidade',
		'politica-de-privacidade',
		'politica-de-privacidade-2',
		'privacy-policy',
		'terms-of-use',
		'terms-and-conditions',
		'terms-of-service',
		'terms-and-conditions-of-use',
		'privacy',
		'terms',
		'privacy-notice',
		'data-privacy',
		'data-protection',
		'use-terms',
		'legal',
		'legal-notice',
		'cookie-policy',
		'cookies',
	);
}

/**
 * Página atual (qualquer idioma Polylang) é legal se o slug próprio ou o de qualquer tradução estiver na lista.
 *
 * @param int $post_id ID da página.
 * @return bool
 */
function green_core_theme_is_legal_document_page_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}
	$legal_slugs = green_core_theme_get_legal_page_slugs();
	$slug        = get_post_field( 'post_name', $post_id );
	if ( is_string( $slug ) && in_array( $slug, $legal_slugs, true ) ) {
		return true;
	}
	if ( function_exists( 'pll_get_post_translations' ) ) {
		foreach ( (array) pll_get_post_translations( $post_id ) as $tid ) {
			$tid = (int) $tid;
			if ( $tid <= 0 ) {
				continue;
			}
			$ts = get_post_field( 'post_name', $tid );
			if ( is_string( $ts ) && in_array( $ts, $legal_slugs, true ) ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Legal: body class para tipografia em Termos / Privacidade (PT ou EN ligados no Polylang).
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function green_core_theme_body_class_legal_page( $classes ) {
	if ( ! is_page() ) {
		return $classes;
	}
	if ( green_core_theme_is_legal_document_page_id( (int) get_queried_object_id() ) ) {
		$classes[] = 'green-is-legal-page';
	}
	return $classes;
}
add_filter( 'body_class', 'green_core_theme_body_class_legal_page' );

/**
 * URL da página inicial alinhada ao cabeçalho (Polylang: home por idioma; idem a home_url('/')).
 *
 * @return string URL absolute com barra final.
 */
function green_core_theme_logo_home_url() {
	if ( function_exists( 'pll_home_url' ) ) {
		$url = pll_home_url();
		if ( is_string( $url ) && '' !== $url ) {
			return trailingslashit( esc_url_raw( $url ) );
		}
	}
	return trailingslashit( home_url( '/' ) );
}

/**
 * O bloco Site Logo e get_custom_logo() devem apontar para a mesma home que o logo do header (evita link absoluto antigo ou resolução errada de '/').
 *
 * @param string $html Markup do logo.
 * @return string
 */
function green_core_theme_filter_custom_logo_home_href( $html ) {
	if ( ! is_string( $html ) || '' === $html || false === strpos( $html, '<a' ) ) {
		return $html;
	}
	$home = green_core_theme_logo_home_url();
	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$p = new WP_HTML_Tag_Processor( $html );
		if ( $p->next_tag( array( 'tag_name' => 'A' ) ) ) {
			$p->set_attribute( 'href', esc_url( $home ) );
		}
		return $p->get_updated_html();
	}
	return preg_replace( '/(<a\s[^>]*\bhref=")([^"]*)(")/', '$1' . esc_url( $home ) . '$3', $html, 1 );
}
add_filter( 'get_custom_logo', 'green_core_theme_filter_custom_logo_home_href', 99 );

/**
 * Garante que o CSS da 1.ª coluna do rodapé (logo + slogan) aplique depois dos estilos globais dos blocos.
 * Sem isto, regras do core / estilos globais podem carregar a seguir e anular o tema (havia «zero» efeito no browser).
 */
function green_core_theme_footer_column_layout_after_global_styles() {
	$css = '
.green-footer-from-page .wp-block-columns > .wp-block-column:first-child {
	align-self: flex-start !important;
}
.green-footer-from-page .wp-block-columns > .wp-block-column:first-child.is-layout-flex,
.green-footer-from-page .wp-block-columns > .wp-block-column:first-child > .is-layout-flex,
.green-footer-from-page .wp-block-columns > .wp-block-column:first-child > .wp-block-group.is-layout-flex,
.green-footer-from-page .wp-block-columns > .wp-block-column:first-child .is-layout-flex.is-vertical,
.green-footer-from-page .wp-block-columns > .wp-block-column:first-child [class*=is-content-justification-space-] {
	justify-content: flex-start !important;
}
.green-footer-from-page .wp-block-columns > .wp-block-column:first-child p.green-footer-lead {
	margin-top: 0 !important;
	margin-block-start: 0 !important;
}
';
	$deps = array( 'green-core-theme-style' );
	if ( wp_style_is( 'wp-block-library', 'registered' ) ) {
		array_unshift( $deps, 'wp-block-library' );
	}
	// Só anexa global-styles se existir (sem theme.json, o core pode não registar o handle).
	if ( wp_style_is( 'global-styles', 'registered' ) ) {
		$deps[] = 'global-styles';
	}
	wp_register_style( 'green-core-footer-column-layout', false, $deps, wp_get_theme()->get( 'Version' ) );
	wp_enqueue_style( 'green-core-footer-column-layout' );
	wp_add_inline_style( 'green-core-footer-column-layout', $css );
}
add_action( 'wp_enqueue_scripts', 'green_core_theme_footer_column_layout_after_global_styles', 200 );

/**
 * Tailwind (CDN) no front: utilitários nos blocos Green Native Builder.
 * Cores alinhadas à paleta do tema; sem alterar o editor de blocos.
 */
function green_core_theme_enqueue_tailwind() {
	if ( is_admin() ) {
		return;
	}
	wp_enqueue_script(
		'green-core-tailwind',
		'https://cdn.tailwindcss.com',
		array(),
		'3.4.1',
		false
	);
	// primary = verde-água profundo (referência «Conecta»), não verde-bandeira.
	$config = 'tailwind.config = { theme: { extend: { colors: {'
		. 'primary: "#006060",'
		. 'secondary: "#00B4D8",'
		. 'tertiary: "#2C3E50",'
		. 'background: "#FFFFFF",'
		. 'surface: "#FAFAF7",'
		. '"brand-forest": "#006060",'
		. '"brand-mint": "#00B4D8"'
		. '} } } };';
	wp_add_inline_script( 'green-core-tailwind', $config, 'after' );
}
add_action( 'wp_enqueue_scripts', 'green_core_theme_enqueue_tailwind', 1 );

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

