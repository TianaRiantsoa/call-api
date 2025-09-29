<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$url = Html::encode($model->url);

if (strpos($url, 'localhost') !== false) {
	// Forcer HTTP pour localhost
	$url = "http://" . $url;
} else {
	// Vérifier si le site est accessible en HTTP
	$headers = @get_headers("http://" . $url);
	if ($headers && strpos($headers[0], '200') !== false) {
		$url = "https://" . $url;
	} else {
		$url = "https://" . $url;
	}
}

$api = Html::encode($model->api_key);

$webService = new PrestaShopWebservice($url, $api, false);
?>
<div class="prestashop-view">
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
            'api_key',
        ],
    ]) ?>

    <br>
    <h1 class="text-center">Option de recherche possible</h1>    
    <br>

    <div style="display: flex; justify-content: center; gap: 20px;">
        <?= Html::a('Recherche de produit', ['products', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de commande', ['orders', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Voir l\'historique d\'une commande', ['orderhistories', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de client', ['customers', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
    </div>

    
</div>