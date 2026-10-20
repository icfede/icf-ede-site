<?php
/**
 * Title: Geven (QR en IBAN)
 * Slug: icf-ede/geven
 * Categories: icf-ede
 * Description: Oproep om te geven, met Rabobank-betaalverzoek, QR-code en rekeningnummer.
 */

require_once get_template_directory() . '/inc/teksten.php';
$icf_betaal = 'https://betaalverzoek.rabobank.nl/betaalverzoek/?id=xVZttsc1SC2WOnYQIsYsxw';
?>
<!-- wp:group {"align":"wide","className":"icf-geven","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide icf-geven" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"220px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:220px"><!-- wp:html -->
<a href="<?php echo esc_url( $icf_betaal ); ?>" target="_blank" rel="noopener"><img class="icf-qr" src="https://icfede.nl/assets/images/qr-code.svg" alt="<?php echo esc_attr( icf_pt( 'geven_qr_alt' ) ); ?>" width="168" height="168"></a>
<!-- /wp:html --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo icf_pt( 'geven_titel' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo icf_pt( 'geven_tekst' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $icf_betaal ); ?>" target="_blank" rel="noreferrer noopener"><?php echo icf_pt( 'geven_knop' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph {"fontSize":"klein"} -->
<p class="has-klein-font-size"><?php echo icf_pt( 'geven_iban' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
