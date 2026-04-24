<?php
/**
 * Migração da homepage em inglês (Polylang: página ligada ao PT).
 *
 * @package GreenNativeBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/homepage-migration-data.php';

/**
 * @param mixed $data
 * @param array<string,string> $map
 * @return mixed
 */
function green_nb_blocks_map_strings_recursive( $data, array $map ) {
	if ( is_array( $data ) ) {
		foreach ( $data as $k => $v ) {
			$data[ $k ] = green_nb_blocks_map_strings_recursive( $v, $map );
		}
		return $data;
	}
	if ( is_string( $data ) && $data !== '' && isset( $map[ $data ] ) ) {
		return $map[ $data ];
	}
	return $data;
}

/**
 * Blocos da homepage em inglês (mapa PT→EN sobre o dataset PT).
 *
 * @return array<int, array<string, mixed>>
 */
function green_nb_get_homepage_migration_blocks_en() {
	$map = require __DIR__ . '/homepage-en-map.php';
	return green_nb_blocks_map_strings_recursive( green_nb_get_homepage_migration_blocks(), $map );
}

/**
 * Aplica migração à página inicial em inglês (tradução Polylang).
 *
 * @param int $pt_home_id ID da homepage em PT (0 = page_on_front).
 * @return bool|WP_Error
 */
function green_nb_apply_homepage_migration_en( $pt_home_id = 0 ) {
	if ( ! function_exists( 'serialize_blocks' ) ) {
		return new WP_Error( 'green_nb_no_blocks', 'serialize_blocks não disponível.' );
	}

	if ( ! function_exists( 'pll_get_post' ) ) {
		return new WP_Error( 'green_nb_no_pll', 'Polylang não está ativo.' );
	}

	$pt_home_id = (int) ( $pt_home_id > 0 ? $pt_home_id : get_option( 'page_on_front' ) );
	if ( $pt_home_id <= 0 ) {
		return new WP_Error( 'green_nb_invalid_pt', 'ID da homepage em português inválido.' );
	}

	$en_id = pll_get_post( $pt_home_id, 'en' );
	if ( ! $en_id ) {
		return new WP_Error( 'green_nb_no_en_page', 'Não existe página em inglês associada à homepage em PT. Crie a tradução no Polylang primeiro.' );
	}

	$blocks  = green_nb_get_homepage_migration_blocks_en();
	$content = serialize_blocks( $blocks );

	$updated = wp_update_post(
		array(
			'ID'           => (int) $en_id,
			'post_content' => $content,
		),
		true
	);

	if ( is_wp_error( $updated ) ) {
		return $updated;
	}

	update_post_meta( (int) $en_id, '_wp_page_template', 'default' );

	return true;
}
