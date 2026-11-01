<?php
/**
 * Blog sidebar.
 *
 * @package renosi
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area blog-sidebar">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
