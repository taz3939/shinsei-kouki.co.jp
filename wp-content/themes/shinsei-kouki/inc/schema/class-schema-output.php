<?php
/**
 * 構造化データ（JSON-LD）出力用ヘルパー
 * 複数グラフの連結・エスケープ・script タグ出力
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * JSON 文字列に改行とインデントを付与する（確認用）
 *
 * @param string $json 1行のJSON文字列
 * @return string
 */
function shinsei_kouki_pretty_json_ld($json) {
    $indent      = 0;
    $result      = '';
    $len         = strlen($json);
    $in_string   = false;
    $escape      = false;
    $string_char = '';
    $pad         = '  ';
    for ($i = 0; $i < $len; $i++) {
        $c = $json[$i];
        if ($in_string) {
            $result .= $c;
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($c === '\\') {
                $escape = true;
                continue;
            }
            if ($c === $string_char) {
                $in_string = false;
            }
            continue;
        }
        if ($c === '"') {
            $in_string   = true;
            $string_char = $c;
            $result     .= $c;
            continue;
        }
        if ($c === ' ' || $c === "\n" || $c === "\r" || $c === "\t") {
            continue;
        }
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
 * 複数のスキーマオブジェクト（グラフ）をまとめて JSON-LD で出力する。
 * 各要素は連想配列。null や空配列はスキップする。
 *
 * @param array $graphs グラフの配列。各要素は @type を持つ連想配列。
 */
function shinsei_kouki_schema_output_json_ld($graphs) {
    echo "<!-- SCHEMA_FILE_LOADED -->\n";
    $graphs = array_filter((array) $graphs, function ($g) {
        return is_array($g) && !empty($g);
    });
    if (empty($graphs)) {
        return;
    }
    $json = json_encode($graphs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512);
    if ($json === false) {
        return;
    }
    $json = shinsei_kouki_pretty_json_ld($json);
    echo "<!-- shinsei_kouki structured data -->\n";
    echo '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
}
