<?php
/**
 * common_theme.php
 * テーマの土台：サポート・不要機能の削除・CSS/JS読み込み
 */

// =============================================================================
// テーマサポート・管理メニュー
// =============================================================================

add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
});

function shinsei_kouki_remove_admin_menus() {
    remove_menu_page('edit.php'); // 投稿
}
add_action('admin_menu', 'shinsei_kouki_remove_admin_menus');

// =============================================================================
// 不要なWP機能の削除・無効化（cleanup）
// =============================================================================

if (is_admin() && !defined('CONCATENATE_SCRIPTS')) {
    define('CONCATENATE_SCRIPTS', true);
}

add_filter('show_admin_bar', '__return_false');

add_filter('body_class', function($classes) {
    if (is_404()) {
        $classes = array_values(array_diff($classes, array('error404')));
    }
    return $classes;
}, 10, 1);

remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('wp_head', 'wp_robots', 1);

add_action('after_setup_theme', function() {
    if (!is_admin()) {
        remove_action('wp_head', 'wp_img_auto_sizes_contain', 10);
    }
}, 1);

add_action('init', function() {
    if (!is_admin()) {
        remove_action('wp_head', 'wp_img_auto_sizes_contain', 10);
    }
}, 1);

add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        wp_dequeue_script('wp-emoji');
        wp_deregister_script('wp-emoji');
        remove_action('wp_head', 'wpemoji_script', 7);
        remove_action('wp_print_styles', 'wpemoji_styles', 10);
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
        wp_dequeue_style('wp-block-library');
        wp_deregister_style('wp-block-library');
        wp_dequeue_style('global-styles');
        wp_deregister_style('global-styles');
        wp_dequeue_style('classic-theme-styles');
        wp_deregister_style('classic-theme-styles');
        add_filter('wp_calculate_image_sizes', '__return_false', 999);
        add_filter('wp_img_tag_add_width_and_height_attr', '__return_false', 999);
        wp_deregister_script('jquery');
        wp_deregister_script('jquery-core');
        wp_deregister_script('jquery-migrate');
    }
}, 1);

add_filter('style_loader_tag', function($html, $handle) {
    if (is_admin()) {
        return $html;
    }
    $remove_handles = array(
        'wp-emoji-styles',
        'wp-block-library',
        'global-styles',
        'classic-theme-styles',
        'wp-img-auto-sizes-contain'
    );
    if (in_array($handle, $remove_handles)) {
        return '';
    }
    $html = preg_replace('/\s+id=[\'"][^\'"]*[\'"]/', '', $html);
    return $html;
}, 10, 2);

add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        remove_action('wp_head', 'wp_print_speculation_rules', 1);
    }
}, 1);

add_action('template_redirect', function() {
    if (!is_admin()) {
        ob_start(function($buffer) {
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-img-auto-sizes-contain-inline-css[\'"][^>]*>.*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-img-auto-sizes-contain-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]global-styles-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-library-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]classic-theme-styles-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-emoji-styles-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-button-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-heading-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-buttons-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-paragraph-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]core-block-supports-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<link[^>]*rel\s*=\s*[\'"]dns-prefetch[\'"][^>]*>/i', '', $buffer);
            $buffer = preg_replace('/<script[^>]*type\s*=\s*[\'"]speculationrules[\'"][^>]*>[\s\S]*?<\/script>/is', '', $buffer);
            if (preg_match('/(<head[^>]*>)([\s\S]*?)(<\/head>)/i', $buffer, $matches)) {
                $head_start = $matches[1];
                $head_content = $matches[2];
                $head_end = $matches[3];
                $tags = array();
                preg_match_all('/<meta[^>]*(?:\/>|>)/i', $head_content, $meta_matches);
                if (!empty($meta_matches[0])) {
                    $tags = array_merge($tags, $meta_matches[0]);
                }
                preg_match_all('/<title[^>]*>.*?<\/title>/is', $head_content, $title_matches);
                if (!empty($title_matches[0])) {
                    $tags = array_merge($tags, $title_matches[0]);
                }
                preg_match_all('/<link[^>]*\/?>/i', $head_content, $link_matches);
                if (!empty($link_matches[0])) {
                    $tags = array_merge($tags, $link_matches[0]);
                }
                preg_match_all('/<script[^>]*>.*?<\/script>/is', $head_content, $script_matches);
                if (!empty($script_matches[0])) {
                    $tags = array_merge($tags, $script_matches[0]);
                }
                preg_match_all('/<style[^>]*>.*?<\/style>/is', $head_content, $style_matches);
                if (!empty($style_matches[0])) {
                    $tags = array_merge($tags, $style_matches[0]);
                }
                $formatted_content = "\n";
                foreach ($tags as $tag) {
                    $tag = preg_replace('/\s+/', ' ', $tag);
                    $tag = trim($tag);
                    $formatted_content .= "\t" . $tag . "\n";
                }
                $buffer = str_replace($matches[0], $head_start . $formatted_content . $head_end, $buffer);
            }
            return $buffer;
        });
    }
}, 1);

