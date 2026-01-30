<?php
/**
 * company-options.php
 * テーマカスタマイザーで会社情報を管理
 * 表示：外観 → カスタマイズ → 会社情報
 */

add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('shinsei_kouki_company', array(
        'title'    => '会社情報',
        'priority' => 30,
    ));

    // 代表者
    $wp_customize->add_setting('company_representative', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_representative', array(
        'label'   => '代表者',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    // メールアドレス（共通）
    $wp_customize->add_setting('company_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('company_email', array(
        'label'   => 'メールアドレス',
        'section' => 'shinsei_kouki_company',
        'type'    => 'email',
    ));

    // --- 本社 ---
    $wp_customize->add_setting('company_postal', array(
        'default'           => '250-0863',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_postal', array(
        'label'   => '本社 郵便番号',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('company_address', array(
        'default'           => '神奈川県小田原市飯泉90-9',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_address', array(
        'label'   => '本社 住所',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('company_tel_hq', array(
        'default'           => '0465-55-8644',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_tel_hq', array(
        'label'   => '本社 電話番号',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('company_fax_hq', array(
        'default'           => '0465-55-8522',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_fax_hq', array(
        'label'   => '本社 FAX',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    // --- 成田営業所（お問い合わせ先） ---
    $wp_customize->add_setting('company_postal_odawara', array(
        'default'           => '250-0862',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_postal_odawara', array(
        'label'   => '成田営業所 郵便番号',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('company_address_odawara', array(
        'default'           => '神奈川県小田原市成田1048',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_address_odawara', array(
        'label'   => '成田営業所 住所',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('company_tel', array(
        'default'           => '0465-38-1194',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_tel', array(
        'label'   => '成田営業所 電話番号（お問い合わせ先）',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('company_fax_odawara', array(
        'default'           => '0465-36-1294',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('company_fax_odawara', array(
        'label'   => '成田営業所 FAX',
        'section' => 'shinsei_kouki_company',
        'type'    => 'text',
    ));
});
