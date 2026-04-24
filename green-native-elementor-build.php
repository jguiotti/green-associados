<?php
/**
 * Green Associados — reconstrução nativa (sem widget HTML).
 *
 * Uso:
 *   docker run --rm -v "$(pwd)/wordpress:/var/www/html" -v "$(pwd)/green-native-elementor-build.php:/var/www/html/green-native-elementor-build.php" \
 *     --network greenassociados_default -e WORDPRESS_DB_HOST=db:3306 -e WORDPRESS_DB_USER=wpuser \
 *     -e WORDPRESS_DB_PASSWORD=wppass -e WORDPRESS_DB_NAME=wordpress \
 *     wordpress:cli wp eval-file green-native-elementor-build.php --user=jana --allow-root
 *
 * @package GreenAssociados
 */

if ( ! defined( 'ABSPATH' ) ) {
	require_once __DIR__ . '/wp-load.php';
}

$green_runner_login = getenv( 'GREEN_WP_ADMIN_USER' );
if ( ! is_string( $green_runner_login ) || $green_runner_login === '' ) {
	$green_runner_login = 'jana';
}
$green_runner = get_user_by( 'login', $green_runner_login );
if ( ! $green_runner ) {
	$green_runner = get_user_by( 'login', 'admin' );
}
if ( $green_runner ) {
	wp_set_current_user( $green_runner->ID );
} else {
	wp_set_current_user( 1 );
}

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	require_once WP_PLUGIN_DIR . '/elementor/elementor.php';
}
\Elementor\Plugin::instance();

/**
 * IDs únicos compatíveis com Elementor (7 chars hex).
 */
function green_eid() {
	return substr( bin2hex( random_bytes( 4 ) ), 0, 7 );
}

/**
 * Salva documento Elementor (post ou elementor-hf).
 *
 * @param int   $post_id  ID do post.
 * @param array $elements Árvore de elementos.
 */
function green_elementor_save( $post_id, array $elements ) {
	$document = \Elementor\Plugin::$instance->documents->get( $post_id, false );
	if ( ! $document ) {
		return false;
	}
	return $document->save(
		array(
			'elements' => $elements,
			'settings' => array(
				'post_status' => get_post_status( $post_id ),
			),
		)
	);
}

/**
 * Garante que o ficheiro da pasta uploads/green-brand existe como anexo na biblioteca de media (para o Kit / widgets usarem ID).
 *
 * @param string $absolute_path Caminho absoluto do ficheiro.
 * @param string $title       Título do anexo.
 * @return int|null ID do anexo ou null.
 */
function green_import_brand_image_to_media( $absolute_path, $title ) {
	if ( ! file_exists( $absolute_path ) || ! is_readable( $absolute_path ) ) {
		return null;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	$filename  = basename( $absolute_path );
	$file_bits = file_get_contents( $absolute_path );
	if ( false === $file_bits ) {
		return null;
	}
	$upload = wp_upload_bits( $filename, null, $file_bits );
	if ( ! empty( $upload['error'] ) ) {
		return null;
	}
	$filetype   = wp_check_filetype( $filename, null );
	$attachment = array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => $title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		return null;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );
	return (int) $attach_id;
}

/**
 * Localiza anexo de imagem pelo caminho relativo em uploads (_wp_attached_file).
 *
 * @param string[] $path_substrings Tentativas em ordem (ex.: team-renata, renata).
 * @return int ID ou 0.
 */
function green_first_attachment_matching_files( array $path_substrings ) {
	global $wpdb;
	foreach ( $path_substrings as $sub ) {
		$sub  = ltrim( (string) $sub, '/' );
		$like = '%' . $wpdb->esc_like( $sub ) . '%';
		$id   = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->postmeta} AS pm ON pm.post_id = p.ID AND pm.meta_key = '_wp_attached_file'
				WHERE p.post_type = 'attachment' AND p.post_mime_type LIKE %s AND pm.meta_value LIKE %s
				ORDER BY p.ID DESC LIMIT 1",
				'image/%',
				$like
			)
		);
		if ( $id ) {
			return (int) $id;
		}
	}
	return 0;
}

/**
 * IDs das fotos da equipe na ordem do layout (Renata … Michael).
 *
 * @return int[]
 */
