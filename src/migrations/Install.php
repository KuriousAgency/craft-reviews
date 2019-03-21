<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\migrations;

use kuriousagency\reviews\Reviews;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class Install extends Migration
{
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

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%reviews}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%reviews}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer(),
					'feedback' => $this->string(),
					'reply' => $this->string(),
					'rating' => $this->integer(),
					'email' => $this->string(255),
					'firstName' => $this->string(255),
					'lastName' => $this->string(255),
					'enabled' => $this->boolean()->notNull()->defaultValue(false),
					'customerId' => $this->integer(),
					'productId' => $this->integer(),
					'orderId' => $this->integer(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
		$this->createIndex(null, '{{%reviews}}', 'rating', false);
		$this->createIndex(null, '{{%reviews}}', 'productId', false);
		$this->createIndex(null, '{{%reviews}}', 'orderId', false);
		$this->createIndex(null, '{{%reviews}}', 'customerId', false);
		
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(null, '{{%reviews}}', ['id'], '{{%elements}}', ['id'], 'CASCADE');
		$this->addForeignKey(null, '{{%reviews}}', ['productId'], '{{%commerce_products}}', ['id'], 'CASCADE');
		$this->addForeignKey(null, '{{%reviews}}', ['orderId'], '{{%commerce_orders}}', ['id']);
		$this->addForeignKey(null, '{{%reviews}}', ['customerId'], '{{%commerce_customers}}', ['id']);
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
        $this->dropTableIfExists('{{%reviews}}');
    }
}
