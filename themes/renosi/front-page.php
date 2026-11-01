<?php
/**
 * Front page: store home (used for both "latest posts" and "static page" settings).
 *
 * @package renosi
 */

get_header();
$renosi_has_wc = class_exists( 'WooCommerce' );
?>
<main id="primary" class="site-main home">
	<?php
	if ( renosi_opt( 'hero_show' ) ) {
		get_template_part( 'template-parts/home/hero' );
	}

	if ( renosi_opt( 'home_features' ) ) {
		get_template_part( 'template-parts/home/features' );
	}

	if ( $renosi_has_wc && renosi_opt( 'home_vehicles' ) ) {
		get_template_part( 'template-parts/home/vehicles' );
	}

	if ( $renosi_has_wc && renosi_opt( 'home_categories' ) ) {
		get_template_part( 'template-parts/home/categories' );
	}

	if ( $renosi_has_wc ) {
		$renosi_count = max( 4, min( 24, (int) renosi_opt( 'home_products_count' ) ) );
		$renosi_shop  = wc_get_page_permalink( 'shop' );

		if ( renosi_opt( 'home_sale' ) ) {
			get_template_part(
				'template-parts/home/products',
				null,
				array(
					'title' => renosi_opt( 'home_sale_title' ),
					'atts'  => 'limit="' . $renosi_count . '" columns="4" on_sale="true" orderby="date" order="DESC"',
					'url'   => add_query_arg( 'orderby', 'date', $renosi_shop ),
					'class' => 'home-products--sale',
				)
			);
		}
		if ( renosi_opt( 'home_new' ) ) {
			get_template_part(
				'template-parts/home/products',
				null,
				array(
					'title' => renosi_opt( 'home_new_title' ),
					'atts'  => 'limit="' . $renosi_count . '" columns="4" orderby="date" order="DESC"',
					'url'   => add_query_arg( 'orderby', 'date', $renosi_shop ),
				)
			);
		}
	}

	// Admin-only hint while the store is empty.
	if ( current_user_can( 'manage_options' ) ) {
		$renosi_products = wp_count_posts( 'product' );
		if ( ! $renosi_has_wc || ! $renosi_products || 0 === (int) $renosi_products->publish ) {
			get_template_part( 'template-parts/home/empty-hint' );
		}
	}

	if ( renosi_opt( 'home_cta' ) ) {
		get_template_part( 'template-parts/home/cta' );
	}

	if ( $renosi_has_wc && renosi_opt( 'home_best' ) ) {
		get_template_part(
			'template-parts/home/products',
			null,
			array(
				'title' => renosi_opt( 'home_best_title' ),
				'atts'  => 'limit="' . $renosi_count . '" columns="4" best_selling="true"',
				'url'   => add_query_arg( 'orderby', 'popularity', $renosi_shop ),
			)
		);
	}

	if ( $renosi_has_wc && renosi_opt( 'home_brands' ) ) {
		get_template_part( 'template-parts/home/brands' );
	}

	// Content of the static front page (if any), e.g. block-editor sections.
	if ( 'page' === get_option( 'show_on_front' ) ) {
		while ( have_posts() ) {
			the_post();
			if ( '' !== trim( get_the_content() ) ) {
				echo '<section class="section"><div class="container entry-content">';
				the_content();
				echo '</div></section>';
			}
		}
	}

	if ( renosi_opt( 'home_blog' ) ) {
		get_template_part( 'template-parts/home/blog' );
	}
	?>
</main>
<?php
get_footer();
