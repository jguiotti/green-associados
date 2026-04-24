<?php
/**
 * Setup Green Associados: Kit Elementor (cores/tipografia), CSS adicional WP, página inicial Elementor.
 * Uso: wp eval-file green-setup-elementor.php --user=admin --allow-root
 *
 * @package GreenAssociados
 */

if ( ! defined( 'ABSPATH' ) ) {
	require_once __DIR__ . '/wp-load.php';
}

wp_set_current_user( 1 );

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	require_once WP_PLUGIN_DIR . '/elementor/elementor.php';
}

\Elementor\Plugin::instance();

$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();

$system_colors = array(
	array(
		'_id'   => 'primary',
		'title' => 'primary',
		'color' => '#005646',
	),
	array(
		'_id'   => 'secondary',
		'title' => 'secondary',
		'color' => '#006b57',
	),
	array(
		'_id'   => 'text',
		'title' => 'on-surface',
		'color' => '#1b1c1b',
	),
	array(
		'_id'   => 'accent',
		'title' => 'secondary-fixed',
		'color' => '#42fdd3',
	),
);

$custom_color_rows = array(
	array( 'primary_container', '#00715C' ),
	array( 'primary_fixed', '#9bf3d9' ),
	array( 'primary_fixed_dim', '#7fd7bd' ),
	array( 'on_primary', '#ffffff' ),
	array( 'on_primary_fixed', '#002019' ),
	array( 'on_primary_container', '#99f2d7' ),
	array( 'secondary_container', '#3dfad0' ),
	array( 'secondary_fixed_dim', '#00e0b8' ),
	array( 'on_secondary', '#ffffff' ),
	array( 'on_secondary_fixed', '#002019' ),
	array( 'tertiary', '#7d3714' ),
	array( 'tertiary_container', '#9b4e29' ),
	array( 'tertiary_fixed', '#ffdbcd' ),
	array( 'tertiary_fixed_dim', '#ffb596' ),
	array( 'on_tertiary', '#ffffff' ),
	array( 'on_tertiary_container', '#ffd9c9' ),
	array( 'surface', '#fcf9f7' ),
	array( 'surface_dim', '#dcd9d8' ),
	array( 'surface_bright', '#fcf9f7' ),
	array( 'surface_container', '#f0edeb' ),
	array( 'surface_container_low', '#f6f3f1' ),
	array( 'surface_container_high', '#eae8e6' ),
	array( 'surface_container_highest', '#e5e2e0' ),
	array( 'surface_container_lowest', '#ffffff' ),
	array( 'surface_variant', '#e5e2e0' ),
	array( 'on_surface_variant', '#3e4945' ),
	array( 'background', '#fcf9f7' ),
	array( 'on_background', '#1b1c1b' ),
	array( 'outline', '#6e7a75' ),
	array( 'outline_variant', '#bdc9c4' ),
	array( 'inverse_surface', '#31302f' ),
	array( 'inverse_on_surface', '#f3f0ee' ),
	array( 'inverse_primary', '#7fd7bd' ),
	array( 'error', '#ba1a1a' ),
	array( 'error_container', '#ffdad6' ),
	array( 'on_error', '#ffffff' ),
	array( 'on_error_container', '#93000a' ),
);

$custom_colors = array();
foreach ( $custom_color_rows as $row ) {
	$custom_colors[] = array(
		'_id'   => $row[0],
		'title' => $row[0],
		'color' => $row[1],
	);
}

$manrope = 'Manrope';

$system_typography = array(
	array(
		'_id'                   => 'primary',
		'title'                 => 'Headings',
		'typography_typography' => 'custom',
		'typography_font_family' => $manrope,
		'typography_font_weight' => '700',
	),
	array(
		'_id'                   => 'secondary',
		'title'                 => 'Labels',
		'typography_typography' => 'custom',
		'typography_font_family' => $manrope,
		'typography_font_weight' => '600',
	),
	array(
		'_id'                   => 'text',
		'title'                 => 'Body',
		'typography_typography' => 'custom',
		'typography_font_family' => $manrope,
		'typography_font_weight' => '400',
	),
	array(
		'_id'                   => 'accent',
		'title'                 => 'Buttons',
		'typography_typography' => 'custom',
		'typography_font_family' => $manrope,
		'typography_font_weight' => '600',
	),
);

