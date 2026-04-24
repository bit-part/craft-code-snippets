<?php

namespace bitpart\codesnippets\services;

use bitpart\codesnippets\elements\Snippet;
use Craft;
use yii\base\Component;

class SnippetService extends Component
{
    /**
     * Per-request cache of active snippets, grouped by position.
     *
     * @var array<string, array<string, Snippet[]>> Outer key = "{env}|{uri}", inner key = position constant.
     */
    private array $_activeSnippetsCache = [];

    /**
     * Reset the per-request cache (mainly for tests).
     */
    public function clearCache(): void
    {
        $this->_activeSnippetsCache = [];
    }

    /**
     * Render all active snippets for a position as a single string.
     */
    public function renderSnippetsForPosition(string $position): string
    {
        $snippets = $this->getActiveSnippetsByPosition($position);

        if (empty($snippets)) {
            return '';
        }

        $output = [];
        foreach ($snippets as $snippet) {
            $output[] = $snippet->code;
        }

        return implode("\n", $output);
    }

    /**
     * Get active snippets for a given position, filtered by environment and URI.
     *
     * @return Snippet[]
     */
    public function getActiveSnippetsByPosition(string $position): array
    {
        $grouped = $this->getActiveSnippetsGrouped();
        return $grouped[$position] ?? [];
    }

    /**
     * Get all active snippets grouped by position (single query, cached per request).
     *
     * @return array<string, Snippet[]>
     */
    private function getActiveSnippetsGrouped(): array
    {
        $currentEnv = $this->getCurrentEnv();
        $currentUri = $this->getCurrentUri();
        $cacheKey = $this->buildCacheKey($currentEnv, $currentUri);

        if (isset($this->_activeSnippetsCache[$cacheKey])) {
            return $this->_activeSnippetsCache[$cacheKey];
        }

        /** @var Snippet[] $snippets */
        $snippets = Snippet::find()
            ->status('enabled')
            ->orderBy(['codesnippets_snippets.sortOrder' => SORT_ASC])
            ->all();

        $grouped = [
            Snippet::POSITION_HEAD_BEGIN => [],
            Snippet::POSITION_HEAD_END => [],
            Snippet::POSITION_BODY_BEGIN => [],
            Snippet::POSITION_BODY_END => [],
        ];

        foreach ($snippets as $snippet) {
            if (!$this->matchesEnvironment($snippet, $currentEnv)) {
                continue;
            }

            if (!$this->matchesUri($snippet, $currentUri)) {
                continue;
            }

            if (isset($grouped[$snippet->position])) {
                $grouped[$snippet->position][] = $snippet;
            }
        }

        $this->_activeSnippetsCache[$cacheKey] = $grouped;
        return $grouped;
    }

    /**
     * Build the cache key for a given environment and URI combination.
     */
    private function buildCacheKey(?string $env, string $uri): string
    {
        return ($env ?? '') . '|' . $uri;
    }

    /**
     * Returns the current Craft environment.
     * Override in tests or subclasses to inject a different value.
     */
    protected function getCurrentEnv(): ?string
    {
        return Craft::$app->env;
    }

    /**
     * Returns the current request's full path.
     * Override in tests or subclasses to inject a different value.
     */
    protected function getCurrentUri(): string
    {
        try {
            return Craft::$app->getRequest()->getFullPath();
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Check if a snippet matches the current environment.
     *
     * Note: when $currentEnv is null/empty (i.e. CRAFT_ENVIRONMENT is unset),
     * the snippet is treated as matching to keep behavior predictable for
     * users who haven't configured the environment variable yet. The edit
     * form's instructions reflect this behavior.
     *
     * @param Snippet|object $snippet Any object exposing getEnvironments(): string[].
     *                                Typed as object so test stubs can pass without
     *                                bootstrapping Craft (Element base class).
     */
    private function matchesEnvironment(object $snippet, ?string $currentEnv): bool
    {
        $environments = $snippet->getEnvironments();

        if (empty($environments)) {
            return true;
        }

        if ($currentEnv === null || $currentEnv === '') {
            return true;
        }

        return in_array($currentEnv, $environments, true);
    }

    /**
     * Check if a snippet matches the current URI.
     *
     * @param Snippet|object $snippet Any object exposing getUriPatterns(): string[].
     */
    private function matchesUri(object $snippet, string $currentUri): bool
    {
        $patterns = $snippet->getUriPatterns();

        if (empty($patterns)) {
            return true;
        }

        foreach ($patterns as $pattern) {
            if ($this->uriMatchesPattern($currentUri, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match a URI against a wildcard pattern.
     */
    private function uriMatchesPattern(string $uri, string $pattern): bool
    {
        $pattern = trim($pattern);

        if ($pattern === '') {
            return false;
        }

        $uri = ltrim($uri, '/');
        $pattern = ltrim($pattern, '/');

        $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/i';

        $result = preg_match($regex, $uri);
        if ($result === false) {
            Craft::warning(
                "Code Snippets: Invalid URI pattern '{$pattern}' (PCRE error: " . preg_last_error_msg() . ')',
                'code-snippets'
            );
            return false;
        }

        return $result === 1;
    }
}
