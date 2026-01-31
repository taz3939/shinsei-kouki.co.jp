<?php
/**
 * external-link-icon.php
 * 投稿本文内の外部リンク（別ホスト）にアイコンSVGを追加
 */

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