$kit->update_settings(
	array(
		'system_colors'         => $system_colors,
		'custom_colors'         => $custom_colors,
		'system_typography'     => $system_typography,
		'default_generic_fonts' => 'sans-serif',
	)
);

$custom_css = <<<'CSS'
/* Material Symbols */
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
}

/* Glass Nav Effect */
.glass-nav {
  backdrop-filter: blur(12px);
  background-color: rgba(252, 249, 247, 0.8);
}

/* Editorial Grid */
.editorial-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 2rem;
}

/* Team Photo B&W hover effect */
.team-photo-bw {
  filter: grayscale(100%);
  transition: filter 0.4s ease;
}
.team-photo-bw:hover {
  filter: grayscale(0%);
}

/* Smooth Scroll */
html {
  scroll-behavior: smooth;
}

/* Body base */
body {
  background-color: #fcf9f7;
  color: #1b1c1b;
  font-family: 'Manrope', sans-serif;
}

::selection {
  background-color: #42fdd3;
  color: #002019;
}

/* Etapa 9 — animações e hovers */
a { transition: color 0.15s ease; }

.section-atuacao .card:hover {
  border-left-color: #006b57;
}

.highlight-card:hover .arrow-icon {
  margin-left: 16px;
}
.arrow-icon {
  transition: margin-left 0.2s ease;
}

.security-card {
  transition: transform 0.5s ease;
}
.security-card:hover {
  transform: translateY(-4px);
}

