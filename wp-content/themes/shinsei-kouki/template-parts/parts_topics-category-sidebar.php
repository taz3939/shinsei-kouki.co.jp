<?php
/**
 * template-parts/parts_topics-category-sidebar.php
 * お知らせカテゴリ一覧サイドバー
 */

$terms = get_terms(array(
	'taxonomy'   => 'topics_category',
	'hide_empty' => true,
	'orderby'    => 'count',
	'order'      => 'DESC',
));

if (!empty($terms) && !is_wp_error($terms)) :
?>
<aside class="topicsCategory">
	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/logo-ci.svg" alt="" width="26" height="24" class="sidebarLogo" decoding="async" loading="lazy" aria-hidden="true">
	<h2>categories</h2>
	<ul class="topicsCategoryList">
		<?php foreach ($terms as $term) :
			$link = get_term_link($term);
			if (is_wp_error($link)) {
				continue;
			}
		?>
		<li>
			<a href="<?php echo esc_url($link); ?>">
				<span class="categoryName"><?php echo esc_html($term->name); ?></span>
				<span class="postsCount">(<?php echo (int) $term->count; ?>)</span>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</aside>
<?php endif; ?>
