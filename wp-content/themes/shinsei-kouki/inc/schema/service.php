<?php
/**
 * Service / ItemList 構造化データ
 * 事業内容ページ用。4つの事業をサービス一覧として出力。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 事業内容ページ用の ItemList（サービス一覧）の JSON-LD 用配列を返す。
 * 各 item は Service 型。provider で Organization を参照。
 *
 * @return array
 */
function shinsei_kouki_get_service_schema() {
    $org_id = home_url('/') . '#organization';
    $base   = home_url('/business');

    $services = array(
        array(
            'name'        => 'はかり・分銅等の販売',
            'description' => '電子天びん・分銅、それぞれ幅広い機種を納入前検査付きでご提供。最適なはかり・分銅をご提案します。',
        ),
        array(
            'name'        => 'はかりの修理',
            'description' => '故障や不具合発生時、迅速な対応で業務再開をサポート。あらゆる機種の修理にご対応します。',
        ),
        array(
            'name'        => 'はかり・分銅の検査',
            'description' => '社内管理用のはかりの検査・分銅校正を実施。休日作業にも柔軟に対応し、品質管理を徹底します。',
        ),
        array(
            'name'        => '特定計量器（はかり）の代検査',
            'description' => '定期検査に代わる、計量士による法定検査（代検査）を実施。トラックスケールから電子天秤まで対応可能です。',
        ),
    );

    $itemListElement = array();
    foreach ($services as $i => $s) {
        $itemListElement[] = array(
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'item'     => array(
                '@type'        => 'Service',
                'name'         => $s['name'],
                'description'  => $s['description'],
                'provider'     => array(
                    '@id' => $org_id,
                ),
                'url'          => $base,
            ),
        );
    }

    return array(
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        '@id'             => $base . '#service-list',
        'name'            => '事業内容',
        'description'     => '神西衡機工業の4つの事業。はかり・分銅等の販売、はかりの修理、はかり・分銅の検査、特定計量器（はかり）の代検査。',
        'url'             => $base,
        'numberOfItems'   => count($services),
        'itemListElement' => $itemListElement,
    );
}
