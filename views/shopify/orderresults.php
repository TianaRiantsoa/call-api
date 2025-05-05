<?php

use PhpParser\Node\Stmt\TryCatch;
use Shopify\ApiVersion;
use Shopify\Exception\ShopifyException;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = 'Commandes | ' . Html::encode($ref) . ' | ' . Html::encode($model->url);
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de commande', 'url' => ['orders', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);
$url = Html::encode($model->url);
$api = Html::encode($model->api_key);
$pwd = Html::encode($model->password);
$sct = Html::encode($model->secret_key);
$type = Html::encode($type);
$ref = Html::encode($ref);

require('function.php');

try {


    $init = InitShopify($url, $api, $pwd, $sct);

    $n = count(str_split($ref));

    if (isset($type) && $type == 'court') {
        $q = "name:$ref";
    } elseif (isset($type) && $type == 'long') {
        $q = "id:$ref";
    } else {
        echo 'Erreur sur ne numéro saisi';
    }

    echo yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'url',
            'api_key',
            'password',
            'secret_key',
        ],
    ]);

    $query = <<<QUERY
        query {
            orders(query: "$q", first: 10) {
                edges {
                node {
                id
                name
                createdAt
                updatedAt
                cancelledAt
                closedAt
                displayFinancialStatus
                displayFulfillmentStatus
                fulfillments {
                    location {
                        id
                    }
                }
                paymentGatewayNames
                subtotalPrice
                taxesIncluded
                totalDiscounts
                totalTax
                shippingAddress {
                    firstName
                    lastName
                    phone
                    address1
                    address2
                    zip
                    city
                    country
                    countryCode
                    countryCodeV2
                }
                shippingLine {
                    title
                    price
                    taxLines {
                    price
                    rate
                    }
                }
                billingAddress {
                    firstName
                    lastName
                    phone
                    address1
                    address2
                    zip
                    city
                    country
                    countryCode
                    countryCodeV2
                }
                customer {
                    id
                    firstName
                    lastName
                    email
                    phone
                    createdAt
                    updatedAt            
                }
                lineItems(first: 250) {
                    edges {
                        node {
                            sku
                            name
                            quantity
                            originalUnitPrice
                            originalTotal
                            discountedUnitPrice
                            discountedTotal
                            taxable
                            product {
                            id
                            }
                            variant {
                            id
                            }                
                        }
                    }
                }
                }
            }
            }
        }
