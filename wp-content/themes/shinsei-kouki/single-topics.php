<?php
/**
 * single-topics.php
 * お知らせ詳細ページテンプレート
 */
get_header();
?>

<section id="newsDetailIntro">
    <div class="inner">
        <p class="pageTitle">
            お知らせ
            <small>topics</small>
        </p>
        <time datetime="<?php echo get_the_date('Y-m-d'); ?>" class="newsDate"><?php echo get_the_date('Y.m.d'); ?></time>
        <h1 class="newsTitle"><?php the_title(); ?></h1>
    </div>
</section>
<section id="newsDetailContent">
    <div class="inner">
        <div class="newsDetailContainer">
            <div class="newsMain">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('newsArticle'); ?>>
                        <?php shinsei_kouki_topics_thumbnail(get_the_ID(), 'large', 'newsDetailEyecatch'); ?>
                        <div class="newsContent">
                            <?php the_content(); ?>
                        </div>
                    </article>
                    <?php endwhile; ?>
                <?php endif; ?>
                
                <div class="newsNav">
                    <div class="btnArea">
                        <a href="<?php echo esc_url(home_url('/topics')); ?>" class="btnSecondary">お知らせ一覧に戻る</a>
                    </div>
                </div>
            </div>
            
            <div class="newsSidebar">
                <?php get_template_part('template-parts/pickup-sidebar'); ?>
                <?php get_template_part('template-parts/monthly-archive-sidebar'); ?>
                <?php get_template_part('template-parts/search-sidebar'); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
