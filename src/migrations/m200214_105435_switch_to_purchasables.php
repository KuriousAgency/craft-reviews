<?php

namespace kuriousagency\reviews\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\helpers\MigrationHelper;

use craft\commerce\elements\Product;

/**
 * m200214_105435_switch_to_purchasables migration.
 */
class m200214_105435_switch_to_purchasables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%reviews}}','purchasableId',$this->integer());
        $this->addColumn('{{%reviews}}','purchasableType',$this->string(255));

        $this->createIndex(null,'{{%reviews}}','purchasableId',false);

        $this->addForeignKey(null, '{{%reviews}}',['purchasableId'], '{{%commerce_purchasables}}', ['id'],'CASCADE');

        $this->_convertExisting();
        MigrationHelper::dropForeignKeyIfExists('{{%reviews}}','productId',$this);
        MigrationHelper::dropIndexIfExists('{{%reviews}}','productId',false,$this);
        $this->dropColumn('{{%reviews}}','productId');


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m200214_105435_switch_to_purchasables cannot be reverted.\n";
        return false;
    }

    private function _convertExisting()
    {
        $productIds = (new Query())
            ->select('productId')
            ->from('{{%reviews}}')
            ->all();
        $uniqueProductIds = array_unique(array_column($productIds,'productId'));

        foreach ($uniqueProductIds as $productId) {
            $purchasable = Product::find()->id($productId)->anyStatus()->one()->defaultVariant;
            $this->update(
                '{{%reviews}}',
                [
                    'purchasableId' => $purchasable->id,
                    'purchasableType' => get_class($purchasable)
                ],
                [
                    'productId' => $productId
                ]
            );
        }
    }
}
