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

$this->title = 'Commandes | ' . Html::encode($ref) . ' | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de commande', 'url' => ['orders', 'id' => $model->id]];
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
$db_id = $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'url:url',
        'api_key',
    ],
]);


try {
    // Initialiser la connexion à l'API PrestaShop
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

    // Récupérer la commande spécifique
    $xmlOrders = $webService->get(['resource' => 'orders', 'id' => $ref]);

    // Récupérer les adresses, les produits, et les clients
    $xmlAddresses = $webService->get(['resource' => 'addresses']);
    $xmlProducts = $webService->get(['resource' => 'products']);

    // Initialiser les tableaux pour stocker les résultats
    $orders = [];
    $customers = [];
    $addresses = [];
    $products = [];

    // Traiter les données des commandes
    foreach ($xmlOrders->order as $order) {
        // Récupérer les informations de la commande

        $state = $order->current_state;
        $xmlState = $webService->get(['resource' => 'order_states', 'id' => $state]);
        $stateName = (string) $xmlState->order_state->name->language;


        $orders[] = [
            'id' => (string) $order->id,
            'current_state' => '(' . (string) $order->current_state . ') ' . (string) $stateName,
            'customer_id' => (string) $order->id_customer,
            'total_paid' => (string) $order->total_paid,
            'total_shipping_tax_incl' => (string) $order->total_shipping_tax_incl,
            'id_address_invoice' => (string) $order->id_address_invoice,
            'id_address_delivery' => (string) $order->id_address_delivery,
            'payment' => (string) $order->payment,
            'reference' => (string) $order->reference,
            'date_add' => (string) $order->date_add,
            'date_upd' => (string) $order->date_upd,
        ];

        // Traiter les informations du client
        $customerId = (string) $order->id_customer;
        $xmlCustomers = $webService->get(['resource' => 'customers', 'id' => $customerId]);
        foreach ($xmlCustomers->customer as $customer) {
            $customers[] = [
                'customer_id' => (string) $customer->id,
                'first_name' => (string) $customer->firstname,
                'last_name' => (string) $customer->lastname,
                'email' => (string) $customer->email,
                'date_add' => (string) $customer->date_add,
                'date_upd' => (string) $customer->date_upd,
            ];
        }

        $addressInvoiceId = (string) $order->id_address_invoice;
        $xmlAddressInvoice = $webService->get(['resource' => 'addresses', 'id' => $addressInvoiceId]);

        $addresses = [];
        foreach ($xmlAddressInvoice->address as $address) {
            $countryId = (string) $address->id_country;
            $xmlCountry = $webService->get(['resource' => 'countries', 'id' => $countryId]);
            $countryName = (string) $xmlCountry->country->iso_code;

            // Concaténer les informations d'adresse
            $fullAddress = (string) $address->address1;
            if (!empty($address->address2)) {
                $fullAddress .= ', ' . (string) $address->address2;
            }
            $fullAddress .= ', ' . (string) $address->postcode . ' ' . (string) $address->city . ', ' . $countryName;

            $addresses[] = [
                'address_type' => 'Facturation', // Indiquer qu'il s'agit de l'adresse de facturation
                'id' => $addressInvoiceId,
                'alias' => $address->alias,
                'company' => $address->company,
                'first_name' => (string) $address->firstname,
                'last_name' => (string) $address->lastname,
                'address' => (string) $fullAddress,
                'phone' => (string) $address->phone,
                'vat_number' => (string) $address->vat_number,
            ];
        }

        // Récupérer les adresses de livraison
        $addressDeliveryId = (string) $order->id_address_delivery;
        $xmlAddressDelivery = $webService->get(['resource' => 'addresses', 'id' => $addressDeliveryId]);

        foreach ($xmlAddressDelivery->address as $address) {
            $countryId = (string) $address->id_country;
            $xmlCountry = $webService->get(['resource' => 'countries', 'id' => $countryId]);
            $countryName = (string) $xmlCountry->country->iso_code;

            // Concaténer les informations d'adresse
            $fullAddress = (string) $address->address1;
            if (!empty($address->address2)) {
                $fullAddress .= ', ' . (string) $address->address2;
            }
            $fullAddress .= ', ' . (string) $address->postcode . ' ' . (string) $address->city . ', ' . $countryName;
            $addresses[] = [
                'address_type' => 'Livraison', // Indiquer qu'il s'agit de l'adresse de livraison
                'id' => $addressDeliveryId,
                'alias' => $address->alias,
                'company' => $address->company,
                'first_name' => (string) $address->firstname,
                'last_name' => (string) $address->lastname,
                'address' => (string) $fullAddress,
                'phone' => (string) $address->phone,
                'vat_number' => (string) $address->vat_number,
            ];
        }

        // Conversion en tableau pour ArrayDataProvider
        $addressDataProvider = new ArrayDataProvider([
            'allModels' => $addresses,  // Passer le tableau de données à ArrayDataProvider
        ]);

        // Récupérer les produits associés à la commande
        if (isset($order->associations->order_rows->order_row)) {
            foreach ($order->associations->order_rows->order_row as $product) {
                // Récupérer les informations de base du produit commandé
                $orderRowId = (string) $product->id;
                $productData = [
                    'order_row' => $orderRowId,
                    'id_product_attribute' => $product->product_attribute_id,
                    'product_reference' => (string) $product->product_reference,
                    'product_name' => (string) $product->product_name,
                    'quantity' => (string) $product->product_quantity,
                    'total' => (string) $product->unit_price_tax_incl,
                ];

                // Récupérer les détails supplémentaires de `order_details`
                try {
                    $orderDetailsOpt = [
                        'resource' => 'order_details',
                        'filter[id]' => $orderRowId, // Filtrer avec l'ID du `order_row`
                        'display' => 'full',
                    ];

                    $orderDetailsXml = $webService->get($orderDetailsOpt);
                    $orderDetails = $orderDetailsXml->order_details->children();

                    foreach ($orderDetails as $detail) {
                        $productData['total_price_tax_excl'] = (float) $detail->total_price_tax_excl;
                        $productData['unit_price_tax_excl'] = (float) $detail->unit_price_tax_excl;
                        $productData['total_price_tax_incl'] = (float) $detail->total_price_tax_incl;
                        $productData['unit_price_tax_incl'] = (float) $detail->unit_price_tax_incl;

                        // Vérification et récupération de l'ID de la taxe
                        if (isset($detail->associations->taxes->tax)) {
                            $taxElement = $detail->associations->taxes->tax;

                            // Vérifier si l'élément `tax` contient un attribut `xlink:href` ou un ID
                            if (isset($taxElement->attributes()->{'xlink:href'})) {
                                $taxHref = (string) $taxElement->attributes()->{'xlink:href'};
                                $taxId = basename($taxHref); // Extraire l'ID de l'URL
                            } elseif (isset($taxElement->id)) {
                                $taxId = (int) $taxElement->id; // Si l'ID est directement présent
                            } else {
                                $taxId = null; // Pas d'ID de taxe trouvé
                            }

                            if ($taxId) {
                                // Récupérer les détails de la taxe via l'API
                                $taxOpt = [
                                    'resource' => 'taxes',
                                    'id' => $taxId,
                                    'language' => $languageId,
                                ];

                                try {
                                    $tax = $webService->get($taxOpt);
                                    $taxName = (string) $tax->tax->name->language;
                                    $taxRate = (float) $tax->tax->rate;

                                    $productData['tax'] = $taxRate . "% (" . $taxName . ")";
                                } catch (Exception $e) {
                                    // Gérer les erreurs de récupération de la taxe
                                    $productData['tax'] = 'Erreur lors de la récupération des taxes';
                                }
                            } else {
                                $productData['tax'] = 'Pas de taxe trouvé';
                            }
                        } else {
                            $productData['tax'] = 'Pas de taxe associé';
                        }
                    }
                } catch (Exception $e) {
                    // Gestion des erreurs API
                    $productData['total_price_tax_excl'] = '';
                    $productData['unit_price_tax_excl'] = '';

                    $productData['total_price_tax_incl'] = '';
                    $productData['unit_price_tax_incl'] = '';

                    $productData['tax'] = 'Erreur lors de la récupération des taxes';
                }

                // Ajouter les données fusionnées dans le tableau des produits
                $products[] = $productData;
            }
        }
    }

    // Fournir les données sous forme de ArrayDataProvider
    $orderDataProvider = new ArrayDataProvider([
        'allModels' => $orders,
        'pagination' => ['pageSize' => 10],
    ]);

    $customerDataProvider = new ArrayDataProvider([
        'allModels' => $customers,
        'pagination' => ['pageSize' => 10],
    ]);

    $billingAddressDataProvider = new ArrayDataProvider([
        'allModels' => $xmlAddressInvoice,
        'pagination' => ['pageSize' => 10],
    ]);

    $shippingAddressDataProvider = new ArrayDataProvider([
        'allModels' => $xmlAddressDelivery,
        'pagination' => ['pageSize' => 10],
    ]);

    $productDataProvider = new ArrayDataProvider([
        'allModels' => $products,
        'pagination' => ['pageSize' => 10],
    ]);
} catch (PrestaShopWebserviceException $e) {
    echo 'Erreur : ' . $e->getMessage();
}

