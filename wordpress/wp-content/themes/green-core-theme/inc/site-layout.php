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
	if ( ! is_readable( $path ) ) {
		return '';
	}
	$html = (string) file_get_contents( $path );
	$aria = esc_attr(
		sprintf(
			/* translators: %s: site title. */
			__( '%s no LinkedIn', 'green-core-theme' ),
			get_bloginfo( 'name', 'display' )
		)
	);
	return str_replace(
		array( '%GREEN_LINKEDIN_ICON%', '%GREEN_LINKEDIN_HREF%', '%GREEN_LINKEDIN_ARIA%' ),
		array(
			green_core_theme_get_footer_linkedin_icon_url(),
			green_core_theme_get_footer_linkedin_href(),
			$aria,
		),
		$html
	);
}

/**
 * Ícone LinkedIn empacotado no tema (PNG).
 *
 * @return string URL escapado.
 */
function green_core_theme_get_footer_linkedin_icon_url() {
	return esc_url( get_stylesheet_directory_uri() . '/assets/linkedin-icon.png' );
}

/**
 * URL institucional do LinkedIn (filtro permite override).
 *
 * @return string URL escapado.
 */
function green_core_theme_get_footer_linkedin_href() {
	return esc_url( apply_filters( 'green_core_theme_footer_linkedin_url', 'https://www.linkedin.com/company/green-associados-tradu-es/' ) );
}

/**
 * Bloco HTML com link + ícone para o primeiro rodapé.
 *
 * @return string Marcador Gutenberg (core/html).
 */
function green_core_theme_build_footer_linkedin_html_block() {
	$icon = green_core_theme_get_footer_linkedin_icon_url();
	$href = green_core_theme_get_footer_linkedin_href();
	$aria = esc_attr(
		sprintf(
			/* translators: %s: site title (Nome do site nos ajustos gerais). */
			__( '%s no LinkedIn', 'green-core-theme' ),
			get_bloginfo( 'name', 'display' )
		)
	);
	$inner =
		'<div class="green-footer-linkedin-wrap">' .
		'<a class="green-footer-linkedin" href="' . $href . '" target="_blank" rel="noopener noreferrer" aria-label="' . $aria . '">' .
		'<img class="green-footer-linkedin__img" src="' . $icon . '" alt="" width="32" height="32" decoding="async" loading="lazy" />' .
		'</a></div>';
	return "<!-- wp:html -->\n" . $inner . "\n<!-- /wp:html -->";
}

/**
 * Substitui tokens no markup do rodapé e garante ícone LinkedIn quando o modelo antigo ainda só tem o slogan.
 *
 * @param string $content post_content bruto ou já com marcadores substituídos.
 * @return string
 */
function green_core_theme_prepare_footer_markup_for_display( $content ) {
	$content = is_string( $content ) ? $content : '';
	if ( '' === $content ) {
		return '';
	}
	$content = green_core_theme_replace_footer_linkedin_placeholder_tokens( $content );
	if ( false !== strpos( $content, 'green-footer-site-cols' ) ) {
		$content = green_core_theme_footer_dedupe_site_logo_self_closing_blocks( $content );
	}
	if ( false !== strpos( $content, 'green-footer-linkedin-wrap' ) ) {
		return $content;
	}
	if ( false === strpos( $content, 'green-footer-site-cols' ) ) {
		return $content;
	}
	return green_core_theme_footer_maybe_append_linkedin_any( $content );
}

/**
 * Remove blocos site-logo auto-fechados repetidos (pattern já traz um; cópias Polylang/migrações geravam dois logos).
 *
 * @param string $content Conteúdo bruto do rodapé.
 * @return string
 */
function green_core_theme_footer_dedupe_site_logo_self_closing_blocks( $content ) {
	$pattern = '/<!--\s*wp:site-logo[^>]*\/-->\s*/';
	if ( ! preg_match_all( $pattern, $content, $m, PREG_SET_ORDER ) || count( $m ) < 2 ) {
		return $content;
	}
	$keep_first = true;
	$out        = preg_replace_callback(
		$pattern,
		static function ( array $match ) use ( &$keep_first ) {
			if ( $keep_first ) {
				$keep_first = false;
				return $match[0];
			}
			return '';
		},
		$content
	);
	return is_string( $out ) ? $out : $content;
}

