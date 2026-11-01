<?php
/**
 * SVG icon set (24px grid, stroke based).
 *
 * @package renosi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Raw path data for each icon.
 *
 * @return array
 */
function renosi_icon_paths() {
	return array(
		'search'      => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'user'        => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'bag'         => '<path d="M5 8h14l-1 12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'       => '<path d="M6 6l12 12M18 6 6 18"/>',
		'phone'       => '<path d="M5 4h3l2 5-2.5 1.5a11 11 0 0 0 6 6L15 14l5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
		'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
		'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
		'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
		'arrow-left'  => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
		'truck'       => '<path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
		'shield'      => '<path d="M12 3 5 6v6c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
		'refresh'     => '<path d="M20 11a8 8 0 0 0-14.5-4.5L4 8"/><path d="M4 4v4h4"/><path d="M4 13a8 8 0 0 0 14.5 4.5L20 16"/><path d="M20 20v-4h-4"/>',
		'headset'     => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14h3v5H5a1 1 0 0 1-1-1v-4ZM20 14h-3v5h2a1 1 0 0 0 1-1v-4Z"/><path d="M17 19c0 1-1.5 2-5 2"/>',
		'grid'        => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
		'home'        => '<path d="M4 11 12 4l8 7"/><path d="M6 10v10h12V10"/><path d="M10 20v-5h4v5"/>',
		'mail'        => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
		'pin'         => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'filter'      => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'minus'       => '<path d="M5 12h14"/>',
		'check'       => '<path d="m5 12 5 5 9-10"/>',
		'car'         => '<path d="M5 16V11l2-5h10l2 5v5"/><path d="M3 16h18v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-1H7v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-2Z"/><path d="M5 11h14"/><circle cx="8" cy="14" r=".5"/><circle cx="16" cy="14" r=".5"/>',
		'wrench'      => '<path d="M14.5 6.5a4 4 0 0 0 5 5l-9 9a2.1 2.1 0 0 1-3-3l9-9a4 4 0 0 1-2-2Z"/><path d="M15 4a4 4 0 0 1 5 5l-3-1-1-3Z"/>',
		'tag'         => '<path d="M3 12V4h8l10 10-8 8L3 12Z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
		'part'        => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="3"/><circle cx="12" cy="6.5" r=".8"/><circle cx="12" cy="17.5" r=".8"/><circle cx="6.5" cy="12" r=".8"/><circle cx="17.5" cy="12" r=".8"/>',
		'instagram'   => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r=".6"/>',
		'telegram'    => '<path d="M21 4 3 11l6 2.5L18 7l-7 8 7 5 3-16Z"/>',
		'whatsapp'    => '<path d="M4 20l1.3-4A8.5 8.5 0 1 1 8.5 19L4 20Z"/><path d="M9 9c0 3 3 6 6 6l1-1.5-2-1-1 .8A4 4 0 0 1 10.7 11l.8-1-1-2L9 9Z"/>',
		'chat'        => '<path d="M5 18 3 21V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5Z"/><path d="M8 9h8M8 13h5"/>',
		'play'        => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="m10 9 5 3-5 3V9Z"/>',
		'star'        => '<path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8-4.3-4.1 5.9-.9L12 3.5Z"/>',
		'box'         => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
		'reset'       => '<path d="M3.5 4.5v5h5"/><path d="M4.2 14a8 8 0 1 0 1.9-8.1L3.5 9.5"/>',
		'barcode'     => '<path d="M4 6v12M7 6v12M10 6v12M14 6v12M16 6v12M20 6v12"/>',
		'bolt'        => '<path d="M13 2.5 4.5 13.5h6.5l-1 8 8.5-11h-6.5l1-8Z"/>',
		'card'        => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h4"/>',
	);
}

/**
 * Return an inline SVG icon.
 *
 * @param string $name  Icon name.
 * @param int    $size  Pixel size.
 * @param string $class Extra class.
 * @return string
 */
function renosi_icon( $name, $size = 20, $class = '' ) {
	$paths = renosi_icon_paths();
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon icon-%1$s %2$s" width="%3$d" height="%3$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%4$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		absint( $size ),
		$paths[ $name ]
	);
}

/**
 * Echo an inline SVG icon.
 *
 * @param string $name  Icon name.
 * @param int    $size  Pixel size.
 * @param string $class Extra class.
 */
function renosi_the_icon( $name, $size = 20, $class = '' ) {
	echo renosi_icon( $name, $size, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
