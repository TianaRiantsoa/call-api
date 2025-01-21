<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

require("./../vendor/prestashop/prestashop-webservice-lib/PSWebServiceLibrary.php");

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Clients | ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de client', 'url' => ['customers', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
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
$ref = Html::encode($ref);

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'url:url',
        'api_key',
    ],
]);

echo '<h2>Résultat de la recherche sur le client : ' . $ref . ' du site ' . $url . '</h2>';

try {

    $webService = new PrestaShopWebservice($url, $api, false);

    $languageOpt = [
        'resource' => 'languages',
        'filter[iso_code]' => 'fr', // Filtrer par code ISO
        'display' => 'full',
    ];
    $languageXml = $webService->get($languageOpt);
    $languages = $languageXml->languages->children();

    $languageId = null;
    foreach ($languages as $language) {
        $languageId = (int)$language->id; // Récupérer l'ID de la langue française
        break; // On s'arrête après avoir trouvé une correspondance
    }

    if (!$languageId) {
        throw new PrestaShopWebserviceException('Langue française introuvable dans la boutique.');
    }


    // Construire les options de la requête pour filtrer par référence
    $opt = [
        'resource' => 'customers',
        'filter[email]' => $ref, // Filtrer par référence
        'display' => 'full',
    ];

    try {
        // Récupérer les clients depuis l'API PrestaShop
        $xml = $webService->get($opt);
        $customers = $xml->customers->children(); // Récupérer tous les clients

        // Initialiser la variable pour stocker les clients
        $customerList = [];

        // Vérifier si nous avons des clients
        if (count($customers) > 0) {
            // Parcourir les clients et vérifier le type
            foreach ($customers as $customer) {
                // Ajouter les clients valides au tableau
                $customerData = [
                    'id' => (int)$customer->id,
                    'id_default_group ' => (string)$customer->id_default_group,
                    'company' => (string)$customer->company,
                    'firstname' => (string)$customer->firstname,
                    'lastname' => (string)$customer->lastname,
                    'email' => (string)$customer->email,
                    'siret' => (int)$customer->siret,
                    'active' => (bool)$customer->active,
                    'siret' => (int)$customer->siret,
                    'date_add' => (string)$customer->date_add,
                    'date_upd' => (string)$customer->date_upd,
                ];

                if ($customer->company != '' && $customer->company != null && $customer->siret != '' && $customer->siret != null) {
                    $customerData['type'] = 'Professionnel';
                    $customerData['company'] = (string)$customer->company;
                } else {
                    $customerData['type'] = 'Particulier';
                    $customerData['company'] = '';
                }

                $customerList[] = $customerData;
            }
        } else {
            // Aucun client trouvé
            Yii::$app->session->setFlash('error', 'Aucun client trouvé avec cette référence.');
            return; // Stopper l'exécution ici
        }
    } catch (PrestaShopWebserviceException $e) {
        // Gérer les erreurs liées à l'API
        Yii::$app->session->setFlash('error', 'Erreur lors de la récupération des données client : ' . $e->getMessage());
        return; // Stopper l'exécution ici
    }


    /* 
		TRAITEMENT DES DONNEES
		*/
    echo '<h3>Détails du Client</h3>';
    // Si des clients sont trouvés et valides, afficher le GridView
    if (!empty($customerList)) {
        echo GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $customerList,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'type',
                    'label' => 'Type',
                ],
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['id'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'company',
                    'label' => 'Société',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['company'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'firstname',
                    'label' => 'Prénom',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['firstname'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'lastname',
                    'label' => 'Nom',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['lastname'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'email',
                    'label' => 'Email',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['email'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'active',
                    'label' => 'Statut',
                    'value' => function ($model) {
                        return isset($model['active']) && $model['active'] ? 'Actif' : 'Inactif';
                    },
                ],
                [
                    'attribute' => 'date_add',
                    'value' => function ($model) {
                        $date = is_array($model) ? $model['date_add'] : $model->date_add;
                        return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
                    },
                    'label' => 'Création',  // Nouveau nom de la colonne
                ],
                [
                    'attribute' => 'date_upd',
                    'value' => function ($model) {
                        $date = is_array($model) ? $model['date_upd'] : $model->date_upd;
                        return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
                    },
                    'label' => 'Mise à jour',  // Nouveau nom de la colonne
                ],
            ],
        ]);
    }
} catch (PrestaShopWebserviceException $e) {
    // Gérer les erreurs liées à l'API
    Yii::$app->session->setFlash('error', 'Erreur lors de la récupération des données produit : ' . $e->getMessage());
    return; // Stopper l'exécution ici
}
