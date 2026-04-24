<?php
/**
 * Seletor de idioma (Polylang) com prioridade a bandeiras oficiais do tema (PT/EN) e nome só para leitores de ecrã.
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SVG mínimo: Brasil (só se o ficheiro PNG do tema estiver em falta).
 *
 * @return string HTML.
 */
function green_core_theme_flag_svg_brazil() {
	return '<svg class="green-lang-flag-icon" viewBox="0 0 20 14" width="22" height="16" role="img" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="14" fill="#009B3A"/><path d="M10 1.8l6.2 4.2L10 10.2 3.8 6z" fill="#FFDF00"/><circle cx="10" cy="6" r="1.5" fill="#002776"/></svg>';
}

/**
 * SVG mínimo: EUA (só se o ficheiro PNG do tema estiver em falta).
 *
 * @return string HTML.
 */
function green_core_theme_flag_svg_usa() {
	return '<svg class="green-lang-flag-icon" viewBox="0 0 20 14" width="22" height="16" role="img" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="14" fill="#B22234"/><rect width="8" height="8" fill="#3C3B6E"/><path fill="#fff" d="M0 1h20v1H0zm0 2h20v1H0zm0 2h20v1H0zm0 2h20v1H0zm0 2h20v1H0zm0 2h20v1H0z"/></svg>';
}

/**
 * URL do ícone de bandeira do tema (ficheiros em assets/flags/) para o código de idioma Polylang.
 *
 * @param string $slug Ex.: pt, pt_br, en.
 * @return string URL absoluta ou string vazia.
 */
function green_core_theme_get_brand_flag_url( $slug ) {
	$slug = is_string( $slug ) ? $slug : '';
	if ( in_array( $slug, array( 'pt', 'pt_br' ), true ) ) {
		$file = 'flag-brazil.png';
	} elseif ( 'en' === $slug ) {
		$file = 'flag-usa.png';
	} else {
		return '';
	}
	$path = get_template_directory() . '/assets/flags/' . $file;
	if ( ! is_readable( $path ) ) {
		return '';
	}
	return get_template_directory_uri() . '/assets/flags/' . $file;
}

/**
 * Marcação <img> para a bandeira de marca (PNG) com tamanho coerente no cabeçalho.
 *
 * @param string $src    URL do recurso.
 * @param int    $w      Largura de referência (atributo width).
 * @param int    $h      Altura de referência (atributo height).
 * @return string HTML.
 */
function green_core_theme_flag_brand_img( $src, $w, $h ) {
	return '<img class="green-lang-flag-img green-lang-flag-img--brand" src="'
		. esc_url( $src ) . '" alt="" width="'
		. (int) $w . '" height="' . (int) $h
		. '" loading="lazy" decoding="async" />';
}

/**
 * Normaliza a saída de bandeira do Polylang: com raw=1 e show_flags=0 o plugin devolve só o URL;
 * nesse caso envolve em <img> para não aparecer como texto no HTML.
 *
 * @param string $flag Valor de $lang['flag'].
 * @return string HTML (img) ou texto escapado.
 */
function green_core_theme_normalize_polylang_flag( $flag ) {
	$flag = is_string( $flag ) ? trim( $flag ) : '';
	if ( '' === $flag ) {
		return '';
	}
	if ( (bool) preg_match( '/^\s*</', $flag ) ) {
		return $flag;
	}
	if ( preg_match( '#^https?://#i', $flag ) || preg_match( '#^/[\w\-.~:/?#&%;=@+\[\]_]+$#i', $flag ) ) {
		$src = esc_url( $flag );
		return '<img class="green-lang-flag-img" src="' . $src . '" alt="" width="24" height="18" loading="lazy" decoding="async" />';
	}
	return esc_html( $flag );
}

/**
 * Exibe a lista de idiomas com bandeira (dados de Polylang) e nome só para leitores de ecrã.
 */
function green_core_theme_render_language_switcher() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}
	$languages = pll_the_languages(
		array(
			'raw'                    => 1,
			'show_flags'             => 1,
			'show_names'             => 0,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
		)
	);
	if ( empty( $languages ) || ! is_array( $languages ) ) {
		return;
	}
	echo '<ul class="menu green-header-lang-list">';
	foreach ( $languages as $lang ) {
		if ( ! is_array( $lang ) ) {
			continue;
		}
		$is_current = ! empty( $lang['current_lang'] );
		$classes    = array();
		if ( ! empty( $lang['classes'] ) && is_array( $lang['classes'] ) ) {
			$classes = $lang['classes'];
		} else {
			$classes = array( 'lang-item' );
			if ( $is_current ) {
				$classes[] = 'current-lang';
			}
		}
		$classes   = is_array( $classes ) ? $classes : array( (string) $classes );
		$url       = isset( $lang['url'] ) ? $lang['url'] : '#';
		$slug      = isset( $lang['slug'] ) ? $lang['slug'] : '';
		$name      = isset( $lang['name'] ) ? $lang['name'] : $slug;
		$flag_html = ! empty( $lang['flag'] ) ? (string) $lang['flag'] : '';
		$class_str = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
		echo '<li class="' . esc_attr( $class_str ) . '">';
		echo '<a href="' . esc_url( $url ) . '" class="green-header-lang-link"';
		if ( $slug ) {
			echo ' hreflang="' . esc_attr( $slug ) . '"';
		}
		if ( $is_current ) {
			echo ' aria-current="true"';
		}
		echo '>';
		$brand_src = green_core_theme_get_brand_flag_url( $slug );
		if ( $brand_src ) {
			// Ícones de marca: PNG em assets/flags/ (proporção ~4:3, cantos arredondados no próprio ficheiro).
			echo wp_kses_post( green_core_theme_flag_brand_img( $brand_src, 28, 22 ) );
		} elseif ( $flag_html !== '' ) {
			$normalized = green_core_theme_normalize_polylang_flag( $flag_html );
			echo wp_kses_post( $normalized );
		} elseif ( in_array( $slug, array( 'pt', 'pt_br' ), true ) ) {
			echo green_core_theme_flag_svg_brazil(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( 'en' === $slug ) {
			echo green_core_theme_flag_svg_usa(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo '<span class="green-lang-fallback-initials">' . esc_html( strtoupper( (string) $slug ) ) . '</span>';
		}
		echo '<span class="screen-reader-text">' . esc_html( $name ) . '</span>';
		echo '</a></li>';
	}
	echo '</ul>';
}
