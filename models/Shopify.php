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
    // Déclare les propriétés dynamiques
    public $config;
    public $erp;
    public $type;
    public $serial_id;
    public $slug;
    public $client;
    public $ctsage;

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
            [['config', 'erp', 'type', 'serial_id', 'slug', 'client', 'ctsage'], 'safe'], // "safe" pour les propriétés non liées à la base de données
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
            'config' => 'Configuration',
            'erp' => 'ERP',
            'type' => 'Type',
            'serial_id' => 'Serial ID',
            'slug' => 'Slug',
            'client' => 'Client',
            'ctsage' => 'Code Tiers Sage',
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
