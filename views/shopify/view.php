<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shopify-view">
    <p>
        <?= Html::a('Mettre à jour', ['update', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('Supprimer', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Êtes-vous sûr de vouloir supprimer ce client ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'url',
            'api_key',
            'password',
            'secret_key',
        ],
    ]) ?>

    <br>
    <h1 class="text-center">Option de recherche possible</h1>    
    <br>

    <div style="display: flex; justify-content: center; gap: 20px;">
        <?= Html::a('Recherche de produit', ['products', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de commande', ['orders', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de client', ['customers', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
    </div>
</div>