<?php

namespace bitpart\codesnippets\elements;

use bitpart\codesnippets\elements\db\SnippetQuery;
use Craft;
use craft\base\Element;
use craft\elements\actions\Delete;
use craft\elements\actions\Duplicate;
use craft\elements\actions\SetStatus;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;
use craft\helpers\Db;
use craft\helpers\Html;
use craft\helpers\UrlHelper;

class Snippet extends Element
{
    // Position constants
    public const POSITION_HEAD_BEGIN = 'headBegin';
    public const POSITION_HEAD_END = 'headEnd';
    public const POSITION_BODY_BEGIN = 'bodyBegin';
    public const POSITION_BODY_END = 'bodyEnd';

    public const POSITIONS = [
        self::POSITION_HEAD_BEGIN,
        self::POSITION_HEAD_END,
        self::POSITION_BODY_BEGIN,
        self::POSITION_BODY_END,
    ];

    /**
     * Per-position metadata for HTML injection.
     *
     * @internal Used by {@see \bitpart\codesnippets\CodeSnippets::injectSnippets()}.
     *           Do not rely on this constant from outside the plugin.
     */
    public const POSITION_META = [
        self::POSITION_HEAD_BEGIN => ['tagPattern' => '/<head[^>]*>/i', 'insertBefore' => false],
        self::POSITION_HEAD_END => ['tagPattern' => '/<\/head>/i', 'insertBefore' => true],
        self::POSITION_BODY_BEGIN => ['tagPattern' => '/<body[^>]*>/i', 'insertBefore' => false],
        self::POSITION_BODY_END => ['tagPattern' => '/<\/body>/i', 'insertBefore' => true],
    ];

    public const DEFAULT_ENVIRONMENTS = ['dev', 'staging', 'production'];

    public string $code = '';
    public string $position = self::POSITION_HEAD_BEGIN;
    public int $sortOrder = 0;
    public ?string $environments = null;
    public ?string $uriPattern = null;
    public ?string $description = null;

    // --- Element Configuration ---

    public static function displayName(): string
    {
        return Craft::t('code-snippets', 'Code Snippet');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('code-snippets', 'Code Snippets');
    }