add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    }
}, 1);

// =============================================================================
// CSS / JS 読み込み（enqueue-assets）
// =============================================================================

function shinsei_kouki_preload_fonts() {
    if (is_admin()) {
        return;
    }
    $font_dir = get_template_directory_uri() . '/assets/font';
    $site_url = parse_url(get_site_url(), PHP_URL_SCHEME) . '://' . parse_url(get_site_url(), PHP_URL_HOST);
    echo '<link rel="preconnect" href="' . esc_url($site_url) . '" crossorigin>';
    echo '<link rel="preload" href="' . esc_url($font_dir . '/400_ZenOldMincho-Regular.woff') . '" as="font" type="font/woff" crossorigin>';
    echo '<link rel="preload" href="' . esc_url($font_dir . '/700_ZenOldMincho-Bold.woff') . '" as="font" type="font/woff" crossorigin>';
    if (is_front_page()) {
        $lcp_image = get_template_directory_uri() . '/assets/img/top/img_products.webp';
        echo '<link rel="preload" href="' . esc_url($lcp_image) . '" as="image" fetchpriority="high">';
    }
}
add_action('wp_head', 'shinsei_kouki_preload_fonts', 1);

function shinsei_kouki_enqueue_scripts() {
    if (is_admin()) {
        return;
    }
    wp_enqueue_style('normalize-style', get_template_directory_uri() . '/assets/css/common/normalize.min.css', array(), '1.0.0');
    wp_enqueue_style('shinsei-kouki-style', get_template_directory_uri() . '/assets/css/common/common.css', array('normalize-style'), '1.0.0');
    if (is_front_page()) {
        wp_enqueue_style('top-page-style', get_template_directory_uri() . '/assets/css/top/top.css', array('shinsei-kouki-style'), '1.0.0');
        wp_enqueue_script('top-script', get_template_directory_uri() . '/assets/js/top/top.js', array('jquery'), '1.0.0', true);
        wp_localize_script('top-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }
    if (is_page('business') || is_page_template('page-business.php')) {
        wp_enqueue_style('business-style', get_template_directory_uri() . '/assets/css/business/business.css', array('shinsei-kouki-style'), '1.0.0');
    }
    if (is_page('company') || is_page_template('page-company.php')) {
        wp_enqueue_style('company-style', get_template_directory_uri() . '/assets/css/company/company.css', array('shinsei-kouki-style'), '1.0.0');
    }
    if (is_page('privacy-policy') || is_page_template('page-privacy-policy.php')) {
        wp_enqueue_style('privacy-style', get_template_directory_uri() . '/assets/css/privacy/privacy.css', array('shinsei-kouki-style'), '1.0.0');
    }
    if (is_page('contact') || is_page_template('page-contact.php')) {
        wp_enqueue_style('contact-style', get_template_directory_uri() . '/assets/css/contact/contact.css', array('shinsei-kouki-style'), '1.0.0');
        wp_enqueue_script('contact-script', get_template_directory_uri() . '/assets/js/contact/contact.js', array('jquery'), '1.0.0', true);
    }
    if (!is_front_page() && (is_post_type_archive('topics') || (is_single() && get_post_type() === 'topics') || (is_search() && get_query_var('post_type') === 'topics'))) {
        wp_enqueue_style('topics-style', get_template_directory_uri() . '/assets/css/topics/topics.css', array('shinsei-kouki-style'), '1.0.0');
    }
    if (is_single() && get_post_type() === 'topics') {
        wp_enqueue_script('topics-script', get_template_directory_uri() . '/assets/js/topics/topics.js', array(), '1.0.0', true);
    }
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.7.0.min.js', array(), '3.7.0', true);
    wp_enqueue_script('common-script', get_template_directory_uri() . '/assets/js/common/script.js', array('jquery'), '1.0.0', true);
    if (is_page('business') || is_page_template('page-business.php')) {
        wp_enqueue_script('business-script', get_template_directory_uri() . '/assets/js/business/business.js', array('jquery'), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'shinsei_kouki_enqueue_scripts', 20);
