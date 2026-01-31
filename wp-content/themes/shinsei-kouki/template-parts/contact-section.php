<?php
/**
 * template-parts/contact-section.php
 * お問い合わせセクション（共通パーツ）
 */
?>
<section id="contactSection">
    <div class="inner">
        <h2>
            お問い合わせ
            <small>contact</small>
        </h2>
        <p>
            当社では、はかりや分銅の販売から、修理、各種検査まで、幅広いサービスに対応しております。<br>
            また、お客様のご都合に合わせて、休日（土日・祝日）の作業対応も可能です。<br>
            お気軽にご相談くださいませ。
        </p>
        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btnContact">
            <span>計量器・分銅のお困りごとやご不明な点は<br class="onlySP">神西衡機にお任せください</span>
            <em>
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_mail.svg" alt="" width="41" height="28" class="iconMail" decoding="async" aria-hidden="true">
                お問い合わせフォームはこちら
            </em>
        </a>
        <dl>
            <dt class="onlyPC">
                <span>お電話での<br>お問い合わせ</span>
                <em>
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_tel.svg" alt="" width="23" height="23" class="iconPhone" decoding="async" aria-hidden="true">
                    0465-38-1194
                </em>
            </dt>
            <dt class="onlySP">
                <a href="tel:0465381194">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/ico_tel-white.svg" alt="" width="23" height="23" class="iconPhone iconPhoneWhite" decoding="async" aria-hidden="true">
                    <span>お電話でのお問い合わせはこちら</span>
                </a>
            </dt>
            <dd>
                <p>営業時間：8:00〜17:00</p>
                <p>定休日：土 (第2･3･4)・日・祝・他</p>
            </dd>
        </dl>
    </div>
</section>
