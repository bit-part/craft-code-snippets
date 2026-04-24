<?php

namespace bitpart\codesnippets\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * Whether to automatically inject snippets into rendered HTML.
     * When false, use Twig functions: {{ craft.codeSnippets.head() }}, etc.
     */
    public bool $autoInject = true;

    public function defineRules(): array
    {
        return [
            [['autoInject'], 'boolean'],
        ];
    }
}
