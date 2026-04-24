<?php

namespace bitpart\codesnippets\migrations;

use bitpart\codesnippets\elements\Snippet;
use craft\db\Migration;

class Install extends Migration
{
    public function safeUp(): bool
    {
        // Pre-release: drop any legacy table from earlier element-less schema.
        // Safe because the plugin has not been publicly released; no production
        // installs exist that would lose data here.
        $this->dropTableIfExists('{{%codesnippets}}');

        $this->createTable('{{%codesnippets_snippets}}', [
            'id' => $this->integer()->notNull(),
            'code' => $this->text()->notNull(),
            'position' => $this->string(20)->notNull()->defaultValue('headBegin'),
            'sortOrder' => $this->smallInteger()->notNull()->defaultValue(0),
            'environments' => $this->text()->null(),
            'uriPattern' => $this->text()->null(),
            'description' => $this->text()->null(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'PRIMARY KEY(id)',
        ]);

        $this->addForeignKey(
            null,
            '{{%codesnippets_snippets}}',
            'id',
            '{{%elements}}',
            'id',
            'CASCADE',
        );

        $this->createIndex(null, '{{%codesnippets_snippets}}', ['position']);

        return true;
    }

    public function safeDown(): bool
    {
        // Remove all Snippet element rows so the elements table doesn't carry
        // orphan references after uninstall.
        $this->delete('{{%elements}}', ['type' => Snippet::class]);
        $this->dropTableIfExists('{{%codesnippets_snippets}}');
        $this->dropTableIfExists('{{%codesnippets}}');

        return true;
    }
}
