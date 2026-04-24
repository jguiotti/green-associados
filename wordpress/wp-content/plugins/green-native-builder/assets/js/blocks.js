( function ( wp ) {
	const { __ } = wp.i18n;
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, MediaUpload, MediaUploadCheck, URLInputButton } = wp.blockEditor;
	const { PanelBody, TextControl, TextareaControl, Button, SelectControl, ToggleControl, RangeControl } = wp.components;

	function updateItemAt( list, index, patch ) {
		return list.map( ( item, i ) => ( i === index ? { ...item, ...patch } : item ) );
	}

	function removeItemAt( list, index ) {
		return list.filter( ( _, i ) => i !== index );
	}

	function addItem( list, item ) {
		return [ ...list, item ];
	}

	function arrayFromLines( text ) {
		if ( ! text || typeof text !== 'string' ) {
			return [];
		}
		return text
			.split( '\n' )
			.map( ( line ) => line.trim() )
			.filter( Boolean );
	}

	function linesFromArray( arr ) {
		return Array.isArray( arr ) && arr.length ? arr.join( '\n' ) : '';
	}

	function repeaterControls( label, items, fields, onChange, onAdd, addLabel ) {
		return el(
			PanelBody,
			{ title: label, initialOpen: false },
			items.map( ( item, index ) =>
				el(
					'div',
					{ key: `${ label }-${ index }`, className: 'green-nb-repeater-item' },
					fields.map( ( field ) =>
						el( TextControl, {
							key: field.key,
							label: field.label,
							value: item[ field.key ] || '',
							onChange: ( value ) => onChange( index, field.key, value ),
						} )
					),
					el(
						Button,
						{
							isDestructive: true,
							variant: 'secondary',
							onClick: () => onAdd( removeItemAt( items, index ) ),
						},
						__( 'Remover item', 'green-native-builder' )
					)
				)
			),
			el(
				Button,
				{
					variant: 'primary',
					onClick: () => onAdd( addItem( items, {} ) ),
				},
				addLabel || __( 'Adicionar item', 'green-native-builder' )
			)
		);
	}

	function dynamicSave() {
		return null;
	}

	function getSiteHeaderMenuOptions() {
		const menus = typeof greenNbSiteHeader !== 'undefined' && greenNbSiteHeader.menus ? greenNbSiteHeader.menus : [];
		return [
			{ value: 0, label: __( 'Menu principal do tema (Aparência → Menus, local «primary»)', 'green-native-builder' ) },
		].concat( menus.map( ( m ) => ( { value: m.id, label: m.name } ) ) );
	}

	registerBlockType( 'green/site-header', {
		title: __( 'Green: Cabeçalho do site', 'green-native-builder' ),
		icon: 'layout',
		category: 'design',
		supports: {
			anchor: false,
		},
		attributes: {
			logoId: { type: 'number', default: 0 },
			menuId: { type: 'number', default: 0 },
			showLangSwitcher: { type: 'boolean', default: true },
			logoMaxWidth: { type: 'number', default: 180 },
		},
		edit: ( { attributes, setAttributes } ) => {
			const menuOptions = getSiteHeaderMenuOptions();
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Logo', 'green-native-builder' ), initialOpen: true },
						el( MediaUploadCheck, null, el( MediaUpload, {
							onSelect: ( media ) => setAttributes( { logoId: media?.id ? parseInt( media.id, 10 ) : 0 } ),
							allowedTypes: [ 'image' ],
							value: attributes.logoId || undefined,
							render: ( { open } ) =>
								el(
									'div',
									{ className: 'green-nb-site-header-logo-actions' },
									el(
										Button,
										{ onClick: open, variant: 'secondary' },
										attributes.logoId
											? __( 'Trocar imagem do logo', 'green-native-builder' )
											: __( 'Escolher imagem do logo', 'green-native-builder' )
									),
									attributes.logoId > 0 &&
										el(
											Button,
											{
												isDestructive: true,
												variant: 'link',
												onClick: () => setAttributes( { logoId: 0 } ),
											},
											__( 'Usar logo do Personalizar', 'green-native-builder' )
										)
								),
						} ) ),
						el( 'p', { className: 'description' }, __( 'Com ID 0, usa o logo definido em Personalizar → Identidade do site.', 'green-native-builder' ) ),
						el( RangeControl, {
							label: __( 'Largura máxima do logo (px)', 'green-native-builder' ),
							value: attributes.logoMaxWidth,
							onChange: ( logoMaxWidth ) => setAttributes( { logoMaxWidth } ),
							min: 80,
							max: 280,
							step: 4,
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Navegação', 'green-native-builder' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Menu WordPress', 'green-native-builder' ),
							value: attributes.menuId,
							options: menuOptions,
							onChange: ( val ) => setAttributes( { menuId: parseInt( val, 10 ) || 0 } ),
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Idiomas (Polylang)', 'green-native-builder' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Mostrar seletor de idioma (Polylang)', 'green-native-builder' ),
							checked: attributes.showLangSwitcher,
							onChange: ( showLangSwitcher ) => setAttributes( { showLangSwitcher } ),
						} )
					)
				),
				el(
					'div',
					{ className: 'green-nb-preview green-nb-preview-site-header' },
					el( 'strong', null, __( 'Cabeçalho Green', 'green-native-builder' ) ),
					el( 'p', null, __( 'Configure logo, menu e idiomas no painel lateral. Use um único bloco deste tipo no cabeçalho global.', 'green-native-builder' ) )
				)
			);
		},
		save: dynamicSave,
	} );

	registerBlockType( 'green/hero-banner', {
		title: __( 'Green: Hero Banner', 'green-native-builder' ),
		icon: 'cover-image',
		category: 'design',
		supports: {
			anchor: true,
		},
		attributes: {
			backgroundUrl: { type: 'string', default: '' },
			badge: { type: 'string', default: 'TRADUÇÕES CORPORATIVAS DE ALTO NÍVEL' },
			title: { type: 'string', default: '' },
			subtitle: { type: 'string', default: '' },
			primaryButtonText: { type: 'string', default: '' },
			primaryButtonUrl: { type: 'string', default: '' },
			secondaryButtonText: { type: 'string', default: '' },
			secondaryButtonUrl: { type: 'string', default: '' },
		},
		edit: ( { attributes, setAttributes } ) =>
			el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Imagem de fundo', 'green-native-builder' ), initialOpen: true },
						el( MediaUploadCheck, null, el( MediaUpload, {
							onSelect: ( media ) => setAttributes( { backgroundUrl: media?.url || '' } ),
							allowedTypes: [ 'image' ],
							render: ( { open } ) =>
								el(
									Button,
									{ onClick: open, variant: 'secondary' },
									attributes.backgroundUrl
										? __( 'Trocar imagem', 'green-native-builder' )
										: __( 'Selecionar imagem', 'green-native-builder' )
								),
						} ) )
					),
					el(
						PanelBody,
						{ title: __( 'Conteúdo principal', 'green-native-builder' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Selo / Badge', 'green-native-builder' ),
							value: attributes.badge,
							onChange: ( badge ) => setAttributes( { badge } ),
						} ),
						el( TextControl, {
							label: __( 'Título', 'green-native-builder' ),
							value: attributes.title,
							onChange: ( title ) => setAttributes( { title } ),
						} ),
						el( TextareaControl, {
							label: __( 'Subtítulo', 'green-native-builder' ),
							value: attributes.subtitle,
							onChange: ( subtitle ) => setAttributes( { subtitle } ),
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Botão primário', 'green-native-builder' ), initialOpen: false },
						el( TextControl, {
							label: __( 'Texto', 'green-native-builder' ),
							value: attributes.primaryButtonText,
							onChange: ( primaryButtonText ) => setAttributes( { primaryButtonText } ),
						} ),
						el( URLInputButton, {
							url: attributes.primaryButtonUrl,
							onChange: ( primaryButtonUrl ) => setAttributes( { primaryButtonUrl } ),
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Botão secundário', 'green-native-builder' ), initialOpen: false },
						el( TextControl, {
							label: __( 'Texto', 'green-native-builder' ),
							value: attributes.secondaryButtonText,
							onChange: ( secondaryButtonText ) => setAttributes( { secondaryButtonText } ),
						} ),
						el( URLInputButton, {
							url: attributes.secondaryButtonUrl,
							onChange: ( secondaryButtonUrl ) => setAttributes( { secondaryButtonUrl } ),
						} )
					)
				),
				el(
					'div',
					{ className: 'green-nb-preview green-nb-preview-hero' },
					el( 'strong', null, attributes.title || __( 'Hero Banner', 'green-native-builder' ) ),
					el( 'p', null, attributes.subtitle || __( 'Edite no painel lateral.', 'green-native-builder' ) )
				)
			),
		save: dynamicSave,
	} );

	registerBlockType( 'green/highlight-cards', {
		title: __( 'Green: Cards de Destaque', 'green-native-builder' ),
		icon: 'grid-view',
		category: 'design',
		supports: {
			anchor: true,
		},
		attributes: {
			items: { type: 'array', default: [] },
		},
		edit: ( { attributes, setAttributes } ) => {
			const items = attributes.items || [];
			const onFieldChange = ( index, key, value ) => {
				setAttributes( { items: updateItemAt( items, index, { [ key ]: value } ) } );
			};
			const onItemsChange = ( nextItems ) => setAttributes( { items: nextItems } );

			const bulletsFromItem = ( item ) => {
				const b = item.bullets;
				if ( Array.isArray( b ) && b.length ) {
					return b.join( '\n' );
				}
				return '';
			};

			const onBulletsChange = ( index, text ) => {
				const lines = text
					.split( '\n' )
					.map( ( line ) => line.trim() )
					.filter( Boolean );
				onFieldChange( index, 'bullets', lines );
			};

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					items.map( ( item, index ) =>
						el(
							PanelBody,
							{
								key: `highlight-card-${ index }`,
								title: `${ __( 'Card', 'green-native-builder' ) } ${ index + 1 }${ item.title ? `: ${ item.title }` : '' }`,
								initialOpen: index === 0,
							},
							el( TextControl, {
								label: __( 'Ícone (Material Symbols)', 'green-native-builder' ),
								value: item.icon || '',
								onChange: ( value ) => onFieldChange( index, 'icon', value ),
							} ),
							el( TextControl, {
								label: __( 'Título', 'green-native-builder' ),
								value: item.title || '',
								onChange: ( value ) => onFieldChange( index, 'title', value ),
							} ),
							el( TextareaControl, {
								label: __( 'Descrição', 'green-native-builder' ),
								value: item.description || '',
								onChange: ( value ) => onFieldChange( index, 'description', value ),
							} ),
							el( TextareaControl, {
								label: __( 'Lista com marcadores (um texto por linha)', 'green-native-builder' ),
								help: __( 'Opcional. Se preencher, a lista substitui o link no front-end. Use várias linhas para vários itens.', 'green-native-builder' ),
								value: bulletsFromItem( item ),
								onChange: ( value ) => onBulletsChange( index, value ),
								rows: 6,
							} ),
							el( TextControl, {
								label: __( 'Texto do link (se não houver lista)', 'green-native-builder' ),
								value: item.linkText || '',
								onChange: ( value ) => onFieldChange( index, 'linkText', value ),
							} ),
							el( TextControl, {
								label: __( 'URL do link', 'green-native-builder' ),
								value: item.linkUrl || '',
								onChange: ( value ) => onFieldChange( index, 'linkUrl', value ),
							} ),
							el( TextControl, {
								label: __( 'Tom (light, primary, muted)', 'green-native-builder' ),
								value: item.tone || 'light',
								onChange: ( value ) => onFieldChange( index, 'tone', value ),
							} ),
							el(
								Button,
								{
									isDestructive: true,
									variant: 'secondary',
									onClick: () => onItemsChange( removeItemAt( items, index ) ),
								},
								__( 'Remover este card', 'green-native-builder' )
							)
						)
					),
					el(
						Button,
						{
							variant: 'primary',
							onClick: () =>
								onItemsChange(
									addItem( items, {
										icon: '',
										title: '',
										description: '',
										linkText: '',
										linkUrl: '',
										tone: 'light',
										bullets: [],
									} )
								),
						},
						__( 'Adicionar card', 'green-native-builder' )
					)
				),
				el(
					'div',
					{ className: 'green-nb-preview' },
					el( 'strong', null, __( 'Cards de destaque', 'green-native-builder' ) ),
					el( 'p', null, `${ items.length } ${ __( 'item(ns) configurado(s)', 'green-native-builder' ) }` )
				)
			);
		},
		save: dynamicSave,
	} );

	registerBlockType( 'green/areas-atuacao', {
		title: __( 'Green: Áreas de Atuação', 'green-native-builder' ),
		icon: 'columns',
		category: 'design',
		supports: {
			anchor: true,
		},
		attributes: {
			sectionTitle: { type: 'string', default: '' },
			sectionDescription: {
				type: 'string',
				default: 'A Green Associados atua em diversos segmentos do mercado, com especialização nas seguintes áreas:',
			},
			services: { type: 'array', default: [] },
		},
		edit: ( { attributes, setAttributes } ) => {
			const services = attributes.services || [];

			const patchService = ( index, patch ) => {
				const cur = services[ index ] || {};
				setAttributes( { services: updateItemAt( services, index, { ...cur, ...patch } ) } );
			};

			const emptyService = () => ( {
				icon: '',
				title: '',
				description: '',
				listLeft: [],
				listRight: [],
				subsections: [],
				tags: [],
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Cabeçalho da seção', 'green-native-builder' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Título', 'green-native-builder' ),
							value: attributes.sectionTitle,
							onChange: ( sectionTitle ) => setAttributes( { sectionTitle } ),
						} ),
						el( TextareaControl, {
							label: __( 'Descrição', 'green-native-builder' ),
							value: attributes.sectionDescription,
							onChange: ( sectionDescription ) => setAttributes( { sectionDescription } ),
						} )
					),
					services.map( ( service, index ) => {
						const subs = Array.isArray( service.subsections ) ? service.subsections : [];
						return el(
							PanelBody,
							{
								key: `area-service-${ index }`,
								title: `${ __( 'Serviço', 'green-native-builder' ) } ${ index + 1 }${ service.title ? `: ${ service.title }` : '' }`,
								initialOpen: index === 0,
							},
							el( TextControl, {
								label: __( 'Ícone (Material Symbols)', 'green-native-builder' ),
								value: service.icon || '',
								onChange: ( value ) => patchService( index, { icon: value } ),
							} ),
							el( TextControl, {
								label: __( 'Título do serviço', 'green-native-builder' ),
								value: service.title || '',
								onChange: ( value ) => patchService( index, { title: value } ),
							} ),
							el( TextareaControl, {
								label: __( 'Descrição', 'green-native-builder' ),
								value: service.description || '',
								onChange: ( value ) => patchService( index, { description: value } ),
								rows: 4,
							} ),
							el( TextareaControl, {
								label: __( 'Lista — coluna esquerda (uma linha por item)', 'green-native-builder' ),
								value: linesFromArray( service.listLeft ),
								onChange: ( value ) => patchService( index, { listLeft: arrayFromLines( value ) } ),
								rows: 6,
							} ),
							el( TextareaControl, {
								label: __( 'Lista — coluna direita (uma linha por item)', 'green-native-builder' ),
								value: linesFromArray( service.listRight ),
								onChange: ( value ) => patchService( index, { listRight: arrayFromLines( value ) } ),
								rows: 6,
							} ),
							el( 'p', { className: 'description', style: { marginTop: '12px' } }, __( 'Subsecções (substituem as duas colunas se preenchidas):', 'green-native-builder' ) ),
							subs.map( ( sub, subIdx ) => {
								const subSafe = sub && typeof sub === 'object' ? sub : { title: '', items: [] };
								return el(
									'div',
									{
										key: `sub-${ index }-${ subIdx }`,
										className: 'green-nb-repeater-item',
									},
									el( TextControl, {
										label: `${ __( 'Título da subsecção', 'green-native-builder' ) } ${ subIdx + 1 }`,
										value: subSafe.title || '',
										onChange: ( value ) => {
											const next = subs.map( ( s, i ) => {
												const base = s && typeof s === 'object' ? s : { title: '', items: [] };
												return i === subIdx ? { ...base, title: value } : base;
											} );
											patchService( index, { subsections: next } );
										},
									} ),
									el( TextareaControl, {
										label: __( 'Itens desta subsecção (uma linha por item)', 'green-native-builder' ),
										value: linesFromArray( Array.isArray( subSafe.items ) ? subSafe.items : [] ),
										onChange: ( value ) => {
											const items = arrayFromLines( value );
											const next = subs.map( ( s, i ) => {
												const base = s && typeof s === 'object' ? s : { title: '', items: [] };
												return i === subIdx ? { ...base, items } : base;
											} );
											patchService( index, { subsections: next } );
										},
										rows: 5,
									} ),
									el(
										Button,
										{
											isDestructive: true,
											variant: 'secondary',
											onClick: () => {
												const next = subs.filter( ( _, i ) => i !== subIdx );
												patchService( index, { subsections: next } );
											},
										},
										__( 'Remover esta subsecção', 'green-native-builder' )
									)
								);
							} ),
							el(
								Button,
								{
									variant: 'secondary',
									onClick: () =>
										patchService( index, {
											subsections: [ ...subs, { title: '', items: [] } ],
										} ),
								},
								__( 'Adicionar subsecção', 'green-native-builder' )
							),
							el( TextareaControl, {
								label: __( 'Tags (uma por linha; bloco jurídico)', 'green-native-builder' ),
								value: linesFromArray( service.tags ),
								onChange: ( value ) => patchService( index, { tags: arrayFromLines( value ) } ),
								rows: 4,
							} ),
							el(
								Button,
								{
									isDestructive: true,
									variant: 'secondary',
									onClick: () =>
										setAttributes( {
											services: removeItemAt( services, index ),
										} ),
								},
								__( 'Remover este serviço', 'green-native-builder' )
							)
						);
					} ),
					el(
						Button,
						{
							variant: 'primary',
							onClick: () =>
								setAttributes( {
									services: addItem( services, emptyService() ),
								} ),
						},
						__( 'Adicionar serviço', 'green-native-builder' )
					)
				),
				el(
					'div',
					{ className: 'green-nb-preview' },
					el( 'strong', null, attributes.sectionTitle || __( 'Áreas de Atuação', 'green-native-builder' ) ),
					el( 'p', null, `${ services.length } ${ __( 'serviço(s)', 'green-native-builder' ) }` )
				)
			);
		},
		save: dynamicSave,
	} );

	registerBlockType( 'green/ia-section', {
		title: __( 'Green: Seção de IA', 'green-native-builder' ),
		icon: 'cpu',
		category: 'design',
		supports: {
			anchor: true,
		},
		attributes: {
			label: { type: 'string', default: '' },
			title: { type: 'string', default: '' },
			paragraphs: { type: 'array', default: [] },
			features: { type: 'array', default: [] },
		},
		edit: ( { attributes, setAttributes } ) => {
			const paragraphs = attributes.paragraphs || [];
			const features = attributes.features || [];

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Textos principais', 'green-native-builder' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Rótulo', 'green-native-builder' ),
							value: attributes.label,
							onChange: ( label ) => setAttributes( { label } ),
						} ),
						el( TextControl, {
							label: __( 'Título', 'green-native-builder' ),
							value: attributes.title,
							onChange: ( title ) => setAttributes( { title } ),
						} ),
						paragraphs.map( ( paragraph, index ) =>
							el( TextareaControl, {
								key: `p-${ index }`,
								label: `${ __( 'Parágrafo', 'green-native-builder' ) } ${ index + 1 }`,
								value: paragraph,
								onChange: ( value ) => {
									const next = [ ...paragraphs ];
									next[ index ] = value;
									setAttributes( { paragraphs: next } );
								},
							} )
						),
						el(
							Button,
							{
								variant: 'secondary',
								onClick: () => setAttributes( { paragraphs: [ ...paragraphs, '' ] } ),
							},
							__( 'Adicionar parágrafo', 'green-native-builder' )
						)
					),
					repeaterControls(
						__( 'Cards laterais', 'green-native-builder' ),
						features,
						[
							{ key: 'icon', label: __( 'Ícone', 'green-native-builder' ) },
							{ key: 'title', label: __( 'Título', 'green-native-builder' ) },
							{ key: 'description', label: __( 'Descrição', 'green-native-builder' ) },
						],
						( index, key, value ) => setAttributes( { features: updateItemAt( features, index, { [ key ]: value } ) } ),
						( next ) => setAttributes( { features: next } ),
						__( 'Adicionar card lateral', 'green-native-builder' )
					)
				),
				el(
					'div',
					{ className: 'green-nb-preview' },
					el( 'strong', null, attributes.title || __( 'Seção de IA', 'green-native-builder' ) )
				)
			);
		},
		save: dynamicSave,
	} );

	registerBlockType( 'green/security-pillars', {
		title: __( 'Green: Segurança (Pilares)', 'green-native-builder' ),
		icon: 'shield',
		category: 'design',
		supports: {
			anchor: true,
		},
		attributes: {
			label: { type: 'string', default: '' },
			title: { type: 'string', default: '' },
			intro: { type: 'string', default: '' },
			paragraphs: { type: 'array', default: [] },
			pillars: { type: 'array', default: [] },
		},
		edit: ( { attributes, setAttributes } ) => {
			const paragraphs = attributes.paragraphs || [];
			const pillars = attributes.pillars || [];

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Introdução', 'green-native-builder' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Rótulo', 'green-native-builder' ),
							value: attributes.label,
							onChange: ( label ) => setAttributes( { label } ),
						} ),
						el( TextControl, {
							label: __( 'Título', 'green-native-builder' ),
							value: attributes.title,
							onChange: ( title ) => setAttributes( { title } ),
						} ),
						el( TextareaControl, {
							label: __( 'Texto de destaque', 'green-native-builder' ),
							value: attributes.intro,
							onChange: ( intro ) => setAttributes( { intro } ),
						} ),
						paragraphs.map( ( paragraph, index ) =>
							el( TextareaControl, {
								key: `security-p-${ index }`,
								label: `${ __( 'Parágrafo', 'green-native-builder' ) } ${ index + 1 }`,
								value: paragraph,
								onChange: ( value ) => {
									const next = [ ...paragraphs ];
									next[ index ] = value;
									setAttributes( { paragraphs: next } );
								},
							} )
						),
						el(
							Button,
							{
								variant: 'secondary',
								onClick: () => setAttributes( { paragraphs: [ ...paragraphs, '' ] } ),
							},
							__( 'Adicionar parágrafo', 'green-native-builder' )
						)
					),
					repeaterControls(
						__( 'Pilares de segurança', 'green-native-builder' ),
						pillars,
						[
							{ key: 'icon', label: __( 'Ícone', 'green-native-builder' ) },
							{ key: 'title', label: __( 'Título', 'green-native-builder' ) },
							{ key: 'description', label: __( 'Descrição', 'green-native-builder' ) },
						],
						( index, key, value ) => setAttributes( { pillars: updateItemAt( pillars, index, { [ key ]: value } ) } ),
						( next ) => setAttributes( { pillars: next } ),
						__( 'Adicionar pilar', 'green-native-builder' )
					)
				),
				el(
					'div',
					{ className: 'green-nb-preview' },
					el( 'strong', null, attributes.title || __( 'Segurança', 'green-native-builder' ) )
				)
			);
		},
		save: dynamicSave,
	} );

	registerBlockType( 'green/team-grid', {
		title: __( 'Green: Equipe', 'green-native-builder' ),
		icon: 'groups',
		category: 'design',
		supports: {
			anchor: true,
		},
		attributes: {
			title: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			members: { type: 'array', default: [] },
		},
		edit: ( { attributes, setAttributes } ) => {
			const members = attributes.members || [];

			const updateMembers = ( next ) => setAttributes( { members: next } );
			const patchMember = ( index, patch ) => {
				const cur = members[ index ] || {};
				setAttributes( { members: updateItemAt( members, index, { ...cur, ...patch } ) } );
			};

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Cabeçalho da equipe', 'green-native-builder' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Título', 'green-native-builder' ),
							value: attributes.title,
							onChange: ( title ) => setAttributes( { title } ),
						} ),
						el( TextareaControl, {
							label: __( 'Descrição', 'green-native-builder' ),
							value: attributes.description,
							onChange: ( description ) => setAttributes( { description } ),
						} )
					),
					members.map( ( member, index ) =>
						el(
							PanelBody,
							{ key: `member-${ index }`, title: `${ __( 'Pessoa', 'green-native-builder' ) } ${ index + 1 }`, initialOpen: false },
							el(
								MediaUploadCheck,
								null,
								el( MediaUpload, {
									onSelect: ( media ) =>
										patchMember( index, {
											photoId: media.id,
											photoUrl: media?.url || '',
										} ),
									allowedTypes: [ 'image' ],
									value: member.photoId,
									render: ( { open } ) =>
										el(
											'div',
											{ className: 'green-nb-media-row' },
											el(
												Button,
												{ variant: 'secondary', onClick: open },
												member.photoUrl || member.photoId
													? __( 'Trocar foto na biblioteca', 'green-native-builder' )
													: __( 'Enviar foto', 'green-native-builder' )
											),
											( member.photoUrl || member.photoId ) &&
												el(
													Button,
													{
														isDestructive: true,
														variant: 'link',
														onClick: () => patchMember( index, { photoId: 0, photoUrl: '' } ),
													},
													__( 'Remover', 'green-native-builder' )
												)
										),
								} )
							),
							member.photoUrl &&
								el( 'p', { className: 'description' }, __( 'Pré-visualização:', 'green-native-builder' ) ),
							member.photoUrl &&
								el( 'img', {
									src: member.photoUrl,
									alt: '',
									style: { maxWidth: '120px', height: 'auto', borderRadius: '999px' },
								} ),
							el( TextControl, {
								label: __( 'Nome', 'green-native-builder' ),
								value: member.name || '',
								onChange: ( name ) => patchMember( index, { name } ),
							} ),
							el( TextControl, {
								label: __( 'Cargo', 'green-native-builder' ),
								value: member.role || '',
								onChange: ( role ) => patchMember( index, { role } ),
							} ),
							el( TextControl, {
								label: __( 'LinkedIn (URL)', 'green-native-builder' ),
								value: member.linkedinUrl || '',
								onChange: ( linkedinUrl ) => patchMember( index, { linkedinUrl } ),
							} ),
							el(
								Button,
								{
									isDestructive: true,
									variant: 'secondary',
									onClick: () => updateMembers( removeItemAt( members, index ) ),
								},
								__( 'Remover pessoa', 'green-native-builder' )
							)
						)
					),
					el(
						PanelBody,
						{ title: __( 'Adicionar', 'green-native-builder' ), initialOpen: false },
						el(
							Button,
							{
								variant: 'primary',
								onClick: () => updateMembers( addItem( members, {} ) ),
							},
							__( 'Adicionar pessoa', 'green-native-builder' )
						)
					)
				),
				el(
					'div',
					{ className: 'green-nb-preview' },
					el( 'strong', null, attributes.title || __( 'Equipe', 'green-native-builder' ) ),
					el( 'p', null, `${ members.length } ${ __( 'pessoa(s)', 'green-native-builder' ) }` )
				)
			);
		},
		save: dynamicSave,
	} );

	const contactSectionAttributes = {
		title: { type: 'string', default: 'FALE CONOSCO' },
		description: { type: 'string', default: '' },
		phoneLabel: { type: 'string', default: '' },
		phone: { type: 'string', default: '' },
		emailLabel: { type: 'string', default: '' },
		email: { type: 'string', default: '' },
		addressLabel: { type: 'string', default: '' },
		address: { type: 'string', default: '' },
		whatsappText: { type: 'string', default: '' },
		whatsappUrl: { type: 'string', default: '' },
	};

	function contactSectionEdit( props ) {
		const { attributes, setAttributes } = props;
		return el(
			Fragment,
			null,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Contato', 'green-native-builder' ), initialOpen: true },
					el( TextControl, { label: __( 'Título', 'green-native-builder' ), value: attributes.title, onChange: ( title ) => setAttributes( { title } ) } ),
					el( TextareaControl, { label: __( 'Descrição', 'green-native-builder' ), value: attributes.description, onChange: ( description ) => setAttributes( { description } ) } ),
					el( TextControl, { label: __( 'Rótulo do telefone', 'green-native-builder' ), value: attributes.phoneLabel, onChange: ( phoneLabel ) => setAttributes( { phoneLabel } ) } ),
					el( TextControl, { label: __( 'Telefone', 'green-native-builder' ), value: attributes.phone, onChange: ( phone ) => setAttributes( { phone } ) } ),
					el( TextControl, { label: __( 'Rótulo do e-mail', 'green-native-builder' ), value: attributes.emailLabel, onChange: ( emailLabel ) => setAttributes( { emailLabel } ) } ),
					el( TextControl, { label: __( 'E-mail', 'green-native-builder' ), value: attributes.email, onChange: ( email ) => setAttributes( { email } ) } ),
					el( TextControl, { label: __( 'Rótulo do endereço', 'green-native-builder' ), value: attributes.addressLabel, onChange: ( addressLabel ) => setAttributes( { addressLabel } ) } ),
					el( TextareaControl, { label: __( 'Endereço', 'green-native-builder' ), value: attributes.address, onChange: ( address ) => setAttributes( { address } ) } ),
					el( TextControl, { label: __( 'Texto do botão WhatsApp', 'green-native-builder' ), value: attributes.whatsappText, onChange: ( whatsappText ) => setAttributes( { whatsappText } ) } ),
					el( URLInputButton, { url: attributes.whatsappUrl, onChange: ( whatsappUrl ) => setAttributes( { whatsappUrl } ) } )
				)
			),
			el(
				'div',
				{ className: 'green-nb-preview' },
				el( 'strong', null, attributes.title || __( 'Contato', 'green-native-builder' ) )
			)
		);
	}

	registerBlockType( 'green/contact-section', {
		title: __( 'Green: Contato', 'green-native-builder' ),
		icon: 'email',
		category: 'design',
		supports: {
			anchor: true,
		},
		attributes: contactSectionAttributes,
		edit: contactSectionEdit,
		save: dynamicSave,
	} );

	registerBlockType( 'green/contact-footer', {
		title: __( 'Green: Contato (legado)', 'green-native-builder' ),
		icon: 'email',
		category: 'design',
		attributes: {
			...contactSectionAttributes,
			footerAbout: { type: 'string', default: '' },
			legalItems: { type: 'array', default: [] },
			copyright: { type: 'string', default: '' },
			credit: { type: 'string', default: '' },
		},
		supports: {
			anchor: true,
			inserter: false,
		},
		edit: contactSectionEdit,
		save: dynamicSave,
	} );
} )( window.wp );

