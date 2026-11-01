<?php
/**
 * Default search form (blog). Searches all content.
 *
 * @package renosi
 */

$renosi_search_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-field" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $renosi_search_id ); ?>"><?php esc_html_e( 'جستجو', 'renosi' ); ?></label>
	<span class="search-field__icon" aria-hidden="true"><?php renosi_the_icon( 'search', 18 ); ?></span>
	<input type="search" id="<?php echo esc_attr( $renosi_search_id ); ?>" class="search-field__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جستجو…', 'renosi' ); ?>">
	<button type="submit" class="search-field__submit"><?php esc_html_e( 'جستجو', 'renosi' ); ?></button>
</form>
