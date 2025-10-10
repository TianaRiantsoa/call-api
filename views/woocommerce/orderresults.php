<?php

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */
/** @var string $ref - La référence (ID) de la commande recherchée */

// Définir le titre et les breadcrumbs
$this->title = 'Commande ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'WooCommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de commande', 'url' => ['orders', 'id' => $model->id]];
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
$client = new Client($url, $consumer_key, $consumer_secret, [
    'version' => 'wc/v3',
    'verify_ssl' => false, // Désactiver la vérification SSL pour les tests
]);

// Initialiser les tableaux de données
$orderDetails = [];
$customerDetails = [];
$billingShippingDetails = [];
$productDetails = [];
$taxDetails = [];
$shippingDetails = [];
$metaDetails = [];
$refundDetails = [];

try {
    // Récupérer les données de la commande
    $order = $client->get('orders/' . $ref);

    // Récupérer les informations du client
    if (!empty($order->customer_id) && $order->customer_id != 0) {
        $customerDetails = getCustomerDetails($client, $order->customer_id);
    }

    // Préparer les détails de la commande
    $orderDetails = prepareOrderDetails($order);

    // Préparer les informations de facturation et livraison
    $billingShippingDetails = prepareBillingShippingDetails($order);

    // Préparer les détails des produits
    $productDetails = prepareProductDetails($order);

    // Préparer les détails des taxes
    $taxDetails = prepareTaxDetails($order);

    // Préparer les détails des frais de livraison
    $shippingDetails = prepareShippingDetails($order);

    // Préparer les métadonnées
    $metaDetails = prepareMetaDetails($order);

    // Préparer les remboursements
    $refundDetails = prepareRefundDetails($order);
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
    
    .order-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #e69500 100%);
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow-hover);
        position: relative;
        overflow: hidden;
    }
    
    .order-header::before {
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
    
    .order-header h1 {
        color: white;
        margin: 0;
        font-weight: 700;
        font-size: 2rem;
        position: relative;
        z-index: 1;
    }
    
    .order-reference {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-top: 0.5rem;
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
    
    .table-wrapper {
        background: white;
        border-radius: 12px;
        padding: 0;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        margin-bottom: 2rem;
    }
    
    .table-header {
        background: linear-gradient(90deg, var(--primary-color) 0%, #e69500 100%);
        padding: 1rem 1.5rem;
        color: white;
    }
    
    .table-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .table {
        margin: 0;
    }
    
    .table thead th {
        background-color: #fef9f0;
        color: var(--text-color);
        font-weight: 600;
        border-bottom: 2px solid var(--primary-color);
        padding: 1rem;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #fef9f0;
        transform: scale(1.01);
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .address-card {
        background: #fef9f0;
        padding: 1.2rem;
        border-radius: 8px;
        border-left: 3px solid var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .address-type {
        color: var(--primary-color);
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.8rem;
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
        margin-bottom: 1rem;
    }
    
    .api-link:hover {
        background: var(--primary-color);
        color: white;
        transform: translateX(4px);
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(241, 172, 22, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .product-name {
        line-height: 1.6;
    }
    
    .product-type {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        background: var(--primary-color);
        color: white;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
    
    .currency {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .timeline {
        position: relative;
        padding: 2rem 0;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, var(--primary-color), transparent);
    }
    
    .timeline-item {
        position: relative;
        padding-left: 70px;
        margin-bottom: 2rem;
    }
    
    .timeline-dot {
        position: absolute;
        left: 20px;
        width: 20px;
        height: 20px;
        background: var(--primary-color);
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(241, 172, 22, 0.2);
        animation: pulse-dot 2s ease-in-out infinite;
    }
    
    @keyframes pulse-dot {
        0%, 100% { box-shadow: 0 0 0 3px rgba(241, 172, 22, 0.2); }
        50% { box-shadow: 0 0 0 8px rgba(241, 172, 22, 0.1); }
    }
    
    .price-total {
        background: linear-gradient(135deg, #fef9f0 0%, white 100%);
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px solid var(--primary-color);
        margin-top: 1rem;
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .price-row:last-child {
        border-bottom: none;
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-top: 0.5rem;
        padding-top: 1rem;
        border-top: 2px solid var(--primary-color);
    }
    
    .icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #e69500 100%);
        border-radius: 10px;
        margin-right: 12px;
        color: white;
        font-size: 1.2rem;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.6rem 1.2rem;
        background: linear-gradient(135deg, #fef9f0 0%, white 100%);
        border: 2px solid var(--primary-color);
        border-radius: 25px;
        font-weight: 600;
        color: var(--text-color);
        gap: 8px;
    }
    
    .status-badge::before {
        content: '●';
        color: var(--primary-color);
        font-size: 1.2rem;
        animation: blink 2s ease-in-out infinite;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    
    .info-row {
        display: flex;
        align-items: flex-start;
        padding: 0.8rem 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--primary-color);
        min-width: 140px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-value {
        color: var(--text-color);
        flex: 1;
    }
    
    .product-image-placeholder {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #fef9f0 0%, #f5f5f5 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-weight: 600;
        margin-right: 1rem;
    }
    
    .quick-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.7rem 1.5rem;
        background: white;
        border: 2px solid var(--primary-color);
        border-radius: 8px;
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
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
    
    .api-url {
        word-break: break-all;
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid var(--primary-color);
        font-family: monospace;
        margin: 1rem 0;
    }
    
    .detail-view {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
    }
    
    .detail-view th {
        background: #fef9f0;
        color: var(--text-color);
        font-weight: 600;
        padding: 0.8rem;
        border: 1px solid #f0f0f0;
    }
    
    .detail-view td {
        padding: 0.8rem;
        border: 1px solid #f0f0f0;
    }
    
    .status-badge-wc {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .status-pending { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .status-processing { background-color: rgba(0, 123, 255, 0.2); color: #007bff; }
    .status-on-hold { background-color: rgba(108, 117, 125, 0.2); color: #6c757d; }
    .status-completed { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
    .status-cancelled { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .status-refunded { background-color: rgba(108, 117, 125, 0.2); color: #6c757d; }
    .status-failed { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    
    .status-payment-pending { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .status-payment-processing { background-color: rgba(0, 123, 255, 0.2); color: #007bff; }
    .status-payment-on-hold { background-color: rgba(108, 117, 125, 0.2); color: #6c757d; }
    .status-payment-completed { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
    .status-payment-cancelled { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .status-payment-refunded { background-color: rgba(108, 117, 125, 0.2); color: #6c757d; }
    .status-payment-failed { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    
    .tax-line, .shipping-line {
        padding: 0.8rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
    }
    
    .tax-line:last-child, .shipping-line:last-child {
        border-bottom: none;
    }
    
    .meta-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .meta-item:last-child {
        border-bottom: none;
    }
    
    .meta-key {
        font-weight: 600;
        color: var(--primary-color);
        display: inline-block;
        min-width: 120px;
    }
    
    .meta-value {
        color: var(--text-color);
    }
    
    .refund-item {
        padding: 0.8rem 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .refund-item:last-child {
        border-bottom: none;
    }
    
    .refund-amount {
        color: #dc3545;
        font-weight: 600;
    }
    
    .refund-date {
        font-size: 0.9em;
        color: var(--text-color);
        opacity: 0.8;
    }
    
    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }
        
        .order-header h1 {
            font-size: 1.5rem;
        }
        
        .data-grid {
            grid-template-columns: 1fr;
        }
        
        .info-row {
            flex-direction: column;
        }
        
        .info-label {
            min-width: 100%;
            margin-bottom: 0.3rem;
        }
        
        .table-responsive {
            font-size: 0.85rem;
        }
        
        .table th, .table td {
            padding: 0.5rem;
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
    
    document.querySelectorAll('.info-card, .stat-card, .table-wrapper').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
    
    // Effet hover sur les lignes de tableau
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 4px 12px rgba(241, 172, 22, 0.15)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });
    
    // Animation compteur pour les statistiques
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value.toLocaleString('fr-FR', {style: 'currency', currency: 'EUR'});
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    // Animer les valeurs des stats au chargement
    document.querySelectorAll('.stat-value').forEach(stat => {
        const text = stat.textContent.trim();
        if (text.includes('€')) {
            const value = parseFloat(text.replace(/[^0-9,]/g, '').replace(',', '.'));
            if (!isNaN(value)) {
                stat.textContent = '0,00 €';
                setTimeout(() => animateValue(stat, 0, value, 1500), 300);
            }
        }
    });
    
    // Copier dans le presse-papier
    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = button.textContent;
            button.textContent = '✓ Copié !';
            button.style.background = 'var(--primary-color)';
            button.style.color = 'white';
            setTimeout(() => {
                button.textContent = originalText;
                button.style.background = '';
                button.style.color = '';
            }, 2000);
        });
    }
    
    // Ajouter des boutons de copie
    document.querySelectorAll('.info-value').forEach(el => {
        const text = el.textContent.trim();
        if (text && text.length > 5) {
            const copyBtn = document.createElement('button');
            copyBtn.innerHTML = '📋';
            copyBtn.className = 'btn btn-sm btn-outline-secondary ms-2';
            copyBtn.style.cssText = 'border: 1px solid var(--primary-color); color: var(--primary-color); padding: 0.2rem 0.5rem;';
            copyBtn.onclick = () => copyToClipboard(text, copyBtn);
            el.appendChild(copyBtn);
        }
    });
    
    // Impression de la page
    window.printOrder = function() {
        window.print();
    }
    
    // Téléchargement en PDF (simulation)
    window.downloadPDF = function() {
        alert('Fonctionnalité de téléchargement PDF à implémenter avec une bibliothèque comme jsPDF ou en générant le PDF côté serveur.');
    }
    
    // Animation de chargement pour les boutons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.textContent.includes('Télécharger')) {
                this.innerHTML = '<span class=\"loading-spinner\"></span> Génération...';
                setTimeout(() => {
                    this.innerHTML = '📄 Télécharger PDF';
                }, 2000);
            }
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
    <!-- En-tête de la commande -->
    <div class="order-header">
        <h1>🛒 Détails de la commande WooCommerce</h1>
        <div class="order-reference">Référence : <strong><?= Html::encode($ref) ?></strong></div>
        <?php if (!empty($orderDetails)): ?>
            <!-- <div class="quick-actions">
                <a href="<?= $url ?>/wp-json/wc/v3/orders/<?= $ref ?>?consumer_key=<?= $consumer_key ?>&consumer_secret=<?= $consumer_secret ?>" target="_blank" class="action-btn">
                    🔗 API WooCommerce
                </a>
                <button onclick="window.print()" class="action-btn">
                    🖨️ Imprimer
                </button>
                <button onclick="alert('Fonctionnalité PDF : Veuillez utiliser la fonction Imprimer et sélectionner \'Enregistrer en PDF\' comme destination.')" class="action-btn">
                    📄 Télécharger PDF
                </button>
            </div> -->
        <?php endif; ?>
    </div>

    <?php if (!empty($orderDetails)): ?>
        <!-- Statistiques principales -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Montant Total</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($orderDetails[0]['total'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Taxes</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($orderDetails[0]['total_tax'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Hors Taxes</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($orderDetails[0]['total_ht'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Méthode de paiement</div>
                <div class="stat-value" style="font-size: 1.3rem;"><?= Html::encode($orderDetails[0]['payment_method_title']) ?></div>
            </div>
        </div>

        <!-- Informations de la commande -->
        <div class="info-card">
            <h3>📋 Informations de la commande</h3>

            <div class="data-grid">
                <div class="data-item">
                    <div class="data-item-label">ID Commande</div>
                    <div class="data-item-value">
                        <a href="<?= $url ?>/wp-json/wc/v3/orders/<?= $ref ?>?consumer_key=<?= $consumer_key ?>&consumer_secret=<?= $consumer_secret ?>" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                            #<?= $orderDetails[0]['id'] ?>
                        </a>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Statut</div>
                    <div class="data-item-value">
                        <span class="status-badge status-<?= strtolower(Html::encode($orderDetails[0]['status'])) ?>"><?= Html::encode($orderDetails[0]['status']) ?></span>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Numéro</div>
                    <div class="data-item-value"><?= Html::encode($orderDetails[0]['number']) ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Date de création</div>
                    <div class="data-item-value"><?= formatDate($orderDetails[0]['date_created']) ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Date de modification</div>
                    <div class="data-item-value"><?= formatDate($orderDetails[0]['date_modified']) ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Date de paiement</div>
                    <div class="data-item-value"><?= $orderDetails[0]['date_paid'] ? formatDate($orderDetails[0]['date_paid']) : 'N/A' ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Clé de commande</div>
                    <div class="data-item-value"><?= Html::encode($orderDetails[0]['order_key']) ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">IP Client</div>
                    <div class="data-item-value"><?= Html::encode($orderDetails[0]['customer_ip_address']) ?></div>
                </div>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="info-row">
                        <span class="info-label">📅 Création</span>
                        <span class="info-value"><?= formatDate($orderDetails[0]['date_created']) ?></span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="info-row">
                        <span class="info-label">🔄 Dernière mise à jour</span>
                        <span class="info-value"><?= formatDate($orderDetails[0]['date_modified']) ?></span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="info-row">
                        <span class="info-label">💳 Paiement</span>
                        <span class="info-value"><?= $orderDetails[0]['date_paid'] ? formatDate($orderDetails[0]['date_paid']) : 'Non payée' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations client -->
        <?php if (!empty($customerDetails)): ?>
            <div class="info-card">
                <h3>👤 Informations du client</h3>
                <?php foreach ($customerDetails as $customer): ?>
                    <div class="data-grid">
                        <div class="data-item">
                            <div class="data-item-label">ID Client</div>
                            <div class="data-item-value">
                                <a href="<?= $url ?>/wp-json/wc/v3/customers/<?= $customer['id'] ?>?consumer_key=<?= $consumer_key ?>&consumer_secret=<?= $consumer_secret ?>" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                    #<?= $customer['id'] ?>
                                </a>
                            </div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">Nom complet</div>
                            <div class="data-item-value"><?= Html::encode($customer['first_name'] . ' ' . $customer['last_name']) ?></div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">Email</div>
                            <div class="data-item-value">
                                <a href="mailto:<?= Html::encode($customer['email']) ?>" style="color: var(--primary-color); text-decoration: none;">
                                    <?= Html::encode($customer['email']) ?>
                                </a>
                            </div>
                        </div>
                        <div class="data-item">
                            <div class="data-item-label">Date d'inscription</div>
                            <div class="data-item-value"><?= formatDate($customer['date_created']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Adresses -->
        <?php if (!empty($billingShippingDetails)): ?>
            <div class="info-card">
                <h3>📍 Adresses</h3>
                <div class="row">
                    <?php foreach ($billingShippingDetails as $address): ?>
                        <div class="col-md-6 mb-3">
                            <div class="address-card">
                                <div class="address-type">
                                    <?= $address['type'] === 'Facturation' ? '🧾' : '📦' ?>
                                    <?= Html::encode($address['type']) ?>
                                </div>
                                <p style="margin: 0.3rem 0;">
                                    <strong><?= Html::encode($address['name']) ?></strong>
                                </p>
                                <p style="margin: 0.3rem 0; color: var(--text-color);">
                                    <?= Html::encode($address['address']) ?>
                                </p>
                                <?php if ($address['phone']): ?>
                                    <p style="margin: 0.5rem 0 0.3rem 0;">
                                        <span style="color: var(--primary-color);">📞</span>
                                        <a href="tel:<?= Html::encode($address['phone']) ?>" style="color: var(--text-color); text-decoration: none;">
                                            <?= Html::encode($address['phone']) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                <?php if ($address['email']): ?>
                                    <p style="margin: 0.3rem 0;">
                                        <span style="color: var(--primary-color);">✉️</span>
                                        <a href="mailto:<?= Html::encode($address['email']) ?>" style="color: var(--text-color); text-decoration: none;">
                                            <?= Html::encode($address['email']) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Produits commandés -->
        <?php if (!empty($productDetails)): ?>
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>Produits commandés</h3>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID Produit</th>
                                <th>Image</th>
                                <th>SKU</th>
                                <th>Produit</th>
                                <th style="text-align: center;">Qté</th>
                                <th style="text-align: right;">P.U HT</th>
                                <th style="text-align: right;">Taxes</th>
                                <th style="text-align: right;">Total HT</th>
                                <th style="text-align: right;">Total TTC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order->line_items as $item): ?>
                                <tr>
                                    <td>
                                        <a href="<?= $url ?>/wp-json/wc/v3/products/<?= $item->product_id ?>?consumer_key=<?= $consumer_key ?>&consumer_secret=<?= $consumer_secret ?>" target="_blank">
                                            #<?= $item->product_id ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($item->image->src)): ?>
                                            <img src="<?= Html::encode($item->image->src) ?>" alt="<?= Html::encode($item->name) ?>" style="width: 50px; height: auto; border-radius: 4px;">
                                        <?php else: ?>
                                            <div class="product-image-placeholder">N/A</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= Html::encode($item->sku) ?></td>
                                    <td class="product-name">
                                        <?= Html::encode($item->name) ?>
                                        <?php if ($item->variation_id): ?>
                                            <span class="product-type">Variante #<?= $item->variation_id ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($item->meta_data)): ?>
                                            <div style="margin-top: 0.5rem;">
                                                <?php foreach ($item->meta_data as $meta): ?>
                                                    <small style="display: block; color: var(--text-color); opacity: 0.8;">
                                                        <strong><?= Html::encode($meta->display_key) ?>:</strong> <?= Html::encode(is_array($meta->display_value) || is_object($meta->display_value) ? json_encode($meta->display_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $meta->display_value) ?>
                                                    </small>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;"><strong><?= $item->quantity ?></strong></td>
                                    <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($item->price, 'EUR') ?></td>
                                    <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($item->total_tax, 'EUR') ?></td>
                                    <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($item->total, 'EUR') ?></td>
                                    <td style="text-align: right;"><strong class="currency"><?= Yii::$app->formatter->asCurrency($item->total + $item->total_tax, 'EUR') ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Détails des taxes -->
        <?php if (!empty($taxDetails)): ?>
            <div class="info-card">
                <h3>💰 Détails des taxes</h3>
                <div class="data-grid">
                    <div class="data-item">
                        <div class="data-item-label">Taxe totale</div>
                        <div class="data-item-value"><?= Yii::$app->formatter->asCurrency($orderDetails[0]['total_tax'], 'EUR') ?></div>
                    </div>
                </div>
                <?php foreach ($taxDetails as $tax): ?>
                    <div class="tax-line">
                        <span class="tax-label"><?= Html::encode($tax['label']) ?> (<?= $tax['rate_percent'] ?>%)</span>
                        <span class="tax-amount currency"><?= Yii::$app->formatter->asCurrency($tax['tax_total'], 'EUR') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Détails de la livraison -->
        <?php if (!empty($shippingDetails)): ?>
            <div class="info-card">
                <h3>🚚 Détails de la livraison</h3>
                <?php foreach ($shippingDetails as $shipping): ?>
                    <div class="shipping-line">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-item-label">Méthode</div>
                                <div class="data-item-value"><?= Html::encode($shipping['method_title']) ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-item-label">Total</div>
                                <div class="data-item-value"><?= Yii::$app->formatter->asCurrency($shipping['total'], 'EUR') ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-item-label">Taxes</div>
                                <div class="data-item-value"><?= Yii::$app->formatter->asCurrency($shipping['total_tax'], 'EUR') ?></div>
                            </div>
                        </div>
                        <?php if (!empty($shipping['meta_data'])): ?>
                            <div style="margin-top: 1.5rem;">
                                <strong style="color: var(--primary-color); display: block; margin-bottom: 0.5rem;">Métadonnées :</strong>
                                <div class="row">
                                    <?php foreach ($shipping['meta_data'] as $index => $meta): ?>
                                        <div class="col-md-6">
                                            <div class="meta-item" style="display: flex; flex-direction: column; align-items: flex-start;">
                                                <span class="meta-key" style="font-weight: 600; color: var(--primary-color); margin-bottom: 0.2rem;"><?= Html::encode($meta->display_key) ?>:</span>
                                                <span class="meta-value" style="text-align: left;">
                                                    <?php
                                                    $value = $meta->display_value;
                                                    if (is_array($value) || is_object($value)) {
                                                        echo Html::encode(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                                                    } else {
                                                        echo Html::encode($value);
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <?php if (($index + 1) % 2 == 0): ?>
                                            <!-- Saut de ligne après chaque paire de métadonnées -->
                                            <div class="w-100"></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Métadonnées de la commande -->
        <?php if (!empty($metaDetails)): ?>
            <div class="info-card">
                <h3>📋 Métadonnées de la commande</h3>
                <?php foreach ($metaDetails as $meta): ?>
                    <div class="meta-item">
                        <span class="meta-key"><?= Html::encode($meta['key']) ?>:</span>
                        <span class="meta-value">
                            <?php
                            $value = $meta['value'];
                            if (is_object($value) || is_array($value)) {
                                // Si c'est un objet ou un tableau, on l'affiche en JSON pour le rendre lisible
                                echo Html::encode(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            } else {
                                // Sinon, on affiche la valeur brute encodée
                                echo Html::encode($value);
                            }
                            ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Remboursements -->
        <?php if (!empty($refundDetails)): ?>
            <div class="info-card">
                <h3>💸 Remboursements</h3>
                <?php foreach ($refundDetails as $refund): ?>
                    <div class="refund-item">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong><?= Html::encode($refund['reason']) ?></strong>
                                <div class="refund-date"><?= formatDate($refund['date_created']) ?></div>
                            </div>
                            <div class="refund-amount currency"><?= Yii::$app->formatter->asCurrency($refund['total'], 'EUR') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Résumé des totaux -->
        <?php if (!empty($orderDetails)): ?>
            <div class="price-total">
                <h4 style="color: var(--primary-color); margin-top: 0;">Résumé des totaux</h4>
                <div class="price-row">
                    <span>Sous-total HT</span>
                    <span class="currency"><?= Yii::$app->formatter->asCurrency($orderDetails[0]['total_ht'], 'EUR') ?></span>
                </div>
                <div class="price-row">
                    <span>Taxes</span>
                    <span class="currency"><?= Yii::$app->formatter->asCurrency($orderDetails[0]['total_tax'], 'EUR') ?></span>
                </div>
                <div class="price-row">
                    <span>Frais de livraison</span>
                    <span class="currency"><?= Yii::$app->formatter->asCurrency($orderDetails[0]['shipping_total'], 'EUR') ?></span>
                </div>
                <div class="price-row">
                    <span><strong>Total TTC</strong></span>
                    <span class="currency"><strong><?= Yii::$app->formatter->asCurrency($orderDetails[0]['total'], 'EUR') ?></strong></span>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Aucune commande trouvée</h4>
            <p>Aucune commande n'a été trouvée avec la référence <strong><?= Html::encode($ref) ?></strong>.</p>
        </div>
    <?php endif; ?>
</div>

<?php

/**
 * Fonctions utilitaires
 */

// Récupérer les détails du client
function getCustomerDetails($client, $customerId)
{
    try {
        $customer = $client->get('customers/' . $customerId);
        return [[
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'date_created' => $customer->date_created,
            'date_modified' => $customer->date_modified,
        ]];
    } catch (HttpClientException $e) {
        \Yii::$app->session->setFlash('error', "Erreur lors de la récupération des informations du client : " . $e->getMessage());
        return [];
    }
}

// Préparer les détails de la commande
function prepareOrderDetails($order)
{
    // S'assurer que $order est un objet stdClass
    if (is_array($order)) {
        $order = (object) $order;
    }

    return [[
        'id' => $order->id,
        'status' => $order->status,
        'date_created' => $order->date_created,
        'date_modified' => $order->date_modified,
        'date_paid' => $order->date_paid,
        'total' => $order->total,
        'total_tax' => $order->total_tax,
        'total_ht' => $order->total - $order->total_tax - $order->shipping_total, // Sous-total HT
        'payment_method_title' => $order->payment_method_title,
        'number' => $order->number,
        'order_key' => $order->order_key,
        'customer_ip_address' => $order->customer_ip_address,
        'shipping_total' => $order->shipping_total,
    ]];
}

// Préparer les informations de facturation et livraison
function prepareBillingShippingDetails($order)
{
    // S'assurer que $order est un objet stdClass
    if (is_array($order)) {
        $order = (object) $order;
    }

    return [
        [
            'type' => 'Facturation',
            'name' => $order->billing->first_name . ' ' . $order->billing->last_name,
            'address' => $order->billing->address_1 . ', ' . $order->billing->postcode . ' ' . $order->billing->city . ', ' . $order->billing->country,
            'phone' => $order->billing->phone,
            'email' => $order->billing->email,
        ],
        [
            'type' => 'Livraison',
            'name' => $order->shipping->first_name . ' ' . $order->shipping->last_name,
            'address' => $order->shipping->address_1 . ', ' . $order->shipping->postcode . ' ' . $order->shipping->city . ', ' . $order->shipping->country,
            'phone' => $order->shipping->phone ?? '',
            'email' => '',
        ],
    ];
}

// Préparer les détails des produits
function prepareProductDetails($order)
{
    // S'assurer que $order est un objet stdClass
    if (is_array($order)) {
        $order = (object) $order;
    }

    $productDetails = [];
    foreach ($order->line_items as $item) {
        $productDetails[] = [
            'product_id' => $item->product_id,
            'sku' => $item->sku,
            'name' => $item->name,
            'variant_id' => $item->variation_id ?? null,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'total' => $item->total,
            'total_tax' => $item->total_tax,
            'total_ttc' => $item->total + $item->total_tax,
            'image' => $item->image,
        ];
    }
    return $productDetails;
}

// Préparer les détails des taxes
function prepareTaxDetails($order)
{
    // S'assurer que $order est un objet stdClass
    if (is_array($order)) {
        $order = (object) $order;
    }

    $taxDetails = [];
    foreach ($order->tax_lines as $tax) {
        $taxDetails[] = [
            'id' => $tax->id,
            'label' => $tax->label,
            'rate_percent' => $tax->rate_percent,
            'tax_total' => $tax->tax_total,
        ];
    }
    return $taxDetails;
}

// Préparer les détails des frais de livraison
function prepareShippingDetails($order)
{
    // S'assurer que $order est un objet stdClass
    if (is_array($order)) {
        $order = (object) $order;
    }

    $shippingDetails = [];
    foreach ($order->shipping_lines as $shipping) {
        $shippingDetails[] = [
            'id' => $shipping->id,
            'method_title' => $shipping->method_title,
            'total' => $shipping->total,
            'total_tax' => $shipping->total_tax,
            'meta_data' => $shipping->meta_data,
        ];
    }
    return $shippingDetails;
}

// Préparer les métadonnées de la commande
function prepareMetaDetails($order)
{
    // S'assurer que $order est un objet stdClass
    if (is_array($order)) {
        $order = (object) $order;
    }

    $metaDetails = [];
    foreach ($order->meta_data as $meta) {
        $metaDetails[] = [
            'id' => $meta->id,
            'key' => $meta->key,
            'value' => $meta->value,
        ];
    }
    return $metaDetails;
}

// Préparer les détails des remboursements
function prepareRefundDetails($order)
{
    // S'assurer que $order est un objet stdClass
    if (is_array($order)) {
        $order = (object) $order;
    }

    $refundDetails = [];
    foreach ($order->refunds as $refund) {
        $refundDetails[] = [
            'id' => $refund->id,
            'reason' => $refund->reason,
            'total' => $refund->total,
            'date_created' => $refund->date_created,
        ];
    }
    return $refundDetails;
}

// Gérer les exceptions HTTP
function handleHttpClientException($e)
{
    $response = $e->getResponse();
    $errorCode = $response instanceof \Automattic\WooCommerce\HttpClient\Response ? $response->getCode() : null;
    $errorMessage = $e->getMessage();

    switch ($errorCode) {
        case 404:
            $message = "Commande introuvable (Erreur 404).";
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