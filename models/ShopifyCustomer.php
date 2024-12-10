<?php

namespace app\models;

use yii\base\Model;

class ShopifyCustomer extends Model
{
    public $url;
    public $api_key;
    public $password;
    public $secret_key;
    public $ref;

    public function rules()
    {
        return [
            [['url', 'api_key', 'password', 'secret_key', 'ref'], 'required'],
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
            'ref' => 'Adresse email',
        ];
    }
}
