<?php
/**
 * archive-news.php
 * お知らせ一覧ページテンプレート
 */
get_header();
?>

<section id="newsArchive">
    <div class="inner">
        <h1>お知らせ</h1>
        
        <?php if (have_posts()) : ?>
            <ul class="newsList">
                <?php while (have_posts()) : the_post(); ?>
                <li>
                    <article class="newsItem">
                        <time datetime="<?php echo get_the_date('Y-m-d'); ?>" class="newsDate"><?php echo get_the_date('Y.m.d'); ?></time>
                        <h2 class="newsTitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if (has_excerpt()) : ?>
                            <p class="newsExcerpt"><?php the_excerpt(); ?></p>
                        <?php endif; ?>
                    </article>
                </li>
                <?php endwhile; ?>
            </ul>
            
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
                $posts_per_page = get_option('posts_per_page');
                $total = $wp_query->found_posts;
                $start = ($paged - 1) * $posts_per_page + 1;
                $end = min($paged * $posts_per_page, $total);
                ?>
                <p class="paginationInfo"><?php echo $start; ?>〜<?php echo $end; ?>／全<?php echo $total; ?>記事中</p>
            </div>
        <?php else : ?>
            <p>お知らせはありません。</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
