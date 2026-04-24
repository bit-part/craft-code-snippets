<?php

return [
    // Plugin name & element type
    'Code Snippets' => 'コードスニペット',
    'Code Snippet' => 'コードスニペット',
    'code snippet' => 'コードスニペット',
    'code snippets' => 'コードスニペット',

    // Permissions
    'View snippets' => 'スニペットの閲覧',
    'Manage snippets (allows injecting arbitrary code into the site)' => 'スニペットの管理（サイトに任意のコードを注入できます）',

    // Index page
    'New Snippet' => '新規スニペット',
    'All Snippets' => 'すべてのスニペット',
    'Position' => '挿入位置',
    'Environments' => '環境',
    'URI Pattern' => 'URIパターン',
    'URI Patterns' => 'URIパターン',
    'All' => 'すべて',
    'All pages' => 'すべてのページ',

    // Edit page
    'Edit Snippet' => 'スニペットを編集',
    'Save' => '保存',
    'Name' => '名前',
    'Description' => '説明',
    'Code' => 'コード',
    'Conditions' => '条件',
    'Settings' => '設定',
    'Sort Order' => '表示順',
    'Start' => '開始直後',
    'End' => '終了直前',
    'Enabled' => '有効',

    // Instructions
    'A descriptive name for this snippet (e.g. "Google Analytics", "Facebook Pixel").' => 'スニペットの名前を入力してください（例: "Google Analytics", "Facebook Pixel"）。',
    'Optional notes about this snippet.' => 'スニペットに関するメモ（任意）。',
    'The HTML/JavaScript code to inject. Include the &lt;script&gt; tags if needed.' => '挿入するHTML/JavaScriptコードを入力してください。必要に応じて&lt;script&gt;タグを含めてください。',
    'Where in the HTML to inject this snippet.' => 'HTMLのどの位置にスニペットを挿入するかを選択します。',
    'Select which environments this snippet should be active in. Leave all unchecked for all environments.' => 'スニペットを有効にする環境を選択します。すべてのチェックを外すと、全環境で有効になります。',
    'Restrict this snippet to specific pages. One pattern per line. Use `*` as wildcard (e.g. `products/*`). Leave empty for all pages.' => '特定のページにスニペットを制限します。1行に1パターン。ワイルドカード `*` が使用可能（例: `products/*`）。空欄の場合はすべてのページで有効です。',
    'Whether this snippet is currently active.' => 'このスニペットが現在有効かどうか。',
    'Controls the order of snippets within the same position. Lower numbers appear first.' => '同じ挿入位置内でのスニペットの表示順を制御します。小さい数値が先に表示されます。',
    'Code entered here is output directly to the site. Use only trusted sources.' => 'ここに入力したコードはサイトに直接出力されます。信頼できるソースのみ使用してください。',

    // Plural / dynamic
    '{n} more patterns' => '他{n}件のパターン',

    // Settings
    'Auto Inject' => '自動挿入',
    'Automatically inject snippets into rendered HTML without template changes. When disabled, use these Twig functions in your layout template:' => 'テンプレートを変更せずに、レンダリングされたHTMLにスニペットを自動挿入します。無効の場合は、レイアウトテンプレートで以下のTwig関数を使用してください:',

    // Flash messages
    'Snippet saved.' => 'スニペットを保存しました。',
    'Could not save the snippet.' => 'スニペットを保存できませんでした。',
    'Snippet not found.' => 'スニペットが見つかりません。',
];
