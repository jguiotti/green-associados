<?php
/**
 * Plugin Name: Green Native Builder
 * Description: Blocos Gutenberg proprietários para a Homepage da Green Associados.
 * Version: 1.2.5
 * Author: Green Associados
 * Text Domain: green-native-builder
 *
 * @package GreenNativeBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function green_nb_asset_url( $file ) {
	return plugin_dir_url( __FILE__ ) . ltrim( $file, '/' );
}

function green_nb_sanitize_text( $value ) {
	return is_string( $value ) ? trim( wp_kses_post( $value ) ) : '';
}

function green_nb_sanitize_url( $value ) {
	$url = is_string( $value ) ? trim( $value ) : '';
	return $url ? esc_url( $url ) : '';
}

/**
 * Resolve URLs do fundo do Hero: imagem única vs carrossel (medias na biblioteca).
 *
 * @param array<string,mixed> $attributes Atributos do bloco.
 * @return array{mode:string,urls:string[],carousel:bool}
 */
function green_nb_resolve_hero_backgrounds( $attributes ) {
	$mode = ( isset( $attributes['backgroundMode'] ) && 'carousel' === $attributes['backgroundMode'] ) ? 'carousel' : 'single';
	$ids  = isset( $attributes['backgroundImageIds'] ) && is_array( $attributes['backgroundImageIds'] )
		? array_values( array_filter( array_map( 'absint', $attributes['backgroundImageIds'] ) ) )
		: array();
	$url_fallback = green_nb_sanitize_url( $attributes['backgroundUrl'] ?? '' );

	if ( 'carousel' === $mode && count( $ids ) >= 2 ) {
		$urls = array();
		foreach ( $ids as $id ) {
			$u = wp_get_attachment_image_url( $id, 'full' );
			if ( $u ) {
				$urls[] = esc_url_raw( $u );
			}
		}
		if ( count( $urls ) >= 2 ) {
			return array(
				'mode'          => 'carousel',
				'urls'          => $urls,
				'carousel'      => true,
			);
		}
	}

	if ( count( $ids ) > 0 && ! empty( $ids[0] ) ) {
		$u = wp_get_attachment_image_url( $ids[0], 'full' );
		if ( $u ) {
			$u = esc_url_raw( $u );
			return array(
				'mode'     => 'single',
				'urls'     => array( $u ),
				'carousel' => false,
			);
		}
	}

	if ( '' !== $url_fallback ) {
		return array(
			'mode'     => 'single',
			'urls'     => array( $url_fallback ),
			'carousel' => false,
		);
	}

	return array(
		'mode'     => 'single',
		'urls'     => array(),
		'carousel' => false,
	);
}

/**
 * Constrói o href `tel:` a partir do texto do telefone guardado no bloco (dígitos; acrescenta +55 se faltar DDI em números BR).
 *
 * @param string $phone Texto do campo telefone.
 * @return string Atributo href seguro ou string vazia se inválido.
 */
function green_nb_phone_to_tel_href( $phone ) {
	$phone = is_string( $phone ) ? trim( $phone ) : '';
	if ( '' === $phone ) {
		return '';
	}
	$digits = preg_replace( '/\D+/', '', $phone );
	$digits = ltrim( $digits, '0' );
	if ( '' === $digits || strlen( $digits ) < 8 ) {
		return '';
	}
	// Números nacionais BR (10 ou 11 dígitos) sem código 55.
	if ( strlen( $digits ) >= 10 && strlen( $digits ) <= 11 && 0 !== strpos( $digits, '55' ) ) {
		$digits = '55' . $digits;
	}
	return esc_url( 'tel:+' . $digits );
}

/**
 * Constrói o href `mailto:` a partir do e-mail guardado no bloco.
 *
 * @param string $email Texto do campo e-mail.
 * @return string Atributo href seguro ou string vazia se inválido.
 */
function green_nb_email_to_mailto_href( $email ) {
	$email = is_string( $email ) ? trim( $email ) : '';
	if ( '' === $email ) {
		return '';
	}
	$addr = sanitize_email( $email );
	if ( '' === $addr || ! is_email( $addr ) ) {
		return '';
	}
	return esc_url( 'mailto:' . $addr );
}

/**
 * ID HTML da secção: bloco «Âncora HTML» (supports.anchor) ou valor por omissão.
 *
 * @param array<string,mixed> $attributes Atributos do bloco.
 * @param string              $default  ID sugerido (ex.: atuacao) se não houver âncora.
 * @return string Slug vazio ou seguro para id="".
 */
function green_nb_get_section_id( $attributes, $default = '' ) {
	$anchor = isset( $attributes['anchor'] ) ? trim( (string) $attributes['anchor'] ) : '';
	if ( '' === $anchor ) {
		$anchor = $default;
		if ( function_exists( 'pll_current_language' ) && 'en' === pll_current_language() ) {
			$en_defaults = array(
				'atuacao'   => 'areas-of-expertise',
				'ia'        => 'ai',
				'seguranca' => 'security',
				'equipe'    => 'team',
				'contato'   => 'contact',
			);
			if ( isset( $en_defaults[ $default ] ) ) {
				$anchor = $en_defaults[ $default ];
			}
		}
	}
	if ( '' === $anchor ) {
		return '';
	}
	$sanitized = sanitize_title( $anchor );
	return '' !== $sanitized ? $sanitized : '';
}

/**
 * Normaliza cor hexadecimal segura para atributos fill em SVG.
 *
 * @param string $value Cor (com ou sem #). Entrada inválida devolve branco.
 * @return string Com #, 6 hex.
 */
function green_nb_sanitize_hex_color_str( $value ) {
	$s = ltrim( is_string( $value ) ? trim( $value ) : '', '#' );
	if ( 6 === strlen( $s ) && preg_match( '/^[0-9A-Fa-f]+$/', $s ) ) {
		return '#' . $s;
	}
	return '#ffffff';
}

/**
 * Caminho: arco convexo (parabólica simétrica, Bézier quadrática Q só — sem C/S).
 * viewBox 0 0 1440 100.
 */
function green_nb_arc_path_convex_q() {
	return 'M0,0 Q720,100 1440,0 L1440,100 L0,100 Z';
}

/**
 * Arco côncavo (topo de secção) — mesmo eixo, Q simétrico.
 */
function green_nb_arc_path_concave_q() {
	return 'M0,100 Q720,0 1440,100 L1440,0 L0,0 Z';
}

/**
 * Classe `text-*` (fill-current) a partir de #hex normalizado.
 *
 * @param string $key_hex #hex
 * @return string ex.: text-primary, text-surface, text-white.
 */
function green_nb_divider_text_class_for_next_bg( $key_hex ) {
	$k = strtolower( ltrim( (string) $key_hex, '#' ) );
	if ( '006060' === $k || '0d9488' === $k ) {
		return 'text-primary';
	}
	if ( 'ffffff' === $k || 'fff' === $k ) {
		return 'text-white';
	}
	if ( 'fafaf7' === $k || 'faf7f0' === $k ) {
		return 'text-surface';
	}
	return 'text-primary';
}

/**
 * Arco inferior do hero: convexo, branco (emenda com o corpo claro / cards).
 * Posição absoluta no fundo de `.green-hero-bg` (não entra no flex do conteúdo).
 */
