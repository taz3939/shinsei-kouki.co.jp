<?php
/**
 * BreadcrumbList 構造化データ
 * パンくず表示があるページで、同じ階層を JSON-LD で出力。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 現在のページのパンくず項目を配列で返す。
 * 各要素は [ 'name' => string, 'url' => string ]。最後の項目は現在ページで url は空でも可。
 *
 * @return array
 */
function shinsei_kouki_get_breadcrumb_items() {
    $items = array();
    $items[] = array(
        'name' => 'トップページ',
        'url'  => home_url('/'),
    );

    if (is_category()) {
        $category = get_queried_object();
        $items[]  = array(
            'name' => $category->name,
            'url'  => '',
        );
    } elseif (is_single()) {
        $post_type = get_post_type();
        if ($post_type === 'topics') {
            $items[] = array(
                'name' => 'お知らせ',
                'url'  => home_url('/topics/'),
            );
        } elseif ($post_type === 'post') {
            $categories = get_the_category();
            if (!empty($categories)) {
                $items[] = array(
                    'name' => $categories[0]->name,
                    'url'  => get_category_link($categories[0]->term_id),
                );
            }
        }
        $items[] = array(
            'name' => get_the_title(),
            'url'  => '',
        );
    } elseif (is_page()) {
        $ancestors = get_post_ancestors(get_the_ID());
        if ($ancestors) {
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor) {
                $items[] = array(
                    'name' => get_the_title($ancestor),
                    'url'  => get_permalink($ancestor),
                );
            }
        }
        $items[] = array(
            'name' => get_the_title(),
            'url'  => '',
        );
    } elseif (is_tax('topics_category')) {
        $term = get_queried_object();
        $items[] = array(
            'name' => 'お知らせ',
            'url'  => home_url('/topics/'),
        );
        $items[] = array(
            'name' => $term && !is_wp_error($term) ? $term->name : 'お知らせカテゴリ',
            'url'  => '',
        );
    } elseif (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        if ($post_type) {
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj) {
                $items[] = array(
                    'name' => $post_type_obj->labels->name,
                    'url'  => '',
                );
            }
        }
    } elseif (is_archive()) {
        $name = '';
        if (is_tax() || is_category() || is_tag()) {
            $obj = get_queried_object();
            $name = $obj && isset($obj->name) ? $obj->name : wp_strip_all_tags(get_the_archive_title());
        } else {
            $name = wp_strip_all_tags(get_the_archive_title());
        }
        $items[] = array(
            'name' => $name !== '' ? $name : 'アーカイブ',
            'url'  => '',
        );
    } elseif (is_search()) {
        $items[] = array(
            'name' => '検索結果',
            'url'  => '',
        );
    } elseif (is_404()) {
        $items[] = array(
            'name' => 'ページが見つかりません',
            'url'  => '',
        );
    }

    return $items;
}

/**
 * BreadcrumbList の JSON-LD 用配列を返す。
 * トップページでは空配列を返す（呼び出し側でスキップする）。
 *
 * @return array
 */
function shinsei_kouki_get_breadcrumb_schema() {
    if (is_front_page()) {
        return array();
    }
    $crumbs = shinsei_kouki_get_breadcrumb_items();
    if (count($crumbs) <= 1) {
        return array();
    }

    $current_url = '';
    if (is_singular()) {
        $current_url = get_permalink();
    } elseif (is_post_type_archive() || is_archive() || is_search()) {
        $current_url = get_post_type_archive_link(get_query_var('post_type')) ?: home_url($_SERVER['REQUEST_URI']);
    }
    if (empty($current_url) && !empty($_SERVER['REQUEST_URI'])) {
        $current_url = home_url($_SERVER['REQUEST_URI']);
    }

    $list = array();
    foreach ($crumbs as $i => $c) {
        $item = array(
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $c['name'],
        );
        $link = $c['url'];
        if (empty($link) && $i === count($crumbs) - 1 && $current_url !== '') {
            $link = $current_url;
        }
        if (!empty($link)) {
            $item['item'] = array(
                '@id' => $link,
            );
        }
        $list[] = $item;
    }

    return array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $list,
    );
}
