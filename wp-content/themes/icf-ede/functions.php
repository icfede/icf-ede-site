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

/*
 * [icf_logo] – het ICF-logo, met een link naar de voorpagina in de taal van de bezoeker.
 * variant="wit" geeft de lichte versie voor een donkere achtergrond (footer).
 */
add_shortcode(
	'icf_logo',
	function ( $atts ) {
		$atts  = shortcode_atts( array( 'variant' => '' ), $atts );
		$wit   = 'wit' === $atts['variant'];
		$thuis = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
		$src   = get_template_directory_uri() . '/assets/logo/' . ( $wit ? 'logo-wit.png' : 'logo-horizontaal.png' );
		return sprintf(
			'<a class="icf-logo%s" href="%s" rel="home"><img src="%s" alt="%s" width="%d" height="%d" decoding="async"></a>',
			$wit ? ' icf-logo-wit' : '',
			esc_url( $thuis ),
			esc_url( $src ),
			esc_attr( get_bloginfo( 'name' ) ),
			$wit ? 559 : 941,
			$wit ? 600 : 300
		);
	}
);

/* Site-icoon (tabblad, telefoon) zolang er in WordPress nog geen eigen icoon is gekozen. */
add_action(
	'wp_head',
	function () {
		if ( has_site_icon() ) {
			return;
		}
		$icoon = esc_url( get_template_directory_uri() . '/assets/logo/icoon.png' );
		echo '<link rel="icon" href="' . $icoon . '" sizes="512x512">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . $icoon . '">' . "\n";
	}
);
