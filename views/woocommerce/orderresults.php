<?php

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use yii\helpers\Html;

use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = 'Commandes | ' . Html::encode($ref) . ' | ' . Html::encode($model->url);
$this->params['breadcrumbs'][] = ['label' => 'Woocommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de commande', 'url' => ['orders', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

$url = Html::encode($model->url);

$headers = @get_headers("http://" . $url);
if ($headers && strpos($headers[0], '200') !== false) {
    $url = "https://" . $url;
} else {
    $url = "https://" . $url;
}

$consumer_key = Html::encode($model->consumer_key);
$consumer_secret = Html::encode($model->consumer_secret);
$ref = Html::encode($ref);

$client = new Client($url, $consumer_key, $consumer_secret, ['version' => 'wc/v3']);

// Récupération des données de commande
$order = $client->get('orders/' . $ref);

// Récupération des informations du client
if ($order->customer_id != 0) {
    $customer = $client->get('customers/' . $order->customer_id);
    $customerDetails[] = [
        'id' => $customer->id,
        'first_name' => $customer->first_name,
        'last_name' => $customer->last_name,
        'email' => $customer->email,
        'date_created' => $customer->date_created,
        'date_modified' => $customer->date_modified,
        //'phone' => $customer->phone,
    ];
}

// Préparer les données pour les GridViews
$orderDetails = [
    [
        'id' => $order->id,
        'status' => $order->status,
        'date_created' => $order->date_created,
        'date_modified' => $order->date_modified,
        'total' => $order->total,
        'total_tax' => $order->total_tax,
        'payment_method_title' => $order->payment_method_title,
    ],
];

$billingShippingDetails = [
    [
        'type' => 'Facturation',
        'name' => $order->billing->first_name . ' ' . $order->billing->last_name,
        'address' => $order->billing->address_1 . ', ' . $order->billing->postcode . ' ' . $order->billing->city . ', ' . $order->billing->country,
        'phone' => $order->billing->phone,
        'email' => $order->billing->email,
    ],
    [
        'type' => 'Livraison',
        'name' => $order->shipping->first_name . ' ' . $order->shipping->last_name,
        'address' => $order->shipping->address_1 . ', ' . $order->shipping->postcode . ' ' . $order->shipping->city . ', ' . $order->shipping->country,
        'phone' => $order->shipping->phone, // Non disponible pour la livraison
        'email' => '', // Non disponible pour la livraison
    ],
];

$productDetails = [];
foreach ($order->line_items as $item) {
    $productDetails[] = [
        'product_id' => $item->product_id,
        'sku' => $item->sku,
        'name' => $item->name,
        'variant_id' => $item->variation_id ?? null,
        'quantity' => $item->quantity,
        'total' => $item->total,
        'total_tax' => $item->total_tax,
    ];
}

// Affichage des GridViews

// Tableau 1 : Détail de la commande
$site = $url . '/wp-json/wc/v3/orders/' . $ref . '?consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret;
echo Html::a('Aller sur le JSON', $site, ['class' => 'btn btn-success', 'target' => '_blank']);

echo '<br><br><h3>Détail de la commande</h3>';
echo GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $orderDetails,
        'pagination' => false,
    ]),
    'columns' => [
        ['attribute' => 'id', 'label' => 'ID'],
        ['attribute' => 'status', 'label' => 'Statut'],
        ['attribute' => 'date_created', 'label' => 'Création'],
        ['attribute' => 'date_modified', 'label' => 'Mise à jour'],
        ['attribute' => 'total', 'label' => 'Total TTC'],
        ['attribute' => 'total_tax', 'label' => 'Taxes'],
        ['attribute' => 'payment_method_title', 'label' => 'Méthode de paiement'],
    ],
]);

// Tableau 2 : Détail du client
if (!empty($customerDetails)) {
    echo '<h3>Détail du client</h3>';
    echo GridView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $customerDetails,
            'pagination' => false,
        ]),
        'columns' => [
            ['attribute' => 'id', 'label' => 'ID Client'],
            ['attribute' => 'first_name', 'label' => 'Prénom'],
            ['attribute' => 'last_name', 'label' => 'Nom'],
            ['attribute' => 'email', 'label' => 'Email'],
            ['attribute' => 'date_created', 'label' => 'Création'],
            ['attribute' => 'date_modified', 'label' => 'Mise à jour'],
            //['attribute' => 'phone', 'label' => 'Téléphone'],
        ],
    ]);
}

// Tableau 3 : Adresse de facturation et livraison
echo '<h3>Adresse de facturation et livraison</h3>';
echo GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $billingShippingDetails,
        'pagination' => false,
    ]),
    'columns' => [
        ['attribute' => 'type', 'label' => 'Type'],
        ['attribute' => 'name', 'label' => 'Nom complet'],
        ['attribute' => 'address', 'label' => 'Adresse'],
        ['attribute' => 'phone', 'label' => 'Téléphone'],
        ['attribute' => 'email', 'label' => 'Email'],
    ],
]);

// Tableau 4 : Détail des produits commandés
echo '<h3>Détail des produits commandés</h3>';
echo GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $productDetails,
        'pagination' => false,
    ]),
    'columns' => [
        ['attribute' => 'product_id', 'label' => 'ID Produit'],
        ['attribute' => 'sku', 'label' => 'SKU'],
        ['attribute' => 'name', 'label' => 'Nom'],
        ['attribute' => 'variant_id', 'label' => 'ID Variante'],
        ['attribute' => 'quantity', 'label' => 'Quantité'],
        ['attribute' => 'total', 'label' => 'Total TTC'],
        ['attribute' => 'total_tax', 'label' => 'Taxes'],
    ],
]);



// echo '<pre>';
// print_r($order);
// echo '</pre>';
