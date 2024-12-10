<?php
use Shopify\ApiVersion;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Graphql;
use Shopify\Context;
use yii\helpers\Html;

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

$scopes = 'read_analytics, read_assigned_fulfillment_orders, read_customer_events, read_customers, read_discounts, read_discovery, read_draft_orders, read_files, read_fulfillments, read_gdpr_data_request, read_gift_cards, read_inventory, read_legal_policies, read_locations, read_marketing_events, read_merchant_managed_fulfillment_orders, read_online_store_navigation, read_online_store_pages, read_order_edits, read_orders, read_packing_slip_templates, read_payment_customizations, read_payment_terms, read_pixels, read_price_rules, read_product_feeds, read_product_listings, read_products, read_publications, read_purchase_options, read_reports, read_resource_feedbacks, read_returns, read_channels, read_script_tags, read_shipping, read_locales, read_markets, read_shopify_payments_accounts, read_shopify_payments_bank_accounts, read_shopify_payments_disputes, read_shopify_payments_payouts, read_content, read_themes, read_third_party_fulfillment_orders, read_translations, read_all_cart_transforms, read_cart_transforms, read_custom_fulfillment_services, read_delivery_customizations, read_fulfillment_constraint_rules, read_gates';

$apiVersion = '2024-10';

Context::initialize($api, $sct, Html::encode($scopes), $url, new FileSessionStorage('/tmp/php_sessions'), Html::encode($apiVersion));

$client = new Graphql($url, $pwd);


$query = <<<QUERY
    query {
        productVariants(first:10, query: "sku:$ref") {
            nodes {
                inventoryItem {
                  unitCost {
                    amount
                  }
                }
              }
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

$response = $client->query(["query" => $query]);

$contents = $response->getBody()->getContents();


$a = json_decode($contents);

file_put_contents('products.json', print_r($response));

$product_gid = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'product'}->{'id'};
$variant_gid = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'id'};
$sku = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'sku'};
$title = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'product'}->{'title'};
$price = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'price'};
$quantity = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'inventoryQuantity'};

$date_add = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'createdAt'};
$date_upd = $a->{'data'}->{'productVariants'}->{'edges'}[0]->{'node'}->{'updatedAt'};


$pid = explode("/", $product_gid);
$vid = explode("/", $variant_gid);
// Product ID = $pid[4]
// Variant ID = $vid[4]

$url_product = "https://$api:$pwd@$url/admin/api/$apiVersion/products/" . $pid[4] . ".json";
$url_variant = "https://$api:$pwd@$url/admin/api/$apiVersion/variants/" . $vid[4] . ".json";

?>
<div class="prestashop-products-results">

    <main class="d-flex flex-nowrap">

        <div class="container-fluid">
            <p><a href="<?= $url_product; ?>" target="_blank" rel="noopener noreferrer">
                    <?= $url_product; ?>
                </a></p>
            <p>
                <?= $url_variant; ?>
            </p>
            <div class="accordion" id="accordionProduct">

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            Détail du produit
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1"
                        data-bs-parent="#accordionProduct">
                        <div class="accordion-body">
                            <table class="table table-striped table-hover">
                                <thead class="thead-inverse">
                                    <tr class="head-table-color">
                                        <th scope="col">ID</th>
                                        <th scope="col">Référence</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prix de vente</th>
                                        <th scope="col">Quantité</th>
                                        <th scope="col">Création</th>
                                        <th scope="col">Mise à jour</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <?php echo $pid[4]; ?>
                                        </td>
                                        <td>
                                            <?php echo $sku; ?>
                                        </td>
                                        <td>
                                            <?php echo $title; ?>
                                        </td>
                                        <td>
                                            <?php echo $price; ?> &euro;
                                        </td>
                                        <td>
                                            <?php echo $quantity ?>
                                        </td>
                                        <td>
                                            <?php echo $date_add; ?>
                                        </td>
                                        <td>
                                            <?php echo $date_upd; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-item quantity-item">
                    <h2 class="accordion-header" id="heading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                            Quantité en stock
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2"
                        data-bs-parent="#accordionProduct">
                        <div class="accordion-body quantity-body">

                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                            Déclinaison
                        </button>
                    </h2>
                    <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3"
                        data-bs-parent="#accordionProduct">
                        <div class="accordion-body">

                        </div>
                    </div>
                </div>
            </div>
    </main>
</div>