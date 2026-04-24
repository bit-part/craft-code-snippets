<?php

namespace bitpart\codesnippets;

use bitpart\codesnippets\elements\Snippet;
use bitpart\codesnippets\models\Settings;
use bitpart\codesnippets\services\SnippetService;
use bitpart\codesnippets\twig\CraftVariableBehavior;
use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\events\TemplateEvent;
use craft\services\Elements;
use craft\services\UserPermissions;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;

/**
 * Code Snippets plugin for Craft CMS 5.
 *
 * Manage and inject code snippets (GTM, Analytics, ad tags, etc.)
 * into your site from the control panel.
 *
 * @property-read SnippetService $snippets
 */
class CodeSnippets extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'snippets' => SnippetService::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        $this->registerElementType();
        $this->registerCpUrlRules();
        $this->registerTwigBehavior();
        $this->registerAutoInjection();
        $this->registerPermissions();
    }

    public function getCpNavItem(): ?array
    {
        // Hide nav item from users without view permission
        if (!Craft::$app->getUser()->checkPermission('codeSnippets-viewSnippets')) {
            return null;
        }

        $item = parent::getCpNavItem();

        if ($item === null) {
            return null;
        }

        $item['label'] = Craft::t('code-snippets', 'Code Snippets');

        return $item;
    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('code-snippets/_settings', [
            'settings' => $this->getSettings(),
        ]);
    }

    // --- Event registration (split for SRP) ---

    private function registerElementType(): void
    {
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Snippet::class;
            }
        );
    }

    private function registerCpUrlRules(): void
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['code-snippets'] = ['template' => 'code-snippets/_index'];
                $event->rules['code-snippets/new'] = 'code-snippets/snippet/edit';
                $event->rules['code-snippets/<snippetId:\d+>'] = 'code-snippets/snippet/edit';
            }
        );
    }

    private function registerTwigBehavior(): void
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_DEFINE_BEHAVIORS,
            function (DefineBehaviorsEvent $event) {
                $event->behaviors['codeSnippets'] = CraftVariableBehavior::class;
            }
        );
    }

    private function registerAutoInjection(): void
    {
        Event::on(
            View::class,
            View::EVENT_AFTER_RENDER_PAGE_TEMPLATE,
            function (TemplateEvent $event) {
                if (!$this->shouldAutoInject($event)) {
                    return;
                }
                $this->injectSnippets($event);
            }
        );
    }

    private function registerPermissions(): void
    {
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function (RegisterUserPermissionsEvent $event) {
                $event->permissions[] = [
                    'heading' => Craft::t('code-snippets', 'Code Snippets'),
                    'permissions' => [
                        'codeSnippets-viewSnippets' => [
                            'label' => Craft::t('code-snippets', 'View snippets'),
                            'nested' => [
                                'codeSnippets-manageSnippets' => [
                                    'label' => Craft::t('code-snippets', 'Manage snippets (allows injecting arbitrary code into the site)'),
                                ],
                            ],
                        ],
                    ],
                ];
            }
        );
    }

    /**
     * Determine if auto-injection should run for this request.
     */
    private function shouldAutoInject(TemplateEvent $event): bool
    {
        $settings = $this->getSettings();
        if (!$settings->autoInject) {
            return false;
        }

        if ($event->templateMode !== View::TEMPLATE_MODE_SITE) {
            return false;
        }

        if (Craft::$app instanceof ConsoleApplication) {
            return false;
        }

        if (stripos($event->output, '</head>') === false && stripos($event->output, '</body>') === false) {
            return false;
        }

        return true;
    }

    /**
     * Inject active snippets into the rendered HTML output.
     *
     * Iterates over each position defined in Snippet::POSITION_META and uses
     * preg_replace_callback to safely insert content, avoiding backreference
     * interpretation in the snippet code.
     */
    private function injectSnippets(TemplateEvent $event): void
    {
        $output = $event->output;

        foreach (Snippet::POSITION_META as $position => $meta) {
            $content = $this->snippets->renderSnippetsForPosition($position);
            if ($content === '') {
                continue;
            }

            $output = $this->injectAtTag(
                $output,
                $meta['tagPattern'],
                $content,
                $meta['insertBefore'],
                $position
            );
        }

        $event->output = $output;
    }

    /**
     * Insert content before or after the first match of $tagPattern in $output.
     * Uses preg_replace_callback throughout so user-provided snippet content
     * is never interpreted as regex backreferences.
     */
    private function injectAtTag(
        string $output,
        string $tagPattern,
        string $content,
        bool $insertBefore,
        string $position,
    ): string {
        if (!preg_match($tagPattern, $output)) {
            return $output;
        }

        $result = preg_replace_callback(
            $tagPattern,
            fn($matches) => $insertBefore
                ? $content . "\n" . $matches[0]
                : $matches[0] . "\n" . $content,
            $output,
            1
        );

        if ($result === null) {
            Craft::warning(
                "Code Snippets: Injection failed for position '{$position}' (PCRE error: " . preg_last_error_msg() . ')',
                'code-snippets'
            );
            return $output;
        }

        return $result;
    }
}
