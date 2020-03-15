<?php
/**
 * Reactions Work plugin for Craft CMS 3.x
 *
 * Add Facebook style likes, angry and other reactions to your site
 *
 * @link      https://en.24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 */

namespace twentyfourhoursmedia\reactionswork\migrations;

use twentyfourhoursmedia\reactionswork\helpers\traits\AttributeNamesGeneratingTrait;
use twentyfourhoursmedia\reactionswork\ReactionsWork;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use twentyfourhoursmedia\reactionswork\records\Recording;
use twentyfourhoursmedia\reactionswork\services\ReactionsWorkService;

/**
 * @author    info@24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0
 */
class Install extends Migration
{

    use AttributeNamesGeneratingTrait;

    /**
     * Keep migrations and model in sync
     * @see ReactionsTrait
     */
    const REACTION_HANDLES = [
        'like',
        'love',
        'haha',
        'wow',
        'sad',
        'angry'
    ];

    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(Recording::TABLE_NAME);
        if ($tableSchema === null) {
            $tablesCreated = true;

            $columns = [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer()->notNull(),
                'elementId' => $this->integer()->notNull(),
                //'some_field' => $this->string(255)->notNull()->defaultValue(''),
            ];
            foreach (self::REACTION_HANDLES as $handle) {
                $columns[$this->createCountAttrName($handle)] = $this->integer()->defaultValue(0)->notNull();
                $columns[$this->createUserIdsAttrName($handle)] = $this->longText()->null()->comment('array of user ids that voted ' . $handle);
            }
            $columns[$this->createCountAttrName('all')] = $this->integer()->defaultValue(0)->notNull();
            $columns[$this->createUserIdsAttrName('all')] = $this->longText()->null()->comment('array of all user ids who voted all');

            $this->createTable(
                Recording::TABLE_NAME,
                $columns
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
//        $this->createIndex(
//            $this->db->getIndexName(
//                Recording::TABLE_NAME,
//                'some_field',
//                true
//            ),
//            Recording::TABLE_NAME,
//            'some_field',
//            true
//        );
        $this->createIndex(
            $this->db->getIndexName(
                Recording::TABLE_NAME,
                ['elementId', 'siteId'],
                true
            ),
            Recording::TABLE_NAME,
            ['elementId', 'siteId'],
            true
        );

        $this->createIndex(
            $this->db->getIndexName(
                Recording::TABLE_NAME,
                [$this->createCountAttrName('all')],
                false
            ),
            Recording::TABLE_NAME,
            [$this->createCountAttrName('all')],
            true
        );

        // Additional commands depending on the db driver
//        switch ($this->driver) {
//            case DbConfig::DRIVER_MYSQL:
//                break;
//            case DbConfig::DRIVER_PGSQL:
//                break;
//        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName(Recording::TABLE_NAME, 'siteId'),
            Recording::TABLE_NAME,
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            $this->db->getForeignKeyName(Recording::TABLE_NAME, 'elementId'),
            Recording::TABLE_NAME,
            'elementId',
            '{{%elements}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists(Recording::TABLE_NAME);
    }
}
