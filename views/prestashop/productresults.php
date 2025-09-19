<?php

use yii\helpers\Html;
use app\services\PrestaShopProductService;
use app\helpers\PrestaShopViewHelper;

// =============================================================================
// CONTRÔLEUR PRINCIPAL - Utilisation des classes refactorisées
// =============================================================================

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Produits | ' . Html::encode($ref) . ' | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de produit', 'url' => ['products', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

// Variables sécurisées
$ref = Html::encode($ref);
$db_id = $model->id;
$type = Html::encode($type);
$variation_type = Html::encode($variation_type);
$languageIso = Yii::$app->request->get('language', 'fr'); // Valeur par défaut

try {
    // Initialisation du service PrestaShop
    $prestaShopService = new PrestaShopProductService($model->url, $model->api_key);

    // Affichage des informations du modèle
    echo yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'url:url',
            'api_key',
        ],
    ]);

    $urlweb = $prestaShopService->getUrl() . '/api/products/?filter[reference]=' . $ref . '&ws_key=' . $prestaShopService->getApiKey();
    echo '<br><h2>Résultat de la recherche sur le produit référence : ' . $ref . ' du site ' . $prestaShopService->getUrl() . '</h2><br>';
    echo '<a href="' . $urlweb . '" target="_blank">' . $urlweb . '</a><br><br>';

    // Traitement selon le type de produit
    switch ($type) {
        case 'simple':
            if ($variation_type == null) {
                handleSimpleProduct($prestaShopService, $ref, $languageIso);
            }
            break;

        case 'variation':
            switch ($variation_type) {
                case 'parent':
                    handleParentProduct($prestaShopService, $ref, $languageIso, $db_id);
                    break;
                case 'child':
                    handleChildProduct($prestaShopService, $ref, $languageIso, $db_id);
                    break;
            }
            break;
    }
} catch (Exception $e) {
    Yii::$app->session->setFlash('error', 'Erreur : ' . $e->getMessage());
}

// =============================================================================
// FONCTIONS DE TRAITEMENT
// =============================================================================

/**
 * Traite l'affichage d'un produit simple
 */
function handleSimpleProduct($prestaShopService, $ref, $languageIso)
{
    try {
        $productList = $prestaShopService->getSimpleProducts($ref, $languageIso);

        echo '<h3>Détails du Produit</h3>';
        echo PrestaShopViewHelper::renderSimpleProductGrid(
            $productList,
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey()
        );
    } catch (Exception $e) {
        Yii::$app->session->setFlash('error', $e->getMessage());
    }
}

/**
 * Traite l'affichage d'un produit parent avec ses déclinaisons
 */
function handleParentProduct($prestaShopService, $ref, $languageIso, $db_id)
{
    try {
        $result = $prestaShopService->getParentProductWithVariations($ref, $languageIso);

        echo '<h3>Détails du produit parent</h3>';
        echo PrestaShopViewHelper::renderParentProductGrid(
            $result['product'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey()
        );

        echo '<h3>Liste des déclinaisons</h3>';
        echo PrestaShopViewHelper::renderCombinationsGrid(
            $result['combinations'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey(),
            $db_id
        );
    } catch (Exception $e) {
        Yii::$app->session->setFlash('error', $e->getMessage());
    }
}

/**
 * Traite l'affichage d'un produit enfant (déclinaison)
 */
function handleChildProduct($prestaShopService, $ref, $languageIso, $db_id)
{
    try {
        $result = $prestaShopService->getChildCombination($ref, $languageIso);

        echo '<h3>Détails du Produit</h3>';
        echo PrestaShopViewHelper::renderChildCombinationGrid(
            $result['combinations'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey(),
            $db_id
        );

        echo '<h3>Tarifs spécifiques</h3>';
        echo PrestaShopViewHelper::renderSpecificPricesGrid(
            $result['specific_prices'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey()
        );
    } catch (Exception $e) {
        // Gestion d'erreur avancée pour les produits enfants
        handleChildProductError($e, $prestaShopService);
    }
}

/**
 * Gestion d'erreur spécifique pour les produits enfants
 */
function handleChildProductError($e, $prestaShopService)
{
    $rawResponse = method_exists($prestaShopService, 'getRawResponse')
        ? $prestaShopService->getRawResponse()
        : null;

    echo '<span style="color:red">Erreur détectée : ' . $e->getMessage() . '</span><br>';

    if ($rawResponse) {
        echo '<span style="color:red">Réponse brute : ' . PHP_EOL . htmlspecialchars($rawResponse) . '</span><br>';

        if (strpos($rawResponse, '<!DOCTYPE html>') !== false) {
            echo '<span style="color:red">Erreur HTML détectée</span><br>';
        } elseif (strpos($rawResponse, '<?xml') === 0) {
            $xml = simplexml_load_string($rawResponse);
            if ($xml !== false) {
                echo '<pre>' . htmlspecialchars(print_r($xml, true)) . '</pre>';
            } else {
                echo '<span style="color:red">Erreur lors du parsing XML</span><br>';
            }
        } else {
            echo '<span style="color:red">Format de réponse inconnu</span><br>';
        }
    } else {
        echo '<span style="color:red">Aucune réponse brute disponible</span><br>';
    }
}
