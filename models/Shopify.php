<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shopify".
 *
 * @property int $id
 * @property string $url
 * @property string $api_key
 * @property string $password
 * @property string $secret_key
 */
class Shopify extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shopify';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'api_key', 'password', 'secret_key'], 'required'],
            [['url', 'api_key', 'password', 'secret_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'URL',
            'api_key' => 'Clé API',
            'password' => 'Mot de passe API',
            'secret_key' => 'Clé Secrète',
        ];
    }

    /**
     * {@inheritdoc}
     * @return ShopifyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ShopifyQuery(get_called_class());
    }
}
