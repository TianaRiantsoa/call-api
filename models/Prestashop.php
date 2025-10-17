<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prestashop".
 *
 * @property int $id
 * @property string $url
 * @property string $api_key
 */
class Prestashop extends \yii\db\ActiveRecord
{
    // // Déclare les propriétés dynamiques
    // public $config;
    // public $erp;
    // public $type;
    // public $serial_id;
    // public $slug;
    // public $client;
    // public $ctsage;
    public $language;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prestashop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'api_key'], 'required'],
            [['url', 'api_key'], 'string', 'max' => 255],
            [['language'], 'safe'], // "safe" pour les propriétés non liées à la base de données
            // [['language'], 'required'],
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
            'config' => 'Configuration',
            'erp' => 'ERP',
            'type' => 'Type',
            'serial_id' => 'Serial ID',
            'slug' => 'Slug',
            'client' => 'Client',
            'ctsage' => 'Code Tiers Sage',
            'language' => 'Langue',
        ];
    }

    /**
     * {@inheritdoc}
     * @return PrestashopQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PrestashopQuery(get_called_class());
    }
}
