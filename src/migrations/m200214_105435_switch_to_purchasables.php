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
            $query = (new Query())
                ->select(['commerce_products.defaultVariantId','elements.type'])
                ->from('{{%commerce_products}}')
                ->innerJoin('{{%elements}}','elements.id = commerce_products.defaultVariantId')
                ->where(['commerce_products.id' => $productId])
                ->one();
            if($query) {
                $this->update(
                    '{{%reviews}}',
                    [
                        'purchasableId' => $query['defaultVariantId'],
                        'purchasableType' => $query['type']
                    ],
                    [
                        'productId' => $productId
                    ]
                );
            }
        }
    }
}
