<?php
/**
 * plugin-customize_mw-wp-form.php
 * MW WP Form プラグインのカスタマイズ：style.css 除外・wpautop 無効化・保存時のマークアップ維持・管理画面表示の簡略化
 *
 * ※ 管理画面の非表示（問い合わせデータの「グラフを表示」列、お問い合わせ一覧・詳細の「管理者メール送信先」）は
 *    プラグインのテンプレート・フック（manage_*_posts_columns, mwform_template_render）に依存しています。
 *    プラグインアップデートでクラス名・テンプレート構造が変わった場合は要確認・要修正。
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
		add_filter('mwform_admin_mail_raw_mw-wp-form-' . $form->ID, 'shinsei_kouki_mw_wp_form_add_serial_to_subject', 10, 3);
		add_filter('mwform_auto_mail_raw_mw-wp-form-' . $form->ID, 'shinsei_kouki_mw_wp_form_auto_mail', 10, 3);
		add_filter('mwform_validation_mw-wp-form-' . $form->ID, 'shinsei_kouki_mw_wp_form_validation', 10, 2);
		add_action('mwform_contact_data_save-mwf_' . $form->ID, 'shinsei_kouki_delete_sales_inquiry');
	}
}

/**
 * 管理者宛メールの件名にお問い合わせ連番を付与（例：末尾に 【No.1】）
 *
 * @param object $Mail_raw MW WP Form のメールオブジェクト
 * @param array  $values   送信データ
 * @param object $Data     フォームデータ
 * @return object
 */
/**
 * 「その他」のうち、営業・取材・掲載として扱う選択肢。
 */
function shinsei_kouki_inquiry_other_sales_label() {
	return '上記以外（営業・ご提案、取材、掲載など）';
}

/**
 * 管理者へ送らず、一覧にも残さない問い合わせか。
 *
 * @param array $values 送信データ
 * @return bool
 */
function shinsei_kouki_is_sales_inquiry($values) {
	if (!is_array($values)) {
		return false;
	}
	$type  = isset($values['inquiry_type']) ? (string) $values['inquiry_type'] : '';
	$other = isset($values['inquiry_other']) ? (string) $values['inquiry_other'] : '';
	return $type === 'その他' && $other === shinsei_kouki_inquiry_other_sales_label();
}

/**
 * 通常のメール本文に、その他の選択内容を足す。
 *
 * @param object $Mail メールオブジェクト
 * @return object
 */
function shinsei_kouki_append_inquiry_other_to_mail($Mail, $values) {
	if (empty($values['inquiry_other']) || !isset($Mail->body) || strpos($Mail->body, '{inquiry_other}') !== false) {
		return $Mail;
	}
	$line = "● その他の内容：{inquiry_other}";
	if (strpos($Mail->body, '{inquiry_type}') !== false) {
		$Mail->body = preg_replace(
			'/^(.*\{inquiry_type\}.*)$/m',
			'$1' . "\n\n" . $line,
			$Mail->body,
			1
		);
		return $Mail;
	}
	if ($Mail->body !== '') {
		$Mail->body .= "\n\n" . $line . "\n";
	}
	return $Mail;
}

function shinsei_kouki_mw_wp_form_add_serial_to_subject($Mail_raw, $values, $Data) {
	if (shinsei_kouki_is_sales_inquiry($values)) {
		$GLOBALS['shinsei_kouki_sales_inquiry'] = true;
		$GLOBALS['shinsei_kouki_skip_admin_mail_once'] = true;
		$Mail_raw->to  = '';
		$Mail_raw->cc  = '';
		$Mail_raw->bcc = '';
		return $Mail_raw;
	}

	$Mail_raw   = shinsei_kouki_append_inquiry_other_to_mail($Mail_raw, $values);
	$option_key = 'shinsei_kouki_inquiry_serial_number';
	$num        = (int) get_option($option_key, 0);
	$num++;
	update_option($option_key, $num);

	if (isset($Mail_raw->subject)) {
		$Mail_raw->subject = '【お問い合わせ】No.' . $num;
	}

	return $Mail_raw;
}

/**
 * 送信先が空の管理者メールは、営業・取材・掲載のときだけ成功扱いにする。
 * これがないと、データベース保存がオフの環境ではエラー画面になり、自動返信も送られない。
 */
add_filter('mwform_is_mail_sended', function ($is_sended) {
	if (!empty($GLOBALS['shinsei_kouki_skip_admin_mail_once'])) {
		$GLOBALS['shinsei_kouki_skip_admin_mail_once'] = false;
		return true;
	}
	return $is_sended;
});

/**
 * データベース保存がオンのとき、営業・取材・掲載は保存された直後に削除する。
 * 連番は管理者メールの処理で増やしていない。
 *
 * @param int $post_id 保存された問い合わせデータ
 */
function shinsei_kouki_delete_sales_inquiry($post_id) {
	if (empty($GLOBALS['shinsei_kouki_sales_inquiry'])) {
		return;
	}
	$GLOBALS['shinsei_kouki_sales_inquiry'] = false;
	wp_delete_post((int) $post_id, true);
}

/**
 * 自動返信。営業・取材・掲載は文面を差し替える。
 *
 * @param object $Mail   メールオブジェクト
 * @param array  $values 送信データ
 * @param object $Data   フォームデータ
 * @return object
 */
