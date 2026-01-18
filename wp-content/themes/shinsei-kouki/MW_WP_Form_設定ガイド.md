# MW WP Form お問い合わせフォーム設定ガイド

## 1. プラグインのインストール

1. WordPress管理画面 → **プラグイン** → **新規追加**
2. 「MW WP Form」を検索
3. **インストール** → **有効化**

## 2. フォームの作成

1. WordPress管理画面 → **MW WP Form** → **新規追加**
2. フォーム名：「お問い合わせフォーム」
3. 以下のHTMLを「フォーム設定」のテキストエリアに貼り付け

## 3. フォームHTML（コピー＆ペースト用）

```html
<p class="form-note">※必須 は入力必須の項目になります</p>

<div class="mw_wp_form_inner">
  <dl class="form-item">
    <dt class="required">お名前<span class="required-mark">必須</span></dt>
    <dd>[mwform_text name="name" placeholder="お名前をご入力ください" show_error="false"]</dd>
  </dl>
  [mwform_error keys="name"]

  <dl class="form-item">
    <dt>会社名</dt>
    <dd>[mwform_text name="company" placeholder="会社名をご入力ください" show_error="false"]</dd>
  </dl>
  [mwform_error keys="company"]

  <dl class="form-item">
    <dt>ご住所</dt>
    <dd>[mwform_text name="address" placeholder="ご住所をご入力ください" show_error="false"]</dd>
  </dl>
  [mwform_error keys="address"]

  <dl class="form-item">
    <dt class="required">ご連絡先お電話番号<span class="required-mark">必須</span></dt>
    <dd>[mwform_text name="tel" placeholder="090-1234-5678" show_error="false"]</dd>
  </dl>
  [mwform_error keys="tel"]

  <dl class="form-item">
    <dt class="required">メールアドレス（半角）<span class="required-mark">必須</span></dt>
    <dd>[mwform_email name="email" placeholder="example@email.com" show_error="false"]</dd>
  </dl>
  [mwform_error keys="email"]

  <dl class="form-item">
    <dt class="required">お問い合わせ種別<span class="required-mark">必須</span></dt>
    <dd>
      [mwform_select name="inquiry_type" children=":選択してください,はかり・分銅等の販売について,はかりの修理について,はかり・分銅の検査について,特定計量器（はかり）の代検査について,その他" show_error="false"]
    </dd>
  </dl>
  [mwform_error keys="inquiry_type"]

  <dl class="form-item">
    <dt class="required">お問い合わせ内容<span class="required-mark">必須</span></dt>
    <dd>
      [mwform_textarea name="message" placeholder="お問い合わせ内容をご入力ください" show_error="false"]
      <p class="form-hint">※お問い合わせ内容の詳細をこちらに入力をお願いいたします。</p>
    </dd>
  </dl>
  [mwform_error keys="message"]

  <dl class="form-item">
    <dt></dt>
    <dd>
      <label>[mwform_checkbox name="privacy_agreement" value="同意する" show_error="false"] 個人情報保護方針に同意する</label>
    </dd>
  </dl>
  [mwform_error keys="privacy_agreement"]
</div>

<div class="submit-area">
  [mwform_submitButton confirm_value="確認画面へ" submit_value="送信する"]
  [mwform_backButton value="入力内容を修正する"]
</div>
```

## 4. バリデーション設定（詳細手順）

フォーム設定画面の「バリデーション」タブを開き、以下の手順で設定してください。

### バリデーションルールの追加方法

1. 「バリデーション」タブを開く
2. 「バリデーションルールを追加」ボタンをクリック
3. 追加されたルールセクションが折りたたまれた状態で表示されます
4. セクションの右側にある**下向き矢印アイコン（▼）**をクリックしてセクションを開く
   - セクションを開くと、設定項目が表示されます
5. 各項目ごとにルールを追加していきます

### 各項目の設定手順

#### 1. name（お名前）の設定

1. 「バリデーションルールを追加」をクリック
2. 追加されたルールセクション（折りたたまれた状態）の右側にある**下向き矢印アイコン**をクリックしてセクションを開く
   - または、セクションのタイトル部分（「name」と表示されている部分）をクリックしても開けます
3. 「バリデーションを適用する項目:」の入力欄に `name` と入力
4. チェックボックスから「**必須項目**」にチェックを入れる
5. これで設定完了です
   - エラーメッセージは、チェックボックスを選択すると自動的にデフォルトのメッセージ（「必須項目です。」など）が使用されます
   - カスタムメッセージが必要な場合は、functions.phpでカスタムバリデーションを追加する必要があります（後述）

