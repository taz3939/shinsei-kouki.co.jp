<?php
/**
 * maintenance.php
 * メンテナンスモード（カスタマイザーでオン/オフ）。ログイン中は通常表示。
 * 共通仕様：WP_THEME_STRUCTURE.md 参照。
 */

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section( 'shinsei_kouki_maintenance', array(
		'title'    => 'メンテナンス',
		'priority' => 160,
	) );
	$wp_customize->add_setting( 'maintenance_mode', array(
		'default'           => false,
		'sanitize_callback' => function ( $v ) { return (bool) $v; },
	) );
	$wp_customize->add_control( 'maintenance_mode', array(
		'label'       => 'メンテナンスモードを有効にする',
		'section'     => 'shinsei_kouki_maintenance',
		'type'        => 'checkbox',
		'description' => '有効にすると、未ログインの訪問者には「メンテナンス中」ページが表示されます。ログイン中の管理者は通常どおりサイトを表示できます。※サイトURLを www あり/なしで変更した場合は、再度ログインしてください。',
	) );
} );

add_action( 'template_redirect', function () {
	if ( is_admin() || ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] === 'wp-login.php' ) ) {
		return;
	}
	if ( is_user_logged_in() ) {
		return;
	}
	if ( ! get_theme_mod( 'maintenance_mode', false ) ) {
		return;
	}

	status_header( 503 );
	header( 'Retry-After: 3600' );
	header( 'Content-Type: text/html; charset=UTF-8' );
	nocache_headers();
	header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
	header( 'Pragma: no-cache' );

	$name = get_bloginfo( 'name' );
	?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>メンテナンス中 | <?php echo esc_html( $name ); ?></title>
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; font-family: "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif; background: #f5f5f5; color: #333; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
		.maintenance { text-align: center; max-width: 480px; }
		.maintenance h1 { font-size: 1.5rem; font-weight: 600; margin: 0 0 1rem; }
		.maintenance p { margin: 0 0 0.5rem; line-height: 1.7; }
		.maintenance .note { font-size: 0.875rem; color: #666; margin-top: 1.5rem; }
	</style>
</head>
<body>
	<div class="maintenance">
		<h1>メンテナンス中です</h1>
		<p>只今、サイトのメンテナンス作業を進めております。<br>しばらくお待ちください。</p>
		<p class="note"><?php echo esc_html( $name ); ?></p>
	</div>
</body>
</html>
<?php
	exit;
}, 10 );
