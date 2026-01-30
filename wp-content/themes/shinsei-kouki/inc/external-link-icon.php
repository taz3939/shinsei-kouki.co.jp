<?php
/**
 * external-link-icon.php
 * 投稿本文内の外部リンク（別ホスト）にアイコンSVGを追加
 */

function shinsei_kouki_external_link_icon_svg() {
    return '<span class="iconExternal" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="26" viewBox="0 0 26 26" aria-hidden="true"><path fill-rule="evenodd" fill="rgb(99, 117, 122)" d="M21.158,26.000 L4.677,26.000 C2.098,26.000 0.000,23.902 0.000,21.323 L0.000,4.842 C0.000,2.263 2.098,0.165 4.677,0.165 L7.068,0.165 C7.741,0.165 8.287,0.710 8.287,1.384 C8.287,2.057 7.741,2.602 7.068,2.602 L4.677,2.602 C3.442,2.602 2.437,3.607 2.437,4.842 L2.437,21.323 C2.437,22.558 3.442,23.563 4.677,23.563 L21.158,23.563 C22.393,23.563 23.398,22.558 23.398,21.323 L23.398,18.932 C23.398,18.259 23.943,17.713 24.616,17.713 C25.290,17.713 25.835,18.259 25.835,18.932 L25.835,21.323 C25.835,23.902 23.737,26.000 21.158,26.000 ZM25.083,10.309 C24.932,10.371 24.774,10.402 24.617,10.402 C24.299,10.402 23.988,10.278 23.755,10.045 L23.946,3.777 L12.894,14.829 C12.656,15.067 12.344,15.186 12.033,15.186 C11.721,15.186 11.409,15.067 11.171,14.829 C10.695,14.353 10.695,13.582 11.171,13.106 L22.223,2.053 L15.956,2.245 C15.607,1.897 15.503,1.372 15.691,0.917 C15.880,0.462 16.325,0.165 16.817,0.165 L23.817,0.165 C24.930,0.165 25.835,1.070 25.835,2.183 L25.835,9.183 C25.835,9.676 25.538,10.120 25.083,10.309 Z"/></svg></span>';
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

    $svg_fragment = shinsei_kouki_external_link_icon_svg();

    foreach ($links as $a) {
        $href = $a->getAttribute('href');
        if (strpos($href, 'http') !== 0) {
            continue;
        }
        $link_host = parse_url($href, PHP_URL_HOST);
        if (!$link_host || strtolower($link_host) === strtolower($host)) {
            continue;
        }

        $span = $dom->createDocumentFragment();
        $span->appendXML($svg_fragment);
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
