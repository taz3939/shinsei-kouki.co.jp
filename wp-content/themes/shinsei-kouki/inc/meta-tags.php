<?php
/**
 * meta-tags.php
 * メタディスクリプション・canonical・OGP の出力を管理
 * 表示：外観 → カスタマイズ → メタ情報
 */

// タイトル区切り文字（&#8211; en-dash の代わりに | を使用）
add_filter('document_title_separator', function() {
    return ' | ';
});

// タイトル部分の固有化（お知らせ詳細・アーカイブ・月別アーカイブ）
add_filter('document_title_parts', function($parts) {
    $site_name = get_bloginfo('name');

    // お知らせの単一記事：記事タイトル | お知らせ | サイト名
    if (is_singular('topics')) {
        $parts['title'] = get_the_title();
        $parts['page']  = 'お知らせ';
        $parts['site']  = $site_name;
        unset($parts['tagline']);
        return $parts;
    }

    // お知らせアーカイブ（一覧 or 月別）
    if (is_post_type_archive('topics')) {
        $year  = get_query_var('year');
        $month = get_query_var('monthnum');
        if ($year && $month) {
            $parts['title'] = $year . '年' . (int) $month . '月のお知らせ';
        } else {
            $parts['title'] = 'お知らせ一覧';
        }
        $parts['site'] = $site_name;
        unset($parts['tagline'], $parts['page']);
        return $parts;
    }

    // 検索結果：「キーワード」の検索結果 | サイト名
    if (is_search()) {
        $search_query = get_search_query();
        $parts['title'] = $search_query !== ''
            ? '「' . $search_query . '」の検索結果'
            : '検索結果';
        $parts['site'] = $site_name;
        unset($parts['tagline'], $parts['page']);
        return $parts;
    }

    return $parts;
}, 10, 1);

// お知らせ詳細のみタイトル順を固定：記事タイトル | お知らせ | サイト名
add_filter('document_title', function($title) {
    if (is_singular('topics')) {
        $sep = ' | ';
        return get_the_title() . $sep . 'お知らせ' . $sep . get_bloginfo('name');
    }
    return $title;
}, 10, 1);

// デフォルトメタディスクリプション（トップ・共通フォールバック）
define('SHINSEI_KOUKI_DEFAULT_META_DESCRIPTION', '神西衡機工業株式会社は神奈川県小田原市で計量器（はかり）・分銅の販売・修理・検査を実施。電子天秤・トラックスケールの販売、はかりの修理、計量士による代検査・分銅校正までワンストップで対応。お問い合わせはお気軽に。');

// お知らせ（topics）の description 用共通文言（記事タイトルを付与して「共通文言＋タイトル」で出力）
define('SHINSEI_KOUKI_TOPICS_DESCRIPTION_PREFIX', '神西衡機工業のお知らせ。');

// お知らせ一覧（/topics/）ページ用メタディスクリプション
define('SHINSEI_KOUKI_TOPICS_ARCHIVE_META_DESCRIPTION', '神西衡機工業のお知らせ一覧。はかり・計量器・分銅に関する新製品のご案内、修理・検査・代検査の情報、夏季・冬季休暇など最新のお知らせを掲載。');

// カスタマイザー：デフォルトメタディスクリプション
add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('shinsei_kouki_meta', array(
        'title'    => 'メタ情報',
        'priority' => 35,
    ));

    $wp_customize->add_setting('default_meta_description', array(
        'default'           => SHINSEI_KOUKI_DEFAULT_META_DESCRIPTION,
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('default_meta_description', array(
        'label'       => 'デフォルトメタディスクリプション',
        'description' => '検索結果やSNSシェア時の説明文。各ページで未設定の場合に使用します。',
        'section'     => 'shinsei_kouki_meta',
        'type'        => 'textarea',
    ));
});

// head 内にメタディスクリプション・canonical・OGP を出力
add_action('wp_head', 'shinsei_kouki_output_meta_tags', 2);

function shinsei_kouki_output_meta_tags() {
    if (is_admin()) {
        return;
    }

    $url   = function_exists('wp_get_canonical_url') ? wp_get_canonical_url() : '';
    $title = '';
    $desc  = '';

    if (is_front_page()) {
        $url   = home_url('/');
        $title = get_bloginfo('name') . (get_bloginfo('description') ? ' | ' . get_bloginfo('description') : '');
        $desc  = get_theme_mod('default_meta_description', SHINSEI_KOUKI_DEFAULT_META_DESCRIPTION);
        if ($desc === '') {
            $desc = get_bloginfo('description') ?: SHINSEI_KOUKI_DEFAULT_META_DESCRIPTION;
        }
    } elseif (is_singular()) {
        $url   = $url ?: get_permalink();
        $title = get_the_title();
        if (get_post_type() === 'topics') {
            $desc = SHINSEI_KOUKI_TOPICS_DESCRIPTION_PREFIX . $title;
        } else {
            $desc = get_post_meta(get_the_ID(), '_meta_description', true);
            if ($desc === '') {
                $desc = get_the_excerpt();
            }
            if ($desc === '') {
                $desc = get_theme_mod('default_meta_description', get_bloginfo('description'));
            }
        }
    } elseif (is_post_type_archive('topics')) {
        $year  = get_query_var('year');
        $month = get_query_var('monthnum');
        if ($year && $month) {
            $base = home_url('/topics/' . $year . '/' . str_pad($month, 2, '0', STR_PAD_LEFT) . '/');
            $paged = get_query_var('paged') ? (int) get_query_var('paged') : 0;
            $url  = $paged > 1 ? $base . 'page/' . $paged . '/' : $base;
            $title = wp_get_document_title();
            $desc  = '神西衡機工業のお知らせ。' . $year . '年' . (int) $month . '月に公開した記事一覧。';
        } else {
            $url   = get_post_type_archive_link('topics');
            $title = wp_get_document_title();
            $desc  = SHINSEI_KOUKI_TOPICS_ARCHIVE_META_DESCRIPTION;
        }
    } elseif (is_search()) {
        $search_query = get_search_query();
        $url          = $url ?: home_url($_SERVER['REQUEST_URI']);
        $title        = wp_get_document_title();
        $desc         = $search_query !== ''
            ? '神西衡機工業のお知らせ。「' . $search_query . '」の検索結果一覧。'
            : '神西衡機工業のお知らせの検索結果一覧。';
    } else {
        $url   = $url ?: home_url($_SERVER['REQUEST_URI']);
        $title = wp_get_document_title();
        $desc  = get_theme_mod('default_meta_description', get_bloginfo('description'));
    }

    $desc = trim(wp_strip_all_tags($desc));
    if ($desc === '') {
        $desc = get_bloginfo('name');
    }
    echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";

    // canonical
    if ($url !== '') {
        echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    }

    // OGP（画像は assets/img/common/ogp.png を想定）
    $og_image = get_template_directory_uri() . '/assets/img/common/ogp.png';
    $og_type  = is_singular() ? 'article' : 'website';

    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:locale" content="ja_JP">' . "\n";
}
