<?php

namespace bitpart\codesnippets\controllers;

use bitpart\codesnippets\elements\Snippet;
use Craft;
use craft\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SnippetController extends Controller
{
    /**
     * Display the create/edit form.
     */
    public function actionEdit(?int $snippetId = null): Response
    {
        $this->requireCpRequest();
        $this->requirePermission('codeSnippets-manageSnippets');

        if ($snippetId !== null) {
            $snippet = Snippet::findOne($snippetId);
            if ($snippet === null) {
                throw new NotFoundHttpException(Craft::t('code-snippets', 'Snippet not found.'));
            }
            $title = Craft::t('code-snippets', 'Edit Snippet');
        } else {
            $snippet = new Snippet();
            $snippet->enabled = true;
            $title = Craft::t('code-snippets', 'New Snippet');
        }

        return $this->renderTemplate('code-snippets/_edit', [
            'snippet' => $snippet,
            'title' => $title,
            'positions' => Snippet::getPositionOptions(),
            'environments' => self::getEnvironmentOptions(),
        ]);
    }

    /**
     * Save a snippet (create or update).
     */
    public function actionSave(): ?Response
    {
        $this->requirePostRequest();
        $this->requireCpRequest();
        $this->requirePermission('codeSnippets-manageSnippets');

        $request = Craft::$app->getRequest();
        $snippetId = $request->getBodyParam('snippetId');

        if ($snippetId) {
            $snippet = Snippet::findOne((int) $snippetId);
            if ($snippet === null) {
                throw new NotFoundHttpException(Craft::t('code-snippets', 'Snippet not found.'));
            }
        } else {
            $snippet = new Snippet();
        }

        $snippet->title = $request->getBodyParam('title', '');
        $snippet->code = $request->getBodyParam('code', '');
        $snippet->position = $request->getBodyParam('position', Snippet::POSITION_HEAD_BEGIN);
        $snippet->description = $request->getBodyParam('description') ?: null;
        $snippet->sortOrder = (int) $request->getBodyParam('sortOrder', 0);
        $snippet->uriPattern = $request->getBodyParam('uriPattern') ?: null;
        $snippet->enabled = (bool) $request->getBodyParam('enabled', true);
        $snippet->environments = $this->parseEnvironments($request->getBodyParam('environments'));

        if (!Craft::$app->getElements()->saveElement($snippet)) {
            // Log only error keys (not values) to avoid leaking snippet code into log streams.
            Craft::error(
                'Code Snippets: Failed to save snippet (errors on: '
                . implode(', ', array_keys($snippet->getErrors())) . ')',
                'code-snippets'
            );

            return $this->asModelFailure(
                $snippet,
                Craft::t('code-snippets', 'Could not save the snippet.'),
                'snippet',
                // $data: kept minimal so JSON responses don't leak option lists
                [],
                // $routeParams: extra variables passed to the re-rendered template
                [
                    'title' => $snippet->id
                        ? Craft::t('code-snippets', 'Edit Snippet')
                        : Craft::t('code-snippets', 'New Snippet'),
                    'positions' => Snippet::getPositionOptions(),
                    'environments' => self::getEnvironmentOptions(),
                ]
            );
        }

        return $this->asModelSuccess(
            $snippet,
            Craft::t('code-snippets', 'Snippet saved.'),
            'snippet',
            ['cpEditUrl' => $snippet->getCpEditUrl()],
            $snippet->getPostEditUrl()
        );
    }

    /**
     * Parse and whitelist environments from request input.
     * Only single-level string arrays are accepted; only DEFAULT_ENVIRONMENTS values pass through.
     */
    private function parseEnvironments(mixed $raw): ?string
    {
        if (!is_array($raw)) {
            return null;
        }

        // Only accept top-level string values. Multi-dimensional input (which the
        // CP UI never produces) is dropped to avoid wasted CPU on malicious POSTs.
        $flat = array_filter($raw, fn($v) => is_string($v) && $v !== '');

        $allowed = array_values(array_unique(array_filter(
            $flat,
            fn(string $v) => in_array($v, Snippet::DEFAULT_ENVIRONMENTS, true)
        )));

        return empty($allowed) ? null : json_encode($allowed);
    }

    /**
     * Build environment checkbox options from element constants.
     */
    private static function getEnvironmentOptions(): array
    {
        return array_map(
            fn(string $env) => ['label' => $env, 'value' => $env],
            Snippet::DEFAULT_ENVIRONMENTS
        );
    }

}
