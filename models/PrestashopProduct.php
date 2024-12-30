<?php

namespace app\models;

use yii\base\Model;

class PrestashopProduct extends Model
{
    public $url;
    public $api_key;
    public $type;
    public $variation_type;
    public $ref;

    public function rules()
    {
        return [
            [['url', 'api_key', 'ref','type'], 'required'],
            [['variation_type'], 'required', 'when' => function ($mod) {
                return $mod->type === 'variation';
            }, 'whenClient' => "function (attribute, value) {
                return $('.product-type-radio:checked').val() === 'variation';
            }"],
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
            'type' => 'Type de produit',
            'variation_type' => 'Parent ou Enfant',
            'ref' => 'Référence',
        ];
    }
}
