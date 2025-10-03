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

?>
<div class="prestashop-products-results">
    <!-- Carte des détails du site -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark">
                <i class="fas fa-shopping-cart text-warning me-2"></i>
                Détails du site : <?= Html::encode($model->url) ?>
            </h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-globe me-2 text-primary"></i> URL du site
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-link text-info"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->url) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-key me-2 text-warning"></i> Clé API
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-key text-warning"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($model->api_key) ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte des informations de recherche -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark">
                <i class="fas fa-search me-2 text-primary"></i>
                Informations de recherche
            </h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-barcode me-2 text-success"></i> Référence
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-barcode text-success"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= Html::encode($ref) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-boxes me-2 text-warning"></i> Type de produit
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-boxes text-warning"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= ucfirst(Html::encode($type)) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-layer-group me-2 text-info"></i> Type de variation
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-layer-group text-info"></i>
                            </span>
                            <input type="text" class="form-control" value="<?= $variation_type ? ucfirst(Html::encode($variation_type)) : 'N/A' ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultat de la recherche -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark">
                <i class="fas fa-file-alt me-2 text-primary"></i>
                Résultat de la recherche
            </h2>
        </div>
        <div class="card-body">
            <?php
            try {
                // Initialisation du service PrestaShop
                $prestaShopService = new PrestaShopProductService($model->url, $model->api_key);

                // Génération de l'URL selon le type de recherche
                if ($type === 'simple' || ($type === 'variation' && $variation_type === 'parent')) {
                    $urlweb = $prestaShopService->getUrl() . '/api/products/?filter[reference]=' . $ref . '&ws_key=' . $prestaShopService->getApiKey();
                } elseif ($type === 'variation' && $variation_type === 'child') {
                    $urlweb = $prestaShopService->getUrl() . '/api/combinations/?filter[reference]=' . $ref . '&ws_key=' . $prestaShopService->getApiKey();
                } else {
                    // Cas par défaut ou type inconnu
                    $urlweb = $prestaShopService->getUrl() . '/api/products/?filter[reference]=' . $ref . '&ws_key=' . $prestaShopService->getApiKey();
                }

                echo '<div class="alert alert-info mb-4">';
                echo '<h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>URL de l\'API</h5>';
                echo '<p class="mb-0">Résultat de la recherche sur le produit référence : <strong>' . $ref . '</strong> du site <strong>' . $prestaShopService->getUrl() . '</strong></p>';
                echo '<hr>';
                echo '<p class="mb-0"><a href="' . $urlweb . '" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-external-link-alt me-1"></i> Accéder à l\'API</a></p>';
                echo '</div>';

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
                echo '<div class="alert alert-danger">';
                echo '<h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreur</h5>';
                echo '<p class="mb-0">Erreur : ' . $e->getMessage() . '</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<style>
    .prestashop-products-results .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .prestashop-products-results .card-header {
        border-radius: 10px 10px 0 0 !important;
    }

    .prestashop-products-results .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .prestashop-products-results .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }

    .prestashop-products-results .input-group-text {
        border-radius: 8px 0 0 8px;
        border-right: none;
    }

    .prestashop-products-results .input-group .form-control {
        border-radius: 0 8px 8px 0;
    }

    .prestashop-products-results .detail-view th {
        font-weight: 600;
        color: #5c5c5c;
    }

    .prestashop-products-results .detail-view td {
        color: #495057;
    }

    .prestashop-products-results .alert {
        border-radius: 8px;
    }

    .prestashop-products-results .table thead th {
        background: linear-gradient(145deg, #5c5c5c, #4a4a4a) !important;
        color: white !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.85rem !important;
        letter-spacing: 0.5px !important;
        border: none !important;
        padding: 1rem 1.25rem !important;
        position: relative !important;
    }

    .prestashop-products-results .table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #f1ac16, transparent);
    }

    .prestashop-products-results .table-hover tbody tr:hover {
        background-color: rgba(241, 172, 22, 0.05) !important;
    }
</style>

