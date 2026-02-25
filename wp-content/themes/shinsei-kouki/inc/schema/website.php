<?php
/**
 * WebSite 構造化データ
 * トップページ用。サイト全体を表し、publisher で Organization を参照。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WebSite の JSON-LD 用配列を返す。
 *
 * @return array
 */
function shinsei_kouki_get_website_schema() {
    $org_id = home_url('/') . '#organization';
    $url    = home_url('/');
    $name   = get_bloginfo('name');
    $desc   = get_bloginfo('description');
    if (empty($desc)) {
        $desc = '神西衡機工業株式会社の公式サイト。計量器（はかり）・分銅の販売、修理、検査。神奈川県小田原市。';
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        '@id'             => $url . '#website',
        'name'            => $name,
        'url'             => $url,
        'description'     => $desc,
        'publisher'       => array(
            '@id' => $org_id,
        ),
    );

    // お知らせ検索がある場合は potentialAction で SearchAction を追加可能
    $search_url = home_url('/topics/');
    if (get_option('permalink_structure')) {
        $search_url = home_url('/topics/?s={search_term_string}');
    } else {
        $search_url = home_url('/?post_type=topics&s={search_term_string}');
    }
    $schema['potentialAction'] = array(
        '@type'       => 'SearchAction',
        'target'      => array(
            '@type'       => 'EntryPoint',
            'urlTemplate' => $search_url,
        ),
        'query-input' => 'required name=search_term_string',
    );

    return $schema;
}
