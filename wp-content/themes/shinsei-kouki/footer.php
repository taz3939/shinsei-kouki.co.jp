<?php
/**
 * footer.php
 * 神西衡機工業株式会社 テーマのフッター部分
 */
?>

    </main>

    <?php if (!is_front_page() && !is_page('contact')) : ?>
        <?php get_template_part('template-parts/contact-section'); ?>
    <?php endif; ?>
    
    <?php if (!is_front_page()) : ?>
        <div class="breadcrumbsWrapper">
            <div class="inner">
                <?php shinsei_kouki_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <footer id="siteFooter">
        <div class="inner">
            <div id="footerContent">
                <nav aria-label="footerNav">
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/business')); ?>">事業内容</a></li>
                        <li><a href="<?php echo esc_url(home_url('/company')); ?>">会社概要</a></li>
                        <li><a href="<?php echo esc_url(home_url('/topics')); ?>">お知らせ</a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>">お問い合わせ</a></li>
                    </ul>
                </nav>
                <p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="トップページへ">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/logo-white.svg" alt="神西衡機工業株式会社" width="57" height="57" class="footerLogoImg" decoding="async">
                        <em>神西衡機工業株式会社</em>
                    </a>
                </p>
                <dl>
                    <dt>本社</dt>
                    <dd>
                        〒<?php echo get_option('company_postal', '250-0863'); ?> <?php echo get_option('company_address', '神奈川県小田原市飯泉90-9'); ?><br>
                        TEL: <?php echo get_option('company_tel_hq', '0465-55-8644'); ?>　FAX: <?php echo get_option('company_fax_hq', '0465-55-8522'); ?>
                    </dd>
                </dl>
                <dl>
                    <dt>成田営業所</dt>
                    <dd>
                        〒<?php echo get_option('company_postal_odawara', '250-0862'); ?> <?php echo get_option('company_address_odawara', '神奈川県小田原市成田1048'); ?><br>
                        TEL: <?php echo get_option('company_tel', '0465-38-1194'); ?>　FAX: <?php echo get_option('company_fax_odawara', '0465-36-1294'); ?>
                    </dd>
                </dl>
            </div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d26053.863684226766!2d139.14197983049357!3d35.28776507271683!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6019a59f3c0eadd5%3A0xdae67f51002c3d08!2z56We6KW_6KGh5qmf5bel5qWt!5e0!3m2!1sja!2sjp!4v1769223541389!5m2!1sja!2sjp" width="470" height="325" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <p class="copyright">&copy; <?php echo date('Y'); ?> 神西衡機工業</p>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
