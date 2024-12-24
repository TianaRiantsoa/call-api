<?php

use prestashop\PrestaShopWebservice;
use yii\helpers\Html;

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
$api = Html::encode($model->api_key);
$ref = Html::encode($ref);

require('function.php');
?>
<div class="prestashop-orders-results">

    <main class="d-flex flex-nowrap">
        <table class="table table-striped table-hover">
            <thead class="thead-inverse">
                <tr class="head-table-color">
                    <th scope="col">N° de la commande</th>
                    <th scope="col">Référence</th>
                    <th scope="col">Mode de paiement</th>
                    <th scope="col">Total HT</th>
                    <th scope="col">Total TTC</th>
                    <th scope="col">TVA</th>
                    <th scope="col">TVA Port</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Création</th>
                    <th scope="col">Mise à jour</th>
                </tr>
            </thead>
            <?php
            $lang_id = getLang($url, $api);

            $req_ord = "https://$url/api/orders/$ref&ws_key=$api&output_format=JSON";
            $get_order = file_get_contents($req_ord);
            $decode = json_decode($get_order);

            $id = $decode->order->id;
            $reference = $decode->order->reference;
            $payment = $decode->order->payment;
            $totalHT = sprintf('%.2f', $decode->order->total_paid_tax_excl);
            $totalTTC = sprintf('%.2f', $decode->order->total_paid_tax_incl);

            $tva_total = ($totalTTC - $totalHT) * 100 / $totalHT;

            $tva_port = $decode->order->carrier_tax_rate;

            $id_state = $decode->order->current_state;
            $statut_url = "https://$url/api/order_states/$id_state&language=$lang_id&ws_key=$api&output_format=JSON";

            $decode_state = json_decode(curl_get($statut_url));
            //var_dump($decode_state);
            //$state = $decode_state->order_state->name;

            $date_add = $decode->order->date_add;
            $date_add = strtotime($date_add);
            $date_add = date("d/m/Y H:i:s", $date_add);

            $date_upd = $decode->order->date_upd;
            $date_upd = strtotime($date_upd);
            $date_upd = date("d/m/Y H:i:s", $date_upd);

            $id_invoice = $decode->order->id_address_invoice;
            $id_delivery = $decode->order->id_address_delivery;

            $id_customer = $decode->order->id_customer;
            ?>
            <tbody>
                <tr>
                    <td><a href="<?= $req_ord; ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo $id; ?>
                        </a></td>
                    <td>
                        <?php echo $reference; ?>
                    </td>
                    <td>
                        <?php echo $payment; ?>
                    </td>
                    <td>
                        <?php echo $totalHT; ?> &euro;
                    </td>
                    <td>
                        <?php echo $totalTTC; ?> &euro;
                    </td>
                    <td>
                        <?php echo $tva_total; ?> &percnt;
                    </td>
                    <td>
                        <?php echo $tva_port; ?> &percnt;
                    </td>
                    <td>
                        <?php echo $id_state; ?>
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

    </main>
</div>