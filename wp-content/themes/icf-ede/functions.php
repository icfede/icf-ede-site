<?php
/**
 * ICF Ede thema-functies.
 *
 * @package icf-ede
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/teksten.php';

/*
 * Lettertype Fredoka via Bunny Fonts: een Europese, privacyvriendelijke kopie van Google Fonts
 * (geen IP-adressen naar Google, dus geen cookiemelding nodig). Fredoka heeft geen Arabisch
 * schrift; Farsi en Arabisch gebruiken hun eigen lettertype (zie style.css).
 */
const ICF_FREDOKA_URL = 'https://fonts.bunny.net/css?family=fredoka:300,400,500,600,700&display=swap';

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'icf-ede', get_template_directory() . '/languages' );
		add_theme_support( 'editor-styles' );
		add_editor_style( array( ICF_FREDOKA_URL, 'style.css' ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$theme = wp_get_theme();
		wp_enqueue_style( 'icf-fredoka', ICF_FREDOKA_URL, array(), null );
		wp_enqueue_style( 'icf-ede', get_stylesheet_uri(), array( 'icf-fredoka' ), $theme->get( 'Version' ) );
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
		$src   = get_template_directory_uri() . '/assets/logo/' . ( $wit ? 'logo-wit.png' : 'logo-horizontaal.png' ) . '?v=' . rawurlencode( wp_get_theme( 'icf-ede' )->get( 'Version' ) );
		return sprintf(
			'<a class="icf-logo%s" href="%s" rel="home"><img src="%s" alt="%s" width="%d" height="%d" decoding="async"></a>',
			$wit ? ' icf-logo-wit' : '',
			esc_url( $thuis ),
			esc_url( $src ),
			esc_attr( get_bloginfo( 'name' ) ),
			587,
			300
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
		$map   = get_template_directory_uri() . '/assets/logo/';
		$v     = '?v=' . rawurlencode( wp_get_theme( 'icf-ede' )->get( 'Version' ) );
		$icoon = esc_url( $map . 'icoon.png' . $v );
		echo '<link rel="icon" href="' . esc_url( $map . 'favicon-32.png' . $v ) . '" sizes="32x32" type="image/png">' . "\n";
		echo '<link rel="icon" href="' . $icoon . '" sizes="512x512" type="image/png">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . $icoon . '">' . "\n";
	}
);

/*
 * Plaatjes uit het thema (logo, illustraties) krijgen het versienummer van het thema achter
 * hun adres. Zo zien bezoekers na een thema-update meteen de nieuwe versie, in plaats van
 * een week lang de oude uit het geheugen van hun browser.
 */
function icf_thema_versie_url( $html ) {
	if ( false === strpos( $html, '/themes/icf-ede/assets/' ) ) {
		return $html;
	}
	$versie = wp_get_theme( 'icf-ede' )->get( 'Version' );
	return preg_replace( '#(/themes/icf-ede/assets/[^"\'?\s]+\.(?:svg|png))(["\'])#', '$1?v=' . rawurlencode( $versie ) . '$2', $html );
}
add_filter( 'render_block', 'icf_thema_versie_url', 20 );
add_filter( 'the_content', 'icf_thema_versie_url', 20 );
