<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Mettre à jour le client : ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Mise à jour';
?>
<div class="prestashop-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>