#### 2. tel（ご連絡先お電話番号）の設定

1. 「バリデーションルールを追加」をクリック
2. 追加されたルールセクションの右側にある**下向き矢印アイコン**をクリックしてセクションを開く
3. 「バリデーションを適用する項目:」の入力欄に `tel` と入力
4. チェックボックスから「**必須項目**」にチェックを入れる
5. チェックボックスから「**電話番号**」にもチェックを入れる（形式チェック）
6. これで設定完了です
   - エラーメッセージは自動的にデフォルトのメッセージが使用されます

#### 3. email（メールアドレス）の設定

1. 「バリデーションルールを追加」をクリック
2. 追加されたルールセクションの右側にある**下向き矢印アイコン**をクリックしてセクションを開く
3. 「バリデーションを適用する項目:」の入力欄に `email` と入力
4. チェックボックスから「**必須項目**」にチェックを入れる
5. チェックボックスから「**メールアドレス**」にもチェックを入れる（形式チェック）
6. これで設定完了です
   - エラーメッセージは自動的にデフォルトのメッセージが使用されます

#### 4. inquiry_type（お問い合わせ種別）の設定

1. 「バリデーションルールを追加」をクリック
2. 追加されたルールセクションの右側にある**下向き矢印アイコン**をクリックしてセクションを開く
3. 「バリデーションを適用する項目:」の入力欄に `inquiry_type` と入力
4. チェックボックスから「**必須項目**」にチェックを入れる
5. これで設定完了です
   - エラーメッセージは自動的にデフォルトのメッセージが使用されます

#### 5. message（お問い合わせ内容）の設定

1. 「バリデーションルールを追加」をクリック
2. 追加されたルールセクションの右側にある**下向き矢印アイコン**をクリックしてセクションを開く
3. 「バリデーションを適用する項目:」の入力欄に `message` と入力
4. チェックボックスから「**必須項目**」にチェックを入れる
5. これで設定完了です
   - エラーメッセージは自動的にデフォルトのメッセージが使用されます

#### 6. privacy_agreement（個人情報保護方針への同意）の設定

1. 「バリデーションルールを追加」をクリック
2. 追加されたルールセクションの右側にある**下向き矢印アイコン**をクリックしてセクションを開く
3. 「バリデーションを適用する項目:」の入力欄に `privacy_agreement` と入力
4. チェックボックスから「**必須項目 (チェックボックス)**」にチェックを入れる
5. これで設定完了です
   - エラーメッセージは自動的にデフォルトのメッセージが使用されます

### 設定のポイント

- **必須項目**：入力必須にする場合は必ずチェック
- **メールアドレス**：メール形式のチェックが必要な場合はチェック
- **電話番号**：電話番号形式のチェックが必要な場合はチェック
- **必須項目 (チェックボックス)**：チェックボックスが必須の場合に使用

### 任意項目（バリデーション不要）

以下の項目は任意のため、バリデーション設定は不要です：
- **company（会社名）**
- **address（ご住所）**

### 設定例のまとめ

| 項目名 | バリデーションを適用する項目 | チェックする項目 | 備考 |
|---|---|---|---|
| name | `name` | 必須項目 | デフォルトのエラーメッセージが使用されます |
| tel | `tel` | 必須項目、電話番号 | デフォルトのエラーメッセージが使用されます |
| email | `email` | 必須項目、メールアドレス | デフォルトのエラーメッセージが使用されます |
| inquiry_type | `inquiry_type` | 必須項目 | デフォルトのエラーメッセージが使用されます |
| message | `message` | 必須項目 | デフォルトのエラーメッセージが使用されます |
| privacy_agreement | `privacy_agreement` | 必須項目 (チェックボックス) | デフォルトのエラーメッセージが使用されます |

### エラーメッセージについて

- **デフォルトメッセージ**：チェックボックスを選択すると、MW WP Formが自動的にデフォルトのエラーメッセージを表示します
  - 必須項目：「必須項目です。」
  - メールアドレス形式：「メールアドレスの形式が正しくありません。」
  - 電話番号形式：「電話番号の形式が正しくありません。」

- **カスタムメッセージが必要な場合**：functions.phpでカスタムバリデーションを追加する必要があります（詳細は後述）

## 5. メール設定

フォーム設定画面の「メール」タブで設定：

