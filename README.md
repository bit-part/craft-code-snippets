# Code Snippets for Craft CMS 5

**[日本語](README.ja.md) | [Deutsch](README.de.md)**

Manage and inject code snippets (GTM, Analytics, ad tags, chat widgets, etc.) into your Craft CMS site directly from the control panel — no template editing required.

## Requirements

- Craft CMS 5.0.0 or later
- PHP 8.2 or later

## Installation

### Via Composer (recommended)

```bash
composer require bit-part/craft-code-snippets
```

Then install the plugin from the Craft control panel under **Settings > Plugins**, or via the CLI:

```bash
php craft plugin/install code-snippets
```

### Manual Installation

1. Download the release from [GitHub](https://github.com/bit-part/craft-code-snippets)
2. Place the contents in a directory and add a [path repository](https://getcomposer.org/doc/05-repositories.md#path) to your project's `composer.json`
3. Run `composer require bit-part/craft-code-snippets`
4. Install via the control panel or CLI

## How It Works

Code Snippets automatically injects your managed code into the rendered HTML output. No template changes are needed by default.

1. Add a snippet in the control panel (e.g., a Google Analytics tracking script)
2. Choose an insertion position: `<head>` start, `</head>` end, `<body>` start, or `</body>` end
3. Optionally restrict by environment (dev, staging, production) or URI pattern
4. The plugin injects active snippets into every matching page

## Features

### Snippet Management

A dedicated **Code Snippets** section in the control panel lets you:

- Create, edit, and delete snippets
- Enable/disable snippets with bulk actions
- Duplicate existing snippets for quick setup across environments
- Set insertion position (4 positions available)
- Filter snippets by position using the sidebar
- Search, sort, and export snippets

The snippet list uses Craft's native element index, providing the same familiar interface as Entries and Assets.

### Insertion Positions

| Position | Inserts at | Typical use |
|----------|-----------|-------------|
| `<head>` Start | Right after `<head>` | GTM, high-priority scripts |
| `</head>` End | Right before `</head>` | Analytics, meta tags |
| `<body>` Start | Right after `<body>` | GTM noscript fallback |
| `</body>` End | Right before `</body>` | Chat widgets, deferred scripts |

### Auto-Injection (Default)

Snippets are automatically injected into rendered HTML without any template changes. The plugin hooks into Craft's template rendering pipeline and inserts your code at the correct positions.

This is ideal for marketers and content editors who need to add tracking codes without developer assistance.

### Twig Functions (Optional)

For developers who prefer explicit control, disable auto-injection in settings and use Twig functions in your layout template:

```twig
{{ craft.codeSnippets.headBegin() }}
{{ craft.codeSnippets.headEnd() }}
{{ craft.codeSnippets.bodyBegin() }}
{{ craft.codeSnippets.bodyEnd() }}
```

### Environment Filtering

Restrict snippets to specific environments. For example, keep debug scripts on `dev` only, or ensure production tracking codes don't fire in development.

Select one or more environments (dev, staging, production) per snippet. Leave all unchecked to run on all environments.

### URI Pattern Matching

Target specific pages using URI patterns with wildcard support:

| Pattern | Matches |
|---------|---------|
| `checkout` | `/checkout` only |
| `products/*` | `/products/shoes`, `/products/hats/red`, etc. |
| `*` | All pages |
| (empty) | All pages |

Enter one pattern per line. Leave empty to match all pages.

### Sort Order

Control the order of multiple snippets in the same position using the sort order field. Lower numbers appear first.

## Settings

Navigate to **Settings > Plugins > Code Snippets** to configure:

| Setting | Default | Description |
|---------|---------|-------------|
| **Auto Inject** | `On` | Automatically inject snippets into rendered HTML. When disabled, use the Twig functions instead. |

## Permissions

The plugin registers two permissions under **Code Snippets**:

| Permission | Description |
|------------|-------------|
| **View snippets** | Access the Code Snippets section in the control panel |
| **Manage snippets** | Create, edit, delete, and toggle snippets. **Note:** This permission allows injecting arbitrary HTML/JavaScript into the site. Grant only to trusted users. |

Permissions are nested: "Manage snippets" requires "View snippets" to be granted first.

## Security

This plugin intentionally outputs user-provided HTML/JavaScript code on the frontend. This is the core functionality — similar to Google Tag Manager, WordPress Header & Footer Scripts, and other snippet injection tools.

**Important:** Only grant the "Manage snippets" permission to trusted administrators. Users with this permission can inject arbitrary scripts into your site.

## Translations

The plugin includes translations for:

- English (`en`)
- Japanese (`ja`)

## Support

- [GitHub Issues](https://github.com/bit-part/craft-code-snippets/issues)
- [Documentation](https://github.com/bit-part/craft-code-snippets)

## License

This plugin is licensed under the [Craft License](LICENSE.md).

---

Built by [bit part LLC](https://bit-part.net)
