<?php
/**
 * customize_topics.php
 * お知らせ（topics）のカスタマイズ：CPT・リライト・メタボックス（目次・ピックアップ）
 */

// =============================================================================
// お知らせカスタム投稿タイプの登録
// =============================================================================

function shinsei_kouki_register_topics_post_type() {
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
            'pages' => true,
        ),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'shinsei_kouki_register_topics_post_type');

// =============================================================================
// お知らせのURL・リライト・リダイレクト
// =============================================================================

function shinsei_kouki_topics_post_link($post_link, $post) {
    if ($post->post_type === 'topics' && $post->post_status === 'publish') {
        $post_date = get_post_time('ymd', false, $post);
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
        $sequence = 1;
        foreach ($same_date_posts as $index => $same_date_post) {
            if ($same_date_post->ID === $post->ID) {
                $sequence = $index + 1;
                break;
            }
        }
        $sequence_str = sprintf('%02d', $sequence);
        $post_link = home_url('/topics/post_' . $post_date . '-' . $sequence_str . '/');
    }
    return $post_link;
}
add_filter('post_type_link', 'shinsei_kouki_topics_post_link', 10, 2);

function shinsei_kouki_topics_redirect_old_html_url() {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if (preg_match('#^/topics/post_([0-9]{6})-([0-9]{2})\.html/?#', $request_uri, $m)) {
        wp_redirect(home_url('/topics/post_' . $m[1] . '-' . $m[2] . '/'), 301);
        exit;
    }
}
add_action('template_redirect', 'shinsei_kouki_topics_redirect_old_html_url', 1);

function shinsei_kouki_topics_rewrite_rules() {
    add_rewrite_rule(
        '^topics/post_([0-9]{6})-([0-9]{2})/?$',
        'index.php?post_type=topics&topics_date=$matches[1]&topics_sequence=$matches[2]',
        'top'
    );
    add_rewrite_rule(
        '^topics/([0-9]{4})/([0-9]{2})/?$',
        'index.php?post_type=topics&year=$matches[1]&monthnum=$matches[2]',
        'top'
    );
    add_rewrite_rule(
        '^topics/([0-9]{4})/?$',
        'index.php?post_type=topics&year=$matches[1]',
        'top'
    );
}
add_action('init', 'shinsei_kouki_topics_rewrite_rules');

function shinsei_kouki_topics_query_vars($vars) {
    $vars[] = 'topics_date';
    $vars[] = 'topics_sequence';
    return $vars;
}
add_filter('query_vars', 'shinsei_kouki_topics_query_vars');

function shinsei_kouki_topics_parse_request($wp) {
    if (isset($wp->query_vars['topics_date']) && isset($wp->query_vars['topics_sequence'])) {
        $topics_date = $wp->query_vars['topics_date'];
        $topics_sequence = intval($wp->query_vars['topics_sequence']);
        $year = '20' . substr($topics_date, 0, 2);
        $month = substr($topics_date, 2, 2);
        $day = substr($topics_date, 4, 2);
        $posts = get_posts(array(
            'post_type' => 'topics',
            'post_status' => 'publish',
            'date_query' => array(
                array('year' => $year, 'month' => $month, 'day' => $day),
            ),
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
        ));
        if (!empty($posts) && isset($posts[$topics_sequence - 1])) {
            $wp->query_vars['post_type'] = 'topics';
            $wp->query_vars['p'] = $posts[$topics_sequence - 1]->ID;
            $wp->query_vars['name'] = '';
        }
    }
}
add_action('parse_request', 'shinsei_kouki_topics_parse_request');

function shinsei_kouki_topics_date_archive_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'topics'
            && isset($query->query_vars['year']) && isset($query->query_vars['monthnum'])) {
            $query->set('post_type', 'topics');
            $query->set('year', $query->query_vars['year']);
            $query->set('monthnum', $query->query_vars['monthnum']);
            $query->is_archive = true;
            $query->is_date = true;
            $query->is_month = true;
        } elseif (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'topics'
            && isset($query->query_vars['year']) && !isset($query->query_vars['monthnum'])) {
            $query->set('post_type', 'topics');
            $query->set('year', $query->query_vars['year']);
            $query->is_archive = true;
            $query->is_date = true;
            $query->is_year = true;
        }
    }
}
add_action('pre_get_posts', 'shinsei_kouki_topics_date_archive_query');

function shinsei_kouki_limit_search_to_topics($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (isset($query->query_vars['s']) && !empty($query->query_vars['s'])) {
            $query->set('post_type', 'topics');
            $query->is_search = true;
            $query->is_archive = false;
            $query->is_post_type_archive = false;
            $query->is_date = false;
            $query->is_month = false;
            $query->is_year = false;
        } elseif ($query->is_search()) {
            $query->set('post_type', 'topics');
        }
    }
}
add_action('pre_get_posts', 'shinsei_kouki_limit_search_to_topics', 10);

