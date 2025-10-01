<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;

require("./../vendor/prestashop/prestashop-webservice-lib/PSWebServiceLibrary.php");

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Commande ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
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
        margin-bottom: 0;
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
        margin-bottom: 2rem;
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

if (strpos($url, 'localhost') !== false) {
    $url = "http://" . $url;
} else {
    $headers = @get_headers("http://" . $url);
    if ($headers && strpos($headers[0], '200') !== false) {
        $url = "http://" . $url;
    } else {
        $url = "https://" . $url;
    }
}

$api = Html::encode($model->api_key);
$ref = Html::encode($ref);
$db_id = $model->id;

// Affichage de l'URL de requête et des détails
echo '<div class="container-fluid mb-4">';
echo '<div class="api-info">';
echo '<h3>🔗 Informations API</h3>';
echo '<p><strong>URL de la requête :</strong></p>';
echo '<div class="api-url">';
echo '<a href=' . $url . '/api/orders/' . $ref . '?ws_key=' . $api . ' target=_blank>' . $url . '/api/orders/' . $ref . '?ws_key=' . $api . '</a>';
echo '</div>';
echo '<div class="detail-view">';
echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'url:url',
        'api_key',
    ],
]);
echo '</div>';
echo '</div>';
echo '</div>';

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

try {
    $webService = new PrestaShopWebservice($url, $api, true);

    $languageOpt = [
        'resource' => 'languages',
        'filter[iso_code]' => 'fr',
        'display' => 'full',
    ];
    $languageXml = $webService->get($languageOpt);
    $languages = $languageXml->languages->children();
    $languageId = null;
    foreach ($languages as $language) {
        $languageId = (int)$language->id;
        break;
    }

    if (!$languageId) {
        throw new PrestaShopWebserviceException('Langue française introuvable dans la boutique.');
    }

    $xmlOrders = $webService->get(['resource' => 'orders', 'id' => $ref]);
    $xmlAddresses = $webService->get(['resource' => 'addresses']);
    $xmlProducts = $webService->get(['resource' => 'products']);

    $orders = [];
    $customers = [];
    $addresses = [];
    $products = [];

    foreach ($xmlOrders->order as $order) {
        $state = $order->current_state;
        $xmlState = $webService->get(['resource' => 'order_states', 'id' => $state]);
        $stateName = (string) $xmlState->order_state->name->language;

        $xmlPayments = $webService->get([
            'resource' => 'order_payments',
            'filter[order_reference]' => (string) $order->reference,
            'display' => 'full'
        ]);

        $transaction_id = $xmlPayments->order_payments->order_payment->transaction_id;

        $orders[] = [
            'id' => (string) $order->id,
            'current_state' => '(' . (string) $order->current_state . ') ' . (string) $stateName,
            'customer_id' => (string) $order->id_customer,
            'total_paid' => (string) $order->total_paid,
            'total_shipping_tax_incl' => (string) $order->total_shipping_tax_incl,
            'id_address_invoice' => (string) $order->id_address_invoice,
            'id_address_delivery' => (string) $order->id_address_delivery,
            'payment' => (string) $order->payment,
            'reference' => (string) $order->reference,
            'transaction_id' => (string) $transaction_id,
            'date_add' => (string) $order->date_add,
            'date_upd' => (string) $order->date_upd,
        ];

        $customerId = (string) $order->id_customer;
        $xmlCustomers = $webService->get(['resource' => 'customers', 'id' => $customerId]);
        foreach ($xmlCustomers->customer as $customer) {
            $customers[] = [
                'customer_id' => (string) $customer->id,
                'first_name' => (string) $customer->firstname,
                'last_name' => (string) $customer->lastname,
                'email' => (string) $customer->email,
                'date_add' => (string) $customer->date_add,
                'date_upd' => (string) $customer->date_upd,
            ];
        }

        $addressInvoiceId = (string) $order->id_address_invoice;
        $xmlAddressInvoice = $webService->get(['resource' => 'addresses', 'id' => $addressInvoiceId]);

        $addresses = [];
        foreach ($xmlAddressInvoice->address as $address) {
            $countryId = (string) $address->id_country;
            $xmlCountry = $webService->get(['resource' => 'countries', 'id' => $countryId]);
            $countryName = (string) $xmlCountry->country->iso_code;

            $fullAddress = (string) $address->address1;
            if (!empty($address->address2)) {
                $fullAddress .= ', ' . (string) $address->address2;
            }
            $fullAddress .= ', ' . (string) $address->postcode . ' ' . (string) $address->city . ', ' . $countryName;

            $addresses[] = [
                'address_type' => 'Facturation',
                'id' => $addressInvoiceId,
                'alias' => $address->alias,
                'company' => $address->company,
                'first_name' => (string) $address->firstname,
                'last_name' => (string) $address->lastname,
                'address' => (string) $fullAddress,
                'phone' => (string) $address->phone,
                'vat_number' => (string) $address->vat_number,
            ];
        }

        $addressDeliveryId = (string) $order->id_address_delivery;
        $xmlAddressDelivery = $webService->get(['resource' => 'addresses', 'id' => $addressDeliveryId]);

        foreach ($xmlAddressDelivery->address as $address) {
            $countryId = (string) $address->id_country;
            $xmlCountry = $webService->get(['resource' => 'countries', 'id' => $countryId]);
            $countryName = (string) $xmlCountry->country->iso_code;

            $fullAddress = (string) $address->address1;
            if (!empty($address->address2)) {
                $fullAddress .= ', ' . (string) $address->address2;
            }
            $fullAddress .= ', ' . (string) $address->postcode . ' ' . (string) $address->city . ', ' . $countryName;
            $addresses[] = [
                'address_type' => 'Livraison',
                'id' => $addressDeliveryId,
                'alias' => $address->alias,
                'company' => $address->company,
                'first_name' => (string) $address->firstname,
                'last_name' => (string) $address->lastname,
                'address' => (string) $fullAddress,
                'phone' => (string) $address->phone,
                'vat_number' => (string) $address->vat_number,
            ];
        }

        $addressDataProvider = new ArrayDataProvider([
            'allModels' => $addresses,
        ]);

        if (isset($order->associations->order_rows->order_row)) {
            foreach ($order->associations->order_rows->order_row as $product) {
                $orderRowId = (string) $product->id;
                $productData = [
                    'order_row' => $orderRowId,
                    'id_product_attribute' => $product->product_attribute_id,
                    'product_reference' => (string) $product->product_reference,
                    'product_name' => (string) $product->product_name,
                    'quantity' => (string) $product->product_quantity,
                    'total' => (string) $product->unit_price_tax_incl,
                ];

                try {
                    $orderDetailsOpt = [
                        'resource' => 'order_details',
                        'filter[id]' => $orderRowId,
                        'display' => 'full',
                    ];

                    $orderDetailsXml = $webService->get($orderDetailsOpt);
                    $orderDetails = $orderDetailsXml->order_details->children();

                    foreach ($orderDetails as $detail) {
                        $productData['total_price_tax_excl'] = (float) $detail->total_price_tax_excl;
                        $productData['unit_price_tax_excl'] = (float) $detail->unit_price_tax_excl;
                        $productData['total_price_tax_incl'] = (float) $detail->total_price_tax_incl;
                        $productData['unit_price_tax_incl'] = (float) $detail->unit_price_tax_incl;
                        $productData['ecotax'] = (float) $detail->ecotax;

                        if (isset($detail->associations->taxes->tax)) {
                            $taxElement = $detail->associations->taxes->tax;

                            if (isset($taxElement->attributes()->{'xlink:href'})) {
                                $taxHref = (string) $taxElement->attributes()->{'xlink:href'};
                                $taxId = basename($taxHref);
                            } elseif (isset($taxElement->id)) {
                                $taxId = (int) $taxElement->id;
                            } else {
                                $taxId = null;
                            }

                            if ($taxId) {
                                $taxOpt = [
                                    'resource' => 'taxes',
                                    'id' => $taxId,
                                ];

                                try {
                                    $tax = $webService->get($taxOpt);
                                    $taxName = (string) $tax->tax->name->language;
                                    $taxRate = (float) $tax->tax->rate;

                                    $productData['tax'] = $taxRate . "% (" . $taxName . ")";
                                } catch (Exception $e) {
                                    $productData['tax'] = 'Erreur lors de la récupération des taxes';
                                }
                            } else {
                                $productData['tax'] = 'Pas de taxe trouvé';
                            }
                        } else {
                            $productData['tax'] = 'Pas de taxe associé';
                        }
                    }
                } catch (Exception $e) {
                    $productData['total_price_tax_excl'] = '';
                    $productData['unit_price_tax_excl'] = '';
                    $productData['total_price_tax_incl'] = '';
                    $productData['unit_price_tax_incl'] = '';
                    $productData['tax'] = 'Erreur lors de la récupération des taxes';
                }

                $products[] = $productData;
            }
        }
    }

    $orderDataProvider = new ArrayDataProvider([
        'allModels' => $orders,
        'pagination' => ['pageSize' => 10],
    ]);

    $customerDataProvider = new ArrayDataProvider([
        'allModels' => $customers,
        'pagination' => ['pageSize' => 10],
    ]);

    $productDataProvider = new ArrayDataProvider([
        'allModels' => $products,
        'pagination' => ['pageSize' => 1000],
    ]);

} catch (PrestaShopWebserviceException $e) {
    echo '<div class="alert alert-danger">';
    echo '<strong>Erreur détectée :</strong> ' . $e->getMessage();
    echo '</div>';
    return;
}

