<?php
/**
 * Plugin Name: ICF Ede – Bijeenkomsten
 * Description: Rooster van bijeenkomsten (verleden datums verdwijnen vanzelf), reeksen in één keer toevoegen, en vertaalbare teksten voor header en footer. Werkt met Polylang.
 * Version: 1.3.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: ICF Ede
 * Text Domain: icf-ede
 * License: GPL-2.0-or-later
 *
 * @package icf-ede
 */

defined( 'ABSPATH' ) || exit;

const ICF_CPT = 'icf_bijeenkomst';

require_once __DIR__ . '/install.php';

/* -----------------------------------------------------------------
 * 1. Vaste teksten (header, footer, rooster) in vier talen
 *    Andere talen: vertaal via Talen > Vertalingen (Polylang).
 * ----------------------------------------------------------------- */
function icf_teksten_alle() {
	return array(
		'nl' => array(
			'footer_intro'   => 'Interculturele Christelijke Familie Ede. Iedereen is welkom, uit welk land of welke cultuur je ook komt.',
			'footer_waar'    => 'Waar',
			'footer_contact' => 'Contact',
			'vers'           => '“Want waar twee of drie in mijn naam samenkomen, daar ben Ik in hun midden.” (Matteüs 18:20)',
			'volgende'       => 'Volgende bijeenkomst',
			'geen'           => 'Er staan op dit moment geen bijeenkomsten gepland. Mail ons gerust: contact@icfede.nl',
			'taal'           => 'Kies je taal',
			'404_titel'      => 'Deze pagina bestaat niet (meer)',
			'404_tekst'      => 'Geen zorgen, je bent nog steeds welkom.',
			'404_link'       => 'Naar de homepagina',
		),
		'en' => array(
			'footer_intro'   => 'Intercultural Christian Family Ede. Everyone is welcome, whatever country or culture you come from.',
			'footer_waar'    => 'Where',
			'footer_contact' => 'Contact',
			'vers'           => '“For where two or three are gathered together in my name, there am I in the midst of them.” (Matthew 18:20)',
			'volgende'       => 'Next meeting',
			'geen'           => 'There are no meetings scheduled at the moment. Feel free to email us: contact@icfede.nl',
			'taal'           => 'Choose your language',
			'404_titel'      => 'This page doesn’t exist (anymore)',
			'404_tekst'      => 'Don’t worry, you are still very welcome.',
			'404_link'       => 'Go to the home page',
		),
		'fa' => array(
			'footer_intro'   => 'خانوادهٔ مسیحی بین‌فرهنگی اِده. همه خوش آمدند، از هر کشور و فرهنگی که باشند.',
			'footer_waar'    => 'کجا',
			'footer_contact' => 'تماس',
			'vers'           => '«زیرا جایی که دو یا سه نفر به اسم من جمع شوند، آنجا در میان ایشان حاضرم.» (متی 18:20)',
			'volgende'       => 'جلسهٔ بعدی',
			'geen'           => 'در حال حاضر جلسه‌ای برنامه‌ریزی نشده است. به ما ایمیل بزنید: contact@icfede.nl',
			'taal'           => 'زبان خود را انتخاب کنید',
			'404_titel'      => 'این صفحه وجود ندارد',
			'404_tekst'      => 'نگران نباشید، شما همچنان خوش آمدید.',
			'404_link'       => 'رفتن به صفحهٔ اصلی',
		),
		'ar' => array(
			'footer_intro'   => 'العائلة المسيحية متعددة الثقافات في إده. الجميع مرحَّب بهم، من أي بلد أو ثقافة جاؤوا.',
			'footer_waar'    => 'المكان',
			'footer_contact' => 'اتصل بنا',
			'vers'           => '«لأَنَّهُ حَيْثُمَا اجْتَمَعَ اثْنَانِ أَوْ ثَلاَثَةٌ بِاسْمِي فَهُنَاكَ أَكُونُ فِي وَسْطِهِمْ» (متى 18:20)',
			'volgende'       => 'الاجتماع القادم',
			'geen'           => 'لا توجد اجتماعات مقرّرة حاليًا. راسلنا: contact@icfede.nl',
			'taal'           => 'اختر لغتك',
			'404_titel'      => 'هذه الصفحة غير موجودة',
			'404_tekst'      => 'لا تقلق، ما زلت موضع ترحيب.',
			'404_link'       => 'إلى الصفحة الرئيسية',
		),
	);
}

