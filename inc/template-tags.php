<?php
/**
 * Reusable template output helpers.
 *
 * @package NexusForge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'nexusforge_posted_on' ) ) {
	/**
	 * Print HTML with meta information for the current post date.
	 */
	function nexusforge_posted_on() {
		$time_string = sprintf(
			'<time class="entry-date published" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);

		printf(
			'<span class="posted-on">%s</span>',
			wp_kses_post( $time_string )
		);
	}
}

if ( ! function_exists( 'nexusforge_posted_by' ) ) {
	/**
	 * Print HTML with meta information for the current author.
	 */
	function nexusforge_posted_by() {
		printf(
			'<span class="byline">%1$s <a class="url fn n" href="%2$s">%3$s</a></span>',
			esc_html__( 'by', 'nexusforge' ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
}

if ( ! function_exists( 'nexusforge_reading_time' ) ) {
	/**
	 * Estimate reading time in minutes for the current post.
	 *
	 * @return int
	 */
	function nexusforge_reading_time() {
		$words   = str_word_count( wp_strip_all_tags( get_the_content() ) );
		$minutes = (int) max( 1, round( $words / 200 ) );
		return $minutes;
	}
}

if ( ! function_exists( 'nexusforge_entry_meta' ) ) {
	/**
	 * Print the combined entry meta line (category, date, reading time).
	 */
	function nexusforge_entry_meta() {
		$categories = get_the_category_list( esc_html__( ', ', 'nexusforge' ) );
		echo '<div class="entry-meta">';
		if ( $categories ) {
			echo '<span class="entry-meta__cat">' . wp_kses_post( $categories ) . '</span>';
		}
		nexusforge_posted_on();
		printf(
			'<span class="entry-meta__time">%s</span>',
			sprintf(
				/* translators: %d: reading time in minutes. */
				esc_html__( '%d min read', 'nexusforge' ),
				(int) nexusforge_reading_time()
			)
		);
		echo '</div>';
	}
}

if ( ! function_exists( 'nexusforge_post_thumbnail' ) ) {
	/**
	 * Output the post thumbnail with a graceful fallback placeholder.
	 *
	 * @param string $size Image size.
	 */
	function nexusforge_post_thumbnail( $size = 'nexusforge-card' ) {
		if ( post_password_required() || is_attachment() ) {
			return;
		}

		if ( has_post_thumbnail() ) {
			echo '<div class="entry-media">';
			the_post_thumbnail(
				$size,
				array(
					'loading' => 'lazy',
					'alt'     => the_title_attribute( array( 'echo' => false ) ),
				)
			);
			echo '</div>';
		} else {
			echo '<div class="entry-media entry-media--placeholder" aria-hidden="true"></div>';
		}
	}
}

if ( ! function_exists( 'nexusforge_pagination' ) ) {
	/**
	 * Output accessible numbered pagination.
	 */
	function nexusforge_pagination() {
		the_posts_pagination(
			array(
				'mid_size'           => 1,
				'prev_text'          => esc_html__( 'Previous', 'nexusforge' ),
				'next_text'          => esc_html__( 'Next', 'nexusforge' ),
				'screen_reader_text' => esc_html__( 'Posts navigation', 'nexusforge' ),
				'aria_label'         => esc_html__( 'Posts', 'nexusforge' ),
				'class'              => 'nexusforge-pagination',
			)
		);
	}
}
