<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = 'Commandes | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Recherche de commande';
\yii\web\YiiAsset::register($this);
?>
<div class="shopify-orders-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($model, 'api_key')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($model, 'secret_key')->textInput(['maxlength' => true])->hiddenInput() ?>

    <?= $form->field($mod, 'ref')->textInput(['maxlength' => true, 'placeholder' => 'Exemple : 123456'])->hint('<small>Renseignez ici le numéro de la commande à rechercher</small>') ?>

    <?= Html::submitButton('Rechercher', ['class' => 'btn btn-success btn-sm']) ?>

    <?php ActiveForm::end(); ?>

</div>