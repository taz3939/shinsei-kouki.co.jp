<?php
/**
 * page-company.php
 * 会社概要ページテンプレート
 */
get_header();
?>

<section id="companyIntro">
    <div class="inner">
        <h1 class="pageTitle">
            会社概要
            <small>about us</small>
        </h1>
        <p>
            神西衡機工業株式会社は、神奈川県西部に位置する小田原を拠点に、はかり（衡機）の専門家として、産業の根幹である「計量の正確さ」を守り続けています。<br>
            <br>
            私たちの生活や産業活動において、「正しく量る」ことは品質管理や公平な取引の第一歩です。当社は、電子天びんから大型トラックスケールまで、多種多様な計量器の販売、修理、メンテナンスを通じて、お客様の業務効率化と安心の提供に尽力してまいりました。<br>
            <br>
            単に計量器を販売するだけでなく、お客様の抱える計量上の課題に寄り添い、最適なソリューションをご提案すること。それが私たちの使命です。これからも「人と社会に貢献できる企業」を目指し、誠実な技術力で未来の「正確な計量」を支えてまいります。
        </p>
        <article>
            <h2>ロゴについて</h2>
            <figure>
                <picture>
                    <source media="(max-width: 768px)" srcset="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/company/img_about-logo_sp.webp">
                    <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/company/img_about-logo_pc.webp" alt="神西衡機工業株式会社のロゴについて" width="539" height="540" loading="lazy">
                </picture>
            </figure>
        </article>
    </div>
</section>
        
<section id="companyContent">
    <div class="inner">
        <dl>
            <dt>社名</dt>
            <dd>神西衡機工業株式会社</dd>
            <dt>代表</dt>
            <dd>髙橋 佳吾</dd>
            <dt>所在地</dt>
            <dd>
                <ul>
                    <li>
                        <strong>本社</strong>
                        〒250-0863<br>
                        神奈川県小田原市飯泉90-9
                    </li>
                    <li>
                        <strong>成田事業所</strong>
                        〒250-0862<br>
                        神奈川県小田原市成田1048
                    </li>
                </ul>
            </dd>
            <dt>TEL & FAX</dt>
            <dd>
                <ul>
                    <li>
                        <strong>本社</strong>
                        TEL：0465-55-8644<br>
                        FAX：0465-55-8522
                    </li>
                    <li>
                        <strong>成田事業所</strong>
                        TEL：0465-38-1194<br>
                        FAX：0465-38-1294
                    </li>
                </ul>
            </dd>
            <dt>事業内容</dt>
            <dd>計量器、はかりの販売・修理・検査</dd>
            <dt>許可</dt>
            <dd>機械器具設置工事業、<br class="sp">特定計量器販売事業届出、<br class="sp">特定計量器製造事業届出</dd>
        </dl>
    </div>
</section>

<?php get_footer(); ?>
