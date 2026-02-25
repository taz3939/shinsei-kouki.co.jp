<?php
/**
 * page-business.php
 * 事業内容ページテンプレート
 */
get_header();
?>

<section id="businessIntro">
    <div class="inner">
        <h1 class="pageTitle">
            事業内容
            <small>our business</small>
        </h1>        
        <p>
            神西衡機工業株式会社は、お客様の「正確な計量」を支える総合計量サービスを提供しております。<br>
            <br>
            当社では、電子天びん、産業用はかり、トラックスケールをはじめとする様々な計量器（はかり）と、あらゆるサイズの分銅の販売に対応しております。故障時の迅速な修理から、計量士による代検査（定期検査に代わる検査）、分銅校正まで、お客様の「正確な計量」に必要なすべてのサービスをワンストップで提供しております。<br>
            <br>
            納入前の受入検査や休日作業にも柔軟に対応し、お客様の業務をトータルにサポートすることで、安心をお届けすることをお約束いたします。
        </p>
        <figure class="onlySP"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_business-intro_sp.webp" alt="神西衡機工業株式会社 事業内容" width="688" height="686" loading="lazy"></figure>
    </div>
</section>
<section id="businessList">
    <div class="inner">
        <p>
            4つの事業それぞれについて、<br>
            サービス内容と納品・完了までの流れの一例を<br class="onlySP">ご紹介しております。
        </p>
        <div class="businessTabContainer">
            <ul class="businessTabs" role="tablist">
                <li role="presentation">
                    <button class="businessTab isActive" data-tab="sale" role="tab" aria-selected="true" aria-controls="sale-content">
                        はかり・分銅等の販売
                    </button>
                </li>
                <li role="presentation">
                    <button class="businessTab" data-tab="repair" role="tab" aria-selected="false" aria-controls="repair-content">
                        はかりの修理
                    </button>
                </li>
                <li role="presentation">
                    <button class="businessTab" data-tab="inspection" role="tab" aria-selected="false" aria-controls="inspection-content">
                        はかり・分銅の検査
                    </button>
                </li>
                <li role="presentation">
                    <button class="businessTab" data-tab="substitute" role="tab" aria-selected="false" aria-controls="substitute-content">
                        特定計量器（はかり）の代検査
                    </button>
                </li>
            </ul>

            <div class="businessTabContents">
                <!-- はかり・分銅等の販売 -->
                <div id="sale-content" class="businessTabContent isActive" role="tabpanel" aria-labelledby="sale-tab">
                    <h2 class="onlySR">はかり・分銅等の販売</h2>
                    <div class="businessContent">
                        <p>
                            はかり・分銅、それぞれ幅広い機種をご提案します。<br>
                            はかりは、納入前検査等を実施し納品いたします。
                        </p>
                        <ol class="businessFlow">
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow01.webp" alt="お問い合わせ・ご相談" width="630" height="430" loading="eager"></figure>
                                <h3>お問い合わせ・ご相談</h3>
                                <p>
                                    お電話、メール、またはお問い合わせフォームよりご連絡ください。<br>
                                    ご希望の用途、計量範囲、設置環境、ご予算などをお聞かせいただければ、お客様に最適な製品をご提案いたします。<br>
                                    不明点やご質問がございましたら、お気軽にお問い合わせください。<br>
                                    必要に応じて、現場調査も実施いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow02.webp" alt="最適機種のご提案・見積もり" width="630" height="430" loading="lazy"></figure>
                                <h3>最適機種のご提案・見積もり</h3>
                                <p>
                                    お客様のご要望に基づき、用途に合わせた最適な機種をご提案し、見積もりを作成いたします。<br>
                                    複数の機種をご提案する場合もございますので、お客様のご予算やご希望に合わせてお選びいただけます。<br>
                                    見積もり内容についてご不明点がございましたら、お気軽にご質問ください。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow03.webp" alt="ご契約" width="630" height="430" loading="lazy"></figure>
                                <h3>ご契約</h3>
                                <p>
                                    見積もり内容にご納得いただけましたら、ご契約となります。<br>
                                    納期や設置日時についてもお客様のご都合に合わせて調整いたします。<br>
                                    ご契約後、製品の手配を開始いたします。<br>
                                    納期については、機種や在庫状況により異なりますが、通常1〜3週間程度です。<br>
                                    お急ぎの場合はご相談ください。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow04.webp" alt="納入前検査・納品・設置" width="630" height="430" loading="lazy"></figure>
                                <h3>納入前検査・納品・設置</h3>
                                <p>
                                    製品到着後、納入前に受入検査を実施し、動作確認、精度確認、外観検査などを行い、品質を保証いたします。<br>
                                    検査完了後、お客様のご指定場所へ納品し、設置作業を実施いたします。設置後の動作確認も行い、正常に動作することを確認いたします。<br>
                                    使用方法のご説明も併せて実施し、安心してお使いいただけるようサポートいたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow05.webp" alt="アフターサポート" width="630" height="430" loading="lazy"></figure>
                                <h3>アフターサポート</h3>
                                <p>
                                    納品後も、使用方法のご説明やメンテナンスのご案内など、継続的なサポートを提供いたします。<br>
                                    故障や不具合が発生した際は、迅速な修理サービスもご利用いただけます。<br>
                                    定期的な点検や校正のご案内も行っておりますので、お気軽にご相談ください。
                                </p>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- はかりの修理 -->
                <div id="repair-content" class="businessTabContent" role="tabpanel" aria-labelledby="repair-tab">
                    <h2 class="onlySR">はかりの修理</h2>
                    <div class="businessContent">
                        <p>
                            故障や不具合発生時、<br class="onlySP">迅速な対応で業務再開をサポート。<br>
                            あらゆる機種の修理にご対応します。
                        </p>
                        <ol class="businessFlow">
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow01.webp" alt="故障・不具合のご連絡" width="630" height="430" loading="lazy"></figure>
                                <h3>故障・不具合のご連絡</h3>
                                <p>
                                    お電話、メール、またはお問い合わせフォームより、故障や不具合の状況をご連絡ください。<br>
                                    機種名、故障内容、発生時期、緊急度などをお聞かせいただければ、迅速に対応いたします。<br>
                                    お客様の業務停滞を最小限に抑えるため、できる限り早急にご対応いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow02.webp" alt="現地確認・診断・見積もり" width="630" height="430" loading="lazy"></figure>
                                <h3>現地確認・診断・見積もり</h3>
                                <p>
                                    お客様のご指定場所へお伺いし、現地で故障状況を確認いたします。<br>
                                    原因を診断し、必要な修理内容を特定いたします。<br>
                                    診断結果に基づき、修理内容と費用の見積もりを作成し、お客様にご提示いたします。<br>
                                    見積もり内容についてご不明点がございましたら、お気軽にご質問ください。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow03.webp" alt="ご承認" width="630" height="430" loading="lazy"></figure>
                                <h3>ご承認</h3>
                                <p>
                                    見積もり内容にご納得いただけましたら、修理の実施についてご承認をお願いいたします。<br>
                                    ご承認後、修理作業のスケジュールを調整し、部品の手配や準備を開始いたします。<br>
                                    お客様のご都合に合わせて修理実施日時を調整いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow04.webp" alt="修理実施・動作確認" width="630" height="430" loading="lazy"></figure>
                                <h3>修理実施・動作確認</h3>
                                <p>
                                    ご承認いただいた内容に基づき、修理作業を実施いたします。<br>
                                    部品交換や調整など、必要な作業を行い、修理完了後には動作確認と精度確認を実施いたします。<br>
                                    正常に動作することを確認し、お客様にも動作確認をしていただきます。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow05.webp" alt="完了報告・アフターサポート" width="630" height="430" loading="lazy"></figure>
                                <h3>完了報告・アフターサポート</h3>
                                <p>
                                    修理完了後、修理内容と動作確認結果をまとめた完了報告書をお客様に提出いたします。<br>
                                    今後のメンテナンスや点検のご案内も併せてご説明いたします。<br>
                                    故障や不具合が再発した際は、お気軽にご連絡ください。<br>
                                    継続的なサポートでお客様の業務を支えてまいります。
                                </p>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- はかり・分銅の検査 -->
                <div id="inspection-content" class="businessTabContent" role="tabpanel" aria-labelledby="inspection-tab">
                    <h2 class="onlySR">はかり・分銅の検査</h2>
                    <div class="businessContent">
                        <p>
                            社内管理用のはかりの検査・分銅校正を実施。<br>
                            休日作業にも柔軟に対応し、<br class="onlySP">品質管理を徹底します。
                        </p>
                        <ol class="businessFlow">
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow01.webp" alt="検査のご依頼" width="630" height="430" loading="lazy"></figure>
                                <h3>検査のご依頼</h3>
                                <p>
                                    お電話、メール、またはお問い合わせフォームより、検査のご依頼をお願いいたします。<br>
                                    検査対象のはかりや分銅の種類、数量、検査の目的、希望時期などをお聞かせください。<br>
                                    社内管理用の定期検査や、校正が必要な分銅など、お客様のご要望に合わせて対応いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow02.webp" alt="日程調整・現地確認" width="630" height="430" loading="lazy"></figure>
                                <h3>日程調整・現地確認</h3>
                                <p>
                                    お客様の業務スケジュールに合わせて、検査実施日時を調整いたします。<br>
                                    休日作業にも柔軟に対応いたしますので、ご都合をお聞かせください。<br>
                                    必要に応じて、検査実施場所の確認や、検査環境の事前確認も行います。<br>
                                    お客様の業務に支障をきたさないよう配慮いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow04.webp" alt="検査実施" width="630" height="430" loading="lazy"></figure>
                                <h3>検査実施</h3>
                                <p>
                                    お客様のご指定場所へお伺いし、はかりや分銅の検査を実施いたします。<br>
                                    精度確認、動作確認、外観検査など、適切な検査項目に基づいて丁寧に検査を行います。<br>
                                    検査中も、お客様の業務を可能な限り継続できるよう配慮いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow06.webp" alt="検査結果報告書の作成・提出" width="630" height="430" loading="lazy"></figure>
                                <h3>検査結果報告書の作成・提出</h3>
                                <p>
                                    検査完了後、検査結果をまとめた報告書を作成いたします。<br>
                                    検査日、検査項目、検査結果、適合・不適合の判定などを詳細に記載いたします。<br>
                                    報告書はお客様に提出し、検査結果についてご説明いたします。<br>
                                    品質管理の記録としてご活用いただけます。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow05.webp" alt="不適合時の対応・アフターサポート" width="630" height="430" loading="lazy"></figure>
                                <h3>不適合時の対応・アフターサポート</h3>
                                <p>
                                    検査結果で不適合が判明した場合は、適切な対応方法をご提案いたします。<br>
                                    調整や修理が必要な場合は、お見積もりを作成し、お客様のご承認をいただいた上で対応いたします。<br>
                                    定期的な検査のご案内や、分銅校正のタイミングについてもご説明いたします。<br>
                                    継続的なサポートで、お客様の品質管理を徹底的にサポートいたします。
                                </p>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- 特定計量器（はかり）の代検査 -->
                <div id="substitute-content" class="businessTabContent" role="tabpanel" aria-labelledby="substitute-tab">
                    <h2 class="onlySR">特定計量器（はかり）の代検査</h2>
                    <div class="businessContent">
                        <p>
                            定期検査に代わる、<br class="onlySP">計量士による法定検査（代検査）を実施。<br>
                            トラックスケールから電子天びんまで対応可能です。
                        </p>
                        <ol class="businessFlow">
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow01.webp" alt="定期検査時期の確認・ご依頼" width="630" height="430" loading="lazy"></figure>
                                <h3>定期検査時期の確認・ご依頼</h3>
                                <p>
                                    お電話、メール、またはお問い合わせフォームより、代検査のご依頼をお願いいたします。<br>
                                    定期検査の時期が近づいた際や、法定検査が必要な特定計量器（はかり）がございましたら、お気軽にご連絡ください。<br>
                                    検査対象のはかりの種類、数量、設置場所などをお聞かせください。<br>
                                    計量士による代検査（定期検査に代わる検査）を実施いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow02.webp" alt="日程調整・現地確認" width="630" height="430" loading="lazy"></figure>
                                <h3>日程調整・現地確認</h3>
                                <p>
                                    お客様の業務スケジュールに合わせて、代検査実施日時を調整いたします。<br>
                                    休日作業にも柔軟に対応いたしますので、ご都合をお聞かせください。<br>
                                    必要に応じて、検査実施場所の確認や、検査環境の事前確認も行います。<br>
                                    お客様の業務に支障をきたさないよう配慮いたします。<br>
                                    トラックスケールから電子天びんまで、幅広い計量器に対応可能です。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow04.webp" alt="計量士による検査実施" width="630" height="430" loading="lazy"></figure>
                                <h3>計量士による検査実施</h3>
                                <p>
                                    お客様のご指定場所へ計量士がお伺いし、特定計量器（はかり）の代検査を実施いたします。<br>
                                    計量士の資格を有する専門スタッフが、法定基準に基づいた適切な検査を行います。<br>
                                    精度確認、動作確認など、必要な検査項目を丁寧に実施いたします。<br>
                                    検査中も、お客様の業務を可能な限り継続できるよう配慮いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow06.webp" alt="検査証明書の発行・提出" width="630" height="430" loading="lazy"></figure>
                                <h3>検査証明書の発行・提出</h3>
                                <p>
                                    検査完了後、検査結果に基づいて検査証明書を発行いたします。<br>
                                    検査証明書には、検査日、検査対象のはかりの情報、検査結果、適合・不適合の判定などを記載いたします。<br>
                                    この証明書により、法定検査の代替として認められます。<br>
                                    検査証明書はお客様に提出し、検査結果についてご説明いたします。
                                </p>
                            </li>
                            <li>
                                <figure><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/business/img_flow05.webp" alt="法定検査完了報告・アフターサポート" width="630" height="430" loading="lazy"></figure>
                                <h3>法定検査完了報告・アフターサポート</h3>
                                <p>
                                    代検査の完了により、法定検査の要件を満たしたことをお客様にご報告いたします。<br>
                                    検査証明書は法定検査の記録として保管いただき、必要に応じて提出していただくことができます。<br>
                                    次回の定期検査時期や、継続的なメンテナンスについてもご案内いたします。<br>
                                    お客様の計量器管理をトータルにサポートいたします。
                                </p>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$weights = get_posts(array(
	'post_type'      => 'reference_weight',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'meta_value_num',
	'meta_key'       => '_weight_order',
	'order'          => 'ASC',
));
if (!empty($weights)) :
?>
<section id="weightList">
    <div class="inner">
        <h2>
            所持している基準器分銅等
            <small>reference weights</small>
        </h2>
        <p class="weightsLead">
            当社が所持している基準器分銅等の一覧です。<br>
            県西部でも屈指の所持数ですので、<br class="onlySP">様々なニーズにお応えできます。
        </p>
        <div class="weightsTableWrap">
            <table class="weightsTable">
                <thead>
                    <tr>
                        <th>名称</th>
                        <th>記号</th>
                        <th>№</th>
                        <th>表す量</th>
                        <th>個数</th>
                        <th>材質・形状</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weights as $w) : ?>
                    <tr>
                        <td data-label="名称"><?php echo esc_html($w->post_title); ?></td>
                        <td data-label="記号"><?php echo esc_html(get_post_meta($w->ID, '_weight_symbol', true)); ?></td>
                        <td data-label="№"><?php echo esc_html(get_post_meta($w->ID, '_weight_no', true)); ?></td>
                        <td data-label="表す量"><?php echo esc_html(get_post_meta($w->ID, '_weight_nominal', true)); ?></td>
                        <td data-label="個数"><?php echo esc_html(get_post_meta($w->ID, '_weight_quantity', true)); ?>個</td>
                        <td data-label="材質・形状"><?php echo esc_html(get_post_meta($w->ID, '_weight_material', true)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
