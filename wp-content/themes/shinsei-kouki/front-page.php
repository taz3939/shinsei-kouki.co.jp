<?php
/**
 * front-page.php
 * トップページテンプレート
 */
get_header();
?>

<section id="mainVisual">
    <div class="inner">
        <h1>
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/common/logo-color.svg" alt="神西衡機工業株式会社" width="120" height="120" class="mainVisualLogo" decoding="async" loading="eager" fetchpriority="high">
            神西衡機工業株式会社
        </h1>
        <p>計量器（はかり）・分銅のすべてを、確かな技術で。<br>販売から修理、高度な検査まで。</p>
        <figure class="mainVisualImage">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/img_products.webp" alt="分銅" width="205" height="332" decoding="async" loading="eager" fetchpriority="high">
        </figure>
    </div>
</section>

<section id="topIntro">
    <div class="inner">
        <h2>
            計量器<span>（はかり）</span>・分銅について
            <small>about</small>
        </h2>
        <p>
            私たち神西衡機工業が扱う計量器<small>（はかり）</small>や分銅には、単なる道具以上の「信頼の歴史」が息づいています。<br>
            <br>
            江戸時代、分銅は公平な取引の象徴であり、幕府公定の「後藤分銅」として、<br class="onlyPC">200年以上にわたり日本の質量の単位を守り続けました。<br>
            その均質性への厳格なこだわりは、現代の「不正のない公正な取引」の礎です。<br>
            <br>
            神西衡機工業は、この「信頼を測る文化」を継承し、最新の技術と専門的な検査をもって、<br class="onlyPC">お客様のビジネスと社会の確かな基盤を支えてまいります。
        </p>
    </div>
</section>

<section id="aboutBusiness">
    <div class="inner">
        <h2>
            事業概要
            <small>business</small>
        </h2>
        <p>
            当社では、電子天びん、産業用はかり、トラックスケールを含む様々な計量器、<br class="onlyPC">また、あらゆるサイズの分銅の販売に対応しております。<br>
            故障時の迅速な修理から、計量士による代検査<small>（定期検査に代わる検査）</small>、分銅校正など<br class="onlyPC">お客様の「正確な計測」に必要なすべてをワンストップでサポート。<br>
            納入前の受入検査や休日作業にも柔軟に対応し、安心をお届けします。
        </p>
        <ol>
            <li>
                <h3>はかり・分銅等の<br class="onlySP">販売</h3>
                <figure>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/ico_business01.webp" alt="はかり・分銅等の販売" width="80" height="34" decoding="async" loading="lazy">
                </figure>
                <p>電子天びん・分銅、それぞれ幅広い機種を納入前検査付きでご提供。最適なはかり・分銅をご提案します。</p>
            </li>
            <li>
                <h3>はかりの<br class="onlySP">修理</h3>
                <figure>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/ico_business02.webp" alt="はかりの修理" width="80" height="34" decoding="async" loading="lazy">
                </figure>
                <p>故障や不具合発生時、迅速な対応で業務再開をサポート。あらゆる機種の修理にご対応します。</p>
            </li>
            <li>
                <h3>はかり・分銅の<br class="onlySP">検査</h3>
                <figure>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/ico_business03.webp" alt="はかり・分銅の検査" width="80" height="34" decoding="async" loading="lazy">
                </figure>
                <p>社内管理用のはかりの検査・分銅校正を実施。休日作業にも柔軟に対応し、品質管理を徹底します。</p>
            </li>
            <li>
                <h3>特定計量器<small>（はかり）</small>の<br class="onlySP">代検査</h3>
                <figure>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/ico_business04.webp" alt="特定計量器（はかり）の代検査" width="80" height="34" decoding="async" loading="lazy">
                </figure>
                <p>定期検査に代わる、計量士による法定検査（代検査）を実施。トラックスケールから電子天びんまで対応可能です。</p>
            </li>
        </ol>
        <a href="<?php echo esc_url(home_url('/business')); ?>" class="btnPrimary">事業内容について詳しくはこちら</a>
    </div>
</section>

<?php get_template_part('template-parts/parts_contact-section'); ?>

