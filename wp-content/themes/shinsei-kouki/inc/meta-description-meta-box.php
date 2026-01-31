<?php
/**
 * meta-description-meta-box.php
 * 固定ページのメタディスクリプションを編集画面で設定するメタボックス
 * （お知らせ topics は共通文言＋記事タイトルで自動生成するためメタボックスなし）
 */

function shinsei_kouki_add_meta_description_meta_box() {
    add_meta_box(
        'shinsei_kouki_meta_description_meta_box',
        'メタディスクリプション',
        'shinsei_kouki_meta_description_meta_box_callback',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'shinsei_kouki_add_meta_description_meta_box');

function shinsei_kouki_meta_description_meta_box_callback($post) {
    wp_nonce_field('shinsei_kouki_meta_description_meta_box', 'shinsei_kouki_meta_description_meta_box_nonce');

    $value = get_post_meta($post->ID, '_meta_description', true);

    echo '<p><label for="meta_description">検索結果やSNSシェア時に表示される説明文（120〜160文字程度を推奨）</label></p>';
    echo '<textarea id="meta_description" name="meta_description" rows="3" class="large-text" style="width:100%;">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">未入力の場合は抜粋、またはカスタマイザー「メタ情報」のデフォルトが使われます。</p>';
}

function shinsei_kouki_save_meta_description_meta_box($post_id) {
    if (!isset($_POST['shinsei_kouki_meta_description_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['shinsei_kouki_meta_description_meta_box_nonce'], 'shinsei_kouki_meta_description_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $value = isset($_POST['meta_description']) ? sanitize_textarea_field($_POST['meta_description']) : '';
    update_post_meta($post_id, '_meta_description', $value);
}
add_action('save_post', 'shinsei_kouki_save_meta_description_meta_box');
