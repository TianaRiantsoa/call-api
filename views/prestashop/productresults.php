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

//Produit simple
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

		try {
			// Récupérer les produits depuis l'API PrestaShop
			$xml = $webService->get($opt);
			$products = $xml->products->children(); // Récupérer tous les produits

			// Initialiser la variable pour stocker les produits
			$productList = [];

			// Vérifier si nous avons des produits
			if (count($products) > 0) {
				// Parcourir les produits et vérifier le type
				foreach ($products as $product) {
					$productType = (string)$product->type;
					$productTypeCheck = (string)$product->product_type;

					// Vérifier si le produit n'est pas de type "simple" ou de type "combinations"
					if ($productType !== 'simple' || $productTypeCheck === 'combinations') {
						// Ajouter un message flash spécifique
						Yii::$app->session->setFlash('error', 'Le produit n\'est pas de type "simple". Veuillez vérifier et choisir "Produit déclinaison" dans le formulaire.');
						return; // Stopper l'exécution ici
					}

					// Ajouter les produits valides au tableau
					$productList[] = [
						'id' => (int)$product->id,
						'name' => (string)$product->name->language,
						//'language' => $languageId,
						'reference' => (string)$product->reference,
						'price' => (float)$product->price,
						// Ajoutez d'autres champs si nécessaire
					];
				}
			} else {
				// Aucun produit trouvé
				Yii::$app->session->setFlash('error', 'Aucun produit trouvé avec cette référence.');
				return; // Stopper l'exécution ici
			}
		} catch (Exception $e) {
			// Gérer les erreurs liées à l'API
			Yii::$app->session->setFlash('error', 'Erreur lors de la récupération des données produit : ' . $e->getMessage());
			return; // Stopper l'exécution ici
		}


		/* 
		TRAITEMENT DES DONNEES
		*/

		// Si des produits sont trouvés et valides, afficher le GridView
		if (!empty($productList)) {
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
					//'language',
					'reference',
					'price',
					// Ajoutez d'autres colonnes selon vos besoins
				],
			]);
		}
	} catch (\Exception $e) {
		// En cas d'erreur, afficher un message d'erreur
		Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
	}
} elseif (
	isset($ref) && $ref != null
	&& isset($type) && $type === 'variation'
	&& isset($variation_type) && $variation_type == 'parent'
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

		try {
			// Récupérer les produits depuis l'API PrestaShop
			$xml = $webService->get($opt);
			$products = $xml->products->children(); // Récupérer tous les produits

			// Initialiser la variable pour stocker les produits
			$productList = [];

			// Vérifier si nous avons des produits
			if (count($products) > 0) {
				// Parcourir les produits et vérifier le type
				foreach ($products as $product) {
					$productType = (string)$product->type;
					$productTypeCheck = (string)$product->product_type;

					// Vérifier si le produit n'est pas de type "simple" ou de type "combinations"
					if ($productType !== 'simple' || $productTypeCheck === 'combinations') {

						// Ajouter les produits valides au tableau
						$productList[] = [
							'id' => (int)$product->id,
							'name' => (string)$product->name->language,
							//'language' => $languageId,
							'reference' => (string)$product->reference,
							'price' => (float)$product->price,
							// Ajoutez d'autres champs si nécessaire
						];
					} else {

						// Ajouter un message flash spécifique
						Yii::$app->session->setFlash('error', 'Le produit n\'est pas trouvé en tant que produit parent. Essayer avec Produit Déclinaison > Enfant ou Produit Simple');
						return; // Stopper l'exécution ici
					}
				}
			} else {
				// Aucun produit trouvé
				Yii::$app->session->setFlash('error', 'Aucun produit trouvé avec cette référence.');
				return; // Stopper l'exécution ici
			}
		} catch (Exception $e) {
			// Gérer les erreurs liées à l'API
			Yii::$app->session->setFlash('error', 'Erreur lors de la récupération des données produit : ' . $e->getMessage());
			return; // Stopper l'exécution ici
		}


		/* 
		TRAITEMENT DES DONNEES
		*/

		// Si des produits sont trouvés et valides, afficher le GridView
		if (!empty($productList)) {
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
					//'language',
					'reference',
					'price',
					// Ajoutez d'autres colonnes selon vos besoins
				],
			]);
		}
	} catch (\Exception $e) {
		// En cas d'erreur, afficher un message d'erreur
		Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
	}
} elseif (
	isset($ref) && $ref != null
	&& isset($type) && $type === 'variation'
	&& isset($variation_type) && $variation_type == 'child'
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
			'resource' => 'combinations',
			'language' => $languageId, // Utiliser l'ID de la langue française
			'filter[reference]' => $ref, // Filtrer par référence
			'display' => 'full',
		];

		try {
			// Récupérer les produits depuis l'API PrestaShop
			$xml = $webService->get($opt);
			$combinations = $xml->combinations->children(); // Récupérer tous les produits

			// Initialiser la variable pour stocker les produits
			$combinationList = [];

			// Vérifier si nous avons des produits
			if (count($combinations) > 0) {
				// Parcourir les produits et vérifier le type
				foreach ($combinations as $combination) {

					// Vérifier si le produit n'est pas de type "simple" ou de type "combinations"
					if (isset($combination->id_product)  && $combination->id_product != null) {

						// Ajouter les produits valides au tableau
						$combinationList[] = [
							'id' => (int)$combination->id,
							'parents' => (string)$combination->id_product,
							//'language' => $languageId,
							'reference' => (string)$combination->reference,
							'price' => (float)$combination->price,
							// Ajoutez d'autres champs si nécessaire
						];
					} else {

						// Ajouter un message flash spécifique
						Yii::$app->session->setFlash('error', 'Le produit n\'est pas trouvé en tant que produit enfant. Essayer avec Produit Déclinaison > Parent ou Produit Simple');
						return; // Stopper l'exécution ici
					}
				}
			} else {
				// Aucun produit trouvé
				Yii::$app->session->setFlash('error', 'Aucun produit trouvé avec cette référence.');
				return; // Stopper l'exécution ici
			}
		} catch (Exception $e) {
			// Gérer les erreurs liées à l'API
			Yii::$app->session->setFlash('error', 'Erreur lors de la récupération des données produit : ' . $e->getMessage());
			return; // Stopper l'exécution ici
		}


		/* 
		TRAITEMENT DES DONNEES
		*/

		// Si des produits sont trouvés et valides, afficher le GridView
		if (!empty($combinationList)) {
			echo GridView::widget([
				'dataProvider' => new \yii\data\ArrayDataProvider([
					'allModels' => $combinationList,
					'pagination' => [
						'pageSize' => 10,
					],
				]),
				'columns' => [
					'id',
					'parents',
					//'language',
					'reference',
					'price',
					// Ajoutez d'autres colonnes selon vos besoins
				],
			]);
		}
	} catch (\Exception $e) {
		// En cas d'erreur, afficher un message d'erreur
		Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
	}
}
