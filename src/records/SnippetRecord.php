<?php

namespace bitpart\codesnippets\records;

use craft\db\ActiveRecord;

/**
 * @property int $id
 * @property string $code
 * @property string $position
 * @property int $sortOrder
 * @property string|null $environments
 * @property string|null $uriPattern
 * @property string|null $description
 * @property string $dateCreated
 * @property string $dateUpdated
 */
class SnippetRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%codesnippets_snippets}}';
    }
}
