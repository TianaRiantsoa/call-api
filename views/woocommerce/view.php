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

    <div style="display: flex; justify-content: center; gap: 20px;">
        <?= Html::a('Recherche de produit', ['products', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de commande', ['orders', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
        <?= Html::a('Recherche de client', ['customers', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mx-3']) ?>
    </div>
    <br><br>

    <?php if ($model->erp != null) { ?>
        <div style="display: flex; justify-content: center; gap: 20px;">
            <h2>ERP : <?= $model->erp[0] ?></h2>
            <h2>CMS : <?= $model->type[0] ?></h2>
            <h2>Code tiers Sage : <?= $model->ctsage[0] ?></h2>
        </div>
        <br><br>
        <h1>Configuration :</h1>

        <div class="accordion" id="accordionExample">
            <?php if (!empty($model->config)) {
                foreach ($model->config as $index => $config) {
                    $c = json_decode($config, true);

                    $grid[] = $c;
            ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $model->slug[$index] ?>">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $model->slug[$index] ?>" aria-expanded="false" aria-controls="collapse<?= $model->slug[$index] ?>">
                                <p>Configuration <?= $model->slug[$index] ?></p>
                            </button>
                        </h2>
                        <!-- Par défaut, la classe 'collapse' est utilisée sans 'show' pour qu'il soit replié -->
                        <div id="collapse<?= $model->slug[$index] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $model->slug[$index] ?>" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <?php
                                // Vérifier si la décodification a réussi
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    // Utiliser json_encode avec JSON_PRETTY_PRINT pour formater le JSON                                
                                ?>
                                    <p>Plannif.exe --key <?= $model->serial_id[$index] ?> --configuration <?= $model->slug[$index] ?></p>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Paramètre</th>
                                                <th>Produits</th>
                                                <th>Champs</th>
                                                <th>Commandes</th>
                                                <th>PRO</th>
                                                <th>B2B</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr data-key="1">
                                                <!-- Paramètre -->
                                                <td>
                                                    URL : <?= $c['site:url'] ?><br>
                                                    Sous dossier : <?= $c['param:api_path'] ?><br>
                                                    Clé client : <?= $c['param:api_key'] ?><br>
                                                    Clé secrète : <?= $c['param:api_key_secret'] ?><br>
                                                    <?php if ($c['site:erp'] === 'ebpsdk') { ?>
                                                        Dossier EBP : <?= $c['erp:ebp_dossier']['folder'] ?><br>
                                                        Utilisateur EBP : <?= $c['erp:ebp_user'] ?><br>
                                                        Mot de passe EBP : <?= $c['erp:ebp_pass'] ?><br>
                                                        Utilisateur SQL : <?= $c['param:ebpsdk_uid'] ?><br>
                                                        Mot de passe SQL : <?= $c['param:ebpsdk_password'] ?><br>
                                                    <?php } elseif ($c['site:erp'] === 'sage') { ?>
                                                        Dossier SAGE : <?= $c['erp:sage_gc_fic']['folder'] ?><br>
                                                        Utilisateur SAGE : <?= $c['erp:sage_gc_user'] ?><br>
                                                        Mot de passe SAGE : <?= $c['erp:sage_gc_pass'] ?><br>
                                                        Utilisateur SQL : <?= $c['sage:uid'] ?><br>
                                                        Mot de passe SQL : <?= $c['sage:password'] ?><br>
                                                    <?php } ?>
                                                </td>

                                                <!-- Produit -->
                                                <td>
                                                    Flux Produits : <?= $c['ecommerce:product'] ?><br>
                                                    Création Produit : <?= $c['ecommerce:insert_product'] ?><br>
                                                    Ne pas mettre à jour un Produit : <?= $c['ecommerce:no_update_product'] ?><br>

                                                </td>
                                                <!-- Champs -->
                                                <td>
                                                    Nom : <?= $c['champs:nom'] ?><br>
                                                    TVA : <?= $c['champs:tva'] ?><br>
                                                    Publier sur le Web : <?= $c['champs:enventesurleweb'] ?>
                                                </td>

                                                <!-- Commandes -->
                                                <td>
                                                    Flux Commandes : <?= $c['erp:commande'] ?><br>
                                                </td>

                                                <!-- PRO -->
                                                <td>
                                                    Gestion des gammes : <?= $c['erp:gamme'] ?><br>
                                                    Mise à jour des déclinaisons / variations : <?= $c['ecommerce:option'] ?><br>
                                                    Mettre à jour à la fois produit et gamme : <?= $c['ecommerce:option_et_produit'] ?><br>
                                                </td>

                                                <!-- B2B -->
                                                <td>
                                                    Flux clients : <?= $c['B2B:clients'] ?><br>
                                                    Grilles de prix : <?= $c['B2B:grilles'] ?><br>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php
                                    // echo '<pre>' . json_encode($c, JSON_PRETTY_PRINT) . '</pre>';
                                } else {
                                    echo 'Erreur dans le format du JSON';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <h2>
                    Aucune configuration V7 n'a été détectée pour ce client. <br>
                    Cela peut signifier soit qu'aucune configuration en ligne n'a été créée,
                    soit qu'il utilise un développement sur mesure intégré au cœur du logiciel E-connecteur.
                </h2>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p style="color:red">Ce client n'apparaît pas dans la liste des clients en V7. Il est possible qu'aucune licence ne soit associée à cette URL.</p>
    <?php } ?>
</div>