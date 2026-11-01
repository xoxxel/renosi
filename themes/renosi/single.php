<?php
/**
 * Single post.
 *
 * @package renosi
 */

get_header();
?>
<div class="container page-shell">
	<?php renosi_breadcrumbs(); ?>
	<div class="content-layout">
		<main id="primary" class="site-main">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/single' );

				the_post_navigation(
					array(
						'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'نوشته‌ی قبلی', 'renosi' ) . '</span><span class="nav-title">%title</span>',
						'next_text' => '<span class="nav-subtitle">' . esc_html__( 'نوشته‌ی بعدی', 'renosi' ) . '</span><span class="nav-title">%title</span>',
					)
				);

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</main>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
