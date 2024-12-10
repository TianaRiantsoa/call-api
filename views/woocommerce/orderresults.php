<?php
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = 'Commandes | ' . Html::encode($ref) . ' | ' . Html::encode($model->url);
$this->params['breadcrumbs'][] = ['label' => 'Woocommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de commande', 'url' => ['orders', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

$url = Html::encode($model->url);

$headers = @get_headers("http://" . $url);
if ($headers && strpos($headers[0], '200') !== false) {
    $url = "https://" . $url;
} else {
    $url = "https://" . $url;
}

$consumer_key = Html::encode($model->consumer_key);
$consumer_secret = Html::encode($model->consumer_secret);
$ref = Html::encode($ref);


$client = new Client($url, $consumer_key, $consumer_secret, ['version' => 'wc/v3']);

$get = $client->get('orders/' . $ref);

/*******************************************************/
$check = file_get_contents("$url/wp-json/wc/v3/orders/$ref?consumer_key=$consumer_key&consumer_secret=$consumer_secret");
file_put_contents("woo-" . $get->id . ".json", $check);

$f = "woo-" . $get->id . ".json";

$file = Yii::getAlias('@web/SDK_6c7e95028cc46aa26ad7402822d209685b6f4be2_orders_get_after.ps1');

$cmd = "powershell.exe -ExecutionPolicy Bypass -File ." . $file . " -data $f";
$res = shell_exec($cmd);

file_put_contents("res-woo-" . $get->id . ".json", $res);
/*******************************************************/
echo '<pre>';
print_r($get);
echo '</pre>';