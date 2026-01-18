<?php
/**
 * enqueue-assets.php
 * CSS, JSの読み込み管理
 */

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
    
    // お知らせ一覧・詳細ページ用のCSS（トップページは除外）
    if (!is_front_page() && (is_home() || is_category() || is_tag() || is_single())) {
        wp_enqueue_style('news-style', get_template_directory_uri() . '/assets/css/news/news.css', array('shinsei-kouki-style'), '1.0.0');
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
