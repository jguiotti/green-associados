<?php
/**
 * Configuração base de SEO para Yoast (versão gratuita).
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Palavras-chave estratégicas para conteúdo e metadados.
 *
 * @return array<int,string>
 */
function green_core_theme_seo_target_keywords() {
	return array(
		'tradução financeira profissional',
		'tradução de documentos ri',
		'tradução para relações com investidores',
		'tradução de documentos cvm e sec',
		'serviços de tradução em inglês e espanhol',
		'tradução de relatórios anuais',
		'tradução de release de resultados e demonstrações financeiras',
		'agência de tradução técnica especializada',
	);
}

/**
 * Define automaticamente metadados Yoast para a página inicial.
 *
 * @param int    $page_id ID da página.
 * @param string $lang    Código de idioma.
 * @return void
 */
function green_core_theme_set_yoast_homepage_meta( $page_id, $lang = '' ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return;
	}

	$site_name = get_bloginfo( 'name' );
	$is_en     = ( 'en' === $lang );

	$title = $is_en
		? 'Financial Translation for Investor Relations and Regulatory Filings'
		: 'Tradução Financeira para RI, CVM e SEC';

	$description = $is_en
		? 'Specialized agency for financial and technical translation: annual reports, earnings releases, RI documents, and English-Spanish language services.'
		: 'Agência especializada em tradução financeira e técnica para RI, CVM e SEC: relatórios anuais, releases de resultados e serviços em inglês e espanhol.';

	$focus_keyword = $is_en
		? 'financial translation for investor relations'
		: 'tradução financeira profissional';

	update_post_meta( $page_id, '_yoast_wpseo_title', $title . ' | %%sitename%%' );
	update_post_meta( $page_id, '_yoast_wpseo_metadesc', $description );
	update_post_meta( $page_id, '_yoast_wpseo_focuskw', $focus_keyword );
	update_post_meta( $page_id, '_yoast_wpseo_canonical', home_url( '/' ) );

	if ( function_exists( 'pll_home_url' ) && $lang ) {
		update_post_meta( $page_id, '_yoast_wpseo_canonical', pll_home_url( $lang ) );
	}

	if ( '' === trim( (string) get_post_meta( $page_id, '_yoast_wpseo_opengraph-title', true ) ) ) {
		update_post_meta( $page_id, '_yoast_wpseo_opengraph-title', $title . ' | ' . $site_name );
	}
	if ( '' === trim( (string) get_post_meta( $page_id, '_yoast_wpseo_opengraph-description', true ) ) ) {
		update_post_meta( $page_id, '_yoast_wpseo_opengraph-description', $description );
	}
	if ( '' === trim( (string) get_post_meta( $page_id, '_yoast_wpseo_twitter-title', true ) ) ) {
		update_post_meta( $page_id, '_yoast_wpseo_twitter-title', $title . ' | ' . $site_name );
	}
	if ( '' === trim( (string) get_post_meta( $page_id, '_yoast_wpseo_twitter-description', true ) ) ) {
		update_post_meta( $page_id, '_yoast_wpseo_twitter-description', $description );
	}
}

/**
 * Configuração inicial de SEO do Yoast para o projeto.
 *
 * @return void
 */
function green_core_theme_configure_yoast_seo() {
	if ( ! is_admin() ) {
		return;
	}
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return;
	}

	$config_version = '1.0.0';
	if ( get_option( 'green_core_theme_yoast_setup_version' ) === $config_version ) {
		return;
	}

	$site_name = get_bloginfo( 'name' );

	$titles = get_option( 'wpseo_titles', array() );
	if ( ! is_array( $titles ) ) {
		$titles = array();
	}

	$titles['company_or_person']      = 'company';
	$titles['company_name']           = $site_name;
	$titles['title-home-wpseo']       = 'Tradução Financeira para RI, CVM e SEC %%sep%% %%sitename%%';
	$titles['metadesc-home-wpseo']    = 'Agência especializada em tradução financeira e técnica para RI, CVM e SEC: relatórios anuais, releases de resultados e serviços em inglês e espanhol.';
	$titles['title-pt-home-wpseo']    = 'Tradução Financeira para RI, CVM e SEC %%sep%% %%sitename%%';
	$titles['metadesc-pt-home-wpseo'] = 'Agência especializada em tradução financeira e técnica para RI, CVM e SEC: relatórios anuais, releases de resultados e serviços em inglês e espanhol.';

	update_option( 'wpseo_titles', $titles );

	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id > 0 ) {
		green_core_theme_set_yoast_homepage_meta( $front_page_id );
		if ( function_exists( 'pll_get_post_translations' ) ) {
			$translations = pll_get_post_translations( $front_page_id );
			if ( is_array( $translations ) ) {
				foreach ( $translations as $lang => $translated_id ) {
					green_core_theme_set_yoast_homepage_meta( (int) $translated_id, (string) $lang );
				}
			}
		}
	}

	update_option( 'green_core_theme_seo_keywords', green_core_theme_seo_target_keywords() );
	update_option( 'green_core_theme_yoast_setup_version', $config_version );
}
add_action( 'admin_init', 'green_core_theme_configure_yoast_seo' );