function green_team_attachment_ids_from_library() {
	$groups = array(
		array( 'team-renata', 'team_renata', 'renata-silverio', 'renata_silverio', 'renata' ),
		array( 'team-samanta', 'team_samanta', 'samanta-bastos', 'samanta' ),
		array( 'team-melisande', 'team_melisande', 'melisande' ),
		array( 'team-vanessa', 'team_vanessa', 'vanessa-melhado', 'vanessa' ),
		array( 'team-diogo', 'team_diogo', 'diogo-hohl', 'diogo' ),
		array( 'team-michael', 'team_michael', 'michael-green', 'michael-finhan', 'michael' ),
	);
	$ids = array();
	foreach ( $groups as $hints ) {
		$ids[] = green_first_attachment_matching_files( $hints );
	}
	return $ids;
}

/**
 * Regras "Exibir em: Todo o site" no formato esperado pelo Header Footer Elementor (meta LIKE %"basic-global"% + parse em array).
 */
function green_hfe_entire_site_rules() {
	return array(
		'rule'     => array( 0 => 'basic-global' ),
		'specific' => array(),
	);
}

/**
 * Duplica um template elementor-hf para inglês (Polylang), com as mesmas meta/Elementor.
 *
 * @param int $source_id Post PT (ou idioma por defeito).
 */
function green_hfe_clone_for_english( $source_id ) {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) || ! function_exists( 'PLL' ) ) {
		return;
	}
	if ( ! PLL()->model->get_language( 'en' ) ) {
		return;
	}
	if ( pll_get_post( $source_id, 'en' ) ) {
		return;
	}
	$src = get_post( $source_id );
	if ( ! $src || 'elementor-hf' !== $src->post_type ) {
		return;
	}
	$new_id = wp_insert_post(
		array(
			'post_title'  => $src->post_title . ' (EN)',
			'post_type'   => 'elementor-hf',
			'post_status' => 'publish',
		),
		true
	);
	if ( is_wp_error( $new_id ) || ! $new_id ) {
		return;
	}
	$skip = array( '_edit_lock', '_edit_last' );
	foreach ( get_post_meta( $source_id ) as $meta_key => $values ) {
		if ( in_array( $meta_key, $skip, true ) ) {
			continue;
		}
		foreach ( $values as $meta_value ) {
			add_post_meta( $new_id, $meta_key, maybe_unserialize( $meta_value ) );
		}
	}
	pll_set_post_language( $new_id, 'en' );
	$default_slug = pll_default_language( 'slug' );
	if ( $default_slug ) {
		pll_save_post_translations(
			array(
				$default_slug => $source_id,
				'en'          => $new_id,
			)
		);
	}
}

/* -----------------------------------------------------------------------------
 * Polylang — idiomas e opções de URL
 * -------------------------------------------------------------------------- */

if ( function_exists( 'PLL' ) ) {
	$model = PLL()->model;
	if ( ! $model->get_language( 'pt' ) ) {
		$model->add_language(
			array(
				'name'   => 'Português',
				'slug'   => 'pt',
				'locale' => 'pt_BR',
			)
		);
	}
	if ( ! $model->get_language( 'en' ) ) {
		$model->add_language(
			array(
				'name'       => 'English',
				'slug'       => 'en',
				'locale'     => 'en_US',
				'term_group' => 1,
			)
		);
	}
	$polylang_opts = get_option( 'polylang', array() );
	if ( ! is_array( $polylang_opts ) ) {
		$polylang_opts = array();
	}
	$polylang_opts['default_lang'] = 'pt';
	$polylang_opts['hide_default'] = true;
	$polylang_opts['rewrite']      = true;
	$polylang_opts['force_lang']   = 1;
	update_option( 'polylang', $polylang_opts );
	if ( method_exists( PLL()->model, 'clean_cache' ) ) {
		PLL()->model->clean_cache();
	}
}

/* -----------------------------------------------------------------------------
 * Brand assets (uploads/green-brand) + Kit Elementor (identidade no Site Settings)
 * -------------------------------------------------------------------------- */

$uploads    = wp_upload_dir();
$brand_base = trailingslashit( $uploads['baseurl'] ) . 'green-brand/';
$brand_dir  = trailingslashit( $uploads['basedir'] ) . 'green-brand/';
$logo_file = $brand_dir . 'green_associados.png';
$hero_fallback_url = $brand_base . 'paulistaGreen.png';

