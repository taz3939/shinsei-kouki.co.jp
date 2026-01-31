<?php
/**
 * cleanup.php
 * 不要なWP機能の削除やオートフォーマット関連の設定
 */

// 管理バーを非表示（フロントエンドでの表示を無効化）
add_filter('show_admin_bar', '__return_false');

// ヘッダーから不要な要素を削除
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

// 画像の自動サイズ調整のインラインスタイルを削除（複数のタイミングで実行）
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

// フロントエンドで不要なスクリプトとスタイルを削除
add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        // wp-emoji関連
        wp_dequeue_script('wp-emoji');
        wp_deregister_script('wp-emoji');
        remove_action('wp_head', 'wpemoji_script', 7);
        remove_action('wp_print_styles', 'wpemoji_styles', 10);
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        
        // dashicons（管理画面用アイコン）
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
        
        // ブロックライブラリ関連
        wp_dequeue_style('wp-block-library');
        wp_deregister_style('wp-block-library');
        wp_dequeue_style('global-styles');
        wp_deregister_style('global-styles');
        wp_dequeue_style('classic-theme-styles');
        wp_deregister_style('classic-theme-styles');
        
        // 画像の自動サイズ調整を無効化
        add_filter('wp_calculate_image_sizes', '__return_false', 999);
        add_filter('wp_img_tag_add_width_and_height_attr', '__return_false', 999);
        
        // WordPressのデフォルトjQueryを無効化（外部jQueryを使用するため）
        wp_deregister_script('jquery');
        wp_deregister_script('jquery-core');
        wp_deregister_script('jquery-migrate');
    }
}, 1);

// インラインスタイルを削除（管理画面ではブロックエディタ用CSSを除去しない）
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

    // スタイルの link タグから id 属性を削除（HTML をシンプルに）
    $html = preg_replace('/\s+id=[\'"][^\'"]*[\'"]/', '', $html);

    return $html;
}, 10, 2);

// speculationrulesスクリプトを削除
add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        // speculationrules（prefetch関連）を無効化
        remove_action('wp_head', 'wp_print_speculation_rules', 1);
    }
}, 1);

// head内のインラインスタイルを削除（出力バッファリング方式 - ページ全体）
add_action('template_redirect', function() {
    if (!is_admin()) {
        ob_start(function($buffer) {
            // 不要なインラインスタイルを削除（シングルクォート・ダブルクォート両方に対応）
            // wp-img-auto-sizes-contain-inline-cssを削除
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-img-auto-sizes-contain-inline-css[\'"][^>]*>.*?<\/style>/is', '', $buffer);
            // より確実に削除するため、複数行パターンにも対応
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-img-auto-sizes-contain-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]global-styles-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-library-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]classic-theme-styles-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-emoji-styles-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            
            // ブロックエディタのブロック別インラインスタイルを削除
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-button-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-heading-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-buttons-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]wp-block-paragraph-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            $buffer = preg_replace('/<style[^>]*id\s*=\s*[\'"]core-block-supports-inline-css[\'"][^>]*>[\s\S]*?<\/style>/is', '', $buffer);
            
            // dns-prefetchを削除
            $buffer = preg_replace('/<link[^>]*rel\s*=\s*[\'"]dns-prefetch[\'"][^>]*>/i', '', $buffer);
            
            // speculationrulesスクリプトを削除
            $buffer = preg_replace('/<script[^>]*type\s*=\s*[\'"]speculationrules[\'"][^>]*>[\s\S]*?<\/script>/is', '', $buffer);
            
            // head内のタグを適切に改行・インデント
            // <head>と</head>の間の内容を整理
            if (preg_match('/(<head[^>]*>)([\s\S]*?)(<\/head>)/i', $buffer, $matches)) {
                $head_start = $matches[1];
                $head_content = $matches[2];
                $head_end = $matches[3];
                
                // 全てのタグを抽出（各タグタイプごとに個別に抽出）
                $tags = array();
                
                // metaタグ（/> で終わる場合と > で終わる場合の両方に対応）
                preg_match_all('/<meta[^>]*(?:\/>|>)/i', $head_content, $meta_matches);
                if (!empty($meta_matches[0])) {
                    $tags = array_merge($tags, $meta_matches[0]);
                }
                
                // titleタグ
                preg_match_all('/<title[^>]*>.*?<\/title>/is', $head_content, $title_matches);
                if (!empty($title_matches[0])) {
                    $tags = array_merge($tags, $title_matches[0]);
                }
                
                // linkタグ
                preg_match_all('/<link[^>]*\/?>/i', $head_content, $link_matches);
                if (!empty($link_matches[0])) {
                    $tags = array_merge($tags, $link_matches[0]);
                }
                
                // scriptタグ
                preg_match_all('/<script[^>]*>.*?<\/script>/is', $head_content, $script_matches);
                if (!empty($script_matches[0])) {
                    $tags = array_merge($tags, $script_matches[0]);
                }
                
                // styleタグ
                preg_match_all('/<style[^>]*>.*?<\/style>/is', $head_content, $style_matches);
                if (!empty($style_matches[0])) {
                    $tags = array_merge($tags, $style_matches[0]);
                }
                
                // 各タグを1行ずつ、適切にインデントして再構築
                $formatted_content = "\n";
                foreach ($tags as $tag) {
                    // タグをクリーンアップ（余計な空白を削除）
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

// グローバルスタイルの出力を無効化
add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        // グローバルスタイルのインライン出力を無効化
        remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    }
}, 1);

// オートフォーマット関連の無効化（必要に応じて）
add_action('init', function() {
    // remove_filter('the_title', 'wptexturize');
    // remove_filter('the_content', 'wptexturize');
    // remove_filter('the_excerpt', 'wptexturize');
    // remove_filter('the_title', 'wpautop');
    // remove_filter('the_content', 'wpautop');
    // remove_filter('the_excerpt', 'wpautop');
});
