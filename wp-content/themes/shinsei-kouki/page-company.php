<?php
/**
 * page-company.php
 * 会社概要ページテンプレート
 */
get_header();
?>

<section id="companyPage">
    <div class="inner">
        <h1 class="pageTitle">
            会社概要
            <small>about us</small>
        </h1>
        
        <div class="companyContent">
            <table class="companyTable">
                <tbody>
                    <tr>
                        <th>商　号</th>
                        <td>神西衡機工業株式会社</td>
                    </tr>
                    <tr>
                        <th>代表者</th>
                        <td><?php echo get_option('company_representative', '代表者名'); ?></td>
                    </tr>
                    <tr>
                        <th>所在地</th>
                        <td><?php echo get_option('company_address', '所在地'); ?></td>
                    </tr>
                    <tr>
                        <th>電話番号</th>
                        <td><?php echo get_option('company_tel', '電話番号'); ?></td>
                    </tr>
                    <tr>
                        <th>FAX</th>
                        <td><?php echo get_option('company_fax', 'FAX番号'); ?></td>
                    </tr>
                    <tr>
                        <th>メールアドレス</th>
                        <td><?php echo get_option('company_email', 'メールアドレス'); ?></td>
                    </tr>
                    <tr>
                        <th>事業内容</th>
                        <td>
                            はかり・分銅等の販売<br>
                            はかりの修理<br>
                            はかり・分銅の検査<br>
                            特定計量器（はかり）の代検査
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php get_footer(); ?>
