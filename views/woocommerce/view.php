<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'WooCommerces', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="woocommerce-view">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0 text-dark">
                    <i class="fab fa-wordpress text-warning me-2"></i>
                    <?= Html::encode($this->title) ?>
                </h1>
                <div>
                    <?= Html::a('<i class="fas fa-edit me-1"></i> Mettre à jour', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-success btn-sm me-2',
                        'style' => 'border-radius: 20px;'
                    ]) ?>
                    <?= Html::a('<i class="fas fa-trash me-1"></i> Supprimer', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => 'Êtes-vous sûr de vouloir supprimer ce client ?',
                            'method' => 'post',
                        ],
                        'style' => 'border-radius: 20px;'
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'url',
                        'format' => 'raw',
                        'value' => Html::a(
                            '<i class="fas fa-external-link-alt me-2 text-info"></i>' . Html::encode($model->url),
                            'http://' . $model->url,
                            [
                                'target' => '_blank',
                                'class' => 'text-decoration-none'
                            ]
                        ),
                        'label' => '<i class="fas fa-globe me-2 text-primary"></i> URL du site',
                    ],
                    [
                        'attribute' => 'consumer_key',
                        'format' => 'raw',
                        'value' => Html::encode($model->consumer_key),
                        'label' => '<i class="fas fa-key me-2 text-warning"></i> Clé de consommateur',
                    ],
                    [
                        'attribute' => 'consumer_secret',
                        'format' => 'raw',
                        'value' => Html::encode($model->consumer_secret),
                        'label' => '<i class="fas fa-shield-alt me-2 text-danger"></i> Secret de consommateur',
                    ],
                ],
                'options' => [
                    'class' => 'table table-borderless detail-view mb-0'
                ],
                'template' => '<tr><th class="col-sm-3 text-end text-muted" style="background-color: #f8f9fa; border-radius: 5px; padding: 1rem;">{label}</th><td class="py-3" style="border-bottom: 1px solid #e9ecef;">{value}</td></tr>',
            ]) ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark text-center">
                <i class="fas fa-search me-2 text-primary"></i>
                Options de recherche disponibles
            </h2>
        </div>
        <div class="card-body text-center">
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <?= Html::a(
                        '<i class="fas fa-box-open fa-2x mb-2 text-primary"></i><br>Produits',
                        ['products', 'id' => $model->id],
                        [
                            'class' => 'btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center',
                            'style' => 'border-radius: 10px; transition: all 0.3s ease; border-color: #f1ac16; color: #5c5c5c;'
                        ]
                    ) ?>
                </div>
                <div class="col-md-4 col-sm-6">
                    <?= Html::a(
                        '<i class="fas fa-shopping-cart fa-2x mb-2 text-success"></i><br>Commandes',
                        ['orders', 'id' => $model->id],
                        [
                            'class' => 'btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center',
                            'style' => 'border-radius: 10px; transition: all 0.3s ease; border-color: #f1ac16; color: #5c5c5c;'
                        ]
                    ) ?>
                </div>
                <div class="col-md-4 col-sm-6">
                    <?= Html::a(
                        '<i class="fas fa-users fa-2x mb-2 text-warning"></i><br>Clients',
                        ['customers', 'id' => $model->id],
                        [
                            'class' => 'btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center',
                            'style' => 'border-radius: 10px; transition: all 0.3s ease; border-color: #f1ac16; color: #5c5c5c;'
                        ]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .woocommerce-view .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .woocommerce-view .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(241, 172, 22, 0.3) !important;
        border-color: #f1ac16 !important;
    }
    
    .woocommerce-view .detail-view th {
        font-weight: 600;
        color: #5c5c5c;
    }
    
    .woocommerce-view .detail-view td {
        color: #495057;
    }
    
    .woocommerce-view .btn-outline-primary {
        border-color: #f1ac16;
        color: #5c5c5c;
    }
    
    .woocommerce-view .btn-outline-primary:hover {
        background-color: #f1ac16;
        border-color: #f1ac16;
        color: white;
    }
    
    .woocommerce-view .card-footer {
        border-top: 1px solid #e9ecef !important;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.woocommerce-view .card').each(function(index) {
        $(this).css('opacity', '0').delay(200 * index).animate({opacity: 1}, 600);
    });
    
    // Animation pour les boutons de recherche
    $('.woocommerce-view .btn-outline-primary').hover(
        function() {
            $(this).animate({fontSize: '1.05em'}, 150);
        },
        function() {
            $(this).animate({fontSize: '1em'}, 150);
        }
    );
    
    // Effet de pulse sur les icônes
    setInterval(function() {
        $('.woocommerce-view .btn-outline-primary i').each(function() {
            $(this).css('transform', 'scale(1.1)').delay(100).animate({transform: 'scale(1)'}, 200);
        });
    }, 3000);
});
</script>