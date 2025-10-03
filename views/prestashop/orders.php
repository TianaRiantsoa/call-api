<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Commandes | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Recherche de commande';
\yii\web\YiiAsset::register($this);
?>
<div class="prestashop-orders">
    <!-- Carte des détails du site -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark">
                <i class="fas fa-shopping-cart text-warning me-2"></i>
                Détails du site : <?= Html::encode($model->url) ?>
            </h2>
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

    <!-- Formulaire de recherche -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark">
                <i class="fas fa-search me-2 text-primary"></i>
                Recherche de commandes
            </h2>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="prestashop-orders-form">
                        <?php $form = ActiveForm::begin([
                            'options' => [
                                'class' => 'needs-validation',
                                'novalidate' => true
                            ]
                        ]); ?>

                        <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <?= $form->field($model, 'api_key')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <!-- Numéro de commande -->
                        <div class="form-group mb-4">
                            <?= $form->field($mod, 'ref', [
                                'inputOptions' => [
                                    'class' => 'form-control form-control-lg',
                                    'placeholder' => 'Exemple : 123456',
                                    'style' => 'border-radius: 8px; padding: 0.75rem 1rem;'
                                ]
                            ])->textInput(['maxlength' => true])->label(
                                '<i class="fas fa-hashtag me-2 text-muted"></i> Numéro de commande',
                                ['class' => 'form-label fw-bold']
                            )->hint(
                                '<small class="text-muted"><i class="fas fa-info-circle me-1"></i> Renseignez ici le numéro de la commande à rechercher</small>',
                                ['class' => 'form-text']
                            ) ?>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="form-group text-center mt-5">
                            <?= Html::submitButton(
                                '<i class="fas fa-search me-2"></i> Rechercher',
                                [
                                    'class' => 'btn btn-primary btn-lg px-5 py-3',
                                    'style' => 'background-color: #f1ac16; border-color: #f1ac16; border-radius: 50px; font-size: 1.1rem; transition: all 0.3s ease;'
                                ]
                            ) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .prestashop-orders .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .prestashop-orders .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .prestashop-orders .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .prestashop-orders .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }
    
    .prestashop-orders .form-control, 
    .prestashop-orders .form-select {
        border-radius: 8px;
        border: 1px solid #e1e5e9;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .prestashop-orders .form-control:focus, 
    .prestashop-orders .form-select:focus {
        border-color: #f1ac16;
        box-shadow: 0 0 0 0.2rem rgba(241, 172, 22, 0.25);
    }
    
    .prestashop-orders .btn-primary {
        background-color: #f1ac16;
        border-color: #f1ac16;
    }
    
    .prestashop-orders .btn-primary:hover {
        background-color: #e69500;
        border-color: #e69500;
    }
    
    .prestashop-orders .detail-view th {
        font-weight: 600;
        color: #5c5c5c;
    }
    
    .prestashop-orders .detail-view td {
        color: #495057;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.prestashop-orders .card').each(function(index) {
        $(this).css('opacity', '0').delay(200 * index).animate({opacity: 1}, 600);
    });
    
    // Animation pour le bouton de soumission
    $('.prestashop-orders .btn-primary').hover(
        function() {
            $(this).animate({fontSize: '1.15rem'}, 100);
        },
        function() {
            $(this).animate({fontSize: '1.1rem'}, 100);
        }
    );
    
    // Validation du formulaire
    $('.needs-validation').on('submit', function(event) {
        if (this.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
});
</script>