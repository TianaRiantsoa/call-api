<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = 'Créer un client Shopify';
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shopify-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
