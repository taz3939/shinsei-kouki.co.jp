<?php
/**
 * archive-topics.php
 * お知らせ一覧ページテンプレート
 */
get_header();
?>

<section id="newsIntro">
    <div class="inner">
        <h1 class="pageTitle">
            お知らせ
            <small>topics</small>
        </h1>
        <p>
            神西衡機工業からのお知らせ、および日々の活動報告を掲載しております。<br>
            新製品のご案内やメンテナンスに関する情報、夏季・冬季休暇などの最新情報を随時更新してまいります。<br>
            お客様の業務にお役立ていただける情報を発信してまいりますので、ぜひご覧ください。
        </p>
    </div>
</section>
<section id="newsArchive">
    <div class="inner">
        <?php if (have_posts()) : ?>
            <ul class="newsList">
                <?php while (have_posts()) : the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                        <figure class="newsThumbnail">
                            <?php echo get_the_post_thumbnail(get_the_ID(), 'medium', array('width' => '110', 'height' => '83', 'decoding' => 'async', 'loading' => 'lazy')); ?>
                        </figure>
                        <?php endif; ?>
                        <div class="newsContent">
                            <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
                            <span><?php the_title(); ?></span>
                        </div>
                    </a>
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
