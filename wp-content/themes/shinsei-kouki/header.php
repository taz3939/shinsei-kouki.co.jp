<?php
/**
 * header.php
 * 神西衡機工業株式会社 テーマのヘッダー部分
 */
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header id="siteHeader">
        <div class="inner">
            <span>Shinsei Koki Kogyo Corporation</span>
            <nav aria-label="headerNav">
                <h2 class="onlySR">ヘッダーナビゲーション</h2>
                <button class="onlySP menuToggle" aria-label="メニューを開く">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="headerNav">
                    <li><a href="<?php echo esc_url(home_url('/business')); ?>">事業内容<small>business</small></a></li>
                    <li><a href="<?php echo esc_url(home_url('/company')); ?>">会社概要<small>company</small></a></li>
                    <li><a href="<?php echo esc_url(home_url('/news')); ?>">お知らせ<small>news</small></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="btnContact">お問い合わせ<small>contact</small></a></li>
                </ul>
                <dl>
                    <dt>
                        <span>お電話でのお問合せはこちら</span>
                        <?php echo get_option('company_tel', '0465-38-1194'); ?>
                    </dt>
                    <dd>
                        <p>営業時間: 8:00~17:00</p>
                        <p>定休日: 土 (第2・3・4)・日・祝・他</p>
                    </dd>
                </dl>
            </nav>
        </div>
    </header>

    <main id="mainContent">
