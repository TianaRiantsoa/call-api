<?php

use Shopify\ApiVersion;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Graphql;
use Shopify\Context;
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
$ref = Html::encode($ref);

$scopes = 'read_analytics, read_assigned_fulfillment_orders, read_customer_events, read_customers, read_discounts, read_discovery, read_draft_orders, read_files, read_fulfillments, read_gdpr_data_request, read_gift_cards, read_inventory, read_legal_policies, read_locations, read_marketing_events, read_merchant_managed_fulfillment_orders, read_online_store_navigation, read_online_store_pages, read_order_edits, read_orders, read_packing_slip_templates, read_payment_customizations, read_payment_terms, read_pixels, read_price_rules, read_product_feeds, read_product_listings, read_products, read_publications, read_purchase_options, read_reports, read_resource_feedbacks, read_returns, read_channels, read_script_tags, read_shipping, read_locales, read_markets, read_shopify_payments_accounts, read_shopify_payments_bank_accounts, read_shopify_payments_disputes, read_shopify_payments_payouts, read_content, read_themes, read_third_party_fulfillment_orders, read_translations, read_all_cart_transforms, read_cart_transforms, read_custom_fulfillment_services, read_delivery_customizations, read_fulfillment_constraint_rules, read_gates';

$apiVersion = ApiVersion::LATEST;

Context::initialize($api, $sct, Html::encode($scopes), $url, new FileSessionStorage('/tmp/php_sessions'), Html::encode($apiVersion));

$client = new Graphql($url, $pwd);

$n = count(str_split($ref));

if ($n != 13) {
  $q = "name:$ref";
} else {
  $q = "id:$ref";
}
$query = <<<QUERY
  query {
    orders(query: "$q", first: 3) {
      edges {
        node {
          id
          createdAt
          updatedAt
          paymentGatewayNames
          totalPrice
          totalTax
          fulfillments {
            status
            location {
              id
            }
          }
          name
          totalShippingPrice
          billingAddress {
            company
            address1
            address2
            city
            country
            countryCode
            countryCodeV2
            firstName
            lastName
            id
            phone
            name
            zip
          }
          shippingAddress {
            company
            address1
            address2
            city
            country
            countryCode
            countryCodeV2
            firstName
            lastName
            id
            phone
            name
            zip
          }
          customer {
            displayName
            id
            firstName
            lastName
            email
            phone
            orders {
              nodes {
                id
              }
            }
            addresses {
              company
              address1
              address2
              city
              country
              countryCode
              countryCodeV2
              firstName
              lastName
              id
              phone
              name
              zip
            }
          }
          lineItems(first: 100) {
            edges {
              node {
                id
                product {
                  id
                }
                sku
                variant {
                  id
                  displayName
                  price
                  sku
                  title
                  createdAt
                  updatedAt
                }
                title
                quantity
                name
              }
            }
          }
        }
      }
    }
  }
QUERY;

$response = $client->query(["query" => $query]);

$contents = $response->getBody()->getContents();

$contenu = json_decode($contents);

$node = $contenu->{'data'}->{'orders'}->{'edges'}[0]->{'node'};

// $location = $node->{'fulfillments'}[0]->{'location'}->{'id'};
// $l_id = explode("/", $location);

$o_gid = $node->{'id'};
$oid = explode("/", $o_gid);

/******************************************************************
$check = file_get_contents("https://$api:$pwd@$url/admin/api/$apiVersion/orders/$oid[4].json");
file_put_contents(substr($node->{'name'}, 7) . ".json", $check);

$f = substr($node->{'name'}, 7) . ".json";

$file = Yii::getAlias('@web/SDK_b3dd4cf24e2923c60bf0678398d6a8934d40781a_orders_get_after.ps1');

$cmd = "powershell.exe -ExecutionPolicy Bypass -File ." . $file . " -data $f";
$res = shell_exec($cmd);

file_put_contents("res-" . substr($node->{'name'}, 7) . ".json", $res);
*****************************************************************/

$total_price = $node->{'totalPrice'};

$items = $node->{'lineItems'};

$date_add = $node->{'createdAt'};
$date_upd = $node->{'updatedAt'};

