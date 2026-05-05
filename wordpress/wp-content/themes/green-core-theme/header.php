<?php
/**
 * Header template.
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$green_header_post      = function_exists( 'green_core_theme_get_site_part_post' ) ? green_core_theme_get_site_part_post( 'header' ) : null;
$green_use_block_header = $green_header_post && trim( (string) $green_header_post->post_content ) !== '';
?>
<?php if ( $green_use_block_header ) : ?>
<header id="green-site-header" class="green-site-header green-site-header--blocks green-section">
	<div class="green-container green-header-blocks-wrap">
		<?php echo apply_filters( 'the_content', $green_header_post->post_content ); ?>
	</div>
</header>
<?php else : ?>
<header id="green-site-header" class="green-site-header green-section">
	<div class="green-container green-header-inner">
		<div class="green-header-logo">
			<a href="<?php echo esc_url( function_exists( 'green_core_theme_logo_home_url' ) ? green_core_theme_logo_home_url() : home_url( '/' ) ); ?>" class="green-header-logo-link" aria-label="<?php esc_attr_e( 'Página inicial', 'green-core-theme' ); ?>">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					echo '<span>' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
				}
				?>
			</a>
		</div>
		<nav id="green-primary-nav" class="green-header-nav" aria-label="<?php esc_attr_e( 'Navegação principal', 'green-core-theme' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
		<div class="green-header-end">
			<div class="green-header-lang" aria-label="<?php esc_attr_e( 'Seletor de idioma', 'green-core-theme' ); ?>">
				<?php
				if ( function_exists( 'green_core_theme_render_language_switcher' ) ) {
					green_core_theme_render_language_switcher();
				}
				?>
			</div>
			<button type="button" class="green-header-menu-toggle" aria-expanded="false" aria-controls="green-primary-nav" id="green-menu-toggle">
				<span class="green-header-menu-toggle-bars" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Abrir menu', 'green-core-theme' ); ?></span>
			</button>
		</div>
	</div>
</header>
<?php endif; ?>