### 管理者宛メール

- **送信先メールアドレス**：管理者のメールアドレス
- **送信者メールアドレス**：サイトのメールアドレス
- **件名**：`[神西衡機工業] お問い合わせがありました`
- **本文**：
```
以下の内容でお問い合わせがありました。

お名前：[name]
会社名：[company]
ご住所：[address]
ご連絡先お電話番号：[tel]
メールアドレス：[email]
お問い合わせ種別：[inquiry_type]
お問い合わせ内容：
[message]
```

### 自動返信メール（オプション）

- **送信先メールアドレス**：`[email]`
- **送信者メールアドレス**：サイトのメールアドレス
- **件名**：`[神西衡機工業] お問い合わせありがとうございます`
- **本文**：
```
[name] 様

この度は、神西衡機工業株式会社にお問い合わせいただき、誠にありがとうございます。

以下の内容でお問い合わせを承りました。
担当者より、3営業日以内にご連絡させていただきます。

【お問い合わせ内容】
お問い合わせ種別：[inquiry_type]
お問い合わせ内容：
[message]

────────────────────
神西衡機工業株式会社
```

## 6. フォームIDの確認

フォーム作成後、フォーム一覧画面で「フォームキー」を確認してください。
（例：`123` のような数字）

## 7. 固定ページへの設定（オプション）

固定ページ「お問い合わせ」のカスタムフィールドに以下を追加：
- **フィールド名**：`mw_wp_form_id`
- **値**：フォームキー（例：`123`）

※カスタムフィールドを設定しない場合、最初に作成したフォームが自動で使用されます。

## 8. 主なショートコード一覧

| ショートコード | 用途 | 例 |
|---|---|---|
| `[mwform_text]` | テキスト入力 | `[mwform_text name="name" placeholder="お名前"]` |
| `[mwform_email]` | メールアドレス入力 | `[mwform_email name="email"]` |
| `[mwform_textarea]` | テキストエリア | `[mwform_textarea name="message"]` |
| `[mwform_select]` | セレクトボックス | `[mwform_select name="type" children=":選択,項目1,項目2"]` |
| `[mwform_checkbox]` | チェックボックス | `[mwform_checkbox name="agree" value="同意する"]` |
| `[mwform_submitButton]` | 送信ボタン | `[mwform_submitButton submit_value="送信"]` |
| `[mwform_backButton]` | 戻るボタン | `[mwform_backButton value="戻る"]` |
| `[mwform_error]` | エラー表示 | `[mwform_error keys="name,email"]` |

## 9. 確認画面の有効化

フォーム設定画面の「確認画面」タブで「確認画面を使用する」にチェックを入れると、送信前に確認画面が表示されます。

## 10. カスタムエラーメッセージの設定（オプション）

デフォルトのエラーメッセージではなく、カスタムメッセージを使用したい場合は、`functions.php`に以下のコードを追加します。

```php
// MW WP Formのカスタムバリデーション
function my_mwform_validation_rule( $Validation, $data ) {
    // お名前
    $Validation->set_rule( 'name', 'noempty', array( 'message' => 'お名前を入力してください。' ) );
    
    // 電話番号
    $Validation->set_rule( 'tel', 'noempty', array( 'message' => '電話番号を入力してください。' ) );
    $Validation->set_rule( 'tel', 'tel', array( 'message' => '電話番号の形式が正しくありません。' ) );
    
    // メールアドレス
    $Validation->set_rule( 'email', 'noempty', array( 'message' => 'メールアドレスを入力してください。' ) );
    $Validation->set_rule( 'email', 'email', array( 'message' => 'メールアドレスの形式が正しくありません。' ) );
    
    // お問い合わせ種別
    $Validation->set_rule( 'inquiry_type', 'noempty', array( 'message' => 'お問い合わせ種別を選択してください。' ) );
    
    // お問い合わせ内容
    $Validation->set_rule( 'message', 'noempty', array( 'message' => 'お問い合わせ内容を入力してください。' ) );
    
    // 個人情報保護方針への同意
    $Validation->set_rule( 'privacy_agreement', 'noempty', array( 'message' => '個人情報保護方針への同意が必要です。' ) );
    
    return $Validation;
}
add_filter( 'mwform_validation_mw-wp-form-XXX', 'my_mwform_validation_rule', 10, 2 );
```

**注意**：`mw-wp-form-XXX`の`XXX`部分は、実際のフォームキー（フォームID）に置き換えてください。