?>

<div class="container-fluid">
    <!-- En-tête de la commande -->
    <div class="order-header">
        <h1>📦 Détails de la commande</h1>
        <div class="order-reference">Référence : <strong><?= !empty($orders) ? $orders[0]['reference'] : $ref ?></strong></div>
        
        <div class="quick-actions">
            <?php if (!empty($orders)): ?>
                <a href="<?= $url ?>/api/orders/<?= $orders[0]['id'] ?>?ws_key=<?= $api ?>" target="_blank" class="action-btn">
                    🔗 API PrestaShop
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="action-btn">
                🖨️ Imprimer
            </button>
            <button onclick="alert('Fonctionnalité PDF : Veuillez utiliser la fonction Imprimer et sélectionner \'Enregistrer en PDF\' comme destination.')" class="action-btn">
                📄 Télécharger PDF
            </button>
        </div>
    </div>

    <!-- Statistiques principales -->
    <?php if (!empty($orders)): ?>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Montant Total</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($orders[0]['total_paid'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Frais de livraison</div>
                <div class="stat-value"><?= Yii::$app->formatter->asCurrency($orders[0]['total_shipping_tax_incl'], 'EUR') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Paiement</div>
                <div class="stat-value" style="font-size: 1.3rem;"><?= $orders[0]['payment'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Date</div>
                <div class="stat-value" style="font-size: 1.1rem;"><?= Yii::$app->formatter->asDatetime($orders[0]['date_add'], 'php:d/m/Y') ?></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Informations de la commande -->
    <div class="info-card">
        <h3>📋 Informations de la commande</h3>
        
        <div class="data-grid">
            <?php if (!empty($orders)): ?>
                <div class="data-item">
                    <div class="data-item-label">ID Commande</div>
                    <div class="data-item-value">#<?= $orders[0]['id'] ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Statut</div>
                    <div class="data-item-value">
                        <span class="status-badge"><?= $orders[0]['current_state'] ?></span>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Client</div>
                    <div class="data-item-value">#<?= $orders[0]['customer_id'] ?></div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Transaction ID</div>
                    <div class="data-item-value"><?= $orders[0]['transaction_id'] ?: 'N/A' ?></div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($orders)): ?>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="info-row">
                        <span class="info-label">📅 Création</span>
                        <span class="info-value"><?= Yii::$app->formatter->asDatetime($orders[0]['date_add'], 'php:d/m/Y à H:i') ?></span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="info-row">
                        <span class="info-label">🔄 Dernière mise à jour</span>
                        <span class="info-value"><?= Yii::$app->formatter->asDatetime($orders[0]['date_upd'], 'php:d/m/Y à H:i') ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Informations client -->
    <?php if (!empty($customers)): ?>
        <div class="info-card">
            <h3>👤 Informations du client</h3>
            <?php foreach ($customers as $customer): ?>
                <div class="data-grid">
                    <div class="data-item">
                        <div class="data-item-label">ID Client</div>
                        <div class="data-item-value">
                            <a href="<?= $url ?>/api/customers/<?= $customer['customer_id'] ?>?ws_key=<?= $api ?>" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                                #<?= $customer['customer_id'] ?>
                            </a>
                        </div>
                    </div>
                    <div class="data-item">
                        <div class="data-item-label">Nom complet</div>
                        <div class="data-item-value"><?= $customer['first_name'] ?> <?= $customer['last_name'] ?></div>
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
                        <div class="data-item-label">Inscrit depuis</div>
                        <div class="data-item-value"><?= Yii::$app->formatter->asDatetime($customer['date_add'], 'php:d/m/Y') ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Adresses -->
    <?php if (!empty($addresses)): ?>
        <div class="info-card">
            <h3>📍 Adresses</h3>
            <div class="row">
                <?php foreach ($addresses as $address): ?>
                    <div class="col-md-6 mb-3">
                        <div class="address-card">
                            <div class="address-type">
                                <?= $address['address_type'] === 'Facturation' ? '🧾' : '📦' ?> 
                                <?= $address['address_type'] ?>
                            </div>
                            <?php if ($address['company']): ?>
                                <p style="margin: 0.5rem 0; font-weight: 600; color: var(--primary-color);">
                                    <?= $address['company'] ?>
                                </p>
                            <?php endif; ?>
                            <p style="margin: 0.3rem 0;">
                                <strong><?= $address['first_name'] ?> <?= $address['last_name'] ?></strong>
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
                            <?php if ($address['vat_number']): ?>
                                <p style="margin: 0.3rem 0; font-size: 0.9rem;">
                                    <span style="color: var(--primary-color);">💼</span> 
                                    TVA: <?= $address['vat_number'] ?>
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
                        <th>Référence</th>
                        <th>Produit</th>
                        <th style="text-align: center;">Qté</th>
                        <th style="text-align: right;">P.U HT</th>
                        <th>TVA</th>
                        <th style="text-align: right;">P.U TTC</th>
                        <th style="text-align: right;">Total HT</th>
                        <th style="text-align: right;">Total TTC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <a href="<?= Url::to(['productresults', 'id' => $db_id, 'ref' => $product['product_reference'], 'type' => $product['id_product_attribute'] == 0 ? 'simple' : 'variation', 'variation_type' => $product['id_product_attribute'] == 0 ? '' : 'child']) ?>" target="_blank">
                                    <?= $product['product_reference'] ?>
                                </a>
                            </td>
                            <td class="product-name">
                                <?= $product['product_name'] ?>
                                <span class="product-type"><?= $product['id_product_attribute'] == 0 ? 'Simple' : 'Déclinaison' ?></span>
                            </td>
                            <td style="text-align: center;"><strong><?= $product['quantity'] ?></strong></td>
                            <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($product['unit_price_tax_excl'], 'EUR') ?></td>
                            <td><?= $product['tax'] ?></td>
                            <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($product['unit_price_tax_incl'], 'EUR') ?></td>
                            <td style="text-align: right;" class="currency"><?= Yii::$app->formatter->asCurrency($product['total_price_tax_excl'], 'EUR') ?></td>
                            <td style="text-align: right;"><strong class="currency"><?= Yii::$app->formatter->asCurrency($product['total_price_tax_incl'], 'EUR') ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>