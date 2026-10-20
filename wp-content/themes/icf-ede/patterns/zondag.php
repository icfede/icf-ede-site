<?php
/**
 * Title: Zo ziet een zondag eruit
 * Slug: icf-ede/zondag
 * Categories: icf-ede
 * Description: Vier foto's die het verloop van een bijeenkomst laten zien.
 */

require_once get_template_directory() . '/inc/teksten.php';
$icf_stappen = array(
	1 => 'https://images.unsplash.com/photo-1561668131-a6e7450dc65e',
	2 => 'https://images.unsplash.com/photo-1666713937678-1dedb9b8f9ff',
	3 => 'https://images.unsplash.com/photo-1605627079912-97c3810a11a4',
	4 => 'https://images.unsplash.com/photo-1578496780896-7081cc23c111',
);
?>
<!-- wp:group {"align":"wide","className":"icf-zondag","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide icf-zondag" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:paragraph {"className":"icf-label"} -->
<p class="icf-label"><?php echo icf_pt( 'zondag_label' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo icf_pt( 'zondag_titel' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"icf-stappen","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40","top":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns icf-stappen" style="margin-top:var(--wp--preset--spacing--40)">
<?php foreach ( $icf_stappen as $icf_n => $icf_src ) : ?>
<!-- wp:column {"className":"icf-stap"} -->
<div class="wp-block-column icf-stap"><!-- wp:image {"sizeSlug":"large","className":"icf-foto icf-foto-staand"} -->
<figure class="wp-block-image size-large icf-foto icf-foto-staand"><img src="<?php echo esc_url( $icf_src . '?auto=format&fit=crop&w=700&h=880&q=70' ); ?>" alt="<?php echo esc_attr( icf_pt( "stap{$icf_n}_alt" ) ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo icf_pt( "stap{$icf_n}_titel" ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo icf_pt( "stap{$icf_n}_tekst" ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
