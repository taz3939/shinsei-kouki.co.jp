<?php
/**
 * search-topics.php
 * お知らせ検索結果ページテンプレート
 */
get_header();
?>

<section id="newsIntro">
    <div class="inner">
        <span class="pageTitle">
            お知らせ
            <small>topics</small>
        </span>
        <?php if (get_search_query()) : ?>
            <h1>
                「<strong><?php echo esc_html(get_search_query()); ?></strong>」の検索結果
            </h1>
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
                <p class="noResults">
                    検索キーワードに一致するお知らせが見つかりませんでした。<br>
                    別のキーワードでお探しになるか、<br>
                    <a href="<?php echo esc_url(home_url('/topics')); ?>">お知らせ一覧</a>からお探しください。<br>
                </p>
                <?php endif; ?>

                <?php if (have_posts()) : ?>
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
                <?php endif; ?>
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
