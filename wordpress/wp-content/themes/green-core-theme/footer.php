<?php
/**
 * Footer template.
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$green_footer_post     = function_exists( 'green_core_theme_get_site_part_post' ) ? green_core_theme_get_site_part_post( 'footer' ) : null;
$green_has_footer_page = $green_footer_post && trim( (string) $green_footer_post->post_content ) !== '';
?>
<footer class="green-site-footer green-section">
	<div class="green-container green-footer-shell">
		<?php if ( $green_has_footer_page ) : ?>
			<div class="green-footer-from-page">
				<?php echo apply_filters( 'the_content', $green_footer_post->post_content ); ?>
			</div>
		<?php else : ?>
			<div class="green-footer-meta">
				<span><?php echo esc_html( sprintf( '© %s %s', gmdate( 'Y' ), get_bloginfo( 'name' ) ) ); ?></span>
				<span><?php esc_html_e( 'Plataforma proprietária Green Native Builder', 'green-core-theme' ); ?></span>
			</div>
		<?php endif; ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

