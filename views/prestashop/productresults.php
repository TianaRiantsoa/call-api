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
$db_id = $model->id;
$type = Html::encode($type);
$variation_type = Html::encode($variation_type);

echo yii\widgets\DetailView::widget([
	'model' => $model,
	'attributes' => [
		'url:url',
		'api_key',
	],
]);

echo '<h2>Résultat de la recherche sur le produit rérérence : ' . $ref . ' du site ' . $url . '</h2>';

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
					$productData = [
						'id' => (int)$product->id,
						'name' => (string)$product->name->language,
						'reference' => (string)$product->reference,
						'price' => (float)$product->price,
					];



					$productList[] = $productData;
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
				'dataProvider' => new ArrayDataProvider([
					'allModels' => $productList,
					'pagination' => [
						'pageSize' => 10,
					],
				]),
				'columns' => [
					'id',
					'name',
					'reference',
					[
						'attribute' => 'price',
						'value' => function ($model) {
							return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
						},
						'label' => 'Prix',
					],
				],
			]);
		}
	} catch (\Exception $e) {
		// En cas d'erreur, afficher un message d'erreur
		Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
	}
}

//Traitement d'une déclinaison via la référence du produit Parent

elseif (
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
			$combinationList = [];

			// Vérifier si nous avons des produits
			if (count($products) > 0) {
				// Parcourir les produits et vérifier le type
				foreach ($products as $product) {
					$productType = (string)$product->type;
					$productTypeCheck = (string)$product->product_type;

					// Vérifier si le produit n'est pas de type "simple" ou de type "combinations"
					if ($productType !== 'simple' || $productTypeCheck === 'combinations') {
						// Ajouter les produits valides au tableau
						$productData = [
							'id' => (int)$product->id,
							'name' => (string)$product->name->language,
							'reference' => (string)$product->reference,
							'active' => (int)$product->active,
							'price' => (float)$product->price,
							'date_add' => (string)$product->date_add,
							'date_upd' => (string)$product->date_upd,
						];

						//Récupération du stock su produit Parent, normalement il s'agit de la quantité total des délinaisons
						try {
							$stockOpt = [
								'resource' => 'stock_availables',
								'filter[id_product]' => (int)$product->id,
								'filter[id_product_attribute]' => '0',
								'display' => 'full',
							];

							// Récupérer le stock de la déclinaison
							$stock = $webService->get($stockOpt);
							$stockXML = $stock->stock_availables->children();

							// Ajouter la référence parent si elle existe
							foreach ($stockXML as $stocks) {
								if (isset($stocks->quantity)) {
									$productData['quantity'] = (string)$stocks->quantity;
								}
							}
						} catch (Exception $e) {
							// En cas d'erreur lors de la récupération du parent
							$productData['quantity'] = 'Erreur lors de la récupération';
						}

						//Envoi des données Final
						$productList[] = $productData;

						// Récupération de la liste des déclinaisons
						try {
							$combOpt = [
								'resource' => 'combinations',
								'filter[id_product]' => (int)$product->id,
								'display' => '[id,reference,price]',
							];

							$comb = $webService->get($combOpt);
							$combXML = $comb->combinations->children();

							foreach ($combXML as $combs) {
								$combData = [
									'id' => (int)$combs->id,
									'reference' => (string)$combs->reference,
									'price' => (float)$combs->price,
								];

								//Récupération du stock des déclinaisons
								try {
									$stockCombOpt = [
										'resource' => 'stock_availables',
										'filter[id_product_attribute]' => (int)$combs->id,
										'display' => 'full',
									];

									// Récupérer le stock de la déclinaison
									$stock = $webService->get($stockCombOpt);
									$stockXML = $stock->stock_availables->children();

									// Ajouter la référence parent si elle existe
									foreach ($stockXML as $stocks) {
										if (isset($stocks->quantity)) {
											$combData['quantity'] = (string)$stocks->quantity;
										}
									}
								} catch (Exception $e) {
									// En cas d'erreur lors de la récupération du parent
									$combData['quantity'] = 'Erreur lors de la récupération';
								}
								//Envoi des données Final
								$combinationList[] = $combData;
							}
						} catch (Exception $e) {
							//throw $th;
						}
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
		echo '<h3>Détails du produit parent</h3>';
		// Si des produits sont trouvés et valides, afficher le GridView
		if (!empty($productList)) {
			echo GridView::widget([
				'dataProvider' => new ArrayDataProvider([
					'allModels' => $productList,
					'pagination' => [
						'pageSize' => 10,
					],
				]),
				'columns' => [
					[
						'attribute' => 'id',
						'label' => 'ID Produit',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							return Html::a(
								$model['id'],
								$url . "/api/products/{$model['id']}?&ws_key=" . $api,
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'reference',
						'label' => 'Référence',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							return Html::a(
								$model['reference'],
								$url . "/api/products/{$model['id']}?&ws_key=" . $api,
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'name',
						'label' => 'Nom du produit',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							return Html::a(
								$model['name'],
								$url . "/api/products/{$model['id']}?&ws_key=" . $api,
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'active',
						'label' => 'Statut',
						'value' => function ($model) {
							return isset($model['active']) && $model['active'] ? 'Actif' : 'Inactif';
						},
					],
					[
						'attribute' => 'price',
						'value' => function ($model) {
							return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
						},
						'label' => 'Prix',
					],
					[
						'attribute' => 'quantity',
						'label' => 'Total en stock',
					],
					[
						'attribute' => 'date_add',
						'value' => function ($model) {
							$date = is_array($model) ? $model['date_add'] : $model->date_add;
							return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
						},
						'label' => 'Création',  // Nouveau nom de la colonne
					],
					[
						'attribute' => 'date_upd',
						'value' => function ($model) {
							$date = is_array($model) ? $model['date_upd'] : $model->date_upd;
							return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
						},
						'label' => 'Mise à jour',  // Nouveau nom de la colonne
					],
				],
			]);
		}

		echo '<h3>Liste des déclinaisons</h3>';
		// Si des produits sont trouvés et valides, afficher le GridView
		if (!empty($combinationList)) {
			echo GridView::widget([
				'dataProvider' => new ArrayDataProvider([
					'allModels' => $combinationList,
					'pagination' => [
						'pageSize' => 10,
					],
				]),
				'columns' => [
					'id',
					[
						'attribute' => 'reference',
						'label' => 'Référence de la déclinaison',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api, $db_id) {
							return Html::a(
								$model['reference'],
								'?id=' . $db_id . '&ref=' . $model['reference'] . '&type=variation&variation_type=child',
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'price',
						'value' => function ($model) {
							return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
						},
						'label' => 'Prix',
					],
					[
						'attribute' => 'quantity',
						'label' => 'Quantité en stock',
					],
				],
			]);
		}
	} catch (\Exception $e) {
		// En cas d'erreur, afficher un message d'erreur
		Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
	}
}


//Traitement d'une déclinaison via la référence du produit enfant

elseif (
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
			// Récupérer les combinaisons depuis l'API PrestaShop
			$xml = $webService->get($opt);
			$combinations = $xml->combinations->children(); // Récupérer toutes les combinaisons

			// Initialiser la liste des combinaisons
			$combinationList = [];

			// Vérifier si nous avons des combinaisons
			if (count($combinations) > 0) {
				// Parcourir les combinaisons
				foreach ($combinations as $combination) {
					$combinationData = [
						'id' => (int) $combination->id,
						'reference' => (string) $combination->reference,
						'price' => (float) $combination->price,
						'parent_reference' => 'N/A',
						'quantity' => 0,
						'specific_prices' => [],
					];

					// Récupération du produit parent
					try {
						$parentOpt = [
							'resource' => 'products',
							'id' => (int) $combination->id_product,
							'language' => $languageId,
						];

						$parent = $webService->get($parentOpt);
						$parentXML = $parent->product;

						if (isset($parentXML->reference)) {
							$combinationData['parent_reference'] = (string) $parentXML->reference;
						}
					} catch (Exception $e) {
						$combinationData['parent_reference'] = 'Erreur lors de la récupération';
					}

					// Récupération du stock de la déclinaison
					try {
						$stockOpt = [
							'resource' => 'stock_availables',
							'filter[id_product_attribute]' => (int) $combination->id,
							'display' => 'full',
						];

						$stock = $webService->get($stockOpt);
						$stockXML = $stock->stock_availables->children();

						foreach ($stockXML as $stockItem) {
							if (isset($stockItem->quantity)) {
								$combinationData['quantity'] = (int) $stockItem->quantity;
								break; // Prendre uniquement le premier stock disponible
							}
						}
					} catch (Exception $e) {
						$combinationData['quantity'] = 'Erreur : ' . $e->getMessage();
					}

					// Récupération des tarifs spécifiques
					try {
						$tarifOpt = [
							'resource' => 'specific_prices',
							'filter[id_product_attribute]' => (int) $combination->id,
							'display' => 'full',
						];

						$tarif = $webService->get($tarifOpt);
						$tarifXML = $tarif->specific_prices->children();

						foreach ($tarifXML as $tarifItem) {
							$tarifList[] = [
								'id' => (int) $tarifItem->id,
								'id_product' => (int) $tarifItem->id_product,
								'id_product_attribute' => (int) $tarifItem->id_product_attribute,
								'id_group' => (int) $tarifItem->id_group,
								'id_customer' => (int) $tarifItem->id_customer,
								'price' => (float) $tarifItem->price,
								'from' => (string) $tarifItem->from,
								'to' => (string) $tarifItem->to,
							];
						}
					} catch (Exception $e) {
						'Erreur : ' . $e->getMessage();
					}

					// Ajouter les données de la combinaison à la liste
					$combinationList[] = $combinationData;
				}
			} else {
				// Aucun produit trouvé
				Yii::$app->session->setFlash('error', 'Aucun produit enfant trouvé avec cette référence.');
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

		echo '<h3>Détails du Produit</h3>';
		// Si des produits sont trouvés et valides, afficher le GridView
		if (!empty($combinationList)) {
			echo GridView::widget([
				'dataProvider' => new ArrayDataProvider([
					'allModels' => $combinationList,
					'pagination' => [
						'pageSize' => 10,
					],
				]),
				'columns' => [
					[
						'attribute' => 'id',
						'label' => 'ID',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							return Html::a(
								$model['id'],
								$url . "/api/combinations/{$model['id']}?&ws_key=" . $api,
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'parent_reference',
						'label' => 'Référence Parente',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api, $db_id) {
							return Html::a(
								$model['parent_reference'],
								'?id=' . $db_id . '&ref=' . $model['parent_reference'] . '&type=variation&variation_type=parent',
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'id',
						'label' => 'Référence',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							return Html::a(
								$model['reference'],
								$url . "/api/combinations/{$model['id']}?&ws_key=" . $api,
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'quantity',
						'label' => 'Quantité',
					],
					[
						'attribute' => 'price',
						'value' => function ($model) {
							return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
						},
						'label' => 'Prix',
					],
				],
			]);
		}

		// Associer les prix des combinaisons à leurs IDs pour une correspondance rapide
		$combinationPrices = [];
		foreach ($combinationList as $combination) {
			$combinationPrices[$combination['id']] = $combination['price'];
		}

		// Calculer les différences pour les tarifs spécifiques
		foreach ($tarifList as &$tarif) {
			$combinationPrice = $combinationPrices[$tarif['id_product_attribute']] ?? null;

			if ($combinationPrice !== null) {
				// Calculer les différences
				$differenceAmount = $tarif['price'] - $combinationPrice;
				$differencePercentage = $combinationPrice != 0 ? ($differenceAmount / $combinationPrice) * 100 : 0;

				// Ajouter les résultats dans les données
				$tarif['difference_amount'] = $differenceAmount;
				$tarif['difference_percentage'] = $differencePercentage;
			} else {
				// Si aucune correspondance n'est trouvée
				$tarif['difference_amount'] = 'N/A';
				$tarif['difference_percentage'] = 'N/A';
			}
		}

		echo '<h3>Tarifs spécifiques</h3>';
		// Si des produits sont trouvés et valides, afficher le GridView
		if (!empty($tarifList)) {
			echo GridView::widget([
				'dataProvider' => new ArrayDataProvider([
					'allModels' => $tarifList,
					'pagination' => [
						'pageSize' => 1000,
					],
				]),
				'columns' => [
					[
						'attribute' => 'id',
						'label' => 'ID',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							return Html::a(
								$model['id'],
								$url . "/api/specific_prices/{$model['id']}?&ws_key=" . $api,
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'id_product',
						'label' => 'ID du produit',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							return Html::a(
								$model['id_product'],
								$url . "/api/products/{$model['id_product']}?&ws_key=" . $api,
								['target' => '_blank', 'encode' => false]
							);
						}
					],
					[
						'attribute' => 'id_product_attribute',
						'label' => 'ID de la déclinaison',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							if ($model['id_product_attribute'] !== 0) {
								return Html::a(
									$model['id_product_attribute'],
									$url . "/api/combinations/{$model['id_product_attribute']}?&ws_key=" . $api,
									['target' => '_blank', 'encode' => false]
								);
							}
						}
					],
					[
						'attribute' => 'price',
						'value' => function ($model) {
							return Yii::$app->formatter->asCurrency($model['price'], 'EUR');
						},
						'label' => 'Prix',
					],
					[
						'attribute' => 'difference_amount',
						'label' => 'Différence €',
						'format' => 'raw',
						'value' => function ($model) {
							$value = is_numeric($model['difference_amount'])
								? Yii::$app->formatter->asCurrency($model['difference_amount'], 'EUR')
								: $model['difference_amount'];

							// Ajouter le style rouge
							return "<span style='color: red;'>{$value}</span>";
						},
						'contentOptions' => function ($model) {
							// Applique la couleur rouge uniquement si la différence est un pourcentage valide
							return ['style' => 'color: red;'];
						},
					],
					[
						'attribute' => 'difference_percentage',
						'label' => 'Différence %',
						'format' => 'raw',
						'value' => function ($model) {
							$value = is_numeric($model['difference_percentage'])
								? Yii::$app->formatter->asPercent($model['difference_percentage'] / 100, 2)
								: $model['difference_percentage'];

							// Ajouter le style rouge
							return "<span style='color: red;'>{$value}</span>";
						},
					],

					[
						'attribute' => 'id_group',
						'label' => 'Groupe de client',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							if ($model['id_group'] !== 0) {
								return Html::a(
									$model['id_group'],
									$url . "/api/groups/{$model['id_group']}?&ws_key=" . $api,
									['target' => '_blank', 'encode' => false]
								);
							}
						}
					],
					[
						'attribute' => 'id_customer',
						'label' => 'Client',
						'format' => 'raw',
						'value' => function ($model) use ($url, $api) {
							if ($model['id_customer'] !== 0) {
								return Html::a(
									$model['id_customer'],
									$url . "/api/groups/{$model['id_customer']}?&ws_key=" . $api,
									['target' => '_blank', 'encode' => false]
								);
							}
						}
					],

					[
						'attribute' => 'from',
						'label' => 'De',
						// 'value' => function ($model) {
						// 	$date = is_array($model) ? $model['from'] : $model->from;
						// 	return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
						// },
					],
					[
						'attribute' => 'to',
						'label' => 'À',
						// 'value' => function ($model) {
						// 	$date = is_array($model) ? $model['to'] : $model->to;
						// 	return Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
						// },
					],
				],
			]);
		}
	} catch (\Exception $e) {
		// En cas d'erreur, afficher un message d'erreur
		Yii::$app->session->setFlash('error', 'Erreur API : ' . $e->getMessage());
	}
}