    public static function lowerDisplayName(): string
    {
        return Craft::t('code-snippets', 'code snippet');
    }

    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('code-snippets', 'code snippets');
    }

    public static function hasTitles(): bool
    {
        return true;
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

    public static function isLocalized(): bool
    {
        return false;
    }

    public static function find(): ElementQueryInterface
    {
        return new SnippetQuery(static::class);
    }

    /**
     * @inheritdoc
     * The plugin's permission grants access in addition to any explicit grant
     * via parent (which may include EVENT_AUTHORIZE_VIEW).
     */
    public function canView(User $user): bool
    {
        return parent::canView($user) || $user->can('codeSnippets-viewSnippets');
    }

    /**
     * @inheritdoc
     * Note: holders of `codeSnippets-manageSnippets` are always allowed,
     * even if a third-party AUTHORIZE_SAVE handler returns false.
     */
    public function canSave(User $user): bool
    {
        return parent::canSave($user) || $user->can('codeSnippets-manageSnippets');
    }

    public function canDelete(User $user): bool
    {
        return parent::canDelete($user) || $user->can('codeSnippets-manageSnippets');
    }

    public function canDuplicate(User $user): bool
    {
        return parent::canDuplicate($user) || $user->can('codeSnippets-manageSnippets');
    }

    public function canCreateDrafts(User $user): bool
    {
        return false;
    }

    // --- CP Section ---

    public function cpEditUrl(): ?string
    {
        if (!$this->id) {
            return null;
        }
        return UrlHelper::cpUrl("code-snippets/{$this->id}");
    }

    public function getPostEditUrl(): ?string
    {
        return UrlHelper::cpUrl('code-snippets');
    }

    // --- Sources (sidebar) ---

    protected static function defineSources(?string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' => Craft::t('code-snippets', 'All Snippets'),
            ],
            ['heading' => Craft::t('code-snippets', 'Position')],
        ];

        foreach (self::getPositionOptions() as $position => $label) {
            $sources[] = [
                'key' => "position:{$position}",
                'label' => $label,
                'criteria' => ['position' => $position],
            ];
        }

        return $sources;
    }

    // --- Table Attributes ---

    protected static function defineTableAttributes(): array
    {
        return [
            'position' => ['label' => Craft::t('code-snippets', 'Position')],
            'environments' => ['label' => Craft::t('code-snippets', 'Environments')],
            'uriPattern' => ['label' => Craft::t('code-snippets', 'URI Pattern')],
            'sortOrder' => ['label' => Craft::t('code-snippets', 'Sort Order')],
            'description' => ['label' => Craft::t('code-snippets', 'Description')],
            'dateCreated' => ['label' => Craft::t('app', 'Date Created')],
            'dateUpdated' => ['label' => Craft::t('app', 'Date Updated')],
        ];
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return ['position', 'environments', 'uriPattern'];
    }

    /**
     * Craft 5 official method for rendering attribute HTML in the element index.
     */
    protected function attributeHtml(string $attribute): string
    {
        return match ($attribute) {
            'position' => Html::encode($this->getPositionLabel()),
            'environments' => $this->renderEnvironmentsHtml(),
            'uriPattern' => $this->renderUriPatternHtml(),
            default => parent::attributeHtml($attribute),
        };
    }

    private function renderEnvironmentsHtml(): string
    {
        $envs = $this->getEnvironments();
        return empty($envs)
            ? '<span class="light">' . Html::encode(Craft::t('code-snippets', 'All')) . '</span>'
            : Html::encode(implode(', ', $envs));
    }

    private function renderUriPatternHtml(): string
    {
        $patterns = $this->getUriPatterns();
        if (empty($patterns)) {
            return '<span class="light">' . Html::encode(Craft::t('code-snippets', 'All pages')) . '</span>';
        }
        $html = '<code class="smalltext">' . Html::encode($patterns[0]) . '</code>';
        if (count($patterns) > 1) {
            $extra = count($patterns) - 1;
            $html .= ' <span class="light" aria-label="'
                . Html::encode(Craft::t('code-snippets', '{n} more patterns', ['n' => $extra]))
                . '">+' . $extra . '</span>';
        }
        return $html;
    }

    // --- Sort Options ---

    protected static function defineSortOptions(): array
    {
        return [
            'title' => Craft::t('app', 'Title'),
            [
                'label' => Craft::t('code-snippets', 'Position'),
                'orderBy' => 'codesnippets_snippets.position',
                'attribute' => 'position',
            ],
            [
                'label' => Craft::t('code-snippets', 'Sort Order'),
                'orderBy' => 'codesnippets_snippets.sortOrder',
                'attribute' => 'sortOrder',
            ],
            'dateCreated' => Craft::t('app', 'Date Created'),
            'dateUpdated' => Craft::t('app', 'Date Updated'),
        ];
    }

    // --- Search ---

    protected static function defineSearchableAttributes(): array
    {
        return ['description', 'code'];
    }

    // --- Actions ---

    protected static function defineActions(?string $source = null): array
    {
        return [
            SetStatus::class,
            Duplicate::class,
            Delete::class,
        ];
    }

    // --- Validation ---

    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['code', 'position'], 'required'];
        $rules[] = [['code'], 'string', 'max' => 65535];
        $rules[] = [['description'], 'string', 'max' => 2000];
        $rules[] = [['uriPattern'], 'string', 'max' => 5000];
        $rules[] = [['position'], 'in', 'range' => self::POSITIONS];
        // UI restricts to 0+ for clarity; SMALLINT max = 32767
        $rules[] = [['sortOrder'], 'integer', 'min' => 0, 'max' => 32767];
        return $rules;
    }

    // --- Save ---

    /**
     * @inheritdoc
     * @throws \Throwable on persistence failure (re-raised after logging).
     */
    public function afterSave(bool $isNew): void
    {
        // Guard for future localization support; currently isLocalized() === false.
        if (!$this->propagating) {
            try {
                Db::upsert('{{%codesnippets_snippets}}', [
                    'id' => $this->id,
                    'code' => $this->code,
                    'position' => $this->position,
                    'sortOrder' => $this->sortOrder,
                    'environments' => $this->environments,
                    'uriPattern' => $this->uriPattern,
                    'description' => $this->description,
                ]);
            } catch (\Throwable $e) {
                Craft::error(
                    "Code Snippets: Failed to persist snippet record for element #{$this->id}: " . $e->getMessage(),
                    'code-snippets'
                );
                throw $e;
            }
        }

        parent::afterSave($isNew);
    }

    // --- Helper Methods ---

    /**
     * Returns the active environments for this snippet.
     *
     * @return string[] Active environment names. Empty array means "all environments".
     */
    public function getEnvironments(): array
    {
        if ($this->environments === null || $this->environments === '') {
            return [];
        }

        $decoded = json_decode($this->environments, true);
        if (!is_array($decoded)) {
            Craft::warning(
                "Code Snippets: Invalid environments JSON for snippet #{$this->id}",
                'code-snippets'
            );
            return [];
        }

        return array_values(array_filter($decoded, 'is_string'));
    }

    /**
     * Returns the URI patterns this snippet matches against.
     *
     * @return string[] One pattern per element. Empty array means "all pages".
     */
    public function getUriPatterns(): array
    {
        if ($this->uriPattern === null || $this->uriPattern === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode("\n", $this->uriPattern))));
    }

    /**
     * Returns the available position options for select fields.
     *
     * @return array<string, string> Map of position constant => translated label.
     */
    public static function getPositionOptions(): array
    {
        return [
            self::POSITION_HEAD_BEGIN => '<head> ' . Craft::t('code-snippets', 'Start'),
            self::POSITION_HEAD_END => '</head> ' . Craft::t('code-snippets', 'End'),
            self::POSITION_BODY_BEGIN => '<body> ' . Craft::t('code-snippets', 'Start'),
            self::POSITION_BODY_END => '</body> ' . Craft::t('code-snippets', 'End'),
        ];
    }

    /**
     * Returns a human-readable label for the snippet's position.
     */
    public function getPositionLabel(): string
    {
        $options = self::getPositionOptions();
        return $options[$this->position] ?? $this->position;
    }
}
