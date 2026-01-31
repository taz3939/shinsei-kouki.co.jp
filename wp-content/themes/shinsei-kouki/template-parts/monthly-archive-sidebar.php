<?php
/**
 * template-parts/monthly-archive-sidebar.php
 * 月別アーカイブサイドバー
 */

// 月別アーカイブを取得（topics投稿タイプのみ）
global $wpdb;
$archives = $wpdb->get_results("
    SELECT DISTINCT 
        YEAR(post_date) AS year,
        MONTH(post_date) AS month,
        COUNT(ID) AS posts
    FROM {$wpdb->posts}
    WHERE post_type = 'topics'
    AND post_status = 'publish'
    GROUP BY YEAR(post_date), MONTH(post_date)
    ORDER BY year DESC, month DESC
");

if (!empty($archives)) :
    // 年ごとにグループ化
    $grouped_archives = array();
    foreach ($archives as $archive) {
        $year = $archive->year;
        if (!isset($grouped_archives[$year])) {
            $grouped_archives[$year] = array();
        }
        $grouped_archives[$year][] = $archive;
    }
    
    // 最新年度を取得（最初のキーが最新）
    $latest_year = !empty($grouped_archives) ? max(array_keys($grouped_archives)) : null;
?>
<aside class="monthlyArchive">
    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/logo-ci.svg" alt="" width="26" height="24" class="sidebarLogo" decoding="async" aria-hidden="true">
    <h2>monthly archives</h2>
    <div class="monthlyArchiveList">
        <?php foreach ($grouped_archives as $year => $months) : 
            $is_open = ($year === $latest_year) ? 'isOpen' : '';
        ?>
        <div class="yearGroup">
            <button type="button" class="yearTitle <?php echo esc_attr($is_open); ?>" aria-expanded="<?php echo ($year === $latest_year) ? 'true' : 'false'; ?>">
                <span class="yearText"><?php echo esc_html($year); ?>年</span>
                <span class="yearToggle"></span>
            </button>
            <ul class="monthList <?php echo esc_attr($is_open); ?>">
                <?php foreach ($months as $archive) : 
                    $month = $archive->month;
                    $posts_count = $archive->posts;
                    $archive_url = home_url('/topics/' . $year . '/' . sprintf('%02d', $month) . '/');
                    $month_name = date_i18n('n月', mktime(0, 0, 0, $month, 1, $year));
                ?>
                <li>
                    <a href="<?php echo esc_url($archive_url); ?>">
                        <span class="monthName"><?php echo esc_html($month_name); ?></span>
                        <span class="postsCount">(<?php echo esc_html($posts_count); ?>)</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>
    </div>
</aside>
<?php endif; ?>
