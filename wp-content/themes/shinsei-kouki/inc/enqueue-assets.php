<?php
/**
 * enqueue-assets.php
 * CSS, JSの読み込み管理
 */

// フォントとLCP画像のpreload（パフォーマンス最適化）
function shinsei_kouki_preload_fonts() {
    $font_dir = get_template_directory_uri() . '/assets/font';
    
    // フォントドメインへのpreconnect（DNS解決と接続を早期に確立）
    $site_url = parse_url(get_site_url(), PHP_URL_SCHEME) . '://' . parse_url(get_site_url(), PHP_URL_HOST);
    echo '<link rel="preconnect" href="' . esc_url($site_url) . '" crossorigin>';
    
    // よく使われるフォントウェイトをpreload
    // Regular (400) - 本文で使用（最も重要）
    echo '<link rel="preload" href="' . esc_url($font_dir . '/400_ZenOldMincho-Regular.woff') . '" as="font" type="font/woff" crossorigin>';
    
    // Bold (700) - 見出しで使用
    echo '<link rel="preload" href="' . esc_url($font_dir . '/700_ZenOldMincho-Bold.woff') . '" as="font" type="font/woff" crossorigin>';
    
    // LCP画像のpreload（メインビジュアル画像）
    if (is_front_page()) {
        $lcp_image = get_template_directory_uri() . '/assets/img/top/img_products.webp';
        echo '<link rel="preload" href="' . esc_url($lcp_image) . '" as="image" fetchpriority="high">';
    }
}
add_action('wp_head', 'shinsei_kouki_preload_fonts', 1);

function shinsei_kouki_enqueue_scripts() {
    // CSS
    wp_enqueue_style('normalize-style', get_template_directory_uri() . '/assets/css/common/normalize.min.css', array(), '1.0.0');
    wp_enqueue_style('shinsei-kouki-style', get_template_directory_uri() . '/assets/css/common/common.css', array('normalize-style'), '1.0.0');
    
    // トップページ用のCSS
    if (is_front_page()) {
        wp_enqueue_style('top-page-style', get_template_directory_uri() . '/assets/css/top/top.css', array('shinsei-kouki-style'), '1.0.0');
        // トップページ用のJS（カレンダーAjax更新など）
        wp_enqueue_script('top-script', get_template_directory_uri() . '/assets/js/top/top.js', array('jquery'), '1.0.0', true);
        // Ajax URLをJavaScriptに渡す
        wp_localize_script('top-script', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
    
    // 事業内容ページ用のCSS
    if (is_page('business') || is_page_template('page-business.php')) {
        wp_enqueue_style('business-style', get_template_directory_uri() . '/assets/css/business/business.css', array('shinsei-kouki-style'), '1.0.0');
    }
    
    // 会社概要ページ用のCSS
    if (is_page('company') || is_page_template('page-company.php')) {
        wp_enqueue_style('company-style', get_template_directory_uri() . '/assets/css/company/company.css', array('shinsei-kouki-style'), '1.0.0');
    }
    
    // お問い合わせページ用のCSS
    if (is_page('contact') || is_page_template('page-contact.php')) {
        wp_enqueue_style('contact-style', get_template_directory_uri() . '/assets/css/contact/contact.css', array('shinsei-kouki-style'), '1.0.0');
    }
    
    // お知らせ一覧・詳細・検索結果ページ用のCSS（トップページは除外）
    if (!is_front_page() && (is_post_type_archive('topics') || (is_single() && get_post_type() === 'topics') || (is_search() && get_query_var('post_type') === 'topics'))) {
        wp_enqueue_style('topics-style', get_template_directory_uri() . '/assets/css/topics/topics.css', array('shinsei-kouki-style'), '1.0.0');
    }

    // お知らせ詳細ページ用のJS（INDEX生成）
    if (is_single() && get_post_type() === 'topics') {
        wp_enqueue_script('topics-script', get_template_directory_uri() . '/assets/js/topics/topics.js', array(), '1.0.0', true);
    }
    
    // JS（cleanup.phpでjQueryが無効化されているので、外部jQueryを読み込む）
    // 優先度を高くして、cleanup.phpの後に実行されるようにする
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.7.0.min.js', array(), '3.7.0', true);
    wp_enqueue_script('common-script', get_template_directory_uri() . '/assets/js/common/script.js', array('jquery'), '1.0.0', true);
    
    // 事業内容ページ用のJS（タブ切り替えなど）
    if (is_page('business') || is_page_template('page-business.php')) {
        wp_enqueue_script('business-script', get_template_directory_uri() . '/assets/js/business/business.js', array('jquery'), '1.0.0', true);
    }
}
// 優先度を20に設定して、cleanup.php（優先度1）より後に実行
add_action('wp_enqueue_scripts', 'shinsei_kouki_enqueue_scripts', 20);
