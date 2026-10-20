<?php
/**
 * Title: Komen, bidden, helpen (fotokaarten)
 * Slug: icf-ede/kaarten
 * Categories: icf-ede
 * Description: Drie kaarten met foto die uitnodigen om mee te doen.
 */

require_once get_template_directory() . '/inc/teksten.php';
$icf_kaarten = array(
	1 => array( 'https://images.unsplash.com/photo-1606788075819-9574a6edfab3', '#icf:home#bijeenkomsten' ),
	2 => array( 'https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d', '#icf:meedoen' ),
	3 => array( 'https://images.unsplash.com/photo-1569292567777-e5d61a759322', '#icf:meedoen' ),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|60"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:paragraph {"className":"icf-label"} -->
<p class="icf-label"><?php echo icf_pt( 'kaarten_label' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo icf_pt( 'kaarten_titel' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"icf-kaarten","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40","top":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns icf-kaarten" style="margin-top:var(--wp--preset--spacing--40)">
<?php foreach ( $icf_kaarten as $icf_n => $icf_k ) : ?>
<!-- wp:column {"className":"icf-kaart"} -->
<div class="wp-block-column icf-kaart"><!-- wp:image {"sizeSlug":"large","className":"icf-foto icf-foto-breed"} -->
<figure class="wp-block-image size-large icf-foto icf-foto-breed"><img src="<?php echo esc_url( $icf_k[0] . '?auto=format&fit=crop&w=800&h=560&q=70' ); ?>" alt="<?php echo esc_attr( icf_pt( "k{$icf_n}_alt" ) ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"icf-kaart-tekst","layout":{"type":"default"}} -->
<div class="wp-block-group icf-kaart-tekst"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo icf_pt( "k{$icf_n}_titel" ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo icf_pt( "k{$icf_n}_tekst" ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"icf-pijl"} -->
<p class="icf-pijl"><a href="<?php echo esc_attr( $icf_k[1] ); ?>"><?php echo icf_pt( "k{$icf_n}_link" ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
