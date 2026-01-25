<?php
/**
 * single-topics.php
 * お知らせ詳細ページテンプレート
 */
get_header();
?>

<section id="newsDetail">
    <div class="inner">
        <p class="pageTitle">
            お知らせ
            <small>topics</small>
        </p>
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('newsArticle'); ?>>
                <header class="newsHeader">
                    <time datetime="<?php echo get_the_date('Y-m-d'); ?>" class="newsDate"><?php echo get_the_date('Y年m月d日'); ?></time>
                    <h1 class="newsTitle"><?php the_title(); ?></h1>
                </header>
                
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
</section>

<?php get_footer(); ?>
