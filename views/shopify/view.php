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

    <div style="display: flex; justify-content: center; gap: 20px;">
        <?= Html::a('Recherche de produit', ['products', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de commande', ['orders', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de client', ['customers', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
    </div>

    <div>

        <h1>Détails du Shopify</h1>

        <!-- Tableau des résultats récupérés depuis MySQL -->
        <table class="table">
            <thead>
                <tr>
                    <th>Configuration</th>
                    <th>ERP</th>
                    <th>Type</th>
                    <th>Serial ID</th>
                    <th>Slug</th>
                    <th>Client</th>
                    <th>CTSage</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($model->config)): ?>
                    <?php foreach ($model->config as $index => $config): ?>
                        <tr>
                            <td>
                                <!-- Accordéon pour chaque ligne -->
                                <div class="accordion" id="accordionExample<?= $index ?>">
                                    <div class="accordion-item">
                                        <!-- Entête personnalisé avec serial_id et slug -->
                                        <h2 class="accordion-header" id="heading<?= $index ?>">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                                                Plannif.exe --key <?= $model->serial_id[$index] ?> --configuration <?= $model->slug[$index] ?>
                                            </button>
                                        </h2>
                                        <!-- Corps de l'accordéon avec le JSON formaté -->
                                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#accordionExample<?= $index ?>">
                                            <div class="accordion-body">
                                                <?php
                                                // Décoder le JSON pour le formater
                                                $decodedConfig = json_decode($config, true); // true pour obtenir un tableau associatif

                                                // Vérifier si la décodification a réussi
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    // Utiliser json_encode avec JSON_PRETTY_PRINT pour formater le JSON
                                                    // echo $decodedConfig['site:url'];
                                                    echo '<pre>' . json_encode($decodedConfig, JSON_PRETTY_PRINT) . '</pre>';
                                                } else {
                                                    echo 'Erreur dans le format du JSON';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><?= $model->erp[$index] ?></td>
                            <td><?= $model->type[$index] ?></td>
                            <td><?= $model->serial_id[$index] ?></td>
                            <td><?= $model->slug[$index] ?></td>
                            <td><?= $model->client[$index] ?></td>
                            <td><?= $model->ctsage[$index] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Aucune donnée trouvée pour cette URL.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</div>