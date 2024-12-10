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
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
