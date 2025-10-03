<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$url = Html::encode($model->url);

if (strpos($url, 'localhost') !== false) {
	// Forcer HTTP pour localhost
	$url = "http://" . $url;
} else {
	// Vérifier si le site est accessible en HTTP
	$headers = @get_headers("http://" . $url);
	if ($headers && strpos($headers[0], '200') !== false) {
		$url = "https://" . $url;
	} else {
		$url = "https://" . $url;
	}
}

$api = Html::encode($model->api_key);

$webService = new PrestaShopWebservice($url, $api, false);
?>
<div class="prestashop-view">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0 text-dark">
                    <i class="fas fa-shopping-cart text-warning me-2"></i>
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
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-globe me-2 text-primary"></i> URL du site
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-link text-info"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->url) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-key me-2 text-warning"></i> Clé API
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-key text-warning"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->api_key) ?>" readonly>
                        </div>
                    </div>
                </div>                
            </div>
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
                <div class="col-md-3 col-sm-6">
                    <?= Html::a(
                        '<i class="fas fa-box-open fa-2x mb-2 text-primary"></i><br>Produits',
                        ['products', 'id' => $model->id],
                        [
                            'class' => 'btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center',
                            'style' => 'border-radius: 10px; transition: all 0.3s ease; border-color: #f1ac16; color: #5c5c5c;'
                        ]
                    ) ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?= Html::a(
                        '<i class="fas fa-shopping-cart fa-2x mb-2 text-success"></i><br>Commandes',
                        ['orders', 'id' => $model->id],
                        [
                            'class' => 'btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center',
                            'style' => 'border-radius: 10px; transition: all 0.3s ease; border-color: #f1ac16; color: #5c5c5c;'
                        ]
                    ) ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?= Html::a(
                        '<i class="fas fa-history fa-2x mb-2 text-info"></i><br>Historique de commandes',
                        ['orderhistories', 'id' => $model->id],
                        [
                            'class' => 'btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center',
                            'style' => 'border-radius: 10px; transition: all 0.3s ease; border-color: #f1ac16; color: #5c5c5c;'
                        ]
                    ) ?>
                </div>
                <div class="col-md-3 col-sm-6">
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
    .prestashop-view .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .prestashop-view .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(241, 172, 22, 0.3) !important;
        border-color: #f1ac16 !important;
    }
    
    .prestashop-view .detail-view th {
        font-weight: 600;
        color: #5c5c5c;
    }
    
    .prestashop-view .detail-view td {
        color: #495057;
    }
    
    .prestashop-view .btn-outline-primary {
        border-color: #f1ac16;
        color: #5c5c5c;
    }
    
    .prestashop-view .btn-outline-primary:hover {
        background-color: #f1ac16;
        border-color: #f1ac16;
        color: white;
    }
    
    .prestashop-view .card-footer {
        border-top: 1px solid #e9ecef !important;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.prestashop-view .card').each(function(index) {
        $(this).css('opacity', '0').delay(200 * index).animate({opacity: 1}, 600);
    });
    
    // Animation pour les boutons de recherche
    $('.prestashop-view .btn-outline-primary').hover(
        function() {
            $(this).animate({fontSize: '1.05em'}, 150);
        },
        function() {
            $(this).animate({fontSize: '1em'}, 150);
        }
    );
    
    // Effet de pulse sur les icônes
    setInterval(function() {
        $('.prestashop-view .btn-outline-primary i').each(function() {
            $(this).css('transform', 'scale(1.1)').delay(100).animate({transform: 'scale(1)'}, 200);
        });
    }, 3000);
});
</script>