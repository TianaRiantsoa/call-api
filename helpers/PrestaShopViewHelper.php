<?php

namespace app\helpers;

use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use Yii;
/**
 * Helper class pour générer les vues
 */
class PrestaShopViewHelper
{
    public static function renderSimpleProductGrid($productList, $url, $api)
    {
        if (empty($productList)) {
            return '';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $productList,
                'pagination' => ['pageSize' => 200],
            ]),
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['id'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Nom',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['name'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'reference',
                    'label' => 'Référence',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['reference'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                // [
                //     'attribute' => 'description',
                //     'label' => 'Description',
                //     'format' => 'raw',
                // ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => 'Prix',
                ],
            ],
        ]);
    }

    public static function renderParentProductGrid($productList, $url, $api)
    {
        if (empty($productList)) {
            return '';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $productList,
                'pagination' => ['pageSize' => 200],
            ]),
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID Produit',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['id'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'reference',
                    'label' => 'Référence',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['reference'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Nom du produit',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['name'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'active',
                    'label' => 'Statut',
                    'value' => function ($model) {
                        return isset($model['active']) && $model['active'] ? 'Actif' : 'Inactif';
                    },
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => 'Prix',
                ],
                [
                    'attribute' => 'quantity',
                    'label' => 'Total en stock',
                ],
                [
                    'attribute' => 'date_add',
                    'value' => function ($model) {
                        $date = is_array($model) ? $model['date_add'] : $model->date_add;
                        return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
                    },
                    'label' => 'Création',
                ],
                [
                    'attribute' => 'date_upd',
                    'value' => function ($model) {
                        $date = is_array($model) ? $model['date_upd'] : $model->date_upd;
                        return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
                    },
                    'label' => 'Mise à jour',
                ],
            ],
        ]);
    }

    public static function renderCombinationsGrid($combinationList, $url, $api, $db_id)
    {
        if (empty($combinationList)) {
            return '';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $combinationList,
                'pagination' => ['pageSize' => 200],
            ]),
            'columns' => [
                'id',
                [
                    'attribute' => 'reference',
                    'label' => 'Référence de la déclinaison',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api, $db_id) {
                        return Html::a(
                            $model['reference'],
                            '?id=' . $db_id . '&ref=' . $model['reference'] . '&type=variation&variation_type=child',
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => 'Prix',
                ],
                [
                    'attribute' => 'quantity',
                    'label' => 'Quantité en stock',
                ],
            ],
        ]);
    }

    public static function renderChildCombinationGrid($combinationList, $url, $api, $db_id)
    {
        if (empty($combinationList)) {
            return '';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $combinationList,
                'pagination' => ['pageSize' => 10],
            ]),
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['id'],
                            $url . "/api/combinations/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'parent_reference',
                    'label' => 'Référence Parente',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api, $db_id) {
                        return Html::a(
                            $model['parent_reference'],
                            '?id=' . $db_id . '&ref=' . $model['parent_reference'] . '&type=variation&variation_type=parent',
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Nom du produit',
                ],
                [
                    'attribute' => 'reference',
                    'label' => 'Référence',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['reference'],
                            $url . "/api/combinations/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'quantity',
                    'label' => 'Quantité',
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => 'Prix',
                ],
                [
                    'attribute' => 'option_values',
                    'label' => 'Déclinaison',
                    'format' => 'raw'
                ],
            ],
        ]);
    }

    public static function renderSpecificPricesGrid($tarifList, $url, $api)
    {
        if (empty($tarifList)) {
            return '<h3>Ce produit ne possède pas des tarifs spécifiques</h3>';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $tarifList,
                'pagination' => ['pageSize' => 1000],
            ]),
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['id'],
                            $url . "/api/specific_prices/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'id_product',
                    'label' => 'ID du produit',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['id_product'],
                            $url . "/api/products/{$model['id_product']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'id_product_attribute',
                    'label' => 'ID de la déclinaison',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        if ($model['id_product_attribute'] !== 0) {
                            return Html::a(
                                $model['id_product_attribute'],
                                $url . "/api/combinations/{$model['id_product_attribute']}?ws_key=" . $api,
                                ['target' => '_blank', 'encode' => false]
                            );
                        }
                        return '';
                    }
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => 'Prix',
                ],
                [
                    'attribute' => 'difference_amount',
                    'label' => 'Différence €',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $value = is_numeric($model['difference_amount'])
                            ? Yii::$app->formatter->asCurrency($model['difference_amount'], 'EUR')
                            : $model['difference_amount'];
                        return "<span style='color: red;'>{$value}</span>";
                    },
                ],
                [
                    'attribute' => 'id_group',
                    'label' => 'Groupe de client',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        if ($model['id_group'] !== 0) {
                            return Html::a(
                                $model['id_group'],
                                $url . "/api/groups/{$model['group_id']}?ws_key=" . $api,
                                ['target' => '_blank', 'encode' => false]
                            );
                        }
                        return '';
                    }
                ],
                [
                    'attribute' => 'id_customer',
                    'label' => 'Client',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        if ($model['id_customer'] !== 0) {
                            return Html::a(
                                $model['id_customer'],
                                $url . "/api/customers/{$model['id_customer']}?ws_key=" . $api,
                                ['target' => '_blank', 'encode' => false]
                            );
                        }
                        return '';
                    }
                ],
                [
                    'attribute' => 'from',
                    'label' => 'De',
                ],
                [
                    'attribute' => 'to',
                    'label' => 'À',
                ],
            ],
        ]);
    }
}