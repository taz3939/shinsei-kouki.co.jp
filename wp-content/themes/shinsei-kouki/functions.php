<?php
/**
 * functions.php
 * モジュール化したfunctions.phpのファイル読み込み
 */

$includes = [
    'inc/cleanup.php',              // 不要なWP機能の削除やオートフォーマット関連の設定
    'inc/enqueue-assets.php',       // CSS, JSの読み込み
    'inc/theme-support.php',        // テーマサポート機能
    'inc/post-types.php',           // カスタム投稿タイプの登録
    'inc/business-calendar.php',   // 営業カレンダー機能
];

foreach ($includes as $file) {
    if (file_exists(get_template_directory() . '/' . $file)) {
        require_once get_template_directory() . '/' . $file;
    }
}

// ページネーション関数（お知らせ一覧用）
function shinsei_kouki_pagination() {
    global $wp_query;
    
    $big = 999999999;
    
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages,
        'prev_text' => '前へ',
        'next_text' => '次へ',
        'end_size' => 1,
        'mid_size' => 2,
    ));
}
