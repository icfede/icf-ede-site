<?php
/**
 * Title: Geven (kort: knop, QR en IBAN)
 * Slug: icf-ede/geven-kort
 * Categories: icf-ede
 * Description: Compacte versie van Geven voor in een tegel: betaalknop, QR-code en rekeningnummer, zonder kop.
 */

require_once get_template_directory() . '/inc/teksten.php';
$icf_betaal = 'https://betaalverzoek.rabobank.nl/betaalverzoek/?id=xVZttsc1SC2WOnYQIsYsxw';
?>
<!-- wp:group {"className":"icf-geven-kort","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group icf-geven-kort"><!-- wp:html -->
<a href="<?php echo esc_url( $icf_betaal ); ?>" target="_blank" rel="noopener"><img class="icf-qr" src="https://icfede.nl/assets/images/qr-code.svg" alt="<?php echo esc_attr( icf_pt( 'geven_qr_alt' ) ); ?>" width="112" height="112"></a>
<!-- /wp:html -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $icf_betaal ); ?>" target="_blank" rel="noreferrer noopener"><?php echo icf_pt( 'geven_knop' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"fontSize":"klein"} -->
<p class="has-klein-font-size"><?php echo icf_pt( 'geven_iban' ); ?></p>
<!-- /wp:paragraph -->
