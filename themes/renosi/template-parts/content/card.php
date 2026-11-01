<?php
/**
 * Post card used in archives and the home page.
 *
 * @package renosi
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'renosi-card', array( 'alt' => '' ) );
		} else {
			echo '<span class="post-card__placeholder">' . renosi_icon( 'wrench', 36 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</a>
	<div class="post-card__body">
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta"><?php renosi_posted_on(); ?></div>
		<?php endif; ?>
		<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20, '…' ) ); ?></p>
	</div>
</article>
