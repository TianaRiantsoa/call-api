<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Shopify]].
 *
 * @see Shopify
 */
class ShopifyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Shopify[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Shopify|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
