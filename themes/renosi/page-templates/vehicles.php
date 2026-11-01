<?php
/**
 * Template Name: برندهای خودرو
 * Template Post Type: page
 *
 * Lists every vehicle brand with its models (internal-linking hub for SEO).
 *
 * @package renosi
 */

get_header();
$renosi_tree = function_exists( 'renosi_get_vehicle_tree' ) ? renosi_get_vehicle_tree() : array();
?>
<div class="container page-shell">
	<?php renosi_breadcrumbs(); ?>
	<main id="primary" class="site-main">
		<?php while ( have_posts() ) : the_post(); ?>
			<header class="archive-hero">
				<div class="archive-hero__body">
					<p class="eyebrow"><?php esc_html_e( 'انتخاب بر اساس خودرو', 'renosi' ); ?></p>
					<h1 class="archive-hero__title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="archive-hero__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php else : ?>
						<p class="archive-hero__text"><?php esc_html_e( 'برند و مدل خودروی خود را انتخاب کنید تا فقط قطعات سازگار با آن را ببینید.', 'renosi' ); ?></p>
					<?php endif; ?>
				</div>
			</header>

			<?php if ( $renosi_tree ) : ?>
				<?php renosi_vehicle_brand_grid(); ?>

				<div class="vehicle-directory">
					<?php foreach ( $renosi_tree as $renosi_node ) : ?>
						<section class="vehicle-directory__group" id="brand-<?php echo esc_attr( $renosi_node['term']->slug ); ?>">
							<div class="vehicle-directory__title">
								<span class="vehicle-logo vehicle-logo--sm" aria-hidden="true"><?php echo renosi_vehicle_logo( $renosi_node['term'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<h2><a href="<?php echo esc_url( get_term_link( $renosi_node['term'] ) ); ?>"><?php echo esc_html( renosi_vehicle_title( $renosi_node['term'] ) ); ?></a></h2>
							</div>
							<?php if ( $renosi_node['models'] ) : ?>
								<ul class="chip-list">
									<?php foreach ( $renosi_node['models'] as $renosi_model ) : ?>
										<li><a class="chip" href="<?php echo esc_url( get_term_link( $renosi_model ) ); ?>"><?php echo esc_html( $renosi_model->name ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</section>
					<?php endforeach; ?>
				</div>
			<?php elseif ( current_user_can( 'manage_product_terms' ) ) : ?>
				<p class="finder__hint"><a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=renosi_vehicle&post_type=product' ) ); ?>"><?php esc_html_e( 'هنوز خودرویی تعریف نشده است — افزودن برند و مدل', 'renosi' ); ?></a></p>
			<?php endif; ?>

			<?php if ( '' !== trim( get_the_content() ) ) : ?>
				<div class="seo-content entry-content"><?php the_content(); ?></div>
			<?php endif; ?>
		<?php endwhile; ?>
	</main>
</div>
<?php
get_footer();
