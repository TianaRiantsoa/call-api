<?php

namespace app\models;

use yii\base\Model;

class PrestashopCustomer extends Model 
{
    public $url;
    public $api_key;
    public $ref;

    public function rules()
    {
        return [
            [['url', 'api_key', 'ref'], 'required'],
            ['ref','email'],
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
            'ref' => 'Adresse email',
        ];
    }

}