function icf_taal() {
	if ( function_exists( 'pll_current_language' ) && pll_current_language() ) {
		return pll_current_language();
	}
	return 'nl';
}

function icf_t( $key ) {
	$alle = icf_teksten_alle();
	$lang = icf_taal();
	if ( isset( $alle[ $lang ][ $key ] ) ) {
		return $alle[ $lang ][ $key ];
	}
	$nl = isset( $alle['nl'][ $key ] ) ? $alle['nl'][ $key ] : $key;
	return function_exists( 'pll__' ) ? pll__( $nl ) : $nl;
}

/* Voor talen die later worden toegevoegd: vertaalbaar in Talen > Vertalingen. */
add_action(
	'init',
	function () {
		if ( function_exists( 'pll_register_string' ) ) {
			foreach ( icf_teksten_alle()['nl'] as $key => $tekst ) {
				pll_register_string( 'icf_' . $key, $tekst, 'ICF Ede', strlen( $tekst ) > 60 );
			}
		}
	},
	20
);

/* -----------------------------------------------------------------
 * 1b. Interne links: href="#icf:meedoen" wijst altijd naar de pagina
 *     in de taal van de bezoeker. Sleutels = Nederlandse slugs.
 * ----------------------------------------------------------------- */
function icf_pagina_url( $key ) {
	$id = ( 'home' === $key ) ? (int) get_option( 'page_on_front' ) : 0;
	if ( ! $id ) {
		$page = get_page_by_path( $key );
		$id   = $page ? $page->ID : 0;
	}
	if ( function_exists( 'pll_get_post' ) && $id ) {
		$vertaald = pll_get_post( $id, icf_taal() );
		if ( $vertaald ) {
			$id = $vertaald;
		}
	}
	if ( 'home' === $key ) {
		return function_exists( 'pll_home_url' ) ? pll_home_url( icf_taal() ) : home_url( '/' );
	}
	return $id ? get_permalink( $id ) : home_url( '/' );
}

function icf_vervang_links( $html ) {
	if ( false === strpos( $html, '#icf:' ) ) {
		return $html;
	}
	return preg_replace_callback(
		'/href="#icf:([a-z0-9-]+)(#[A-Za-z0-9_-]+)?"/',
		function ( $m ) {
			$anker = isset( $m[2] ) ? $m[2] : '';
			return 'href="' . esc_url( icf_pagina_url( $m[1] ) . $anker ) . '"';
		},
		$html
	);
}
add_filter( 'the_content', 'icf_vervang_links', 20 );
add_filter( 'render_block', 'icf_vervang_links', 20 );

/* -----------------------------------------------------------------
 * 2. Berichttype "Bijeenkomst"
 * ----------------------------------------------------------------- */
add_action(
	'init',
	function () {
		register_post_type(
			ICF_CPT,
			array(
				'labels'       => array(
					'name'          => 'Bijeenkomsten',
					'singular_name' => 'Bijeenkomst',
					'add_new'       => 'Nieuwe bijeenkomst',
					'add_new_item'  => 'Nieuwe bijeenkomst',
					'edit_item'     => 'Bijeenkomst bewerken',
					'all_items'     => 'Alle bijeenkomsten',
					'menu_name'     => 'Bijeenkomsten',
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => false,
				'menu_icon'    => 'dashicons-calendar-alt',
				'menu_position'=> 5,
				'supports'     => array( 'title' ),
			)
		);
	}
);

/* Bijeenkomsten zijn voor alle talen hetzelfde: niet laten vertalen door Polylang. */
add_filter(
	'pll_get_post_types',
	function ( $types ) {
		unset( $types[ ICF_CPT ] );
		return $types;
	}
);

function icf_standaard() {
	return wp_parse_args(
		get_option( 'icf_standaard', array() ),
		array(
			'tijd'    => '15:45',
			'locatie' => 'Paasbergschool',
			'url'     => 'https://maps.google.com/?q=Kleefsehoek+1A+Ede',
		)
	);
}

function icf_meta( $post_id ) {
	return array(
		'datum'     => get_post_meta( $post_id, '_icf_datum', true ),
		'tijd'      => get_post_meta( $post_id, '_icf_tijd', true ),
		'locatie'   => get_post_meta( $post_id, '_icf_locatie', true ),
		'url'       => get_post_meta( $post_id, '_icf_url', true ),
		'opmerking' => get_post_meta( $post_id, '_icf_opmerking', true ),
	);
}

/** Maakt of werkt een bijeenkomst bij. Wordt ook door de reeks-tool en de preview gebruikt. */
function icf_bewaar_bijeenkomst( $data, $post_id = 0 ) {
	$data  = wp_parse_args( $data, icf_standaard() + array( 'opmerking' => '', 'datum' => '' ) );
	$datum = DateTime::createFromFormat( 'Y-m-d', $data['datum'] );
	if ( ! $datum ) {
		return 0;
	}
	$args = array(
		'post_type'   => ICF_CPT,
		'post_status' => 'publish',
		'post_title'  => wp_date( 'j F Y', $datum->getTimestamp() ),
	);
	if ( $post_id ) {
		$args['ID'] = $post_id;
		wp_update_post( $args );
	} else {
		$post_id = wp_insert_post( $args );
	}
	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return 0;
	}
	update_post_meta( $post_id, '_icf_datum', $datum->format( 'Y-m-d' ) );
	update_post_meta( $post_id, '_icf_tijd', sanitize_text_field( $data['tijd'] ) );
	update_post_meta( $post_id, '_icf_locatie', sanitize_text_field( $data['locatie'] ) );
	update_post_meta( $post_id, '_icf_url', esc_url_raw( $data['url'] ) );
	update_post_meta( $post_id, '_icf_opmerking', sanitize_text_field( $data['opmerking'] ) );
	return $post_id;
}

