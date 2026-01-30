<?php
/**
 * pickup-meta-box.php
 * ピックアップ用カスタムメタボックス
 */

// メタボックスを追加
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

// メタボックスのコールバック関数
function shinsei_kouki_pickup_meta_box_callback($post) {
    // ノンスフィールドを追加（セキュリティ）
    wp_nonce_field('shinsei_kouki_pickup_meta_box', 'shinsei_kouki_pickup_meta_box_nonce');
    
    // 既存の値を取得
    $is_pickup = get_post_meta($post->ID, '_is_pickup', true);
    
    // チェックボックスを表示
    echo '<label for="is_pickup">';
    echo '<input type="checkbox" id="is_pickup" name="is_pickup" value="1" ' . checked($is_pickup, '1', false) . ' />';
    echo ' ピックアップに表示する';
    echo '</label>';
    echo '<p class="description">チェックを入れると、サイドバーの「pickup topics」に表示されます。</p>';
}

// メタボックスの値を保存
function shinsei_kouki_save_pickup_meta_box($post_id) {
    // ノンスの検証
    if (!isset($_POST['shinsei_kouki_pickup_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['shinsei_kouki_pickup_meta_box_nonce'], 'shinsei_kouki_pickup_meta_box')) {
        return;
    }
    
    // 自動保存時は処理をスキップ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // topics投稿タイプのみ処理
    if (get_post_type($post_id) !== 'topics') {
        return;
    }
    
    // チェックボックスの値を保存
    if (isset($_POST['is_pickup']) && $_POST['is_pickup'] === '1') {
        update_post_meta($post_id, '_is_pickup', '1');
    } else {
        delete_post_meta($post_id, '_is_pickup');
    }
}
add_action('save_post', 'shinsei_kouki_save_pickup_meta_box');
