<?php
/**
 * Page template.
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<?php
$green_is_front_onepage = is_front_page() || is_page( array( 'homepage', 'Homepage' ) );
?>
<main class="green-site-main green-section <?php echo $green_is_front_onepage ? 'green-site-main--home' : 'green-site-main--inner'; ?>">
	<?php if ( $green_is_front_onepage ) : ?>
		<div class="green-section">
			<?php
			while ( have_posts() ) {
				the_post();
				the_content();
			}
			?>
		</div>
	<?php else : ?>
		<div class="green-container">
			<?php
			while ( have_posts() ) {
				the_post();
				the_title( '<h1>', '</h1>' );
				the_content();
			}
			?>
		</div>
	<?php endif; ?>
</main>
<?php
get_footer();

