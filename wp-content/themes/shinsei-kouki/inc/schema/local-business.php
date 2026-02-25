<?php
/**
 * LocalBusiness 構造化データ
 * 会社概要ページ用。本社・成田事業所の住所・電話・代表者・事業内容・許可を出力。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * LocalBusiness の JSON-LD 用配列を返す。
 * 本社をメインの 1 件として出力。成田事業所はページ本文で案内。
 *
 * @return array
 */
function shinsei_kouki_get_local_business_schema() {
    $org_id = home_url('/') . '#organization';
    $name   = get_bloginfo('name');
    $url    = home_url('/');
    $logo   = get_template_directory_uri() . '/assets/img/common/logo-color.svg';

    $postal   = get_theme_mod('company_postal', '250-0863');
    $address  = get_theme_mod('company_address', '神奈川県小田原市飯泉90-9');
    $tel_hq   = get_theme_mod('company_tel_hq', '0465-55-8644');
    $fax_hq   = get_theme_mod('company_fax_hq', '0465-55-8522');
    $rep      = get_theme_mod('company_representative', '髙橋 佳吾');

    $street = $address;
    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'LocalBusiness',
        '@id'             => $url . '#localbusiness',
        'name'            => $name,
        'url'             => $url,
        'logo'            => array(
            '@type' => 'ImageObject',
            'url'   => $logo,
        ),
        'description'     => '計量器、はかりの販売・修理・検査。電子天秤・産業用はかり・トラックスケールの販売、修理、計量士による代検査・分銅校正。',
        'image'           => $logo,
        'address'         => array(
            '@type'           => 'PostalAddress',
            'postalCode'      => $postal,
            'addressRegion'   => '神奈川県',
            'addressLocality' => '小田原市',
            'streetAddress'   => $street,
        ),
        'telephone'       => $tel_hq,
        'faxNumber'       => $fax_hq,
    );

    if (!empty($rep)) {
        $schema['owner'] = array(
            '@type' => 'Person',
            'name'  => $rep,
        );
    }

    $schema['parentOrganization'] = array(
        '@id' => $org_id,
    );
    $schema['areaServed'] = array(
        '@type' => 'Place',
        'name'  => '神奈川県小田原市',
    );

    return $schema;
}