$logo_attach_id = (int) attachment_url_to_postid( $brand_base . 'green_associados.png' );
if ( ! $logo_attach_id ) {
	$logo_attach_id = green_first_attachment_matching_files(
		array(
			'green_associados',
			'green-associados',
			'logo-green',
			'logo_green',
			'green-logo',
			'green_logo',
		)
	);
}
if ( ! $logo_attach_id ) {
	$logo_attach_id = (int) green_import_brand_image_to_media( $logo_file, 'Logo Green Associados' );
}
$logo_url = $logo_attach_id ? (string) wp_get_attachment_image_url( $logo_attach_id, 'full' ) : $brand_base . 'green_associados.png';

$hero_attach_id = green_first_attachment_matching_files(
	array(
		'paulistaGreen',
		'paulista-green',
		'paulista',
		'hero-banner',
		'hero_banner',
		'banner-hero',
	)
);
$hero_bg_url = $hero_fallback_url;
if ( $hero_attach_id ) {
	$hero_u = wp_get_attachment_image_url( $hero_attach_id, 'full' );
	if ( $hero_u ) {
		$hero_bg_url = $hero_u;
	}
}
$hero_media = array(
	'url' => $hero_bg_url,
	'id'  => $hero_attach_id ? (string) $hero_attach_id : '',
);

$team_attachment_ids = green_team_attachment_ids_from_library();
if ( ! array_filter( $team_attachment_ids ) ) {
	$team_attachment_ids = null;
}

$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();

/* Design system Layout V2 (cores globais do brief — Kit Elementor). */
$system_colors = array(
	array( '_id' => 'primary', 'title' => 'primary', 'color' => '#005646' ),
	array( '_id' => 'secondary', 'title' => 'secondary', 'color' => '#006b57' ),
	array( '_id' => 'text', 'title' => 'text', 'color' => '#1b1c1b' ),
	array( '_id' => 'accent', 'title' => 'accent', 'color' => '#42fdd3' ),
);

$custom_color_map = array(
	'light-on-dark'             => '#ffffff',
	'primary-container'         => '#00715C',
	'primary-fixed-dim'         => '#7fd7bd',
	'on-primary'                => '#ffffff',
	'on-primary-container'      => '#99f2d7',
	'secondary-container'       => '#3dfad0',
	'secondary-fixed'           => '#42fdd3',
	'secondary-fixed-dim'       => '#00e0b8',
	'on-secondary-fixed'        => '#002019',
	'tertiary'                  => '#7d3714',
	'tertiary-container'        => '#9b4e29',
	'tertiary-fixed'            => '#ffdbcd',
	'on-tertiary-container'     => '#ffd9c9',
	'surface'                   => '#fcf9f7',
	'surface-container'         => '#f0edeb',
	'surface-container-low'     => '#f6f3f1',
	'surface-container-high'    => '#eae8e6',
	'surface-container-lowest'  => '#ffffff',
	'on-surface'                => '#1b1c1b',
	'on-surface-variant'        => '#3e4945',
	'outline'                   => '#6e7a75',
	'stone-200'                 => '#e7e5e4',
	'stone-400'                 => '#a8a29e',
	'stone-500'                 => '#78716c',
	'stone-600'                 => '#57534e',
	'stone-50'                  => '#fafaf9',
);

$custom_colors = array();
foreach ( $custom_color_map as $slug => $hex ) {
	$custom_colors[] = array(
		'_id'   => str_replace( '-', '_', $slug ),
		'title' => $slug,
		'color' => $hex,
	);
}

$fs = static function ( $px ) {
	return array( 'unit' => 'px', 'size' => $px, 'sizes' => array() );
};

$system_typography = array(
	array(
		'_id'                     => 'primary',
		'title'                   => 'Heading H1',
		'typography_typography'   => 'custom',
		'typography_font_family'  => 'Manrope',
		'typography_font_weight'  => '700',
		'typography_font_size'      => $fs( 56 ),
		'typography_line_height'    => array( 'unit' => 'em', 'size' => 1.1, 'sizes' => array() ),
		'typography_letter_spacing' => array( 'unit' => 'em', 'size' => -0.02, 'sizes' => array() ),
	),
	array(
		'_id'                     => 'secondary',
		'title'                   => 'Heading H2',
		'typography_typography'   => 'custom',
		'typography_font_family'  => 'Manrope',
		'typography_font_weight'  => '700',
		'typography_font_size'    => $fs( 40 ),
	),
	array(
		'_id'                     => 'text',
		'title'                   => 'Body',
		'typography_typography'   => 'custom',
		'typography_font_family'  => 'Manrope',
		'typography_font_weight'  => '400',
		'typography_font_size'    => $fs( 16 ),
	),
	array(
		'_id'                     => 'accent',
		'title'                   => 'Button',
		'typography_typography'   => 'custom',
		'typography_font_family'  => 'Manrope',
		'typography_font_weight'  => '700',
		'typography_font_size'    => $fs( 14 ),
	),
);

