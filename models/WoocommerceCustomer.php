<?php

namespace app\models;

use yii\base\Model;

class WoocommerceCustomer extends Model
{
    public $url;
    public $api_key;
    public $password;
    public $secret_key;
    public $ref;

    public function rules()
    {
        return [
            [['url', 'consumer_key', 'consumer_secret', 'ref'], 'required'],
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
            'consumer_key' => 'Clé Client',
            'consumer_secret' => 'Clé Secrète',
            'ref' => 'Adresse email',
        ];
    }
}
