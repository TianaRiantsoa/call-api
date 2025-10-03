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
    public $language;

    public function rules()
    {
        return [
            [['url', 'api_key', 'ref', 'type'], 'required'],
            [['variation_type'], 'required', 'when' => function ($model) {
                return $model->type === 'variation';
            }, 'whenClient' => "function (attribute, value) {
                return $('[name=\"PrestashopProduct[type]\"]:checked').val() === 'variation';
            }"],
            [['variation_type'], 'default', 'value' => ''], // Valeur par défaut vide
            [['language'], 'safe'],
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
            'language' => 'Langue',
        ];
    }
}