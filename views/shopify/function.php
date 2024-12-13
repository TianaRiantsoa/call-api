<?php

use Shopify\Context;
use Shopify\Auth\FileSessionStorage;
use yii\helpers\Html;
use Shopify\Clients\Graphql;

function InitShopify($url,$api_key,$password,$secret_key)
{    
    $scopes = 'read_analytics, read_assigned_fulfillment_orders, read_customer_events, read_customers, read_discounts, read_discovery, read_draft_orders, read_files, read_fulfillments, read_gdpr_data_request, read_gift_cards, read_inventory, read_legal_policies, read_locations, read_marketing_events, read_merchant_managed_fulfillment_orders, read_online_store_navigation, read_online_store_pages, read_order_edits, read_orders, read_packing_slip_templates, read_payment_customizations, read_payment_terms, read_pixels, read_price_rules, read_product_feeds, read_product_listings, read_products, read_publications, read_purchase_options, read_reports, read_resource_feedbacks, read_returns, read_channels, read_script_tags, read_shipping, read_locales, read_markets, read_shopify_payments_accounts, read_shopify_payments_bank_accounts, read_shopify_payments_disputes, read_shopify_payments_payouts, read_content, read_themes, read_third_party_fulfillment_orders, read_translations, read_all_cart_transforms, read_cart_transforms, read_custom_fulfillment_services, read_delivery_customizations, read_fulfillment_constraint_rules, read_gates';

    Context::initialize($api_key, $secret_key, Html::encode($scopes), $url, new FileSessionStorage(__DIR__ . '/tmp/php_sessions'));

    $client = new Graphql($url, $password);

    return $client;
}

function getProduct($url,$api_key,$password,$secret_key,$sku)
{

    $query = <<<QUERY
            query {
                products(first:10, query: "sku:$sku") {            
                    edges {                
                        node {                            
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

    $init = InitShopify($url,$api_key,$password,$secret_key);

    $response = $init->query(["query" => $query]);

    $contents = $response->getBody()->getContents();

    return $contents;
}