// $site = $url . '/api/orders/' . $ref . '?ws_key=' . $api;
// echo Html::a('Afficher le XML de la commande', $site, ['class' => 'btn btn-success', 'target' => '_blank']);
?>
<h3>Détails des Commandes</h3>
<?php
// Afficher les commandes
echo GridView::widget([
    'dataProvider' => $orderDataProvider,
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ID',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['id'],
                    $url . "/api/orders/{$model['id']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            }  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'current_state',
            'label' => 'Statut de la commande',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'customer_id',
            'label' => 'ID du client',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'payment',
            'label' => 'Moyen de paiement',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'reference',
            'label' => 'Référence',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'total_paid',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['total_paid'], 'EUR');
            },
            'label' => 'Montant total payé',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'total_shipping_tax_incl',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['total_shipping_tax_incl'], 'EUR');
            },
            'label' => 'Frais de livraison',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'date_add',
            'value' => function ($model) {
                $date = is_array($model) ? $model['date_add'] : $model->date_created;
                return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
            },
            'label' => 'Création',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'date_upd',
            'value' => function ($model) {
                $date = is_array($model) ? $model['date_upd'] : $model->date_created;
                return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
            },
            'label' => 'Mise à jour',  // Nouveau nom de la colonne
        ],
    ],
]);

