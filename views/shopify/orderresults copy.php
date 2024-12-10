<?php

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

$apiVersion = '2023-07';

Context::initialize($api, $sct, Html::encode($scopes), $url, new FileSessionStorage('/tmp/php_sessions'), Html::encode($apiVersion));

$client = new Graphql($url, $pwd);

$n = count(str_split($ref));

if ($n != 13) {
  $query = <<<QUERY
  query {
    orders(query: "name:$ref", first: 3) {
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

  file_put_contents('testname.json', $contents);

  $node = $contenu->{'data'}->{'orders'}->{'edges'}[0]->{'node'};

  $o_gid = $node->{'id'};
  $oid = explode("/", $o_gid);

  $total_p = $node->{'totalPrice'};

  echo "ID de la commande : <a target='_blank' href='https://$api:$pwd@$url/admin/api/$apiVersion/orders/$oid[4].json'>" . Html::encode($oid[4]) . "</a><br><br>";
  echo "Prix total : " . Html::encode($total_p) . " &euro;<br><br>";

  $items = $node->{'lineItems'};

  $size = sizeof($items->{'edges'});
  $n = $size - 1;




  $paiement = $node->{'paymentGatewayNames'}[0];

  echo "Mode de paiement : " . Html::encode($paiement);
?>
  <div class="shopify-orders-results">
    <main class="d-flex flex-nowrap">
      <div class="container-fluid">
        <div class="accordion" id="accordionOrder">

          <!-- Information Commande -->
          

          <!-- Liste des produits dans la commande -->

          <?php
          if (!empty($items)) {
            $i = 0;
          ?>

            <div class="accordion-item">
              <h2 class="accordion-header" id="heading4">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                  Liste des produits dans la commande
                </button>
              </h2>
              <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordionOrder">
                <div class="accordion-body">
                  <table class="table table-striped table-hover">
                    <thead class="thead-inverse">
                      <tr class="head-table-color">
                        <th scope="col">ID</th>
                        <th scope="col">Référence</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Quantité</th>
                        <th scope="col">Prix HT</th>
                        <th scope="col">TVA</th>
                        <th scope="col">Prix TTC</th>
                      </tr>
                    </thead>
                    <?php
                    //Interrogation de produit
                    for ($i = 0; $i <= $n; $i++) {
                      $quantity = $items->{'edges'}[$i]->{'node'}->{'quantity'};
                      $sku = $items->{'edges'}[$i]->{'node'}->{'sku'};
                      $name = $items->{'edges'}[$i]->{'node'}->{'name'};
                      $price = $items->{'edges'}[$i]->{'node'}->{'variant'}->{'price'};
                      $p_gid = $items->{'edges'}[$i]->{'node'}->{'variant'}->{'id'};
                      $pid = explode("/", $p_gid);

                      echo "ID du produit : <a target='_blank' href='https://$api:$pwd@$url/admin/api/$apiVersion/products/$pid[4].json'>" . Html::encode($pid[4]) . "</a><br>";
                      echo "Référence : " . Html::encode($sku) . "<br>";
                      echo "Nom : " . Html::encode($name) . "<br>";
                      echo "Prix : " . Html::encode($price) . " &euro;<br>";
                      echo "Quantité : " . Html::encode($quantity) . "<br><br>";
                    ?>
                      <tbody>
                        <tr>
                          <td scope='row'><?= Html::a($pid[4], ['productresults', 'id' => $model->id, 'ref' => Html::encode($sku)], ['class' => 'link-offset-2 link-underline link-underline-opacity-0']) ?></td>
                          <td><?php echo Html::encode($sku); ?></td>
                          <td><?php echo Html::encode($name); ?></td>
                          <td><?php echo Html::encode($quantity); ?></td>
                          <td><?php echo Html::encode($price); ?> &euro;</td>
                          <td><?php echo $tva; ?> &euro;</td>
                          <td><?php echo $pricettc; ?> &euro;</td>
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
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading5">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
                  À propos du client
                </button>
              </h2>
              <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#accordionOrder">
                <div class="accordion-body">
                  <table class="table table-striped table-hover">
                    <thead class="thead-inverse">
                      <tr class="head-table-color">
                        <th scope="col">ID</th>
                        <th scope="col">Société</th>
                        <th scope="col">Nom et Prénom</th>
                        <th scope="col">Adresse</th>
                        <th scope="col">Téléphone</th>
                      </tr>
                    </thead>
                    <?php
                    $req_cust = "https://$url/api/customers/$id_customer&ws_key=$api&output_format=JSON";

                    $decode_del = json_decode(curl_get($req_del));
                    $id_del = $decode_del->address->id;
                    $soc = $decode_del->address->company;
                    $fname = $decode_del->address->firstname;
                    $lname = $decode_del->address->lastname;

                    $pfixe = $decode_del->address->phone;
                    $pmobile = $decode_del->address->phone_mobile;

                    $add1 = $decode_del->address->address1;
                    $add2 = $decode_del->address->address2;
                    $zip = $decode_del->address->postcode;
                    $city = $decode_del->address->city;

                    $id_country = $decode_del->address->id_country;
                    $req_country = "https://$url/api/countries/$id_country&language=$lang_id&ws_key=$api&output_format=JSON";

                    $decode_coun = json_decode(curl_get($req_country));
                    $country = $decode_coun->country->name
                    ?>
                    <tbody>
                      <tr>
                        <td><?php echo $id_del; ?></td>
                        <td><?php echo $soc; ?></td>
                        <td><?php echo $fname . ' ' . $lname; ?></td>
                        <td><?php echo $add1 . ', ' . $add2 . ', ' . $zip . ', ' . $city . ', ' . $country; ?></td>
                        <td><?php echo 'Fixe : ' . $pfixe . '<br>Mobile : ' . $pmobile; ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
        </div>
      </div>
    </main>
  </div>
<?php
} else {
  $query = <<<QUERY
  query {
    orders(query: "id:$ref", first: 3) {
      edges {
        node {
          id
          createdAt
          updatedAt
          totalPrice
          taxLines {
            price
            ratePercentage
            title
          }
          lineItems(first: 100) {
            edges {
              node {
                quantity
                sku
                name
                taxLines {
                  price
                  ratePercentage
                  title
                }
                
                variant {
                  id
                  sku
                  price
                  displayName
                }
              }
            }
          }
          customer {
            id
            firstName
            lastName
            email
            phone
            defaultAddress {
              company
              firstName
              lastName
              address1
              address2
              zip
              city
              country
            }
          }
          paymentGatewayNames
        }
      }
    }
  }
QUERY;

  $response = $client->query(["query" => $query]);

  $contents = $response->getBody()->getContents();

  $contenu = json_decode($contents);

  file_put_contents('testid.json', $contents);

  $node = $contenu->{'data'}->{'orders'}->{'edges'}[0]->{'node'};

  $o_gid = $node->{'id'};
  $oid = explode("/", $o_gid);

  echo "ID de la commande : <a target='_blank' href='https://$api:$pwd@$url/admin/api/$apiVersion/orders/$oid[4].json'>" . Html::encode($oid[4]) . "</a><br><br>";

  $items = $node->{'lineItems'};

  $size = sizeof($items->{'edges'});
  $n = $size - 1;

  if (!empty($items)) {
    $i = 0;
    for ($i = 0; $i <= $n; $i++) {
      $quantity = $items->{'edges'}[$i]->{'node'}->{'quantity'};
      $sku = $items->{'edges'}[$i]->{'node'}->{'sku'};
      $name = $items->{'edges'}[$i]->{'node'}->{'name'};
      $price = $items->{'edges'}[$i]->{'node'}->{'variant'}->{'price'};
      $p_gid = $items->{'edges'}[$i]->{'node'}->{'variant'}->{'id'};
      $pid = explode("/", $p_gid);

      echo "ID du produit : <a target='_blank' href='https://$api:$pwd@$url/admin/api/$apiVersion/products/$pid[4].json'>" . Html::encode($pid[4]) . "</a><br>";
      echo "Référence : " . Html::encode($sku) . "<br>";
      echo "Nom : " . Html::encode($name) . "<br>";
      echo "Prix : " . Html::encode($price) . " &euro;<br>";
      echo "Quantité : " . Html::encode($quantity) . "<br><br>";
    }
  }

  $paiement = $node->{'paymentGatewayNames'}[0];

  echo "Mode de paiement : " . Html::encode($paiement);
}
