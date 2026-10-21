<?php
/**
 * Title: Komende bijeenkomsten
 * Slug: icf-ede/bijeenkomsten
 * Categories: icf-ede
 * Description: Adres plus het automatische rooster (beheer via Bijeenkomsten in het menu).
 */

require_once get_template_directory() . '/inc/teksten.php';
?>
<!-- wp:group {"align":"full","anchor":"bijeenkomsten","backgroundColor":"creme","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"margin":{"top":"0"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div id="bijeenkomsten" class="wp-block-group alignfull has-creme-background-color has-background" style="margin-top:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60","top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"42%"} -->
<div class="wp-block-column" style="flex-basis:42%"><!-- wp:paragraph {"className":"icf-label"} -->
<p class="icf-label"><?php echo icf_pt( 'rooster_label' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo icf_pt( 'rooster_titel' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo icf_pt( 'rooster_tekst' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:image {"sizeSlug":"large","className":"icf-foto icf-foto-breed"} -->
<figure class="wp-block-image size-large icf-foto icf-foto-breed"><img src="https://images.unsplash.com/photo-1576867757603-05b134ebc379?auto=format&amp;fit=crop&amp;w=900&amp;h=600&amp;q=70" alt="<?php echo esc_attr( icf_pt( 'rooster_alt' ) ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p><strong>Paasbergschool</strong><br>Kleefsehoek 1A, 6711 SP Ede<br><a href="https://maps.google.com/?q=Kleefsehoek+1A+Ede"><?php echo icf_pt( 'route' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"58%"} -->
<div class="wp-block-column" style="flex-basis:58%"><!-- wp:shortcode -->
[icf_bijeenkomsten aantal="8"]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
