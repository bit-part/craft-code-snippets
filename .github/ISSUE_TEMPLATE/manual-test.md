---
name: Manual Test Checklist
about: Code Snippets manual testing scenarios
title: "Manual Test: v1.0.0"
labels: testing
---

# Code Snippets — Manual Test Checklist

**Test Environment**: https://craft-plugin-dev.bpdev.cfbx.jp/admin
**Tester**:
**Date**:
**Version**: 1.0.0

---

## 1. CP Navigation & Access

- [ ] **1.1** "Code Snippets" がCPナビゲーションに表示されること
- [ ] **1.2** アイコン（`</>`）が正しく表示されること
- [ ] **1.3** クリックするとスニペット一覧ページが開くこと

## 2. Empty State

- [ ] **2.1** スニペットがない状態で「No snippets yet.」が表示されること
- [ ] **2.2** 「Create your first snippet」ボタンが表示されること
- [ ] **2.3** ヘッダーの「New Snippet」ボタンは非表示であること（スニペット0件時）

## 3. Create Snippet — Head Position

- [ ] **3.1** 「Create your first snippet」または「New Snippet」をクリック → 作成フォームが開くこと
- [ ] **3.2** 以下の情報でスニペットを作成:
  - Name: `GA Test`
  - Code: `<!-- GA HEAD TEST -->`
  - Position: `<head>`
  - Environments: すべて未チェック（= 全環境）
  - URI Pattern: 空（= 全ページ）
  - Enabled: ON
  - Sort Order: 0
- [ ] **3.3** 保存後「Snippet saved.」のメッセージが表示されること
- [ ] **3.4** 一覧に戻り、作成したスニペットが表示されること

## 4. Create Snippet — Body End Position

- [ ] **4.1** 2つ目のスニペットを作成:
  - Name: `Chat Widget`
  - Code: `<!-- CHAT BODY END -->`
  - Position: `</body> End`
  - その他: デフォルト
- [ ] **4.2** 一覧に2つのスニペットが表示されること

## 5. Auto-Injection Verification

- [ ] **5.1** フロントエンドページ（例: `https://craft-plugin-dev.bpdev.cfbx.jp/`）をブラウザで開く
- [ ] **5.2** ページのソースを表示（右クリック → ページのソースを表示）
- [ ] **5.3** `<!-- GA HEAD TEST -->` が `</head>` の直前にあること
- [ ] **5.4** `<!-- CHAT BODY END -->` が `</body>` の直前にあること

## 6. Body Begin Position

- [ ] **6.1** 3つ目のスニペットを作成:
  - Name: `Body Open Script`
  - Code: `<!-- BODY BEGIN TEST -->`
  - Position: `<body> Start`
- [ ] **6.2** フロントエンドのソースで `<!-- BODY BEGIN TEST -->` が `<body...>` タグの直後にあること

## 7. Environment Filtering

- [ ] **7.1** 新しいスニペットを作成:
  - Name: `Production Only`
  - Code: `<!-- PROD ONLY -->`
  - Position: `<head>`
  - Environments: `production` のみチェック
- [ ] **7.2** フロントエンドのソースで `<!-- PROD ONLY -->` が **表示されないこと**（テスト環境は dev のため）
- [ ] **7.3** CP一覧でEnvironments列に「production」と表示されること

## 8. URI Pattern Filtering

- [ ] **8.1** 新しいスニペットを作成:
  - Name: `Homepage Only`
  - Code: `<!-- HOME ONLY -->`
  - Position: `<head>`
  - URI Pattern: `/`（ホームページのみ）
- [ ] **8.2** ホームページ（`/`）のソースに `<!-- HOME ONLY -->` があること
- [ ] **8.3** 他のページ（例: `/admin` 以外の存在するページ）のソースに `<!-- HOME ONLY -->` がないこと

## 9. Toggle Enable/Disable

- [ ] **9.1** 一覧で `GA Test` の「Disable」ボタンをクリック
- [ ] **9.2** 「Snippet disabled.」メッセージが表示されること
- [ ] **9.3** 一覧のStatus列が「Disabled」に変わること
- [ ] **9.4** フロントエンドのソースで `<!-- GA HEAD TEST -->` が **表示されないこと**
- [ ] **9.5** 再度「Enable」をクリック → 「Snippet enabled.」メッセージ → フロントエンドに復活

## 10. Edit Snippet

- [ ] **10.1** 一覧で `GA Test` のリンクをクリック → 編集フォームが開くこと
- [ ] **10.2** 名前を `GA Test Modified` に変更して保存
- [ ] **10.3** 一覧に反映されていること
- [ ] **10.4** 保存済みのEnvironmentsチェックボックスが正しく復元されていること

## 11. Delete Snippet

- [ ] **11.1** 一覧で `Body Open Script` の「Delete」ボタンをクリック
- [ ] **11.2** 確認ダイアログが表示されること
- [ ] **11.3** 確認 → 「Snippet deleted.」メッセージが表示されること
- [ ] **11.4** 一覧からスニペットが消えていること
- [ ] **11.5** フロントエンドのソースから `<!-- BODY BEGIN TEST -->` が消えていること

## 12. Sort Order

- [ ] **12.1** 2つの `<head>` スニペットの Sort Order を入れ替え:
  - `GA Test Modified` → sortOrder: `10`
  - `Homepage Only` → sortOrder: `0`
- [ ] **12.2** フロントエンドのソースで `<!-- HOME ONLY -->` が `<!-- GA HEAD TEST -->` より先に出力されること

## 13. Plugin Settings

- [ ] **13.1** Settings > Plugins > Code Snippets を開く
- [ ] **13.2** 「Auto Inject」ライトスイッチが表示されること（デフォルト: ON）
- [ ] **13.3** OFF にして保存 → フロントエンドのソースにスニペットが **出力されないこと**
- [ ] **13.4** ON に戻して保存 → フロントエンドにスニペットが **復活すること**

## 14. Permissions

- [ ] **14.1** Settings > Users > User Groups でテストグループを作成（または既存グループを使用）
- [ ] **14.2** Code Snippets パーミッションが表示されること（View snippets / Manage snippets）
- [ ] **14.3** 「Manage snippets」に「allows injecting arbitrary code into the site」の警告が表示されること
- [ ] **14.4**（オプション）View のみの権限でログイン → 一覧は見えるが、New/Edit/Delete ボタンが非表示であること

## 15. CP Does Not Get Injected

- [ ] **15.1** CP内のページ（Dashboard等）のソースに、作成したスニペットのコードが含まれ **ないこと**
  - ブラウザの開発者ツール（Console）で `document.documentElement.innerHTML` を検索して確認

---

## Test Results

| # | 項目 | 結果 | メモ |
|---|------|------|------|
| 1 | CP Navigation | | |
| 2 | Empty State | | |
| 3 | Create (Head) | | |
| 4 | Create (Body End) | | |
| 5 | Auto-Injection | | |
| 6 | Body Begin | | |
| 7 | Env Filtering | | |
| 8 | URI Pattern | | |
| 9 | Toggle | | |
| 10 | Edit | | |
| 11 | Delete | | |
| 12 | Sort Order | | |
| 13 | Settings | | |
| 14 | Permissions | | |
| 15 | CP Not Injected | | |
