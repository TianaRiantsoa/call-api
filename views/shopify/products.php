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
<div class="shopify-products-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($model, 'api_key')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($model, 'secret_key')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($mod, 'ref')->textInput(['maxlength' => true, 'placeholder' => 'Exemple : AR00000'])->hint('<small>Renseignez ici la référence du produit à rechercher</small>') ?>

    <?= Html::submitButton('Rechercher', ['class' => 'btn btn-success btn-sm']) ?>

    <?php ActiveForm::end(); ?>

</div>