<?php
/**
 * Eenmalige installatie: Gereedschap › ICF Ede installeren.
 *
 * Zet de talen (Polylang), alle pagina's in vier talen, de vertaalkoppelingen,
 * de voorpagina en (optioneel) een voorbeeldrooster klaar. Veilig om nog eens
 * te draaien: wat al bestaat wordt overgeslagen, er wordt niets verwijderd.
 *
 * @package icf-ede
 */

defined( 'ABSPATH' ) || exit;

function icf_install_talen() {
	return array(
		array( 'name' => 'Nederlands', 'slug' => 'nl', 'locale' => 'nl_NL', 'rtl' => false, 'term_group' => 0, 'flag' => 'nl' ),
		array( 'name' => 'English', 'slug' => 'en', 'locale' => 'en_GB', 'rtl' => false, 'term_group' => 1, 'flag' => 'gb' ),
		array( 'name' => 'فارسی', 'slug' => 'fa', 'locale' => 'fa_IR', 'rtl' => true, 'term_group' => 2, 'flag' => 'ir' ),
		array( 'name' => 'العربية', 'slug' => 'ar', 'locale' => 'ar', 'rtl' => true, 'term_group' => 3, 'flag' => '' ),
	);
}

/** Controleert of alles klaarstaat. Geeft een lijst met problemen terug (leeg = ok). */
function icf_install_controle() {
	$fouten = array();
	if ( ! function_exists( 'PLL' ) || ! PLL() || ! isset( PLL()->model->languages ) ) {
		$fouten[] = 'Installeer en activeer eerst de plugin Polylang (Plugins › Nieuwe plugin › zoek "Polylang").';
	}
	if ( 'icf-ede' !== get_template() ) {
		$fouten[] = 'Activeer eerst het thema ICF Ede (Weergave › Thema\'s).';
	}
	if ( ! file_exists( __DIR__ . '/inhoud/inhoud.json' ) ) {
		$fouten[] = 'Het bestand inhoud/inhoud.json ontbreekt in de plugin.';
	}
	return $fouten;
}

/**
 * Voert de installatie uit.
 *
 * @param array $opties rooster (bool): voorbeeldrooster toevoegen.
 * @return string[] logregels.
 */
