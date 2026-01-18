<?php
/**
 * page-contact.php
 * お問い合わせページテンプレート
 */
get_header();
?>

<section id="contactPage">
    <div class="inner">
        <h1>お問い合わせ</h1>
        
        <div class="contactContent">
            <p class="contactIntro">
                お問い合わせは、以下のフォームよりお願いいたします。<br>
                お電話でのお問い合わせも承っております。
            </p>
            
            <div class="contactInfo">
                <dl>
                    <dt>電話番号</dt>
                    <dd><?php echo get_option('company_tel', '電話番号'); ?></dd>
                    <dt>受付時間</dt>
                    <dd>平日 9:00〜18:00</dd>
                    <dt>メールアドレス</dt>
                    <dd><?php echo get_option('company_email', 'メールアドレス'); ?></dd>
                </dl>
            </div>
            
            <?php
            // MW WP Formプラグインが有効な場合
            if (class_exists('MW_WP_Form_Admin')) {
                // フォームIDを取得（固定ページのカスタムフィールドから取得、または直接指定）
                $form_id = get_post_meta(get_the_ID(), 'mw_wp_form_id', true);
                
                // フォームIDが設定されていない場合は、最初のフォームを使用
                if (empty($form_id)) {
                    $mw_wp_form_admin = new MW_WP_Form_Admin();
                    $forms = $mw_wp_form_admin->get_forms();
                    if (!empty($forms)) {
                        $form_id = $forms[0]->ID;
                    }
                }
                
                if (!empty($form_id)) {
                    echo do_shortcode('[mwform_formkey key="' . esc_attr($form_id) . '"]');
                } else {
                    echo '<p>お問い合わせフォームが設定されていません。管理画面でMW WP Formのフォームを作成してください。</p>';
                }
            } else {
                // プラグインがない場合のメッセージ
                echo '<p>MW WP Formプラグインをインストールして有効化してください。</p>';
            }
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
