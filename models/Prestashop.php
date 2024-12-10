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
