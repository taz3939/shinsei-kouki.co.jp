<?php
/**
 * post-type_reference-weight.php
 * 所持している基準器分銅等：カスタム投稿タイプと管理画面のメタボックス
 */

if (!defined('ABSPATH')) {
	exit;
}

/* テーマ有効化時：基準器分銅が0件ならサンプルを1件作成 */
add_action('after_switch_theme', function() {
	if (get_posts(array('post_type' => 'reference_weight', 'post_status' => 'any', 'posts_per_page' => 1))) {
		return;
	}
	$id = wp_insert_post(array(
		'post_type'   => 'reference_weight',
		'post_title'  => '標準分銅',
		'post_status' => 'publish',
	));
	if ($id && !is_wp_error($id)) {
		update_post_meta($id, '_weight_symbol', 'F1クラス');
		update_post_meta($id, '_weight_no', '84');
		update_post_meta($id, '_weight_nominal', '20kg～1mg');
		update_post_meta($id, '_weight_quantity', '23');
		update_post_meta($id, '_weight_material', 'ステンレス製OIML形・枕型');
		update_post_meta($id, '_weight_order', '0');
	}
});

/* カスタム投稿タイプ：基準器分銅 */
add_action('init', function() {
	register_post_type('reference_weight', array(
		'labels' => array(
			'name'               => '基準器分銅',
			'singular_name'      => '基準器分銅',
			'add_new'            => '新規追加',
			'add_new_item'       => '基準器分銅を追加',
			'edit_item'          => '基準器分銅を編集',
			'new_item'           => '新規',
			'view_item'          => '基準器分銅を表示',
			'search_items'       => '基準器分銅を検索',
			'not_found'          => '基準器分銅が見つかりません',
			'not_found_in_trash' => 'ゴミ箱に基準器分銅はありません',
			'menu_name'          => '基準器分銅',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => 9,
		'menu_icon'           => 'dashicons-performance',
		'supports'            => array('title'),
	));
});

/* メタボックス：記号・№・表す量・個数・材質・形状・表示順 */
add_action('add_meta_boxes', function() {
	add_meta_box(
		'reference_weight_data',
		'基準器分銅データ',
		'shinsei_kouki_reference_weight_meta_box_cb',
		'reference_weight',
		'normal'
	);
});

function shinsei_kouki_reference_weight_meta_box_cb($post) {
	wp_nonce_field('reference_weight_data', 'reference_weight_data_nonce');
	$symbol   = get_post_meta($post->ID, '_weight_symbol', true);
	$no       = get_post_meta($post->ID, '_weight_no', true);
	$nominal  = get_post_meta($post->ID, '_weight_nominal', true);
	$quantity = get_post_meta($post->ID, '_weight_quantity', true);
	$material = get_post_meta($post->ID, '_weight_material', true);
	$order    = get_post_meta($post->ID, '_weight_order', true);
	if ($order === '') {
		$order = '0';
	}
	?>
	<p><strong>タイトル</strong>が「名称」として表に表示されます。</p>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="weight_symbol">記号</label></th>
			<td><input type="text" id="weight_symbol" name="weight_symbol" value="<?php echo esc_attr($symbol); ?>" class="regular-text"> <span class="description">実用基準分銅は空欄</span></td>
		</tr>
		<tr>
			<th><label for="weight_no">№</label></th>
			<td><input type="text" id="weight_no" name="weight_no" value="<?php echo esc_attr($no); ?>" class="small-text"></td>
		</tr>
		<tr>
			<th><label for="weight_nominal">表す量</label></th>
			<td><input type="text" id="weight_nominal" name="weight_nominal" value="<?php echo esc_attr($nominal); ?>" class="regular-text"> <span class="description">例：20kg～1mg</span></td>
		</tr>
		<tr>
			<th><label for="weight_quantity">個数</label></th>
			<td><input type="number" id="weight_quantity" name="weight_quantity" value="<?php echo esc_attr($quantity); ?>" min="0" class="small-text"> 個</td>
		</tr>
		<tr>
			<th><label for="weight_material">材質・形状</label></th>
			<td><input type="text" id="weight_material" name="weight_material" value="<?php echo esc_attr($material); ?>" class="large-text"> <span class="description">例：ステンレス製OIML形・枕型</span></td>
		</tr>
		<tr>
			<th><label for="weight_order">表示順</label></th>
			<td><input type="number" id="weight_order" name="weight_order" value="<?php echo esc_attr($order); ?>" min="0" class="small-text"> <span class="description">小さいほど上に表示</span></td>
		</tr>
	</table>
	<?php
}

add_action('save_post_reference_weight', function($post_id) {
	if (!isset($_POST['reference_weight_data_nonce']) || !wp_verify_nonce($_POST['reference_weight_data_nonce'], 'reference_weight_data')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	$fields = array(
		'weight_symbol'   => '_weight_symbol',
		'weight_no'       => '_weight_no',
		'weight_nominal'  => '_weight_nominal',
		'weight_quantity' => '_weight_quantity',
		'weight_material' => '_weight_material',
		'weight_order'    => '_weight_order',
	);
	foreach ($fields as $post_key => $meta_key) {
		if (isset($_POST[$post_key])) {
			$value = sanitize_text_field($_POST[$post_key]);
			if ($post_key === 'weight_order' && $value === '') {
				$value = '0';
			}
			update_post_meta($post_id, $meta_key, $value);
		}
	}
}, 10, 1);

/* 一覧に「表示順」「個数」列を追加 */
add_filter('manage_reference_weight_posts_columns', function($columns) {
	$new = array();
	$new['cb'] = $columns['cb'];
	$new['title'] = '名称';
	$new['weight_order'] = '表示順';
	$new['weight_symbol'] = '記号';
	$new['weight_no'] = '№';
	$new['weight_nominal'] = '表す量';
	$new['weight_quantity'] = '個数';
	$new['weight_material'] = '材質・形状';
	$new['date'] = $columns['date'];
	return $new;
});

add_filter('manage_reference_weight_posts_custom_column', function($column, $post_id) {
	switch ($column) {
		case 'weight_order':
			echo esc_html(get_post_meta($post_id, '_weight_order', true) ?: '0');
			break;
		case 'weight_symbol':
			echo esc_html(get_post_meta($post_id, '_weight_symbol', true));
			break;
		case 'weight_no':
			echo esc_html(get_post_meta($post_id, '_weight_no', true));
			break;
		case 'weight_nominal':
			echo esc_html(get_post_meta($post_id, '_weight_nominal', true));
			break;
		case 'weight_quantity':
			echo esc_html(get_post_meta($post_id, '_weight_quantity', true));
			break;
		case 'weight_material':
			echo esc_html(get_post_meta($post_id, '_weight_material', true));
			break;
	}
}, 10, 2);

add_filter('manage_edit-reference_weight_sortable_columns', function($columns) {
	$columns['weight_order'] = 'weight_order';
	$columns['weight_quantity'] = 'weight_quantity';
	return $columns;
});

add_action('pre_get_posts', function($query) {
	if (!is_admin() || !$query->is_main_query()) {
		return;
	}
	if ($query->get('post_type') !== 'reference_weight') {
		return;
	}
	$orderby = $query->get('orderby');
	if ($orderby === 'weight_order') {
		$query->set('meta_key', '_weight_order');
		$query->set('orderby', 'meta_value_num');
		$query->set('order', 'ASC');
	} elseif ($orderby === 'weight_quantity') {
		$query->set('meta_key', '_weight_quantity');
		$query->set('orderby', 'meta_value_num');
	}
});
