<?php
/**
 * footer.php
 * 神西衡機工業株式会社 テーマのフッター部分
 */
?>

    </main>

    <footer id="siteFooter">
        <div class="inner">
            <div class="footerContent">
                <div class="footerLogo">
                    <p>神西衡機工業株式会社</p>
                </div>
                <nav aria-label="footerNav">
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/business')); ?>">事業内容</a></li>
                        <li><a href="<?php echo esc_url(home_url('/company')); ?>">会社概要</a></li>
                        <li><a href="<?php echo esc_url(home_url('/news')); ?>">お知らせ</a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>">お問い合わせ</a></li>
                    </ul>
                </nav>
            </div>
            
            <div class="footerInfo">
                <div class="footerOffice">
                    <h3>本社</h3>
                    <p>
                        〒<?php echo get_option('company_postal', '250-0003'); ?> <?php echo get_option('company_address', '神奈川県小田原市'); ?><br>
                        TEL: <?php echo get_option('company_tel_hq', '0465-55-8644'); ?><br>
                        FAX: <?php echo get_option('company_fax_hq', '0465-55-8522'); ?>
                    </p>
                </div>
                <div class="footerOffice">
                    <h3>小田原営業所</h3>
                    <p>
                        〒<?php echo get_option('company_postal_odawara', '250-0002'); ?> <?php echo get_option('company_address_odawara', '神奈川県小田原市成田1049'); ?><br>
                        TEL: <?php echo get_option('company_tel', '0465-38-1194'); ?><br>
                        FAX: <?php echo get_option('company_fax_odawara', '0465-36-1294'); ?>
                    </p>
                </div>
            </div>
            
            <?php
            // Google Mapsの埋め込み（オプション）
            $map_embed_code = get_option('company_map_embed', '');
            if (!empty($map_embed_code)) :
            ?>
            <div class="footerMap">
                <?php echo $map_embed_code; ?>
            </div>
            <?php endif; ?>
            
            <p class="copyright">&copy; <?php echo date('Y'); ?> 神西衡機工業</p>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
