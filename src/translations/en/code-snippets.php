<?php

return [
    // Plugin name & element type
    'Code Snippets' => 'Code Snippets',
    'Code Snippet' => 'Code Snippet',
    'code snippet' => 'code snippet',
    'code snippets' => 'code snippets',

    // Permissions
    'View snippets' => 'View snippets',
    'Manage snippets (allows injecting arbitrary code into the site)' => 'Manage snippets (allows injecting arbitrary code into the site)',

    // Index page
    'New Snippet' => 'New Snippet',
    'All Snippets' => 'All Snippets',
    'Position' => 'Position',
    'Environments' => 'Environments',
    'URI Pattern' => 'URI Pattern',
    'URI Patterns' => 'URI Patterns',
    'All' => 'All',
    'All pages' => 'All pages',

    // Edit page
    'Edit Snippet' => 'Edit Snippet',
    'Save' => 'Save',
    'Name' => 'Name',
    'Description' => 'Description',
    'Code' => 'Code',
    'Conditions' => 'Conditions',
    'Settings' => 'Settings',
    'Sort Order' => 'Sort Order',
    'Start' => 'Start',
    'End' => 'End',
    'Enabled' => 'Enabled',

    // Instructions
    'A descriptive name for this snippet (e.g. "Google Analytics", "Facebook Pixel").' => 'A descriptive name for this snippet (e.g. "Google Analytics", "Facebook Pixel").',
    'Optional notes about this snippet.' => 'Optional notes about this snippet.',
    'The HTML/JavaScript code to inject. Include the &lt;script&gt; tags if needed.' => 'The HTML/JavaScript code to inject. Include the &lt;script&gt; tags if needed.',
    'Where in the HTML to inject this snippet.' => 'Where in the HTML to inject this snippet.',
    'Select which environments this snippet should be active in. Leave all unchecked for all environments.' => 'Select which environments this snippet should be active in. Leave all unchecked for all environments.',
    'Restrict this snippet to specific pages. One pattern per line. Use `*` as wildcard (e.g. `products/*`). Leave empty for all pages.' => 'Restrict this snippet to specific pages. One pattern per line. Use `*` as wildcard (e.g. `products/*`). Leave empty for all pages.',
    'Whether this snippet is currently active.' => 'Whether this snippet is currently active.',
    'Controls the order of snippets within the same position. Lower numbers appear first.' => 'Controls the order of snippets within the same position. Lower numbers appear first.',
    'Code entered here is output directly to the site. Use only trusted sources.' => 'Code entered here is output directly to the site. Use only trusted sources.',

    // Plural / dynamic
    '{n} more patterns' => '{n,plural,=1{1 more pattern} other{# more patterns}}',

    // Settings
    'Auto Inject' => 'Auto Inject',
    'Automatically inject snippets into rendered HTML without template changes. When disabled, use these Twig functions in your layout template:' => 'Automatically inject snippets into rendered HTML without template changes. When disabled, use these Twig functions in your layout template:',

    // Flash messages
    'Snippet saved.' => 'Snippet saved.',
    'Could not save the snippet.' => 'Could not save the snippet.',
    'Snippet not found.' => 'Snippet not found.',
];
