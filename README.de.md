# Code Snippets für Craft CMS 5

**[English](README.md) | [日本語](README.ja.md)**

Verwalten und injizieren Sie Code-Snippets (GTM, Analytics, Werbetags, Chat-Widgets usw.) direkt über das Control Panel in Ihre Craft CMS-Website — keine Template-Bearbeitung erforderlich.

## Voraussetzungen

- Craft CMS 5.0.0 oder neuer
- PHP 8.2 oder neuer

## Installation

### Über Composer (empfohlen)

```bash
composer require bit-part/craft-code-snippets
```

Installieren Sie das Plugin anschließend im Craft Control Panel unter **Einstellungen > Plugins** oder über die CLI:

```bash
php craft plugin/install code-snippets
```

### Manuelle Installation

1. Laden Sie das Release von [GitHub](https://github.com/bit-part/craft-code-snippets) herunter
2. Platzieren Sie den Inhalt in einem Verzeichnis und fügen Sie ein [Path-Repository](https://getcomposer.org/doc/05-repositories.md#path) zur `composer.json` Ihres Projekts hinzu
3. Führen Sie `composer require bit-part/craft-code-snippets` aus
4. Installieren Sie über das Control Panel oder die CLI

## Funktionsweise

Code Snippets injiziert Ihren verwalteten Code automatisch in die gerenderte HTML-Ausgabe. Standardmäßig sind keine Template-Änderungen erforderlich.

1. Fügen Sie ein Snippet im Control Panel hinzu (z.B. ein Google Analytics Tracking-Script)
2. Wählen Sie eine Einfügeposition: `<head>` Anfang, `</head>` Ende, `<body>` Anfang oder `</body>` Ende
3. Optional nach Umgebung (dev, staging, production) oder URI-Muster einschränken
4. Das Plugin injiziert aktive Snippets in jede passende Seite

## Funktionen

### Snippet-Verwaltung

Ein eigener **Code Snippets**-Bereich im Control Panel ermöglicht:

- Erstellen, Bearbeiten und Löschen von Snippets
- Massenaktionen zum Aktivieren/Deaktivieren
- Duplizieren vorhandener Snippets für schnelles Setup über Umgebungen hinweg
- Einstellen der Einfügeposition (4 Positionen verfügbar)
- Filtern von Snippets nach Position über die Seitenleiste
- Suchen, Sortieren und Exportieren von Snippets

Die Snippet-Liste verwendet den nativen Element-Index von Craft und bietet die gleiche vertraute Oberfläche wie Einträge und Assets.

### Einfügepositionen

| Position | Einfügung bei | Typische Verwendung |
|----------|--------------|---------------------|
| `<head>` Start | Direkt nach `<head>` | GTM, hochprioritäre Skripte |
| `</head>` Ende | Direkt vor `</head>` | Analytics, Meta-Tags |
| `<body>` Start | Direkt nach `<body>` | GTM noscript-Fallback |
| `</body>` Ende | Direkt vor `</body>` | Chat-Widgets, verzögerte Skripte |

### Auto-Injektion (Standard)

Snippets werden automatisch in das gerenderte HTML injiziert, ohne dass Template-Änderungen erforderlich sind. Das Plugin hakt sich in Crafts Template-Rendering-Pipeline ein und fügt Ihren Code an den richtigen Positionen ein.

Ideal für Marketer und Content-Editoren, die Tracking-Codes ohne Entwicklerunterstützung hinzufügen müssen.

### Twig-Funktionen (Optional)

Für Entwickler, die explizite Kontrolle bevorzugen: Deaktivieren Sie die Auto-Injektion in den Einstellungen und verwenden Sie Twig-Funktionen in Ihrem Layout-Template:

```twig
{{ craft.codeSnippets.headBegin() }}
{{ craft.codeSnippets.headEnd() }}
{{ craft.codeSnippets.bodyBegin() }}
{{ craft.codeSnippets.bodyEnd() }}
```

### Umgebungsfilterung

Beschränken Sie Snippets auf bestimmte Umgebungen. Zum Beispiel: Debug-Skripte nur auf `dev` belassen oder sicherstellen, dass Produktions-Tracking-Codes nicht in der Entwicklung ausgeführt werden.

Wählen Sie eine oder mehrere Umgebungen (dev, staging, production) pro Snippet. Lassen Sie alle deaktiviert, um auf allen Umgebungen auszuführen.

### URI-Muster-Matching

Zielen Sie auf bestimmte Seiten mit URI-Mustern und Wildcard-Unterstützung:

| Muster | Trifft zu auf |
|--------|--------------|
| `checkout` | nur `/checkout` |
| `products/*` | `/products/shoes`, `/products/hats/red` usw. |
| `*` | Alle Seiten |
| (leer) | Alle Seiten |

Geben Sie ein Muster pro Zeile ein. Leer lassen, um alle Seiten zu erfassen.

### Sortierreihenfolge

Steuern Sie die Reihenfolge mehrerer Snippets an derselben Position über das Sortierfeld. Niedrigere Zahlen erscheinen zuerst.

## Einstellungen

Navigieren Sie zu **Einstellungen > Plugins > Code Snippets** zur Konfiguration:

| Einstellung | Standard | Beschreibung |
|-------------|----------|--------------|
| **Auto-Injektion** | `Ein` | Snippets automatisch in gerendertes HTML injizieren. Bei Deaktivierung verwenden Sie stattdessen die Twig-Funktionen. |

## Berechtigungen

Das Plugin registriert zwei Berechtigungen unter **Code Snippets**:

| Berechtigung | Beschreibung |
|--------------|-------------|
| **Snippets anzeigen** | Zugriff auf den Code Snippets-Bereich im Control Panel |
| **Snippets verwalten** | Erstellen, Bearbeiten, Löschen und Umschalten von Snippets. **Hinweis:** Diese Berechtigung erlaubt das Injizieren von beliebigem HTML/JavaScript in die Website. Nur vertrauenswürdigen Benutzern gewähren. |

Berechtigungen sind verschachtelt: „Snippets verwalten" erfordert, dass „Snippets anzeigen" zuerst gewährt wird.

## Sicherheit

Dieses Plugin gibt absichtlich vom Benutzer bereitgestellten HTML/JavaScript-Code im Frontend aus. Dies ist die Kernfunktionalität — ähnlich wie Google Tag Manager, WordPress Header & Footer Scripts und andere Snippet-Injektionstools.

**Wichtig:** Gewähren Sie die Berechtigung „Snippets verwalten" nur vertrauenswürdigen Administratoren. Benutzer mit dieser Berechtigung können beliebige Skripte in Ihre Website injizieren.

## Übersetzungen

Das Plugin enthält Übersetzungen für:

- Englisch (`en`)
- Japanisch (`ja`)

## Support

- [GitHub Issues](https://github.com/bit-part/craft-code-snippets/issues)
- [Dokumentation](https://github.com/bit-part/craft-code-snippets)

## Lizenz

Dieses Plugin ist unter der [Craft License](LICENSE.md) lizenziert.

---

Entwickelt von [bit part LLC](https://bit-part.net)