function shinsei_kouki_mw_wp_form_auto_mail($Mail, $values, $Data) {
	if (!shinsei_kouki_is_sales_inquiry($values)) {
		return shinsei_kouki_append_inquiry_other_to_mail($Mail, $values);
	}

	$name = isset($values['name']) ? $values['name'] : '';
	if ($Mail->to === '' && !empty($values['email'])) {
		$Mail->to = $values['email'];
	}
	$Mail->subject = '【神西衡機工業株式会社】お問い合わせについて';
	$Mail->body    = $name . " 様\n\n"
		. "この度はお問い合わせいただき、ありがとうございます。\n\n"
		. "誠に恐れ入りますが、商品・サービスの売り込み、取材、掲載のご提案はお受けしておりません。\n"
		. "担当者からの連絡はいたしませんので、あらかじめご了承ください。\n\n"
		. "神西衡機工業株式会社では、計量器に関するご相談のみ対応しております。\n\n"
		. "────────────────────\n"
		. "神西衡機工業株式会社\n\n"
		. "・本社\n"
		. "　〒250-0863 神奈川県小田原市飯泉90-9\n"
		. "　TEL: 0465-55-8644　FAX: 0465-55-8522\n\n"
		. "・成田営業所\n"
		. "　〒250-0862 神奈川県小田原市成田1048\n"
		. "　TEL: 0465-38-1194　FAX: 0465-36-1294\n"
		. "────────────────────\n";
	return $Mail;
}

/**
 * 「その他」のときだけ、ラジオの選択を必須にする。
 *
 * @param object $Validation バリデーション
 * @param array  $data       送信データ
 * @return object
 */
function shinsei_kouki_mw_wp_form_validation($Validation, $data) {
	$type = isset($data['inquiry_type']) ? (string) $data['inquiry_type'] : '';
	if ($type === 'その他') {
		$Validation->set_rule('inquiry_other', 'noempty', array(
			'message' => '未選択です。',
		));
	}
	return $Validation;
}

/**
 * 送信直後のセッションから、営業・取材・掲載かどうかを見る。
 * フォーム番号はローカル・demo・本番で違う。
 *
 * @return bool
 */
function shinsei_kouki_is_sales_inquiry_in_session() {
	if (!class_exists('MW_WP_Form_Data') || !class_exists('MW_WP_Form_Admin')) {
		return false;
	}
	$forms = (new MW_WP_Form_Admin())->get_forms();
	if (!is_array($forms)) {
		return false;
	}
	foreach ($forms as $form) {
		$form_key = 'mw-wp-form-' . $form->ID;
		if (empty($_COOKIE['mw-wp-form_session_' . $form_key])) {
			continue;
		}
		$Data = MW_WP_Form_Data::connect($form_key);
		if (shinsei_kouki_is_sales_inquiry($Data->gets())) {
			return true;
		}
	}
	return false;
}

/**
 * 営業・取材・掲載の完了画面だけ、担当者から連絡する旨を出さない。
 */
add_filter('the_content', function ($content) {
	if (!is_page('complete') || !shinsei_kouki_is_sales_inquiry_in_session()) {
		return $content;
	}
	$sales = '<p>お問い合わせありがとうございます。<br>'
		. '商品・サービスの売り込み、取材、掲載のご提案はお受けしておりません。<br>'
		. '担当者からの連絡はいたしません。</p>'
		. '<p>ご入力いただいたメールアドレスへ、その旨のメールをお送りしています。</p>';
	$replaced = preg_replace(
		'/(<h1 class="pageTitle">[\s\S]*?<\/h1>\s*)<p>[\s\S]*?<\/p>\s*<p>[\s\S]*?<\/p>/',
		'$1' . $sales,
		$content,
		1
	);
	return is_string($replaced) ? $replaced : $content;
}, 15);

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
	if (($name === 'inquiry_type' || $name === 'inquiry_other') && $rule === 'noempty') {
		return '未選択です。';
	}
	return $error;
}

// =============================================================================
// 管理画面：不要な列・項目の非表示（問い合わせデータ・お問い合わせ一覧・詳細）
// =============================================================================

/**
 * 問い合わせデータ一覧で「グラフを表示」列を非表示（CSS）
 */
add_action('admin_head', function() {
	$screen = get_current_screen();
	if (!$screen || $screen->id !== 'mw-wp-form_page_mw-wp-form-save-data') {
		return;
	}
	echo '<style>.mw-wp-form-table-chart{display:none !important;}</style>' . "\n";
}, 20);

/**
 * お問い合わせ一覧で「管理者メール送信先」列を非表示（manage_*_posts_columns で unset）
 */
add_action('admin_init', function() {
	if (!is_admin() || !class_exists('MW_WP_Form_Contact_Data_Setting')) {
		return;
	}
	$post_types = MW_WP_Form_Contact_Data_Setting::get_form_post_types();
	if (empty($post_types) || !is_array($post_types)) {
		return;
	}
	foreach ($post_types as $post_type) {
		add_filter('manage_' . $post_type . '_posts_columns', function($columns) {
			unset($columns['admin_mail_to']);
			return $columns;
		}, 20);
	}
}, 20);

/**
 * お問い合わせ詳細で「管理者メール送信先」の行のみ非表示（<th> がその文言の 1 行だけ削除）
 */
add_filter('mwform_template_render', function($html, $template) {
	if ($template !== 'contact-data/detail') {
		return $html;
	}
	// <th> 内が「管理者メール送信先」または「Admin Email To」の 1 行だけ削除（隣の <td> まで含む）
	$html = preg_replace(
		'/<tr[^>]*>\s*<th[^>]*>\s*(?:管理者メール送信先|Admin Email To)\s*<\/th>\s*<td[^>]*>[\s\S]*?<\/td>\s*<\/tr>/u',
		'',
		$html,
		1
	);
	return $html;
}, 10, 2);
