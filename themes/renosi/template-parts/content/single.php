<?php
/**
 * Single post content.
 *
 * @package renosi
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<header class="entry-header">
		<?php
		$renosi_cats = get_the_category();
		if ( $renosi_cats ) {
			echo '<a class="eyebrow" href="' . esc_url( get_category_link( $renosi_cats[0] ) ) . '">' . esc_html( $renosi_cats[0]->name ) . '</a>';
		}
		the_title( '<h1 class="entry-title">', '</h1>' );
		?>
		<div class="entry-meta"><?php renosi_posted_on(); ?><?php renosi_posted_by(); ?></div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'صفحه‌ها:', 'renosi' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<footer class="entry-footer"><?php renosi_entry_footer(); ?></footer>
</article>
