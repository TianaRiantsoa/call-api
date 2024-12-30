<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
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

$api = Html::encode($model->api_key);
$ref = Html::encode($ref);
$type = Html::encode($type);
$variation_type = Html::encode($variation_type);


if (
	isset($ref) && $ref != null
	&& isset($type) && $type === 'simple'
	&& isset($variation_type) && $variation_type == null
) {
	try {
		// Connexion à l'API PrestaShop
		$webService = new PrestaShopWebservice($url, $api, false);

		$languageOpt = [
			'resource' => 'languages',
			'filter[iso_code]' => 'fr', // Filtrer par code ISO
			'display' => 'full',
		];
		$languageXml = $webService->get($languageOpt);
		$languages = $languageXml->languages->children();

		$languageId = null;
		foreach ($languages as $language) {
			$languageId = (int)$language->id; // Récupérer l'ID de la langue française
			break; // On s'arrête après avoir trouvé une correspondance
		}

		if (!$languageId) {
			throw new PrestaShopWebserviceException('Langue française introuvable dans la boutique.');
		}

		// Construire les options de la requête pour filtrer par référence
		$opt = [
			'resource' => 'products',
			'language' => $languageId, // Utiliser l'ID de la langue française
			'filter[reference]' => $ref, // Filtrer par référence
			'display' => 'full',
		];

		// Récupérer les produits depuis l'API
		$xml = $webService->get($opt);
		$products = $xml->products->children();
		$productList = [];

		// Parcourir les produits récupérés et les stocker dans un tableau
		foreach ($products as $product) {
			$productList[] = [
				'id' => (int)$product->id,
				'name' => (string)$product->name->language,
				'language' => $languageId,
				'reference' => (string)$product->reference,
				'price' => (float)$product->price,
				// Ajoutez d'autres champs si nécessaire
			];
		}

		// Afficher les produits dans un GridView
		echo GridView::widget([
			'dataProvider' => new \yii\data\ArrayDataProvider([
				'allModels' => $productList,
				'pagination' => [
					'pageSize' => 10,
				],
			]),
			'columns' => [
				'id',
				'name',
				'language',
				'reference',
				'price',
				// Ajoutez d'autres colonnes selon vos besoins
			],
		]);
	} catch (\Exception $e) {
		// En cas d'erreur, afficher un message d'erreur
		Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
	}
}
