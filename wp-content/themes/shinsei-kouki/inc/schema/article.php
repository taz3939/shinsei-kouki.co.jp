<?php
/**
 * Article 構造化データ
 * お知らせ詳細（single-topics）用。headline, datePublished, publisher, image を出力。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 現在の投稿（お知らせ）の Article 用 JSON-LD 配列を返す。
 * single-topics で呼び出す想定。
 *
 * @return array|null 投稿が無い場合は null
 */
function shinsei_kouki_get_article_schema() {
    if (!is_singular('topics')) {
        return null;
    }
    $post = get_post();
    if (!$post) {
        return null;
    }

    $org_id = home_url('/') . '#organization';
    $url    = get_permalink();
    $title = get_the_title();
    $desc  = defined('SHINSEI_KOUKI_TOPICS_DESCRIPTION_PREFIX') ? (SHINSEI_KOUKI_TOPICS_DESCRIPTION_PREFIX . $title) : $title;

    $published = get_the_date('c');
    $modified  = get_the_modified_date('c');

    $image_url = null;
    if (has_post_thumbnail()) {
        $img_id = get_post_thumbnail_id();
        $full   = wp_get_attachment_image_src($img_id, 'full');
        if (!empty($full[0])) {
            $image_url = $full[0];
        }
    }
    if (!$image_url) {
        $image_url = get_template_directory_uri() . '/assets/img/topics/img_no-image.webp';
    }

    $schema = array(
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        '@id'              => $url . '#article',
        'headline'         => $title,
        'description'      => $desc,
        'url'              => $url,
        'datePublished'    => $published,
        'dateModified'     => $modified,
        'publisher'        => array(
            '@id' => $org_id,
        ),
        'author'           => array(
            '@id' => $org_id,
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id'   => $url,
        ),
    );

    $schema['image'] = array(
        '@type'  => 'ImageObject',
        'url'    => $image_url,
    );

    return $schema;
}
