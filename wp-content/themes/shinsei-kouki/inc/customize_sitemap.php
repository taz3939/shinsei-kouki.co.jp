<?php
/**
 * customize_sitemap.php
 * WordPress 標準サイトマップ（wp-sitemap.xml）の出力調整
 * 固定ページ（page）とお知らせ（topics）のみ出力し、投稿・ユーザー・タクソノミーは除外する。
 */

if (!defined('ABSPATH')) {
	exit;
}

/* 投稿タイプ「post」をサイトマップから除外（page / topics のみ残す） */
add_filter('wp_sitemaps_post_types', function($post_types) {
	unset($post_types['post']);
	return $post_types;
});

/* ユーザー（著者）サイトマップを除外 */
add_filter('wp_sitemaps_add_provider', function($provider, $name) {
	if ($name === 'users') {
		return false;
	}
	return $provider;
}, 10, 2);

/* タクソノミー（カテゴリー等）サイトマップを除外 */
add_filter('wp_sitemaps_taxonomies', function($taxonomies) {
	unset($taxonomies['category']);
	return $taxonomies;
});