<script>
    $(document).ready(function() {
        // Animation au chargement
        $('.prestashop-products-results .card').each(function(index) {
            $(this).css('opacity', '0').delay(200 * index).animate({
                opacity: 1
            }, 600);
        });

        // Animation pour les boutons
        $('.prestashop-products-results .btn').hover(
            function() {
                $(this).animate({
                    fontSize: '1.05rem'
                }, 100);
            },
            function() {
                $(this).animate({
                    fontSize: '1rem'
                }, 100);
            }
        );
    });
</script>

<?php
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

        echo '<h3 class="mb-4"><i class="fas fa-box text-success me-2"></i>Détails du Produit</h3>';
        echo '<div class="table-responsive">';
        echo PrestaShopViewHelper::renderSimpleProductGrid(
            $productList,
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey()
        );
        echo '</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-warning">';
        echo '<h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Avertissement</h5>';
        echo '<p class="mb-0">' . $e->getMessage() . '</p>';
        echo '</div>';
    }
}

/**
 * Traite l'affichage d'un produit parent avec ses déclinaisons
 */
function handleParentProduct($prestaShopService, $ref, $languageIso, $db_id)
{
    try {
        $result = $prestaShopService->getParentProductWithVariations($ref, $languageIso);

        echo '<h3 class="mb-4"><i class="fas fa-folder-open text-primary me-2"></i>Détails du produit parent</h3>';
        echo '<div class="table-responsive">';
        echo PrestaShopViewHelper::renderParentProductGrid(
            $result['product'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey()
        );
        echo '</div>';

        echo '<h3 class="mt-5 mb-4"><i class="fas fa-exchange-alt text-warning me-2"></i>Liste des déclinaisons</h3>';
        echo '<div class="table-responsive">';
        echo PrestaShopViewHelper::renderCombinationsGrid(
            $result['combinations'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey(),
            $db_id
        );
        echo '</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-warning">';
        echo '<h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Avertissement</h5>';
        echo '<p class="mb-0">' . $e->getMessage() . '</p>';
        echo '</div>';
    }
}

/**
 * Traite l'affichage d'un produit enfant (déclinaison)
 */
function handleChildProduct($prestaShopService, $ref, $languageIso, $db_id)
{
    try {
        $result = $prestaShopService->getChildCombination($ref, $languageIso);

        echo '<h3 class="mb-4"><i class="fas fa-boxes text-info me-2"></i>Détails du Produit</h3>';
        echo '<div class="table-responsive">';
        echo PrestaShopViewHelper::renderChildCombinationGrid(
            $result['combinations'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey(),
            $db_id
        );
        echo '</div>';

        echo '<h3 class="mt-5 mb-4"><i class="fas fa-tag text-success me-2"></i>Tarifs spécifiques</h3>';
        echo '<div class="table-responsive">';
        echo PrestaShopViewHelper::renderSpecificPricesGrid(
            $result['specific_prices'],
            $prestaShopService->getUrl(),
            $prestaShopService->getApiKey()
        );
        echo '</div>';
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

    echo '<div class="alert alert-danger">';
    echo '<h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreur détectée</h5>';
    echo '<p class="mb-0">' . $e->getMessage() . '</p>';

    if ($rawResponse) {
        echo '<hr>';
        echo '<p class="mb-0">Réponse brute : ' . PHP_EOL . htmlspecialchars($rawResponse) . '</p>';

        if (strpos($rawResponse, '<!DOCTYPE html>') !== false) {
            echo '<p class="mb-0 text-warning">Erreur HTML détectée</p>';
        } elseif (strpos($rawResponse, '<?xml') === 0) {
            $xml = simplexml_load_string($rawResponse);
            if ($xml !== false) {
                echo '<pre class="mt-2 p-2 bg-light rounded">' . htmlspecialchars(print_r($xml, true)) . '</pre>';
            } else {
                echo '<p class="mb-0 text-warning">Erreur lors du parsing XML</p>';
            }
        } else {
            echo '<p class="mb-0 text-warning">Format de réponse inconnu</p>';
        }
    } else {
        echo '<p class="mb-0 text-warning">Aucune réponse brute disponible</p>';
    }
    echo '</div>';
}
?>