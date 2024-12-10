<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Clients | ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de client', 'url' => ['customers', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);
$url = Html::encode($model->url);

$headers = @get_headers("http://" . $url);
if ($headers && strpos($headers[0], '200') !== false) {
    $url = "https://" . $url;
} else {
    $url = "https://" . $url;
}

$api = Html::encode($model->api_key);
$ref = Html::encode($ref);

require('function.php');

$c = Customer::get($url, $api, $ref);
?>
<div class="prestashop-customers-results">

    <pre>
    <?php print_r($c);
    for ($i = 0; $i <= (sizeof($c) - 1); $i++) {
        echo "$url/api/customers/" . $c[$i]['id'] . "&ws_key=$api";
    }
    ?>
</pre>

</div>