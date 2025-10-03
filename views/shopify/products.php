<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = 'Produits | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Recherche de produit';
\yii\web\YiiAsset::register($this);
?>
<div class="shopify-products">
    <!-- Carte des détails du site -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark">
                <i class="fas fa-store text-warning me-2"></i>
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
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-lock me-2 text-danger"></i> Mot de passe
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-lock text-danger"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->password) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-shield-alt me-2 text-success"></i> Clé secrète
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-shield-alt text-success"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->secret_key) ?>" readonly>
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
                    <div class="shopify-products-form">
                        <?php $form = ActiveForm::begin([
                            'options' => [
                                'class' => 'needs-validation',
                                'novalidate' => true
                            ]
                        ]); ?>

                        <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <?= $form->field($model, 'api_key')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <?= $form->field($model, 'password')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <?= $form->field($model, 'secret_key')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <!-- Type de produit -->
                        <div class="form-group mb-4">
                            <label class="form-label text-muted mb-3">
                                <i class="fas fa-boxes me-2 text-primary"></i>
                                Type de produit
                            </label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-2 type-option" style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body text-center p-4">
                                            <i class="fas fa-box fa-2x text-success mb-2"></i>
                                            <h5 class="card-title text-success mb-2">Produit Simple</h5>
                                            <p class="card-text text-muted small mb-3">Produit standard sans variantes</p>
                                            <?= Html::radio('ShopifyProduct[type]', false, [
                                                'value' => 'simple',
                                                'class' => 'type-radio',
                                                'id' => 'simple-type',
                                                'style' => 'transform: scale(1.2);'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-2 type-option" style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body text-center p-4">
                                            <i class="fas fa-exchange-alt fa-2x text-warning mb-2"></i>
                                            <h5 class="card-title text-warning mb-2">Produit Variable</h5>
                                            <p class="card-text text-muted small mb-3">Produit avec variantes (tailles, couleurs...)</p>
                                            <?= Html::radio('ShopifyProduct[type]', false, [
                                                'value' => 'variable',
                                                'class' => 'type-radio',
                                                'id' => 'variable-type',
                                                'style' => 'transform: scale(1.2);'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
    .shopify-products .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .shopify-products .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .shopify-products .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .shopify-products .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }
    
    .shopify-products .form-control, 
    .shopify-products .form-select {
        border-radius: 8px;
        border: 1px solid #e1e5e9;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .shopify-products .form-control:focus, 
    .shopify-products .form-select:focus {
        border-color: #f1ac16;
        box-shadow: 0 0 0 0.2rem rgba(241, 172, 22, 0.25);
    }
    
    .shopify-products .btn-primary {
        background-color: #f1ac16;
        border-color: #f1ac16;
    }
    
    .shopify-products .btn-primary:hover {
        background-color: #e69500;
        border-color: #e69500;
    }
    
    .shopify-products .input-group-text {
        border-radius: 8px 0 0 8px;
        border-right: none;
    }
    
    .shopify-products .input-group .form-control {
        border-radius: 0 8px 8px 0;
    }
    
    .shopify-products .type-option {
        transition: all 0.3s ease;
        border-color: #dee2e6 !important;
    }
    
    .shopify-products .type-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        border-color: #f1ac16 !important;
    }
    
    .shopify-products .type-option.active {
        border-color: #f1ac16 !important;
        box-shadow: 0 0 0 3px rgba(241, 172, 22, 0.25);
    }
    
    .shopify-products .detail-view th {
        font-weight: 600;
        color: #5c5c5c;
    }
    
    .shopify-products .detail-view td {
        color: #495057;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.shopify-products .card').each(function(index) {
        $(this).css('opacity', '0').delay(200 * index).animate({opacity: 1}, 600);
    });
    
    // Gestion des options de type de produit
    $('.type-option').on('click', function() {
        // Retirer la classe active de tous les éléments
        $('.type-option').removeClass('active');
        // Ajouter la classe active à l'élément cliqué
        $(this).addClass('active');
        
        // Mettre à jour le radio input correspondant
        var radio = $(this).find('.type-radio');
        $('.type-radio').prop('checked', false);
        radio.prop('checked', true);
    });
    
    // Animation pour le bouton de soumission
    $('.shopify-products .btn-primary').hover(
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
    
    // Initialiser l'état des éléments actifs
    $('.type-radio:checked').closest('.type-option').addClass('active');
});
</script>