/* ---- Invoervelden ---- */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'icf_details', 'Gegevens', 'icf_metabox', ICF_CPT, 'normal', 'high' );
	}
);

function icf_metabox( $post ) {
	$m = icf_meta( $post->ID );
	if ( ! $m['datum'] ) {
		$m = array_merge( $m, icf_standaard() );
	}
	wp_nonce_field( 'icf_bewaar', 'icf_nonce' );
	?>
	<style>.icf-velden{display:grid;grid-template-columns:140px 1fr;gap:10px 16px;align-items:center;max-width:640px}.icf-velden input{width:100%}</style>
	<p>De titel wordt automatisch de datum. Verleden bijeenkomsten verdwijnen vanzelf van de website.</p>
	<div class="icf-velden">
		<label for="icf_datum"><strong>Datum</strong></label>
		<input type="date" id="icf_datum" name="icf[datum]" value="<?php echo esc_attr( $m['datum'] ); ?>" required>
		<label for="icf_tijd">Aanvang</label>
		<input type="time" id="icf_tijd" name="icf[tijd]" value="<?php echo esc_attr( $m['tijd'] ); ?>">
		<label for="icf_locatie">Locatie</label>
		<input type="text" id="icf_locatie" name="icf[locatie]" value="<?php echo esc_attr( $m['locatie'] ); ?>">
		<label for="icf_url">Link naar kaart</label>
		<input type="url" id="icf_url" name="icf[url]" value="<?php echo esc_attr( $m['url'] ); ?>" placeholder="https://maps.app.goo.gl/...">
		<label for="icf_opm">Opmerking</label>
		<input type="text" id="icf_opm" name="icf[opmerking]" value="<?php echo esc_attr( $m['opmerking'] ); ?>" placeholder="bijv. Paasdienst, gezamenlijke dienst">
	</div>
	<?php
}

add_action(
	'save_post_' . ICF_CPT,
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['icf_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['icf_nonce'] ), 'icf_bewaar' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || empty( $_POST['icf'] ) || ! is_array( $_POST['icf'] ) ) {
			return;
		}
		$data = array_map( 'sanitize_text_field', wp_unslash( $_POST['icf'] ) );
		remove_all_actions( 'save_post_' . ICF_CPT );
		icf_bewaar_bijeenkomst( $data, $post_id );
	}
);

/* ---- Overzicht in wp-admin: op datum, verleden grijs ---- */
add_filter(
	'manage_' . ICF_CPT . '_posts_columns',
	function () {
		return array(
			'cb'        => '<input type="checkbox">',
			'title'     => 'Datum',
			'icf_tijd'  => 'Aanvang',
			'icf_plek'  => 'Locatie',
			'icf_opm'   => 'Opmerking',
		);
	}
);

add_action(
	'manage_' . ICF_CPT . '_posts_custom_column',
	function ( $col, $post_id ) {
		$m = icf_meta( $post_id );
		if ( 'icf_tijd' === $col ) {
			echo esc_html( $m['tijd'] );
		} elseif ( 'icf_plek' === $col ) {
			echo esc_html( $m['locatie'] );
		} elseif ( 'icf_opm' === $col ) {
			echo esc_html( $m['opmerking'] );
		}
	},
	10,
	2
);

