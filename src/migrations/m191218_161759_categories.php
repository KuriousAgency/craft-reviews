<?php

namespace kuriousagency\reviews\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\commerce\Plugin as Commerce;

/**
 * m191218_161759_categories migration.
 */
class m191218_161759_categories extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%reviews}}','categoryId',$this->integer());
        $this->createIndex(null,'{{%reviews}}', 'categoryId', false);
        $this->addForeignKey(null,'{{%reviews}}',['categoryId'],'{{%categories}}',['id'],'CASCADE');

        $reviews = (new Query())
            ->select(['id','productId'])
            ->from('{{%reviews}}')
            ->where(['not',['productId'=>null]])
            ->all();
        foreach ($reviews as $review) {
            $product = Commerce::getInstance()->getProducts()->getProductById($review['productId']);
            if ($product && count($product->productCategories->all())) {
                $categories = $product->productCategories->all();
                $categoryId = end($categories)->id;
                $this->update('{{%reviews}}', ['categoryId' => $categoryId], ['id'=>$review['id']]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%reviews}}','categoryId');
    }
}
