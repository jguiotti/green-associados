<?php
/**
 * Definições de blocos para migração assistida da Homepage (conteúdo alinhado ao layout aprovado).
 *
 * @package GreenNativeBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blocos serializáveis para a página inicial (uso com serialize_blocks).
 *
 * @return array<int, array<string, mixed>>
 */
function green_nb_get_homepage_migration_blocks() {
	$hero_bg = 'https://lh3.googleusercontent.com/aida/ADBb0uhaZlSafcU11hflbYOHwYTuyCqzobvu02gg8epWWW0aohsDwJjIdl41bmM5xs6Oog4uaB3WBUfAlVAJ2GDZb-k8Rdhx8aCdBhjNr-vTumrVrYsaw-vEstAlhAy52DcC4hpUKOMWmzYFKVR1RLF6iAp5MQXrXPs674l4R5WvJKoYO3zrOhPoKttVT-5OSfl87b8nzXe6avd_Ni38seHoNy7oPYMLRRyUzXyYAmqfPcElkJwptyoY1-o6mRLuUwoAE5UD13o3heNz2bA';

	return array(
		array(
			'blockName'    => 'green/hero-banner',
			'attrs'        => array(
				'backgroundUrl'       => $hero_bg,
				'badge'               => 'Traduções Corporativas de Elite',
				'title'               => 'Assim como você, nós também percorremos um longo caminho para chegar aonde estamos.',
				'subtitle'            => 'Criada por associados com mais de 20 anos de atuação em traduções financeiras e corporativas para as principais companhias brasileiras, a Green Associados possui a experiência e o conhecimento necessários para que a sua mensagem seja interpretada corretamente em qualquer parte do mundo.',
				'primaryButtonText'   => 'Fale Conosco',
				'primaryButtonUrl'    => '#contato',
				'secondaryButtonText' => 'Nossos Serviços',
				'secondaryButtonUrl'  => '#atuacao',
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
		array(
			'blockName'    => 'green/highlight-cards',
			'attrs'        => array(
				'items' => array(
					array(
						'icon'        => 'translate',
						'title'       => 'ÁREAS DE ATUAÇÃO',
						'description' => 'Prestamos serviços especializados de tradução, revisão e também de pós-edição de conteúdos traduzidos por IA, para as mais diversas áreas, nos idiomas português, inglês e espanhol.',
						'linkText'    => 'Saiba mais aqui',
						'linkUrl'     => '#atuacao',
						'tone'        => 'light',
					),
					array(
						'icon'        => 'groups',
						'title'       => 'NOSSA EQUIPE',
						'description' => 'Somos especialistas no mercado financeiro e de negócios, com mais de 20 anos de experiência.',
						'linkText'    => 'Conheça nosso time',
						'linkUrl'     => '#equipe',
						'tone'        => 'primary',
					),
					array(
						'icon'        => 'verified_user',
						'title'       => 'DIFERENCIAIS',
						'description' => '',
						'linkText'    => '',
						'linkUrl'     => '',
						'tone'        => 'muted',
						'bullets'     => array(
							'Alta disponibilidade e rápido atendimento',
							'Traduções que combinam IA + revisão humana',
							'Time pequeno e especializado, garantindo uniformidade, consistência e atenção a cada detalhe',
							'Confidencialidade e Firewall físico',
						),
					),
				),
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
		array(
			'blockName'    => 'green/areas-atuacao',
			'attrs'        => array(
				'sectionTitle'       => 'ÁREAS DE ATUAÇÃO',
				'sectionDescription' => 'A equipe da Green Associados atua há mais de 20 anos em diversos segmentos do mercado, sendo especialista nas seguintes áreas:',
				'services'           => array(
					array(
						'icon'        => 'corporate_fare',
						'title'       => 'Comunicação Corporativa',
						'description' => 'Nossos serviços profissionais são respaldados por knowledge e experiência, a fim de garantir que a transmissão de informações para seus stakeholders globais seja clara e precisa, colaborando na construção da reputação de sua empresa.',
						'listLeft'    => array(
							'Balanços sociais',
							'Websites',
							'Press releases',
							'Newsletters',
							'Folders',
							'Acordos e contratos',
						),
						'listRight'   => array(
							'Códigos de conduta ética',
							'Políticas de divulgação e negociação',
							'Relatórios de avaliação e de rating',
							'Relatórios anuais e de sustentabilidade',
							'Materiais de comunicação para eventos',
							'Materiais de comunicação interna',
						),
					),
					array(
						'icon'          => 'query_stats',
						'title'         => 'Relações com Investidores',
						'description'   => 'Nossa comprovada experiência e longa especialização no mercado de RI são componentes essenciais para ajudá-lo a alavancar o potencial de sua base acionária fora do Brasil.',
						'subsections'   => array(
							array(
								'title' => 'Divulgação de resultados trimestrais',
								'items' => array(
									'Releases de resultados',
									'Discursos para teleconferência',
									'Apresentações',
									'Transcrições',
								),
							),
							array(
								'title' => 'Documentos diversos',
								'items' => array(
									'Comunicados ao mercado',
									'Fatos relevantes',
									'Atas de assembleias gerais e reuniões',
									'Apresentações para road shows',
									'Fact sheets',
								),
							),
						),
					),
					array(
						'icon'        => 'account_balance',
						'title'       => 'Financeira (documentos exigidos pela CVM e SEC)',
						'description' => 'Nossa equipe possui extenso conhecimento de práticas contábeis e exigências regulatórias e de governança.',
						'listLeft'    => array(
							'Informações Trimestrais (ITR)',
							'Demonstrações Padronizadas (DFP)',
							'Formulários de Referência',
							'Demonstrações financeiras e balanços contábeis',
						),
						'listRight'   => array(
							'Parecer dos auditores',
							'Formulários 20-F, 10-K e 8-K (SEC)',
							'Manuais para participação em assembleias gerais',
						),
					),
					array(
						'icon'        => 'gavel',
						'title'       => 'Jurídica',
						'description' => 'No universo jurídico, cada termo importa — e qualquer imprecisão pode alterar o sentido de cláusulas e comprometer resultados. Por isso, nossas traduções jurídicas são realizadas por profissionais especializados, com profundo entendimento de conceitos legais e terminologia específica de cada jurisdição.',
						'tags'        => array(
							'Procurações',
							'Atas de assembleias',
							'Contratos',
							'Pareceres jurídicos',
						),
					),
				),
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
		array(
			'blockName'    => 'green/ia-section',
			'attrs'        => array(
				'label'      => 'Tecnologia & Inovação',
				'title'      => 'INTELIGÊNCIA ARTIFICIAL',
				'paragraphs' => array(
					'Para seguir as últimas tecnologias aplicadas ao mercado linguístico e otimizar o tempo e os custos de nossos clientes, atualmente utilizamos na Green Associados uma ferramenta desenvolvida por e para tradutores, que combina Inteligência Artificial (IA) com os glossários e memórias de tradução desenvolvidos por nós, específicos para cada cliente atendido. Assim, é possível gerar conteúdos de qualidade superior, que passam por revisão e edição cuidadosa, humana e especializada de nossos sócios.',
					'Além disso, desenvolvemos técnicas para revisões e pós-edição profissional de conteúdos traduzidos por IA gerados pelos clientes, corrigindo inconsistências, aprimorando o estilo e garantindo conformidade terminológica.',
					'Desta forma, é possível obter o melhor equilíbrio entre eficiência, qualidade e economia — com textos finalizados prontos para publicação e alinhados às expectativas do público global.',
				),
				'features'   => array(
					array(
						'icon'        => 'memory',
						'title'       => 'Processamento Híbrido',
						'description' => 'Velocidade tecnológica aliada ao discernimento humano.',
					),
					array(
						'icon'        => 'verified',
						'title'       => 'Curadoria de Especialistas',
						'description' => 'Revisão por tradutores com mais de 20 anos de mercado.',
					),
				),
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
		array(
			'blockName'    => 'green/security-pillars',
			'attrs'        => array(
				'label'      => 'Protocolos Rigorosos',
				'title'      => 'SEGURANÇA',
				'intro'      => 'A Green Associados está ciente da imensa importância de se manter a confidencialidade e a integridade das informações recebidas de seus clientes.',
				'paragraphs' => array(
					'Por isso, todos os nossos processos foram desenvolvidos para este fim, com destaque para a utilização de infraestrutura de firewall física e independente em nossos servidores e da ferramenta de criptografia de e-mail Microsoft Exchange Hosted Encryption® em todas as mensagens de e-mail enviadas.',
					'A ferramenta utilizada em todas as traduções foi desenvolvida para garantir confidencialidade e segurança – por meio de criptografia, controles de acesso e armazenamento seguro–, atestada pela certificação SOC 2 Type II.',
					'Além disso, todos os membros da equipe e eventuais fornecedores que possam ter acesso a informações confidenciais possuem contrato de confidencialidade assinado com a Green Associados.',
				),
				'pillars'    => array(
					array(
						'icon'        => 'dns',
						'title'       => 'Firewall independente',
						'description' => 'Infraestrutura física dedicada para proteção máxima de dados em nossos servidores.',
					),
					array(
						'icon'        => 'encrypted',
						'title'       => 'E-mail e conteúdos criptografados',
						'description' => 'Utilização de Microsoft Exchange Hosted Encryption® em todas as comunicações.',
					),
					array(
						'icon'        => 'psychology_alt',
						'title'       => 'Uso seguro da IA',
						'description' => 'Tecnologia com certificação SOC 2 Type II para garantir privacidade total dos projetos.',
					),
					array(
						'icon'        => 'description',
						'title'       => 'Contrato de confidencialidade',
						'description' => 'Todos os envolvidos possuem NDAs assinados legalmente para sua tranquilidade.',
					),
				),
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
		array(
			'blockName'    => 'green/team-grid',
			'attrs'        => array(
				'title'       => 'NOSSA EQUIPE',
				'description' => 'Na Green Associados, nosso compromisso é garantir que sua comunicação internacional seja sempre segura, precisa e confiável. Aqui, temos paixão pelo que fazemos e traduzimos e revisamos seus documentos com cuidado e excelência.',
				'members'     => array(
					array(
						'name'         => 'RENATA SILVERIO BALBINO',
						'role'         => 'Socia e tradutora',
						'photoUrl'     => 'https://lh3.googleusercontent.com/aida/ADBb0ugN-XpFBfQU6KkH0PpwblKpRVejCN8fsgp_uc-aqAjkpCpAHtl5fq6SzNkzRzufo1v0zVK1rPxB5BYpev09lf3EbyKLClg_uxlCwO4uyJ5qIgxBVJKwdbaB-Uuy-OZ3oNJtIFuTUwru1m_VaumChNNIvCYEwzjw46lYD-908tr85im5EXuhCwVeBMl-MiOj7OGuvjupyLUFEKhZdjgzxKcIhG9v_POspfYUWnp0ki3und4Z-7CImPgHWLchkNkM45mCh1WXXOVDw-k',
						'linkedinUrl'  => '',
					),
					array(
						'name'         => 'SAMANTA BASTOS ALCARDO',
						'role'         => 'Socia e tradutora',
						'photoUrl'     => 'https://lh3.googleusercontent.com/aida/ADBb0uifpZ9SqtkTUdcmsPtexqIMez5tac1qpL88mEsWbyg-xpkeb4y0LFAt6elPu3xJ1Aq9AwEMFl_DHPso71qOokhnm-lRGT5ZP_neWrApambNyddzti-gnMSKhTajfzHsACo4YCgQEE0EracgmnDCnmn-y5763HVusnXHyu24XzgnLKpVyM7kMUS7IH2LfWH1qZSeDCLCUmmaxMApVVvBp9lf1CQae7rwBHOGhJShZnnt2HBq8ocEa-_14NIpjHWLYyMEoQ67x1XvHdY',
						'linkedinUrl'  => '',
					),
					array(
						'name'         => 'MELISANDE MANCUSI',
						'role'         => 'Socia e tradutora',
						'photoUrl'     => 'https://lh3.googleusercontent.com/aida/ADBb0uiDDuj2gHjL0Teoc1-mKYqwWRCsq_p9nN4T3HrMrjU7NaWQ2ZUBeT5GkHv97CaneEof-kmVc-wp-iFD360VYs1GQ42fy4PsJtSujkHv9ibW8Iyxa9JBL9Ysw26mVN3x-t--ZPUvJk7SitP9tQak3x-sJIgG6VJ0I5eofGVyoZPy3zDtqHRlMIXNJsavSc0oZFKEHDXERfzWd4SlF8n8qRX27JPmlMnskREkvvaOKqBRiFdBscgvuH3rUhE34iwnjzyLcoxsvMWhLQ',
						'linkedinUrl'  => '',
					),
					array(
						'name'         => 'VANESSA MELHADO',
						'role'         => 'Sócia, gestora de projetos e da parte administrativa',
						'photoUrl'     => 'https://lh3.googleusercontent.com/aida/ADBb0uhUTppolMY-9gZoKWCTHVR_2kjWiDs3hvGUasW5JmETboDGnOHe0NUSgo3qAGlDLMTq2Ct1_jVUcJBGVo0MFivfaQn2xYJSpq3nI9QPcQYouXI05JnV5pUwI8x97WCvpyFhw0kZUxHnIOIHJD42CF_5c5JEKoCjmG3NKjtvZEldM_iF5z1jSe4FDM19WK-yLEpJPv6U3A4pLKIlWcqk_o7ODTlPCQcGxvjafLg93e0BYR5MxrjAH9obwGjP9QEmUtfXeiJB-hdedwA',
						'linkedinUrl'  => '',
					),
					array(
						'name'         => 'DIOGO HOHL ORSI',
						'role'         => 'Cofundador, sócio e gestor da parte financeira',
						'photoUrl'     => 'https://lh3.googleusercontent.com/aida/ADBb0ugmom9e4FyJhjdvpr2VNibddXgtpo0_NAsmqCBeqNAZWhC9OW233qlmgqkzWQoaiD46oB_P2uC1qTkVGcBkAS0wm-otFfbnvePmmZc3AA-LSLfBXEAyo4uFBlfoWscsTEN-xI0iV7oXetaC7GLO-T_CDLDVR8a-SgFL9FVZFIG-t19zg5yrlCIUFWajZeZh_g3sJF0Cs9tY-liv9hp5trWKBxNb2CTATPIXhe39_X7xFjBaNS0G86zeKNLkDx2KuKGdvCYzJOYhTw',
						'linkedinUrl'  => '',
					),
					array(
						'name'         => 'MICHAEL JOSEPH FINHANE GREEN',
						'role'         => 'Fundador e sócio majoritário',
						'photoUrl'     => 'https://lh3.googleusercontent.com/aida/ADBb0uh3WZT0eiPR1a-oHFZQAGo-O2xKbQTYjZGOZH_kK3NBDS7WKiQyk21ssGybs9_VVi3OfBgb4QnIlENbtajFa1oKMYZljGnYkWtUqXT4CsYnQ9x0sAdwkECRulkoL7VZTyfjOIyRUM7uqPXqLIvSAGR6KUnv35zePfskDFF6488abwyS9jmt3hY5kSU7U8NrqnImLFFWDF9Mv92mAuyiUruAt-YTLk1BsPfr57t0LhlwvyoLWVltRDF775pFl9NlImKfDra9BD8pocM',
						'linkedinUrl'  => '',
					),
				),
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
		array(
			'blockName'    => 'green/contact-section',
			'attrs'        => array(
				'title'        => 'FALE COM NOSSOS ESPECIALISTAS',
				'description'  => 'Fale com nossos especialistas e eleve a qualidade da sua comunicação global com tradução profissional, segura e técnica.',
				'phoneLabel'   => 'Telefone',
				'phone'        => '55 11 3812-8780',
				'emailLabel'   => 'E-mail',
				'email'        => 'traducao@greenassociados.com.br',
				'addressLabel' => 'Endereço',
				'address'      => "Avenida Pedroso de Morais, 631 – cj 107\nPinheiros – São Paulo/SP",
				'whatsappText' => 'WhatsApp',
				'whatsappUrl'  => 'https://wa.me/551138128780',
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
	);
}

/**
 * Aplica o conteúdo migrado a um post de página.
 *
 * @param int $post_id ID do post.
 * @return bool|WP_Error
 */
function green_nb_apply_homepage_migration( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return new WP_Error( 'green_nb_invalid_post', 'ID de post inválido.' );
	}

	if ( ! function_exists( 'serialize_blocks' ) ) {
		return new WP_Error( 'green_nb_no_blocks', 'serialize_blocks não disponível.' );
	}

	$blocks  = green_nb_get_homepage_migration_blocks();
	$content = serialize_blocks( $blocks );

	$updated = wp_update_post(
		array(
			'ID'           => $post_id,
			'post_content' => $content,
		),
		true
	);

	if ( is_wp_error( $updated ) ) {
		return $updated;
	}

	update_post_meta( $post_id, '_wp_page_template', 'default' );

	return true;
}
