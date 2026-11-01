<?php
/**
 * Main template: blog index and fallback.
 *
 * @package renosi
 */

get_header();
?>
<div class="container page-shell">
	<?php renosi_breadcrumbs(); ?>
	<header class="page-header">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		<?php elseif ( is_archive() ) : ?>
			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		<?php elseif ( is_search() ) : ?>
			<h1 class="page-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'نتایج جستجو برای «%s»', 'renosi' ), esc_html( get_search_query() ) );
				?>
			</h1>
		<?php else : ?>
			<h1 class="page-title"><?php esc_html_e( 'وبلاگ', 'renosi' ); ?></h1>
		<?php endif; ?>
	</header>

	<div class="content-layout">
		<main id="primary" class="site-main">
			<?php if ( have_posts() ) : ?>
				<div class="post-grid">
					<?php
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/content/card' );
					}
					?>
				</div>
				<?php renosi_pagination(); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content/none' ); ?>
			<?php endif; ?>
		</main>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
