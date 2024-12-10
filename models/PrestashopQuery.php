<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Prestashop]].
 *
 * @see Prestashop
 */
class PrestashopQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Prestashop[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Prestashop|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
