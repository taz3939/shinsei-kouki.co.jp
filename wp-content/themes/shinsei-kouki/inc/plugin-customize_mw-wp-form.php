<?php
/**
 * plugin-customize_mw-wp-form.php
 * MW WP Form プラグインのカスタマイズ：style.css 除外・wpautop 無効化・保存時のマークアップ維持
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * フォーム保存時のみ <section> を許可（管理画面でマークアップが解除されないようにする）
 */
add_filter('wp_insert_post_data', function($data, $postarr) {
	if (isset($data['post_type']) && $data['post_type'] === 'mw-wp-form') {
		$GLOBALS['shinsei_kouki_mwform_saving'] = true;
	}
	return $data;
}, 5, 2);

add_action('wp_insert_post', function($post_id) {
	$GLOBALS['shinsei_kouki_mwform_saving'] = false;
}, 10, 1);

add_filter('wp_kses_allowed_html', function($allowed, $context) {
	if ($context === 'post' && !empty($GLOBALS['shinsei_kouki_mwform_saving'])) {
		$allowed['section'] = array('class' => array(), 'id' => array());
	}
	return $allowed;
}, 10, 2);

/**
 * フォーム編集でブロックエディタを無効化（クラシックエディタ＋テキストタブで編集すると遷移後もマークアップが崩れにくい）
 */
add_filter('use_block_editor_for_post_type', function($use, $post_type) {
	if ($post_type === 'mw-wp-form') {
		return false;
	}
	return $use;
}, 10, 2);

// プラグインのデフォルト style.css を除外（テーマ側でスタイルを当てるため）
add_action('wp_print_styles', function() {
	if (!is_admin()) {
		wp_dequeue_style('mw-wp-form');
	}
}, 5);

add_filter('style_loader_tag', function($html, $handle) {
	if (!is_admin() && $handle === 'mw-wp-form') {
		return '';
	}
	return $html;
}, 10, 2);

add_action('init', 'shinsei_kouki_mw_wp_form_setup', 20);

function shinsei_kouki_mw_wp_form_setup() {
	if (!class_exists('MW_WP_Form_Admin')) {
		return;
	}
	$mw_wp_form_admin = new MW_WP_Form_Admin();
	$forms = $mw_wp_form_admin->get_forms();
	if (empty($forms) || !is_array($forms)) {
		return;
	}
	foreach ($forms as $form) {
		add_filter('mwform_content_wpautop_mw-wp-form-' . $form->ID, '__return_false');
		add_filter('mwform_error_message_mw-wp-form-' . $form->ID, 'shinsei_kouki_mw_wp_form_error_message', 10, 3);
	}
}

/**
 * フォーム内「個人情報保護方針」を個人情報保護方針ページへのリンクに置換（ラベル表示部分のみ）
 */
add_filter('the_content', function($content) {
	if (strpos($content, '個人情報保護方針に同意する') === false) {
		return $content;
	}
	$link = '<a href="' . esc_url(home_url('/privacy-policy/')) . '" target="_blank" rel="noopener">個人情報保護方針</a>';
	// value 属性はそのままにし、ラベル内の表示テキストのみ置換（> の直後のテキストを対象）
	$content = preg_replace('/(>)\s*個人情報保護方針に同意する(\s*<)/', '$1' . $link . 'に同意する$2', $content);
	return $content;
}, 20);

/**
 * select（お問い合わせ種別）の必須エラーを「未選択です。」に変更
 *
 * @param string $error プラグインのデフォルトメッセージ（例: 未入力です。）
 * @param string $name  フィールド名
 * @param string $rule  バリデーションルール名
 * @return string
 */
function shinsei_kouki_mw_wp_form_error_message($error, $name, $rule) {
	if ($name === 'inquiry_type' && $rule === 'noempty') {
		return '未選択です。';
	}
	return $error;
}
