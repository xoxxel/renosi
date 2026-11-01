<?php
/**
 * Template Name: تمام عرض (بدون عنوان)
 * Template Post Type: page
 *
 * Full-width canvas for block-editor landing pages.
 *
 * @package renosi
 */

get_header();
?>
<main id="primary" class="site-main site-main--canvas">
	<?php
	while ( have_posts() ) :
		the_post();
		echo '<div class="entry-content entry-content--canvas">';
		the_content();
		echo '</div>';
	endwhile;
	?>
</main>
<?php
get_footer();
