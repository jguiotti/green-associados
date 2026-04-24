<?php
/**
 * Migração assistida da Homepage — preenche atributos dos blocos Green a partir do dataset em includes/homepage-migration-data.php.
 *
 * Uso (a partir da pasta `wordpress`):
 *   wp eval-file wp-content/plugins/green-native-builder/migrate-homepage.php
 *
 * @package GreenNativeBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	require_once dirname( __DIR__, 3 ) . '/wp-load.php';
}

require_once __DIR__ . '/includes/homepage-migration-data.php';

$home_id = (int) get_option( 'page_on_front' );
if ( $home_id <= 0 ) {
	$home_id = 13;
}

$result = green_nb_apply_homepage_migration( $home_id );

if ( is_wp_error( $result ) ) {
	fwrite( STDERR, $result->get_error_message() . PHP_EOL );
	exit( 1 );
}

echo 'Homepage migrada (post ID ' . $home_id . ').' . PHP_EOL;