add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() && $q->is_main_query() && ICF_CPT === $q->get( 'post_type' ) && ! $q->get( 'orderby' ) ) {
			$q->set( 'meta_key', '_icf_datum' );
			$q->set( 'orderby', 'meta_value' );
			$q->set( 'order', 'ASC' );
		}
	}
);

add_filter(
	'post_class',
	function ( $classes, $class, $post_id ) {
		if ( is_admin() && ICF_CPT === get_post_type( $post_id ) && get_post_meta( $post_id, '_icf_datum', true ) < wp_date( 'Y-m-d' ) ) {
			$classes[] = 'icf-verleden';
		}
		return $classes;
	},
	10,
	3
);

add_action(
	'admin_head',
	function () {
		echo '<style>tr.icf-verleden{opacity:.45}</style>';
	}
);

/* -----------------------------------------------------------------
 * 3. Reeks toevoegen (bijv. elke 2 weken, 26 keer) + standaardwaarden
 * ----------------------------------------------------------------- */
add_action(
	'admin_menu',
	function () {
		add_submenu_page( 'edit.php?post_type=' . ICF_CPT, 'Reeks toevoegen', 'Reeks toevoegen', 'edit_posts', 'icf-reeks', 'icf_reeks_pagina' );
	}
);

function icf_reeks_pagina() {
	$st      = icf_standaard();
	$bericht = '';

	if ( isset( $_POST['icf_reeks_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['icf_reeks_nonce'] ), 'icf_reeks' ) && current_user_can( 'edit_posts' ) ) {
		$start  = DateTime::createFromFormat( 'Y-m-d', sanitize_text_field( wp_unslash( $_POST['start'] ?? '' ) ) );
		$weken  = max( 1, min( 8, absint( $_POST['weken'] ?? 2 ) ) );
		$aantal = max( 1, min( 60, absint( $_POST['aantal'] ?? 10 ) ) );
		$data   = array(
			'tijd'    => sanitize_text_field( wp_unslash( $_POST['tijd'] ?? $st['tijd'] ) ),
			'locatie' => sanitize_text_field( wp_unslash( $_POST['locatie'] ?? $st['locatie'] ) ),
			'url'     => esc_url_raw( wp_unslash( $_POST['url'] ?? $st['url'] ) ),
		);
		if ( ! empty( $_POST['bewaar_standaard'] ) ) {
			update_option( 'icf_standaard', $data );
			$st = icf_standaard();
		}
		if ( $start ) {
			$gemaakt = 0;
			for ( $i = 0; $i < $aantal; $i++ ) {
				$d      = ( clone $start )->modify( '+' . ( $i * $weken ) . ' weeks' )->format( 'Y-m-d' );
				$bestaat = get_posts( array( 'post_type' => ICF_CPT, 'meta_key' => '_icf_datum', 'meta_value' => $d, 'fields' => 'ids', 'posts_per_page' => 1 ) );
				if ( ! $bestaat && icf_bewaar_bijeenkomst( $data + array( 'datum' => $d ) ) ) {
					$gemaakt++;
				}
			}
			/* translators: %d aantal */
			$bericht = sprintf( '%d bijeenkomsten toegevoegd (bestaande datums overgeslagen).', $gemaakt );
		}
	}
	?>
	<div class="wrap">
		<h1>Reeks bijeenkomsten toevoegen</h1>
		<?php if ( $bericht ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $bericht ); ?></p></div>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'icf_reeks', 'icf_reeks_nonce' ); ?>
			<table class="form-table">
				<tr><th><label for="start">Eerste datum</label></th><td><input type="date" id="start" name="start" required></td></tr>
				<tr><th><label for="weken">Elke … weken</label></th><td><input type="number" id="weken" name="weken" value="2" min="1" max="8"></td></tr>
				<tr><th><label for="aantal">Aantal keer</label></th><td><input type="number" id="aantal" name="aantal" value="13" min="1" max="60"> <span class="description">13 × 2 weken ≈ een half jaar</span></td></tr>
				<tr><th><label for="tijd">Aanvang</label></th><td><input type="time" id="tijd" name="tijd" value="<?php echo esc_attr( $st['tijd'] ); ?>"></td></tr>
				<tr><th><label for="locatie">Locatie</label></th><td><input type="text" id="locatie" name="locatie" class="regular-text" value="<?php echo esc_attr( $st['locatie'] ); ?>"></td></tr>
				<tr><th><label for="url">Link naar kaart</label></th><td><input type="url" id="url" name="url" class="regular-text" value="<?php echo esc_attr( $st['url'] ); ?>"></td></tr>
				<tr><th>Standaard</th><td><label><input type="checkbox" name="bewaar_standaard" value="1"> Aanvang en locatie onthouden als standaard</label></td></tr>
			</table>
			<?php submit_button( 'Reeks toevoegen' ); ?>
		</form>
		<p>Een afwijkende dienst (andere tijd of plek)? Pas die ene bijeenkomst daarna los aan.</p>
	</div>
	<?php
}

/* -----------------------------------------------------------------
 * 4. Shortcodes voor de website
 * ----------------------------------------------------------------- */
function icf_komende( $aantal ) {
	return get_posts(
		array(
			'post_type'      => ICF_CPT,
			'posts_per_page' => $aantal,
			'meta_key'       => '_icf_datum',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array(
				array( 'key' => '_icf_datum', 'value' => wp_date( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
			),
			'no_found_rows'  => true,
		)
	);
}

function icf_ts( $m ) {
	$tijd = $m['tijd'] ? $m['tijd'] : '00:00';
	$dt   = date_create_immutable( $m['datum'] . ' ' . $tijd, wp_timezone() );
	return $dt ? $dt->getTimestamp() : 0;
}

function icf_plek_html( $m ) {
	$naam = esc_html( $m['locatie'] );
	return $m['url'] ? '<a href="' . esc_url( $m['url'] ) . '" rel="noopener" target="_blank">' . $naam . '</a>' : $naam;
}

/* [icf_bijeenkomsten aantal="8"] */
add_shortcode(
	'icf_bijeenkomsten',
	function ( $atts ) {
		$atts  = shortcode_atts( array( 'aantal' => 8 ), $atts );
		$items = icf_komende( max( 1, absint( $atts['aantal'] ) ) );
		if ( ! $items ) {
			return '<p class="icf-leeg">' . esc_html( icf_t( 'geen' ) ) . '</p>';
		}
		$st   = icf_standaard();
		$html = '<ul class="icf-rooster">';
		foreach ( $items as $p ) {
			$m         = icf_meta( $p->ID );
			$ts        = icf_ts( $m );
			$afwijkend = ( $m['locatie'] !== $st['locatie'] || $m['tijd'] !== $st['tijd'] || $m['opmerking'] );
			$html     .= '<li class="' . ( $afwijkend ? 'is-afwijkend' : '' ) . '">';
			$html     .= '<time class="icf-dag" datetime="' . esc_attr( $m['datum'] ) . '"><b>' . esc_html( wp_date( 'j', $ts ) ) . '</b><span>' . esc_html( wp_date( 'M', $ts ) ) . '</span></time>';
			$html     .= '<div class="icf-info"><span class="icf-tijd">' . esc_html( wp_date( 'l', $ts ) . ' · ' . $m['tijd'] ) . '</span>';
			$html     .= '<span class="icf-plek">' . icf_plek_html( $m ) . '</span>';
			if ( $m['opmerking'] ) {
				$html .= '<span class="icf-opm">' . esc_html( $m['opmerking'] ) . '</span>';
			}
			$html .= '</div></li>';
		}
		return $html . '</ul>';
	}
);

/* [icf_volgende_bijeenkomst] */
add_shortcode(
	'icf_volgende_bijeenkomst',
	function () {
		$items = icf_komende( 1 );
		if ( ! $items ) {
			return '';
		}
		$m  = icf_meta( $items[0]->ID );
		$ts = icf_ts( $m );
		return '<p class="icf-volgende"><strong>' . esc_html( icf_t( 'volgende' ) ) . '</strong>'
			. '<span class="icf-volgende-datum">' . esc_html( wp_date( 'l j F', $ts ) ) . ' · ' . esc_html( $m['tijd'] ) . '</span>'
			. '<span>' . icf_plek_html( $m ) . ( $m['opmerking'] ? ' · ' . esc_html( $m['opmerking'] ) : '' ) . '</span></p>';
	}
);

/* [icf_tekst key="footer_intro"] – vertaalbaar via Polylang > Vertalingen */
add_shortcode(
	'icf_tekst',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'key' => '' ), $atts );
		return esc_html( icf_t( $atts['key'] ) );
	}
);

