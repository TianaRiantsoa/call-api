<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = 'Produits | ' . Html::encode($ref) . ' | ' . Html::encode($model->url);
$this->params['breadcrumbs'][] = ['label' => 'Woocommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de produit', 'url' => ['orders', 'id' => $model->id]];
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

$consumer_key = Html::encode($model->consumer_key);
$consumer_secret = Html::encode($model->consumer_secret);
$ref = Html::encode($ref);

echo "$url/wp-json/wc/v3/products/?sku=$ref&consumer_key=$consumer_key&consumer_secret=$consumer_secret";