<section id="handledBrands">
    <div class="inner">
        <h2>
            取り扱いメーカー
            <small>our brands</small>
        </h2>
        <p>
            トラックスケールから高精度な電子天びん、多様な産業用はかりまで、<br class="onlyPC">お客様のあらゆるニーズにお応えするため、主要メーカーの製品を幅広く取り扱っております。<br>
            <br>
            特性と最新技術を深く理解した専門スタッフが、お客様の業態や計量環境に最適な一台を選定し、<br class="onlyPC">販売から導入後のサポートまで一貫して対応いたします。どうぞ安心してお任せください。
        </p>
        <ul>
            <?php
            // 取り扱いメーカーのロゴ画像を表示（name: alt・表示用、url: リンク先・空ならリンクなし）
            $brands = array(
                '01' => array( 'name' => '株式会社石蔵商店', 'url' => 'https://www.ishikura-s.co.jp/' ),
                '02' => array( 'name' => '株式会社イシダ', 'url' => 'https://www.ishida.co.jp/ww/jp/' ),
                '03' => array( 'name' => '株式会社エー・アンド・デイ', 'url' => 'https://www.aandd.co.jp/' ),
                '04' => array( 'name' => '近江度量衡株式会社', 'url' => 'https://www.omiscale.co.jp/' ),
                '05' => array( 'name' => '株式会社協立商会', 'url' => 'https://www.krsdigi.co.jp/' ),
                '06' => array( 'name' => '株式会社櫛田度器製作所', 'url' => 'http://www.kushidadoki.com/' ),
                '07' => array( 'name' => '株式会社CAS', 'url' => 'https://cas-japan.com/' ),
                '08' => array( 'name' => '株式会社クボタ計装', 'url' => 'https://www.kubota-keiso.co.jp/' ),
                '09' => array( 'name' => 'JFEアドバンテック株式会社', 'url' => 'https://www.jfe-advantech.co.jp/' ),
                '10' => array( 'name' => '株式会社守隨本店', 'url' => 'https://www.shuzui.jp/' ),
                '11' => array( 'name' => '新光電子株式会社', 'url' => 'https://www.vibra.co.jp/' ),
                '12' => array( 'name' => '株式会社田中衡機工業所', 'url' => 'https://www.tanaka-scale.co.jp/' ),
                '13' => array( 'name' => 'ユニパルス株式会社', 'url' => 'https://www.unipulse.tokyo/' ),
            );
            foreach ($brands as $num => $brand) {
                $logo_path = get_template_directory_uri() . '/assets/img/top/img_logo-brand' . $num . '.webp';
                $name = $brand['name'];
                $url  = ! empty( $brand['url'] ) ? $brand['url'] : '';
                echo '<li>';
                if ( $url ) {
                    echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">';
                }
                echo '<img src="' . esc_url( $logo_path ) . '" alt="' . esc_attr( $name ) . '" width="120" height="40" decoding="async" loading="lazy">';
                if ( $url ) {
                    echo '</a>';
                }
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</section>

<section id="latestNewsCalendar">
    <div class="inner">
        <?php
        // お知らせの最新3件を表示
        $news_query = new WP_Query(array(
            'post_type' => 'topics',
            'posts_per_page' => 3,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        if ($news_query->have_posts()) :
        ?>
        <div id="latestNews">
            <h2>
                <span>神西衡機工業株式会社からの</span>お知らせ
                <small>topics</small>
            </h2>
            <ul class="newsList">
                <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>">
                        <?php shinsei_kouki_topics_thumbnail(null, 'medium', 'newsThumbnail'); ?>
                        <div class="newsContent">
                            <div class="newsContentMeta">
                                <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
                                <?php
                                $terms = get_the_terms(get_the_ID(), 'topics_category');
                                if ($terms && !is_wp_error($terms)) {
                                    foreach ($terms as $t) {
                                        echo '<span class="newsCategory">' . esc_html($t->name) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                            <span><?php the_title(); ?></span>
                        </div>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
            <a href="<?php echo esc_url(home_url('/topics')); ?>">お知らせ一覧</a>
        </div>
        <?php
        endif;
        wp_reset_postdata();
        ?>
        
        <div id="businessCalendar">
            <h2>
                営業カレンダー
                <small>calendar</small>
            </h2>
            <?php
            // 現在の月を取得（URLパラメータがあればそれを使用）。表示範囲（2026年1月〜登録がある月まで）に収める。
            $cal_year  = isset($_GET['cal_year']) ? intval($_GET['cal_year']) : date('Y');
            $cal_month = isset($_GET['cal_month']) ? intval($_GET['cal_month']) : date('m');
            if (function_exists('shinsei_kouki_get_calendar_display_range')) {
                $cal_range = shinsei_kouki_get_calendar_display_range();
                $cal_min   = $cal_range['min'];
                $cal_max   = $cal_range['max'];
                if ($cal_year < $cal_min['year'] || ($cal_year === $cal_min['year'] && $cal_month < $cal_min['month'])) {
                    $cal_year  = $cal_min['year'];
                    $cal_month = $cal_min['month'];
                } elseif ($cal_year > $cal_max['year'] || ($cal_year === $cal_max['year'] && $cal_month > $cal_max['month'])) {
                    $cal_year  = $cal_max['year'];
                    $cal_month = $cal_max['month'];
                }
            }
            if (function_exists('shinsei_kouki_display_calendar')) {
                shinsei_kouki_display_calendar($cal_year, $cal_month);
            }
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
