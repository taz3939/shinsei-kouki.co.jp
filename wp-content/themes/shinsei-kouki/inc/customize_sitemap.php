<?php
/**
 * customize_sitemap.php
 * /sitemap.xml をテーマ側で動的出力する。
 */

if (!defined('ABSPATH')) {
	exit;
}

// テーマ実装の /sitemap.xml を優先するため、WP標準サイトマップ機能を無効化
add_filter('wp_sitemaps_enabled', '__return_false');

add_filter('query_vars', function($vars) {
	$vars[] = 'shinsei_kouki_sitemap';
	return $vars;
});

add_action('init', function() {
	add_rewrite_rule('sitemap\.xml$', 'index.php?shinsei_kouki_sitemap=1', 'top');
	add_rewrite_rule('sitemap\.xml/?$', 'index.php?shinsei_kouki_sitemap=1', 'top');
});

add_filter('redirect_canonical', function($redirect_url, $requested_url) {
	if ($redirect_url && strpos((string) parse_url($requested_url, PHP_URL_PATH), 'sitemap.xml') !== false) {
		return false;
	}
	return $redirect_url;
}, 10, 2);

add_action('template_redirect', function() {
	if (1 !== (int) get_query_var('shinsei_kouki_sitemap')) {
		return;
	}
	shinsei_kouki_output_sitemap_xml();
	exit;
});

/**
 * サイトマップXMLを出力
 */
function shinsei_kouki_output_sitemap_xml() {
	header('Content-Type: application/xml; charset=UTF-8');
	header('X-Robots-Tag: noindex', true);

	$urls = array();

	$urls[] = array(
		'loc'      => home_url('/'),
		'lastmod'  => null,
		'priority' => '1.0',
	);

	$pages = get_posts(array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	));
	foreach ($pages as $page) {
		$urls[] = array(
			'loc'      => get_permalink($page),
			'lastmod'  => get_post_modified_time('Y-m-d\TH:i:s+09:00', true, $page),
			'priority' => '0.8',
		);
	}

	$archive_url = get_post_type_archive_link('topics');
	if ($archive_url) {
		$urls[] = array(
			'loc'      => $archive_url,
			'lastmod'  => null,
			'priority' => '0.8',
		);
	}

	$posts = get_posts(array(
		'post_type'      => 'topics',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'modified',
		'order'          => 'DESC',
	));
	foreach ($posts as $post) {
		$urls[] = array(
			'loc'      => get_permalink($post),
			'lastmod'  => get_post_modified_time('Y-m-d\TH:i:s+09:00', true, $post),
			'priority' => '0.6',
		);
	}

	$unique_urls = array();
	foreach ($urls as $url) {
		$loc = user_trailingslashit( $url['loc'] );
		$url['loc'] = $loc;
		$unique_urls[ $loc ] = $url;
	}

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach ($unique_urls as $url) {
		echo '  <url>' . "\n";
		echo '    <loc>' . esc_url($url['loc']) . '</loc>' . "\n";
		if (!empty($url['lastmod'])) {
			echo '    <lastmod>' . esc_html($url['lastmod']) . '</lastmod>' . "\n";
		}
		if (isset($url['priority'])) {
			echo '    <priority>' . esc_html($url['priority']) . '</priority>' . "\n";
		}
		echo '  </url>' . "\n";
	}
	echo '</urlset>';
}
