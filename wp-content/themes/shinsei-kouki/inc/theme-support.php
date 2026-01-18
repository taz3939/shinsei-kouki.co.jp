<?php
/**
 * theme-support.php
 * テーマサポート機能
 */

add_action('after_setup_theme', function() {
    // タイトルタグの自動生成を有効化
    add_theme_support('title-tag');
    
    // アイキャッチ画像を有効化
    add_theme_support('post-thumbnails');
    
    // HTML5マークアップを有効化
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
});

// 管理画面メニューから「投稿」を非表示
function shinsei_kouki_remove_admin_menus() {
    remove_menu_page('edit.php'); // 投稿
}
add_action('admin_menu', 'shinsei_kouki_remove_admin_menus');
