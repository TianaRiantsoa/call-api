<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

require("./../vendor/prestashop/prestashop-webservice-lib/PSWebServiceLibrary.php");

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Hitorique de commandes | ' . Html::encode($ref) . ' | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche d\'historique de commande', 'url' => ['orders', 'id' => $model->id]];
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
        $url = "http://" . $url;
    } else {
        $url = "https://" . $url;
    }
}

$api = Html::encode($model->api_key);
$ref = Html::encode($ref);
$db_id = $model->id;

echo 'URL de la requête : <a href=' . $url . '/api/order_histories/?filter[id_order]=' . $ref . '&ws_key=' . $api . ' target=_blank>' . $url . '/api/order_histories/?filter[id_order]=' . $ref . '&ws_key=' . $api . '</a>';

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'url:url',
        'api_key',
    ],
]);
ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

try {

    // Initialiser la connexion à l'API PrestaShop
    $webService = new PrestaShopWebservice($url, $api, true);

    // Récupérer la commande spécifique
    $xmlOH = $webService->get(['resource' => 'order_histories', 'filter[id_order]' => $ref, 'display' => 'full']);
    $OH = $xmlOH->children()->children();

    // echo '<pre>';
    // print_r($OH); // Pour debug
    // echo '</pre>';
    // exit;


    // Initialiser les tableaux pour stocker les résultats
    $orders = [];
    $customers = [];
    $addresses = [];
    $products = [];

    // Traiter les données des commandes
    foreach ($OH as $Order_H) {
        // Récupérer les informations de la commande

        $ohs[] = [
            'id' => (string) $Order_H->id,
            'id_employee' => (string) $Order_H->id_employee,
            'id_order_state' => (string) $Order_H->id_order_state,
            'id_order' => (string) $Order_H->id_order,
            'date_add' => (string) $Order_H->date_add,
        ];
        if ($Order_H->id_employee != 0) {
            $xml_employee = $webService->get(['resource' => 'employees', 'id' => (string) $Order_H->id_employee]);

            $employee = $xml_employee->children()->children();
            $ohs[count($ohs) - 1]['employee_name'] = '(' . $Order_H->id_employee . ') ' . (string) $employee->firstname . ' ' . (string) $employee->lastname . ' [' . (string) $employee->email . ']';
        }

        $xmlStatut = $webService->get(['resource' => 'order_states', 'id' => (string) $Order_H->id_order_state]);
        $statut = $xmlStatut->children()->children();
        $ohs[count($ohs) - 1]['order_state'] = '(' . $Order_H->id_order_state . ') ' . (string) $statut->name->language;
    }

    // Fournir les données sous forme de ArrayDataProvider
    $ohsDataProvider = new ArrayDataProvider([
        'allModels' => $ohs,
        'pagination' => ['pageSize' => 20],
    ]);
} catch (PrestaShopWebserviceException $e) {
    // Yii::$app->session->setFlash('error', 'Erreur : ' . $e->getMessage());
    // return;

    $rawResponse = $webService->getRawResponse();

    echo '<span style="color:red">Erreur détectée : ' . $e->getMessage() . PHP_EOL . '</span><br>';

    if ($rawResponse) {
        echo '<span style="color:red">Réponse brute : '  . PHP_EOL . $rawResponse . '</span>';
        // Tenter de parser ou analyser manuellement
        if (strpos($rawResponse, '<!DOCTYPE html>') !== false) {
            echo 'Erreur HTML détectée' . PHP_EOL;
        } elseif (strpos($rawResponse, '<?xml') === 0) {
            // Parser le XML manuellement
            $xml = simplexml_load_string($rawResponse);
            if ($xml !== false) {
                print_r($xml);
            } else {
                Yii::$app->session->setFlash('error', 'Erreur lors du parsing XML.' . PHP_EOL);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Format de réponse inconnu.' . PHP_EOL);
        }
    } else {
        Yii::$app->session->setFlash('error', 'Aucune réponse brute disponible.' . PHP_EOL);
    }
    return;
}
?>

<h3>Historique de la commande</h3>
<?php
// Afficher les commandes
echo GridView::widget([
    'dataProvider' => $ohsDataProvider,
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ID',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['id'],
                    $url . "/api/order_histories/{$model['id']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            }  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'id_order',
            'label' => 'ID de la commande',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'employee_name',
            'label' => 'Nom de l\'employé',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'order_state',
            'label' => 'Statut',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'date_add',
            'value' => function ($model) {
                $date = is_array($model) ? $model['date_add'] : $model->date_created;
                return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
            },
            'label' => 'Date de l\'historique',  // Nouveau nom de la colonne
        ],

    ],
]);