QUERY;

    $response = $init->query(["query" => $query]);

    $contents = $response->getBody()->getContents();

    $data = json_decode($contents, true);

    //$orders = ArrayHelper::getValue($data, 'data.orders.edges', []);

    // echo '<pre>';
    // print_r($data);
    // echo '<pre>';
    // exit;

    $orders = $data['data']['orders']['edges'];
    $gridDataOrders = [];
    $gridDataCustomers = [];
    $gridDataAddresses = [];
    $gridDataLineItems = [];

    foreach ($orders as $orderEdge) {
        $order = $orderEdge['node'];

        $statut = 'OPEN';

        if (!empty($order['cancelledAt'])) {
            $statut = 'CANCELLED';
        } elseif (!empty($order['closedAt'])) {
            $statut = 'CLOSED';
        } elseif ($order['displayFinancialStatus'] === 'PAID' && $order['displayFulfillmentStatus'] === 'FULFILLED') {
            $statut = 'ARCHIVED';
        }

        // Initialisation de la commande
        $gridOrder = [
            'id' => getId($order['id']),
            'name' => $order['name'],
            'createdAt' => formatDateTime($order['createdAt']),
            'updatedAt' => formatDateTime($order['updatedAt']),
            'payment_method' => !empty($order['paymentGatewayNames'][0]) ? $order['paymentGatewayNames'][0] : '',
            'status' => $statut,
            'status_payment' => $order['displayFinancialStatus'],
            'status_fulfillment' => $order['displayFulfillmentStatus'],
            'subtotal' => $order['subtotalPrice'],
            'totalDiscounts' => $order['totalDiscounts'],
            'totalTax' => $order['totalTax'],
            'location' => '', // Par défaut vide
        ];

        // Récupération du location ID si fulfillment existe
        if (!empty($order['fulfillments']) && !empty($order['fulfillments'][0]['location']['id'])) {
            $locationId = $order['fulfillments'][0]['location']['id'];

            $queryLocation = <<<QUERY
                            query {
                                location(id: "$locationId") {
                                    id
                                    name
                                }
                            }
                            QUERY;

            $res = $init->query(["query" => $queryLocation]);
            $get = $res->getBody()->getContents();
            $rep = json_decode($get, true);

            if (!empty($rep['data']['location'])) {
                $locName = htmlspecialchars($rep['data']['location']['name']);
                $gridOrder['location'] = getId($locationId) . '<br>' . $locName; // ID + nom
            }
        }

        // Ajouter une seule entrée à $gridDataOrders
        $gridDataOrders[] = $gridOrder;


        // Clients
        if (!empty($order['customer'])) {
            $gridDataCustomers[] = [
                'id' => getId($order['customer']['id']),
                'firstName' => $order['customer']['firstName'],
                'lastName' => $order['customer']['lastName'],
                'email' => $order['customer']['email'],
                'phone' => $order['customer']['phone'],
                'createdAt' => formatDateTime($order['customer']['createdAt']),
                'updatedAt' => formatDateTime($order['customer']['updatedAt']),
            ];
        }

        // Adresses
        foreach (['billingAddress' => 'Facturation', 'shippingAddress' => 'Livraison'] as $addressKey => $type) {
            if (!empty($order[$addressKey])) {
                $address = $order[$addressKey];

                $fullAddress = (string) $address['address1'];
                if (!empty($address['address2'])) {
                    $fullAddress .= ', ' . (string) $address['address2'];
                }
                $fullAddress .= ', ' . (string) $address['zip'] . ' ' . (string) $address['city'] . ', ' . (string) $address['country'] . ', ' . $address['countryCode'];

                $gridDataAddresses[] = [
                    'type' => $type,
                    'firstName' => $address['firstName'],
                    'lastName' => $address['lastName'],
                    'phone' => $address['phone'],
                    'address' => $fullAddress,
                ];
            }
        }

        // Produits commandés
        if (!empty($order['lineItems']['edges'])) {
            foreach ($order['lineItems']['edges'] as $lineItemEdge) {
                $lineItem = $lineItemEdge['node'];
                $gridDataLineItems[] = [
                    'product_id' => getId($lineItem['product']['id']),
                    'variant_id' => getId($lineItem['variant']['id']),
                    'sku' => $lineItem['sku'],
                    'name' => $lineItem['name'],
                    'quantity' => $lineItem['quantity'],
                    'originalUnitPrice' => $lineItem['originalUnitPrice'],
                    'discountedUnitPrice' => $lineItem['discountedUnitPrice'],
                    'remise' => $lineItem['originalUnitPrice'] > 0 ? number_format((($lineItem['originalUnitPrice'] - $lineItem['discountedUnitPrice']) / $lineItem['originalUnitPrice']) * 100, 2, '.', '') . ' %' : '0 %',

                    'originalTotal' => $lineItem['originalTotal'],
                    'discountedTotal' => $lineItem['discountedTotal'],
                    'taxable' => $lineItem['taxable'] ? 'Oui' : 'Non',
                ];
            }
        }
    }


    // Data Providers
    $orderProvider = new ArrayDataProvider(['allModels' => $gridDataOrders]);
    $customerProvider = new ArrayDataProvider(['allModels' => $gridDataCustomers]);
    $addressProvider = new ArrayDataProvider(['allModels' => $gridDataAddresses]);
    $lineItemProvider = new ArrayDataProvider(['allModels' => $gridDataLineItems]);

    // echo '<pre>';
    // print_r($orderProvider->getModels());
    // echo '<pre>';
    // exit;
} catch (ShopifyException $e) {
    // En cas d'erreur, afficher un message d'erreur
    Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
}



// Tableau 1 : Détail de la commande




