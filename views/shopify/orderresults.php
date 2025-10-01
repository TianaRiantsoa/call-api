<?php

use PhpParser\Node\Stmt\TryCatch;
use Shopify\ApiVersion;
use Shopify\Exception\ShopifyException;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Shopify $model */

$this->title = 'Commande ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'Shopify', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de commande', 'url' => ['orders', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

// Styles personnalisés
$this->registerCss("
    :root {
        --primary-color: #F1ac16;
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
        background: linear-gradient(135deg, var(--primary-color) 0%, #d99912 100%);
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
        background: linear-gradient(90deg, var(--primary-color) 0%, #d99912 100%);
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
        background: linear-gradient(135deg, var(--primary-color) 0%, #d99912 100%);
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
       /* margin-bottom: 2rem;*/
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
    
    .status-badge-shopify {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .status-open { background-color: rgba(0, 123, 255, 0.2); color: #007bff; }
    .status-cancelled { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .status-closed { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .status-archived { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
    
    .status-payment-paid { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
    .status-payment-pending { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .status-payment-refunded { background-color: rgba(108, 117, 125, 0.2); color: #6c757d; }
    
    .status-fulfillment-fulfilled { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
    .status-fulfillment-pending { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .status-fulfillment-shipped { background-color: rgba(0, 123, 255, 0.2); color: #007bff; }
    
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

$url = Html::encode($model->url);
$api = Html::encode($model->api_key);
$pwd = Html::encode($model->password);
$sct = Html::encode($model->secret_key);
$type = Html::encode($type);
$ref = Html::encode($ref);

require('function.php');

try {
    $init = InitShopify($url, $api, $pwd, $sct);

    $n = count(str_split($ref));

    if (isset($type) && $type == 'court') {
        $q = "name:$ref";
    } elseif (isset($type) && $type == 'long') {
        $q = "id:$ref";
    } else {
        echo 'Erreur sur ne numéro saisi';
    }

    echo '<div class="container-fluid mb-4">';
    echo '<div class="api-info">';
    echo '<h3>🔗 Informations API</h3>';
    // echo '<p><strong>URL de la requête :</strong></p>';
    // echo '<div class="api-url">';
    // echo '<a href="https://' . $api . ':' . $pwd . '@' . $url . '/admin/api/' . ApiVersion::LATEST . '/orders/' . urlencode($q) . '.json?query=" target="_blank">' . 'https://' . $api . ':' . $pwd . '@' . $url . '/admin/api/' . ApiVersion::LATEST . '/orders/' . urlencode($q) . '.json</a>';
    // echo '</div>';
    echo '<div class="detail-view">';
    echo yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'url',
            'api_key',
            'password',
            'secret_key',
        ],
    ]);
    echo '</div>';
    echo '</div>';
    echo '</div>';

    $query = <<<QUERY
        query {
            orders(query: "$q", first: 10) {
                edges {
                node {
                id
                name
                createdAt
                updatedAt
                cancelledAt
                closedAt
                displayFinancialStatus
                displayFulfillmentStatus
                fulfillments {
                    location {
                        id
                    }
                }
                paymentGatewayNames
                subtotalPrice
                taxesIncluded
                totalDiscounts
                totalTax
                shippingAddress {
                    firstName
                    lastName
                    phone
                    address1
                    address2
                    zip
                    city
                    country
                    countryCode
                    countryCodeV2
                }
                shippingLine {
                    title
                    price
                    taxLines {
                    price
                    rate
                    }
                }
                billingAddress {
                    firstName
                    lastName
                    phone
                    address1
                    address2
                    zip
                    city
                    country
                    countryCode
                    countryCodeV2
                }
                customer {
                    id
                    firstName
                    lastName
                    email
                    phone
                    createdAt
                    updatedAt            
                }
                lineItems(first: 250) {
                    edges {
                        node {
                            sku
                            name
                            quantity
                            originalUnitPrice
                            originalTotal
                            discountedUnitPrice
                            discountedTotal
                            taxable
                            product {
                            id
                            }
                            variant {
                            id
                            }                
                        }
                    }
                }
                }
            }
            }
        }
QUERY;

    $response = $init->query(["query" => $query]);

    $contents = $response->getBody()->getContents();

    $data = json_decode($contents, true);

    $orders = $data['data']['orders']['edges'];
    $gridDataOrders = [];
    $gridDataCustomers = [];
    $gridDataAddresses = [];
    $gridDataLineItems = [];

    foreach ($orders as $orderEdge) {
        $order = $orderEdge['node'];

        $statut = 'OPEN';

        if (!empty($order['cancelledAt'])) {
            $statut = 'CANCELLED';
        } elseif (!empty($order['closedAt'])) {
            $statut = 'CLOSED';
        } elseif ($order['displayFinancialStatus'] === 'PAID' && $order['displayFulfillmentStatus'] === 'FULFILLED') {
            $statut = 'ARCHIVED';
        }

        // Initialisation de la commande
        $gridOrder = [
            'id' => getId($order['id']),
            'name' => $order['name'],
            'createdAt' => formatDateTime($order['createdAt']),
            'updatedAt' => formatDateTime($order['updatedAt']),
            'payment_method' => !empty($order['paymentGatewayNames'][0]) ? $order['paymentGatewayNames'][0] : '',
            'status' => $statut,
            'status_payment' => $order['displayFinancialStatus'],
            'status_fulfillment' => $order['displayFulfillmentStatus'],
            'subtotal' => $order['subtotalPrice'],
            'totalDiscounts' => $order['totalDiscounts'],
            'totalTax' => $order['totalTax'],
            'location' => '', // Par défaut vide
            'transporter' => !empty($order['shippingLine']['title']) ? $order['shippingLine']['title'] : 'Non spécifié',
        ];

        // Récupération du location ID si fulfillment existe
        if (!empty($order['fulfillments']) && !empty($order['fulfillments'][0]['location']['id'])) {
            $locationId = $order['fulfillments'][0]['location']['id'];

            $queryLocation = <<<QUERY
                            query {
                                location(id: "$locationId") {
                                    id
                                    name
                                }
                            }
                            QUERY;

            $res = $init->query(["query" => $queryLocation]);
            $get = $res->getBody()->getContents();
            $rep = json_decode($get, true);

            if (!empty($rep['data']['location'])) {
                $locName = htmlspecialchars($rep['data']['location']['name']);
                $gridOrder['location'] = getId($locationId) . '<br>' . $locName; // ID + nom
            }
        }

        // Ajouter une seule entrée à $gridDataOrders
        $gridDataOrders[] = $gridOrder;


        // Clients
        if (!empty($order['customer'])) {
            $gridDataCustomers[] = [
                'id' => getId($order['customer']['id']),
                'firstName' => $order['customer']['firstName'],
                'lastName' => $order['customer']['lastName'],
                'email' => $order['customer']['email'],
                'phone' => $order['customer']['phone'],
                'createdAt' => formatDateTime($order['customer']['createdAt']),
                'updatedAt' => formatDateTime($order['customer']['updatedAt']),
            ];
        }

        // Adresses
        foreach (['billingAddress' => 'Facturation', 'shippingAddress' => 'Livraison'] as $addressKey => $type) {
            if (!empty($order[$addressKey])) {
                $address = $order[$addressKey];

                $fullAddress = (string) $address['address1'];
                if (!empty($address['address2'])) {
                    $fullAddress .= ', ' . (string) $address['address2'];
                }
                $fullAddress .= ', ' . (string) $address['zip'] . ' ' . (string) $address['city'] . ', ' . (string) $address['country'] . ', ' . $address['countryCode'];

                $gridDataAddresses[] = [
                    'type' => $type,
                    'firstName' => $address['firstName'],
                    'lastName' => $address['lastName'],
                    'phone' => $address['phone'],
                    'address' => $fullAddress,
                ];
            }
        }

        // Produits commandés
        if (!empty($order['lineItems']['edges'])) {
            foreach ($order['lineItems']['edges'] as $lineItemEdge) {
                $lineItem = $lineItemEdge['node'];
                $gridDataLineItems[] = [
                    'product_id' => getId($lineItem['product']['id']),
                    'variant_id' => getId($lineItem['variant']['id']),
                    'sku' => $lineItem['sku'],
                    'name' => $lineItem['name'],
                    'quantity' => $lineItem['quantity'],
                    'originalUnitPrice' => $lineItem['originalUnitPrice'],
                    'discountedUnitPrice' => $lineItem['discountedUnitPrice'],
                    'remise' => $lineItem['originalUnitPrice'] > 0 ? number_format((($lineItem['originalUnitPrice'] - $lineItem['discountedUnitPrice']) / $lineItem['originalUnitPrice']) * 100, 2, '.', '') . ' %' : '0 %',

                    'originalTotal' => $lineItem['originalTotal'],
                    'discountedTotal' => $lineItem['discountedTotal'],
                    'taxable' => $lineItem['taxable'] ? 'Oui' : 'Non',
                ];
            }
        }
    }


    // Data Providers
    $orderProvider = new ArrayDataProvider(['allModels' => $gridDataOrders]);
    $customerProvider = new ArrayDataProvider(['allModels' => $gridDataCustomers]);
    $addressProvider = new ArrayDataProvider(['allModels' => $gridDataAddresses]);
    $lineItemProvider = new ArrayDataProvider(['allModels' => $gridDataLineItems]);
} catch (ShopifyException $e) {
    echo '<div class="alert alert-danger">';
    echo '<strong>Erreur détectée :</strong> ' . $e->getMessage();
    echo '</div>';
    return;
}

?>

<div class="container-fluid">
    <!-- En-tête de la commande -->
    <div class="order-header">
        <h1>📦 Détails de la commande Shopify</h1>
        <div class="order-reference">Référence : <strong><?= !empty($gridDataOrders) ? $gridDataOrders[0]['name'] : $ref ?></strong></div>
<!-- 
        <div class="quick-actions">
            <?php if (!empty($gridDataOrders)): ?>
                <a href="https://<?= $api ?>:<?= $pwd ?>@<?= $url ?>/admin/api/<?= ApiVersion::LATEST ?>/orders/<?= $gridDataOrders[0]['id'] ?>.json" target="_blank" class="action-btn">
                    🔗 API Shopify
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="action-btn">
                🖨️ Imprimer
            </button>
            <button onclick="alert('Fonctionnalité PDF : Veuillez utiliser la fonction Imprimer et sélectionner \'Enregistrer en PDF\' comme destination.')" class="action-btn">
                📄 Télécharger PDF
            </button>
        </div> -->
    </div>

    <!-- Statistiques principales -->
    <?php if (!empty($gridDataOrders)): ?>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Montant Total</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($gridDataOrders[0]['subtotal'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Taxes</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($gridDataOrders[0]['totalTax'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Réductions</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($gridDataOrders[0]['totalDiscounts'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Paiement</div>
                <div class="stat-value" style="font-size: 1.3rem;"><?= $gridDataOrders[0]['payment_method'] ?></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Informations de la commande -->
    <div class="info-card">
        <h3>📋 Informations de la commande</h3>

        <div class="data-grid">
            <?php if (!empty($gridDataOrders)): ?>
                <div class="data-item">
                    <div class="data-item-label">ID Commande</div>
                    <div class="data-item-value">#<?= $gridDataOrders[0]['id'] ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Statut</div>
                    <div class="data-item-value">
                        <span class="status-badge status-<?= strtolower($gridDataOrders[0]['status']) ?>"><?= $gridDataOrders[0]['status'] ?></span>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Statut Paiement</div>
                    <div class="data-item-value">
                        <span class="status-badge status-payment-<?= strtolower($gridDataOrders[0]['status_payment']) ?>"><?= $gridDataOrders[0]['status_payment'] ?></span>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Statut Expédition</div>
                    <div class="data-item-value">
                        <span class="status-badge status-fulfillment-<?= strtolower($gridDataOrders[0]['status_fulfillment']) ?>"><?= $gridDataOrders[0]['status_fulfillment'] ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($gridDataOrders)): ?>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="info-row">
                        <span class="info-label">📅 Création</span>
                        <span class="info-value"><?= $gridDataOrders[0]['createdAt'] ?></span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="info-row">
                        <span class="info-label">🔄 Dernière mise à jour</span>
                        <span class="info-value"><?= $gridDataOrders[0]['updatedAt'] ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Informations client -->
    <?php if (!empty($gridDataCustomers)): ?>
        <div class="info-card">
            <h3>👤 Informations du client</h3>
            <?php foreach ($gridDataCustomers as $customer): ?>
                <div class="data-grid">
                    <div class="data-item">
                        <div class="data-item-label">ID Client</div>
                        <div class="data-item-value">
                            <a href="https://<?= $api ?>:<?= $pwd ?>@<?= $url ?>/admin/api/<?= ApiVersion::LATEST ?>/customers/<?= $customer['id'] ?>.json" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                #<?= $customer['id'] ?>
                            </a>
                        </div>
                    </div>
                    <div class="data-item">
                        <div class="data-item-label">Nom complet</div>
                        <div class="data-item-value"><?= $customer['firstName'] ?> <?= $customer['lastName'] ?></div>
                    </div>
                    <div class="data-item">
                        <div class="data-item-label">Email</div>
                        <div class="data-item-value">
                            <a href="mailto:<?= $customer['email'] ?>" style="color: var(--primary-color); text-decoration: none;">
                                <?= $customer['email'] ?>
                            </a>
                        </div>
                    </div>
                    <div class="data-item">
                        <div class="data-item-label">Téléphone</div>
                        <div class="data-item-value"><?= $customer['phone'] ?: 'N/A' ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Adresses -->
    <?php if (!empty($gridDataAddresses)): ?>
        <div class="info-card">
            <h3>📍 Adresses</h3>
            <div class="row">
                <?php foreach ($gridDataAddresses as $address): ?>
                    <div class="col-md-6 mb-3">
                        <div class="address-card">
                            <div class="address-type">
                                <?= $address['type'] === 'Facturation' ? '🧾' : '📦' ?>
                                <?= $address['type'] ?>
                            </div>
                            <p style="margin: 0.3rem 0;">
                                <strong><?= $address['firstName'] ?> <?= $address['lastName'] ?></strong>
                            </p>
                            <p style="margin: 0.3rem 0; color: var(--text-color);">
                                <?= $address['address'] ?>
                            </p>
                            <?php if ($address['phone']): ?>
                                <p style="margin: 0.5rem 0 0.3rem 0;">
                                    <span style="color: var(--primary-color);">📞</span>
                                    <a href="tel:<?= $address['phone'] ?>" style="color: var(--text-color); text-decoration: none;">
                                        <?= $address['phone'] ?>
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
    <div class="table-wrapper">
        <div class="table-header">
            <h3>Produits commandés</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Produit</th>
                        <th style="text-align: center;">Qté</th>
                        <th style="text-align: right;">P.U Original</th>
                        <th style="text-align: right;">P.U Réduit</th>
                        <th style="text-align: center;">% Remise</th>
                        <th style="text-align: right;">Total Original</th>
                        <th style="text-align: right;">Total Réduit</th>
                        <th style="text-align: center;">Taxable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gridDataLineItems as $product): ?>
                        <tr>
                            <td>
                                <a href="https://<?= $api ?>:<?= $pwd ?>@<?= $url ?>/admin/api/<?= ApiVersion::LATEST ?>/variants/<?= $product['variant_id'] ?>.json" target="_blank">
                                    <?= $product['sku'] ?>
                                </a>
                            </td>
                            <td class="product-name">
                                <?= $product['name'] ?>
                            </td>
                            <td style="text-align: center;"><strong><?= $product['quantity'] ?></strong></td>
                            <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($product['originalUnitPrice'], 'EUR') ?></td>
                            <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($product['discountedUnitPrice'], 'EUR') ?></td>
                            <td style="text-align: center;"><?= $product['remise'] ?></td>
                            <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($product['originalTotal'], 'EUR') ?></td>
                            <td style="text-align: right;"><strong class="currency"><?= Yii::$app->formatter->asCurrency($product['discountedTotal'], 'EUR') ?></strong></td>
                            <td style="text-align: center;"><?= $product['taxable'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>