<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Woocommerce]].
 *
 * @see Woocommerce
 */
class WoocommerceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Woocommerce[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Woocommerce|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
