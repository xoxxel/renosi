<?php
/**
 * Home: latest posts.
 *
 * @package renosi
 */

$renosi_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
if ( ! $renosi_posts->have_posts() ) {
	return;
}
$renosi_blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : '';
?>
<section class="section">
	<div class="container">
		<?php renosi_section_head( renosi_opt( 'home_blog_title' ), $renosi_blog_url ); ?>
		<div class="post-grid">
			<?php
			while ( $renosi_posts->have_posts() ) {
				$renosi_posts->the_post();
				get_template_part( 'template-parts/content/card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
