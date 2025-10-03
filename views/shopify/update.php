<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = 'Mettre à jour le client : ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Mise à jour';
?>
<div class="shopify-update">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0 text-dark">
                    <i class="fas fa-edit text-warning me-2"></i>
                    <?= Html::encode($this->title) ?>
                </h1>
                <div>
                    <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Retour', ['view', 'id' => $model->id], [
                        'class' => 'btn btn-secondary btn-sm',
                        'style' => 'border-radius: 20px;'
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shopify-update .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .shopify-update .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .shopify-update .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .shopify-update .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }
    
    .shopify-update .form-control {
        border-radius: 8px;
        border: 1px solid #e1e5e9;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .shopify-update .form-control:focus {
        border-color: #f1ac16;
        box-shadow: 0 0 0 0.2rem rgba(241, 172, 22, 0.25);
    }
    
    .shopify-update .btn-primary {
        background-color: #f1ac16;
        border-color: #f1ac16;
    }
    
    .shopify-update .btn-primary:hover {
        background-color: #e69500;
        border-color: #e69500;
    }
    
    .shopify-update .form-group {
        margin-bottom: 1.5rem;
    }
    
    .shopify-update label {
        font-weight: 500;
        color: #5c5c5c;
        margin-bottom: 0.5rem;
    }
    
    .shopify-update .form-text {
        color: #6c757d;
        font-size: 0.875rem;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.shopify-update .card').css('opacity', '0').animate({opacity: 1}, 500);
    
    // Animation pour les champs de formulaire
    $('.shopify-update .form-control').on('focus', function() {
        $(this).animate({borderWidth: '2px'}, 100);
    }).on('blur', function() {
        $(this).animate({borderWidth: '1px'}, 100);
    });
    
    // Animation pour le bouton de soumission
    $('.shopify-update .btn-primary').hover(
        function() {
            $(this).animate({fontSize: '1.05em'}, 100);
        },
        function() {
            $(this).animate({fontSize: '1em'}, 100);
        }
    );
    
    // Effet de pulse doux sur le formulaire
    $('.shopify-update .card-body').animate({
        backgroundColor: 'rgba(241, 172, 22, 0.02)'
    }, 1000).animate({
        backgroundColor: 'rgba(241, 172, 22, 0.05)'
    }, 1000);
});
</script>