$custom_typography = array(
	array(
		'_id'                     => 'a8f3c91',
		'title'                   => 'Heading H3',
		'typography_typography'   => 'custom',
		'typography_font_family'  => 'Manrope',
		'typography_font_weight'  => '700',
		'typography_font_size'    => $fs( 20 ),
	),
	array(
		'_id'                     => 'a8f3c92',
		'title'                   => 'Heading H4',
		'typography_typography'   => 'custom',
		'typography_font_family'  => 'Manrope',
		'typography_font_weight'  => '700',
		'typography_font_size'    => $fs( 16 ),
	),
	array(
		'_id'                     => 'a8f3c93',
		'title'                   => 'Heading H5',
		'typography_typography'   => 'custom',
		'typography_font_family'  => 'Manrope',
		'typography_font_weight'  => '500',
		'typography_font_size'    => $fs( 14 ),
	),
);

$kit_settings = array(
	'system_colors'              => $system_colors,
	'custom_colors'              => $custom_colors,
	'system_typography'          => $system_typography,
	'custom_typography'          => $custom_typography,
	'default_generic_fonts'      => 'sans-serif',
	'body_background_background' => 'classic',
	'body_background_color'      => '#fcf9f7',
	'body_color'                 => '#1b1c1b',
	'body_typography_typography' => 'custom',
	'body_typography_font_family' => 'Manrope',
	'body_typography_font_weight' => '400',
	'body_typography_font_size'  => $fs( 16 ),
	'h1_typography_typography'   => 'custom',
	'h1_typography_font_family'  => 'Manrope',
	'h1_typography_font_weight'  => '700',
	'h1_typography_font_size'    => $fs( 56 ),
	'h2_typography_typography'   => 'custom',
	'h2_typography_font_family'  => 'Manrope',
	'h2_typography_font_weight'  => '700',
	'h2_typography_font_size'    => $fs( 40 ),
	'h3_typography_typography'   => 'custom',
	'h3_typography_font_family'  => 'Manrope',
	'h3_typography_font_weight'  => '700',
	'h3_typography_font_size'    => $fs( 20 ),
	'h4_typography_typography'   => 'custom',
	'h4_typography_font_family'  => 'Manrope',
	'h4_typography_font_weight'  => '700',
	'h4_typography_font_size'    => $fs( 16 ),
	'h5_typography_typography'   => 'custom',
	'h5_typography_font_family'  => 'Manrope',
	'h5_typography_font_weight'  => '500',
	'h5_typography_font_size'    => $fs( 14 ),
	'container_width'          => array(
		'unit'  => 'px',
		'size'  => 1200,
		'sizes' => array(),
	),
	'container_width_tablet'   => array(
		'unit'  => 'px',
		'size'  => 1024,
		'sizes' => array(),
	),
	'container_width_mobile'   => array(
		'unit'  => '%',
		'size'  => 100,
		'sizes' => array(),
	),
);
if ( $logo_attach_id ) {
	$kit_settings['site_logo'] = array(
		'id'  => $logo_attach_id,
		'url' => $logo_url,
	);
}

$kit->update_settings( $kit_settings );

if ( $logo_attach_id ) {
	set_theme_mod( 'custom_logo', $logo_attach_id );
}

/*
 * CSS extra do site passa a ir para o MU-plugin green-elementor-brand-utilities.php
 * (inline + Google Fonts dos ícones), não para o Personalizador. Cores/tipos/fundo
 * vêm do Kit em Elementor → Site Settings.
 */
wp_update_custom_css_post( '' );

\Elementor\Plugin::$instance->files_manager->clear_cache();

$home_url = home_url( '/' );

