<?php
/**
 * Sinxelo functions and definitions
 *
 * An updated continuation of Simppeli by Sami Keijonen (foxland.fi).
 *
 * @package Sinxelo
 */

defined( 'ABSPATH' ) || exit;

define( 'SINXELO_VERSION', '1.0.0' );

/* ==========================================================================
   Theme setup
   ========================================================================== */

function sinxelo_setup(): void {
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 750;
	}

	load_theme_textdomain( 'sinxelo', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1120, 9999, false );

	register_nav_menus( [
		'primary' => esc_html__( 'Primary Menu', 'sinxelo' ),
	] );

	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	] );

	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support( 'custom-logo', [
		'height'      => 80,
		'width'       => 80,
		'flex-height' => true,
		'flex-width'  => true,
	] );

	add_theme_support( 'post-formats', [ 'aside', 'image', 'quote', 'link' ] );

	// Block editor support (no FSE — blocks in content area only)
	add_theme_support( 'editor-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_editor_style( 'assets/css/editor-style.css' );
}
add_action( 'after_setup_theme', 'sinxelo_setup' );

/* ==========================================================================
   Enqueue scripts and styles
   ========================================================================== */

function sinxelo_scripts(): void {
	wp_enqueue_style( 'sinxelo-style', get_stylesheet_uri(), [], SINXELO_VERSION );

	wp_enqueue_script(
		'sinxelo-skip-link-focus-fix',
		get_template_directory_uri() . '/js/skip-link-focus-fix.js',
		[],
		SINXELO_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sinxelo_scripts' );

/* ==========================================================================
   Custom blocks
   ========================================================================== */

add_action( 'init', function () {
	register_block_type( __DIR__ . '/blocks/callout' );
} );

/* ==========================================================================
   Excerpt
   ========================================================================== */

function sinxelo_excerpt_more(): string {
	$text = sprintf(
		__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'sinxelo' ),
		'<span class="screen-reader-text">' . esc_html( get_the_title() ) . '</span>'
	);
	return sprintf(
		'&hellip; <p class="sinxelo-read-more"><a href="%s" class="more-link">%s</a></p>',
		esc_url( get_permalink() ),
		$text
	);
}
add_filter( 'excerpt_more', 'sinxelo_excerpt_more' );

/* ==========================================================================
   Search form
   ========================================================================== */

function sinxelo_search_form( $form ): string {
	return '<section class="search"><form role="search" method="get" id="search-form" action="' . esc_url( home_url( '/' ) ) . '">'
		. '<label class="screen-reader-text" for="s">' . esc_html__( 'Search', 'sinxelo' ) . '</label>'
		. '<input type="search" value="' . esc_attr( get_search_query() ) . '" name="s" id="s" placeholder=" " />'
		. '<input type="submit" id="searchsubmit" value="' . esc_attr__( 'Search', 'sinxelo' ) . '" />'
		. '</form></section>';
}
add_filter( 'get_search_form', 'sinxelo_search_form' );

/* ==========================================================================
   Shortcodes
   ========================================================================== */

// [callout] — backward-compat fallback for existing content using the shortcode.
// New content should use the sinxelo/callout block instead.
function sinxelo_callout_shortcode( $atts, string $content = '' ): string {
	return '<div class="callout-box">' . do_shortcode( trim( $content ) ) . '</div>';
}
add_shortcode( 'callout', 'sinxelo_callout_shortcode' );

// [searchform] — renders the theme search form inline
add_shortcode( 'searchform', function (): string {
	ob_start();
	get_search_form();
	return ob_get_clean();
} );

// [tag_cloud] — top 45 tags by count, plain text with links
function sinxelo_tag_cloud_shortcode(): string {
	$tags = get_terms( [
		'taxonomy'   => 'post_tag',
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 45,
		'hide_empty' => true,
	] );

	if ( empty( $tags ) || is_wp_error( $tags ) ) {
		return '';
	}

	$links = [];
	foreach ( $tags as $tag ) {
		$url = get_term_link( $tag );
		if ( ! is_wp_error( $url ) ) {
			$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $tag->name ) . '</a> (' . $tag->count . ')';
		}
	}
	return implode( ', ', $links );
}
add_shortcode( 'tag_cloud', 'sinxelo_tag_cloud_shortcode' );

// [category_cloud] — posts only (generic version).
// The ats-site plugin overrides this with a version that includes the podcast CPT.
function sinxelo_category_cloud_shortcode(): string {
	$categories = get_categories( [
		'orderby'    => 'count',
		'order'      => 'DESC',
		'hide_empty' => false,
	] );

	$items = [];
	foreach ( $categories as $cat ) {
		$url = get_category_link( $cat->term_id );
		if ( $cat->count > 0 && ! is_wp_error( $url ) ) {
			$items[] = '<a href="' . esc_url( $url ) . '">' . esc_html( strtolower( $cat->name ) ) . '</a> (' . $cat->count . ')';
		}
	}
	return implode( ', ', $items );
}
add_shortcode( 'category_cloud', 'sinxelo_category_cloud_shortcode' );

/* ==========================================================================
   Content filters
   ========================================================================== */

// Easy Anchors — add id attributes to H1–H3 headings for deep-linking.
// Ported from the easy-anchors plugin (Jimmy Baum).
function sinxelo_add_heading_anchors( string $content ): string {
	$used = [];
	return preg_replace_callback(
		'/<(h[1-3])([^>]*)>(.*?)<\/\1>/is',
		function ( $matches ) use ( &$used ) {
			[, $tag, $attrs, $inner] = $matches;
			if ( preg_match( '/\bid\s*=/i', $attrs ) ) {
				return $matches[0];
			}
			$slug = sanitize_title( wp_strip_all_tags( $inner ) );
			if ( empty( $slug ) ) {
				return $matches[0];
			}
			$unique = $slug;
			$count  = 2;
			while ( isset( $used[ $unique ] ) ) {
				$unique = $slug . '-' . $count++;
			}
			$used[ $unique ] = true;
			return "<{$tag} id=\"{$unique}\"{$attrs}>{$inner}</{$tag}>";
		},
		$content
	);
}
add_filter( 'the_content', 'sinxelo_add_heading_anchors', 20 );

// Archive title cleanup — strip "Category:", "Tag:", etc. prefixes.
add_filter( 'get_the_archive_title', function ( string $title ): string {
	if ( is_category() ) {
		return single_cat_title( '', false );
	}
	if ( is_tag() ) {
		return single_tag_title( '', false );
	}
	if ( is_author() ) {
		return '<span class="vcard">' . get_the_author() . '</span>';
	}
	if ( is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	return $title;
} );

/* ==========================================================================
   Disable Twemoji
   ========================================================================== */

add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
} );

/* ==========================================================================
   Includes
   ========================================================================== */

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/custom-background.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/customizer.php';
// inc/jetpack.php removed — Jetpack hooks live in the ats-site plugin.
