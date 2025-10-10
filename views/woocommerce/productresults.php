<?php

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */
/** @var string $ref - Le SKU recherché */

// Définir le titre et les breadcrumbs
$this->title = 'Produit SKU: ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'WooCommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de produit', 'url' => ['products', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

// Initialiser les variables
$url = Html::encode($model->url);
$consumer_key = Html::encode($model->consumer_key);
$consumer_secret = Html::encode($model->consumer_secret);
$ref = Html::encode($ref); // Ne pas encoder ici pour la requête API

// Forcer HTTPS pour l'URL
$url = "https://" . ltrim($url, "https://");

// Initialiser le client WooCommerce
$client = new Client(
    $url,
    $consumer_key,
    $consumer_secret,
    [
        'version' => 'wc/v3',
        'verify_ssl' => false, // Désactiver la vérification SSL pour les tests
    ]
);

// Initialiser les tableaux de données
$products = [];
$rawProducts = [];

try {
    // Récupérer les produits par SKU
    $rawProducts = $client->get('products', ['sku' => $ref]);

    if (!empty($rawProducts)) {
        // S'assurer que c'est un tableau
        if (!is_array($rawProducts)) {
            $rawProducts = [$rawProducts];
        }
        foreach ($rawProducts as $product) {
            $products[] = prepareProductDetails($product);
        }
    }
} catch (HttpClientException $e) {
    handleHttpClientException($e);
}