/* Menu Principal (âncoras) */
$menu_name   = 'Menu Principal';
$menu_obj    = wp_get_nav_menu_object( $menu_name );
$menu_id_int = 0;
if ( ! $menu_obj ) {
	$created = wp_create_nav_menu( $menu_name );
	$menu_id_int = is_wp_error( $created ) ? 0 : (int) $created;
} else {
	$menu_id_int = (int) $menu_obj->term_id;
}
if ( $menu_id_int ) {
	$existing_items = wp_get_nav_menu_items( $menu_id_int );
	if ( is_array( $existing_items ) ) {
		foreach ( $existing_items as $mi ) {
			wp_delete_post( $mi->ID, true );
		}
	}
	$anchor_items = array(
		'Áreas de Atuação'        => '#atuacao',
		'Inteligência Artificial' => '#ia',
		'Segurança'               => '#seguranca',
		'Nossa Equipe'            => '#equipe',
		'Contato'                 => '#contato',
	);
	foreach ( $anchor_items as $title => $hash ) {
		wp_update_nav_menu_item(
			$menu_id_int,
			0,
			array(
				'menu-item-title'  => $title,
				'menu-item-url'    => untrailingslashit( $home_url ) . $hash,
				'menu-item-status' => 'publish',
			)
		);
	}
}
$menu_id = $menu_id_int ? (string) $menu_id_int : '';

$ghs_paths = array(
	__DIR__ . '/green-homepage-sections.php',
	__DIR__ . '/wordpress/green-homepage-sections.php',
);
foreach ( $ghs_paths as $ghs_path ) {
	if ( file_exists( $ghs_path ) ) {
		require_once $ghs_path;
		break;
	}
}
if ( ! function_exists( 'green_build_homepage_elements' ) ) {
	wp_die( 'Ficheiro green-homepage-sections.php em falta (coloque-o junto ao script ou em wordpress/).' );
}

/* -----------------------------------------------------------------------------
 * Header HFE (elementor-hf)
 * -------------------------------------------------------------------------- */

$hfe_display = green_hfe_entire_site_rules();

$header_elements = array(
	array(
		'id'       => green_eid(),
		'elType'   => 'section',
		'isInner'  => false,
		'settings' => array(
			'layout'                          => 'full_width',
			'content_width'                   => array( 'unit' => 'px', 'size' => 1200, 'sizes' => array() ),
			'gap'                             => 'no',
			'height'                          => 'min-height',
			'custom_height'                   => array( 'unit' => 'px', 'size' => 64, 'sizes' => array() ),
			'html_tag'                        => 'header',
			'_element_id'                     => 'green-site-header',
			'z_index'                         => 1000,
			'padding'                         => array(
				'unit'     => 'px',
				'top'      => '12',
				'right'    => '24',
				'bottom'   => '12',
				'left'     => '24',
				'isLinked' => false,
			),
			'padding_tablet'                  => array(
				'unit'     => 'px',
				'top'      => '12',
				'right'    => '20',
				'bottom'   => '12',
				'left'     => '20',
				'isLinked' => false,
			),
			'padding_mobile'                  => array(
				'unit'     => 'px',
				'top'      => '10',
				'right'    => '16',
				'bottom'   => '10',
				'left'     => '16',
				'isLinked' => false,
			),
			'border_border'                   => 'solid',
			'border_width'                    => array(
				'unit'     => 'px',
				'top'      => '0',
				'right'    => '0',
				'bottom'   => '1',
				'left'     => '0',
				'isLinked' => false,
			),
			'border_color'                    => 'rgba(231, 229, 228, 0.85)',
			'css_classes'                     => 'glass-nav green-site-header-bar',
			'stretch_section'                 => 'section-stretched',
			'background_background'           => 'classic',
			'background_color'                => 'rgba(252, 249, 247, 0.01)',
		),
		'elements' => array(
			array(
				'id'       => green_eid(),
				'elType'   => 'column',
				'settings' => array(
					'_column_size'  => 20,
					'_inline_size'  => null,
					'content_position'=> 'center',
				),
				'elements' => array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'image',
						'settings'   => array(
							'image'            => array(
								'url' => $logo_url,
								'id'  => $logo_attach_id ? (string) $logo_attach_id : '',
							),
							'link_to'          => 'custom',
							'link'             => array( 'url' => $home_url, 'is_external' => '', 'nofollow' => '' ),
							'width'            => array( 'unit' => 'px', 'size' => 260, 'sizes' => array() ),
							'width_tablet'     => array( 'unit' => 'px', 'size' => 220, 'sizes' => array() ),
							'width_mobile'     => array( 'unit' => 'px', 'size' => 180, 'sizes' => array() ),
							'height'           => array( 'unit' => 'px', 'size' => '', 'sizes' => array() ),
							'image_size'       => 'full',
						),
					),
				),
			),
			array(
				'id'       => green_eid(),
				'elType'   => 'column',
				'settings' => array(
					'_column_size'                    => 60,
					'content_position'                => 'center',
					'padding'                         => array(
						'unit' => 'px',
						'top' => '0',
						'right' => '0',
						'bottom' => '0',
						'left' => '0',
						'isLinked' => true,
					),
					'hide_mobile'                     => 'hidden-mobile',
				),
				'elements' => array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'navigation-menu',
						'settings'   => array(
							'menu'           => $menu_id,
							'layout'         => 'horizontal',
							'menu_space_between' => array(
								'unit' => 'px',
								'size' => 24,
								'sizes' => array(),
							),
							'menu_space_between_tablet' => array(
								'unit' => 'px',
								'size' => 16,
								'sizes' => array(),
							),
							'menu_space_between_mobile' => array(
								'unit' => 'px',
								'size' => 12,
								'sizes' => array(),
							),
							'menu_typography_typography' => 'custom',
							'menu_typography_font_family' => 'Manrope',
							'menu_typography_font_size' => array( 'unit' => 'px', 'size' => 14, 'sizes' => array() ),
							'menu_typography_font_size_mobile' => array( 'unit' => 'px', 'size' => 12, 'sizes' => array() ),
							'menu_typography_font_weight' => '500',
							'menu_typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.02, 'sizes' => array() ),
							'color_menu_item' => '#57534e',
							'color_menu_item_hover' => '#0f766e',
							'menu_item' => array(
								'text_transform' => 'uppercase',
							),
						),
					),
				),
			),
			array(
				'id'       => green_eid(),
				'elType'   => 'column',
				'settings' => array(
					'_column_size'     => 20,
					'content_position' => 'center',
				),
				'elements' => array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'polylang-language-switcher',
						'settings'   => array(
							'layout'               => 'horizontal',
							'show_country_flag'    => '',
							'show_language_name'   => '',
							'show_language_code'   => 'yes',
							'align_items'          => 'right',
						),
					),
				),
			),
		),
	),
);

