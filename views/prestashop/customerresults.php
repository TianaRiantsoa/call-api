<?php

use PHPUnit\Framework\Constraint\IsFalse;
use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

use function PHPUnit\Framework\isFalse;

require("./../vendor/prestashop/prestashop-webservice-lib/PSWebServiceLibrary.php");

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Clients | ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de client', 'url' => ['customers', 'id' => $model->id]];
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



echo 'URL de la requête : <a href=' . $url . '/api/customers/?filter[email]=' . $ref . '&ws_key=' . $api . ' target=_blank>' . $url . '/api/customers/?filter[email]=' . $ref . '&ws_key=' . $api . '</a>';

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'url:url',
        'api_key',
    ],
]);

echo '<h2>Résultat de la recherche sur le client : ' . $ref . ' du site ' . $url . '</h2>';

try {

    // Initialisation du webservice
    try {
        $webService = new PrestaShopWebservice($url, $api, false);
    } catch (PrestaShopWebserviceException $e) {
        Yii::$app->session->setFlash('error', 'Erreur d\'authentification : ' . $e->getMessage());
        return;
    }

    // Récupérer l'ID de la langue française
    try {
        // Initialisation du webservice
        $webService = new PrestaShopWebservice($url, $api, false);
    
        // Options pour récupérer la langue française
        $languageOpt = [
            'resource' => 'languages',
            'filter[iso_code]' => 'fr',
            'display' => 'full',
        ];
    
        // Récupérer les données
        $languageXml = $webService->get($languageOpt);
    
        // Vérifier si la réponse est valide
        if (!isset($languageXml->languages)) {
            throw new Exception('Réponse XML invalide : aucune balise <languages> trouvée.');
        }
    
        // Traiter les langues
        $languages = $languageXml->languages->children();
        $languageId = null;
    
        foreach ($languages as $language) {
            $languageId = (int)$language->id;
            break; // On s'arrête après avoir trouvé une correspondance
        }
    
        if (!$languageId) {
            throw new Exception('Langue française introuvable dans la boutique.');
        }
    
        // Utiliser $languageId pour d'autres requêtes
        echo 'ID de la langue française : ' . $languageId;
    
    } catch (PrestaShopWebserviceException $e) {
        // Gestion des erreurs de l'API
        // Yii::$app->session->setFlash('error', 'Erreur : ' . $e->getMessage());
		// return;

		$rawResponse = $webService->getRawResponse();

		echo '<span style="color:red">Erreur détectée : ' . $e->getMessage() . PHP_EOL . '</span><br>';

		if ($rawResponse) {
			echo '<span style="color:red">Réponse brute : '  . PHP_EOL . $rawResponse . '</span>';
			// Tenter de parser ou analyser manuellement
			if (strpos($rawResponse, '<!DOCTYPE html>') !== false) {
				echo 'Erreur HTML détectée' . PHP_EOL;
			} elseif (strpos($rawResponse, '<?xml') === 0) {
				// Parser le XML manuellement
				$xml = simplexml_load_string($rawResponse);
				if ($xml !== false) {
					print_r($xml);
				} else {
					Yii::$app->session->setFlash('error', 'Erreur lors du parsing XML.' . PHP_EOL);
				}
			} else {
				Yii::$app->session->setFlash('error', 'Format de réponse inconnu.' . PHP_EOL);
			}
		} else {
			Yii::$app->session->setFlash('error', 'Aucune réponse brute disponible.' . PHP_EOL);
		}
		return;
    } catch (Exception $e) {
        // Gestion des autres erreurs
        echo 'Erreur : ' . $e->getMessage();
    }

    // Récupérer les clients par email
    try {
        $opt = [
            'resource' => 'customers',
            'filter[email]' => $ref, // Filtrer par email
            'display' => 'full',
        ];

        $xml = $webService->get($opt);
        $customers = $xml->customers->children();

        $customerList = [];

        if (count($customers) > 0) {
            foreach ($customers as $customer) {
                $customerData = [
                    'id' => (int)$customer->id,
                    'id_default_group' => (string)$customer->id_default_group,
                    'company' => (string)$customer->company,
                    'firstname' => (string)$customer->firstname,
                    'lastname' => (string)$customer->lastname,
                    'email' => (string)$customer->email,
                    'siret' => (int)$customer->siret,
                    'active' => (bool)$customer->active,
                    'date_add' => (string)$customer->date_add,
                    'date_upd' => (string)$customer->date_upd,
                ];

                // Déterminer le type de client
                if (!empty($customer->company)) {
                    $customerData['type'] = 'Professionnel';
                } else {
                    $customerData['type'] = 'Particulier';
                    $customerData['company'] = '';
                }

                $customerList[] = $customerData;
            }
        } else {
            Yii::$app->session->setFlash('error', 'Aucun client trouvé avec cette référence.');
            return;
        }
    } catch (PrestaShopWebserviceException $e) {
       // Yii::$app->session->setFlash('error', 'Erreur : ' . $e->getMessage());
		// return;

		$rawResponse = $webService->getRawResponse();

		echo '<span style="color:red">Erreur détectée : ' . $e->getMessage() . PHP_EOL . '</span><br>';

		if ($rawResponse) {
			echo '<span style="color:red">Réponse brute : '  . PHP_EOL . $rawResponse . '</span>';
			// Tenter de parser ou analyser manuellement
			if (strpos($rawResponse, '<!DOCTYPE html>') !== false) {
				echo 'Erreur HTML détectée' . PHP_EOL;
			} elseif (strpos($rawResponse, '<?xml') === 0) {
				// Parser le XML manuellement
				$xml = simplexml_load_string($rawResponse);
				if ($xml !== false) {
					print_r($xml);
				} else {
					Yii::$app->session->setFlash('error', 'Erreur lors du parsing XML.' . PHP_EOL);
				}
			} else {
				Yii::$app->session->setFlash('error', 'Format de réponse inconnu.' . PHP_EOL);
			}
		} else {
			Yii::$app->session->setFlash('error', 'Aucune réponse brute disponible.' . PHP_EOL);
		}
		return;
    }


    /* 
		TRAITEMENT DES DONNEES
		*/
    echo '<h3>Détails du Client</h3>';
    // Si des clients sont trouvés et valides, afficher le GridView
    if (!empty($customerList)) {
        echo GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $customerList,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'type',
                    'label' => 'Type',
                ],
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['id'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'company',
                    'label' => 'Société',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['company'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'firstname',
                    'label' => 'Prénom',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['firstname'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'lastname',
                    'label' => 'Nom',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['lastname'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
                            ['target' => '_blank', 'encode' => false]
                        );
                    }
                ],
                [
                    'attribute' => 'email',
                    'label' => 'Email',
                    'format' => 'raw',
                    'value' => function ($model) use ($url, $api) {
                        return Html::a(
                            $model['email'],
                            $url . "/api/customers/{$model['id']}?ws_key=" . $api,
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
} catch (PrestaShopWebserviceException $e) {
    // Yii::$app->session->setFlash('error', 'Erreur : ' . $e->getMessage());
    // return;

    $rawResponse = $webService->getRawResponse();

    echo '<span style="color:red">Erreur détectée : ' . $e->getMessage() . PHP_EOL . '</span><br>';

    if ($rawResponse) {
        echo '<span style="color:red">Réponse brute : '  . PHP_EOL . $rawResponse . '</span>';
        // Tenter de parser ou analyser manuellement
        if (strpos($rawResponse, '<!DOCTYPE html>') !== false) {
            echo 'Erreur HTML détectée' . PHP_EOL;
        } elseif (strpos($rawResponse, '<?xml') === 0) {
            // Parser le XML manuellement
            $xml = simplexml_load_string($rawResponse);
            if ($xml !== false) {
                print_r($xml);
            } else {
                Yii::$app->session->setFlash('error', 'Erreur lors du parsing XML.' . PHP_EOL);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Format de réponse inconnu.' . PHP_EOL);
        }
    } else {
        Yii::$app->session->setFlash('error', 'Aucune réponse brute disponible.' . PHP_EOL);
    }
    return;
}