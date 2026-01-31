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
	<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/common/favicon.ico" sizes="32x32" type="image/x-icon">
	<?php wp_head(); ?>
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9TLHRWHMHP"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-9TLHRWHMHP');
</script>

<body <?php body_class(); ?>>
    <header id="siteHeader">
        <div class="inner">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="headerLogo" aria-label="トップページへ">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/logo-color.svg" alt="神西衡機工業株式会社" width="44" height="44" class="headerLogoImg" decoding="async">
                <em>神西衡機工業株式会社</em>
                <span>Shinsei Kouki Kogyo Corporation</span>
            </a>
            <nav aria-label="headerNav">
                <h2 class="onlySR">ヘッダーナビゲーション</h2>
                <button class="onlySP menuToggle" aria-label="メニューを開く">
                    <i></i>
                    <i></i>
                    <i></i>
                </button>
                <ul class="headerNav">
                    <li class="onlySP"><a href="<?php echo esc_url(home_url('/')); ?>">トップページ<small>top</small></a></li>
                    <li><a href="<?php echo esc_url(home_url('/business')); ?>">事業内容<small>business</small></a></li>
                    <li><a href="<?php echo esc_url(home_url('/company')); ?>">会社概要<small>about us</small></a></li>
                    <li><a href="<?php echo esc_url(home_url('/topics')); ?>">お知らせ<small>topics</small></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="btnContact">
                        <picture>
                            <source media="(max-width: 768px)" srcset="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_mail-green.svg">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_mail.svg" alt="" width="41" height="28" class="iconMail" decoding="async" aria-hidden="true">
                        </picture>
                        <span>お問い合わせ<small>contact</small></span>
                    </a></li>
                    <li class="onlySP">
                        <a href="tel:0465381194">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_tel-white.svg" alt="" width="23" height="23" class="iconPhone iconPhoneWhite" decoding="async" aria-hidden="true">
                            <span>お電話でのお問い合わせはこちら</span>
                        </a>
                        <p>営業時間：8:00〜17:00</p>
                        <p>定休日：土 (第2･3･4)・日・祝・他</p>
                    </li>
                </ul>
                <dl>
                    <dt>
                        <span>お電話でのお問い合わせはこちら</span>
                        <em>
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_tel.svg" alt="" width="23" height="23" class="iconPhone" decoding="async" aria-hidden="true">
                            0465-38-1194
                        </em>
                    </dt>
                    <dd>
                        <p>営業時間：8:00〜17:00</p>
                        <p>定休日：土 (第2･3･4)・日・祝・他</p>
                    </dd>
                </dl>
            </nav>
        </div>
    </header>

    <main id="mainContent">
