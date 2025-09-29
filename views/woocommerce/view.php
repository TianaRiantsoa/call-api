<?php


use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Woocommerces', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="woocommerce-view">
    <p>
        <?= Html::a('Mettre à jour', ['update', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::a('Supprimer', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Êtes-vous sûr de vouloir suppriemr ce client ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'url',
            'consumer_key',
            'consumer_secret',
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