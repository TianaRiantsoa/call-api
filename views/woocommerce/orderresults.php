<?php

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

// Définir le titre et les breadcrumbs
$this->title = 'Commandes | ' . Html::encode($ref) . ' | ' . Html::encode($model->url);
$this->params['breadcrumbs'][] = ['label' => 'Woocommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de commande', 'url' => ['orders', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

// Initialiser les variables
$url = Html::encode($model->url);
$consumer_key = Html::encode($model->consumer_key);
$consumer_secret = Html::encode($model->consumer_secret);
$ref = Html::encode($ref);

// Forcer HTTPS pour l'URL
$url = "https://" . $url;

// Initialiser le client WooCommerce
$client = new Client($url, $consumer_key, $consumer_secret, ['version' => 'wc/v3', 'verify_ssl' => false]);

// Initialiser les tableaux de données
$orderDetails = [];
$customerDetails = [];
$billingShippingDetails = [];
$productDetails = [];

try {
    // Récupérer les données de la commande
    $order = $client->get('orders/' . $ref);

    // Récupérer les informations du client
    if (!empty($order->customer_id)) {
        $customerDetails = getCustomerDetails($client, $order->customer_id);
    }

    // Préparer les détails de la commande
    $orderDetails = prepareOrderDetails($order);

    // Préparer les informations de facturation et livraison
    $billingShippingDetails = prepareBillingShippingDetails($order);

    // Préparer les détails des produits
    $productDetails = prepareProductDetails($order);

} catch (HttpClientException $e) {
    handleHttpClientException($e);
}

// Afficher les données dans des GridView
renderOrderDetails($orderDetails, $url, $consumer_key, $consumer_secret, $ref);
renderCustomerDetails($customerDetails);
renderBillingShippingDetails($billingShippingDetails);
renderProductDetails($productDetails);

/**
 * Fonctions utilitaires
 */

// Récupérer les détails du client
function getCustomerDetails($client, $customerId)
{
    try {
        $customer = $client->get('customers/' . $customerId);
        return [[
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'date_created' => $customer->date_created,
            'date_modified' => $customer->date_modified,
        ]];
    } catch (HttpClientException $e) {
        Yii::$app->session->setFlash('error', "Erreur lors de la récupération des informations du client : " . $e->getMessage());
        return [];
    }
}

// Préparer les détails de la commande
function prepareOrderDetails($order)
{
    return [[
        'id' => $order->id,
        'status' => $order->status,
        'date_created' => $order->date_created,
        'date_modified' => $order->date_modified,
        'total' => $order->total,
        'total_tax' => $order->total_tax,
        'total_ht' => $order->total - $order->total_tax,
        'payment_method_title' => $order->payment_method_title,
        'carrier' => $order->shipping_lines[0]->method_title ?? 'N/A',
    ]];
}

// Préparer les informations de facturation et livraison
function prepareBillingShippingDetails($order)
{
    return [
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
            'phone' => $order->shipping->phone ?? '',
            'email' => '',
        ],
    ];
}

// Préparer les détails des produits
function prepareProductDetails($order)
{
    $productDetails = [];
    foreach ($order->line_items as $item) {
        $productDetails[] = [
            'product_id' => $item->product_id,
            'sku' => $item->sku,
            'name' => $item->name,
            'variant_id' => $item->variation_id ?? null,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'total' => $item->total,
            'total_tax' => $item->total_tax,
            'total_ttc' => $item->total + $item->total_tax,
        ];
    }
    return $productDetails;
}

// Gérer les exceptions HTTP
function handleHttpClientException($e)
{
    $response = $e->getResponse();
    $errorCode = $response instanceof \Automattic\WooCommerce\HttpClient\Response ? $response->getCode() : null;
    $errorMessage = $e->getMessage();

    switch ($errorCode) {
        case 404:
            $message = "Commande introuvable (Erreur 404).";
            break;
        case 403:
            $message = "Accès interdit (Erreur 403). Vérifiez vos clés API.";
            break;
        case 500:
            $message = "Erreur interne du serveur (Erreur 500).";
            break;
        default:
            $message = "Erreur inconnue : $errorMessage.";
    }

    Yii::$app->session->setFlash('error', $message);
}

// Afficher les détails de la commande
function renderOrderDetails($orderDetails, $url, $consumer_key, $consumer_secret, $ref)
{
    $site = $url . '/wp-json/wc/v3/orders/' . $ref . '?consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret;
    echo Html::a('Afficher le JSON de la commande', $site, ['class' => 'btn btn-success', 'target' => '_blank']);
    echo '<br><br><h3>Détail de la commande</h3>';
    echo GridView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $orderDetails,
            'pagination' => false,
        ]),
        'columns' => [
            ['attribute' => 'id', 'label' => 'ID'],
            ['attribute' => 'status', 'label' => 'Statut'],
            ['attribute' => 'date_created', 'value' => fn($model) => formatDate($model['date_created']), 'label' => 'Création'],
            ['attribute' => 'date_modified', 'value' => fn($model) => formatDate($model['date_modified']), 'label' => 'Mise à jour'],
            ['attribute' => 'total_ht', 'value' => fn($model) => formatCurrency($model['total_ht']), 'label' => 'Total HT'],
            ['attribute' => 'total_tax', 'value' => fn($model) => formatCurrency($model['total_tax']), 'label' => 'Taxes'],
            ['attribute' => 'total', 'value' => fn($model) => formatCurrency($model['total']), 'label' => 'Total TTC'],
            ['attribute' => 'payment_method_title', 'label' => 'Méthode de paiement'],
            ['attribute' => 'carrier', 'label' => 'Transporteur'],
        ],
    ]);
}

// Afficher les détails du client
function renderCustomerDetails($customerDetails)
{
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
                ['attribute' => 'date_created', 'value' => fn($model) => formatDate($model['date_created']), 'label' => 'Création'],
                ['attribute' => 'date_modified', 'value' => fn($model) => formatDate($model['date_modified']), 'label' => 'Mise à jour'],
            ],
        ]);
    }
}

// Afficher les informations de facturation et livraison
function renderBillingShippingDetails($billingShippingDetails)
{
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
}

// Afficher les détails des produits
function renderProductDetails($productDetails)
{
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
            ['attribute' => 'price', 'value' => fn($model) => formatCurrency($model['price']), 'label' => 'Prix unitaire'],
            ['attribute' => 'total', 'value' => fn($model) => formatCurrency($model['total']), 'label' => 'Total HT'],
            ['attribute' => 'total_tax', 'value' => fn($model) => formatCurrency($model['total_tax']), 'label' => 'Taxes'],
            ['attribute' => 'total_ttc', 'value' => fn($model) => formatCurrency($model['total_ttc']), 'label' => 'Total TTC'],
        ],
    ]);
}

// Formater une date
function formatDate($date)
{
    return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
}

// Formater une valeur monétaire
function formatCurrency($value)
{
    return Yii::$app->formatter->asCurrency($value, 'EUR');
}