/* [icf_talen] – opvallende taalbalk + automatisch de taal van de browser volgen */
add_shortcode(
	'icf_talen',
	function () {
		if ( ! function_exists( 'pll_the_languages' ) ) {
			return '';
		}
		$talen = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0, 'hide_if_no_translation' => 0 ) );
		if ( ! is_array( $talen ) || count( $talen ) < 2 ) {
			return '';
		}
		$alle = icf_teksten_alle();
		$html = '<nav class="icf-taalbalk-nav" aria-label="' . esc_attr( icf_t( 'taal' ) ) . '" data-huidig="' . esc_attr( icf_taal() ) . '" data-standaard="' . esc_attr( pll_default_language() ) . '">';
		$html .= '<svg class="icf-wereld" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M2.5 12h19M12 2.5c2.6 2.7 3.9 5.9 3.9 9.5s-1.3 6.8-3.9 9.5M12 2.5C9.4 5.2 8.1 8.4 8.1 12s1.3 6.8 3.9 9.5" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>';
		$html .= '<ul>';
		foreach ( $talen as $t ) {
			$slug   = $t['slug'];
			$label  = isset( $alle[ $slug ]['taal'] ) ? $alle[ $slug ]['taal'] : $t['name'];
			$dir    = in_array( $slug, array( 'fa', 'ar' ), true ) ? 'rtl' : 'ltr';
			$actief = ! empty( $t['current_lang'] );
			$html  .= sprintf(
				'<li><a href="%s" lang="%s" hreflang="%s" dir="%s" data-taal="%s" title="%s"%s>%s</a></li>',
				esc_url( $t['url'] ),
				esc_attr( $slug ),
				esc_attr( $slug ),
				$dir,
				esc_attr( $slug ),
				esc_attr( $label ),
				$actief ? ' aria-current="true" class="is-actief"' : '',
				esc_html( $t['name'] )
			);
		}
		return $html . '</ul></nav>';
	}
);

