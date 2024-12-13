<?php

namespace app\models;

use yii\base\Model;

class ShopifyProduct extends Model
{
    public $url;
    public $api_key;
    public $password;
    public $secret_key;
    public $type;
    public $ref;

    public function rules()
    {
        return [
            [['url', 'api_key', 'password', 'secret_key', 'ref', 'type'], 'required'],
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
            'type' => 'Type de produit',
            'ref' => 'Référence',
        ];
    }
}
