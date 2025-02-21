<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "woocommerce".
 *
 * @property int $id
 * @property string $url
 * @property string $consumer_key
 * @property string $consumer_secret
 */
class Woocommerce extends \yii\db\ActiveRecord
{
    // Déclare les propriétés dynamiques
    public $config;
    public $erp;
    public $type;
    public $serial_id;
    public $slug;
    public $client;
    public $ctsage;
    public $idconfig;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'woocommerce';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'consumer_key', 'consumer_secret'], 'required'],
            [['url', 'consumer_key', 'consumer_secret'], 'string', 'max' => 255],
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
            'consumer_key' => 'Clé Client',
            'consumer_secret' => 'Clé Secrète',
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
     * @return WoocommerceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WoocommerceQuery(get_called_class());
    }
}
