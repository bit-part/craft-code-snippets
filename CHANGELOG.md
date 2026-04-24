# Changelog

## 1.0.0 - 2026-04-24

### Added
- CP section for managing code snippets using Craft's native element index
- Four insertion positions: `<head>` start, `</head>` end, `<body>` start, `</body>` end
- Auto-injection into rendered HTML (no template changes required)
- Optional Twig functions: `craft.codeSnippets.headBegin()`, `headEnd()`, `bodyBegin()`, `bodyEnd()`
- Environment-based filtering (dev, staging, production)
- URI pattern matching with wildcard support for page-level targeting
- Sort order control for snippets in the same position
- Bulk actions: enable/disable, duplicate, delete
- Sidebar filtering by insertion position
- User permissions: view snippets, manage snippets
- English and Japanese translations
