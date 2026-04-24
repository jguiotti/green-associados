<?php
/**
 * Árvore Elementor da Homepage (widgets nativos + UAE) — Green Associados.
 *
 * @package GreenAssociados
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Liga widgets às cores globais do Kit (Elementor → Site Settings).
 *
 * @param string $kit_color_id ID interno da cor (ex.: primary, accent, light_on_dark).
 */
function green_g_color( $kit_color_id ) {
	return 'globals/colors?id=' . $kit_color_id;
}

/**
 * Liga widgets às tipografias globais do Kit.
 *
 * @param string $kit_typo_id primary | secondary | text | accent
 */
function green_g_typo( $kit_typo_id ) {
	return 'globals/typography?id=' . $kit_typo_id;
}

/**
 * Text Editor com ícone Material Symbols (CSS global carrega a fonte).
 *
 * @param string $name Nome do ícone.
 * @param string $color Cor CSS.
 * @param string $size Tamanho (ex. 36px).
 * @param bool   $block Se true, margin-bottom.
 */
function green_ms_te( $name, $color, $size = '36px', $block = true ) {
	$mb  = $block ? 'margin-bottom:24px' : '';
	$disp = $block ? 'block' : 'inline-block';
	$html = '<span class="material-symbols-outlined" style="color:' . esc_attr( $color ) . ';font-size:' . esc_attr( $size ) . ';display:' . esc_attr( $disp ) . ';' . esc_attr( $mb ) . '">' . esc_html( $name ) . '</span>';
	return array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'text-editor',
		'settings'   => array( 'editor' => $html ),
	);
}

/**
 * Icon List com check (Font Awesome).
 *
 * @param string[] $lines Textos.
 */
function green_icon_list_checks( array $lines ) {
	$icon_list = array();
	foreach ( $lines as $text ) {
		$icon_list[] = array(
			'text'          => $text,
			'selected_icon' => array(
				'value'   => 'fas fa-check-circle',
				'library' => 'fa-solid',
			),
		);
	}
	return array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'icon-list',
		'settings'   => array(
			'icon_list'  => $icon_list,
			'__globals__' => array(
				'icon_color' => green_g_color( 'secondary' ),
			),
			'space_between' => array(
				'unit' => 'px',
				'size' => 12,
				'sizes' => array(),
			),
		),
	);
}

/**
 * Botão estilo tag (cards jurídicos).
 *
 * @param string $label Texto.
 */
function green_jur_tag_button( $label ) {
	return array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'button',
		'settings'   => array(
			'text'                  => $label,
			'link'                  => array( 'url' => '#' ),
			'button_type'           => 'default',
			'background_color'      => '#ffffff',
			'button_text_color'     => 'rgba(27,28,27,0.7)',
			'border_border'         => 'solid',
			'border_width'          => array(
				'unit'     => 'px',
				'top'      => '1',
				'right'    => '1',
				'bottom'   => '1',
				'left'     => '1',
				'isLinked' => true,
			),
			'border_color'          => '#e7e5e4',
			'typography_typography' => 'custom',
			'typography_font_family' => 'Manrope',
			'typography_font_size'  => array( 'unit' => 'px', 'size' => 12, 'sizes' => array() ),
			'typography_font_weight' => '600',
			'button_padding'        => array(
				'unit'     => 'px',
				'top'      => '4',
				'right'    => '12',
				'bottom'   => '4',
				'left'     => '12',
				'isLinked' => false,
			),
			'border_radius'         => array(
				'unit'     => 'px',
				'top'      => '4',
				'right'    => '4',
				'bottom'   => '4',
				'left'     => '4',
				'isLinked' => true,
			),
		),
	);
}

/**
 * Icon List com marcador redondo (círculo pequeno).
 *
 * @param string[] $lines Textos.
 */
function green_icon_list_dots( array $lines ) {
	$icon_list = array();
	foreach ( $lines as $text ) {
		$icon_list[] = array(
			'text'          => $text,
			'selected_icon' => array(
				'value'   => 'fas fa-circle',
				'library' => 'fa-solid',
			),
		);
	}
	return array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'icon-list',
		'settings'   => array(
			'icon_list'     => $icon_list,
			'icon_size'     => array(
				'unit' => 'px',
				'size' => 6,
				'sizes' => array(),
			),
			'__globals__'   => array(
				'icon_color' => green_g_color( 'secondary' ),
			),
			'space_between' => array(
				'unit' => 'px',
				'size' => 8,
				'sizes' => array(),
			),
		),
	);
}

/**
 * Card de especialidade (coluna externa com specialty-card).
 *
 * @param string   $icon Nome Material icon.
 * @param string   $title Título H3.
 * @param string   $desc HTML parágrafo.
 * @param array    $body Widgets adicionais (listas, inner sections).
 */
function green_specialty_column( $icon, $title, $desc, array $body = array() ) {
	$icon_row = green_sec(
		array(
			green_col(
				array(
					green_ms_te( $icon, 'var(--e-global-color-secondary)', '30px', false ),
				),
				array(
					'_column_size'     => 15,
					'content_position' => 'center',
					'css_classes'      => 'icon-round-bg',
				)
			),
			green_col(
				array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'       => $title,
							'header_size' => 'h3',
							'__globals__' => array(
								'title_color'            => green_g_color( 'primary' ),
								'typography_typography'  => 'globals/typography?id=a8f3c91',
							),
						),
					),
				),
				array(
					'_column_size'     => 85,
					'content_position' => 'center',
				)
			),
		),
		array(
			'layout'             => 'full_width',
			'gap'                  => 'extended',
			'structure'            => '20',
			'content_width'        => array( 'unit' => 'px', 'size' => 1200 ),
		),
		true
	);

	$intro = array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'text-editor',
		'settings'   => array(
			'editor'                 => '<p>' . esc_html( $desc ) . '</p>',
			'text_color'             => '#3e4945',
			'typography_typography'  => 'custom',
			'typography_font_family' => 'Manrope',
			'typography_font_size'   => array( 'unit' => 'px', 'size' => 14, 'sizes' => array() ),
			'typography_line_height' => array( 'unit' => 'em', 'size' => 1.625, 'sizes' => array() ),
		),
	);

	$elements = array_merge( array( $icon_row, $intro ), $body );

	return green_col(
		$elements,
		array(
			'_column_size'        => 50,
			'width_tablet'        => array( 'size' => 100, 'unit' => '%' ),
			'width_mobile'        => array( 'size' => 100, 'unit' => '%' ),
			'background_background' => 'classic',
			'background_color'    => '#f6f3f1',
			'padding'             => array(
				'unit'     => 'px',
				'top'      => '32',
				'bottom'   => '32',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => true,
			),
			'css_classes'         => 'specialty-card',
		)
	);
}

