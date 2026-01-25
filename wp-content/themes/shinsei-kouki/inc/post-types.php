<?php
/**
 * post-types.php
 * カスタム投稿タイプの登録
 */

// お知らせカスタム投稿タイプの登録
function shinsei_kouki_register_post_types() {
    // お知らせ
    register_post_type('topics', array(
        'labels' => array(
            'name' => 'お知らせ',
            'singular_name' => 'お知らせ',
            'add_new' => '新規追加',
            'add_new_item' => 'お知らせを追加',
            'edit_item' => 'お知らせを編集',
            'new_item' => '新規お知らせ',
            'view_item' => 'お知らせを表示',
            'search_items' => 'お知らせを検索',
            'not_found' => 'お知らせが見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱にお知らせはありません',
            'all_items' => 'すべてのお知らせ',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array(
            'slug' => 'topics',
            'with_front' => false,
            'feeds' => false,
            'pages' => false,
        ),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'custom-fields',
        ),
        'show_in_rest' => true, // ブロックエディタ対応
    ));
    
    // 営業カレンダー
    register_post_type('business_calendar', array(
        'labels' => array(
            'name' => '営業カレンダー',
            'singular_name' => '営業カレンダー',
            'add_new' => '新規追加',
            'add_new_item' => '営業カレンダーを追加',
            'edit_item' => '営業カレンダーを編集',
            'new_item' => '新規営業カレンダー',
            'view_item' => '営業カレンダーを表示',
            'search_items' => '営業カレンダーを検索',
            'not_found' => '営業カレンダーが見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に営業カレンダーはありません',
            'all_items' => 'すべての営業カレンダー',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array(
            'title',
            'custom-fields',
        ),
        'show_in_rest' => false,
    ));
}
add_action('init', 'shinsei_kouki_register_post_types');

// お知らせのURLをカスタム形式に変更（post_260117-01.html形式）
function shinsei_kouki_topics_post_link($post_link, $post) {
    if ($post->post_type === 'topics' && $post->post_status === 'publish') {
        // 投稿日を取得
        $post_date = get_post_time('ymd', false, $post);
        
        // 同じ日付の投稿数を取得して連番を決定
        $same_date_posts = get_posts(array(
            'post_type' => 'topics',
            'post_status' => 'publish',
            'date_query' => array(
                array(
                    'year' => get_post_time('Y', false, $post),
                    'month' => get_post_time('m', false, $post),
                    'day' => get_post_time('d', false, $post),
                ),
            ),
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
        ));
        
        // 現在の投稿が同じ日付の投稿の中で何番目かを取得
        $sequence = 1;
        foreach ($same_date_posts as $index => $same_date_post) {
            if ($same_date_post->ID === $post->ID) {
                $sequence = $index + 1;
                break;
            }
        }
        
        // 連番を2桁のゼロパディング形式に
        $sequence_str = sprintf('%02d', $sequence);
        
        // URLを生成: /topics/post_260117-01.html
        $post_link = home_url('/topics/post_' . $post_date . '-' . $sequence_str . '.html');
    }
    return $post_link;
}
add_filter('post_type_link', 'shinsei_kouki_topics_post_link', 10, 2);

// リライトルールを追加（.html形式のURLを認識）
function shinsei_kouki_topics_rewrite_rules() {
    add_rewrite_rule(
        '^topics/post_([0-9]{6})-([0-9]{2})\.html$',
        'index.php?post_type=topics&topics_date=$matches[1]&topics_sequence=$matches[2]',
        'top'
    );
}
add_action('init', 'shinsei_kouki_topics_rewrite_rules');

// クエリ変数を追加
function shinsei_kouki_topics_query_vars($vars) {
    $vars[] = 'topics_date';
    $vars[] = 'topics_sequence';
    return $vars;
}
add_filter('query_vars', 'shinsei_kouki_topics_query_vars');

// カスタムクエリ変数に基づいて投稿を取得
function shinsei_kouki_topics_parse_request($wp) {
    if (isset($wp->query_vars['topics_date']) && isset($wp->query_vars['topics_sequence'])) {
        $topics_date = $wp->query_vars['topics_date'];
        $topics_sequence = intval($wp->query_vars['topics_sequence']);
        
        // 日付を分解（例: 260117 → 2026年01月17日）
        $year = '20' . substr($topics_date, 0, 2);
        $month = substr($topics_date, 2, 2);
        $day = substr($topics_date, 4, 2);
        
        // 同じ日付の投稿を取得
        $posts = get_posts(array(
            'post_type' => 'topics',
            'post_status' => 'publish',
            'date_query' => array(
                array(
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                ),
            ),
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
        ));
        
        // 指定された連番の投稿を取得
        if (!empty($posts) && isset($posts[$topics_sequence - 1])) {
            $wp->query_vars['post_type'] = 'topics';
            $wp->query_vars['p'] = $posts[$topics_sequence - 1]->ID;
            $wp->query_vars['name'] = '';
        }
    }
}
add_action('parse_request', 'shinsei_kouki_topics_parse_request');

// 既存のnews投稿タイプをtopicsに移行（一度だけ実行）
function shinsei_kouki_migrate_news_to_topics() {
    // 既に移行済みかチェック
    $migrated = get_option('shinsei_kouki_news_migrated_to_topics');
    if ($migrated) {
        return;
    }
    
    global $wpdb;
    
    // post_typeをnewsからtopicsに変更
    $updated = $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->posts} SET post_type = %s WHERE post_type = %s",
            'topics',
            'news'
        )
    );
    
    // 移行完了フラグを設定（更新があった場合のみ）
    if ($updated !== false) {
        update_option('shinsei_kouki_news_migrated_to_topics', true);
        
        // リライトルールをフラッシュ
        flush_rewrite_rules();
    }
}
// initフックで一度だけ実行
add_action('init', 'shinsei_kouki_migrate_news_to_topics', 20);
