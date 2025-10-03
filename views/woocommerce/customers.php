<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = 'Clients | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Recherche de client';
\yii\web\YiiAsset::register($this);
?>
<div class="prestashop-customers-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'consumer_key')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'consumer_secret')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>

    <?= $form->field($mod, 'ref')->textInput([
        'maxlength' => true,
        'placeholder' => 'Exemple : dupont@gmail.com',
    ])->hint('<small>Renseignez ici l\'adresse email du client à rechercher</small>') ?>

        <?= Html::submitButton('Rechercher', ['class' => 'btn btn-success btn-sm']) ?>

    <?php ActiveForm::end(); ?>

</div>