// Styles personnalisés
$this->registerCss("
    :root {
        --primary-color: #f1ac16;
        --bg-color: #f6f4f0;
        --text-color: #5c5c5c;
        --card-shadow: 0 2px 12px rgba(241, 172, 22, 0.08);
        --card-shadow-hover: 0 8px 24px rgba(241, 172, 22, 0.15);
    }
    
    body {
        background-color: var(--bg-color);
        color: var(--text-color);
    }
    
    .product-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #e69500 100%);
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow-hover);
        position: relative;
        overflow: hidden;
    }
    
    .product-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .product-header h1 {
        color: white;
        margin: 0;
        font-weight: 700;
        font-size: 2rem;
        position: relative;
        z-index: 1;
    }
    
    .product-reference {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-top: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .products-count {
        background: rgba(255,255,255,0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        display: inline-block;
        margin-top: 1rem;
        position: relative;
        z-index: 1;
    }
    
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border-left: 4px solid var(--primary-color);
    }
    
    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
    }
    
    .info-card h3 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        font-size: 1.3rem;
    }
    
    .info-card h3::before {
        content: '';
        width: 4px;
        height: 24px;
        background: var(--primary-color);
        margin-right: 12px;
        border-radius: 2px;
    }
    
    .product-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border-top: 4px solid var(--primary-color);
    }
    
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
    }
    
    .product-title {
        color: var(--text-color);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .product-image-container {
        float: right;
        margin: 0 0 1rem 2rem;
        max-width: 300px;
    }
    
    .product-image {
        width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
    }
    
    .product-gallery {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
        flex-wrap: wrap;
    }
    
    .product-gallery img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .product-gallery img:hover {
        border-color: var(--primary-color);
        transform: scale(1.1);
    }
    
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        text-align: center;
        border-top: 3px solid var(--primary-color);
    }
    
    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: var(--card-shadow-hover);
    }
    
    .stat-label {
        color: var(--text-color);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-value {
        color: var(--primary-color);
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .data-item {
        background: #fef9f0;
        padding: 1rem;
        border-radius: 8px;
        border-left: 3px solid var(--primary-color);
    }
    
    .data-item-label {
        font-size: 0.85rem;
        color: var(--text-color);
        opacity: 0.8;
        margin-bottom: 0.3rem;
    }
    
    .data-item-value {
        font-weight: 600;
        color: var(--text-color);
        font-size: 1.1rem;
    }
    
    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .status-publish { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
    .status-draft { background-color: rgba(108, 117, 125, 0.2); color: #6c757d; }
    .status-pending { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .status-private { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .status-importing { background-color: rgba(0, 123, 255, 0.2); color: #007bff; }
    
    .stock-instock { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
    .stock-outofstock { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .stock-onbackorder { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }
    
    .type-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-block;
        background: linear-gradient(135deg, var(--primary-color) 0%, #e69500 100%);
        color: white;
    }
    
    .categories-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .category-badge, .tag-badge {
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .category-badge {
        background-color: rgba(241, 172, 22, 0.2);
        color: var(--primary-color);
    }
    
    .tag-badge {
        background-color: rgba(108, 117, 125, 0.2);
        color: #6c757d;
    }
    
    .description-box {
        background: #fef9f0;
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
        margin: 1rem 0;
    }
    
    .price-box {
        background: linear-gradient(135deg, #fef9f0 0%, white 100%);
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px solid var(--primary-color);
        margin: 1rem 0;
    }
    
    .price-display {
        font-size: 2rem;
        color: var(--primary-color);
        font-weight: 700;
    }
    
    .regular-price {
        font-size: 1.5rem;
        color: var(--text-color);
        text-decoration: line-through;
        opacity: 0.6;
        margin-right: 1rem;
    }
    
    .sale-badge {
        background: #dc3545;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        display: inline-block;
        margin-left: 1rem;
    }
    
    .dimensions-box {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        display: inline-block;
        margin: 0.5rem 0;
    }
    
    .api-info {
        background: linear-gradient(135deg, #fef9f0 0%, white 100%);
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px solid var(--primary-color);
        margin-bottom: 2rem;
    }
    
    .api-info h3 {
        color: var(--primary-color);
        margin-top: 0;
    }
    
    .api-link {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: #fef9f0;
        border: 1px solid var(--primary-color);
        border-radius: 6px;
        color: var(--primary-color);
        text-decoration: none;
        transition: all 0.3s ease;
        margin: 0.5rem 0.5rem 0.5rem 0;
    }
    
    .api-link:hover {
        background: var(--primary-color);
        color: white;
        transform: translateX(4px);
    }
    
    .divider {
        height: 2px;
        background: linear-gradient(to right, transparent, var(--primary-color), transparent);
        margin: 2rem 0;
    }
    
    @media (max-width: 768px) {
        .product-image-container {
            float: none;
            margin: 0 0 1rem 0;
            max-width: 100%;
        }
        
        .stat-grid {
            grid-template-columns: 1fr;
        }
        
        .product-title {
            font-size: 1.4rem;
        }
        
        .data-grid {
            grid-template-columns: 1fr;
        }
    }
");

// JavaScript pour les interactions
$this->registerJs("
    // Animation au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.info-card, .stat-card, .product-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
    
    // Galerie d'images
    document.querySelectorAll('.product-gallery img').forEach(img => {
        img.addEventListener('click', function() {
            const mainImage = this.closest('.product-image-container').querySelector('.product-image');
            mainImage.src = this.src;
        });
    });
");

echo '<div class="container-fluid mb-4">';
echo '<div class="api-info">';
echo '<h3>🔗 Informations API</h3>';
echo '<div class="detail-view">';
echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'url',
        'consumer_key',
        'consumer_secret',
    ],
]);
echo '</div>';
echo '</div>';
echo '</div>';

?>

<div class="container-fluid">
    <!-- En-tête des produits -->
    <div class="product-header">
        <h1>📦 Recherche de produit par SKU</h1>
        <div class="product-reference">SKU recherché : <strong><?= Html::encode($ref) ?></strong></div>
        <?php if (!empty($products)): ?>
            <div class="products-count">
                <?= count($products) ?> produit<?= count($products) > 1 ? 's' : '' ?> trouvé<?= count($products) > 1 ? 's' : '' ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($products)): ?>
        <?php foreach ($products as $index => $product): ?>
            <?php if ($index > 0): ?>
                <div class="divider"></div>
            <?php endif; ?>

            <!-- Carte produit -->
            <div class="product-card">
                <!-- Images du produit -->
                <?php if (!empty($product['images'])): ?>
                    <div class="product-image-container">
                        <img src="<?= Html::encode($product['images'][0]->src ?? '') ?>" alt="<?= Html::encode($product['name'] ?? '') ?>" class="product-image">
                        <?php if (count($product['images']) > 1): ?>
                            <div class="product-gallery">
                                <?php foreach (array_slice($product['images'], 1) as $image): ?>
                                    <img src="<?= Html::encode($image->src) ?>" alt="<?= Html::encode($product['name']) ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Titre et badges -->
                <div class="product-title">
                    <?= Html::encode($product['name']) ?>
                    <span class="type-badge"><?= strtoupper(Html::encode($product['type'])) ?></span>
                </div>

                <!-- Lien API -->
                <a href="<?= $url ?>/wp-json/wc/v3/products/<?= $product['id'] ?>?consumer_key=<?= $consumer_key ?>&consumer_secret=<?= $consumer_secret ?>" target="_blank" class="api-link">
                    🔗 Voir dans l'API
                </a>

                <!-- Statistiques principales -->
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-label">Prix</div>
                        <div class="stat-value">
                            <?php if ($product['on_sale']): ?>
                                <span class="regular-price" style="font-size: 1.2rem;"><?= Html::encode($product['regular_price']) ?> €</span>
                                <br><?= Html::encode($product['sale_price']) ?> €
                                <span class="sale-badge">PROMO</span>
                            <?php else: ?>
                                <?= Html::encode($product['price'] ?: 'N/A') ?> <?= $product['price'] ? '€' : '' ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Statut</div>
                        <div class="stat-value" style="font-size: 1.2rem;">
                            <span class="badge-status status-<?= strtolower(Html::encode($product['status'])) ?>"><?= strtoupper(Html::encode($product['status'])) ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Stock</div>
                        <div class="stat-value" style="font-size: 1.2rem;">
                            <span class="badge-status stock-<?= strtolower(Html::encode($product['stock_status'])) ?>"><?= strtoupper(str_replace('_', ' ', Html::encode($product['stock_status']))) ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Ventes totales</div>
                        <div class="stat-value"><?= $product['total_sales'] ?></div>
                    </div>
                </div>

                <!-- Informations principales -->
                <div class="info-card">
                    <h3>📋 Informations principales</h3>
                    <div class="data-grid">
                        <div class="data-item">
                            <div class="data-item-label">ID Produit</div>
                            <div class="data-item-value">#<?= $product['id'] ?></div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">SKU</div>
                            <div class="data-item-value"><?= Html::encode($product['sku'] ?: 'N/A') ?></div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">Type</div>
                            <div class="data-item-value"><?= strtoupper(Html::encode($product['type'])) ?></div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">Date de création</div>
                            <div class="data-item-value"><?= formatDate($product['date_created']) ?></div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">Dernière modification</div>
                            <div class="data-item-value"><?= formatDate($product['date_modified']) ?></div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">Note moyenne</div>
                            <div class="data-item-value"><?= Html::encode($product['average_rating']) ?> ⭐ (<?= $product['rating_count'] ?> avis)</div>
                        </div>
                    </div>

                    <!-- Catégories et tags -->
                    <?php if (!empty($product['categories']) || !empty($product['tags'])): ?>
                        <div style="margin-top: 1.5rem;">
                            <?php if (!empty($product['categories'])): ?>
                                <div style="margin-bottom: 1rem;">
                                    <strong style="color: var(--primary-color);">Catégories :</strong>
                                    <div class="categories-tags">
                                        <?php foreach ($product['categories'] as $category): ?>
                                            <span class="category-badge"><?= Html::encode($category->name) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($product['tags'])): ?>
                                <div>
                                    <strong style="color: var(--primary-color);">Tags :</strong>
                                    <div class="categories-tags">
                                        <?php foreach ($product['tags'] as $tag): ?>
                                            <span class="tag-badge"><?= Html::encode($tag->name) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <?php if (!empty($product['description'])): ?>
                    <div class="info-card">
                        <h3>📝 Description</h3>
                        <div class="description-box">
                            <?= $product['description'] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Description courte -->
                <?php if (!empty($product['short_description'])): ?>
                    <div class="info-card">
                        <h3>📄 Description courte</h3>
                        <div class="description-box">
                            <?= $product['short_description'] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Caractéristiques techniques -->
                <div class="info-card">
                    <h3>⚙️ Caractéristiques techniques</h3>
                    <div class="data-grid">
                        <?php if ($product['manage_stock']): ?>
                            <div class="data-item">
                                <div class="data-item-label">Quantité en stock</div>
                                <div class="data-item-value"><?= $product['stock_quantity'] ?? 'N/A' ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($product['weight'])): ?>
                            <div class="data-item">
                                <div class="data-item-label">Poids</div>
                                <div class="data-item-value"><?= Html::encode($product['weight']) ?> kg</div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($product['dimensions']['length']) || !empty($product['dimensions']['width']) || !empty($product['dimensions']['height'])): ?>
                            <div class="data-item">
                                <div class="data-item-label">Dimensions (L x l x H)</div>
                                <div class="data-item-value">
                                    <?= Html::encode($product['dimensions']['length'] ?? '0') ?> x
                                    <?= Html::encode($product['dimensions']['width'] ?? '0') ?> x
                                    <?= Html::encode($product['dimensions']['height'] ?? '0') ?> cm
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="data-item">
                            <div class="data-item-label">Virtuel</div>
                            <div class="data-item-value"><?= $product['virtual'] ? '✅ Oui' : '❌ Non' ?></div>
                        </div>

                        <div class="data-item">
                            <div class="data-item-label">Téléchargeable</div>
                            <div class="data-item-value"><?= $product['downloadable'] ? '✅ Oui' : '❌ Non' ?></div>
                        </div>

                        <div class="data-item">
                            <div class="data-item-label">Vendu individuellement</div>
                            <div class="data-item-value"><?= $product['sold_individually'] ? '✅ Oui' : '❌ Non' ?></div>
                        </div>
                    </div>
                </div>

                <!-- Attributs du produit -->
                <?php if (!empty($product['attributes'])): ?>
                    <div class="info-card">
                        <h3>🏷️ Attributs</h3>
                        <div class="data-grid">
                            <?php foreach ($product['attributes'] as $attribute): ?>
                                <div class="data-item">
                                    <div class="data-item-label"><?= Html::encode($attribute->name) ?></div>
                                    <div class="data-item-value">
                                        <?php if (!empty($attribute->options)): ?>
                                            <?= implode(', ', array_map(function ($opt) {
                                                return Html::encode($opt);
                                            }, $attribute->options)) ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-warning" style="background: white; border-left: 4px solid #ffc107; padding: 2rem; border-radius: 12px;">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Aucun produit trouvé</h4>
            <p>Aucun produit n'a été trouvé avec le SKU <strong><?= Html::encode($ref) ?></strong>.</p>
        </div>
    <?php endif; ?>
</div>

<?php

/**
 * Fonctions utilitaires
 */

// Préparer les détails du produit
function prepareProductDetails($product)
{
    // S'assurer que $product est un objet stdClass
    if (is_array($product)) {
        $product = (object) $product;
    }

    return [
        'id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'permalink' => $product->permalink,
        'type' => $product->type,
        'status' => $product->status,
        'sku' => $product->sku,
        'price' => $product->price,
        'regular_price' => $product->regular_price,
        'sale_price' => $product->sale_price,
        'on_sale' => $product->on_sale,
        'purchasable' => $product->purchasable,
        'total_sales' => $product->total_sales,
        'virtual' => $product->virtual,
        'downloadable' => $product->downloadable,
        'manage_stock' => $product->manage_stock,
        'stock_quantity' => $product->stock_quantity,
        'stock_status' => $product->stock_status,
        'weight' => $product->weight,
        'dimensions' => [
            'length' => $product->dimensions->length ?? null,
            'width' => $product->dimensions->width ?? null,
            'height' => $product->dimensions->height ?? null,
        ],
        'sold_individually' => $product->sold_individually,
        'average_rating' => $product->average_rating,
        'rating_count' => $product->rating_count,
        'categories' => $product->categories,
        'tags' => $product->tags,
        'images' => $product->images,
        'attributes' => $product->attributes,
        'description' => $product->description,
        'short_description' => $product->short_description,
        'date_created' => $product->date_created,
        'date_modified' => $product->date_modified,
    ];
}

// Gérer les exceptions HTTP
function handleHttpClientException($e)
{
    $response = $e->getResponse();
    $errorCode = $response instanceof \Automattic\WooCommerce\HttpClient\Response ? $response->getCode() : null;
    $errorMessage = $e->getMessage();

    switch ($errorCode) {
        case 404:
            $message = "Produit introuvable (Erreur 404).";
            break;
        case 403:
            $message = "Accès interdit (Erreur 403). Vérifiez vos clés API.";
            break;
        case 500:
            $message = "Erreur interne du serveur (Erreur 500).";
            break;
        default:
            $message = "Erreur WooCommerce : $errorMessage (Code: $errorCode).";
    }

    \Yii::$app->session->setFlash('error', $message);
}

// Formater une date
function formatDate($date)
{
    if (empty($date)) {
        return 'N/A';
    }
    return \Yii::$app->formatter->asDatetime($date, 'php:d/m/Y H:i:s');
}

// Formater une valeur monétaire
function formatCurrency($value)
{
    if ($value === null || $value === '') {
        return 'N/A';
    }
    return \Yii::$app->formatter->asCurrency($value, 'EUR');
}
?>
```