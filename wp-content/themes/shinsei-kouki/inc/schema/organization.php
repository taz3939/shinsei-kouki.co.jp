<?php
/**
 * Organization 構造化データ
 * 全ページで参照する基盤となる組織情報。@id で他スキーマから参照可能にする。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Organization の JSON-LD 用配列を返す。
 *
 * @return array
 */
function shinsei_kouki_get_organization_schema() {
    $name = get_bloginfo('name');
    $url  = home_url('/');
    $logo = get_template_directory_uri() . '/assets/img/common/logo-color.svg';
    $desc = get_bloginfo('description');
    if (empty($desc)) {
        $desc = '計量器（はかり）・分銅の販売、修理、検査。神奈川県小田原市の神西衡機工業株式会社。';
    }

    return array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        '@id'         => $url . '#organization',
        'name'        => $name,
        'url'         => $url,
        'logo'        => array(
            '@type' => 'ImageObject',
            'url'   => $logo,
        ),
        'description' => $desc,
    );
}
