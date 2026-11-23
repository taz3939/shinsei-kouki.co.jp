<?php
/**
 * customize_robots.php
 * robots.txt の出力カスタマイズ（WordPress 標準の仮想 robots.txt に Sitemap を追加）
 */

if (!defined('ABSPATH')) {
	exit;
}

add_filter('robots_txt', function($output, $public) {
	if (!$public) {
		return $output;
	}
	if (strpos($output, 'Sitemap:') === false) {
		$output .= "\nSitemap: " . esc_url(home_url('/sitemap.xml')) . "\n";
	}
	return $output;
}, 10, 2);