function green_nb_print_divider_hero_bottom() {
	?>
		<div class="green-nb-arc-hero-bottom pointer-events-none absolute bottom-0 left-0 z-[4] w-full h-16 leading-[0] md:h-28" aria-hidden="true">
			<svg class="block absolute -bottom-1 left-0 h-full w-full text-white" preserveAspectRatio="none" viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg" focusable="false">
				<path class="fill-current" d="<?php echo esc_attr( green_nb_arc_path_convex_q() ); ?>"/>
			</svg>
		</div>
		<?php
}

/**
 * Arco convexo — preenchimento = fundo da secção **seguinte** (`fill-current` + `text-*`).
 * Pode colocar-se no fim da secção anterior **ou** no início da seguinte (transição análoga).
 *
 * @param string $next_bg_hex #hex (ex. #006060, #FAFAF7) ou a palavra-chave "surface" / "white" / "primary".
 * @param string $container_extra_class Classes no contentor (ex. bg-white, bg-surface) se a onda fica no topo da secção seguinte e o fill coincidia com o fundo.
 */
function green_nb_print_divider_section_bottom( $next_bg_hex, $container_extra_class = '' ) {
	$in = is_string( $next_bg_hex ) ? trim( $next_bg_hex ) : '';
	if ( 'surface' === strtolower( $in ) ) {
		$text_class = 'text-surface';
	} else {
		$fill         = green_nb_sanitize_hex_color_str( '' !== $in ? $in : '#ffffff' );
		$text_class = green_nb_divider_text_class_for_next_bg( $fill );
	}
	$extra = is_string( $container_extra_class ) && '' !== trim( $container_extra_class ) ? ' ' . trim( $container_extra_class ) : '';
	?>
	<div class="green-nb-arc-join green-nb-wave-between-sections pointer-events-none relative z-[1] h-16 w-full leading-[0] md:h-28<?php echo esc_attr( $extra ); ?>" aria-hidden="true">
		<svg class="<?php echo esc_attr( 'absolute -bottom-1 left-0 block h-full w-full ' . $text_class ); ?>" preserveAspectRatio="none" viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg" focusable="false">
			<path class="fill-current" d="<?php echo esc_attr( green_nb_arc_path_convex_q() ); ?>"/>
		</svg>
	</div>
	<?php
}

/**
 * Arco côncavo no topo de uma secção (sorriso), fill = ligação com a transição.
 *
 * @param string $text_class ex. text-primary, text-white.
 */
function green_nb_print_divider_concave_top( $text_class ) {
	$allow = array( 'text-primary', 'text-white', 'text-surface', 'text-background' );
	$in    = trim( (string) $text_class );
	$tc    = in_array( $in, $allow, true ) ? $in : 'text-white';
	?>
	<div class="green-nb-arc-concave-top pointer-events-none relative z-[1] -mt-px h-16 w-full text-[0] leading-none md:h-28" aria-hidden="true">
		<svg class="<?php echo esc_attr( 'absolute -top-1 left-0 block h-full w-full ' . $tc ); ?>" preserveAspectRatio="none" viewBox="0 0 1440 100" xmlns="http://www.w3.org/2000/svg" focusable="false">
			<path class="fill-current" d="<?php echo esc_attr( green_nb_arc_path_concave_q() ); ?>"/>
		</svg>
	</div>
	<?php
}

/**
 * Títulos de secção: escala e peso (Tailwind) — inclusive em leitores de ecrã (HTML semântico).
 *
 * @param string $tone 'on-dark' | 'on-light'.
 * @return string
 */
function green_nb_section_title_tw( $tone = 'on-light' ) {
	$base = 'green-section-title !text-3xl !leading-tight !tracking-tight !font-extrabold sm:!text-4xl md:!text-5xl';
	if ( 'on-dark' === $tone ) {
		return $base . ' !text-white';
	}
	return $base . ' !text-primary';
}

function green_nb_render_link_button( $text, $url, $class ) {
	$label = green_nb_sanitize_text( $text );
	$link  = green_nb_sanitize_url( $url );
	if ( '' === $label || '' === $link ) {
		return '';
	}
	$tw  = ' !inline-flex !items-center !justify-center !rounded-full !px-8 !py-3 !text-[15px] !font-bold !no-underline transition-transform duration-200 ease-out hover:scale-105 active:scale-95';
	$all = trim( (string) $class ) . $tw;
	return sprintf(
		'<a class="%1$s" href="%2$s">%3$s</a>',
		esc_attr( $all ),
		esc_url( $link ),
		esc_html( $label )
	);
}

/**
 * Reservado: manchas de cor (blur) em secções — desativado para evitar recortes com overflow e ruído visual.
 *
 * @param string $context Ignorado.
 */
function green_nb_render_glow_marks_lite( $context = 'default' ) {
}

/**
 * Reservado: brilho em secções escuras — desativado (ver `green_nb_render_glow_marks_lite`).
 */
function green_nb_render_glow_marks_dark() {
}

/**
 * Classes Tailwind reutilizadas no front (requer script tailwindcss.com do tema).
 *
 * @param string $key Identificador do padrão.
 * @return string
 */
function green_nb_tw( $key ) {
	$map = array(
		'service_card'    => 'green-service-card w-full !max-w-lg !rounded-3xl !border !border-slate-200/90 !bg-stone-50 !px-5 !py-8 !text-left !text-slate-700 !shadow-xl !shadow-slate-900/5 transition-transform duration-300 ease-out will-change-transform sm:!px-7 sm:!py-9 hover:-translate-y-0.5 hover:!shadow-2xl hover:!shadow-teal-900/5',
		'highlight_card'  => 'green-highlight-card !rounded-[2rem] sm:!rounded-[2rem] !border !border-slate-200/80 !bg-white !shadow-xl !shadow-slate-900/6 transition-all !duration-500 will-change-transform hover:-translate-y-0.5 hover:!shadow-2xl hover:!shadow-slate-900/8',
		'security_pillar' => 'green-security-pillar !max-w-sm !border-0 !bg-transparent !p-4 !shadow-none transition-transform duration-500 ease-out md:!p-5 hover:translate-y-[-2px]',
		'ia_feature'      => 'green-ia-feature !mx-auto !w-full !max-w-md !rounded-[1.75rem] !border !border-white/10 !bg-white/5 !p-5 !shadow-none !backdrop-blur-sm sm:!p-6 transition-all duration-500 hover:-translate-y-2 hover:!shadow-2xl',
		'contact_card'    => 'green-contact-card !items-center !rounded-[2rem] !border !border-slate-200/90 !bg-white !text-left !text-slate-800 !shadow-xl !shadow-slate-900/8',
		'team_card'       => 'green-team-card group',
		'team_photo'      => 'green-team-photo rounded-tl-3xl rounded-br-2xl object-cover transition-transform duration-500 group-hover:scale-110',
	);
	return isset( $map[ $key ] ) ? $map[ $key ] : '';
}

/**
 * Ícone SVG do WhatsApp (cor via currentColor no botão).
 *
 * @return string HTML do SVG.
 */
function green_nb_whatsapp_icon_svg() {
	return '<svg class="green-btn-wa-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>';
}

/**
 * Versão do ficheiro CSS (cache-bust a cada deploy).
 */
function green_nb_blocks_asset_version( $file_rel ) {
	$path = __DIR__ . '/' . ltrim( $file_rel, '/' );
	$m    = is_readable( $path ) ? (int) filemtime( $path ) : 0;
	return $m > 0 ? (string) $m : '1.2.5';
}

