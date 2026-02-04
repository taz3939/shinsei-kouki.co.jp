<?php
/**
 * template-parts/parts_pickup-sidebar.php
 * ピックアップ記事サイドバー
 */

// ピックアップ記事を取得
$pickup_posts = get_posts(array(
    'post_type' => 'topics',
    'posts_per_page' => 3,
    'meta_key' => '_is_pickup',
    'meta_value' => '1',
    'orderby' => 'date',
    'order' => 'DESC'
));

if (!empty($pickup_posts)) :
?>
<aside class="pickupTopics">
    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/logo-ci.svg" alt="" width="26" height="24" class="sidebarLogo" decoding="async" aria-hidden="true">
    <h2>pickup topics</h2>
    <ul class="pickupList">
        <?php foreach ($pickup_posts as $post) : setup_postdata($post); ?>
        <li>
            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                <?php shinsei_kouki_topics_thumbnail($post->ID, 'medium', 'pickupThumbnail', array('width' => '100', 'height' => '75')); ?>
                <div class="pickupContent">
                    <time datetime="<?php echo get_the_date('Y-m-d', $post->ID); ?>"><?php echo get_the_date('Y.m.d', $post->ID); ?></time>
                    <span><?php echo esc_html(get_the_title($post->ID)); ?></span>
                </div>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</aside>
<?php 
    wp_reset_postdata();
endif;
?>
