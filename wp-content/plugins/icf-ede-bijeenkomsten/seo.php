<?php
/**
 * Zoekmachines en delen: omschrijving per pagina, voorvertoning bij delen
 * (WhatsApp, Facebook) en kerkgegevens voor Google.
 *
 * @package icf-ede
 */

defined( 'ABSPATH' ) || exit;

/** Omschrijving per pagina-adres (slug), in de taal van die pagina. */
function icf_omschrijvingen() {
	$home    = array(
		'nl' => 'Interculturele christelijke gemeente in Ede. Om de twee weken op zondagmiddag samen vieren, met koffie vooraf en een maaltijd na afloop. Iedereen is welkom.',
		'en' => 'Intercultural Christian church in Ede, the Netherlands. Every other Sunday afternoon we worship together, with coffee beforehand and a shared meal afterwards. All are welcome.',
		'fa' => 'کلیسای مسیحی بین‌فرهنگی در شهر اِده. هر دو هفته یک‌بار، یکشنبه بعدازظهر با هم عبادت می‌کنیم؛ پیش از آن چای و قهوه و بعد از آن غذای مشترک. همه خوش آمدند.',
		'ar' => 'كنيسة مسيحية متعددة الثقافات في مدينة إده. نجتمع مرة كل أسبوعين بعد ظهر الأحد للعبادة، مع القهوة قبل الاجتماع ووجبة مشتركة بعده. الجميع مرحَّب بهم.',
	);
	$meedoen = array(
		'nl' => 'Doe mee met ICF Ede: kom naar een bijeenkomst, bid mee, help in de keuken, bij de kinderen of met vervoer vanaf het AZC, of steun ons met een gift.',
		'en' => 'Get involved with ICF Ede: join a meeting, pray with us, help in the kitchen, with the children or with transport from the asylum centre, or support us with a gift.',
		'fa' => 'با ICF Ede همراه شوید: به جلسات بیایید، با ما دعا کنید، در آشپزخانه، با کودکان یا در رفت‌وآمد از کمپ پناهندگان کمک کنید، یا با هدیه از ما حمایت کنید.',
		'ar' => 'شارِك مع ICF Ede: احضر اجتماعاتنا، صلِّ معنا، ساعد في المطبخ أو مع الأطفال أو في النقل من مركز اللجوء، أو ادعمنا بتبرّع.',
	);
	$missie  = array(
		'nl' => 'Visie, missie en kernwaarden van ICF Ede: een gastvrije, interculturele christelijke gemeenschap waar mensen uit alle culturen Christus ontmoeten en groeien in geloof.',
		'en' => 'Vision, mission and core values of ICF Ede: a welcoming, intercultural Christian community where people of every culture meet Christ and grow in faith.',
		'fa' => 'چشم‌انداز، مأموریت و ارزش‌های ICF Ede: جامعه‌ای مسیحی، مهمان‌نواز و بین‌فرهنگی که در آن مردم از همهٔ فرهنگ‌ها مسیح را می‌شناسند و در ایمان رشد می‌کنند.',
		'ar' => 'رؤية ICF Ede ورسالتها وقيمها: جماعة مسيحية مضيافة متعددة الثقافات، يلتقي فيها أناس من كل الثقافات بالمسيح وينمون في الإيمان.',
	);
	$contact = array(
		'nl' => 'Neem contact op met ICF Ede via contact@icfede.nl. We komen samen in de Paasbergschool, Kleefsehoek 1A in Ede. Met gegevens van de stichting en jaarverslagen.',
		'en' => 'Contact ICF Ede at contact@icfede.nl. We meet at the Paasbergschool, Kleefsehoek 1A in Ede. Includes foundation details and annual reports.',
		'fa' => 'تماس با ICF Ede از طریق contact@icfede.nl. محل جلسات: مدرسهٔ Paasbergschool، نشانی Kleefsehoek 1A در اِده. همراه با مشخصات بنیاد و گزارش‌های سالانه.',
		'ar' => 'تواصل مع ICF Ede عبر contact@icfede.nl. نجتمع في مدرسة Paasbergschool، العنوان Kleefsehoek 1A في إده. مع بيانات المؤسسة والتقارير السنوية.',
	);
	return array(
		'home'         => $home['nl'],
		'en-home'      => $home['en'],
		'fa-home'      => $home['fa'],
		'ar-home'      => $home['ar'],
		'meedoen'      => $meedoen['nl'],
		'get-involved' => $meedoen['en'],
		'fa-hamrahi'   => $meedoen['fa'],
		'ar-musharaka' => $meedoen['ar'],
		'missie'       => $missie['nl'],
		'mission'      => $missie['en'],
		'fa-mamuriat'  => $missie['fa'],
		'ar-risala'    => $missie['ar'],
		'contact'      => $contact['nl'],
		'en-contact'   => $contact['en'],
		'fa-tamas'     => $contact['fa'],
		'ar-ittisal'   => $contact['ar'],
	);
}

