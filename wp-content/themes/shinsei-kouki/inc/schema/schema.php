<?php
/**
 * 構造化データ（JSON-LD）エントリポイント
 * 各スキーマファイルを読み込み、wp_head でグラフを組み立て、header.php で出力する。
 */

if (!defined('ABSPATH')) {
    exit;
}

$schema_dir = dirname(__FILE__);

require_once $schema_dir . '/organization.php';
require_once $schema_dir . '/local-business.php';
require_once $schema_dir . '/website.php';
require_once $schema_dir . '/service.php';
require_once $schema_dir . '/article.php';
require_once $schema_dir . '/breadcrumb.php';

/** wp_head 内で組み立てたグラフを保持（header.php で出力するため） */
$shinsei_kouki_schema_graphs = array();

/**
 * JSON 文字列に改行・インデントを付与（確認しやすいように）
 */
function shinsei_kouki_schema_pretty_json($json) {
    $indent = 0;
    $result = '';
    $len    = strlen($json);
    $in_string = false;
    $escape = false;
    $pad    = '  ';
    for ($i = 0; $i < $len; $i++) {
        $c = $json[$i];
        if ($in_string) {
            $result .= $c;
            if ($escape) { $escape = false; continue; }
            if ($c === '\\') { $escape = true; continue; }
            if ($c === '"') { $in_string = false; }
            continue;
        }
        if ($c === '"') { $in_string = true; $result .= $c; continue; }
        if ($c === ' ' || $c === "\n" || $c === "\r" || $c === "\t") { continue; }
        if ($c === '{' || $c === '[') {
            $indent++;
            $result .= $c . "\n" . str_repeat($pad, $indent);
            continue;
        }
        if ($c === '}' || $c === ']') {
            $indent--;
            $result .= "\n" . str_repeat($pad, $indent) . $c;
            continue;
        }
        if ($c === ',') {
            $result .= $c . "\n" . str_repeat($pad, $indent);
            continue;
        }
        $result .= $c;
    }
    return $result;
}

/**
 * 保持したグラフを JSON-LD で出力（header.php から呼ぶ）
 */
function shinsei_kouki_render_schema_json_ld() {
    global $shinsei_kouki_schema_graphs;
    $graphs = array_filter((array) $shinsei_kouki_schema_graphs, function ($g) {
        return is_array($g) && !empty($g);
    });
    if (empty($graphs)) {
        return;
    }
    $json = json_encode($graphs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512);
    if ($json === false) {
        return;
    }
    $json = shinsei_kouki_schema_pretty_json($json);
    echo '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
}

add_action('wp_head', function () {
    global $shinsei_kouki_schema_graphs;
    if (is_admin()) {
        return;
    }

    $shinsei_kouki_schema_graphs = array();

    // Organization は全ページで出力（他スキーマが @id で参照するため）
    $shinsei_kouki_schema_graphs[] = shinsei_kouki_get_organization_schema();

    if (is_front_page()) {
        $shinsei_kouki_schema_graphs[] = shinsei_kouki_get_website_schema();
    }

    if (is_page('company')) {
        $shinsei_kouki_schema_graphs[] = shinsei_kouki_get_local_business_schema();
    }

    if (is_page('business')) {
        $shinsei_kouki_schema_graphs[] = shinsei_kouki_get_service_schema();
    }

    if (is_singular('topics')) {
        $article = shinsei_kouki_get_article_schema();
        if ($article) {
            $shinsei_kouki_schema_graphs[] = $article;
        }
    }

    $breadcrumb = shinsei_kouki_get_breadcrumb_schema();
    if (!empty($breadcrumb)) {
        $shinsei_kouki_schema_graphs[] = $breadcrumb;
    }
}, 2);
