<?php
/**
 * 404.php
 * ページが見つからない場合のテンプレート
 */
get_header();
?>

<section class="error-404 not-found">
    <div class="inner">
        <h1 class="pageTitle">
            ページが見つかりません
            <small>404 Not Found</small>
        </h1>
        <div class="errorContent">
            <p>お探しのページは存在しないか、移動した可能性があります。<br>URLをご確認いただくか、下記リンクからお探しください。</p>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/')); ?>">トップページ</a></li>
                <li><a href="<?php echo esc_url(home_url('/business')); ?>">事業内容</a></li>
                <li><a href="<?php echo esc_url(home_url('/company')); ?>">会社概要</a></li>
                <li><a href="<?php echo esc_url(home_url('/topics')); ?>">お知らせ</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact')); ?>">お問い合わせ</a></li>
            </ul>
        </div>
    </div>
</section>

<?php get_footer(); ?>
