<?php
/**
 * archive-topics.php
 * お知らせ一覧ページテンプレート
 */

// 検索クエリが存在する場合は処理をスキップ（search-topics.phpで処理）
if (get_search_query() || (isset($_GET['s']) && !empty($_GET['s']))) {
    // template_includeフィルターでsearch-topics.phpが使用されるため、ここでは何もしない
    // ただし、WordPressがarchive-topics.phpを選択した場合は処理をスキップ
    return;
}

get_header();
?>

<section id="newsIntro">
    <div class="inner">
        <?php
        // 月別アーカイブの場合の判定（クエリ変数を直接確認）
        $year = get_query_var('year');
        $month = get_query_var('monthnum');
        if ($year && $month && is_numeric($year) && is_numeric($month)) : ?>
            <?php
            // 月別アーカイブの場合
            $date_obj = DateTime::createFromFormat('Y-n', $year . '-' . $month);
            if ($date_obj) {
                $date_text = $date_obj->format('Y年n月');
            } else {
                $date_text = '';
            }
            ?>
            <span class="pageTitle">
                お知らせ
                <small>topics</small>
            </span>
            <h1>
                <?php echo esc_html($date_text); ?>に公開されたお知らせ。
            </h1>
        <?php else : ?>
            <h1 class="pageTitle">
                お知らせ
                <small>topics</small>
            </h1>
            <p>
                神西衡機工業からのお知らせ、および日々の活動報告を掲載しております。<br>
                新製品のご案内やメンテナンスに関する情報、夏季・冬季休暇などの最新情報を随時更新してまいります。<br>
                お客様の業務にお役立ていただける情報を発信してまいりますので、ぜひご覧ください。
            </p>
        <?php endif; ?>
    </div>
</section>
<section id="newsArchive">
    <div class="inner">
        <div class="newsArchiveContainer">
            <div class="newsMain">
                <?php if (have_posts()) : ?>
                <ul class="newsList">
                    <?php while (have_posts()) : the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <?php shinsei_kouki_topics_thumbnail(null, 'medium', 'newsThumbnail'); ?>
                            <div class="newsContent">
                                <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
                                <span><?php the_title(); ?></span>
                            </div>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>

                <?php else : ?>
                <p>お知らせはありません。</p>
                <?php endif; ?>

                <div class="paginationArea">
                    <?php
                    // ページネーション表示
                    if (function_exists('shinsei_kouki_pagination')) {
                        shinsei_kouki_pagination();
                    }
                    ?>
                    
                    <?php
                    // 記事数表示
                    global $wp_query;
                    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                    // 実際の表示件数を取得（pre_get_postsで変更されている可能性があるため）
                    $posts_per_page = isset($wp_query->query_vars['posts_per_page']) && $wp_query->query_vars['posts_per_page'] > 0 
                        ? $wp_query->query_vars['posts_per_page'] 
                        : get_option('posts_per_page');
                    $total = $wp_query->found_posts;
                    $start = ($paged - 1) * $posts_per_page + 1;
                    $end = min($paged * $posts_per_page, $total);
                    
                    // 1記事1ページの場合は「開始」のみ表示、複数記事の場合は「開始〜終了」を表示
                    if ($start == $end) {
                        $pagination_text = $start . '／全' . $total . '記事中';
                    } else {
                        $pagination_text = $start . '〜' . $end . '／全' . $total . '記事中';
                    }
                    ?>
                    <p class="paginationInfo"><?php echo $pagination_text; ?></p>
                </div>
            </div>
            
            <div class="newsSidebar">
                <?php get_template_part('template-parts/parts_pickup-sidebar'); ?>
                <?php get_template_part('template-parts/parts_monthly-archive-sidebar'); ?>
                <?php get_template_part('template-parts/parts_search-sidebar'); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