function icf_installeer( $opties = array() ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';
	$opties = wp_parse_args( $opties, array( 'rooster' => false, 'home_opnieuw' => false ) );
	$log    = array();

	$fouten = icf_install_controle();
	if ( $fouten ) {
		return $fouten;
	}
	$pll    = PLL()->model;
	$inhoud = json_decode( file_get_contents( __DIR__ . '/inhoud/inhoud.json' ), true );

	/* 1. Basisinstellingen */
	if ( ! get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$log[] = 'Mooie adressen ingesteld (/%postname%/).';
	}
	if ( ! get_option( 'timezone_string' ) ) {
		update_option( 'timezone_string', 'Europe/Amsterdam' );
	}
	update_option( 'date_format', 'j F Y' );
	update_option( 'time_format', 'H:i' );

	/* 2. Taalpakketten en talen */
	foreach ( icf_install_talen() as $t ) {
		if ( ! in_array( $t['locale'], get_available_languages(), true ) ) {
			try {
				$ok    = wp_download_language_pack( $t['locale'] );
				$log[] = 'Taalpakket ' . $t['locale'] . ( $ok ? ' gedownload.' : ' kon niet worden gedownload (niet erg: alleen WordPress-teksten blijven Engels).' );
			} catch ( Throwable $e ) {
				$log[] = 'Taalpakket ' . $t['locale'] . ': ' . $e->getMessage();
			}
		}
		if ( ! $pll->languages->get( $t['slug'] ) ) {
			$r = $pll->languages->add( $t );
			if ( is_wp_error( $r ) ) {
				// Sommige Polylang-versies kennen niet elke vlag: dan zonder vlag.
				$t['flag'] = '';
				$r         = $pll->languages->add( $t );
			}
			$log[] = 'Taal ' . $t['name'] . ( is_wp_error( $r ) ? ': ' . implode( ', ', $r->get_error_messages() ) : ' toegevoegd.' );
			$pll->languages->clean_cache();
		}
	}
	if ( in_array( 'nl_NL', get_available_languages(), true ) ) {
		update_option( 'WPLANG', 'nl_NL' );
	}
	$opt                 = get_option( 'polylang' );
	$opt['default_lang'] = 'nl';
	$opt['hide_default'] = true;
	$opt['force_lang']   = 1;
	$opt['rewrite']      = true;
	$opt['browser']      = false; // de ICF-plugin volgt zelf de browsertaal (werkt ook met caching)
	update_option( 'polylang', $opt );
	$pll->languages->clean_cache();

	/* Standaard voorbeeldinhoud van WordPress naar de prullenbak (alleen als die onaangeroerd is) */
	foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page', 'voorbeeld-pagina' => 'page', 'hallo-wereld' => 'post' ) as $slug => $type ) {
		$p = get_page_by_path( $slug, OBJECT, $type );
		if ( $p && $p->post_date === $p->post_modified ) {
			wp_trash_post( $p->ID );
			$log[] = 'Voorbeeldinhoud "' . $p->post_title . '" naar de prullenbak verplaatst.';
		}
	}

	/* Bestaande berichten/pagina's zonder taal krijgen Nederlands */
	if ( method_exists( $pll, 'set_language_in_mass' ) ) {
		$pll->set_language_in_mass();
	}

	/* 3. Pagina's per taal, gekoppeld als vertalingen */
	$groepen = array();
	foreach ( $inhoud['pages'] as $p ) {
		if ( ! $pll->languages->get( $p['lang'] ) ) {
			$log[] = 'Pagina "' . $p['title'] . '" overgeslagen: taal ' . $p['lang'] . ' ontbreekt.';
			continue;
		}
		$bestaand = get_page_by_path( $p['slug'] );
		/* Homepagina's opnieuw opbouwen uit de patronen (alleen als daarvoor gekozen is) */
		if ( $bestaand && $opties['home_opnieuw'] && ! empty( $p['patterns'] ) ) {
			$GLOBALS['icf_lang'] = $p['lang'];
			ob_start();
			foreach ( $p['patterns'] as $pat ) {
				$bestand = get_template_directory() . '/patterns/' . $pat . '.php';
				if ( file_exists( $bestand ) ) {
					include $bestand;
					echo "\n\n";
				}
			}
			$nieuw_inhoud = str_replace( '{{thema}}', get_template_directory_uri(), ob_get_clean() );
			unset( $GLOBALS['icf_lang'] );
			wp_update_post( array( 'ID' => $bestaand->ID, 'post_content' => wp_slash( $nieuw_inhoud ) ) );
			if ( pll_get_post_language( $bestaand->ID ) !== $p['lang'] ) {
				pll_set_post_language( $bestaand->ID, $p['lang'] );
			}
			$groepen[ $p['key'] ][ $p['lang'] ] = $bestaand->ID;
			$log[] = 'Pagina "' . $p['title'] . '" opnieuw opgebouwd.';
			continue;
		}
		if ( $bestaand ) {
			if ( pll_get_post_language( $bestaand->ID ) !== $p['lang'] ) {
				pll_set_post_language( $bestaand->ID, $p['lang'] );
				$log[] = 'Pagina "' . $p['title'] . '" kreeg de juiste taal (' . $p['lang'] . ').';
			}
			$groepen[ $p['key'] ][ $p['lang'] ] = $bestaand->ID;
			$log[] = 'Pagina "' . $p['title'] . '" bestond al, overgeslagen.';
			continue;
		}
		if ( ! empty( $p['patterns'] ) ) {
			$GLOBALS['icf_lang'] = $p['lang'];
			ob_start();
			foreach ( $p['patterns'] as $pat ) {
				$bestand = get_template_directory() . '/patterns/' . $pat . '.php';
				if ( file_exists( $bestand ) ) {
					include $bestand;
					echo "\n\n";
				}
			}
			$p['content'] = ob_get_clean();
			unset( $GLOBALS['icf_lang'] );
		}
		$p['content'] = str_replace( '{{thema}}', get_template_directory_uri(), $p['content'] );
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_name'    => $p['slug'],
				'menu_order'   => $p['order'],
				'post_content' => wp_slash( $p['content'] ),
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			pll_set_post_language( $id, $p['lang'] );
			$groepen[ $p['key'] ][ $p['lang'] ] = $id;
			$log[] = 'Pagina "' . $p['title'] . '" (' . $p['lang'] . ') gemaakt.';
		}
	}
	foreach ( $groepen as $g ) {
		pll_save_post_translations( $g );
	}

	/* 4. Voorpagina */
	if ( ! empty( $groepen['home']['nl'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $groepen['home']['nl'] );
		$log[] = 'Homepagina ingesteld als voorpagina.';
	}
	$pll->languages->clean_cache();

	/* 5. Ondertitel van de site per taal */
	if ( class_exists( 'PLL_MO' ) && ! empty( $inhoud['ondertitel'] ) ) {
		try {
			update_option( 'blogdescription', $inhoud['ondertitel']['nl'] );
			foreach ( $inhoud['ondertitel'] as $slug => $tekst ) {
				$lang = $pll->languages->get( $slug );
				if ( 'nl' === $slug || ! $lang ) {
					continue;
				}
				$mo = new PLL_MO();
				$mo->import_from_db( $lang );
				$mo->add_entry( $mo->make_entry( $inhoud['ondertitel']['nl'], $tekst ) );
				$mo->export_to_db( $lang );
			}
			$log[] = 'Ondertitel vertaald.';
		} catch ( Throwable $e ) {
			$log[] = 'Ondertitel: ' . $e->getMessage();
		}
	}

	/* 6. Voorbeeldrooster (alleen toekomstige datums die nog niet bestaan) */
	if ( $opties['rooster'] && ! empty( $inhoud['rooster'] ) ) {
		$nieuw = 0;
		foreach ( $inhoud['rooster'] as $d ) {
			if ( $d < wp_date( 'Y-m-d' ) ) {
				continue;
			}
			$bestaat = get_posts( array( 'post_type' => ICF_CPT, 'meta_key' => '_icf_datum', 'meta_value' => $d, 'fields' => 'ids', 'posts_per_page' => 1 ) );
			if ( ! $bestaat && icf_bewaar_bijeenkomst( array( 'datum' => $d ) ) ) {
				$nieuw++;
			}
		}
		$log[] = $nieuw . ' bijeenkomsten aan het rooster toegevoegd.';
	}

	/* 7. Adressen (/en/, /fa/, /ar/) opnieuw laten opbouwen bij het volgende bezoek */
	delete_option( 'rewrite_rules' );
	/* Op een echte server (Apache) heeft WordPress een .htaccess nodig voor de mooie adressen. */
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	if ( got_mod_rewrite() && save_mod_rewrite_rules() ) {
		$log[] = '.htaccess bijgewerkt voor de mooie adressen.';
	}
	update_option( 'icf_geinstalleerd', current_time( 'mysql' ) );
	$log[] = 'Klaar.';
	return $log;
}

