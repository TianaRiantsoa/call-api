<?php

use yii\helpers\Html;

require("./../vendor/prestashop/prestashop-webservice-lib/PSWebServiceLibrary.php");

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Produits | ' . Html::encode($ref) . ' | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de produit', 'url' => ['products', 'id' => $model->id]];
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

$p = Product::get($url, $api, $ref);
?>
<div class="prestashop-products-results">
	<main class="d-flex flex-nowrap">
		<div class="container-fluid">
			<div class="accordion" id="accordionProduct">
				<div class="accordion-item">
					<h2 class="accordion-header" id="headingOne">
						<button class="accordion-button test" type="button" data-bs-toggle="collapse"
							data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							Détail du produit
						</button>
					</h2>
					<div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1"
						data-bs-parent="#accordionProduct">
						<div class="accordion-body" id="detail">
							<?php include('include/product.php') ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</div>