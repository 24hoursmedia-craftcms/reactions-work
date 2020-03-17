<?php

namespace twentyfourhoursmedia\reactionswork\migrations;

use Craft;
use craft\db\Migration;
use twentyfourhoursmedia\reactionswork\helpers\traits\AttributeNamesGeneratingTrait;
use twentyfourhoursmedia\reactionswork\records\Recording;

/**
 * m200316_145043_add_custom_fields migration.
 */
class m200316_145043_add_custom_fields extends Migration
{

    use AttributeNamesGeneratingTrait;

    const NEW_REACTION_HANDLES = [
        'custom1',
        'custom2',
        'custom3',
        'custom4',
        'custom5'
    ];


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach (self::NEW_REACTION_HANDLES as $handle) {
            $this->addColumn(Recording::TABLE_NAME, $this->createCountAttrName($handle), $this->integer()->notNull()->defaultValue(0));
            $this->addColumn(Recording::TABLE_NAME, $this->createUserIdsAttrName($handle), $this->longText()->null()->comment('array of user ids that voted ' . $handle));
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        foreach (self::NEW_REACTION_HANDLES as $handle) {
            $this->dropColumn(Recording::TABLE_NAME, $this->createCountAttrName($handle));
            $this->dropColumn(Recording::TABLE_NAME, $this->createUserIdsAttrName($handle));
        }
    }
}
