<?php
/**
 * Title: Waar we voor staan (visie, missie, kernwaarden)
 * Slug: icf-ede/missie-teaser
 * Categories: icf-ede
 * Description: Drie getekende kaarten met visie, missie en kernwaarden, met link naar de missiepagina.
 */

require_once get_template_directory() . '/inc/teksten.php';
$icf_beeld = get_template_directory_uri() . '/assets/illustraties/';
?>
<!-- wp:group {"align":"wide","className":"icf-missie icf-wvs","style":{"spacing":{"margin":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide icf-missie icf-wvs" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--60)"><!-- wp:paragraph {"className":"icf-label"} -->
<p class="icf-label"><?php echo icf_pt( 'missie_label' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo icf_pt( 'missie_titel' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"icf-wvs-kaarten"} -->
<div class="wp-block-columns icf-wvs-kaarten">
<?php foreach ( array( 'visie', 'missie', 'waarden' ) as $icf_k ) : ?>
<!-- wp:column {"className":"icf-wvs-kaart"} -->
<div class="wp-block-column icf-wvs-kaart"><!-- wp:image {"sizeSlug":"full","className":"icf-illustratie"} -->
<figure class="wp-block-image size-full icf-illustratie"><img src="<?php echo esc_url( $icf_beeld . $icf_k . '.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo icf_pt( $icf_k . '_kop' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo icf_pt( $icf_k . '_kort' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->

<!-- wp:paragraph {"className":"icf-wvs-link"} -->
<p class="icf-wvs-link"><a href="#icf:missie"><?php echo icf_pt( 'missie_link' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
