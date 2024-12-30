<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Produits | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Recherche de produit';
\yii\web\YiiAsset::register($this);
?>
<div class="prestashop-products-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($model, 'api_key')->textInput(['maxlength' => true])->hiddenInput() ?>

    <div class="form-group">
        <!-- Radio principal -->
        <?= $form->field($mod, 'type')->radioList(
            [
                'simple' => 'Produit Simple',
                'variation' => 'Produit Déclinaison',
            ],
            [
                'item' => function ($index, $label, $name, $checked, $value) {
                    return Html::radio($name, $checked, [
                        'value' => $value,
                        'label' => $label,
                        'class' => 'type-radio',
                    ]);
                },
            ]
        ) ?>
    </div>

    <!-- Sous-radio pour produit déclinaison -->
    <div id="variation-options" style="display: none;">
        <div class="form-group">
            <?= $form->field($mod, 'variation_type')->radioList(
                [
                    'parent' => 'Parent',
                    'child' => 'Enfant',
                ],
                [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return Html::radio($name, $checked, [
                            'value' => $value,
                            'label' => $label,
                            'class' => 'variation-type-radio',
                        ]);
                    },
                ]
            ) ?>
        </div>
    </div>

    <?= $form->field($mod, 'ref')->textInput(['maxlength' => true, 'placeholder' => 'Exemple : AR00000'])->hint('<small>Renseignez ici la référence du produit à rechercher</small>') ?>

    <?= Html::submitButton('Rechercher', ['class' => 'btn btn-success btn-sm']) ?>

    <?php ActiveForm::end(); ?>

    <?php
    $js = <<<JS
    // Gérer l'affichage des options de variation et leur soumission
    $('.type-radio').on('change', function() {
        if ($(this).val() === 'variation') {
            $('#variation-options').show();
            // Réactiver le champ variation_type si "Produit Déclinaison" est sélectionné
            $('.variation-type-radio').prop('disabled', false);
        } else {
            $('#variation-options').hide();
            // Désactiver le champ variation_type si "Produit Simple" est sélectionné
            $('.variation-type-radio').prop('disabled', true);
        }
    });
    
    // Initialiser l'état du formulaire au chargement
    $(document).ready(function() {
        if ($('.type-radio:checked').val() === 'variation') {
            $('#variation-options').show();
            $('.variation-type-radio').prop('disabled', false);
        } else {
            $('#variation-options').hide();
            $('.variation-type-radio').prop('disabled', true);
        }
    });
    JS;

    $this->registerJs($js);
    ?>

</div>