$h_post = get_page_by_title( 'Green Header', OBJECT, 'elementor-hf' );
if ( ! $h_post ) {
	$header_id = wp_insert_post(
		array(
			'post_type'   => 'elementor-hf',
			'post_title'  => 'Green Header',
			'post_status' => 'publish',
		)
	);
} else {
	$header_id = $h_post->ID;
}

update_post_meta( $header_id, 'ehf_template_type', 'type_header' );
update_post_meta( $header_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $header_id, '_wp_page_template', 'default' );
update_post_meta( $header_id, 'ehf_target_include_locations', $hfe_display );
update_post_meta( $header_id, 'ehf_target_user_roles', array() );
green_elementor_save( $header_id, $header_elements );

$pll_default = function_exists( 'pll_default_language' ) ? pll_default_language( 'slug' ) : '';
if ( $pll_default && function_exists( 'pll_set_post_language' ) && function_exists( 'PLL' ) && PLL()->model->get_language( $pll_default ) ) {
	pll_set_post_language( $header_id, $pll_default );
}
green_hfe_clone_for_english( $header_id );

/* -----------------------------------------------------------------------------
 * Footer HFE
 * -------------------------------------------------------------------------- */

$footer_text_col1 = array(
	array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'image',
		'settings'   => array(
			'image'      => array(
				'url' => $logo_url,
				'id'  => $logo_attach_id ? (string) $logo_attach_id : '',
			),
			'width'      => array( 'unit' => 'px', 'size' => 240 ),
			'height'     => array( 'unit' => 'px', 'size' => '' ),
			'image_size' => 'full',
		),
	),
	array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'text-editor',
		'settings'   => array(
			'editor' => '<p>Excelência e precisão técnica em traduções corporativas para o mercado global há mais de 20 anos.</p>',
			'__globals__' => array(
				'text_color'            => 'globals/colors?id=text',
				'typography_typography' => 'globals/typography?id=text',
			),
		),
	),
);