function shinsei_kouki_force_search_topics_template($template) {
    global $wp_query;
    if (isset($_GET['s']) && !empty($_GET['s'])) {
        $post_type = get_query_var('post_type');
        if (empty($post_type) || $post_type === 'topics') {
            $search_template = locate_template('search-topics.php');
            if ($search_template) {
                return $search_template;
            }
        }
    } elseif (is_search() && (get_query_var('post_type') === 'topics' || (is_array($wp_query->query_vars['post_type']) && in_array('topics', $wp_query->query_vars['post_type'])))) {
        $search_template = locate_template('search-topics.php');
        if ($search_template) {
            return $search_template;
        }
    }
    return $template;
}
add_filter('template_include', 'shinsei_kouki_force_search_topics_template', 99);

function shinsei_kouki_migrate_news_to_topics() {
    if (get_option('shinsei_kouki_news_migrated_to_topics')) {
        return;
    }
    global $wpdb;
    $updated = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_type = %s WHERE post_type = %s",
        'topics',
        'news'
    ));
    if ($updated !== false) {
        update_option('shinsei_kouki_news_migrated_to_topics', true);
        flush_rewrite_rules();
    }
}
add_action('init', 'shinsei_kouki_migrate_news_to_topics', 20);

// =============================================================================
// お知らせ：目次（INDEX）メタボックス
// =============================================================================

function shinsei_kouki_add_news_index_meta_box() {
    add_meta_box(
        'shinsei_kouki_news_index_meta_box',
        '目次（INDEX）',
        'shinsei_kouki_news_index_meta_box_callback',
        'topics',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'shinsei_kouki_add_news_index_meta_box');

function shinsei_kouki_news_index_meta_box_callback($post) {
    wp_nonce_field('shinsei_kouki_news_index_meta_box', 'shinsei_kouki_news_index_meta_box_nonce');
    $show_index = get_post_meta($post->ID, '_show_news_index', true);
    if ($show_index === '') {
        $show_index = '1';
    }
    echo '<label for="show_news_index">';
    echo '<input type="checkbox" id="show_news_index" name="show_news_index" value="1" ' . checked($show_index, '1', false) . ' />';
    echo ' この記事で目次を表示する';
    echo '</label>';
    echo '<p class="description">本文中の h2 見出しから目次を自動生成します。チェックを外すと目次を表示しません。</p>';
}

function shinsei_kouki_save_news_index_meta_box($post_id) {
    if (!isset($_POST['shinsei_kouki_news_index_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['shinsei_kouki_news_index_meta_box_nonce'], 'shinsei_kouki_news_index_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $value = isset($_POST['show_news_index']) && $_POST['show_news_index'] === '1' ? '1' : '0';
    update_post_meta($post_id, '_show_news_index', $value);
}
add_action('save_post', 'shinsei_kouki_save_news_index_meta_box');

// =============================================================================
// お知らせ：ピックアップメタボックス
// =============================================================================

function shinsei_kouki_add_pickup_meta_box() {
    add_meta_box(
        'shinsei_kouki_pickup_meta_box',
        'ピックアップ設定',
        'shinsei_kouki_pickup_meta_box_callback',
        'topics',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'shinsei_kouki_add_pickup_meta_box');

function shinsei_kouki_pickup_meta_box_callback($post) {
    wp_nonce_field('shinsei_kouki_pickup_meta_box', 'shinsei_kouki_pickup_meta_box_nonce');
    $is_pickup = get_post_meta($post->ID, '_is_pickup', true);
    echo '<label for="is_pickup">';
    echo '<input type="checkbox" id="is_pickup" name="is_pickup" value="1" ' . checked($is_pickup, '1', false) . ' />';
    echo ' ピックアップに表示する';
    echo '</label>';
    echo '<p class="description">チェックを入れると、サイドバーの「pickup topics」に表示されます。</p>';
}

function shinsei_kouki_save_pickup_meta_box($post_id) {
    if (!isset($_POST['shinsei_kouki_pickup_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['shinsei_kouki_pickup_meta_box_nonce'], 'shinsei_kouki_pickup_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (get_post_type($post_id) !== 'topics') {
        return;
    }
    if (isset($_POST['is_pickup']) && $_POST['is_pickup'] === '1') {
        update_post_meta($post_id, '_is_pickup', '1');
    } else {
        delete_post_meta($post_id, '_is_pickup');
    }
}
add_action('save_post', 'shinsei_kouki_save_pickup_meta_box');
