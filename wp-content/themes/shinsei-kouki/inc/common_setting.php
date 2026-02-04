<?php
/**
 * common_setting.php
 * サイト共通の設定・出力：固定ページメタ・本文加工・カスタマイザー・メタタグ
 */

// =============================================================================
// 固定ページ：メタディスクリプション用メタボックス
// =============================================================================

function shinsei_kouki_add_meta_description_meta_box() {
    add_meta_box(
        'shinsei_kouki_meta_description_meta_box',
        'メタディスクリプション',
        'shinsei_kouki_meta_description_meta_box_callback',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'shinsei_kouki_add_meta_description_meta_box');

function shinsei_kouki_meta_description_meta_box_callback($post) {
    wp_nonce_field('shinsei_kouki_meta_description_meta_box', 'shinsei_kouki_meta_description_meta_box_nonce');
    $value = get_post_meta($post->ID, '_meta_description', true);
    echo '<p><label for="meta_description">検索結果やSNSシェア時に表示される説明文（120〜160文字程度を推奨）</label></p>';
    echo '<textarea id="meta_description" name="meta_description" rows="3" class="large-text" style="width:100%;">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">未入力の場合は抜粋、またはカスタマイザー「メタ情報」のデフォルトが使われます。</p>';
}

function shinsei_kouki_save_meta_description_meta_box($post_id) {
    if (!isset($_POST['shinsei_kouki_meta_description_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['shinsei_kouki_meta_description_meta_box_nonce'], 'shinsei_kouki_meta_description_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $value = isset($_POST['meta_description']) ? sanitize_textarea_field($_POST['meta_description']) : '';
    update_post_meta($post_id, '_meta_description', $value);
}
add_action('save_post', 'shinsei_kouki_save_meta_description_meta_box');

// =============================================================================
// 本文：外部リンクにアイコン付与
// =============================================================================

function shinsei_kouki_external_link_icon_create(DOMDocument $dom) {
    $icon_url = get_template_directory_uri() . '/assets/img/common/ico_external.svg';
    $span = $dom->createElement('span');
    $span->setAttribute('class', 'iconExternal');
    $span->setAttribute('aria-hidden', 'true');
    $img = $dom->createElement('img');
    $img->setAttribute('src', esc_url($icon_url));
    $img->setAttribute('alt', '');
    $img->setAttribute('width', '26');
    $img->setAttribute('height', '26');
    $img->setAttribute('class', 'iconExternalImg');
    $img->setAttribute('decoding', 'async');
    $img->setAttribute('aria-hidden', 'true');
    $span->appendChild($img);
    return $span;
}

function shinsei_kouki_add_external_link_icon($content) {
    if (empty($content) || !class_exists('DOMDocument')) {
        return $content;
    }
    $host = parse_url(home_url(), PHP_URL_HOST);
    if (!$host) {
        return $content;
    }
    $wrap = '<div id="shinsei-external-link-wrap">' . $content . '</div>';
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(
        '<?xml encoding="UTF-8">' . $wrap,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    $links = $xpath->query('.//a[@href]');
    if (!$links || $links->length === 0) {
        return $content;
    }
    foreach ($links as $a) {
        $href = $a->getAttribute('href');
        if (strpos($href, 'http') !== 0) {
            continue;
        }
        $link_host = parse_url($href, PHP_URL_HOST);
        if (!$link_host || strtolower($link_host) === strtolower($host)) {
            continue;
        }
        $span = shinsei_kouki_external_link_icon_create($dom);
        $a->appendChild($span);
    }
    $root = $dom->getElementsByTagName('div')->item(0);
    $inner = '';
    foreach ($root->childNodes as $child) {
        $inner .= $dom->saveHTML($child);
    }
    return $inner;
}
add_filter('the_content', 'shinsei_kouki_add_external_link_icon', 20);

// =============================================================================
// カスタマイザー：会社情報
// =============================================================================

add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('shinsei_kouki_company', array(
        'title'    => '会社情報',
        'priority' => 30,
    ));
    $wp_customize->add_setting('company_representative', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_representative', array('label' => '代表者', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_email', array('default' => '', 'sanitize_callback' => 'sanitize_email'));
    $wp_customize->add_control('company_email', array('label' => 'メールアドレス', 'section' => 'shinsei_kouki_company', 'type' => 'email'));
    $wp_customize->add_setting('company_postal', array('default' => '250-0863', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_postal', array('label' => '本社 郵便番号', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_address', array('default' => '神奈川県小田原市飯泉90-9', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_address', array('label' => '本社 住所', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_tel_hq', array('default' => '0465-55-8644', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_tel_hq', array('label' => '本社 電話番号', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_fax_hq', array('default' => '0465-55-8522', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_fax_hq', array('label' => '本社 FAX', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_postal_odawara', array('default' => '250-0862', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_postal_odawara', array('label' => '成田営業所 郵便番号', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_address_odawara', array('default' => '神奈川県小田原市成田1048', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_address_odawara', array('label' => '成田営業所 住所', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_tel', array('default' => '0465-38-1194', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_tel', array('label' => '成田営業所 電話番号（お問い合わせ先）', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
    $wp_customize->add_setting('company_fax_odawara', array('default' => '0465-36-1294', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('company_fax_odawara', array('label' => '成田営業所 FAX', 'section' => 'shinsei_kouki_company', 'type' => 'text'));
});

// =============================================================================
// メタ出力：タイトル・description・canonical・OGP
// =============================================================================

define('SHINSEI_KOUKI_DEFAULT_META_DESCRIPTION', '神西衡機工業株式会社は神奈川県小田原市で計量器（はかり）・分銅の販売・修理・検査を実施。電子天秤・トラックスケールの販売、はかりの修理、計量士による代検査・分銅校正までワンストップで対応。お問い合わせはお気軽に。');
define('SHINSEI_KOUKI_TOPICS_DESCRIPTION_PREFIX', '神西衡機工業のお知らせ。');
define('SHINSEI_KOUKI_TOPICS_ARCHIVE_META_DESCRIPTION', '神西衡機工業のお知らせ一覧。はかり・計量器・分銅に関する新製品のご案内、修理・検査・代検査の情報、夏季・冬季休暇など最新のお知らせを掲載。');

add_filter('document_title_separator', function() {
    return ' | ';
});

add_filter('document_title_parts', function($parts) {
    $site_name = get_bloginfo('name');
    if (is_singular('topics')) {
        $parts['title'] = get_the_title();
        $parts['page']  = 'お知らせ';
        $parts['site']  = $site_name;
        unset($parts['tagline']);
        return $parts;
    }
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
    if (is_search()) {
        $search_query = get_search_query();
        $parts['title'] = $search_query !== '' ? '「' . $search_query . '」の検索結果' : '検索結果';
        $parts['site'] = $site_name;
        unset($parts['tagline'], $parts['page']);
        return $parts;
    }
    return $parts;
}, 10, 1);

add_filter('document_title', function($title) {
    if (is_singular('topics')) {
        return get_the_title() . ' | ' . 'お知らせ' . ' | ' . get_bloginfo('name');
    }
    return $title;
}, 10, 1);

add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('shinsei_kouki_meta', array('title' => 'メタ情報', 'priority' => 35));
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
        $desc         = $search_query !== '' ? '神西衡機工業のお知らせ。「' . $search_query . '」の検索結果一覧。' : '神西衡機工業のお知らせの検索結果一覧。';
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
    if ($url !== '') {
        echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    }
    $og_image = get_template_directory_uri() . '/assets/img/common/ogp.webp';
    $og_type  = is_singular() ? 'article' : 'website';
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:locale" content="ja_JP">' . "\n";
}
