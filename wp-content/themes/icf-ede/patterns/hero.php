<?php
/**
 * Title: Welkom (fotobanner)
 * Slug: icf-ede/hero
 * Categories: icf-ede
 * Description: Grote foto over de volle breedte met welkomsttekst. Vervang de foto via het blok "Omslag".
 */

require_once get_template_directory() . '/inc/teksten.php';
$icf_img = 'https://images.unsplash.com/photo-1679579664774-da4283cab147?auto=format&fit=crop&w=2000&q=70';
?>
<!-- wp:cover {"url":"<?php echo esc_url( $icf_img ); ?>","dimRatio":70,"customGradient":"linear-gradient(180deg,rgba(35,54,42,0.15) 0%,rgba(35,54,42,0.55) 45%,rgba(35,54,42,0.92) 100%)","minHeight":640,"contentPosition":"bottom left","isDark":true,"align":"full","className":"icf-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"margin":{"top":"0"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<div class="wp-block-cover alignfull is-dark has-custom-content-position is-position-bottom-left icf-hero" style="margin-top:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:640px"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( $icf_img ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-70 has-background-dim has-background-gradient" style="background:linear-gradient(180deg,rgba(35,54,42,0.15) 0%,rgba(35,54,42,0.55) 45%,rgba(35,54,42,0.92) 100%)"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"className":"icf-label"} -->
<p class="icf-label"><?php echo icf_pt( 'hero_label' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"icf-hero-titel"} -->
<h1 class="wp-block-heading icf-hero-titel"><?php echo icf_pt( 'hero_titel' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"icf-hero-tekst","fontSize":"groot"} -->
<p class="icf-hero-tekst has-groot-font-size"><?php echo icf_pt( 'hero_tekst' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->