/** Omschrijving van de huidige pagina, of leeg. */
function icf_huidige_omschrijving() {
	if ( ! is_singular( 'page' ) ) {
		return '';
	}
	$post = get_queried_object();
	if ( ! $post || ( function_exists( 'icf_is_intern' ) && icf_is_intern( $post ) ) ) {
		return '';
	}
	$alle = icf_omschrijvingen();
	if ( isset( $alle[ $post->post_name ] ) ) {
		return $alle[ $post->post_name ];
	}
	if ( has_excerpt( $post ) ) {
		return wp_strip_all_tags( get_the_excerpt( $post ) );
	}
	return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
}

add_action(
	'wp_head',
	function () {
		if ( is_admin() || is_404() || is_search() ) {
			return;
		}
		$post  = is_singular() ? get_queried_object() : null;
		$titel = wp_get_document_title();
		$tekst = icf_huidige_omschrijving();
		$url   = $post ? get_permalink( $post ) : home_url( '/' );
		$beeld = get_template_directory_uri() . '/assets/images/delen.png';
		$taal  = function_exists( 'pll_current_language' ) ? pll_current_language( 'locale' ) : get_locale();

		echo "\n<!-- ICF Ede: omschrijving en delen -->\n";
		if ( $tekst ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $tekst ) );
		}
		$og = array(
			'og:type'         => 'website',
			'og:site_name'    => 'ICF Ede',
			'og:title'        => $titel,
			'og:description'  => $tekst,
			'og:url'          => $url,
			'og:locale'       => $taal,
			'og:image'        => $beeld,
			'og:image:width'  => '1200',
			'og:image:height' => '630',
			'og:image:alt'    => 'ICF Ede',
		);
		foreach ( $og as $eigenschap => $waarde ) {
			if ( '' !== (string) $waarde ) {
				printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $eigenschap ), esc_attr( $waarde ) );
			}
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

		/* Kerkgegevens voor Google: op de homepagina van elke taal. */
		$is_home = is_front_page() || ( $post && in_array( $post->post_name, array( 'home', 'en-home', 'fa-home', 'ar-home' ), true ) );
		if ( ! $is_home ) {
			return;
		}
		$adres = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Kleefsehoek 1A',
			'postalCode'      => '6711 SP',
			'addressLocality' => 'Ede',
			'addressCountry'  => 'NL',
		);
		$plek  = array(
			'@type'   => 'Place',
			'name'    => 'Paasbergschool',
			'address' => $adres,
		);
		$org   = array(
			'@type'       => 'Organization',
			'@id'         => home_url( '/#organisatie' ),
			'name'        => 'ICF Ede',
			'alternateName' => 'Interculturele Christelijke Familie Ede',
			'legalName'   => 'Stichting ICF Ede',
			'url'         => home_url( '/' ),
			'logo'        => get_template_directory_uri() . '/assets/logo/logo.png',
			'email'       => 'contact@icfede.nl',
			'description' => $tekst,
			'location'    => $plek,
		);
		$graph = array( $org );

		if ( defined( 'ICF_CPT' ) ) {
			$komend = get_posts(
				array(
					'post_type'      => ICF_CPT,
					'posts_per_page' => 6,
					'meta_key'       => '_icf_datum',
					'orderby'        => 'meta_value',
					'order'          => 'ASC',
					'meta_query'     => array(
						array( 'key' => '_icf_datum', 'value' => wp_date( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
					),
				)
			);
			foreach ( $komend as $b ) {
				$datum = get_post_meta( $b->ID, '_icf_datum', true );
				$tijd  = get_post_meta( $b->ID, '_icf_tijd', true );
				$waar  = get_post_meta( $b->ID, '_icf_locatie', true );
				$opm   = get_post_meta( $b->ID, '_icf_opmerking', true );
				if ( ! $datum ) {
					continue;
				}
				try {
					$start = new DateTimeImmutable( $datum . ' ' . ( $tijd ? $tijd : '15:45' ), wp_timezone() );
				} catch ( Exception $e ) {
					continue;
				}
				$locatie = ( '' === $waar || false !== stripos( $waar, 'paasberg' ) ) ? $plek : array(
					'@type' => 'Place',
					'name'  => $waar,
					'address' => array( '@type' => 'PostalAddress', 'addressLocality' => 'Ede', 'addressCountry' => 'NL' ),
				);
				$naam = 'Samenkomst ICF Ede' . ( $opm ? ' – ' . $opm : '' );
				$graph[] = array(
					'@type'               => 'Event',
					'name'                => $naam,
					'startDate'           => $start->format( 'c' ),
					'eventStatus'         => ( false !== stripos( (string) $opm, 'niet door' ) ) ? 'https://schema.org/EventCancelled' : 'https://schema.org/EventScheduled',
					'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
					'location'            => $locatie,
					'organizer'           => array( '@id' => home_url( '/#organisatie' ) ),
					'image'               => $beeld,
					'description'         => $tekst,
					'isAccessibleForFree' => true,
				);
			}
		}

		echo '<script type="application/ld+json">' . wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . "</script>\n";
	},
	5
);
