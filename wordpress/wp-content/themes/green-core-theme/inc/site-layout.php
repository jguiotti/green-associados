<?php
/**
 * Cabeçalho e rodapé globais (CPT privado em Aparências) + Polylang.
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GREEN_CORE_SITE_PART_HEADER', 'green_core_site_part_header_id' );
define( 'GREEN_CORE_SITE_PART_FOOTER', 'green_core_site_part_footer_id' );
/** @deprecated IDs antigos (post type page) — migrados automaticamente */
define( 'GREEN_CORE_HEADER_PAGE_OPTION', 'green_core_layout_header_page_id' );
define( 'GREEN_CORE_FOOTER_PAGE_OPTION', 'green_core_layout_footer_page_id' );

function green_core_theme_default_footer_block_markup() {
	$path = get_template_directory() . '/patterns/footer-site-content.html';
	if ( is_readable( $path ) ) {
		return (string) file_get_contents( $path );
	}
	return '';
}

function green_core_theme_register_site_part_cpt() {
	register_post_type(
		'green_site_part',
		array(
			'labels'              => array(
				'name'               => __( 'Cabeçalho e rodapé', 'green-core-theme' ),
				'singular_name'      => __( 'Bloco global', 'green-core-theme' ),
				'menu_name'          => __( 'Cabeçalho e rodapé', 'green-core-theme' ),
				'add_new'            => __( 'Adicionar', 'green-core-theme' ),
				'add_new_item'       => __( 'Novo bloco global', 'green-core-theme' ),
				'edit_item'          => __( 'Editar bloco global', 'green-core-theme' ),
				'search_items'       => __( 'Pesquisar', 'green-core-theme' ),
				'not_found'          => __( 'Nenhum bloco encontrado.', 'green-core-theme' ),
				'not_found_in_trash' => __( 'Nenhum bloco na lixeira.', 'green-core-theme' ),
			),
			'description'         => __( 'Conteúdo reutilizado no cabeçalho ou rodapé de todo o site. Traduza cada entrada no Polylang.', 'green-core-theme' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => 'themes.php',
			'menu_position'       => 62,
			'menu_icon'           => 'dashicons-layout',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'supports'            => array( 'title', 'editor', 'revisions' ),
			'show_in_rest'        => true,
			'rewrite'             => false,
			'query_var'           => false,
			'exclude_from_search' => true,
			'can_export'          => true,
		)
	);

	register_post_meta(
		'green_site_part',
		'_green_site_role',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'green_core_theme_register_site_part_cpt', 0 );

/**
 * Polylang: permitir tradução do tipo (aparece em Idiomas → Configurações → Tipos de conteúdo).
 */
function green_core_theme_pll_post_types( $post_types, $is_settings ) {
	$post_types['green_site_part'] = 'green_site_part';
	return $post_types;
}
add_filter( 'pll_get_post_types', 'green_core_theme_pll_post_types', 10, 2 );

/**
 * Localiza o post do CPT pelo papel (uma entrada por papel no idioma padrão).
 *
 * @param string $role 'header'|'footer'.
 */
function green_core_theme_find_site_part_by_role( $role ) {
	$args = array(
		'post_type'      => 'green_site_part',
		'post_status'    => array( 'publish', 'draft', 'private' ),
		'meta_key'       => '_green_site_role',
		'meta_value'     => $role,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	);
	if ( function_exists( 'pll_default_language' ) ) {
		$args['lang'] = pll_default_language();
	}
	$q = new WP_Query( $args );
	return ! empty( $q->posts[0] ) ? (int) $q->posts[0] : 0;
}

/**
 * Localiza cabeçalho/rodapé publicado num idioma específico (quando não há tradução ligada ao canónico).
 *
 * @param string $role 'header'|'footer'.
 * @param string $lang Código Polylang (ex.: en, pt).
 * @return int ID do post ou 0.
 */
function green_core_theme_find_site_part_in_language( $role, $lang ) {
	if ( ! $role || ! $lang ) {
		return 0;
	}
	$candidate_ids = get_posts(
		array(
			'post_type'              => 'green_site_part',
			'post_status'            => 'publish',
			'posts_per_page'         => 50,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'suppress_filters'       => false,
			'meta_query'             => array(
				array(
					'key'   => '_green_site_role',
					'value' => $role,
				),
			),
		)
	);
	if ( empty( $candidate_ids ) ) {
		return 0;
	}
	if ( function_exists( 'pll_get_post_language' ) ) {
		foreach ( $candidate_ids as $pid ) {
			if ( pll_get_post_language( (int) $pid ) === $lang ) {
				return (int) $pid;
			}
		}
		return 0;
	}
	return (int) $candidate_ids[0];
}

/**
 * ID canónico (idioma padrão Polylang) guardado nas opções.
 *
 * @param string $role 'header'|'footer'.
 */
function green_core_theme_get_canonical_site_part_id( $role ) {
	$opt = ( 'header' === $role ) ? GREEN_CORE_SITE_PART_HEADER : GREEN_CORE_SITE_PART_FOOTER;
	$id  = (int) get_option( $opt, 0 );
	if ( $id > 0 && get_post( $id ) ) {
		return $id;
	}
	return green_core_theme_find_site_part_by_role( $role );
}

/**
 * Objeto post do cabeçalho/rodapé no idioma atual (Polylang).
 *
 * @param string $role 'header'|'footer'.
 * @return WP_Post|null
 */
function green_core_theme_get_site_part_post( $role ) {
	$id = green_core_theme_get_canonical_site_part_id( $role );
	if ( $id <= 0 ) {
		return null;
	}

	$resolved_id = $id;

	if ( function_exists( 'pll_get_post' ) && function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language();
		if ( $lang ) {
			$translated = pll_get_post( $id, $lang );
			if ( $translated ) {
				$resolved_id = (int) $translated;
			} else {
				$by_lang = green_core_theme_find_site_part_in_language( $role, $lang );
				if ( $by_lang > 0 ) {
					$resolved_id = $by_lang;
				}
			}
		}
	}

	$post = get_post( $resolved_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return null;
	}

	if ( function_exists( 'pll_current_language' ) && function_exists( 'pll_get_post_language' ) ) {
		$curlang = pll_current_language();
		$post_lang = pll_get_post_language( $post->ID );
		if ( $curlang && $post_lang && $post_lang !== $curlang ) {
			$fixed = green_core_theme_find_site_part_in_language( $role, $curlang );
			if ( $fixed > 0 ) {
				$alt = get_post( $fixed );
				if ( $alt && 'publish' === $alt->post_status ) {
					$post = $alt;
				}
			}
		}
	}

	return $post;
}

/**
 * Cria os dois blocos se não existirem e sincroniza opções.
 */
function green_core_theme_ensure_site_parts() {
	$defs = array(
		'header' => array(
			'title'   => __( 'Cabeçalho do site', 'green-core-theme' ),
			'content' => '',
		),
		'footer' => array(
			'title'   => __( 'Rodapé do site', 'green-core-theme' ),
			'content' => green_core_theme_default_footer_block_markup(),
		),
	);

	foreach ( $defs as $role => $def ) {
		$existing = green_core_theme_find_site_part_by_role( $role );
		if ( $existing > 0 ) {
			$opt = ( 'header' === $role ) ? GREEN_CORE_SITE_PART_HEADER : GREEN_CORE_SITE_PART_FOOTER;
			if ( ! get_option( $opt, 0 ) ) {
				update_option( $opt, $existing );
			}
			continue;
		}

		$new_id = wp_insert_post(
			array(
				'post_type'    => 'green_site_part',
				'post_status'  => 'publish',
				'post_title'   => $def['title'],
				'post_content' => $def['content'],
			),
			true
		);

		if ( is_wp_error( $new_id ) || ! $new_id ) {
			continue;
		}

		update_post_meta( $new_id, '_green_site_role', $role );
		if ( function_exists( 'pll_set_post_language' ) && function_exists( 'pll_default_language' ) ) {
			pll_set_post_language( $new_id, pll_default_language() );
		}

		update_option( ( 'header' === $role ) ? GREEN_CORE_SITE_PART_HEADER : GREEN_CORE_SITE_PART_FOOTER, (int) $new_id );
	}
}

/**
 * Migra conteúdo das páginas antigas (opções legadas) para o CPT.
 */
function green_core_theme_migrate_legacy_layout_pages_to_cpt() {
	if ( get_option( 'green_core_legacy_site_part_migrated' ) ) {
		return;
	}

	green_core_theme_ensure_site_parts();

	$h_cpt = green_core_theme_find_site_part_by_role( 'header' );
	$f_cpt = green_core_theme_find_site_part_by_role( 'footer' );

	$h_old = (int) get_option( GREEN_CORE_HEADER_PAGE_OPTION, 0 );
	$f_old = (int) get_option( GREEN_CORE_FOOTER_PAGE_OPTION, 0 );

	if ( $h_old > 0 && $h_cpt > 0 ) {
		$p = get_post( $h_old );
		if ( $p && 'page' === $p->post_type && '' !== trim( (string) $p->post_content ) ) {
			wp_update_post(
				array(
					'ID'           => $h_cpt,
					'post_content' => $p->post_content,
				)
			);
		}
	}

	if ( $f_old > 0 && $f_cpt > 0 ) {
		$p = get_post( $f_old );
		if ( $p && 'page' === $p->post_type && '' !== trim( (string) $p->post_content ) ) {
			wp_update_post(
				array(
					'ID'           => $f_cpt,
					'post_content' => $p->post_content,
				)
			);
		}
	}

	update_option( 'green_core_legacy_site_part_migrated', 1 );
}

add_action( 'after_setup_theme', 'green_core_theme_ensure_site_parts', 15 );
add_action( 'after_setup_theme', 'green_core_theme_migrate_legacy_layout_pages_to_cpt', 25 );
add_action( 'after_switch_theme', 'green_core_theme_ensure_site_parts' );
add_action( 'after_switch_theme', 'green_core_theme_migrate_legacy_layout_pages_to_cpt' );

/**
 * Compatibilidade com funções antigas (templates).
 *
 * @return int
 */
function green_core_theme_get_header_page_id() {
	return green_core_theme_get_canonical_site_part_id( 'header' );
}

/**
 * @return int
 */
function green_core_theme_get_footer_page_id() {
	return green_core_theme_get_canonical_site_part_id( 'footer' );
}

/**
 * Prefixa URLs do menu que são só âncora (#secao) com a URL da página inicial no idioma atual (Polylang).
 * Assim, a partir de outras páginas, o clique abre a home correta + hash.
 *
 * @param array<int,WP_Post> $items Itens do menu.
 * @param object               $args  Argumentos de wp_nav_menu.
 * @return array<int,WP_Post>
 */
function green_core_theme_nav_menu_prefix_hash_urls( $items, $args ) {
	if ( ! is_array( $items ) ) {
		return $items;
	}
	$base = trailingslashit( home_url( '/' ) );
	foreach ( $items as $item ) {
		if ( empty( $item->url ) || ! is_string( $item->url ) ) {
			continue;
		}
		if ( '#' === substr( $item->url, 0, 1 ) && strlen( $item->url ) > 1 ) {
			$item->url = $base . $item->url;
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'green_core_theme_nav_menu_prefix_hash_urls', 20, 2 );

/**
 * Corrige href="#..." em blocos Navegação / lista de páginas no rodapé.
 *
 * @param string               $block_content HTML renderizado.
 * @param array<string,mixed> $block       Bloco.
 * @return string
 */
function green_core_theme_render_block_prefix_hash_links( $block_content, $block ) {
	if ( ! is_array( $block ) || empty( $block['blockName'] ) ) {
		return $block_content;
	}
	$name = $block['blockName'];
	if ( 'core/navigation' !== $name && 'core/page-list' !== $name && 'core/navigation-link' !== $name && 'core/navigation-submenu' !== $name ) {
		return $block_content;
	}
	$base = trailingslashit( home_url( '/' ) );
	return preg_replace_callback(
		'/href="(#[^"#]+)"/',
		function ( $m ) use ( $base ) {
			return 'href="' . esc_url( $base . $m[1] ) . '"';
		},
		$block_content
	);
}
add_filter( 'render_block', 'green_core_theme_render_block_prefix_hash_links', 20, 2 );
