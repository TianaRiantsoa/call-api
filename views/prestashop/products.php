<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Produits | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Recherche de produit';
\yii\web\YiiAsset::register($this);

$url = Html::encode($model->url);

if (strpos($url, 'localhost') !== false) {
    $url = "http://" . $url;
} else {
    $headers = @get_headers("http://" . $url);
    if ($headers && strpos($headers[0], '200') !== false) {
        $url = "http://" . $url;
    } else {
        $url = "https://" . $url;
    }
}

$api = Html::encode($model->api_key);

$webService = new PrestaShopWebservice($url, $api, false);

// Récupération des langues disponibles
$xmlLang = $webService->get(['resource' => 'languages', 'display' => 'full']);
$languages = $xmlLang->languages->language;

// Transformer en tableau [iso_code => name]
$langOptions = [];
foreach ($languages as $lang) {
    $iso = (string)$lang->iso_code;
    $name = (string)$lang->name;
    $langOptions[$iso] = $name;
}

?>
<div class="prestashop-products">
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
                Recherche de produits
            </h2>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="prestashop-products-form">
                        <?php $form = ActiveForm::begin([
                            'options' => [
                                'class' => 'needs-validation',
                                'novalidate' => true
                            ]
                        ]); ?>

                        <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <?= $form->field($model, 'api_key')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

                        <!-- Champ caché pour variation_type qui envoie toujours une valeur -->
                        <?= Html::hiddenInput('PrestashopProduct[variation_type]', '', ['id' => 'hidden-variation-type']) ?>

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
                                            <p class="card-text text-muted small mb-3">Produit standard sans déclinaisons</p>
                                            <?= Html::radio('PrestashopProduct[type]', false, [
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
                                            <h5 class="card-title text-warning mb-2">Produit Déclinaison</h5>
                                            <p class="card-text text-muted small mb-3">Produit avec variantes (tailles, couleurs...)</p>
                                            <?= Html::radio('PrestashopProduct[type]', false, [
                                                'value' => 'variation',
                                                'class' => 'type-radio',
                                                'id' => 'variation-type',
                                                'style' => 'transform: scale(1.2);'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Options de variation -->
                        <div id="variation-options" class="mb-4" style="display: none;">
                            <div class="form-group">
                                <label class="form-label text-muted mb-3">
                                    <i class="fas fa-layer-group me-2 text-info"></i>
                                    Type de déclinaison
                                </label>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-2 variation-option" style="cursor: pointer; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-4">
                                                <i class="fas fa-folder-open fa-2x text-primary mb-2"></i>
                                                <h5 class="card-title text-primary mb-2">Parent</h5>
                                                <p class="card-text text-muted small mb-3">Produit parent avec déclinaisons</p>
                                                <?= Html::radio('PrestashopProduct[variation_type]', false, [
                                                    'value' => 'parent',
                                                    'class' => 'variation-type-radio',
                                                    'id' => 'parent-type',
                                                    'style' => 'transform: scale(1.2);'
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-2 variation-option" style="cursor: pointer; transition: all 0.3s ease;">
                                            <div class="card-body text-center p-4">
                                                <i class="fas fa-boxes fa-2x text-info mb-2"></i>
                                                <h5 class="card-title text-info mb-2">Enfant</h5>
                                                <p class="card-text text-muted small mb-3">Variante spécifique du produit</p>
                                                <?= Html::radio('PrestashopProduct[variation_type]', false, [
                                                    'value' => 'child',
                                                    'class' => 'variation-type-radio',
                                                    'id' => 'child-type',
                                                    'style' => 'transform: scale(1.2);'
                                                ]) ?>
                                            </div>
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

                        <!-- Langue -->
                        <div class="form-group mb-4">
                            <?= $form->field($model, 'language', [
                                'inputOptions' => [
                                    'class' => 'form-select form-select-lg',
                                    'style' => 'border-radius: 8px; padding: 0.75rem 1rem;'
                                ]
                            ])->dropDownList(
                                $langOptions,
                                [
                                    'prompt' => 'Choisissez une langue...',
                                    'class' => 'form-select'
                                ]
                            )->label(
                                '<i class="fas fa-language me-2 text-muted"></i> Langue',
                                ['class' => 'form-label fw-bold']
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
    .prestashop-products .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .prestashop-products .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .prestashop-products .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .prestashop-products .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }
    
    .prestashop-products .form-control, 
    .prestashop-products .form-select {
        border-radius: 8px;
        border: 1px solid #e1e5e9;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .prestashop-products .form-control:focus, 
    .prestashop-products .form-select:focus {
        border-color: #f1ac16;
        box-shadow: 0 0 0 0.2rem rgba(241, 172, 22, 0.25);
    }
    
    .prestashop-products .btn-primary {
        background-color: #f1ac16;
        border-color: #f1ac16;
    }
    
    .prestashop-products .btn-primary:hover {
        background-color: #e69500;
        border-color: #e69500;
    }
    
    .prestashop-products .type-option,
    .prestashop-products .variation-option {
        transition: all 0.3s ease;
        border-color: #dee2e6 !important;
    }
    
    .prestashop-products .type-option:hover,
    .prestashop-products .variation-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        border-color: #f1ac16 !important;
    }
    
    .prestashop-products .type-option.active,
    .prestashop-products .variation-option.active {
        border-color: #f1ac16 !important;
        box-shadow: 0 0 0 3px rgba(241, 172, 22, 0.25);
    }
    
    .prestashop-products .detail-view th {
        font-weight: 600;
        color: #5c5c5c;
    }
    
    .prestashop-products .detail-view td {
        color: #495057;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.prestashop-products .card').each(function(index) {
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
        
        // Afficher/cacher les options de variation
        if (radio.val() === 'variation') {
            $('#variation-options').slideDown(300);
        } else {
            $('#variation-options').slideUp(300);
            // Réinitialiser les options de variation à vide pour les produits simples
            $('.variation-type-radio').prop('checked', false);
            $('#hidden-variation-type').val('');
        }
    });
    
    // Gestion des options de variation
    $('.variation-option').on('click', function() {
        // Retirer la classe active de tous les éléments
        $('.variation-option').removeClass('active');
        // Ajouter la classe active à l'élément cliqué
        $(this).addClass('active');
        
        // Mettre à jour le radio input correspondant
        var radio = $(this).find('.variation-type-radio');
        $('.variation-type-radio').prop('checked', false);
        radio.prop('checked', true);
        
        // Mettre à jour le champ caché
        $('#hidden-variation-type').val(radio.val());
    });
    
    // Animation pour le bouton de soumission
    $('.prestashop-products .btn-primary').hover(
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
    $('.variation-type-radio:checked').closest('.variation-option').addClass('active');
    
    if ($('.type-radio:checked').val() === 'variation') {
        $('#variation-options').show();
    }
});
</script>