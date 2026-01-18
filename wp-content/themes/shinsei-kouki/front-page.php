<?php
/**
 * front-page.php
 * トップページテンプレート
 */
get_header();
?>

<section id="mainVisual">
    <div class="inner">
        <div class="mainVisualLeft">
            <div class="mainVisualLogo">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/logo_main-visual.webp" alt="神西衡機工業株式会社">
            </div>
            <h1>神西衡機工業株式会社</h1>
            <p class="mainVisualTxt">計量器（はかり）・分銅のすべてを、確かな技術で。<br>販売から修理、高度な検査まで。</p>
        </div>
        <div class="mainVisualRight">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/img_weights.webp" alt="分銅">
        </div>
    </div>
</section>

<section id="aboutBusiness">
    <div class="inner">
        <h2>事業概要</h2>
        <div class="aboutBusinessContent">
            <p>
                当社では、電子天秤、産業用はかり、トラックスケールを含むまた様々な計量器（はかり）、<br>
                また、あらゆるサイズの分銅の販売に対応しております。<br>
                故障時の迅速な修理から、計量士による代検査（定期検査に代わる検査）、分銅校正など<br>
                お客様の「正確な計測」に必要なすべてをワンストップでサポート。<br>
                納入前の受入検査や休日作業にも柔軟に対応し、安心をお届けします。
            </p>
            <div class="businessList">
                <div class="businessItem">
                    <div class="businessIcon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/icon_business_01.webp" alt="はかり・分銅等の販売">
                    </div>
                    <h3>はかり・分銅等の販売</h3>
                    <p>電子天びん・分銅、それぞれ幅広い機種を納入前検査付きでご提供。最適なはかり・分銅をご提案します。</p>
                </div>
                <div class="businessItem">
                    <div class="businessIcon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/icon_business_02.webp" alt="はかりの修理">
                    </div>
                    <h3>はかりの修理</h3>
                    <p>故障や不具合発生時、迅速な対応で業務再開をサポート。あらゆる機種の修理にご対応します。</p>
                </div>
                <div class="businessItem">
                    <div class="businessIcon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/icon_business_03.webp" alt="はかり・分銅の検査">
                    </div>
                    <h3>はかり・分銅の検査</h3>
                    <p>社内管理用のはかりの検査・分銅校正を実施。休日作業にも柔軟に対応し、品質管理を徹底します。</p>
                </div>
                <div class="businessItem">
                    <div class="businessIcon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/top/icon_business_04.webp" alt="特定計量器（はかり）の代検査">
                    </div>
                    <h3>特定計量器（はかり）の代検査</h3>
                    <p>定期検査に代わる、計量士による法定検査（代検査）を実施。トラックスケールから電子天びんまで対応可能です。</p>
                </div>
            </div>
            <div class="btnArea">
                <a href="<?php echo esc_url(home_url('/business')); ?>" class="btnPrimary">事業内容について詳しくはこちら</a>
            </div>
        </div>
    </div>
</section>

<section id="latestNewsCalendar">
    <div class="inner">
        <?php
        // お知らせの最新3件を表示
        $news_query = new WP_Query(array(
            'post_type' => 'news',
            'posts_per_page' => 3,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        if ($news_query->have_posts()) :
        ?>
        <div id="latestNews">
            <h2>神西衡機工業株式会社からのお知らせ</h2>
            <ul class="newsList">
                <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                <li>
                    <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </li>
                <?php endwhile; ?>
            </ul>
            <div class="btnArea">
                <a href="<?php echo esc_url(home_url('/news')); ?>" class="btnSecondary">お知らせ一覧</a>
            </div>
        </div>
        <?php
        endif;
        wp_reset_postdata();
        ?>
        
        <div id="businessCalendar">
            <h2>営業カレンダー</h2>
            <?php
            // 現在の月を取得（URLパラメータがあればそれを使用）
            $cal_year = isset($_GET['cal_year']) ? intval($_GET['cal_year']) : date('Y');
            $cal_month = isset($_GET['cal_month']) ? intval($_GET['cal_month']) : date('m');
            
            // 現在の月のカレンダーを表示
            if (function_exists('shinsei_kouki_display_calendar')) {
                shinsei_kouki_display_calendar($cal_year, $cal_month);
            }
            ?>
            <div class="calendarInfo">
                <p>休業日: 土 (第2・3・4)・日・祝・他</p>
                <p>営業時間: 8:00~17:00</p>
                <p>休日・時間外作業もお気軽にご相談ください</p>
            </div>
        </div>
    </div>
</section>

<section id="contactSection">
    <div class="inner">
        <h2>お問い合わせ</h2>
        <p>お問い合わせは、以下のフォームよりお願いいたします。<br>お電話でのお問い合わせも承っております。</p>
        <div class="btnArea">
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btnContact">お問い合わせはこちら</a>
        </div>
        <div class="contactPhone">
            <?php echo get_option('company_tel', '0465-38-1194'); ?>
        </div>
    </div>
</section>

<section id="handledBrands">
    <div class="inner">
        <h2>取り扱いメーカー</h2>
        <p>当社では、お客様の様々なニーズにお応えするため、<br>トラックスケールから高精度電子天びんまで、主要メーカーの製品を取り扱っております。</p>
        <div class="brandLogos">
            <?php
            // 取り扱いメーカーのロゴ画像を表示
            // カスタムフィールドやオプションから取得するか、固定で表示
            $brands = array(
                'ishikura' => '株式会社石蔵商店',
                'ishida' => 'ISHIDA',
                'and' => 'AND',
                'omi' => '近江度量衡株式会社',
                'kubota' => 'KUBOTA',
                'antec' => 'アンテック',
                'moriya' => '守屋商店',
                'shinko' => '新光電子株式会社',
                'tanaka' => '田中衡機工業',
                'unipulse' => 'UNIPULSE'
            );
            
            foreach ($brands as $slug => $name) {
                $logo_path = get_template_directory_uri() . '/assets/img/top/logo_brand_' . $slug . '.webp';
                echo '<div class="brandLogo">';
                echo '<img src="' . esc_url($logo_path) . '" alt="' . esc_attr($name) . '">';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
