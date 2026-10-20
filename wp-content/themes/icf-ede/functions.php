<?php
/**
 * ICF Ede thema-functies.
 *
 * @package icf-ede
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/teksten.php';

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'icf-ede', get_template_directory() . '/languages' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$theme = wp_get_theme();
		wp_enqueue_style( 'icf-ede', get_stylesheet_uri(), array(), $theme->get( 'Version' ) );
	}
);

/* Patrooncategorie zodat het team de ICF-blokken snel terugvindt. */
add_action(
	'init',
	function () {
		register_block_pattern_category( 'icf-ede', array( 'label' => __( 'ICF Ede', 'icf-ede' ) ) );
	}
);

/* Shortcodes in koppen en alinea's van templates (zoals de 404-pagina) uitvoeren. */
add_filter(
	'render_block',
	function ( $html, $block ) {
		if ( in_array( $block['blockName'], array( 'core/heading', 'core/paragraph' ), true ) && false !== strpos( $html, '[icf_tekst' ) ) {
			return do_shortcode( $html );
		}
		return $html;
	},
	10,
	2
);
