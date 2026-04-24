# Code Snippets for Craft CMS 5

**[English](README.md) | [Deutsch](README.de.md)**

コードスニペット（GTM、Analytics、広告タグ、チャットウィジェットなど）を管理画面から直接管理・挿入できる Craft CMS プラグインです。テンプレートの編集は不要です。

## 要件

- Craft CMS 5.0.0 以降
- PHP 8.2 以降

## インストール

### Composer（推奨）

```bash
composer require bit-part/craft-code-snippets
```

コントロールパネルの **設定 > プラグイン** からインストールするか、CLI で実行します：

```bash
php craft plugin/install code-snippets
```

### 手動インストール

1. [GitHub](https://github.com/bit-part/craft-code-snippets) からリリースをダウンロード
2. ディレクトリに配置し、プロジェクトの `composer.json` に [path リポジトリ](https://getcomposer.org/doc/05-repositories.md#path) を追加
3. `composer require bit-part/craft-code-snippets` を実行
4. コントロールパネルまたは CLI からインストール

## 仕組み

Code Snippets は、管理しているコードをレンダリングされた HTML 出力に自動的に挿入します。デフォルトではテンプレートの変更は不要です。

1. コントロールパネルでスニペットを追加（例：Google Analytics のトラッキングスクリプト）
2. 挿入位置を選択：`<head>` 開始直後、`</head>` 終了直前、`<body>` 開始直後、`</body>` 終了直前
3. 必要に応じて環境（dev、staging、production）や URI パターンで制限
4. プラグインが条件に一致するすべてのページにスニペットを挿入

## 機能

### スニペット管理

コントロールパネルの **Code Snippets** セクションでは以下の操作が可能です：

- スニペットの作成・編集・削除
- 一括操作による有効/無効の切り替え
- 既存スニペットの複製（環境別の設定を素早く作成）
- 挿入位置の設定（4つのポジション）
- サイドバーによるポジション別フィルタリング
- 検索・並べ替え・エクスポート

スニペット一覧は Craft のネイティブ エレメントインデックスを使用しており、エントリやアセットと同じ使い慣れたインターフェースで操作できます。

### 挿入位置

| ポジション | 挿入位置 | 主な用途 |
|-----------|---------|---------|
| `<head>` Start | `<head>` 開始直後 | GTM、優先度の高いスクリプト |
| `</head>` End | `</head>` 終了直前 | Analytics、メタタグ |
| `<body>` Start | `<body>` 開始直後 | GTM noscript フォールバック |
| `</body>` End | `</body>` 終了直前 | チャットウィジェット、遅延スクリプト |

### 自動挿入（デフォルト）

スニペットはテンプレートを変更することなく、レンダリングされた HTML に自動挿入されます。プラグインは Craft のテンプレートレンダリングパイプラインにフックし、適切な位置にコードを挿入します。

マーケターやコンテンツ編集者が開発者の助けを借りずにトラッキングコードを追加するのに最適です。

### Twig 関数（オプション）

明示的な制御を好む開発者は、設定で自動挿入を無効にし、レイアウトテンプレートで Twig 関数を使用できます：

```twig
{{ craft.codeSnippets.headBegin() }}
{{ craft.codeSnippets.headEnd() }}
{{ craft.codeSnippets.bodyBegin() }}
{{ craft.codeSnippets.bodyEnd() }}
```

### 環境フィルタリング

スニペットを特定の環境に制限できます。例えば、デバッグスクリプトを `dev` のみにしたり、本番のトラッキングコードが開発環境で実行されないようにできます。

スニペットごとに1つ以上の環境（dev、staging、production）を選択します。すべて未チェックの場合、全環境で有効になります。

### URI パターンマッチング

ワイルドカードをサポートした URI パターンで特定のページをターゲットにできます：

| パターン | マッチ |
|---------|-------|
| `checkout` | `/checkout` のみ |
| `products/*` | `/products/shoes`、`/products/hats/red` など |
| `*` | すべてのページ |
| （空欄） | すべてのページ |

1行に1パターンを入力します。空欄の場合はすべてのページにマッチします。

### 表示順

同じ挿入位置にある複数のスニペットの順序を制御します。小さい数値が先に表示されます。

## 設定

**設定 > プラグイン > Code Snippets** で設定できます：

| 設定 | デフォルト | 説明 |
|------|-----------|------|
| **自動挿入** | `オン` | レンダリングされた HTML にスニペットを自動挿入します。無効の場合は Twig 関数を使用してください。 |

## 権限

プラグインは **Code Snippets** の下に2つの権限を登録します：

| 権限 | 説明 |
|------|------|
| **スニペットの閲覧** | コントロールパネルの Code Snippets セクションへのアクセス |
| **スニペットの管理** | スニペットの作成・編集・削除・切り替え。**注意：** この権限はサイトに任意の HTML/JavaScript を挿入できます。信頼できるユーザーにのみ付与してください。 |

権限はネストされています：「スニペットの管理」には「スニペットの閲覧」が必要です。

## セキュリティ

このプラグインは、ユーザーが入力した HTML/JavaScript コードをフロントエンドに出力します。これは Google Tag Manager や WordPress の Header & Footer Scripts などのスニペット挿入ツールと同様の中核機能です。

**重要：** 「スニペットの管理」権限は信頼できる管理者にのみ付与してください。この権限を持つユーザーはサイトに任意のスクリプトを挿入できます。

## 翻訳

プラグインは以下の翻訳を含んでいます：

- 英語（`en`）
- 日本語（`ja`）

## サポート

- [GitHub Issues](https://github.com/bit-part/craft-code-snippets/issues)
- [ドキュメント](https://github.com/bit-part/craft-code-snippets)

## ライセンス

このプラグインは [Craft License](LICENSE.md) の下でライセンスされています。

---

開発: [bit part LLC](https://bit-part.net)
