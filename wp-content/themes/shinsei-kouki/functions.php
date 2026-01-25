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

// パンくずリスト関数
function shinsei_kouki_breadcrumbs() {
    if (is_front_page()) {
        return;
    }
    
    echo '<nav aria-label="breadcrumbs" class="breadcrumbs">';
    echo '<ol>';
    
    // ホーム
    echo '<li><a href="' . esc_url(home_url('/')) . '">';
    echo '<svg id="トップページ" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30px" height="30px" viewBox="0 0 30 30" aria-hidden="true"><path fill-rule="evenodd" fill="rgb(51, 51, 68)" d="M29.345,29.986 L24.004,29.992 L24.006,5.383 L27.636,7.200 C28.181,7.473 28.500,7.975 28.507,8.613 L28.506,28.449 L29.212,28.500 C29.644,28.464 29.959,28.782 29.998,29.167 C30.032,29.505 29.785,29.985 29.345,29.986 ZM14.998,25.442 C14.977,24.613 14.345,23.986 13.527,23.957 L10.497,23.957 C9.677,23.974 9.005,24.600 9.003,25.474 L8.995,29.994 L0.798,29.995 C0.351,29.995 0.037,29.705 0.003,29.298 C-0.029,28.900 0.268,28.503 0.718,28.493 L1.495,28.475 L1.498,5.639 C1.498,4.994 1.973,4.381 2.589,4.237 L20.620,0.047 C21.114,-0.068 21.551,0.029 21.922,0.318 C22.276,0.593 22.505,1.010 22.505,1.543 L22.506,29.989 L15.008,29.998 L14.998,25.442 ZM8.997,9.604 C8.997,9.201 8.652,8.865 8.256,8.865 L6.770,8.863 C6.315,8.862 5.997,9.213 5.997,9.658 L5.997,11.083 C5.997,11.524 6.316,11.883 6.770,11.880 L8.297,11.872 C8.695,11.871 8.997,11.492 8.997,11.142 L8.997,9.604 ZM8.997,14.132 C8.997,13.730 8.653,13.394 8.256,13.394 L6.770,13.392 C6.315,13.391 5.997,13.742 5.997,14.188 L5.997,15.613 C5.997,16.053 6.316,16.411 6.770,16.409 L8.297,16.401 C8.695,16.399 8.997,16.021 8.997,15.670 L8.997,14.132 ZM8.997,18.661 C8.997,18.258 8.652,17.922 8.256,17.922 L6.770,17.920 C6.315,17.920 5.997,18.271 5.997,18.716 L5.997,20.141 C5.997,20.582 6.316,20.940 6.770,20.938 L8.297,20.930 C8.695,20.927 8.997,20.550 8.997,20.198 L8.997,18.661 ZM10.499,20.141 C10.499,20.582 10.817,20.940 11.271,20.938 L12.799,20.930 C13.196,20.927 13.499,20.550 13.499,20.198 L13.499,18.661 C13.499,18.258 13.154,17.922 12.758,17.922 L11.271,17.920 C10.817,17.920 10.499,18.271 10.499,18.716 L10.499,20.141 ZM13.499,9.604 C13.499,9.201 13.154,8.865 12.758,8.865 L11.271,8.863 C10.817,8.862 10.499,9.213 10.499,9.658 L10.499,11.083 C10.499,11.524 10.817,11.883 11.271,11.880 L12.799,11.872 C13.196,11.871 13.499,11.492 13.499,11.142 L13.499,9.604 ZM13.499,14.132 C13.499,13.730 13.154,13.394 12.758,13.394 L11.271,13.392 C10.817,13.391 10.499,13.742 10.499,14.188 L10.499,15.613 C10.499,16.053 10.817,16.411 11.271,16.409 L12.799,16.401 C13.196,16.399 13.499,16.021 13.499,15.670 L13.499,14.132 ZM18.000,9.604 C18.000,9.201 17.656,8.865 17.259,8.865 L15.773,8.863 C15.319,8.862 15.000,9.213 15.000,9.658 L15.000,11.083 C15.000,11.524 15.319,11.883 15.773,11.880 L17.301,11.872 C17.698,11.871 18.001,11.492 18.001,11.142 L18.000,9.604 ZM18.000,14.132 C18.000,13.730 17.656,13.394 17.259,13.394 L15.773,13.392 C15.319,13.391 15.000,13.742 15.000,14.188 L15.000,15.613 C15.000,16.053 15.319,16.411 15.773,16.409 L17.301,16.401 C17.698,16.399 18.001,16.021 18.001,15.670 L18.000,14.132 ZM15.773,20.938 L17.301,20.930 C17.698,20.927 18.001,20.550 18.001,20.198 L18.000,18.661 C18.000,18.258 17.656,17.922 17.259,17.922 L15.773,17.920 C15.319,17.920 15.000,18.271 15.000,18.716 L15.000,20.141 C15.000,20.582 15.319,20.940 15.773,20.938 ZM13.503,29.986 L10.504,29.999 L10.497,25.469 L13.500,25.466 L13.503,29.986 Z"/></svg>';
    echo '<span>トップページ</span></a></li>';
    
    if (is_category()) {
        // カテゴリーページ
        $category = get_queried_object();
        echo '<li>' . esc_html($category->name) . '</li>';
    } elseif (is_single()) {
        // 投稿ページ
        $post_type = get_post_type();
        if ($post_type === 'topics') {
            echo '<li><a href="' . esc_url(home_url('/topics')) . '">お知らせ</a></li>';
        } elseif ($post_type === 'post') {
            $categories = get_the_category();
            if (!empty($categories)) {
                echo '<li><a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a></li>';
            }
        }
        echo '<li>' . esc_html(get_the_title()) . '</li>';
    } elseif (is_page()) {
        // 固定ページ
        $ancestors = get_post_ancestors(get_the_ID());
        if ($ancestors) {
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor) {
                echo '<li><a href="' . esc_url(get_permalink($ancestor)) . '">' . esc_html(get_the_title($ancestor)) . '</a></li>';
            }
        }
        echo '<li>' . esc_html(get_the_title()) . '</li>';
    } elseif (is_post_type_archive()) {
        // カスタム投稿タイプのアーカイブ
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        if ($post_type) {
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj) {
                echo '<li>' . esc_html($post_type_obj->labels->name) . '</li>';
            }
        }
    } elseif (is_archive()) {
        // その他のアーカイブ
        echo '<li>' . esc_html(get_the_archive_title()) . '</li>';
    } elseif (is_search()) {
        // 検索結果
        echo '<li>検索結果</li>';
    } elseif (is_404()) {
        // 404ページ
        echo '<li>ページが見つかりません</li>';
    }
    
    echo '</ol>';
    echo '</nav>';
}
