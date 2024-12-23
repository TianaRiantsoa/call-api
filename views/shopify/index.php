<?php

use app\models\Shopify;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\ShopifySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Shopify';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shopify-index">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <p>
        <?= Html::a('Créer un client Shopify', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            // 'id',
            [
                'attribute' => 'url', // Colonne de la table
                'format' => 'raw', // Permet de rendre le contenu HTML cliquable
                'value' => function ($model) {
                    return Html::a($model->url, ['view', 'id' => $model->id], ['target' => '_blank']);
                },
            ],
            [
                'attribute' => 'api_key', // Colonne de la table
                'format' => 'raw', // Permet de rendre le contenu HTML cliquable
                'value' => function ($model) {
                    return Html::a($model->api_key, ['view', 'id' => $model->id], ['target' => '_blank']);
                },
            ],
            [
                'attribute' => 'password', // Colonne de la table
                'format' => 'raw', // Permet de rendre le contenu HTML cliquable
                'value' => function ($model) {
                    return Html::a($model->password, ['view', 'id' => $model->id], ['target' => '_blank']);
                },
            ],
            [
                'attribute' => 'secret_key', // Colonne de la table
                'format' => 'raw', // Permet de rendre le contenu HTML cliquable
                'value' => function ($model) {
                    return Html::a($model->secret_key, ['view', 'id' => $model->id], ['target' => '_blank']);
                },
            ],
            [
                'header' => 'Action',
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Shopify $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>