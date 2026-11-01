<?php
/**
 * Page content.
 *
 * @package renosi
 */

$renosi_is_wc_page = function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
	<header class="entry-header<?php echo $renosi_is_wc_page ? ' entry-header--compact' : ''; ?>">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header>

	<?php if ( has_post_thumbnail() && ! $renosi_is_wc_page ) : ?>
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

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php edit_post_link( esc_html__( 'ویرایش برگه', 'renosi' ), '<span class="edit-link">', '</span>' ); ?>
		</footer>
	<?php endif; ?>
</article>
