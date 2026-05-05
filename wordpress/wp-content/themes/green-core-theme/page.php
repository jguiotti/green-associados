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
<main class="green-site-main green-section <?php echo $green_is_front_onepage ? 'green-site-main--home' : '!m-0 !p-0'; ?>">
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
		<div class="green-page-inner mx-auto w-full max-w-4xl px-8 pb-36 pt-36 text-slate-800 md:pb-44 md:pt-44">
			<?php
			while ( have_posts() ) {
				the_post();
				the_title( '<h1>', '</h1>' );
				echo '<div class="entry-content max-w-none space-y-4 text-base leading-relaxed text-slate-800 [&_a]:text-primary [&_a:hover]:text-[#33c4e0] [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-bold [&_ul]:list-disc [&_ul]:pl-6">';
				the_content();
				echo '</div>';
			}
			?>
		</div>
	<?php endif; ?>
</main>
<?php
get_footer();