function green_nb_register_assets() {
	$ver_js  = green_nb_blocks_asset_version( 'assets/js/blocks.js' );
	$ver_css = green_nb_blocks_asset_version( 'assets/css/blocks.css' );
	$ver_hero_carousel = green_nb_blocks_asset_version( 'assets/js/hero-carousel.js' );

	wp_register_script(
		'green-native-builder-blocks',
		green_nb_asset_url( 'assets/js/blocks.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		$ver_js,
		true
	);

	wp_register_script(
		'green-nb-hero-carousel',
		green_nb_asset_url( 'assets/js/hero-carousel.js' ),
		array(),
		$ver_hero_carousel,
		true
	);

	wp_register_style(
		'green-native-builder-style',
		green_nb_asset_url( 'assets/css/blocks.css' ),
		array(),
		$ver_css
	);

	wp_register_style(
		'green-native-builder-editor-style',
		green_nb_asset_url( 'assets/css/blocks-editor.css' ),
		array( 'wp-edit-blocks' ),
		$ver_css
	);
}
add_action( 'init', 'green_nb_register_assets' );

/**
 * Garante o CSS do builder no front (em alguns alojamentos o estilo de bloco não é enfileirado).
 */
function green_nb_enqueue_blocks_style_front() {
	if ( is_admin() || ! is_singular() ) {
		return;
	}
	$post = get_post();
	if ( ! $post || ! is_string( $post->post_content ) ) {
		return;
	}
	if ( strpos( $post->post_content, 'wp:green/' ) === false ) {
		return;
	}
	if ( ! wp_style_is( 'green-native-builder-style', 'registered' ) ) {
		return;
	}
	if ( ! wp_style_is( 'green-native-builder-style', 'enqueued' ) ) {
		wp_enqueue_style( 'green-native-builder-style' );
	}
}
add_action( 'wp_enqueue_scripts', 'green_nb_enqueue_blocks_style_front', 15 );

/**
 * Dados para o bloco Cabeçalho (lista de menus).
 */
function green_nb_localize_site_header_script() {
	if ( ! wp_script_is( 'green-native-builder-blocks', 'registered' ) ) {
		return;
	}
	wp_localize_script(
		'green-native-builder-blocks',
		'greenNbSiteHeader',
		array(
			'menus' => array_values(
				array_map(
					function ( $m ) {
						return array(
							'id'   => (int) $m->term_id,
							'name' => $m->name,
						);
					},
					wp_get_nav_menus()
				)
			),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'green_nb_localize_site_header_script', 20 );

function green_nb_register_blocks() {
	$common = array(
		'editor_script' => 'green-native-builder-blocks',
		'style'         => 'green-native-builder-style',
		'editor_style'  => 'green-native-builder-editor-style',
		'supports'        => array(
			'anchor' => true,
		),
	);

	register_block_type(
		'green/hero-banner',
		array_merge(
			$common,
			array(
				'attributes'      => array(
					'backgroundUrl'        => array( 'type' => 'string', 'default' => '' ),
					'backgroundMode'       => array( 'type' => 'string', 'default' => 'single' ),
					'backgroundImageIds'   => array( 'type' => 'array', 'default' => array() ),
					'carouselIntervalMs'   => array( 'type' => 'number', 'default' => 7000 ),
					'carouselFadeMs'       => array( 'type' => 'number', 'default' => 1200 ),
					'badge'                => array( 'type' => 'string', 'default' => 'TRADUÇÕES CORPORATIVAS DE ALTO NÍVEL' ),
					'title'                => array( 'type' => 'string', 'default' => '' ),
					'subtitle'             => array( 'type' => 'string', 'default' => '' ),
					'primaryButtonText'    => array( 'type' => 'string', 'default' => '' ),
					'primaryButtonUrl'     => array( 'type' => 'string', 'default' => '' ),
					'secondaryButtonText'  => array( 'type' => 'string', 'default' => '' ),
					'secondaryButtonUrl'   => array( 'type' => 'string', 'default' => '' ),
				),
				'render_callback' => 'green_nb_render_hero_banner',
			)
		)
	);

	register_block_type(
		'green/highlight-cards',
		array_merge(
			$common,
			array(
				'attributes'      => array(
					'items' => array( 'type' => 'array', 'default' => array() ),
				),
				'render_callback' => 'green_nb_render_highlight_cards',
			)
		)
	);

	register_block_type(
		'green/areas-atuacao',
		array_merge(
			$common,
			array(
				'attributes'      => array(
					'sectionTitle'       => array( 'type' => 'string', 'default' => '' ),
					'sectionDescription' => array( 'type' => 'string', 'default' => 'A Green Associados atua em diversos segmentos do mercado, com especialização nas seguintes áreas:' ),
					'services'           => array( 'type' => 'array', 'default' => array() ),
				),
				'render_callback' => 'green_nb_render_areas_atuacao',
			)
		)
	);

	register_block_type(
		'green/ia-section',
		array_merge(
			$common,
			array(
				'attributes'      => array(
					'label'      => array( 'type' => 'string', 'default' => '' ),
					'title'      => array( 'type' => 'string', 'default' => '' ),
					'paragraphs' => array( 'type' => 'array', 'default' => array() ),
					'features'   => array( 'type' => 'array', 'default' => array() ),
				),
				'render_callback' => 'green_nb_render_ia_section',
			)
		)
	);

	register_block_type(
		'green/security-pillars',
		array_merge(
			$common,
			array(
				'attributes'      => array(
					'label'      => array( 'type' => 'string', 'default' => '' ),
					'title'      => array( 'type' => 'string', 'default' => '' ),
					'intro'      => array( 'type' => 'string', 'default' => '' ),
					'paragraphs' => array( 'type' => 'array', 'default' => array() ),
					'pillars'    => array( 'type' => 'array', 'default' => array() ),
				),
				'render_callback' => 'green_nb_render_security_pillars',
			)
		)
	);

	register_block_type(
		'green/team-grid',
		array_merge(
			$common,
			array(
				'attributes'      => array(
					'title'       => array( 'type' => 'string', 'default' => '' ),
					'description' => array( 'type' => 'string', 'default' => '' ),
					'members'     => array( 'type' => 'array', 'default' => array() ),
				),
				'render_callback' => 'green_nb_render_team_grid',
			)
		)
	);

	$contact_attrs = array(
		'title'        => array( 'type' => 'string', 'default' => 'FALE CONOSCO' ),
		'description'  => array( 'type' => 'string', 'default' => '' ),
		'phoneLabel'   => array( 'type' => 'string', 'default' => '' ),
		'phone'        => array( 'type' => 'string', 'default' => '' ),
		'emailLabel'   => array( 'type' => 'string', 'default' => '' ),
		'email'        => array( 'type' => 'string', 'default' => '' ),
		'addressLabel' => array( 'type' => 'string', 'default' => '' ),
		'address'      => array( 'type' => 'string', 'default' => '' ),
		'whatsappText' => array( 'type' => 'string', 'default' => '' ),
		'whatsappUrl'  => array( 'type' => 'string', 'default' => '' ),
		'showWhatsapp' => array( 'type' => 'boolean', 'default' => true ),
	);

	register_block_type(
		'green/contact-section',
		array_merge(
			$common,
			array(
				'attributes'      => $contact_attrs,
				'render_callback' => 'green_nb_render_contact_section',
			)
		)
	);

	register_block_type(
		'green/contact-footer',
		array_merge(
			$common,
			array(
				'attributes'      => array_merge(
					$contact_attrs,
					array(
						'footerAbout' => array( 'type' => 'string', 'default' => '' ),
						'legalItems'  => array( 'type' => 'array', 'default' => array() ),
						'copyright'   => array( 'type' => 'string', 'default' => '' ),
						'credit'      => array( 'type' => 'string', 'default' => '' ),
					)
				),
				'render_callback' => 'green_nb_render_contact_section',
			)
		)
	);

	register_block_type(
		'green/site-header',
		array_merge(
			$common,
			array(
				'supports'        => array(
					'anchor' => false,
				),
				'attributes'      => array(
					'logoId'           => array( 'type' => 'number', 'default' => 0 ),
					'menuId'           => array( 'type' => 'number', 'default' => 0 ),
					'showLangSwitcher' => array( 'type' => 'boolean', 'default' => true ),
					'logoMaxWidth'     => array( 'type' => 'number', 'default' => 180 ),
				),
				'render_callback' => 'green_nb_render_site_header',
			)
		)
	);
}
add_action( 'init', 'green_nb_register_blocks' );

/**
 * Polylang: menu guardado no bloco pode ser o termo do idioma do editor; troca pela tradução ou usa a localização «primary».
 *
 * @param int $menu_id ID do termo nav_menu (0 = usar só theme_location).
 * @return int ID do menu a usar, ou 0 para forçar theme_location.
 */
function green_nb_resolve_nav_menu_for_display( $menu_id ) {
	$menu_id = (int) $menu_id;
	if ( $menu_id <= 0 ) {
		return 0;
	}
	if ( ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_get_term' ) ) {
		return $menu_id;
	}
	$lang = pll_current_language();
	if ( ! is_string( $lang ) || '' === $lang ) {
		return $menu_id;
	}
	$resolved = pll_get_term( $menu_id, $lang );
	if ( $resolved ) {
		$tid = (int) $resolved;
		$term = get_term( $tid, 'nav_menu' );
		if ( $term && ! is_wp_error( $term ) ) {
			return $tid;
		}
		return 0;
	}
	if ( function_exists( 'pll_get_term_language' ) ) {
		$term_lang = pll_get_term_language( $menu_id, 'slug' );
		if ( $term_lang && $term_lang === $lang ) {
			return $menu_id;
		}
	}
	return 0;
}

/**
 * Cabeçalho configurável (logo, menu, idiomas) para o conteúdo «Cabeçalho do site».
 *
 * @param array<string,mixed> $attributes Atributos do bloco.
 * @return string HTML.
 */
function green_nb_render_site_header( $attributes ) {
	$logo_id = isset( $attributes['logoId'] ) ? (int) $attributes['logoId'] : 0;
	if ( $logo_id <= 0 ) {
		$logo_id = (int) get_theme_mod( 'custom_logo', 0 );
	}

	$menu_id = isset( $attributes['menuId'] ) ? (int) $attributes['menuId'] : 0;
	$resolved_menu = green_nb_resolve_nav_menu_for_display( $menu_id );
	$show_lang = ! isset( $attributes['showLangSwitcher'] ) || (bool) $attributes['showLangSwitcher'];
	$logo_max  = isset( $attributes['logoMaxWidth'] ) ? (int) $attributes['logoMaxWidth'] : 180;
	$logo_max  = max( 80, min( 320, $logo_max ) );

	$nav_args = array(
		'container'   => false,
		'menu_class'  => 'menu',
		'fallback_cb' => false,
		'echo'        => false,
	);
	if ( $resolved_menu > 0 ) {
		$nav_args['menu'] = $resolved_menu;
	} else {
		$nav_args['theme_location'] = 'primary';
	}

	$nav_html = wp_nav_menu( $nav_args );
	if ( ! $nav_html && $resolved_menu > 0 ) {
		$nav_html = wp_nav_menu(
			array(
				'container'      => false,
				'menu_class'     => 'menu',
				'fallback_cb'    => false,
				'echo'           => false,
				'theme_location' => 'primary',
			)
		);
	}

	$style = sprintf( '--green-header-logo-max-w:%dpx;', $logo_max );

	ob_start();
	?>
	<div class="green-header-inner green-header-inner--block" style="<?php echo esc_attr( $style ); ?>">
		<div class="green-header-logo">
			<a href="<?php echo esc_url( function_exists( 'green_core_theme_logo_home_url' ) ? green_core_theme_logo_home_url() : home_url( '/' ) ); ?>" class="green-header-logo-link" aria-label="<?php esc_attr_e( 'Página inicial', 'green-core-theme' ); ?>">
				<?php
				if ( $logo_id > 0 ) {
					echo wp_get_attachment_image(
						$logo_id,
						'full',
						false,
						array(
							'class'   => 'green-header-logo-img',
							'loading' => 'eager',
							'alt'     => get_bloginfo( 'name' ),
						)
					);
				} else {
					echo '<span class="green-header-site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
				}
				?>
			</a>
		</div>
		<nav id="green-primary-nav" class="green-header-nav" aria-label="<?php esc_attr_e( 'Navegação principal', 'green-core-theme' ); ?>">
			<?php
			if ( $nav_html ) {
				echo $nav_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</nav>
		<div class="green-header-end">
			<?php if ( $show_lang && function_exists( 'pll_the_languages' ) ) : ?>
				<div class="green-header-lang" aria-label="<?php esc_attr_e( 'Seletor de idioma', 'green-core-theme' ); ?>">
					<?php
					if ( function_exists( 'green_core_theme_render_language_switcher' ) ) {
						green_core_theme_render_language_switcher();
					} else {
						pll_the_languages(
							array(
								'dropdown'         => 0,
								'hide_current'     => 0,
								'display_names_as' => 'name',
								'show_flags'       => 1,
							)
						);
					}
					?>
				</div>
			<?php endif; ?>
			<button type="button" class="green-header-menu-toggle" aria-expanded="false" aria-controls="green-primary-nav" id="green-menu-toggle">
				<span class="green-header-menu-toggle-bars" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Abrir menu', 'green-core-theme' ); ?></span>
			</button>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function green_nb_render_hero_banner( $attributes ) {
	$section_id     = green_nb_get_section_id( $attributes, '' );
	$bg_data        = green_nb_resolve_hero_backgrounds( $attributes );
	$is_carousel    = ! empty( $bg_data['carousel'] ) && count( $bg_data['urls'] ) >= 2;
	$interval_raw   = isset( $attributes['carouselIntervalMs'] ) ? (int) $attributes['carouselIntervalMs'] : 7000;
	$fade_raw       = isset( $attributes['carouselFadeMs'] ) ? (int) $attributes['carouselFadeMs'] : 1200;
	$interval_ms    = min( 60000, max( 3500, $interval_raw ) );
	$fade_ms        = min( 4000, max( 400, $fade_raw ) );

	$background = '';
	if ( ! empty( $bg_data['urls'][0] ) ) {
		$background = esc_url( $bg_data['urls'][0] );
	}

	$badge    = green_nb_sanitize_text( $attributes['badge'] ?? '' );
	$title    = green_nb_sanitize_text( $attributes['title'] ?? '' );
	$subtitle = green_nb_sanitize_text( $attributes['subtitle'] ?? '' );

	$primary_button = green_nb_render_link_button(
		$attributes['primaryButtonText'] ?? '',
		$attributes['primaryButtonUrl'] ?? '',
		'green-btn !bg-white !text-primary shadow-lg shadow-primary/20 hover:!bg-white/95'
	);
	$secondary_button = green_nb_render_link_button(
		$attributes['secondaryButtonText'] ?? '',
		$attributes['secondaryButtonUrl'] ?? '',
		'green-btn !border-2 !border-white/50 !bg-white/5 !text-white !backdrop-blur-sm hover:!border-white/80 hover:!bg-white/10'
	);

	if ( $is_carousel ) {
		wp_enqueue_script( 'green-nb-hero-carousel' );
	}

	$slides_json = '';
	if ( $is_carousel ) {
		$slides_json = wp_json_encode( $bg_data['urls'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	}

	ob_start();
	?>
	<section<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> class="green-section green-hero-block relative overflow-hidden">
		<?php if ( $is_carousel && '' !== $slides_json ) : ?>
		<div
			class="green-hero-bg green-hero-bg--carousel relative"
			data-slides="<?php echo esc_attr( $slides_json ); ?>"
			data-interval="<?php echo esc_attr( (string) $interval_ms ); ?>"
			data-fade="<?php echo esc_attr( (string) $fade_ms ); ?>"
			style="--green-hero-fade:<?php echo esc_attr( (string) $fade_ms ); ?>ms"
		>
			<div class="green-hero-slides" aria-hidden="true">
				<div class="green-hero-slide green-hero-slide--a" style="background-image:url('<?php echo esc_url( $bg_data['urls'][0] ); ?>')"></div>
				<div class="green-hero-slide green-hero-slide--b" style="background-image:url('<?php echo esc_url( $bg_data['urls'][1] ); ?>')"></div>
			</div>
			<div class="green-hero-overlay" aria-hidden="true"></div>
			<div class="green-hero-dim" aria-hidden="true"></div>
			<div class="mx-auto w-full max-w-[1200px] px-6 md:px-8 relative z-10 green-hero-content">
				<div class="green-hero-inner">
					<?php if ( '' !== $badge ) : ?>
						<span class="green-hero-badge !border !border-white/20 !bg-secondary/20 !text-white"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $title ) : ?>
						<h1 class="green-hero-title"><?php echo esc_html( $title ); ?></h1>
					<?php endif; ?>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="green-hero-subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $primary_button || '' !== $secondary_button ) : ?>
						<div class="green-hero-actions">
							<?php echo wp_kses_post( $primary_button ); ?>
							<?php echo wp_kses_post( $secondary_button ); ?>
						</div>
					<?php endif; ?>
					</div>
				</div>
			<?php green_nb_print_divider_hero_bottom(); ?>
		</div>
		<?php else : ?>
		<div class="green-hero-bg relative"<?php echo $background ? ' style="background-image:url(' . esc_url( $background ) . ');"' : ''; ?>>
			<div class="green-hero-overlay" aria-hidden="true"></div>
			<div class="green-hero-dim" aria-hidden="true"></div>
			<div class="mx-auto w-full max-w-[1200px] px-6 md:px-8 relative z-10 green-hero-content">
				<div class="green-hero-inner">
					<?php if ( '' !== $badge ) : ?>
						<span class="green-hero-badge !border !border-white/20 !bg-secondary/20 !text-white"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $title ) : ?>
						<h1 class="green-hero-title"><?php echo esc_html( $title ); ?></h1>
					<?php endif; ?>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="green-hero-subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $primary_button || '' !== $secondary_button ) : ?>
						<div class="green-hero-actions">
							<?php echo wp_kses_post( $primary_button ); ?>
							<?php echo wp_kses_post( $secondary_button ); ?>
						</div>
					<?php endif; ?>
					</div>
				</div>
			<?php green_nb_print_divider_hero_bottom(); ?>
		</div>
		<?php endif; ?>
	</section>
	<?php
	return ob_get_clean();
}

function green_nb_render_highlight_cards( $attributes ) {
	$section_id = green_nb_get_section_id( $attributes, '' );
	$items        = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
	if ( empty( $items ) ) {
		return '';
	}

	ob_start();
	?>
	<section<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> class="green-section green-highlight-wrap relative z-10 -mt-24 overflow-visible !bg-transparent sm:!-mt-28 max-md:!-mt-12">
		<div class="green-container relative z-[1] pb-8">
			<?php green_nb_render_glow_marks_lite(); ?>
			<div class="green-highlight-grid relative z-10 reveal">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$icon        = green_nb_sanitize_text( $item['icon'] ?? '' );
					$title       = green_nb_sanitize_text( $item['title'] ?? '' );
					$description = green_nb_sanitize_text( $item['description'] ?? '' );
					$link_text   = green_nb_sanitize_text( $item['linkText'] ?? '' );
					$link_url  = green_nb_sanitize_url( $item['linkUrl'] ?? '' );
					$tone      = green_nb_sanitize_text( $item['tone'] ?? 'light' );
					$is_equipe = (
						( '' !== $title && ( 0 === strcasecmp( $title, 'NOSSA EQUIPE' ) || 0 === strcasecmp( $title, 'OUR TEAM' ) ) )
						|| ( 'groups' === $icon && ( '' === $link_url || false !== stripos( $link_url, 'equipe' ) || false !== stripos( $link_url, 'team' ) ) )
					);
					$article_mod = $is_equipe ? ' green-highlight-equipe' : '';
					?>
					<article class="<?php echo esc_attr( green_nb_tw( 'highlight_card' ) . $article_mod ); ?> green-highlight-<?php echo esc_attr( $tone ); ?>">
						<?php if ( '' !== $icon ) : ?>
							<span class="material-symbols-outlined green-card-icon"><?php echo esc_html( $icon ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $title ) : ?>
							<h3><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $description ) : ?>
							<p><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
						<?php
						$bullets = isset( $item['bullets'] ) && is_array( $item['bullets'] ) ? $item['bullets'] : array();
						$bullets = array_filter(
							array_map(
								function ( $b ) {
									return is_string( $b ) ? trim( $b ) : '';
								},
								$bullets
							)
						);
						?>
						<?php if ( ! empty( $bullets ) ) : ?>
							<ul class="green-highlight-bullets">
								<?php foreach ( $bullets as $line ) : ?>
									<li>
										<span class="material-symbols-outlined green-highlight-bullet-icon">check_circle</span>
										<span><?php echo esc_html( $line ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php elseif ( '' !== $link_text && '' !== $link_url ) : ?>
							<a href="<?php echo esc_url( $link_url ); ?>" class="green-card-link">
								<?php echo esc_html( $link_text ); ?>
								<span class="material-symbols-outlined">arrow_forward</span>
							</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Conteúdo detalhado de um cartão de serviço (listas, subsecções RI, tags jurídicas).
 *
 * @param array<string,mixed> $service Atributos do serviço.
 * @return string HTML.
 */
function green_nb_render_service_card( $service ) {
	if ( ! is_array( $service ) ) {
		return '';
	}
	$icon        = green_nb_sanitize_text( $service['icon'] ?? '' );
	$title       = green_nb_sanitize_text( $service['title'] ?? '' );
	$description = green_nb_sanitize_text( $service['description'] ?? '' );

	$list_left  = isset( $service['listLeft'] ) && is_array( $service['listLeft'] ) ? $service['listLeft'] : array();
	$list_right = isset( $service['listRight'] ) && is_array( $service['listRight'] ) ? $service['listRight'] : array();
	$list_left  = array_values( array_filter( array_map( 'green_nb_sanitize_text', $list_left ) ) );
	$list_right = array_values( array_filter( array_map( 'green_nb_sanitize_text', $list_right ) ) );

	$subsections = isset( $service['subsections'] ) && is_array( $service['subsections'] ) ? $service['subsections'] : array();

	$tags = isset( $service['tags'] ) && is_array( $service['tags'] ) ? $service['tags'] : array();
	$tags = array_values( array_filter( array_map( 'green_nb_sanitize_text', $tags ) ) );

	ob_start();
	?>
	<article class="<?php echo esc_attr( green_nb_tw( 'service_card' ) ); ?>">
		<?php if ( '' !== $icon || '' !== $title ) : ?>
			<div class="flex flex-col items-start text-left">
				<?php if ( '' !== $icon ) : ?>
					<div class="mb-5 flex h-16 w-16 shrink-0 items-center justify-center self-start rounded-full bg-secondary text-white shadow-md shadow-slate-900/10 ring-2 ring-slate-200/80">
						<span class="material-symbols-outlined text-[1.75rem] leading-none"><?php echo esc_html( $icon ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $title ) : ?>
					<h3 class="!mb-3 !w-full !text-left !text-lg !font-bold !text-primary md:!text-xl"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $description ) : ?>
			<p class="green-service-lead !text-left !text-sm !text-slate-600 md:!text-base"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $subsections ) ) : ?>
			<div class="green-service-subsections<?php echo count( $subsections ) === 2 ? ' green-service-subsections--pair' : ''; ?>">
				<?php foreach ( $subsections as $sub ) : ?>
					<?php
					if ( ! is_array( $sub ) ) {
						continue;
					}
					$sub_title = green_nb_sanitize_text( $sub['title'] ?? '' );
					$sub_items = isset( $sub['items'] ) && is_array( $sub['items'] ) ? $sub['items'] : array();
					$sub_items = array_values( array_filter( array_map( 'green_nb_sanitize_text', $sub_items ) ) );
					if ( '' === $sub_title && empty( $sub_items ) ) {
						continue;
					}
					?>
					<div class="green-service-subsection">
						<?php if ( '' !== $sub_title ) : ?>
							<h4 class="green-service-subsection-title"><?php echo esc_html( $sub_title ); ?></h4>
						<?php endif; ?>
						<?php if ( ! empty( $sub_items ) ) : ?>
							<ul class="green-service-list">
								<?php foreach ( $sub_items as $li ) : ?>
									<li><span class="green-service-dot"></span><?php echo esc_html( $li ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( ! empty( $list_left ) || ! empty( $list_right ) ) : ?>
			<div class="green-service-columns">
				<?php if ( ! empty( $list_left ) ) : ?>
					<ul class="green-service-list">
						<?php foreach ( $list_left as $li ) : ?>
							<li><span class="green-service-dot"></span><?php echo esc_html( $li ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( ! empty( $list_right ) ) : ?>
					<ul class="green-service-list">
						<?php foreach ( $list_right as $li ) : ?>
							<li><span class="green-service-dot"></span><?php echo esc_html( $li ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $tags ) ) : ?>
			<ul class="green-service-list green-service-list--as-tags">
				<?php foreach ( $tags as $tag ) : ?>
					<li><span class="green-service-dot" aria-hidden="true"></span><?php echo esc_html( $tag ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</article>
	<?php
	return ob_get_clean();
}

function green_nb_render_areas_atuacao( $attributes ) {
	$section_id  = green_nb_get_section_id( $attributes, 'atuacao' );
	$title       = green_nb_sanitize_text( $attributes['sectionTitle'] ?? '' );
	$description = green_nb_sanitize_text( $attributes['sectionDescription'] ?? '' );
	$services    = isset( $attributes['services'] ) && is_array( $attributes['services'] ) ? $attributes['services'] : array();

	if ( '' === $title && '' === $description && empty( $services ) ) {
		return '';
	}

	ob_start();
	?>
	<section<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> class="green-section green-areas-block relative !bg-white !pb-0 !pt-32">
		<?php green_nb_render_glow_marks_lite( 'areas' ); ?>
		<div class="green-container relative z-10">
			<div class="green-areas-intro reveal !pt-2">
				<?php if ( '' !== $title ) : ?>
					<h2 class="<?php echo esc_attr( green_nb_section_title_tw( 'on-light' ) . ' !text-left' ); ?>"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<div class="green-section-divider !mx-0 !bg-secondary" aria-hidden="true"></div>
				<?php if ( '' !== $description ) : ?>
					<p class="green-section-description !text-left !text-slate-600"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $services ) ) : ?>
				<div class="green-service-grid reveal">
					<?php foreach ( $services as $service ) : ?>
						<?php echo green_nb_render_service_card( $service ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function green_nb_render_ia_section( $attributes ) {
	$section_id = green_nb_get_section_id( $attributes, 'ia' );
	$label      = green_nb_sanitize_text( $attributes['label'] ?? '' );
	$title      = green_nb_sanitize_text( $attributes['title'] ?? '' );
	$paragraphs = isset( $attributes['paragraphs'] ) && is_array( $attributes['paragraphs'] ) ? $attributes['paragraphs'] : array();
	$features   = isset( $attributes['features'] ) && is_array( $attributes['features'] ) ? $attributes['features'] : array();

	if ( '' === $label && '' === $title && empty( $paragraphs ) && empty( $features ) ) {
		return '';
	}

	ob_start();
	?>
	<section<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> class="green-section green-ia-block relative !overflow-hidden !bg-primary !pt-0 !pb-16 md:!pb-24">
		<?php green_nb_print_divider_section_bottom( '#006060', 'bg-white' ); ?>
		<?php green_nb_render_glow_marks_dark(); ?>
		<div class="green-container green-ia-grid relative z-10 !items-center !pt-8 md:!pt-10">
			<div class="reveal !text-left">
				<?php if ( '' !== $label ) : ?>
					<p class="green-ia-label !text-secondary"><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $title ) : ?>
					<h2 class="green-ia-title !text-3xl !font-extrabold !text-white sm:!text-4xl md:!text-5xl"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php foreach ( $paragraphs as $paragraph ) : ?>
					<?php if ( is_string( $paragraph ) && '' !== trim( $paragraph ) ) : ?>
						<p class="green-ia-paragraph !ml-0 !mr-0 !max-w-3xl"><?php echo esc_html( trim( $paragraph ) ); ?></p>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="green-ia-feature-list reveal">
				<?php foreach ( $features as $feature ) : ?>
					<?php
					$icon = green_nb_sanitize_text( $feature['icon'] ?? '' );
					$fh   = green_nb_sanitize_text( $feature['title'] ?? '' );
					$fd   = green_nb_sanitize_text( $feature['description'] ?? '' );
					if ( '' === $icon && '' === $fh && '' === $fd ) {
						continue;
					}
					?>
					<article class="<?php echo esc_attr( green_nb_tw( 'ia_feature' ) ); ?>">
						<?php if ( '' !== $icon ) : ?>
							<div class="flex h-14 w-14 shrink-0 items-center justify-center self-start rounded-2xl bg-secondary/20 text-secondary">
								<span class="material-symbols-outlined !text-3xl"><?php echo esc_html( $icon ); ?></span>
							</div>
						<?php endif; ?>
						<div>
							<?php if ( '' !== $fh ) : ?>
								<h4><?php echo esc_html( $fh ); ?></h4>
							<?php endif; ?>
							<?php if ( '' !== $fd ) : ?>
								<p><?php echo esc_html( $fd ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php green_nb_print_divider_section_bottom( '#ffffff' ); ?>
	</section>
	<?php
	return ob_get_clean();
}

function green_nb_render_security_pillars( $attributes ) {
	$section_id = green_nb_get_section_id( $attributes, 'seguranca' );
	$label      = green_nb_sanitize_text( $attributes['label'] ?? '' );
	$title      = green_nb_sanitize_text( $attributes['title'] ?? '' );
	$intro      = green_nb_sanitize_text( $attributes['intro'] ?? '' );
	$paragraphs = isset( $attributes['paragraphs'] ) && is_array( $attributes['paragraphs'] ) ? $attributes['paragraphs'] : array();
	$pillars    = isset( $attributes['pillars'] ) && is_array( $attributes['pillars'] ) ? $attributes['pillars'] : array();

	if ( '' === $label && '' === $title && '' === $intro && empty( $paragraphs ) && empty( $pillars ) ) {
		return '';
	}

	ob_start();
	?>
	<section<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> class="green-section green-security-block relative !overflow-hidden !bg-background !pb-0 !pt-10 md:!pt-12">
		<?php green_nb_render_glow_marks_lite(); ?>
		<div class="green-container green-security-grid relative z-10 !pt-2">
			<div class="reveal !text-left">
				<?php if ( '' !== $label ) : ?>
					<p class="green-security-label !text-left !text-secondary"><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $title ) : ?>
					<h2 class="<?php echo esc_attr( green_nb_section_title_tw( 'on-light' ) . ' !text-left' ); ?>"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $intro ) : ?>
					<p class="green-security-intro !ml-0 !mr-0 !max-w-3xl !text-left"><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
				<?php foreach ( $paragraphs as $paragraph ) : ?>
					<?php if ( is_string( $paragraph ) && '' !== trim( $paragraph ) ) : ?>
						<p class="green-security-paragraph !ml-0 !mr-0 !max-w-3xl !text-left"><?php echo esc_html( trim( $paragraph ) ); ?></p>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="green-security-pillar-grid reveal !mx-auto !max-w-5xl !justify-items-center !gap-8 md:!gap-10">
				<?php foreach ( $pillars as $pillar ) : ?>
					<?php
					$icon = green_nb_sanitize_text( $pillar['icon'] ?? '' );
					$pt   = green_nb_sanitize_text( $pillar['title'] ?? '' );
					$pd   = green_nb_sanitize_text( $pillar['description'] ?? '' );
					if ( '' === $icon && '' === $pt && '' === $pd ) {
						continue;
					}
					?>
					<article class="<?php echo esc_attr( green_nb_tw( 'security_pillar' ) ); ?>">
						<?php if ( '' !== $icon ) : ?>
							<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-secondary text-white shadow-md shadow-primary/10">
								<span class="material-symbols-outlined !text-2xl"><?php echo esc_html( $icon ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( '' !== $pt ) : ?>
							<h4 class="!text-center !text-base !font-bold !text-primary md:!text-lg"><?php echo esc_html( $pt ); ?></h4>
						<?php endif; ?>
						<?php if ( '' !== $pd ) : ?>
							<p class="!text-center !text-sm !text-slate-600 md:!text-[0.95rem]"><?php echo esc_html( $pd ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function green_nb_render_team_grid( $attributes ) {
	$section_id  = green_nb_get_section_id( $attributes, 'equipe' );
	$title       = green_nb_sanitize_text( $attributes['title'] ?? '' );
	$description = green_nb_sanitize_text( $attributes['description'] ?? '' );
	$members     = isset( $attributes['members'] ) && is_array( $attributes['members'] ) ? $attributes['members'] : array();

	if ( '' === $title && '' === $description && empty( $members ) ) {
		return '';
	}

	ob_start();
	?>
	<section<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> class="green-section green-team-block relative !overflow-hidden !bg-surface !pt-0 !pb-16 md:!pb-20">
		<?php green_nb_print_divider_section_bottom( 'surface', 'bg-white' ); ?>
		<?php green_nb_render_glow_marks_lite(); ?>
		<div class="green-container relative z-10 !pt-8 md:!pt-10">
			<?php if ( '' !== $title ) : ?>
				<h2 class="reveal <?php echo esc_attr( green_nb_section_title_tw( 'on-light' ) . ' !text-left' ); ?>"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $description ) : ?>
				<p class="green-section-description !text-left reveal"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
			<div class="green-team-grid reveal">
				<?php foreach ( $members as $member ) : ?>
					<?php
					$name     = green_nb_sanitize_text( $member['name'] ?? '' );
					$role     = green_nb_sanitize_text( $member['role'] ?? '' );
					$photo_id = isset( $member['photoId'] ) ? (int) $member['photoId'] : 0;
					$photo    = '';
					if ( $photo_id > 0 ) {
						$att = wp_get_attachment_image_url( $photo_id, 'medium' );
						if ( $att ) {
							$photo = $att;
						}
					}
					if ( '' === $photo ) {
						$photo = green_nb_sanitize_url( $member['photoUrl'] ?? '' );
					}
					$link = green_nb_sanitize_url( $member['linkedinUrl'] ?? '' );
					if ( '' === $name && '' === $role && '' === $photo ) {
						continue;
					}
					?>
					<article class="<?php echo esc_attr( green_nb_tw( 'team_card' ) ); ?>">
						<?php if ( '' !== $photo ) : ?>
							<div class="green-team-photo-wrap">
								<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="<?php echo esc_attr( green_nb_tw( 'team_photo' ) ); ?>" width="192" height="192" loading="lazy" decoding="async">
							</div>
						<?php endif; ?>
						<div class="green-team-text">
							<?php if ( '' !== $name ) : ?>
								<h4><?php echo esc_html( $name ); ?></h4>
							<?php endif; ?>
							<?php if ( '' !== $role ) : ?>
								<p><?php echo esc_html( $role ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== $link ) : ?>
								<a href="<?php echo esc_url( $link ); ?>" class="green-team-link" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Perfil no LinkedIn', 'green-native-builder' ); ?>">
									<span class="material-symbols-outlined" aria-hidden="true">work</span>
								</a>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function green_nb_render_contact_section( $attributes ) {
	$section_id  = green_nb_get_section_id( $attributes, 'contato' );
	$title       = green_nb_sanitize_text( $attributes['title'] ?? '' );
	$description = green_nb_sanitize_text( $attributes['description'] ?? '' );
	$phone_label = green_nb_sanitize_text( $attributes['phoneLabel'] ?? '' );
	$phone       = green_nb_sanitize_text( $attributes['phone'] ?? '' );
	$email_label = green_nb_sanitize_text( $attributes['emailLabel'] ?? '' );
	$email       = green_nb_sanitize_text( $attributes['email'] ?? '' );
	$addr_label  = green_nb_sanitize_text( $attributes['addressLabel'] ?? '' );
	$address     = green_nb_sanitize_text( $attributes['address'] ?? '' );
	$wa_text     = green_nb_sanitize_text( $attributes['whatsappText'] ?? '' );
	$wa_url      = green_nb_sanitize_url( $attributes['whatsappUrl'] ?? '' );
	$show_wa     = ! isset( $attributes['showWhatsapp'] ) ? true : (bool) $attributes['showWhatsapp'];

	$phone_href = green_nb_phone_to_tel_href( $phone );
	$email_href = green_nb_email_to_mailto_href( $email );

	ob_start();
	?>
	<section<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> class="green-section green-contact-block relative !overflow-hidden !bg-primary !pt-0 !pb-16 md:!pb-20">
		<?php green_nb_print_divider_section_bottom( '#006060', 'bg-surface' ); ?>
		<?php green_nb_render_glow_marks_lite( 'contact' ); ?>
		<div class="green-container relative z-10 !pt-8 md:!pt-10">
			<div class="<?php echo esc_attr( green_nb_tw( 'contact_card' ) ); ?> reveal !mx-auto !max-w-5xl !overflow-hidden">
				<div>
					<?php if ( '' !== $title ) : ?>
						<h2 class="green-contact-title !text-left !text-3xl !font-extrabold !text-primary sm:!text-4xl md:!text-5xl"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $description ) : ?>
						<p class="green-contact-description !text-left !text-slate-600"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
					<div class="green-contact-list !text-left">
						<?php if ( '' !== $phone_label || '' !== $phone ) : ?>
							<div>
								<?php if ( '' !== $phone_label ) : ?><span><?php echo esc_html( $phone_label ); ?></span><?php endif; ?>
								<?php if ( '' !== $phone ) : ?>
									<p>
										<?php if ( '' !== $phone_href ) : ?>
											<a class="green-contact-link green-contact-link--phone" href="<?php echo esc_url( $phone_href ); ?>"><?php echo esc_html( $phone ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $phone ); ?>
										<?php endif; ?>
									</p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( '' !== $email_label || '' !== $email ) : ?>
							<div>
								<?php if ( '' !== $email_label ) : ?><span><?php echo esc_html( $email_label ); ?></span><?php endif; ?>
								<?php if ( '' !== $email ) : ?>
									<p>
										<?php if ( '' !== $email_href ) : ?>
											<a class="green-contact-link green-contact-link--email" href="<?php echo esc_url( $email_href ); ?>"><?php echo esc_html( $email ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $email ); ?>
										<?php endif; ?>
									</p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( '' !== $addr_label || '' !== $address ) : ?>
							<div>
								<?php if ( '' !== $addr_label ) : ?><span><?php echo esc_html( $addr_label ); ?></span><?php endif; ?>
								<?php if ( '' !== $address ) : ?>
									<p class="green-contact-address"><?php echo wp_kses_post( nl2br( esc_html( $address ) ) ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php
				$wa_label = green_nb_sanitize_text( $wa_text );
				$wa_link  = green_nb_sanitize_url( $wa_url );
				if ( $show_wa && '' !== $wa_label && '' !== $wa_link ) {
					?>
				<div class="green-contact-cta !flex !w-full min-h-[72px] !items-center !justify-center !self-center md:!justify-center !pt-2">
					<?php
					echo '<a class="green-btn green-btn-wa !inline-flex !items-center !justify-center !gap-3 !rounded-full !border-0 !bg-secondary !px-8 !py-3 !text-[15px] !font-bold !text-white !no-underline !shadow-md !shadow-slate-900/10 transition-transform duration-200 ease-out hover:scale-105 active:scale-95" href="' . esc_url( $wa_link ) . '">';
					echo green_nb_whatsapp_icon_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<span class="green-btn-wa-label">' . esc_html( $wa_label ) . '</span>';
					echo '</a>';
					?>
				</div>
					<?php
				}
				?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function green_nb_activate_plugin() {
	$homepage = get_page_by_title( 'Homepage' );
	if ( ! $homepage || ! isset( $homepage->ID ) ) {
		return;
	}

	$content = (string) get_post_field( 'post_content', $homepage->ID );
	if ( false !== strpos( $content, '<!-- wp:green/hero-banner' ) ) {
		return;
	}

	$default_content = implode(
		"\n\n",
		array(
			'<!-- wp:green/hero-banner /-->',
			'<!-- wp:green/highlight-cards /-->',
			'<!-- wp:green/areas-atuacao /-->',
			'<!-- wp:green/ia-section /-->',
			'<!-- wp:green/security-pillars /-->',
			'<!-- wp:green/team-grid /-->',
			'<!-- wp:green/contact-section /-->',
		)
	);

	wp_update_post(
		array(
			'ID'           => $homepage->ID,
			'post_content' => $default_content,
		)
	);
}
register_activation_hook( __FILE__, 'green_nb_activate_plugin' );

function green_nb_register_tools_migration_page() {
	add_management_page(
		__( 'Migrar Homepage Green', 'green-native-builder' ),
		__( 'Migrar Homepage Green', 'green-native-builder' ),
		'manage_options',
		'green-nb-migrate-homepage',
		'green_nb_render_tools_migration_page'
	);
}
add_action( 'admin_menu', 'green_nb_register_tools_migration_page' );

function green_nb_render_tools_migration_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sem permissão.', 'green-native-builder' ) );
	}

	require_once __DIR__ . '/includes/homepage-migration-en.php';

	$feedback = '';

	if ( isset( $_POST['green_nb_run_migration'] ) && check_admin_referer( 'green_nb_migrate_homepage_action' ) ) {
		$home_id = (int) get_option( 'page_on_front' );
		if ( $home_id <= 0 ) {
			$home_id = 13;
		}
		$result = green_nb_apply_homepage_migration( $home_id );
		if ( is_wp_error( $result ) ) {
			$feedback = 'error:' . $result->get_error_message();
		} else {
			$feedback = 'success';
		}
	}

	if ( isset( $_POST['green_nb_run_migration_en'] ) && check_admin_referer( 'green_nb_migrate_homepage_action' ) ) {
		$home_id = (int) get_option( 'page_on_front' );
		if ( $home_id <= 0 ) {
			$home_id = 13;
		}
		$result = green_nb_apply_homepage_migration_en( $home_id );
		if ( is_wp_error( $result ) ) {
			$feedback = 'error:' . $result->get_error_message();
		} else {
			$feedback = 'success-en';
		}
	}

	$home_id = (int) get_option( 'page_on_front' );
	if ( $home_id <= 0 ) {
		$home_id = 13;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Migrar Homepage Green', 'green-native-builder' ); ?></h1>
		<p><?php esc_html_e( 'Substitui o conteúdo da página inicial pelos blocos Green com texto e imagens do layout aprovado (migração assistida).', 'green-native-builder' ); ?></p>
		<p>
			<strong><?php esc_html_e( 'Página alvo (ID):', 'green-native-builder' ); ?></strong>
			<?php echo (int) $home_id; ?>
		</p>
		<?php if ( 'success' === $feedback ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Migração concluída.', 'green-native-builder' ); ?></p></div>
		<?php elseif ( 'success-en' === $feedback ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Migração da homepage em inglês concluída.', 'green-native-builder' ); ?></p></div>
		<?php elseif ( '' !== $feedback && 0 === strpos( $feedback, 'error:' ) ) : ?>
			<div class="notice notice-error"><p><?php echo esc_html( substr( $feedback, 6 ) ); ?></p></div>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'green_nb_migrate_homepage_action' ); ?>
			<p>
				<input
					type="submit"
					name="green_nb_run_migration"
					class="button button-primary"
					value="<?php esc_attr_e( 'Executar migração (PT)', 'green-native-builder' ); ?>"
					onclick="return confirm('<?php echo esc_js( __( 'Isto substitui o conteúdo da página inicial. Continuar?', 'green-native-builder' ) ); ?>');"
				/>
			</p>
		</form>
		<form method="post">
			<?php wp_nonce_field( 'green_nb_migrate_homepage_action' ); ?>
			<p>
				<input
					type="submit"
					name="green_nb_run_migration_en"
					class="button button-secondary"
					value="<?php esc_attr_e( 'Replicar migração para a homepage em inglês', 'green-native-builder' ); ?>"
					onclick="return confirm('<?php echo esc_js( __( 'Isto substitui o conteúdo da página em inglês associada no Polylang. Continuar?', 'green-native-builder' ) ); ?>');"
				/>
			</p>
		</form>
		<p class="description"><?php esc_html_e( 'Alternativa por terminal: wp eval-file wp-content/plugins/green-native-builder/migrate-homepage.php', 'green-native-builder' ); ?></p>
	</div>
	<?php
}