$footer_nav = array(
	'id'         => green_eid(),
	'elType'     => 'widget',
	'widgetType' => 'navigation-menu',
	'settings'   => array(
		'menu'                        => $menu_id,
		'layout'                      => 'vertical',
		'menu_typography_typography'  => 'custom',
		'menu_typography_font_family' => 'Manrope',
		'menu_typography_font_size'   => array( 'unit' => 'px', 'size' => 14, 'sizes' => array() ),
		'menu_typography_font_weight' => '500',
		'menu_typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.02, 'sizes' => array() ),
		'menu_item'                   => array(
			'text_transform' => 'uppercase',
		),
		'color_menu_item'             => 'rgba(27,28,27,0.78)',
		'color_menu_item_hover'       => '#005646',
		'menu_space_between'          => array(
			'unit'  => 'px',
			'size'  => 6,
			'sizes' => array(),
		),
	),
);

$footer_legal_items = array();
foreach ( array( 'Privacidade' => '#', 'Termos de Uso' => '#' ) as $label => $url ) {
	$footer_legal_items[] = array(
		'text'          => $label,
		'selected_icon' => array(
			'value'   => '',
			'library' => '',
		),
		'link'          => array( 'url' => $url ),
	);
}

$footer_legal = array(
	'id'         => green_eid(),
	'elType'     => 'widget',
	'widgetType' => 'icon-list',
	'settings'   => array(
		'icon_list'                     => $footer_legal_items,
		'view'                          => 'traditional',
		'icon_typography_typography'    => 'custom',
		'icon_typography_font_family'   => 'Manrope',
		'icon_typography_font_size'     => array( 'unit' => 'px', 'size' => 13, 'sizes' => array() ),
		'icon_typography_font_weight'   => '600',
		'icon_typography_text_transform' => 'uppercase',
		'icon_typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.08, 'sizes' => array() ),
		'icon_color'                    => 'rgba(27,28,27,0.78)',
		'icon_hover_color'              => '#005646',
		'space_between'                 => array(
			'unit'  => 'px',
			'size'  => 6,
			'sizes' => array(),
		),
	),
);

$footer_row1 = array(
	'id'       => green_eid(),
	'elType'   => 'section',
	'settings' => array(
		'layout'                  => 'full_width',
		'structure'               => '30',
		'gap'                     => 'extended',
		'background_background' => 'classic',
		'background_color'      => '#fafaf9',
		'border_border'         => 'solid',
		'border_width'          => array(
			'top'    => '1',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '0',
			'unit'   => 'px',
		),
		'border_color'          => '#e5e5e5',
		'padding'               => array(
			'unit'     => 'px',
			'top'      => '80',
			'bottom'   => '48',
			'left'     => '32',
			'right'    => '32',
			'isLinked' => false,
		),
		'padding_mobile'        => array(
			'unit'     => 'px',
			'top'      => '40',
			'bottom'   => '32',
			'left'     => '20',
			'right'    => '20',
			'isLinked' => false,
		),
		'content_width'         => array( 'unit' => 'px', 'size' => 1200 ),
	),
		'elements' => array(
		array(
			'id'       => green_eid(),
			'elType'   => 'column',
			'settings' => array(
				'_column_size' => 33,
				'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
			),
			'elements' => $footer_text_col1,
		),
		array(
			'id'       => green_eid(),
			'elType'   => 'column',
			'settings' => array(
				'_column_size' => 33,
				'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
			),
			'elements' => array(
				array(
					'id'         => green_eid(),
					'elType'     => 'widget',
					'widgetType' => 'heading',
					'settings'   => array(
						'title'       => 'NAVEGAÇÃO',
						'header_size' => 'h4',
						'css_classes' => 'footer-col-heading',
					),
				),
				$footer_nav,
			),
		),
		array(
			'id'       => green_eid(),
			'elType'   => 'column',
			'settings' => array(
				'_column_size' => 33,
				'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
			),
			'elements' => array(
				array(
					'id'         => green_eid(),
					'elType'     => 'widget',
					'widgetType' => 'heading',
					'settings'   => array(
						'title'       => 'LEGAL',
						'header_size' => 'h4',
						'css_classes' => 'footer-col-heading',
					),
				),
				$footer_legal,
			),
		),
	),
);

