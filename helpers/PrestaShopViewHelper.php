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
            return '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Aucun produit trouvé.</div>';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $productList,
                'pagination' => ['pageSize' => 200],
            ]),
            'layout' => "{items}\n<div class='card-footer bg-white py-3'>{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover mb-0',
                'style' => 'margin-bottom: 0;'
            ],
            'options' => [
                'class' => 'table-responsive'
            ],
            'rowOptions' => function ($model, $key, $index, $grid) {
                return [
                    'class' => $index % 2 == 0 ? 'table-light' : 'table-white',
                    'style' => 'cursor: pointer; transition: all 0.2s ease;'
                ];
            },
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => '<i class="fas fa-hashtag me-2 text-white"></i>ID',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => '<i class="fas fa-tag me-2 text-white"></i>Nom',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['name'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'reference',
                    'label' => '<i class="fas fa-barcode me-2 text-white"></i>Référence',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-qrcode me-2 text-success"></i>' . $model['reference'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => '<i class="fas fa-euro-sign me-2 text-white"></i>Prix',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
            ],
            'pager' => [
                'options' => [
                    'class' => 'pagination justify-content-center mb-0',
                    'style' => 'margin-top: 0; margin-bottom: 0;'
                ],
                'linkContainerOptions' => [
                    'class' => 'page-item'
                ],
                'linkOptions' => [
                    'class' => 'page-link',
                    'style' => 'color: #5c5c5c; border-color: #dee2e6;'
                ],
                'activePageCssClass' => 'active',
                'disabledPageCssClass' => 'disabled',
                'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
            ],
        ]);
    }

    public static function renderParentProductGrid($productList, $url, $api)
    {
        if (empty($productList)) {
            return '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Aucun produit trouvé.</div>';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $productList,
                'pagination' => ['pageSize' => 200],
            ]),
            'layout' => "{items}\n<div class='card-footer bg-white py-3'>{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover mb-0',
                'style' => 'margin-bottom: 0;'
            ],
            'options' => [
                'class' => 'table-responsive'
            ],
            'rowOptions' => function ($model, $key, $index, $grid) {
                return [
                    'class' => $index % 2 == 0 ? 'table-light' : 'table-white',
                    'style' => 'cursor: pointer; transition: all 0.2s ease;'
                ];
            },
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => '<i class="fas fa-hashtag me-2 text-white"></i>ID Produit',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'reference',
                    'label' => '<i class="fas fa-barcode me-2 text-white"></i>Référence',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-qrcode me-2 text-success"></i>' . $model['reference'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => '<i class="fas fa-tag me-2 text-white"></i>Nom du produit',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['name'],
                            $url . "/api/products/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'active',
                    'label' => '<i class="fas fa-toggle-on me-2 text-white"></i>Statut',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'value' => function ($model) {
                        $status = isset($model['active']) && $model['active'] ? 'Actif' : 'Inactif';
                        $icon = isset($model['active']) && $model['active'] ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger';
                        return Html::decode('<i class="fas ' . $icon . ' me-2"></i>' . $status);
                    },
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => '<i class="fas fa-euro-sign me-2 text-white"></i>Prix',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'quantity',
                    'label' => '<i class="fas fa-boxes me-2 text-white"></i>Total en stock',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'date_add',
                    'value' => function ($model) {
                        $date = is_array($model) ? $model['date_add'] : $model->date_add;
                        return '<i class="fas fa-calendar-plus me-2 text-primary"></i>' . Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
                    },
                    'label' => '<i class="fas fa-calendar-plus me-2 text-white"></i>Création',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'date_upd',
                    'value' => function ($model) {
                        $date = is_array($model) ? $model['date_upd'] : $model->date_upd;
                        return '<i class="fas fa-calendar-check me-2 text-info"></i>' . Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
                    },
                    'label' => '<i class="fas fa-calendar-check me-2 text-white"></i>Mise à jour',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
            ],
            'pager' => [
                'options' => [
                    'class' => 'pagination justify-content-center mb-0',
                    'style' => 'margin-top: 0; margin-bottom: 0;'
                ],
                'linkContainerOptions' => [
                    'class' => 'page-item'
                ],
                'linkOptions' => [
                    'class' => 'page-link',
                    'style' => 'color: #5c5c5c; border-color: #dee2e6;'
                ],
                'activePageCssClass' => 'active',
                'disabledPageCssClass' => 'disabled',
                'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
            ],
        ]);
    }

    public static function renderCombinationsGrid($combinationList, $url, $api, $db_id)
    {
        if (empty($combinationList)) {
            return '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Aucune déclinaison trouvée.</div>';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $combinationList,
                'pagination' => ['pageSize' => 200],
            ]),
            'layout' => "{items}\n<div class='card-footer bg-white py-3'>{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover mb-0',
                'style' => 'margin-bottom: 0;'
            ],
            'options' => [
                'class' => 'table-responsive'
            ],
            'rowOptions' => function ($model, $key, $index, $grid) {
                return [
                    'class' => $index % 2 == 0 ? 'table-light' : 'table-white',
                    'style' => 'cursor: pointer; transition: all 0.2s ease;'
                ];
            },
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => '<i class="fas fa-hashtag me-2 text-white"></i>ID',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'reference',
                    'label' => '<i class="fas fa-barcode me-2 text-white"></i>Référence de la déclinaison',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api, $db_id) {
                        return Html::a(
                            '<i class="fas fa-qrcode me-2 text-success"></i>' . $model['reference'],
                            '?id=' . $db_id . '&ref=' . $model['reference'] . '&type=variation&variation_type=child',
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => '<i class="fas fa-euro-sign me-2 text-white"></i>Prix',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'quantity',
                    'label' => '<i class="fas fa-box me-2 text-white"></i>Quantité en stock',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
            ],
            'pager' => [
                'options' => [
                    'class' => 'pagination justify-content-center mb-0',
                    'style' => 'margin-top: 0; margin-bottom: 0;'
                ],
                'linkContainerOptions' => [
                    'class' => 'page-item'
                ],
                'linkOptions' => [
                    'class' => 'page-link',
                    'style' => 'color: #5c5c5c; border-color: #dee2e6;'
                ],
                'activePageCssClass' => 'active',
                'disabledPageCssClass' => 'disabled',
                'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
            ],
        ]);
    }

    public static function renderChildCombinationGrid($combinationList, $url, $api, $db_id)
    {
        if (empty($combinationList)) {
            return '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Aucune combinaison trouvée.</div>';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $combinationList,
                'pagination' => ['pageSize' => 10],
            ]),
            'layout' => "{items}\n<div class='card-footer bg-white py-3'>{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover mb-0',
                'style' => 'margin-bottom: 0;'
            ],
            'options' => [
                'class' => 'table-responsive'
            ],
            'rowOptions' => function ($model, $key, $index, $grid) {
                return [
                    'class' => $index % 2 == 0 ? 'table-light' : 'table-white',
                    'style' => 'cursor: pointer; transition: all 0.2s ease;'
                ];
            },
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => '<i class="fas fa-hashtag me-2 text-white"></i>ID',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id'],
                            $url . "/api/combinations/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'parent_reference',
                    'label' => '<i class="fas fa-link me-2 text-white"></i>Référence Parente',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api, $db_id) {
                        return Html::a(
                            '<i class="fas fa-qrcode me-2 text-success"></i>' . $model['parent_reference'],
                            '?id=' . $db_id . '&ref=' . $model['parent_reference'] . '&type=variation&variation_type=parent',
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => '<i class="fas fa-tag me-2 text-white"></i>Nom du produit',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'reference',
                    'label' => '<i class="fas fa-barcode me-2 text-white"></i>Référence',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-qrcode me-2 text-success"></i>' . $model['reference'],
                            $url . "/api/combinations/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'quantity',
                    'label' => '<i class="fas fa-box me-2 text-white"></i>Quantité',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => '<i class="fas fa-euro-sign me-2 text-white"></i>Prix',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'option_values',
                    'label' => '<i class="fas fa-exchange-alt me-2 text-white"></i>Déclinaison',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
            ],
            'pager' => [
                'options' => [
                    'class' => 'pagination justify-content-center mb-0',
                    'style' => 'margin-top: 0; margin-bottom: 0;'
                ],
                'linkContainerOptions' => [
                    'class' => 'page-item'
                ],
                'linkOptions' => [
                    'class' => 'page-link',
                    'style' => 'color: #5c5c5c; border-color: #dee2e6;'
                ],
                'activePageCssClass' => 'active',
                'disabledPageCssClass' => 'disabled',
                'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
            ],
        ]);
    }

    public static function renderSpecificPricesGrid($tarifList, $url, $api)
    {
        if (empty($tarifList)) {
            return '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Ce produit ne possède pas de tarifs spécifiques.</div>';
        }

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $tarifList,
                'pagination' => ['pageSize' => 1000],
            ]),
            'layout' => "{items}\n<div class='card-footer bg-white py-3'>{summary}\n{pager}</div>",
            'tableOptions' => [
                'class' => 'table table-hover mb-0',
                'style' => 'margin-bottom: 0;'
            ],
            'options' => [
                'class' => 'table-responsive'
            ],
            'rowOptions' => function ($model, $key, $index, $grid) {
                return [
                    'class' => $index % 2 == 0 ? 'table-light' : 'table-white',
                    'style' => 'cursor: pointer; transition: all 0.2s ease;'
                ];
            },
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => '<i class="fas fa-hashtag me-2 text-white"></i>ID',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id'],
                            $url . "/api/specific_prices/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'id_product',
                    'label' => '<i class="fas fa-box me-2 text-white"></i>ID du produit',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id_product'],
                            $url . "/api/products/{$model['id_product']}?ws_key=" . $api,
                            ['target' => '_blank', 'class' => 'text-decoration-none']
                        );
                    }
                ],
                [
                    'attribute' => 'id_product_attribute',
                    'label' => '<i class="fas fa-cube me-2 text-white"></i>ID de la déclinaison',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        if ($model['id_product_attribute'] !== 0) {
                            return Html::a(
                                '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id_product_attribute'],
                                $url . "/api/combinations/{$model['id_product_attribute']}?ws_key=" . $api,
                                ['target' => '_blank', 'class' => 'text-decoration-none']
                            );
                        }
                        return '<span class="text-muted">-</span>';
                    }
                ],
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
                    },
                    'label' => '<i class="fas fa-euro-sign me-2 text-white"></i>Prix',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'difference_amount',
                    'label' => '<i class="fas fa-exchange-alt me-2 text-white"></i>Différence €',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) {
                        $value = is_numeric($model['difference_amount'])
                            ? Yii::$app->formatter->asCurrency($model['difference_amount'], 'EUR')
                            : $model['difference_amount'];
                        $icon = is_numeric($model['difference_amount']) && $model['difference_amount'] >= 0 
                            ? 'fa-arrow-up text-success' 
                            : 'fa-arrow-down text-danger';
                        return '<i class="fas ' . $icon . ' me-2"></i><span style="color: ' . (is_numeric($model['difference_amount']) && $model['difference_amount'] >= 0 ? 'green' : 'red') . ';">' . $value . '</span>';
                    },
                ],
                [
                    'attribute' => 'id_group',
                    'label' => '<i class="fas fa-users me-2 text-white"></i>Groupe de client',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        if ($model['id_group'] !== 0) {
                            return Html::a(
                                '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id_group'],
                                $url . "/api/groups/{$model['group_id']}?ws_key=" . $api,
                                ['target' => '_blank', 'class' => 'text-decoration-none']
                            );
                        }
                        return '<span class="text-muted">-</span>';
                    }
                ],
                [
                    'attribute' => 'id_customer',
                    'label' => '<i class="fas fa-user me-2 text-white"></i>Client',
                    'format' => 'raw',
                    'encodeLabel' => false,
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) use ($url, $api) {
                        if ($model['id_customer'] !== 0) {
                            return Html::a(
                                '<i class="fas fa-external-link-alt me-2 text-info"></i>' . $model['id_customer'],
                                $url . "/api/customers/{$model['id_customer']}?ws_key=" . $api,
                                ['target' => '_blank', 'class' => 'text-decoration-none']
                            );
                        }
                        return '<span class="text-muted">-</span>';
                    }
                ],
                [
                    'attribute' => 'from',
                    'label' => '<i class="fas fa-calendar-alt me-2 text-white"></i>De',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
                [
                    'attribute' => 'to',
                    'label' => '<i class="fas fa-calendar-check me-2 text-white"></i>À',
                    'encodeLabel' => false,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'text-white',
                        'style' => 'background: linear-gradient(145deg, #f1ac16, #e69500) !important; border: none !important; color: white !important; font-weight: 600 !important; text-transform: uppercase !important; font-size: 0.85rem !important; letter-spacing: 0.5px !important; padding: 1rem 1.25rem !important;'
                    ],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                ],
            ],
            'pager' => [
                'options' => [
                    'class' => 'pagination justify-content-center mb-0',
                    'style' => 'margin-top: 0; margin-bottom: 0;'
                ],
                'linkContainerOptions' => [
                    'class' => 'page-item'
                ],
                'linkOptions' => [
                    'class' => 'page-link',
                    'style' => 'color: #5c5c5c; border-color: #dee2e6;'
                ],
                'activePageCssClass' => 'active',
                'disabledPageCssClass' => 'disabled',
                'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
            ],
        ]);
    }
}