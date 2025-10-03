<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\ShopifySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Shopify';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shopify-index">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0 text-dark">
                    <i class="fas fa-store text-warning me-2"></i>
                    <?= Html::encode($this->title) ?>
                </h1>
                <div>
                    <?= Html::a('<i class="fas fa-plus me-1"></i> Créer un client', ['create'], [
                        'class' => 'btn btn-primary btn-sm',
                        'style' => 'background-color: #f1ac16; border-color: #f1ac16; border-radius: 20px;'
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{items}\n<div class='card-footer bg-white py-3'>{summary}\n{pager}</div>",
                'tableOptions' => [
                    'class' => 'table table-hover mb-0',
                    'style' => 'margin-bottom: 0;'
                ],
                'options' => [
                    'class' => 'table-responsive'
                ],
                'headerRowOptions' => [
                    'class' => 'table-dark'
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'class' => $index % 2 == 0 ? 'table-light' : 'table-white',
                        'style' => 'cursor: pointer; transition: all 0.2s ease;'
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'url',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none;'],
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                        'value' => function ($model) {
                            return Html::a(
                                '<i class="fas fa-external-link-alt me-2 text-info"></i>' . Html::encode($model->url),
                                ['view', 'id' => $model->id],
                                [
                                    'target' => '_blank',
                                    'class' => 'text-decoration-none',
                                    'data-pjax' => 0
                                ]
                            );
                        },
                    ],
                    [
                        'attribute' => 'api_key',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none;'],
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                        'value' => function ($model) {
                            $displayKey = strlen($model->api_key) > 20 ? 
                                substr($model->api_key, 0, 10) . '...' . substr($model->api_key, -10) : 
                                $model->api_key;
                            
                            return Html::a(
                                '<i class="fas fa-key me-2 text-warning"></i>' . Html::encode($model->api_key),
                                ['view', 'id' => $model->id],
                                [
                                    'target' => '_blank',
                                    'class' => 'text-decoration-none',
                                    'title' => 'Cliquez pour voir les détails',
                                    'data-pjax' => 0
                                ]
                            );
                        },
                    ],
                    [
                        'attribute' => 'password',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none;'],
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                        'value' => function ($model) {
                            $displayPassword = strlen($model->password) > 20 ? 
                                substr($model->password, 0, 10) . '...' . substr($model->password, -10) : 
                                $model->password;
                            
                            return Html::a(
                                '<i class="fas fa-lock me-2 text-danger"></i>' . Html::encode($model->password),
                                ['view', 'id' => $model->id],
                                [
                                    'target' => '_blank',
                                    'class' => 'text-decoration-none',
                                    'title' => 'Cliquez pour voir les détails',
                                    'data-pjax' => 0
                                ]
                            );
                        },
                    ],
                    [
                        'attribute' => 'secret_key',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none;'],
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                        'value' => function ($model) {
                            $displaySecret = strlen($model->secret_key) > 20 ? 
                                substr($model->secret_key, 0, 10) . '...' . substr($model->secret_key, -10) : 
                                $model->secret_key;
                            
                            return Html::a(
                                '<i class="fas fa-shield-alt me-2 text-success"></i>' . Html::encode($model->secret_key),
                                ['view', 'id' => $model->id],
                                [
                                    'target' => '_blank',
                                    'class' => 'text-decoration-none',
                                    'title' => 'Cliquez pour voir les détails',
                                    'data-pjax' => 0
                                ]
                            );
                        },
                    ],
                    // [
                    //     'class' => 'yii\grid\ActionColumn',
                    //     'template' => '{view} {update} {delete}',
                    //     'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none; width: 120px;'],
                    //     'contentOptions' => ['class' => 'text-center', 'style' => 'vertical-align: middle;'],
                    //     'buttons' => [
                    //         'view' => function ($url, $model) {
                    //             return Html::a(
                    //                 '<i class="fas fa-eye text-primary"></i>',
                    //                 $url,
                    //                 [
                    //                     'title' => 'Voir',
                    //                     'class' => 'me-2',
                    //                     'data-pjax' => 0
                    //                 ]
                    //             );
                    //         },
                    //         'update' => function ($url, $model) {
                    //             return Html::a(
                    //                 '<i class="fas fa-edit text-success"></i>',
                    //                 $url,
                    //                 [
                    //                     'title' => 'Modifier',
                    //                     'class' => 'me-2',
                    //                     'data-pjax' => 0
                    //                 ]
                    //             );
                    //         },
                    //         'delete' => function ($url, $model) {
                    //             return Html::a(
                    //                 '<i class="fas fa-trash text-danger"></i>',
                    //                 $url,
                    //                 [
                    //                     'title' => 'Supprimer',
                    //                     'data-confirm' => 'Êtes-vous sûr de vouloir supprimer cet élément ?',
                    //                     'data-method' => 'post',
                    //                     'data-pjax' => 0
                    //                 ]
                    //             );
                    //         },
                    //     ],
                    // ],
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
            ]); ?>
            
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<style>
    .shopify-index .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .shopify-index .table-hover tbody tr:hover {
        background-color: rgba(241, 172, 22, 0.05) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .shopify-index .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .shopify-index .table td, 
    .shopify-index .table th {
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }
    
    .shopify-index .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .shopify-index .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }
    
    .shopify-index .card-footer {
        border-top: 1px solid #e9ecef !important;
    }
    
    .shopify-index .pagination .page-item.active .page-link {
        background-color: #f1ac16 !important;
        border-color: #f1ac16 !important;
        color: white !important;
    }
    
    .shopify-index .pagination .page-link:hover {
        background-color: #f1ac16 !important;
        border-color: #f1ac16 !important;
        color: white !important;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.shopify-index .card').css('opacity', '0').animate({opacity: 1}, 500);
    
    // Effet de survol sur les lignes du tableau
    $('.shopify-index .table tbody tr').hover(
        function() {
            $(this).css({
                'transform': 'translateY(-2px)',
                'box-shadow': '0 4px 12px rgba(0,0,0,0.15)'
            });
        },
        function() {
            $(this).css({
                'transform': 'translateY(0)',
                'box-shadow': 'none'
            });
        }
    );
    
    // Animation pour les boutons d'action
    $('.shopify-index .action-column a').hover(
        function() {
            $(this).animate({fontSize: '1.2em'}, 100);
        },
        function() {
            $(this).animate({fontSize: '1em'}, 100);
        }
    );
});
</script>