$paiement = $node->{'paymentGatewayNames'}[0];
?>
<div class="shopify-orders-results">
  <main class="d-flex flex-nowrap">
    <div class="container-fluid">
      <div class="accordion" id="accordionOrder">

        <!-- Information Commande -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading1">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1"
              aria-expanded="true" aria-controls="collapse1">
              Information sur la commande
            </button>
          </h2>
          <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1"
            data-bs-parent="#accordionOrder">
            <div class="accordion-body">
              <table class="table table-striped table-hover">
                <thead class="thead-inverse">
                 <tr class="head-table-color">
                    <th scope="col">ID de la commande</th>
                    <th scope="col">N° court</th>
                    <th scope="col">Mode de paiement</th>
                    <th scope="col">Total HT</th>
                    <!-- <th scope="col">ID Location</th> -->
                    <th scope="col">Création</th>
                    <th scope="col">Mise à jour</th>
                  </tr>
                </thead>
                <?php
                ?>
                <tbody>
                  <tr>
                    <td><a class="link-offset-2 link-underline link-underline-opacity-0"
                        href="<?php echo "https://$api:$pwd@$url/admin/api/$apiVersion/orders/$oid[4].json"; ?>">
                        <?= $oid[4]; ?>
                      </a></td>
                    <td>
                      <?php echo Html::encode($node->{'name'}); ?>
                    </td>
                    <td>
                      <?php echo Html::encode($paiement); ?>
                    </td>
                    <td>
                      <?php echo Html::encode($total_price) . " &euro;"; ?>
                    </td>
                    <!-- <td> -->
                      <?php // echo Html::encode($l_id[4]); ?>
                    <!-- </td> -->
                    <td>
                      <?php echo Html::encode($date_add); ?>
                    </td>
                    <td>
                      <?php echo Html::encode($date_upd); ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Liste des produits dans la commande -->

        <?php
        if (!empty ($items)) {
          $i = 0;
          ?>

          <div class="accordion-item">
            <h2 class="accordion-header" id="heading4">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4"
                aria-expanded="true" aria-controls="collapse4">
                Liste des produits dans la commande
              </button>
            </h2>
            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4"
              data-bs-parent="#accordionOrder">
              <div class="accordion-body">
                <table class="table table-striped table-hover">
                  <thead class="thead-inverse">
                    <tr class="head-table-color">
                      <th scope="col">ID</th>
                      <th scope="col">Référence</th>
                      <th scope="col">Nom</th>
                      <th scope="col">Quantité</th>
                      <th scope="col">Prix HT</th>
                    </tr>
                  </thead>
                  <?php
                  //Interrogation de produit
                  for ($i = 0; $i <= sizeof($items->{'edges'}) - 1; $i++) {
                    $quantity = $items->{'edges'}[$i]->{'node'}->{'quantity'};
                    $sku = $items->{'edges'}[$i]->{'node'}->{'sku'};
                    $name = $items->{'edges'}[$i]->{'node'}->{'name'};
                    $price = $items->{'edges'}[$i]->{'node'}->{'variant'}->{'price'};
                    $p_gid = $items->{'edges'}[$i]->{'node'}->{'variant'}->{'id'};
                    $pid = explode("/", $p_gid);
                    ?>
                    <tbody>
                      <tr>
                        <td scope='row'>
                          <?= Html::a($pid[4], ['productresults', 'id' => $model->id, 'ref' => Html::encode($sku)], ['class' => 'link-offset-2 link-underline link-underline-opacity-0']) ?>
                        </td>
                        <td>
                          <?= Html::a($sku, ['productresults', 'id' => $model->id, 'ref' => Html::encode($sku)], ['class' => 'link-offset-2 link-underline link-underline-opacity-0']) ?>
                        </td>
                        <td>
                          <?php echo Html::encode($name); ?>
                        </td>
                        <td>
                          <?php echo Html::encode($quantity); ?>
                        </td>
                        <td>
                          <?php echo Html::encode($price); ?> &euro;
                        </td>
                      </tr>
                    </tbody>
                    <?php
                  }
        }
        ?>
              </table>
            </div>
          </div>
        </div>

        <!-- À propos du client -->

      </div>
    </div>
  </main>
</div>