<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

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
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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

    <div style="display: flex; justify-content: center; gap: 20px;">
        <?= Html::a('Recherche de produit', ['products', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de commande', ['orders', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de client', ['customers', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
    </div>
    <br><br>
    <div style="display: flex; justify-content: center; gap: 20px;">
        <h2>ERP : <?= $model->erp[0] ?></h2>
        <h2>CMS : <?= $model->type[0] ?></h2>
        <h2>Code tiers Sage : <?= $model->ctsage[0] ?></h2>
    </div>
    <br><br>
    <h1>Configuration :</h1>

    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Accordéon Exemple
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    Contenu de l'accordéon.
                </div>
            </div>
        </div>
    </div>



    <?php if (!empty($model->config)) {
        foreach ($model->config as $index => $config) {
            $c = json_decode($config, true);
    ?>
            <p>CMD : Plannif.exe --key <?= $model->serial_id[$index] ?> --configuration <?= $model->slug[$index] ?></p>
        <?php
            // Vérifier si la décodification a réussi
            if (json_last_error() === JSON_ERROR_NONE) {
                // Utiliser json_encode avec JSON_PRETTY_PRINT pour formater le JSON
                echo '<pre>' . json_encode($c, JSON_PRETTY_PRINT) . '</pre>';
            } else {
                echo 'Erreur dans le format du JSON';
            }
        }
    } else { ?>
        <h2>Aucune donnée trouvée pour cette URL.</h2>
    <?php } ?>
</div>