$footer_row2 = array(
	'id'       => green_eid(),
	'elType'   => 'section',
	'settings' => array(
		'background_background' => 'classic',
		'background_color'      => '#fafaf9',
		'border_border'         => 'solid',
		'border_width'          => array(
			'top'    => '1',
			'unit'   => 'px',
		),
		'border_color'          => '#e7e5e4',
		'padding'               => array(
			'unit'     => 'px',
			'top'      => '32',
			'bottom'   => '48',
			'left'     => '32',
			'right'    => '32',
			'isLinked' => false,
		),
		'content_width'         => array( 'unit' => 'px', 'size' => 1200 ),
	),
	'elements' => array(
		array(
			'id'       => green_eid(),
			'elType'   => 'column',
			'settings' => array( '_column_size' => 50 ),
			'elements' => array(
				array(
					'id'         => green_eid(),
					'elType'     => 'widget',
					'widgetType' => 'text-editor',
					'settings'   => array(
						'editor'      => '<p>© 2026 Green Associados. Todos os direitos reservados.</p>',
						'css_classes' => 'footer-bar-meta',
					),
				),
			),
		),
		array(
			'id'       => green_eid(),
			'elType'   => 'column',
			'settings' => array(
				'_column_size' => 50,
				'align'        => 'flex-end',
			),
			'elements' => array(
				array(
					'id'         => green_eid(),
					'elType'     => 'widget',
					'widgetType' => 'text-editor',
					'settings'   => array(
						'editor'      => '<p>Desenvolvido por Fábrica das Artes</p>',
						'css_classes' => 'footer-bar-meta',
					),
				),
			),
		),
	),
);

$footer_elements = array( $footer_row1, $footer_row2 );

$f_post = get_page_by_title( 'Green Footer', OBJECT, 'elementor-hf' );
if ( ! $f_post ) {
	$footer_id = wp_insert_post(
		array(
			'post_type'   => 'elementor-hf',
			'post_title'  => 'Green Footer',
			'post_status' => 'publish',
		)
	);
} else {
	$footer_id = $f_post->ID;
}

update_post_meta( $footer_id, 'ehf_template_type', 'type_footer' );
update_post_meta( $footer_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $footer_id, '_wp_page_template', 'default' );
update_post_meta( $footer_id, 'ehf_target_include_locations', $hfe_display );
update_post_meta( $footer_id, 'ehf_target_user_roles', array() );
green_elementor_save( $footer_id, $footer_elements );

$pll_default = function_exists( 'pll_default_language' ) ? pll_default_language( 'slug' ) : '';
if ( $pll_default && function_exists( 'pll_set_post_language' ) && function_exists( 'PLL' ) && PLL()->model->get_language( $pll_default ) ) {
	pll_set_post_language( $footer_id, $pll_default );
}
green_hfe_clone_for_english( $footer_id );

/* -----------------------------------------------------------------------------
 * Homepage — conteúdo nativo (Hero + bloco inicial; expandir no editor)
 * -------------------------------------------------------------------------- */

$page_id = (int) get_option( 'page_on_front' );
if ( ! $page_id ) {
	$page_id = (int) get_option( 'green_homepage_id' );
}
if ( ! $page_id || ! get_post( $page_id ) ) {
	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Homepage',
			'post_name'    => 'homepage',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		)
	);
	update_option( 'green_homepage_id', $page_id );
}

/* Header & Footer: chama get_header()/get_footer() — necessário para o HFE (Canvas não carrega o tema). */
update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );

$home_elements = green_build_homepage_elements( $logo_url, $hero_media, $home_url, $brand_base, $team_attachment_ids );

$document = \Elementor\Plugin::$instance->documents->get( $page_id, false );
$document->save(
	array(
		'elements' => $home_elements,
		'settings' => array(
			'post_status' => 'publish',
			'hide_title'  => 'yes',
		),
	)
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_id );

$pll_default = function_exists( 'pll_default_language' ) ? pll_default_language( 'slug' ) : '';
if ( $pll_default && function_exists( 'pll_set_post_language' ) ) {
	pll_set_post_language( $page_id, $pll_default );
}

echo "OK: Polylang, Kit Layout V2 (Manrope + cores do brief), header #green-site-header, HFE {$header_id}/{$footer_id}, Homepage {$page_id}.\n";
echo "Edite identidade em Elementor → Site Settings; utilitários (.glass-nav, etc.) no MU-plugin green-elementor-brand-utilities.php (CSS inline, sem .css no tema).\n";
