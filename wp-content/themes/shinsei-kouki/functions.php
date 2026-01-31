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
    'inc/pickup-meta-box.php',     // ピックアップ用カスタムメタボックス
    'inc/news-index-meta-box.php', // お知らせ詳細の目次（INDEX）表示設定
    'inc/company-options.php',     // 会社情報（カスタマイザー）
    'inc/meta-tags.php',           // メタディスクリプション・canonical・OGP
    'inc/meta-description-meta-box.php', // 固定ページのメタディスクリプション
    'inc/external-link-icon.php',  // 投稿本文内の外部リンクにアイコンを追加
];

foreach ($includes as $file) {
    if (file_exists(get_template_directory() . '/' . $file)) {
        require_once get_template_directory() . '/' . $file;
    }
}

// お知らせ一覧ページの表示件数を1件に設定（スタイル設定用）
// function shinsei_kouki_set_topics_posts_per_page($query) {
//     if (!is_admin() && $query->is_main_query()) {
//         // お知らせのアーカイブページか、topicsのクエリの場合
//         if ($query->is_post_type_archive('topics') || 
//             (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] === 'topics')) {
//             $query->set('posts_per_page', 1);
//         }
//     }
// }
// add_action('pre_get_posts', 'shinsei_kouki_set_topics_posts_per_page');

/**
 * お知らせ（topics）のアイキャッチ画像を出力。未設定時はフォールバック画像を表示。
 *
 * @param int|null $post_id 投稿ID。null の場合は現在の投稿。
 * @param string   $size    画像サイズ名。
 * @param string   $figure_class figure に付与するクラス名。
 * @param array    $img_attr img タグの width/height 等。フォールバック時のみ使用。
 */
function shinsei_kouki_topics_thumbnail($post_id = null, $size = 'medium', $figure_class = 'newsThumbnail', $img_attr = array()) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$attr = array_merge(array('decoding' => 'async', 'loading' => 'lazy'), isset($img_attr['width']) ? array() : array('width' => '110', 'height' => '83'));
	if (!empty($img_attr)) {
		$attr = array_merge($attr, $img_attr);
	}
	$fallback_url = get_template_directory_uri() . '/assets/img/topics/img_no-image.webp';
	?>
	<figure class="<?php echo esc_attr($figure_class); ?>">
		<?php if (has_post_thumbnail($post_id)) : ?>
			<?php echo get_the_post_thumbnail($post_id, $size, $attr); ?>
		<?php else : ?>
			<img src="<?php echo esc_url($fallback_url); ?>" alt="" <?php echo isset($attr['width']) ? 'width="' . esc_attr($attr['width']) . '"' : ''; ?> <?php echo isset($attr['height']) ? 'height="' . esc_attr($attr['height']) . '"' : ''; ?> decoding="async" loading="lazy">
		<?php endif; ?>
	</figure>
	<?php
}

// ページネーション関数（お知らせ一覧用）
function shinsei_kouki_pagination() {
    global $wp_query;
    
    $total_pages = $wp_query->max_num_pages;
    $current_page = max(1, get_query_var('paged'));
    
    if ($total_pages <= 1) {
        return;
    }
    
    // パス形式（/topics/page/2/）のURLを直接生成
    $base_url = home_url('/topics/');
    
    // URL生成ヘルパー関数（パス形式を直接生成）
    $get_pagination_url = function($page) use ($base_url) {
        if ($page == 1) {
            return $base_url;
        }
        // パス形式を直接生成: /topics/page/2/
        return $base_url . 'page/' . $page . '/';
    };
    
    echo '<nav class="pagination" aria-label="ページネーション">';
    echo '<ul>';
    
    // 前へリンク
    if ($current_page > 1) {
        $prev_url = $get_pagination_url($current_page - 1);
        echo '<li class="paginationPrev"><a href="' . esc_url($prev_url) . '">前へ</a></li>';
    } else {
        echo '<li class="paginationPrev"><span>前へ</span></li>';
    }
    
    // ページ番号（パターン3: 現在のページの前後2ページ + 最初/最後）
    $pages_to_show = array();
    
    // 常に最初のページを追加
    $pages_to_show[] = 1;
    
    // 常に最後のページを追加
    if ($total_pages > 1) {
        $pages_to_show[] = $total_pages;
    }
    
    // 現在のページの前後2ページを追加
    $start_range = max(1, $current_page - 2);
    $end_range = min($total_pages, $current_page + 2);
    for ($i = $start_range; $i <= $end_range; $i++) {
        $pages_to_show[] = $i;
    }
    
    // 重複を除去してソート
    $pages_to_show = array_unique($pages_to_show);
    sort($pages_to_show);
    
    // ページ番号を表示
    $last_shown = 0;
    foreach ($pages_to_show as $page) {
        // 省略記号を表示（連続していない場合）
        if ($last_shown > 0 && $page - $last_shown > 1) {
            echo '<li class="paginationDots"><span>...</span></li>';
        }
        
        $class = ($page == $current_page) ? ' class="isCurrent"' : '';
        $url = $get_pagination_url($page);
        echo '<li' . $class . '><a href="' . esc_url($url) . '">' . $page . '</a></li>';
        $last_shown = $page;
    }
    
    // 次へリンク
    if ($current_page < $total_pages) {
        $next_url = $get_pagination_url($current_page + 1);
        echo '<li class="paginationNext"><a href="' . esc_url($next_url) . '">次へ</a></li>';
    } else {
        echo '<li class="paginationNext"><span>次へ</span></li>';
    }
    
    echo '</ul>';
    echo '</nav>';
}

// パンくずリスト関数
function shinsei_kouki_breadcrumbs() {
    if (is_front_page()) {
        return;
    }
    
    echo '<nav aria-label="breadcrumbs" class="breadcrumbs">';
    echo '<ol>';
    
    // ホーム
    $ico_company_url = esc_url( get_template_directory_uri() . '/assets/img/common/ico_company.svg' );
    echo '<li><a href="' . esc_url(home_url('/')) . '">';
    echo '<img src="' . $ico_company_url . '" alt="" width="30" height="30" class="iconHome" decoding="async" aria-hidden="true">';
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
