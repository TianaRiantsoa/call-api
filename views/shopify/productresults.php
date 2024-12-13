<?php
// use Shopify\ApiVersion;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Graphql;
use Shopify\Context;
use yii\helpers\Html;
use SPF\init;
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

// $scopes = 'read_analytics, read_assigned_fulfillment_orders, read_customer_events, read_customers, read_discounts, read_discovery, read_draft_orders, read_files, read_fulfillments, read_gdpr_data_request, read_gift_cards, read_inventory, read_legal_policies, read_locations, read_marketing_events, read_merchant_managed_fulfillment_orders, read_online_store_navigation, read_online_store_pages, read_order_edits, read_orders, read_packing_slip_templates, read_payment_customizations, read_payment_terms, read_pixels, read_price_rules, read_product_feeds, read_product_listings, read_products, read_publications, read_purchase_options, read_reports, read_resource_feedbacks, read_returns, read_channels, read_script_tags, read_shipping, read_locales, read_markets, read_shopify_payments_accounts, read_shopify_payments_bank_accounts, read_shopify_payments_disputes, read_shopify_payments_payouts, read_content, read_themes, read_third_party_fulfillment_orders, read_translations, read_all_cart_transforms, read_cart_transforms, read_custom_fulfillment_services, read_delivery_customizations, read_fulfillment_constraint_rules, read_gates';

// // $apiVersion = ApiVersion::LATEST;

// Context::initialize($api, $sct, Html::encode($scopes), $url, new FileSessionStorage('/tmp/php_sessions'));

// $client = new Graphql($url, $pwd);

$init = init::InitShopify($url,$api, $pwd, $sct);

$query = <<<QUERY
    query {
        products(first:10, query: "sku:$ref") {            
            edges {                
                node {
                    product {
                        id
                        title
                        }
                    id
                    sku
                    price
                    inventoryQuantity
                    title
                    createdAt
                    updatedAt
                    }                    
                }
            }
      
        }
    QUERY;

    

$response = $init->query(["query" => $query]);

$contents = $response->getBody()->getContents();

