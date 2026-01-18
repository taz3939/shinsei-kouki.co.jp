<?php
/**
 * index.php
 * メインテンプレートファイル（フォールバック）
 */
get_header();
?>

<div class="contentArea">
    <div class="inner">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="postContent">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>コンテンツが見つかりませんでした。</p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
