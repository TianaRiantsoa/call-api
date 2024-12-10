<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PrestashopSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="prestashop-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 0
        ],
    ]); ?>

    <?php
    // Afficher les champs de recherche
    echo $form->field($model, 'id');
    echo $form->field($model, 'url');
    echo $form->field($model, 'api_key');
    ?>

    <div class="form-group">
        <?= Html::submitButton('Chercher', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Effacer', ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>