?>
<!-- Commandes -->
<h3>Détail de la commande</h3>
<?= GridView::widget([
    'dataProvider' => $orderProvider,
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ID Long',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                if (isset($model['id'])) {
                    return Html::a(
                        $model['id'],
                        "https://" . urlencode($api) . ":" . urlencode($pwd) . "@" . urlencode($url) . "/admin/api/" . ApiVersion::LATEST . "/orders/{$model['id']}.json",
                        ['target' => '_blank', 'encode' => false]
                    );
                } else {
                    return 'ID non disponible'; // Message si l'ID est manquant
                }
            }
        ],
        [
            'attribute' => 'name',
            'label' => 'ID Court',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                if (isset($model['name'])) {
                    return Html::a(
                        $model['name'],
                        "https://" . urlencode($api) . ":" . urlencode($pwd) . "@" . urlencode($url) . "/admin/api/" . ApiVersion::LATEST . "/orders/{$model['id']}.json",
                        ['target' => '_blank', 'encode' => false]
                    );
                } else {
                    return 'Nom non disponible'; // Message si le nom est manquant
                }
            }
        ],
        [
            'attribute' => 'payment_method',
            'label' => 'Mode de paiement'
        ],
        [
            'attribute' => 'location',
            'format' => 'raw',
            'label' => 'Emplacement'
        ],
        [
            'attribute' => 'status',
            'label' => 'Statut'
        ],
        [
            'attribute' => 'status_payment',
            'label' => 'Statut de paiement'
        ],
        [
            'attribute' => 'status_fulfillment',
            'label' => 'Statut d\'expédition'
        ],
        [
            'attribute' => 'subtotal',
            'label' => 'Sous-total',
            'value' => function ($model) {
                return isset($model['subtotal']) && is_numeric($model['subtotal'])
                    ? Yii::$app->formatter->asCurrency($model['subtotal'], 'EUR')
                    : 'Non disponible';
            },
        ],
        [
            'attribute' => 'totalDiscounts',
            'label' => 'Réductions totales',
            'value' => function ($model) {
                return isset($model['totalDiscounts']) && is_numeric($model['totalDiscounts'])
                    ? Yii::$app->formatter->asCurrency($model['totalDiscounts'], 'EUR')
                    : 'Non disponible';
            },
        ],
        [
            'attribute' => 'totalTax',
            'label' => 'Taxes totales',
            'value' => function ($model) {
                return isset($model['totalTax']) && is_numeric($model['totalTax'])
                    ? Yii::$app->formatter->asCurrency($model['totalTax'], 'EUR')
                    : 'Non disponible';
            },
        ],
        [
            'attribute' => 'createdAt',
            'label' => 'Création'
        ],
        [
            'attribute' => 'updatedAt',
            'label' => 'Mis à jour'
        ],
    ],
]);

?>

<!-- Clients -->
<h3>Détail du clients</h3>
<?= GridView::widget([
    'dataProvider' => $customerProvider,
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ID Client',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['id'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/customers/{$model['id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'firstName',
            'label' => 'Prénom',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['firstName'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/customers/{$model['id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'lastName',
            'label' => 'Nom',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['lastName'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/customers/{$model['id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'email',
            'label' => 'Email',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['email'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/customers/{$model['id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'phone',
            'label' => 'Téléphone'
        ],
        [
            'attribute' => 'createdAt',
            'label' => 'Création'
        ],
        [
            'attribute' => 'updatedAt',
            'label' => 'Mis à jour'
        ],
    ],
]);
?>

<!-- Adresses -->
<h3>Adresses de Facturation et de Livraison</h3>
<?= GridView::widget([
    'dataProvider' => $addressProvider,
    'columns' => [
        [
            'attribute' => 'type',
            'label' => 'Type'
        ],
        [
            'attribute' => 'firstName',
            'label' => 'Prénom'
        ],
        [
            'attribute' => 'lastName',
            'label' => 'Nom'
        ],
        [
            'attribute' => 'phone',
            'label' => 'Téléphone'
        ],
        [
            'attribute' => 'address',
            'label' => 'Adresse Complète'
        ],
    ],
]); ?>

<!-- Produits Commandés -->
<h3>Détails des Produits Commandés</h3>
<?= GridView::widget([
    'dataProvider' => $lineItemProvider,
    'columns' => [
        [
            'attribute' => 'product_id',
            'label' => 'ID Produit',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['product_id'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/products/{$model['product_id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'variant_id',
            'label' => 'ID Variante',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['variant_id'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/variants/{$model['variant_id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'sku',
            'label' => 'SKU',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['sku'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/variants/{$model['variant_id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'name',
            'label' => 'Nom du Produit',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api, $pwd) {
                return Html::a(
                    $model['name'],
                    "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/variants/{$model['variant_id']}.json",
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'quantity',
            'label' => 'Quantité'
        ],
        [
            'attribute' => 'originalUnitPrice',
            'label' => 'P.U Original',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['originalUnitPrice'], 'EUR');
            },
        ],
        [
            'attribute' => 'discountedUnitPrice',
            'label' => 'P.U Réduit',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['discountedUnitPrice'], 'EUR');
            },
        ],
        [
            'attribute' => 'remise',
            'label' => '% remise',
            // 'value' => function ($model) {
            //     return Yii::$app->formatter->asPercent($model['remise']);
            // },
        ],
        [
            'attribute' => 'originalTotal',
            'label' => 'Total Original',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['originalTotal'], 'EUR');
            },
        ],
        [
            'attribute' => 'discountedTotal',
            'label' => 'Total Réduit',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['discountedTotal'], 'EUR');
            },
        ],
        [
            'attribute' => 'taxable',
            'label' => 'Taxable'
        ],
    ],
]); ?>