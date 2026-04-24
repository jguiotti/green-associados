<?php
/**
 * Main template fallback.
 *
 * @package GreenCoreTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="green-site-main green-section green-site-main--inner">
	<div class="green-container">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		}
		?>
	</div>
</main>
<?php
get_footer();