/* ---------- Beheerpagina ---------- */
add_action(
	'admin_menu',
	function () {
		add_management_page( 'ICF Ede installeren', 'ICF Ede installeren', 'manage_options', 'icf-installeren', 'icf_install_pagina' );
	}
);

function icf_install_pagina() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = array();
	if ( isset( $_POST['icf_install_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['icf_install_nonce'] ), 'icf_install' ) ) {
		$log = icf_installeer(
			array(
				'rooster'      => ! empty( $_POST['rooster'] ),
				'home_opnieuw' => ! empty( $_POST['home_opnieuw'] ),
			)
		);
	}
	$fouten      = icf_install_controle();
	$geinstalleerd = get_option( 'icf_geinstalleerd' );
	?>
	<div class="wrap">
		<h1>ICF Ede installeren</h1>
		<p>Deze knop zet in één keer klaar: vier talen (Nederlands, English, فارسی, العربية), de pagina's Home, Meedoen, Missie en Contact in alle talen, de vertaalkoppelingen en de voorpagina.</p>
		<p>Bestaande pagina's worden <strong>niet</strong> overschreven of verwijderd. Je kunt de knop dus veilig nog een keer gebruiken, bijvoorbeeld na het toevoegen van een taal.</p>
		<?php if ( $geinstalleerd && ! $log ) : ?>
			<div class="notice notice-info"><p>Eerder uitgevoerd op <?php echo esc_html( $geinstalleerd ); ?>.</p></div>
		<?php endif; ?>
		<?php if ( $log ) : ?>
			<div class="notice notice-success"><ul style="list-style:disc;margin-left:1.5em">
				<?php foreach ( $log as $regel ) : ?>
					<li><?php echo esc_html( $regel ); ?></li>
				<?php endforeach; ?>
			</ul></div>
			<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">Bekijk de site</a></p>
		<?php endif; ?>
		<?php if ( $fouten ) : ?>
			<div class="notice notice-warning"><p><strong>Eerst nog doen:</strong></p><ul style="list-style:disc;margin-left:1.5em">
				<?php foreach ( $fouten as $f ) : ?>
					<li><?php echo esc_html( $f ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php else : ?>
			<form method="post">
				<?php wp_nonce_field( 'icf_install', 'icf_install_nonce' ); ?>
				<p><label><input type="checkbox" name="rooster" value="1" checked> Voorbeeldrooster toevoegen (de datums van de huidige site)</label></p>
				<p><label><input type="checkbox" name="home_opnieuw" value="1"> Homepagina's opnieuw opbouwen met de nieuwste versie van het thema (overschrijft wijzigingen aan de homepagina's in alle talen; een vorige versie blijft terug te halen via Revisies)</label></p>
				<?php submit_button( 'Installeren', 'primary large' ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}