/**
 * Membro da equipe (inner: foto + texto + ícone LinkedIn).
 *
 * @param string $photo_url URL da foto.
 * @param string $name Nome.
 * @param string $role Cargo.
 * @param int    $attach_id ID na biblioteca de mídia (0 = só URL).
 */
function green_team_member( $photo_url, $name, $role, $attach_id = 0 ) {
	$img = array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'image',
		'settings'   => array(
			'image'       => array(
				'url' => $photo_url,
				'id'  => $attach_id ? (string) $attach_id : '',
			),
			'width'                 => array( 'unit' => 'px', 'size' => 96 ),
			'height'                => array( 'unit' => 'px', 'size' => 96 ),
			'height_tablet'         => array( 'unit' => 'px', 'size' => 96 ),
			'height_mobile'         => array( 'unit' => 'px', 'size' => 96 ),
			'image_border_radius'   => array(
				'unit'     => 'px',
				'top'      => '9999',
				'right'    => '9999',
				'bottom'   => '9999',
				'left'     => '9999',
				'isLinked' => true,
			),
			'image_border_border'   => 'solid',
			'image_border_width'    => array( 'unit' => 'px', 'size' => 2, 'sizes' => array() ),
			'image_border_color'    => '#ffffff',
			'css_classes'           => 'team-photo-bw team-member-photo',
		),
	);

	$heading = array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'heading',
		'settings'   => array(
			'title'                 => $name,
			'header_size'           => 'h4',
			'title_color'           => '#005646',
			'typography_typography' => 'custom',
			'typography_font_family' => 'Manrope',
			'typography_font_size'  => array( 'unit' => 'px', 'size' => 16, 'sizes' => array() ),
			'typography_font_weight' => '700',
			'typography_text_transform' => 'uppercase',
			'typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.02, 'sizes' => array() ),
			'typography_line_height' => array( 'unit' => 'em', 'size' => 1.25, 'sizes' => array() ),
		),
	);

	$role_te = array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'text-editor',
		'settings'   => array(
			'editor'                => '<p>' . esc_html( $role ) . '</p>',
			'text_color'             => '#78716c',
			'typography_typography'  => 'custom',
			'typography_font_family' => 'Manrope',
			'typography_font_size'   => array( 'unit' => 'px', 'size' => 11, 'sizes' => array() ),
			'typography_font_weight' => '400',
			'typography_text_transform' => 'uppercase',
			'typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.06, 'sizes' => array() ),
		),
	);

	$li = array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'icon',
		'settings'   => array(
			'selected_icon' => array(
				'value'   => 'fab fa-linkedin-in',
				'library' => 'fa-brands',
			),
			'link'          => array(
				'url'         => '#',
				'is_external' => 'true',
				'nofollow'    => '',
			),
			'align'          => 'left',
			'size'           => array( 'unit' => 'px', 'size' => 18, 'sizes' => array() ),
			'primary_color'  => '#006b57',
			'css_classes'    => 'green-team-linkedin',
		),
	);

	return green_sec(
		array(
			green_col(
				array( $img ),
				array(
					'_column_size'      => 25,
					'width'             => array( 'unit' => 'px', 'size' => 104, 'sizes' => array() ),
					'width_tablet'      => array( 'unit' => 'px', 'size' => 104, 'sizes' => array() ),
					'content_position'  => 'center',
				)
			),
			green_col(
				array( $heading, $role_te, $li ),
				array(
					'_column_size'     => 75,
					'content_position' => 'center',
				)
			),
		),
		array(
			'layout'                => 'full_width',
			'gap'                   => 'narrow',
			'structure'             => '20',
			'content_width'         => array( 'unit' => 'px', 'size' => 1200, 'sizes' => array() ),
			'content_position'      => 'middle',
		),
		true
	);
}

/**
 * @param array    $columns Colunas (cada uma é green_col()).
 * @param array    $settings Settings da secção.
 * @param bool     $inner Inner section.
 */
function green_sec( $columns, $settings = array(), $inner = false ) {
	return array(
		'id'        => green_eid(),
		'elType'    => 'section',
		'isInner'   => $inner,
		'settings'  => $settings,
		'elements'  => $columns,
	);
}

/**
 * @param array $elements Widgets ou secções internas.
 * @param array $settings Settings da coluna.
 */
function green_col( $elements, $settings = array() ) {
	return array(
		'id'       => green_eid(),
		'elType'   => 'column',
		'settings' => array_merge( array( '_column_size' => 100 ), $settings ),
		'elements' => $elements,
	);
}

/**
 * Homepage completa.
 *
 * @param string       $logo_url URL do logo (reservado / alinhamento futuro).
 * @param string|array $hero_bg  URL do hero ou array [ 'url' => string, 'id' => int|string ].
 * @param string       $home_url URL base.
 * @param string       $team_base URL base (com barra final) para fotos team-*.png em uploads/green-brand/.
 * @param int[]|null   $team_attachment_ids Seis IDs de mídia na ordem Renata… Michael; null = só URLs em $team_base.
 * @return array
 */