.whatsapp-btn {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.whatsapp-btn:hover {
  transform: scale(1.05);
  box-shadow: 0 25px 50px rgba(0,0,0,0.25);
}

.hero-btn-primary:hover {
  box-shadow: 0 10px 30px rgba(66,253,211,0.2);
}
.hero-btn-primary:active,
.hero-btn-secondary:active {
  transform: scale(0.95);
}

@media (max-width: 767px) {
  .ga-hero-title { font-size: 36px !important; }
}
CSS;

wp_update_custom_css_post( $custom_css );

$html_path = ABSPATH . 'green-code.html';
if ( ! is_readable( $html_path ) ) {
	echo "ERRO: green-code.html não encontrado em ABSPATH.\n";
	exit( 1 );
}

$raw = file_get_contents( $html_path );
$raw = str_replace( 'traducao@greenassociados.com.br', 'contato@greenassociados.com.br', $raw );

preg_match( '/<script id="tailwind-config">(.*?)<\/script>/s', $raw, $tw );
preg_match( '/<link href="https:\/\/fonts.googleapis.com\/css2\?family=Manrope[^"]*"[^>]*>/', $raw, $fm );
preg_match( '/<link href="https:\/\/fonts.googleapis.com\/css2\?family=Material[^"]*"[^>]*>/', $raw, $mat );
preg_match( '/<style>(.*?)<\/style>/s', $raw, $st );

preg_match( '/<nav class="fixed.*?<\/footer>/s', $raw, $main );

if ( empty( $main[0] ) ) {
	echo "ERRO: não foi possível extrair nav…footer do HTML.\n";
	exit( 1 );
}

$widget_html  = ( isset( $fm[0] ) ? $fm[0] . "\n" : '' );
$widget_html .= ( isset( $mat[0] ) ? $mat[0] . "\n" : '' );
$widget_html .= '<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>' . "\n";
if ( ! empty( $tw[1] ) ) {
	$widget_html .= '<script id="tailwind-config">' . $tw[1] . '</script>' . "\n";
}
if ( ! empty( $st[1] ) ) {
	$widget_html .= '<style>' . $st[1] . '</style>' . "\n";
}

$widget_html .= $main[0];

$widget_html = str_replace(
	'<h1 class="text-white text-4xl md:text-5xl lg:text-6xl',
	'<h1 class="ga-hero-title text-white text-4xl md:text-5xl lg:text-6xl',
	$widget_html
);

$widget_html = str_replace(
	'<a class="px-8 py-4 bg-secondary-fixed text-on-secondary-fixed font-bold rounded-md hover:shadow-lg',
	'<a class="hero-btn-primary px-8 py-4 bg-secondary-fixed text-on-secondary-fixed font-bold rounded-md hover:shadow-lg',
	$widget_html
);

$widget_html = str_replace(
	'<a class="px-8 py-4 bg-white/10 backdrop-blur-md text-white border border-white/20 font-bold rounded-md hover:bg-white/20',
	'<a class="hero-btn-secondary px-8 py-4 bg-white/10 backdrop-blur-md text-white border border-white/20 font-bold rounded-md hover:bg-white/20',
	$widget_html
);

$widget_html = str_replace(
	'<div class="bg-surface-container-lowest p-10 shadow-xl shadow-primary/5 group border-b-4 border-secondary-fixed">',
	'<div class="highlight-card bg-surface-container-lowest p-10 shadow-xl shadow-primary/5 group border-b-4 border-secondary-fixed">',
	$widget_html
);

$widget_html = str_replace(
	'<span class="material-symbols-outlined text-sm" style="">arrow_forward</span>',
	'<span class="material-symbols-outlined text-sm arrow-icon" style="">arrow_forward</span>',
	$widget_html
);

$widget_html = str_replace(
	'<div class="group p-8 bg-surface-container-low border-l-4 border-transparent hover:border-secondary transition-all">',
	'<div class="card section-atuacao group p-8 bg-surface-container-low border-l-4 border-transparent hover:border-secondary transition-all">',
	$widget_html
);

$widget_html = str_replace(
	'<div class="bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 md:translate-y-8 transition-transform hover:-translate-y-1 duration-500 group">',
	'<div class="security-card bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 md:translate-y-8 transition-transform hover:-translate-y-1 duration-500 group">',
	$widget_html
);

$widget_html = str_replace(
	'<div class="bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 transition-transform hover:-translate-y-1 duration-500 group">',
	'<div class="security-card bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 transition-transform hover:-translate-y-1 duration-500 group">',
	$widget_html
);

$widget_html = str_replace(
	'<div class="bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 md:translate-y-12 transition-transform hover:-translate-y-1 duration-500 group">',
	'<div class="security-card bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 md:translate-y-12 transition-transform hover:-translate-y-1 duration-500 group">',
	$widget_html
);

$widget_html = str_replace(
	'<div class="bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 md:translate-y-4 transition-transform hover:-translate-y-1 duration-500 group">',
	'<div class="security-card bg-white p-8 rounded-2xl shadow-xl shadow-tertiary/5 border border-tertiary/10 md:translate-y-4 transition-transform hover:-translate-y-1 duration-500 group">',
	$widget_html
);

$widget_html = str_replace(
	'<a class="inline-flex items-center gap-4 px-12 py-6 bg-secondary-fixed text-on-secondary-fixed text-xl font-bold rounded-full hover:shadow-2xl hover:scale-105 transition-all" href="https://wa.me/551138128780"',
	'<a class="whatsapp-btn inline-flex items-center gap-4 px-12 py-6 bg-secondary-fixed text-on-secondary-fixed text-xl font-bold rounded-full hover:shadow-2xl hover:scale-105 transition-all" href="https://wa.me/551138128780"',
	$widget_html
);

$page_id = (int) get_option( 'green_homepage_id' );
if ( $page_id && get_post( $page_id ) ) {
	// já existe
} else {
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Homepage',
			'post_name'    => 'homepage',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);
	if ( is_wp_error( $page_id ) ) {
		echo $page_id->get_error_message();
		exit( 1 );
	}
	update_option( 'green_homepage_id', $page_id );
}

update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );

$document = \Elementor\Plugin::$instance->documents->get( $page_id, false );

$eid = function () {
	return substr( bin2hex( random_bytes( 5 ) ), 0, 7 );
};

$sid = $eid();
$cid = $eid();
$wid = $eid();

$elements = array(
	array(
		'id'       => $sid,
		'elType'   => 'section',
		'isInner'  => false,
		'settings' => array(
			'layout' => 'full_width',
			'gap'    => 'no',
		),
		'elements' => array(
			array(
				'id'       => $cid,
				'elType'   => 'column',
				'isInner'  => false,
				'settings' => array(
					'_column_size' => 100,
				),
				'elements' => array(
					array(
						'id'         => $wid,
						'elType'     => 'widget',
						'widgetType' => 'html',
						'settings'   => array(
							'html' => $widget_html,
						),
					),
				),
			),
		),
	),
);

$document->save(
	array(
		'elements' => $elements,
		'settings' => array(
			'post_status' => 'publish',
			'hide_title'  => 'yes',
		),
	)
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_id );

echo "OK: Kit atualizado, CSS adicional salvo, Homepage ID {$page_id} com Elementor (Canvas, sem título).\n";
