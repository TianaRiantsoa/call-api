<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\WoocommerceProduct $model */

$this->title = 'Produits | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'WooCommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Recherche de produit';
\yii\web\YiiAsset::register($this);
?>
<div class="woocommerce-products">
    <!-- Carte des détails du site -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark">
                <i class="fab fa-wordpress text-warning me-2"></i>
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
                            <i class="fas fa-key me-2 text-warning"></i> Clé consommateur
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-key text-warning"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->consumer_key) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-key me-2 text-warning"></i> Clé secrète
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-key text-warning"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->consumer_secret) ?>" readonly>
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
                Recherche de produits
            </h2>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="woocommerce-products-form">
                        <?php $form = ActiveForm::begin([
                            'options' => [
                                'class' => 'needs-validation',
                                'novalidate' => true
                            ]
                        ]); ?>

                        <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <?= $form->field($model, 'consumer_key')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'consumer_secret')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <!-- Référence du produit -->
                        <div class="form-group mb-4">
                            <?= $form->field($mod, 'ref', [
                                'inputOptions' => [
                                    'class' => 'form-control form-control-lg',
                                    'placeholder' => 'Exemple : AR00000',
                                    'style' => 'border-radius: 8px; padding: 0.75rem 1rem;'
                                ]
                            ])->textInput(['maxlength' => true])->label(
                                '<i class="fas fa-barcode me-2 text-muted"></i> Référence du produit',
                                ['class' => 'form-label fw-bold']
                            )->hint(
                                '<small class="text-muted"><i class="fas fa-info-circle me-1"></i> Renseignez ici la référence du produit à rechercher</small>',
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
    .woocommerce-products .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .woocommerce-products .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .woocommerce-products .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .woocommerce-products .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }
    
    .woocommerce-products .form-control, 
    .woocommerce-products .form-select {
        border-radius: 8px;
        border: 1px solid #e1e5e9;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .woocommerce-products .form-control:focus, 
    .woocommerce-products .form-select:focus {
        border-color: #f1ac16;
        box-shadow: 0 0 0 0.2rem rgba(241, 172, 22, 0.25);
    }
    
    .woocommerce-products .btn-primary {
        background-color: #f1ac16;
        border-color: #f1ac16;
    }
    
    .woocommerce-products .btn-primary:hover {
        background-color: #e69500;
        border-color: #e69500;
    }
    
    .woocommerce-products .input-group-text {
        border-radius: 8px 0 0 8px;
        border-right: none;
    }
    
    .woocommerce-products .input-group .form-control {
        border-radius: 0 8px 8px 0;
    }
    
    .woocommerce-products .detail-view th {
        font-weight: 600;
        color: #5c5c5c;
    }
    
    .woocommerce-products .detail-view td {
        color: #495057;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.woocommerce-products .card').each(function(index) {
        $(this).css('opacity', '0').delay(200 * index).animate({opacity: 1}, 600);
    });
    
    // Animation pour le bouton de soumission
    $('.woocommerce-products .btn-primary').hover(
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