/*
 * Browsertaal volgen: wie voor het eerst op de (Nederlandse) site komt en een browser
 * in het Farsi, Arabisch of Engels heeft, gaat meteen naar die taal. Een keuze in de
 * taalbalk wordt onthouden en gaat altijd voor. Engels alleen als Nederlands niet in de
 * browserinstellingen staat (veel Nederlanders hebben een Engelse browser).
 */
add_action(
	'wp_footer',
	function () {
		if ( ! function_exists( 'pll_the_languages' ) ) {
			return;
		}
		?>
<script id="icf-taalkeuze">
(function () {
	var SLEUTEL = 'icf_taal';
	var nav = document.querySelector('.icf-taalbalk-nav');
	if (!nav) { return; }
	var links = Array.prototype.slice.call(nav.querySelectorAll('a[data-taal]'));
	var beschikbaar = links.map(function (a) { return a.getAttribute('data-taal'); });

	function bewaar(taal) { try { localStorage.setItem(SLEUTEL, taal); } catch (e) {} }
	function gekozen() { try { return localStorage.getItem(SLEUTEL); } catch (e) { return null; } }

	links.forEach(function (a) {
		a.addEventListener('click', function () { bewaar(a.getAttribute('data-taal')); });
	});

	/* Pure functie, ook te gebruiken om te testen: icfKiesTaal(['fa-IR','en']) -> 'fa' */
	window.icfKiesTaal = function (browserTalen, standaard) {
		var lijst = (browserTalen || []).map(function (t) {
			var hoofd = String(t).toLowerCase().split('-')[0];
			return hoofd === 'prs' ? 'fa' : hoofd; /* Dari -> Farsi */
		});
		var heeftNl = lijst.indexOf('nl') !== -1;
		for (var i = 0; i < lijst.length; i++) {
			var t = lijst[i];
			if (t === standaard) { return standaard; }
			if (t === 'en' && heeftNl) { continue; }
			if (beschikbaar.indexOf(t) !== -1) { return t; }
		}
		return standaard;
	};

	var huidig = nav.getAttribute('data-huidig');
	var standaard = nav.getAttribute('data-standaard');
	if (gekozen()) { return; }               /* bezoeker koos zelf al een taal */
	if (huidig !== standaard) { return; }    /* alleen doorsturen vanaf Nederlandse pagina's */
	var talen = navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language || ''];
	var doel = window.icfKiesTaal(talen, standaard);
	if (doel && doel !== huidig) {
		var link = links.filter(function (a) { return a.getAttribute('data-taal') === doel; })[0];
		if (link) { location.replace(link.href); }
	}
})();
</script>
		<?php
	}
);