// Afficher les clients
echo '<h3>Détails du Client</h3>';
echo GridView::widget([
    'dataProvider' => $customerDataProvider,
    'columns' => [
        [
            'attribute' => 'customer_id',
            'label' => 'ID du client',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['customer_id'],
                    $url . "/api/customers/{$model['customer_id']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'first_name',
            'label' => 'Prénom',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['first_name'],
                    $url . "/api/customers/{$model['customer_id']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            }  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'last_name',
            'label' => 'Nom',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['last_name'],
                    $url . "/api/customers/{$model['customer_id']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            } // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'email',
            'label' => 'Email',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['email'],
                    $url . "/api/customers/{$model['customer_id']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            }  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'date_add',
            'value' => function ($model) {
                $date = is_array($model) ? $model['date_add'] : $model->date_created;
                return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
            },
            'label' => 'Création',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'date_upd',
            'value' => function ($model) {
                $date = is_array($model) ? $model['date_upd'] : $model->date_created;
                return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
            },
            'label' => 'Mise à jour',  // Nouveau nom de la colonne
        ],
    ],
]);

// Afficher les adresses de facturation et livraison
echo '<h3>Adresse de facturation et livraison</h3>';
echo GridView::widget([
    'dataProvider' => $addressDataProvider,
    'columns' => [
        [
            'attribute' => 'address_type',
            'label' => 'Type d\'adresse',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['address_type'],
                    $url . "/api/addresses/{$model['id']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            }  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'id',
            'label' => 'ID de l\'adresse',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'alias',
            'label' => 'Alias',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'company',
            'label' => 'Société',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'first_name',
            'label' => 'Prénom',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'last_name',
            'label' => 'Nom',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'vat_number',
            'label' => 'N° de TVA Intracom',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'address',
            'label' => 'Adresse complet',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'phone',
            'label' => 'Téléphone',  // Nouveau nom de la colonne
        ],
    ],
]);