/**
 * @param string $content Conteúdo do rodapé.
 * @return string
 */
function green_core_theme_replace_footer_linkedin_placeholder_tokens( $content ) {
	if ( false === strpos( $content, '%GREEN_LINKEDIN_' ) ) {
		return $content;
	}
	$aria = esc_attr(
		sprintf(
			/* translators: %s: site title. */
			__( '%s no LinkedIn', 'green-core-theme' ),
			get_bloginfo( 'name', 'display' )
		)
	);
	return str_replace(
		array( '%GREEN_LINKEDIN_ICON%', '%GREEN_LINKEDIN_HREF%', '%GREEN_LINKEDIN_ARIA%' ),
		array(
			green_core_theme_get_footer_linkedin_icon_url(),
			green_core_theme_get_footer_linkedin_href(),
			$aria,
		),
		$content
	);
}

/**
 * Fallback: primeira coluna do rodapé sem className green-footer-lead — ancora LinkedIn depois do último </wp:paragraph> da coluna.
 *
 * @param string $content Conteúdo do rodapé.
 * @param string $inject  Bloco HTML do LinkedIn (com marcação wp:html).
 * @return string
 */
function green_core_theme_footer_append_linkedin_after_first_paragraph_first_column( $content, $inject ) {
	if ( false !== strpos( $content, 'green-footer-linkedin-wrap' ) ) {
		return $content;
	}
	$markers = strpos( $content, 'green-footer-site-cols' );
	if ( false === $markers ) {
		return $content;
	}
	$slice = substr( $content, $markers );
	$c1    = strpos( $slice, '<!-- wp:column -->' );
	if ( false === $c1 ) {
		return $content;
	}
	$abs_c1 = $markers + $c1;

	$post_col_comment = substr( $content, $abs_c1 + strlen( '<!-- wp:column -->' ) );
	$d0               = strpos( $post_col_comment, '<div' );
	if ( false === $d0 ) {
		return $content;
	}
	$d_close = strpos( $post_col_comment, '>', $d0 );
	if ( false === $d_close ) {
		return $content;
	}

	$inner_start = $abs_c1 + strlen( '<!-- wp:column -->' ) + $d_close + 1;
	$from_inner  = substr( $content, $inner_start );
	$c2_offset   = strpos( $from_inner, '<!-- wp:column -->' );
	if ( false === $c2_offset ) {
		return $content;
	}
	$chunk = substr( $from_inner, 0, $c2_offset );

	$needle_par = '<!-- /wp:paragraph -->';
	$p           = strrpos( $chunk, $needle_par );
	if ( false !== $p ) {
		$insert_at = $inner_start + $p + strlen( $needle_par );
		return substr( $content, 0, $insert_at ) . "\n\n" . $inject . substr( $content, $insert_at );
	}
	if ( false === strpos( $chunk, '<!-- wp:paragraph' ) ) {
		$needle_logo = '<!-- /wp:site-logo -->';
		$lg          = strrpos( $chunk, $needle_logo );
		if ( false !== $lg ) {
			$insert_at = $inner_start + $lg + strlen( $needle_logo );
			return substr( $content, 0, $insert_at ) . "\n\n" . $inject . substr( $content, $insert_at );
		}
	}
	return $content;
}

/**
 * Ancora LinkedIn logo abaixo do slogan (detecta slogan com className OU primeiro parágrafo da 1.ª coluna).
 *
 * @param string $content Conteúdo do rodapé.
 * @return string
 */
function green_core_theme_footer_maybe_append_linkedin_any( $content ) {
	if ( false !== strpos( $content, 'green-footer-linkedin-wrap' ) ) {
		return $content;
	}
	$inject = green_core_theme_build_footer_linkedin_html_block();
	$pat    = '/(<!-- wp:paragraph[^\/>]*green-footer-lead[^\/>]*-->\s*<p[^>]*green-footer-lead[^>]*>.*?<\/p>\s*<!-- \/wp:paragraph -->)/s';
	$out    = preg_replace( $pat, '$1' . "\n\n" . $inject, $content, 1 );
	if ( null !== $out && false !== strpos( $out, 'green-footer-linkedin-wrap' ) ) {
		return $out;
	}
	return green_core_theme_footer_append_linkedin_after_first_paragraph_first_column( $content, $inject );
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
