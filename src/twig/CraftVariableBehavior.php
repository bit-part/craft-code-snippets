<?php

namespace bitpart\codesnippets\twig;

use bitpart\codesnippets\CodeSnippets;
use bitpart\codesnippets\elements\Snippet;
use craft\helpers\Template;
use Twig\Markup;
use yii\base\Behavior;

/**
 * Adds craft.codeSnippets Twig functions.
 *
 * Usage:
 *   {{ craft.codeSnippets.headBegin() }}
 *   {{ craft.codeSnippets.headEnd() }}
 *   {{ craft.codeSnippets.bodyBegin() }}
 *   {{ craft.codeSnippets.bodyEnd() }}
 */
class CraftVariableBehavior extends Behavior
{
    public function headBegin(): Markup
    {
        return $this->render(Snippet::POSITION_HEAD_BEGIN);
    }

    public function headEnd(): Markup
    {
        return $this->render(Snippet::POSITION_HEAD_END);
    }

    public function bodyBegin(): Markup
    {
        return $this->render(Snippet::POSITION_BODY_BEGIN);
    }

    public function bodyEnd(): Markup
    {
        return $this->render(Snippet::POSITION_BODY_END);
    }

    private function render(string $position): Markup
    {
        return Template::raw(
            CodeSnippets::getInstance()->snippets->renderSnippetsForPosition($position)
        );
    }
}