// Afficher les produits commandés
echo '<h3>Détails des Produits Commandés</h3>';
echo GridView::widget([
    'dataProvider' => $productDataProvider,
    'columns' => [
        [
            'attribute' => 'order_row',
            'label' => 'ID Order Details',
            'format' => 'raw',
            'value' => function ($model) use ($url, $api) {
                return Html::a(
                    $model['order_row'],
                    $url . "/api/order_details/{$model['order_row']}?&ws_key=" . $api,
                    ['target' => '_blank', 'encode' => false]
                );
            }
        ],
        [
            'attribute' => 'product_reference',
            'label' => 'Référence Produit',
            'format' => 'raw',
            'value' => function ($model) use ($db_id) {
                // Vérifier si id_product_attribute est 0
                if ($model['id_product_attribute'] == 0) {
                    // Générer l'URL pour le produit simple
                    $url = Url::to([
                        'productresults',
                        'id' => $db_id,  // Utilisation du $db_id déjà défini
                        'ref' => $model['product_reference'],
                        'type' => 'simple',
                        'variation_type' => ''
                    ]);
                    $typeLabel = 'Simple';
                }
                // Vérifier si id_product_attribute est 1
                else {
                    // Générer l'URL pour la variation (type = 'variation', variation_type = 'child')
                    $url = Url::to([
                        'productresults',
                        'id' => $db_id,
                        'ref' => $model['product_reference'],
                        'type' => 'variation',
                        'variation_type' => 'child'
                    ]);
                    $typeLabel = 'Déclinaison';
                }

                // Afficher la référence produit avec le lien généré
                return Html::a($model['product_reference'] . ' (' . $typeLabel . ')', $url, [
                    'target' => '_blank',
                    'encode' => false,
                ]);
            },
        ],
        [
            'attribute' => 'product_name',
            'label' => 'Nom du produit',
            'format' => 'raw',
            'value' => function ($model) use ($db_id) {
                // Initialiser le titre formaté
                $formattedName = '';

                // Vérifier si le nom du produit existe
                if (isset($model['product_name'])) {
                    // Découper le nom du produit en mots
                    $words = explode(' ', $model['product_name']);

                    // Regrouper les mots en groupes de 4
                    $chunks = array_chunk($words, 4);

                    // Rejoindre chaque groupe avec un <br> pour créer un saut de ligne
                    $formattedName = implode('<br>', array_map(function ($chunk) {
                        return implode(' ', $chunk);
                    }, $chunks));
                }

                // Générer l'URL et le label en fonction de `id_product_attribute`
                if ($model['id_product_attribute'] == 0) {
                    // Générer l'URL pour le produit simple
                    $url = Url::to([
                        'productresults',
                        'id' => $db_id,
                        'ref' => $model['product_reference'],
                        'type' => 'simple',
                        'variation_type' => ''
                    ]);
                    $typeLabel = 'Simple';
                } else {
                    // Générer l'URL pour la variation
                    $url = Url::to([
                        'productresults',
                        'id' => $db_id,
                        'ref' => $model['product_reference'],
                        'type' => 'variation',
                        'variation_type' => 'child'
                    ]);
                    $typeLabel = 'Déclinaison';
                }

                // Ajouter le type au nom formaté
                $formattedNameWithType = $formattedName . ' (' . $typeLabel . ')';

                // Retourner le titre formaté avec le lien
                return Html::a($formattedNameWithType, $url, [
                    'target' => '_blank',
                    'encode' => false,
                ]);
            }

        ],
        [
            'attribute' => 'quantity',
            'label' => 'Quantité',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'unit_price_tax_excl',
            'label' => 'P.U HT',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['unit_price_tax_excl'], 'EUR');
            }, // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'tax',
            'label' => 'TVA',  // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'unit_price_tax_incl',
            'label' => 'P.U TTC',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['unit_price_tax_incl'], 'EUR');
            }, // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'total_price_tax_excl',
            'label' => 'Total HT',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['total_price_tax_excl'], 'EUR');
            }, // Nouveau nom de la colonne
        ],
        [
            'attribute' => 'total_price_tax_incl',
            'label' => 'Total TTC',
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model['total_price_tax_incl'], 'EUR');
            }, // Nouveau nom de la colonne
        ],
        // [
        //     'attribute' => 'total',
        //     'value' => function ($model) {
        //         return Yii::$app->formatter->asCurrency($model['total'], 'EUR');
        //     },
        //     'label' => 'Prix TTC',  // Nouveau nom de la colonne
        // ],
    ],
]);
