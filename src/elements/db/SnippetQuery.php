<?php

namespace bitpart\codesnippets\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class SnippetQuery extends ElementQuery
{
    public ?string $position = null;

    public function position(?string $value): self
    {
        $this->position = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('codesnippets_snippets');

        // Use addSelect (not select) to preserve core element columns
        // selected by the parent ElementQuery.
        $this->query->addSelect([
            'codesnippets_snippets.code',
            'codesnippets_snippets.position',
            'codesnippets_snippets.sortOrder',
            'codesnippets_snippets.environments',
            'codesnippets_snippets.uriPattern',
            'codesnippets_snippets.description',
        ]);

        if ($this->position !== null) {
            $this->subQuery->andWhere(Db::parseParam('codesnippets_snippets.position', $this->position));
        }

        return parent::beforePrepare();
    }
}
