<?php
/**
 * news-index-meta-box.php
 * お知らせ詳細ページの目次（INDEX）表示の有無を設定するメタボックス
 */

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
