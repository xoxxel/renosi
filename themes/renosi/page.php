<?php
/**
 * Page.
 *
 * @package renosi
 */

get_header();
$renosi_wide = function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() );
?>
<div class="container page-shell">
	<?php renosi_breadcrumbs(); ?>
	<main id="primary" class="site-main<?php echo $renosi_wide ? ' site-main--wide' : ' site-main--narrow'; ?>">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/page' );
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</main>
</div>
<?php
get_footer();
