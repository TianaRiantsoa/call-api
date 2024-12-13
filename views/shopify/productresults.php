<?php

use Shopify\ApiVersion;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = 'Shopify | ' . Html::encode($ref) . ' | ' . Html::encode($model->url);
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de produit', 'url' => ['products', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);
$url = Html::encode($model->url);
$api = Html::encode($model->api_key);
$pwd = Html::encode($model->password);
$sct = Html::encode($model->secret_key);
$ref = Html::encode($ref);
$type = Html::encode($type);

// $scopes = 'read_analytics, read_assigned_fulfillment_orders, read_customer_events, read_customers, read_discounts, read_discovery, read_draft_orders, read_files, read_fulfillments, read_gdpr_data_request, read_gift_cards, read_inventory, read_legal_policies, read_locations, read_marketing_events, read_merchant_managed_fulfillment_orders, read_online_store_navigation, read_online_store_pages, read_order_edits, read_orders, read_packing_slip_templates, read_payment_customizations, read_payment_terms, read_pixels, read_price_rules, read_product_feeds, read_product_listings, read_products, read_publications, read_purchase_options, read_reports, read_resource_feedbacks, read_returns, read_channels, read_script_tags, read_shipping, read_locales, read_markets, read_shopify_payments_accounts, read_shopify_payments_bank_accounts, read_shopify_payments_disputes, read_shopify_payments_payouts, read_content, read_themes, read_third_party_fulfillment_orders, read_translations, read_all_cart_transforms, read_cart_transforms, read_custom_fulfillment_services, read_delivery_customizations, read_fulfillment_constraint_rules, read_gates';

// // $apiVersion = ApiVersion::LATEST;

// Context::initialize($api, $sct, Html::encode($scopes), $url, new FileSessionStorage('/tmp/php_sessions'));

// $client = new Graphql($url, $pwd);

require('function.php');

$init = InitShopify($url, $api, $pwd, $sct);
if (isset($type) && $type == 'simple') {
    $query = <<<QUERY
    query {
        products(first: 250, query: "sku:$ref") {
            edges {
                node {
                    id
                    title
                    createdAt
                    updatedAt
                    status                    
                    variants(first: 250) {
                        edges {
                            node {
                                id
                                sku
                                price
                                title
                                inventoryQuantity
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

    $products = ArrayHelper::getValue($data, 'data.products.edges', []);

    $gridData = [];
    $id = Yii::$app->request->get('id');
    $ref = Yii::$app->request->get('ref');
    $globalSearch = Yii::$app->request->get('globalSearch', ''); // Récupération de la recherche globale

    foreach ($products as $product) {
        $node = $product['node'];
        $variants = ArrayHelper::getValue($node, 'variants.edges', []);

        foreach ($variants as $variant) {
            $variantNode = $variant['node'];
            $row = [
                'product_id' => explode("/", $node['id'])[4],
                'product_title' => $node['title'],
                'product_status' => $node['status'] ?? 'Indéfini',
                'variant_id' => explode("/", $variantNode['id'])[4],
                'variant_sku' => $variantNode['sku'],
                'variant_title' => $variantNode['title'],
                'variant_price' => $variantNode['price'] . ' €',
                'variant_quantity' => $variantNode['inventoryQuantity'],
                'date_add' => (new DateTime($node['createdAt'], new DateTimeZone('UTC')))
                    ->setTimezone(new DateTimeZone('Europe/Paris'))
                    ->format("d/m/Y") . "<br>" .
                    (new DateTime($node['createdAt'], new DateTimeZone('UTC')))
                    ->setTimezone(new DateTimeZone('Europe/Paris'))
                    ->format("H:i:s"),
                'date_upd' => (new DateTime($node['updatedAt'], new DateTimeZone('UTC')))
                    ->setTimezone(new DateTimeZone('Europe/Paris'))
                    ->format("d/m/Y") . "<br>" .
                    (new DateTime($node['updatedAt'], new DateTimeZone('UTC')))
                    ->setTimezone(new DateTimeZone('Europe/Paris'))
                    ->format("H:i:s"),
            ];

            // Ajout du filtre global (case insensitive)
            if (stripos(json_encode($row), $globalSearch) !== false || empty($globalSearch)) {
                $gridData[] = $row;
            }
        }
    }

    $dataProvider = new ArrayDataProvider([
        'allModels' => $gridData,
        'pagination' => [
            'pageSize' => 25,
        ],
    ]);
?>

    <div class="product-index">
        <h1><?= Html::encode('Liste des Produits Shopify') ?></h1>

        <!-- Barre de recherche -->
        <p>
            <?= Html::input('text', 'globalSearch', $globalSearch, [
                'id' => 'globalSearchInput',
                'class' => 'form-control',
                'placeholder' => 'Rechercher...',
            ]) ?>
        </p>

        <?php Pjax::begin(['id' => 'productGrid']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'date_add',
                    'format' => 'raw',
                    'label' => 'Création',
                ],
                [
                    'attribute' => 'product_id',
                    'label' => 'ID Produit',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api, $pwd) {
                        return Html::a(
                            $model['product_id'],

                            "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/products/{$model['product_id']}.json",
                            ['target' => '_blank', 'encode' => false, 'class' => 'badge badge-pill badge-primary']
                        );
                    }
                ],
                [
                    'attribute' => 'product_status',
                    'label' => 'Statut',
                ],
                [
                    'attribute' => 'product_title',
                    'label' => 'Nom du produit',
                    'value' => function ($model) {
                        // Assure-toi que le nom du produit existe
                        if (isset($model['product_title'])) {
                            // Découper le nom du produit en mots
                            $words = explode(' ', $model['product_title']);

                            // Regrouper les mots en groupes de 4
                            $chunks = array_chunk($words, 3);

                            // Rejoindre chaque groupe avec un <br> pour créer un saut de ligne
                            return implode('<br>', array_map(function ($chunk) {
                                return implode(' ', $chunk);
                            }, $chunks));
                        }

                        return '';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'variant_id',
                    'label' => 'ID Variante',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api, $pwd) {
                        return Html::a(
                            $model['variant_id'],

                            "https://" . $api . ":" . $pwd . "@" . $url . "/admin/api/" . ApiVersion::LATEST . "/variants/{$model['variant_id']}.json",
                            ['target' => '_blank', 'encode' => false, 'class' => 'badge badge-pill badge-success']
                        );
                    }
                ],
                [
                    'attribute' => 'variant_sku',
                    'label' => 'SKU',
                ],
                [
                    'attribute' => 'variant_price',
                    'label' => 'Prix de la variante',
                ],
                [
                    'attribute' => 'variant_quantity',
                    'label' => 'Quantité de la variante',
                ],
                [
                    'attribute' => 'date_upd',
                    'format' => 'raw',
                    'label' => 'Mise à jour', //2022-05-25T13:29:18Z
                ],
            ],
        ]); ?>
    <?php
    Pjax::end();
}
    ?>
    </div>

    <?php
    $script = <<<JS
// Recherche dynamique avec AJAX
$('#globalSearchInput').on('keyup', function() {
    let currentUrl = new URL(window.location.href);
    let searchParams = new URLSearchParams(currentUrl.search);
    searchParams.set('globalSearch', $(this).val());

    $.pjax.reload({
        container: '#productGrid',
        url: currentUrl.pathname + '?' + searchParams.toString(),
        timeout: 2000
    });
});
JS;
    $this->registerJs($script);
    ?>