function green_build_homepage_elements( $logo_url, $hero_bg, $home_url, $team_base = '', $team_attachment_ids = null ) {
	unset( $logo_url );
	$b           = trailingslashit( $team_base );
	$team_photos = array(
		$b . 'team-renata.png',
		$b . 'team-samanta.png',
		$b . 'team-melisande.png',
		$b . 'team-vanessa.png',
		$b . 'team-diogo.png',
		$b . 'team-michael.png',
	);

	$hero_bg_url = is_array( $hero_bg ) ? (string) ( $hero_bg['url'] ?? '' ) : (string) $hero_bg;
	$hero_bg_id  = '';
	if ( is_array( $hero_bg ) && ! empty( $hero_bg['id'] ) ) {
		$hero_bg_id = (string) $hero_bg['id'];
	}

	$team_ids = array( 0, 0, 0, 0, 0, 0 );
	if ( is_array( $team_attachment_ids ) ) {
		for ( $ti = 0; $ti < 6; $ti++ ) {
			$tid = isset( $team_attachment_ids[ $ti ] ) ? (int) $team_attachment_ids[ $ti ] : 0;
			$team_ids[ $ti ] = $tid;
			if ( $tid ) {
				$u = wp_get_attachment_image_url( $tid, 'full' );
				if ( $u ) {
					$team_photos[ $ti ] = $u;
				}
			}
		}
	}

	/* --- Hero (compensa header fixo + largura máx. 1200) --- */
	$hero = green_sec(
		array(
			green_col(
				array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor'        => '<p>TRADUÇÕES CORPORATIVAS DE ELITE</p>',
							'css_classes'   => 'hero-badge',
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'       => 'Assim como você, nós também percorremos um longo caminho para chegar aonde estamos.',
							'header_size' => 'h1',
							'align'       => 'left',
							'title_color' => '#ffffff',
							'css_classes' => 'green-hero-h1',
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor'      => '<p>Criada por associados com mais de 20 anos de atuação em traduções financeiras e corporativas para as principais companhias brasileiras, a Green Associados possui a experiência e o conhecimento necessários para que a sua mensagem seja interpretada corretamente em qualquer parte do mundo.</p>',
							'css_classes' => 'green-hero-lede',
						),
					),
					green_sec(
						array(
							green_col(
								array(
									array(
										'id'         => green_eid(),
										'elType'     => 'widget',
										'widgetType' => 'button',
										'settings'   => array(
											'text'         => 'Fale Conosco',
											'link'         => array( 'url' => '#contato' ),
											'align'        => 'left',
											'size'         => 'md',
											'button_type'  => 'default',
											'background_color' => '#42fdd3',
											'button_text_color'     => '#002019',
											'typography_typography' => 'custom',
											'typography_font_family' => 'Manrope',
											'typography_font_weight' => '700',
											'typography_font_size'  => array( 'unit' => 'px', 'size' => 15, 'sizes' => array() ),
										),
									),
								),
								array(
									'_column_size' => 50,
									'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
								)
							),
							green_col(
								array(
									array(
										'id'         => green_eid(),
										'elType'     => 'widget',
										'widgetType' => 'button',
										'settings'   => array(
											'text'                => 'Nossos Serviços',
											'link'                => array( 'url' => '#atuacao' ),
											'align'               => 'left',
											'button_type'         => 'outline',
											'background_color'    => 'rgba(255,255,255,0.1)',
											'border_border'       => 'solid',
											'border_width'        => array(
												'unit'     => 'px',
												'top'      => '1',
												'right'    => '1',
												'bottom'   => '1',
												'left'     => '1',
												'isLinked' => true,
											),
											'border_color'        => 'rgba(255,255,255,0.25)',
											'button_text_color'   => '#ffffff',
											'typography_typography' => 'custom',
											'typography_font_family' => 'Manrope',
											'typography_font_weight' => '700',
											'typography_font_size'  => array( 'unit' => 'px', 'size' => 15, 'sizes' => array() ),
											'css_classes'            => 'hero-btn-outline',
										),
									),
								),
								array(
									'_column_size' => 50,
									'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
								)
							),
						),
						array(
							'layout'    => 'full_width',
							'structure' => '20',
							'gap'       => 'narrow',
						),
						true
					),
				),
				array(
					'_column_size'  => 100,
					'width'         => array( 'unit' => 'px', 'size' => 768, 'sizes' => array() ),
					'width_tablet'  => array( 'unit' => '%', 'size' => 100, 'sizes' => array() ),
					'width_mobile'  => array( 'unit' => '%', 'size' => 100, 'sizes' => array() ),
				)
			),
		),
		array(
			'layout'                          => 'full_width',
			'height'                          => 'min-height',
			'custom_height'                   => array( 'unit' => 'px', 'size' => 870, 'sizes' => array() ),
			'custom_height_tablet'            => array( 'unit' => 'px', 'size' => 720, 'sizes' => array() ),
			'custom_height_mobile'            => array( 'unit' => 'vh', 'size' => 88, 'sizes' => array() ),
			'padding'                         => array(
				'unit'     => 'px',
				'top'      => '168',
				'bottom'   => '128',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => false,
			),
			'padding_tablet'                  => array(
				'unit'     => 'px',
				'top'      => '144',
				'bottom'   => '112',
				'left'     => '24',
				'right'    => '24',
				'isLinked' => false,
			),
			'padding_mobile'                  => array(
				'unit'     => 'px',
				'top'      => '120',
				'bottom'   => '88',
				'left'     => '16',
				'right'    => '16',
				'isLinked' => false,
			),
			'css_classes'                     => 'green-hero-paulista',
			'background_background'           => 'classic',
			'background_image'              => array( 'url' => $hero_bg_url, 'id' => $hero_bg_id ),
			'background_position'           => 'center center',
			'background_size'               => 'cover',
			'background_overlay_background' => 'gradient',
			'background_overlay_color'            => 'rgba(0,86,70,0.9)',
			'background_overlay_color_b'          => 'rgba(0,86,70,0)',
			'background_overlay_gradient_angle'     => array( 'unit' => 'deg', 'size' => 90 ),
			'background_overlay_opacity'    => array( 'size' => 1, 'unit' => 'px' ),
			'content_width'                 => array( 'unit' => 'px', 'size' => 1200 ),
		),
		false
	);

	/* --- Quick Highlights --- */
	$btn_link = array(
		'button_type'   => 'link',
		'align'         => 'left',
		'selected_icon' => array(
			'value'   => 'fas fa-arrow-right',
			'library' => 'fa-solid',
		),
		'icon_align'    => 'row-reverse',
		'icon_indent'   => array( 'unit' => 'px', 'size' => 8 ),
		'css_classes'   => 'highlight-card',
		'__globals__'   => array(
			'typography_typography' => green_g_typo( 'accent' ),
		),
	);

	$qh1 = green_col(
		array(
			green_ms_te( 'translate', '#00e0b8', '38px' ),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'                 => 'ÁREAS DE ATUAÇÃO',
					'header_size'           => 'h3',
					'title_color'           => '#005646',
					'typography_typography' => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_size'  => array( 'unit' => 'px', 'size' => 18, 'sizes' => array() ),
					'typography_font_weight' => '700',
				),
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p>Prestamos serviços especializados de tradução, revisão e também de pós-edição de conteúdos traduzidos por IA, para as mais diversas áreas, nos idiomas português, inglês e espanhol.</p>',
					'text_color' => '#3e4945',
					'typography_typography' => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_size' => array( 'unit' => 'px', 'size' => 14, 'sizes' => array() ),
					'typography_line_height' => array( 'unit' => 'em', 'size' => 1.6, 'sizes' => array() ),
				),
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'button',
				'settings'   => array_merge(
					$btn_link,
					array(
						'text' => 'Saiba mais aqui',
						'link' => array( 'url' => '#atuacao' ),
						'__globals__' => array(
							'typography_typography' => green_g_typo( 'accent' ),
							'button_text_color'     => green_g_color( 'primary' ),
						),
					)
				),
			),
		),
		array(
			'_column_size'  => 33,
			'width_mobile'  => array( 'size' => 100, 'unit' => '%' ),
			'css_classes'   => 'highlight-card qh-card-white',
		)
	);

	$qh2 = green_col(
		array(
			green_ms_te( 'groups', '#3dfad0', '38px' ),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'                  => 'NOSSA EQUIPE',
					'header_size'            => 'h3',
					'title_color'            => '#ffffff',
					'typography_typography'  => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_size'   => array( 'unit' => 'px', 'size' => 18, 'sizes' => array() ),
					'typography_font_weight' => '700',
				),
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p>Somos especialistas no mercado financeiro e de negócios, com mais de 20 anos de experiência.</p>',
					'text_color' => '#99f2d7',
					'typography_typography' => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_size' => array( 'unit' => 'px', 'size' => 14, 'sizes' => array() ),
					'typography_line_height' => array( 'unit' => 'em', 'size' => 1.6, 'sizes' => array() ),
				),
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'button',
				'settings'   => array_merge(
					$btn_link,
					array(
						'text' => 'Conheça nosso time',
						'link' => array( 'url' => '#equipe' ),
						'__globals__' => array(
							'typography_typography' => green_g_typo( 'accent' ),
							'button_text_color'     => green_g_color( 'light_on_dark' ),
						),
					)
				),
			),
		),
		array(
			'_column_size' => 33,
			'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
			'css_classes'  => 'highlight-card qh-card-primary',
		)
	);

	$qh3 = green_col(
		array(
			green_ms_te( 'verified_user', '#7d3714', '38px' ),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'                  => 'DIFERENCIAIS',
					'header_size'            => 'h3',
					'title_color'            => '#005646',
					'typography_typography'  => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_size'   => array( 'unit' => 'px', 'size' => 18, 'sizes' => array() ),
					'typography_font_weight' => '700',
				),
			),
			green_icon_list_checks(
				array(
					'Alta disponibilidade e rápido atendimento',
					'Traduções que combinam IA + revisão humana',
					'Time pequeno e especializado, garantindo uniformidade, consistência e atenção a cada detalhe',
					'Confidencialidade e Firewall físico',
				)
			),
		),
		array(
			'_column_size' => 33,
			'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
			'css_classes'  => 'qh-card-muted',
		)
	);

	$quick_highlights = green_sec(
		array( $qh1, $qh2, $qh3 ),
		array(
			'layout'                  => 'full_width',
			'css_classes'             => 'green-qh-overlap',
			'content_width'           => array( 'unit' => 'px', 'size' => 1200 ),
			'gap'                     => 'extended',
			'structure'               => '30',
			'padding'                 => array(
				'unit'     => 'px',
				'top'      => '0',
				'bottom'   => '0',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => false,
			),
			'margin'                  => array(
				'unit'     => 'px',
				'top'      => '-80',
				'bottom'   => '0',
				'left'     => '0',
				'right'    => '0',
				'isLinked' => false,
			),
			'margin_mobile'           => array(
				'unit'     => 'px',
				'top'      => '-56',
				'bottom'   => '0',
				'left'     => '0',
				'right'    => '0',
				'isLinked' => false,
			),
			'z_index'                 => 20,
		),
		false
	);

	/* --- Áreas de atuação --- */
	$corp_items = array(
		'Balanços sociais',
		'Websites',
		'Press releases',
		'Newsletters',
		'Folders',
		'Acordos e contratos',
		'Códigos de conduta ética',
		'Políticas de divulgação e negociação',
		'Relatórios de avaliação e de rating',
		'Relatórios anuais e de sustentabilidade',
		'Materiais de comunicação para eventos',
		'Materiais de comunicação interna',
	);
	$corp_left  = array_slice( $corp_items, 0, 6 );
	$corp_right = array_slice( $corp_items, 6 );

	$card_corp = green_specialty_column(
		'corporate_fare',
		'Comunicação Corporativa',
		'Nossos serviços profissionais são respaldados por knowledge e experiência, a fim de garantir que a transmissão de informações para seus stakeholders globais seja clara e precisa, colaborando na construção da reputação de sua empresa.',
		array(
			green_sec(
				array(
					green_col( array( green_icon_list_dots( $corp_left ) ), array( '_column_size' => 50 ) ),
					green_col( array( green_icon_list_dots( $corp_right ) ), array( '_column_size' => 50 ) ),
				),
				array( 'layout' => 'full_width', 'structure' => '20' ),
				true
			),
		)
	);

	$ri_inner = green_sec(
		array(
			green_col(
				array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'       => 'DIVULGAÇÃO DE RESULTADOS TRIMESTRAIS',
							'header_size' => 'h4',
							'css_classes' => 'subgroup-heading',
						),
					),
					green_icon_list_dots(
						array(
							'Releases de resultados',
							'Discursos para teleconferência',
							'Apresentações',
							'Transcrições',
						)
					),
				),
				array( '_column_size' => 50 )
			),
			green_col(
				array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'       => 'DOCUMENTOS DIVERSOS',
							'header_size' => 'h4',
							'css_classes' => 'subgroup-heading',
						),
					),
					green_icon_list_dots(
						array(
							'Comunicados ao mercado',
							'Fatos relevantes',
							'Atas de assembleias gerais e reuniões',
							'Apresentações para road shows',
							'Fact sheets',
						)
					),
				),
				array( '_column_size' => 50 )
			),
		),
		array( 'layout' => 'full_width', 'structure' => '20' ),
		true
	);

	$card_ri = green_specialty_column(
		'query_stats',
		'Relações com Investidores',
		'Nossa comprovada experiência e longa especialização no mercado de RI são componentes essenciais para ajudá-lo a alavancar o potencial de sua base acionária fora do Brasil.',
		array( $ri_inner )
	);

	$fin_left  = array(
		'Informações Trimestrais (ITR)',
		'Demonstrações Padronizadas (DFP)',
		'Formulários de Referência',
		'Demonstrações financeiras e balanços contábeis',
	);
	$fin_right = array(
		'Parecer dos auditores',
		'Formulários 20-F, 10-K e 8-K (SEC)',
		'Manuais para participação em assembleias gerais',
	);

	$card_fin = green_specialty_column(
		'account_balance',
		'Financeira (documentos exigidos pela CVM e SEC)',
		'Nossa equipe possui extenso conhecimento de práticas contábeis e exigências regulatórias e de governança.',
		array(
			green_sec(
				array(
					green_col( array( green_icon_list_dots( $fin_left ) ), array( '_column_size' => 50 ) ),
					green_col( array( green_icon_list_dots( $fin_right ) ), array( '_column_size' => 50 ) ),
				),
				array( 'layout' => 'full_width', 'structure' => '20' ),
				true
			),
		)
	);

	$jur_btns = green_sec(
		array(
			green_col( array( green_jur_tag_button( 'Procurações' ) ), array( '_column_size' => 25 ) ),
			green_col( array( green_jur_tag_button( 'Atas de assembleias' ) ), array( '_column_size' => 25 ) ),
			green_col( array( green_jur_tag_button( 'Contratos' ) ), array( '_column_size' => 25 ) ),
			green_col( array( green_jur_tag_button( 'Pareceres jurídicos' ) ), array( '_column_size' => 25 ) ),
		),
		array( 'layout' => 'full_width', 'structure' => '25' ),
		true
	);

	$card_jur = green_specialty_column(
		'gavel',
		'Jurídica',
		'No universo jurídico, cada termo importa — e qualquer imprecisão pode alterar o sentido de cláusulas e comprometer resultados. Por isso, nossas traduções jurídicas são realizadas por profissionais especializados, com profundo entendimento de conceitos legais e terminologia específica de cada jurisdição.',
		array( $jur_btns )
	);

	$sec_atuacao = green_sec(
		array(
			green_col(
				array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'                  => 'ÁREAS DE ATUAÇÃO',
							'header_size'            => 'h2',
							'title_color'            => '#005646',
							'typography_typography'  => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 38, 'sizes' => array() ),
							'typography_font_weight' => '700',
							'typography_line_height' => array( 'unit' => 'em', 'size' => 1.15, 'sizes' => array() ),
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'divider',
						'settings'   => array(
							'width' => array( 'unit' => 'px', 'size' => 80 ),
							'weight' => array( 'unit' => 'px', 'size' => 6 ),
							'color' => '#7d3714',
							'align' => 'left',
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor'                 => '<p>A equipe da Green Associados atua há mais de 20 anos em diversos segmentos do mercado, sendo especialista nas seguintes áreas:</p>',
							'text_color'             => '#3e4945',
							'typography_typography'  => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 18, 'sizes' => array() ),
							'typography_line_height' => array( 'unit' => 'em', 'size' => 1.625, 'sizes' => array() ),
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'spacer',
						'settings'   => array(
							'space' => array( 'unit' => 'px', 'size' => 48 ),
						),
					),
					green_sec(
						array( $card_corp, $card_ri ),
						array(
							'layout'        => 'full_width',
							'gap'           => 'extended',
							'structure'     => '20',
							'content_width' => array( 'unit' => 'px', 'size' => 1200 ),
						),
						true
					),
					green_sec(
						array( $card_fin, $card_jur ),
						array(
							'layout'    => 'full_width',
							'gap'       => 'extended',
							'structure' => '20',
						),
						true
					),
				),
				array( '_column_size' => 100 ),
			),
		),
		array(
			'css_id'            => 'atuacao',
			'layout'            => 'full_width',
			'content_width'     => array( 'unit' => 'px', 'size' => 1200 ),
			'background_background' => 'classic',
			'background_color'  => '#fcf9f7',
			'padding'           => array(
				'unit'     => 'px',
				'top'      => '96',
				'bottom'   => '96',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => false,
			),
			'padding_mobile'    => array(
				'unit'     => 'px',
				'top'      => '56',
				'bottom'   => '56',
				'left'     => '16',
				'right'    => '16',
				'isLinked' => false,
			),
		),
		false
	);

	/* --- IA --- */
	$feat = function ( $icon, $h, $txt, $extra_sec = array() ) {
		$base = array( 'layout' => 'full_width', 'gap' => 'extended', 'structure' => '20' );
		return green_sec(
			array(
				green_col(
					array( green_ms_te( $icon, '#42fdd3', '48px', false ) ),
					array( '_column_size' => 20, 'content_position' => 'center' )
				),
				green_col(
					array(
						array(
							'id'         => green_eid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'                  => $h,
								'header_size'            => 'h4',
								'title_color'            => '#ffffff',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Manrope',
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 18, 'sizes' => array() ),
								'typography_font_weight' => '700',
							),
						),
						array(
							'id'         => green_eid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor'                  => '<p>' . esc_html( $txt ) . '</p>',
								'text_color'              => '#f6f3f1',
								'typography_typography'   => 'custom',
								'typography_font_family'  => 'Manrope',
								'typography_font_size'    => array( 'unit' => 'px', 'size' => 14, 'sizes' => array() ),
								'typography_line_height'  => array( 'unit' => 'em', 'size' => 1.5, 'sizes' => array() ),
							),
						),
					),
					array( '_column_size' => 80 )
				),
			),
			array_merge( $base, $extra_sec ),
			true
		);
	};

	$ia_left = green_col(
		array(
			green_sec(
				array(
					green_col(
						array(
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'divider',
								'settings'   => array(
									'width'  => array( 'unit' => 'px', 'size' => 40 ),
									'weight' => array( 'unit' => 'px', 'size' => 1 ),
									'color'  => '#42fdd3',
									'align'  => 'left',
								),
							),
						),
						array( '_column_size' => 10, 'content_position' => 'center' )
					),
					green_col(
						array(
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'text-editor',
								'settings'   => array(
									'editor' => '<p>TECNOLOGIA &amp; INOVAÇÃO</p>',
									'text_color' => '#42fdd3',
									'typography_typography' => 'custom',
									'typography_font_family' => 'Manrope',
									'typography_font_size' => array( 'unit' => 'px', 'size' => 11, 'sizes' => array() ),
									'typography_font_weight' => '700',
									'typography_text_transform' => 'uppercase',
									'typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.25, 'sizes' => array() ),
								),
							),
						),
						array( '_column_size' => 90, 'content_position' => 'center' ),
					),
				),
				array( 'layout' => 'full_width', 'structure' => '20', 'gap' => 'narrow' ),
				true
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'                  => 'INTELIGÊNCIA ARTIFICIAL',
					'header_size'            => 'h2',
					'title_color'            => '#ffffff',
					'typography_typography'  => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_size'   => array( 'unit' => 'px', 'size' => 46, 'sizes' => array() ),
					'typography_font_weight' => '700',
					'typography_line_height' => array( 'unit' => 'em', 'size' => 1.15, 'sizes' => array() ),
				),
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p>Para seguir as últimas tecnologias aplicadas ao mercado linguístico e otimizar o tempo e os custos de nossos clientes, atualmente utilizamos na Green Associados uma ferramenta desenvolvida por e para tradutores, que combina Inteligência Artificial (IA) com os glossários e memórias de tradução desenvolvidos por nós, específicos para cada cliente atendido. Assim, é possível gerar conteúdos de qualidade superior, que passam por revisão e edição cuidadosa, humana e especializada de nossos sócios.</p>',
					'text_color'               => 'rgba(255,255,255,0.9)',
					'typography_typography'    => 'custom',
					'typography_font_family'   => 'Manrope',
					'typography_font_size'     => array( 'unit' => 'px', 'size' => 17, 'sizes' => array() ),
					'typography_font_weight'   => '300',
					'typography_line_height'   => array( 'unit' => 'em', 'size' => 1.7, 'sizes' => array() ),
				),
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p>Além disso, desenvolvemos técnicas para revisões e pós-edição profissional de conteúdos traduzidos por IA gerados pelos clientes, corrigindo inconsistências, aprimorando o estilo e garantindo conformidade terminológica. Desta forma, é possível obter o melhor equilíbrio entre eficiência, qualidade e economia — com textos finalizados prontos para publicação e alinhados às expectativas do público global.</p>',
					'text_color'               => 'rgba(255,255,255,0.9)',
					'typography_typography'    => 'custom',
					'typography_font_family'   => 'Manrope',
					'typography_font_size'     => array( 'unit' => 'px', 'size' => 17, 'sizes' => array() ),
					'typography_font_weight'   => '300',
					'typography_line_height'   => array( 'unit' => 'em', 'size' => 1.7, 'sizes' => array() ),
				),
			),
		),
		array(
			'_column_size' => 58,
			'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
		)
	);

	$ia_right = green_col(
		array(
			$feat(
				'bolt',
				'Processamento Rápido',
				'Fluxo ágil com tecnologia de ponta e supervisão especializada.',
				array( 'css_classes' => 'feature-card-ia' )
			),
			$feat(
				'shield',
				'Resultados Comprovados',
				'Qualidade validada por especialistas e alinhada às melhores práticas do setor.',
				array( 'css_classes' => 'feature-card-ia' )
			),
		),
		array(
			'_column_size' => 42,
			'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
		)
	);

	$sec_ia = green_sec(
		array( $ia_left, $ia_right ),
		array(
			'css_id'                 => 'ia',
			'layout'                 => 'full_width',
			'content_width'          => array( 'unit' => 'px', 'size' => 1200 ),
			'background_background'  => 'classic',
			'background_color'       => '#005646',
			'padding'                => array(
				'unit'     => 'px',
				'top'      => '96',
				'bottom'   => '96',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => false,
			),
			'padding_mobile'         => array(
				'unit'     => 'px',
				'top'      => '56',
				'bottom'   => '56',
				'left'     => '16',
				'right'    => '16',
				'isLinked' => false,
			),
		),
		false
	);

	/* --- Segurança (pilares = inner sections empilhados em 2 colunas) --- */
	$pillar_card = function ( $icon, $title, $desc ) {
		$ms      = green_ms_te( $icon, '#7d3714', '28px' );
		$icon_te = array(
			'id'         => green_eid(),
			'elType'     => 'widget',
			'widgetType' => 'text-editor',
			'settings'   => array(
				'editor' => '<div class="pillar-icon-wrap">' . $ms['settings']['editor'] . '</div>',
			),
		);
		return green_sec(
			array(
				green_col(
					array(
						$icon_te,
						array(
							'id'         => green_eid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'                  => $title,
								'header_size'            => 'h4',
								'title_color'            => '#005646',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Manrope',
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 16, 'sizes' => array() ),
								'typography_font_weight' => '700',
							),
						),
						array(
							'id'         => green_eid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor'                 => '<p>' . esc_html( $desc ) . '</p>',
								'text_color'             => 'rgba(62,73,69,0.75)',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Manrope',
								'typography_font_size'   => array( 'unit' => 'px', 'size' => 13, 'sizes' => array() ),
								'typography_line_height' => array( 'unit' => 'em', 'size' => 1.6, 'sizes' => array() ),
							),
						),
					),
					array( '_column_size' => 100 ),
				),
			),
			array(
				'layout'                 => 'full_width',
				'css_classes'            => 'security-pillar',
				'background_background'  => 'classic',
				'background_color'       => '#ffffff',
				'border_border'          => 'solid',
				'border_width'           => array(
					'unit'     => 'px',
					'top'      => '1',
					'right'    => '1',
					'bottom'   => '1',
					'left'     => '1',
					'isLinked' => true,
				),
				'border_color'           => 'rgba(125, 55, 20, 0.1)',
				'border_radius'          => array(
					'unit'     => 'px',
					'top'      => '16',
					'right'    => '16',
					'bottom'   => '16',
					'left'     => '16',
					'isLinked' => true,
				),
				'padding'                => array(
					'unit'     => 'px',
					'top'      => '32',
					'bottom'   => '32',
					'left'     => '32',
					'right'    => '32',
					'isLinked' => true,
				),
			),
			true
		);
	};

	$pc1 = $pillar_card( 'dns', 'Firewall independente', 'Infraestrutura física dedicada para proteção máxima de dados em nossos servidores.' );
	$pc2 = $pillar_card( 'encrypted', 'E-mail e conteúdos criptografados', 'Utilização de Microsoft Exchange Hosted Encryption® em todas as comunicações.' );
	$pc3 = $pillar_card( 'psychology_alt', 'Uso seguro da IA', 'Tecnologia com certificação SOC 2 Type II para garantir privacidade total dos projetos.' );
	$pc4 = $pillar_card( 'description', 'Contrato de confidencialidade', 'Todos os envolvidos possuem NDAs assinados legalmente para sua tranquilidade.' );

	$spacer_pillar = array(
		'id'         => green_eid(),
		'elType'     => 'widget',
		'widgetType' => 'spacer',
		'settings'   => array(
			'space' => array( 'unit' => 'px', 'size' => 24 ),
		),
	);

	$grid_seg = green_sec(
		array(
			green_col(
				array( $pc1, $spacer_pillar, $pc3 ),
				array(
					'_column_size' => 50,
					'margin'       => array(
						'unit'     => 'px',
						'top'      => '32',
						'bottom'   => '0',
						'left'     => '0',
						'right'    => '0',
						'isLinked' => false,
					),
					'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
				)
			),
			green_col(
				array(
					$pc2,
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'spacer',
						'settings'   => array(
							'space' => array( 'unit' => 'px', 'size' => 24 ),
						),
					),
					$pc4,
				),
				array(
					'_column_size' => 50,
					'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
				)
			),
		),
		array( 'layout' => 'full_width', 'structure' => '20' ),
		true
	);

	$sec_seg = green_sec(
		array(
			green_col(
				array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor'                  => '<p>PROTEÇÃO E CONFIANÇA</p>',
							'text_color'              => '#7d3714',
							'typography_typography'   => 'custom',
							'typography_font_family'  => 'Manrope',
							'typography_font_size'    => array( 'unit' => 'px', 'size' => 11, 'sizes' => array() ),
							'typography_font_weight'  => '700',
							'typography_text_transform' => 'uppercase',
							'typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.18, 'sizes' => array() ),
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'                  => 'SEGURANÇA',
							'header_size'            => 'h2',
							'title_color'            => '#005646',
							'typography_typography'  => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 46, 'sizes' => array() ),
							'typography_font_weight' => '700',
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'divider',
						'settings'   => array(
							'width'  => array( 'unit' => 'px', 'size' => 80 ),
							'weight' => array( 'unit' => 'px', 'size' => 6 ),
							'color'  => '#7d3714',
							'align'  => 'left',
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor'      => '<p>A Green Associados está ciente da imensa importância de se manter a confidencialidade e a integridade das informações recebidas de seus clientes.</p>',
							'css_classes' => 'quote-block',
							'text_color'  => '#3e4945',
							'typography_typography' => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 17, 'sizes' => array() ),
							'typography_font_weight' => '500',
							'typography_line_height' => array( 'unit' => 'em', 'size' => 1.65, 'sizes' => array() ),
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor' => '<p>Por isso, todos os nossos processos foram desenvolvidos para este fim, com destaque para a utilização de infraestrutura de firewall física e independente em nossos servidores e da ferramenta de criptografia de e-mail Microsoft Exchange Hosted Encryption® em todas as mensagens de e-mail enviadas.</p>',
							'text_color' => 'rgba(62,73,69,0.8)',
							'typography_typography' => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 15, 'sizes' => array() ),
							'typography_line_height' => array( 'unit' => 'em', 'size' => 1.65, 'sizes' => array() ),
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor' => '<p>A ferramenta utilizada em todas as traduções foi desenvolvida para garantir confidencialidade e segurança – por meio de criptografia, controles de acesso e armazenamento seguro –, atestada pela certificação SOC 2 Type II.</p>',
							'text_color' => 'rgba(62,73,69,0.8)',
							'typography_typography' => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 15, 'sizes' => array() ),
							'typography_line_height' => array( 'unit' => 'em', 'size' => 1.65, 'sizes' => array() ),
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor' => '<p>Além disso, todos os membros da equipe e eventuais fornecedores que possam ter acesso a informações confidenciais possuem contrato de confidencialidade assinado com a Green Associados.</p>',
							'text_color' => 'rgba(62,73,69,0.8)',
							'typography_typography' => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 15, 'sizes' => array() ),
							'typography_line_height' => array( 'unit' => 'em', 'size' => 1.65, 'sizes' => array() ),
						),
					),
				),
				array(
					'_column_size' => 50,
					'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
				)
			),
			green_col(
				array( $grid_seg ),
				array(
					'_column_size' => 50,
					'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
				)
			),
		),
		array(
			'css_id'                => 'seguranca',
			'layout'                => 'full_width',
			'content_width'         => array( 'unit' => 'px', 'size' => 1200 ),
			'background_background' => 'classic',
			'background_color'      => '#fcf9f7',
			'padding'               => array(
				'unit'     => 'px',
				'top'      => '96',
				'bottom'   => '96',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => false,
			),
			'overflow'              => 'hidden',
		),
		false
	);

	/* --- Equipe --- */
	$names = array(
		array( 'RENATA SILVERIO BALBINO', 'Sócia e tradutora' ),
		array( 'SAMANTA BASTOS ALCARDO', 'Sócia e tradutora' ),
		array( 'MELISANDE MANCUSI', 'Sócia e tradutora' ),
		array( 'VANESSA MELHADO', 'Sócia, gestora de projetos e da parte administrativa' ),
		array( 'DIOGO HOHL ORSI', 'Cofundador, sócio e gestor da parte financeira' ),
		array( 'MICHAEL JOSEPH FINHANE GREEN', 'Fundador e sócio majoritário' ),
	);

	$team_cells = array();
	foreach ( $names as $i => $nr ) {
		$team_cells[] = green_col(
			array( green_team_member( $team_photos[ $i ], $nr[0], $nr[1], (int) $team_ids[ $i ] ) ),
			array(
				'_column_size'  => 33,
				'width_tablet'  => array( 'size' => 50, 'unit' => '%' ),
				'width_mobile'  => array( 'size' => 100, 'unit' => '%' ),
			)
		);
	}

	$team_inner_opts = array(
		'layout'        => 'full_width',
		'gap'           => 'extended',
		'structure'     => '30',
		'content_width' => array( 'unit' => 'px', 'size' => 1200 ),
	);
	$team_row1       = green_sec(
		array( $team_cells[0], $team_cells[1], $team_cells[2] ),
		$team_inner_opts,
		true
	);
	$team_row2       = green_sec(
		array( $team_cells[3], $team_cells[4], $team_cells[5] ),
		$team_inner_opts,
		true
	);

	$sec_equipe = green_sec(
		array(
			green_col(
				array(
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'heading',
						'settings'   => array(
							'title'                  => 'NOSSA EQUIPE',
							'header_size'            => 'h2',
							'title_color'            => '#005646',
							'align'                  => 'center',
							'typography_typography'  => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 38, 'sizes' => array() ),
							'typography_font_weight' => '700',
						),
					),
					array(
						'id'         => green_eid(),
						'elType'     => 'widget',
						'widgetType' => 'text-editor',
						'settings'   => array(
							'editor'                 => '<p>Na Green Associados, nosso compromisso é garantir que sua comunicação internacional seja sempre segura, precisa e confiável. Aqui, temos paixão pelo que fazemos e traduzimos e revisamos seus documentos com cuidado e excelência.</p>',
							'align'                  => 'center',
							'text_color'             => '#475569',
							'typography_typography'  => 'custom',
							'typography_font_family' => 'Manrope',
							'typography_font_size'   => array( 'unit' => 'px', 'size' => 18, 'sizes' => array() ),
							'typography_font_weight' => '300',
							'typography_line_height' => array( 'unit' => 'em', 'size' => 1.625, 'sizes' => array() ),
						),
					),
					$team_row1,
					$team_row2,
				),
				array( '_column_size' => 100 ),
			),
		),
		array(
			'css_id'                => 'equipe',
			'layout'                => 'full_width',
			'background_background' => 'classic',
			'background_color'      => '#f6f3f1',
			'padding'               => array(
				'unit'     => 'px',
				'top'      => '96',
				'bottom'   => '96',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => false,
			),
		),
		false
	);

	/* --- Contato --- */
	$contact_left = green_col(
		array(
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'                  => 'FALE COM NOSSOS ESPECIALISTAS',
					'header_size'            => 'h2',
					'title_color'            => '#ffffff',
					'typography_typography'  => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_size'   => array( 'unit' => 'px', 'size' => 36, 'sizes' => array() ),
					'typography_font_weight' => '700',
					'typography_line_height' => array( 'unit' => 'em', 'size' => 1.2, 'sizes' => array() ),
				),
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor'      => '<p>Fale com nossos especialistas e eleve a qualidade da sua comunicação global com tradução profissional, segura e técnica.</p>',
					'css_classes' => 'contato-lede',
				),
			),
			green_sec(
				array(
					green_col( array( green_ms_te( 'phone_in_talk', '#42fdd3', '32px', false ) ), array( '_column_size' => 15 ) ),
					green_col(
						array(
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'text-editor',
								'settings'   => array(
									'editor'      => '<p>Telefone</p>',
									'css_classes' => 'contato-label',
								),
							),
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'heading',
								'settings'   => array(
									'title'       => '+55 11 97355-1012',
									'header_size' => 'h5',
									'title_color' => '#ffffff',
									'link'        => array(
										'url' => 'tel:+5511973551012',
									),
									'css_classes' => 'contato-valor',
								),
							),
						),
						array( '_column_size' => 85 )
					),
				),
				array( 'layout' => 'full_width', 'structure' => '20' ),
				true
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'spacer',
				'settings'   => array( 'space' => array( 'unit' => 'px', 'size' => 16 ) ),
			),
			green_sec(
				array(
					green_col( array( green_ms_te( 'mail', '#42fdd3', '32px', false ) ), array( '_column_size' => 15 ) ),
					green_col(
						array(
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'text-editor',
								'settings'   => array(
									'editor'      => '<p>E-mail</p>',
									'css_classes' => 'contato-label',
								),
							),
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'heading',
								'settings'   => array(
									'title'       => 'traducao@greenassociados.com.br',
									'header_size' => 'h5',
									'title_color' => '#ffffff',
									'link'        => array(
										'url' => 'mailto:traducao@greenassociados.com.br',
									),
									'css_classes' => 'contato-valor',
								),
							),
						),
						array( '_column_size' => 85 )
					),
				),
				array( 'layout' => 'full_width', 'structure' => '20' ),
				true
			),
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'spacer',
				'settings'   => array( 'space' => array( 'unit' => 'px', 'size' => 16 ) ),
			),
			green_sec(
				array(
					green_col( array( green_ms_te( 'location_on', '#42fdd3', '32px', false ) ), array( '_column_size' => 15 ) ),
					green_col(
						array(
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'text-editor',
								'settings'   => array(
									'editor'      => '<p>Endereço</p>',
									'css_classes' => 'contato-label',
								),
							),
							array(
								'id'         => green_eid(),
								'elType'     => 'widget',
								'widgetType' => 'text-editor',
								'settings'   => array(
									'editor'      => '<p>Avenida Pedroso de Morais, 631 – cj 107<br>Pinheiros – São Paulo/SP</p>',
									'css_classes' => 'contato-endereco',
								),
							),
						),
						array( '_column_size' => 85 )
					),
				),
				array( 'layout' => 'full_width', 'structure' => '20' ),
				true
			),
		),
		array(
			'_column_size' => 50,
			'width_mobile' => array( 'size' => 100, 'unit' => '%' ),
		)
	);

	$contact_right = green_col(
		array(
			array(
				'id'         => green_eid(),
				'elType'     => 'widget',
				'widgetType' => 'button',
				'settings'   => array(
					'text'  => 'WhatsApp',
					'link'  => array(
						'url'         => 'https://wa.me/551138128780',
						'is_external' => 'on',
						'nofollow'    => '',
					),
					'align' => 'center',
					'background_color'  => '#42fdd3',
					'button_text_color' => '#002019',
					'typography_typography' => 'custom',
					'typography_font_family' => 'Manrope',
					'typography_font_weight' => '700',
					'typography_font_size'  => array( 'unit' => 'px', 'size' => 20, 'sizes' => array() ),
					'button_padding'        => array(
						'unit'     => 'px',
						'top'      => '22',
						'right'    => '40',
						'bottom'   => '22',
						'left'     => '40',
						'isLinked' => false,
					),
					'border_radius'     => array(
						'unit' => 'px',
						'top' => '9999',
						'right' => '9999',
						'bottom' => '9999',
						'left' => '9999',
						'isLinked' => true,
					),
					'css_classes'      => 'whatsapp-cta',
					'selected_icon'    => array(
						'value'   => 'fab fa-whatsapp',
						'library' => 'fa-brands',
					),
				),
			),
		),
		array(
			'_column_size'     => 50,
			'content_position' => 'center',
			'width_mobile'     => array( 'size' => 100, 'unit' => '%' ),
		)
	);

	$sec_contato = green_sec(
		array(
			green_col(
				array(
					green_sec(
						array( $contact_left, $contact_right ),
						array(
							'layout'                 => 'full_width',
							'structure'              => '20',
							'css_classes'            => 'contato-block',
							'background_background'  => 'classic',
							'background_color'       => '#00715c',
							'padding'                => array(
								'unit'     => 'px',
								'top'      => '80',
								'bottom'   => '80',
								'left'     => '48',
								'right'    => '48',
								'isLinked' => false,
							),
							'padding_tablet'         => array(
								'unit'     => 'px',
								'top'      => '64',
								'bottom'   => '64',
								'left'     => '40',
								'right'    => '40',
								'isLinked' => false,
							),
						),
						true
					),
				),
				array( '_column_size' => 100 ),
			),
		),
		array(
			'css_id'                => 'contato',
			'layout'                => 'full_width',
			'content_width'         => array( 'unit' => 'px', 'size' => 1200 ),
			'background_background' => 'classic',
			'background_color'      => '#fafaf9',
			'padding'               => array(
				'unit'     => 'px',
				'top'      => '96',
				'bottom'   => '96',
				'left'     => '32',
				'right'    => '32',
				'isLinked' => false,
			),
		),
		false
	);

	return array(
		$hero,
		$quick_highlights,
		$sec_atuacao,
		$sec_ia,
		$sec_seg,
		$sec_equipe,
		$sec_contato,
	);
}
