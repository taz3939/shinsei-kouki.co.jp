# テーマ構成（inc / template-parts）

テーマのロジック（inc）と表示パーツ（template-parts）の命名ルール・ファイル一覧です。

---

## inc/ ディレクトリ

**役割**: テーマの設定・ロジック（フック・フィルター・CPT・メタボックス・enqueue 等）。`functions.php` から `require_once` で一括読み込み。

### 命名ルール

| プレフィックス | 意味 | 例 |
|----------------|------|-----|
| **common_** | サイト全体で共通する土台・設定。複数スコープにまたがる小機能をまとめる。 | common_theme.php, common_setting.php |
| **customize_** | 特定の投稿タイプ・ページ種別の「カスタマイズ」（CPT登録・リライト・メタボックス等）。 | customize_topics.php |
| **plugin-customize_** | 特定プラグインの挙動をカスタマイズする連携コード。 | plugin-customize_mw-wp-form.php |

### ファイル一覧

| ファイル | 役割・内容 |
|----------|-------------|
| **common_theme.php** | テーマの土台。add_theme_support・管理メニュー非表示、不要なWP機能の削除（cleanup）、CSS/JS の読み込み（enqueue）・preload。 |
| **common_setting.php** | サイト共通の設定・出力。固定ページのメタディスクリプション用メタボックス、本文中の外部リンクにアイコン付与、カスタマイザー「会社情報」・「メタ情報」、メタタグ（description・canonical・OGP）の出力。 |
| **customize_topics.php** | お知らせ（topics）のカスタマイズ。CPT登録・URLリライト・旧.htmlリダイレクト・クエリ変数・月別/年別アーカイブ・検索対象のtopics限定・検索テンプレート・news→topics移行、目次（INDEX）メタボックス、ピックアップメタボックス。 |
| **plugin-customize_mw-wp-form.php** | MW WP Form プラグインのカスタマイズ。デフォルト style.css の読み込み除外、各フォームの wpautop 無効化（空 p タグを出力しない）。 |

### 読み込み順（functions.php）

1. common_theme.php  
2. common_setting.php  
3. customize_topics.php  
4. plugin-customize_mw-wp-form.php  

※ **parts_*** は template-parts/ に配置し、`parts_business-calendar.php` のみ functions.php から require で読み込む。

---

## template-parts/ ディレクトリ

**役割**: 表示用の断片（HTML＋軽いPHP）。テンプレートから `get_template_part('template-parts/parts_xxx')` で読み込む。  
**命名**: すべて **parts_** を付与し、「パーツ」であることを明示。

### ファイル一覧

| ファイル | 役割・読み込み方法 |
|----------|--------------------|
| **parts_contact-section.php** | お問い合わせセクション（共通）。`get_template_part('template-parts/parts_contact-section')` で読み込み。front-page.php / footer.php で使用。 |
| **parts_pickup-sidebar.php** | ピックアップ記事サイドバー。`get_template_part('template-parts/parts_pickup-sidebar')`。archive-topics / single-topics / search-topics で使用。 |
| **parts_monthly-archive-sidebar.php** | 月別アーカイブサイドバー。`get_template_part('template-parts/parts_monthly-archive-sidebar')`。同上。 |
| **parts_search-sidebar.php** | サイト内検索サイドバー。`get_template_part('template-parts/parts_search-sidebar')`。同上。 |
| **parts_business-calendar.php** | 営業カレンダー機能（CPT登録・休業日メタボックス・Ajax・フロント表示）。**get_template_part では読まない**。`functions.php` から **require** で読み込み。サイト内で複数配置される可能性がある「機能パーツ」として template-parts に配置。 |

---

## まとめ

- **inc/** = 設定・ロジック（common_, customize_, plugin-customize_）。4ファイル。
- **template-parts/** = 表示パーツ（すべて parts_*）。5ファイル。うち 1 つ（parts_business-calendar.php）は require で読み込み、残り 4 つは get_template_part で読み込み。
