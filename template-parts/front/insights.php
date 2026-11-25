<?php
/**
 * Front page — latest posts preview (theme starter fallback).
 *
 * Renders only when published posts exist, so a fresh install stays clean.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nexusforge_recent = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( $nexusforge_recent->have_posts() ) :
	?>
	<section class="nexusforge-section nexusforge-band--surface">
		<div class="nexusforge-container">
			<header class="nexusforge-section__head" style="display:flex;justify-content:space-between;align-items:flex-end;gap:var(--nexusforge-space-4);flex-wrap:wrap;">
				<div>
					<p class="nexusforge-eyebrow"><?php esc_html_e( 'Insights', 'nexusforge' ); ?></p>
					<h2 style="margin:0;"><?php esc_html_e( 'From the field', 'nexusforge' ); ?></h2>
				</div>
				<a class="nexusforge-btn nexusforge-btn--ghost" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' ) ); ?>">
					<?php esc_html_e( 'All articles', 'nexusforge' ); ?>
				</a>
			</header>

			<div class="nexusforge-grid" style="margin-top:var(--nexusforge-space-6);">
				<?php
				while ( $nexusforge_recent->have_posts() ) :
					$nexusforge_recent->the_post();
					?>
					<article class="nexusforge-card">
						<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php nexusforge_post_thumbnail( 'nexusforge-card' ); ?>
						</a>
						<div class="nexusforge-card__body">
							<?php
							$nexusforge_cat = get_the_category();
							if ( ! empty( $nexusforge_cat ) ) :
								?>
								<p class="nexusforge-eyebrow" style="margin-bottom:var(--nexusforge-space-2);"><?php echo esc_html( $nexusforge_cat[0]->name ); ?></p>
							<?php endif; ?>
							<h3 class="nexusforge-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="nexusforge-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
endif;
