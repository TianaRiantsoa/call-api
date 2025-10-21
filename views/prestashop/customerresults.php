<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

require("./../vendor/prestashop/prestashop-webservice-lib/PSWebServiceLibrary.php");

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Client | ' . Html::encode($ref);
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de client', 'url' => ['customers', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

// Styles personnalisés (repris du design orders avec adaptations pour customers)
$this->registerCss("
    :root {
        --primary-color: #F1ac16;
        --secondary-color: #3498db;
        --bg-color: #f6f4f0;
        --text-color: #5c5c5c;
        --card-shadow: 0 2px 12px rgba(241, 172, 22, 0.08);
        --card-shadow-hover: 0 8px 24px rgba(241, 172, 22, 0.15);
    }
    
    body {
        background-color: var(--bg-color);
        color: var(--text-color);
    }
    
    .customer-header {
        background: linear-gradient(135deg, #f1ac16 0%, #5c5c5c 100%);
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow-hover);
        position: relative;
        overflow: hidden;
    }
    
    .customer-header::before {
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
    
    .customer-header h1 {
        color: white;
        margin: 0;
        font-weight: 700;
        font-size: 2rem;
        position: relative;
        z-index: 1;
    }
    
    .customer-type-badge {
        display: inline-block;
        padding: 0.5rem 1.2rem;
        background: rgba(255,255,255,0.2);
        border: 2px solid white;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
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
    
    .address-card {
        background: #fef9f0;
        padding: 1.2rem;
        border-radius: 8px;
        border-left: 3px solid var(--primary-color);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .address-card:hover {
        transform: translateX(5px);
        box-shadow: var(--card-shadow);
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
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        animation: pulse-dot 2s ease-in-out infinite;
    }
    
    @keyframes pulse-dot {
        0%, 100% { box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2); }
        50% { box-shadow: 0 0 0 8px rgba(52, 152, 219, 0.1); }
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
    
    .status-badge.active::before {
        content: '●';
        color: #27ae60;
        font-size: 1.2rem;
        animation: blink 2s ease-in-out infinite;
    }
    
    .status-badge.inactive::before {
        content: '●';
        color: #e74c3c;
        font-size: 1.2rem;
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
        border: 2px solid var(--secondary-color);
        border-radius: 8px;
        color: var(--secondary-color);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        background: var(--secondary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
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
    
    .group-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #fef9f0 0%, white 100%);
        border: 2px solid var(--primary-color);
        border-radius: 20px;
        margin: 0.3rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .group-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.2);
    }
    
    .group-badge.default {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .group-badge.default::before {
        content: '⭐ ';
    }
    
    .group-id {
        background: rgba(0,0,0,0.1);
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        font-size: 0.85rem;
        margin-left: 0.5rem;
    }
    
    .group-badge.default .group-id {
        background: rgba(255,255,255,0.3);
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
    
    .order-status {
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-color);
        opacity: 0.6;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .currency {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }
        
        .customer-header h1 {
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
            this.style.boxShadow = '0 4px 12px rgba(52, 152, 219, 0.15)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
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
echo '<a href=' . $url . '/api/customers/?filter[email]=' . $ref . '&ws_key=' . $api . ' target=_blank>' . $url . '/api/customers/?filter[email]=' . $ref . '&ws_key=' . $api . '</a>';
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

    // Récupérer l'ID de la langue française
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

    // Récupérer les clients par email
    $opt = [
        'resource' => 'customers',
        'filter[email]' => $ref,
        'display' => 'full',
    ];

    $xml = $webService->get($opt);
    $customers = $xml->customers->children();

    $customerList = [];
    $allOrders = [];
    $allAddresses = [];
    $allGroups = [];

    if (count($customers) > 0) {
        foreach ($customers as $customer) {
            $customerId = (int)$customer->id;
            
            $customerData = [
                'id' => $customerId,
                'id_default_group' => (string)$customer->id_default_group,
                'company' => (string)$customer->company,
                'firstname' => (string)$customer->firstname,
                'lastname' => (string)$customer->lastname,
                'email' => (string)$customer->email,
                'siret' => (string)$customer->siret,
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

            // Récupérer les groupes du client
            try {
                $defaultGroupId = (string)$customer->id_default_group;
                
                // Récupérer le groupe par défaut
                if ($defaultGroupId) {
                    $defaultGroupXml = $webService->get(['resource' => 'groups', 'id' => $defaultGroupId]);
                    $defaultGroupName = (string)$defaultGroupXml->group->name->language;
                    
                    $allGroups[] = [
                        'id' => $defaultGroupId,
                        'name' => $defaultGroupName,
                        'is_default' => true,
                    ];
                }
                
                // Récupérer tous les groupes associés
                if (isset($customer->associations->groups->group)) {
                    foreach ($customer->associations->groups->group as $group) {
                        $groupId = (string)$group->id;
                        
                        // Éviter de dupliquer le groupe par défaut
                        if ($groupId != $defaultGroupId) {
                            $groupXml = $webService->get(['resource' => 'groups', 'id' => $groupId]);
                            $groupName = (string)$groupXml->group->name->language;
                            
                            $allGroups[] = [
                                'id' => $groupId,
                                'name' => $groupName,
                                'is_default' => false,
                            ];
                        }
                    }
                }
            } catch (Exception $e) {
                // Continuer même si la récupération des groupes échoue
            }

            $customerList[] = $customerData;

            // Récupérer l'historique des commandes du client
            try {
                $ordersOpt = [
                    'resource' => 'orders',
                    'filter[id_customer]' => $customerId,
                    'display' => 'full',
                    'sort' => 'date_add_DESC',
                ];

                $ordersXml = $webService->get($ordersOpt);
                
                if (isset($ordersXml->orders->order)) {
                    foreach ($ordersXml->orders->order as $order) {
                        $orderId = (string)$order->id;
                        $stateId = (string)$order->current_state;
                        
                        // Récupérer le nom du statut
                        $xmlState = $webService->get(['resource' => 'order_states', 'id' => $stateId]);
                        $stateName = (string)$xmlState->order_state->name->language;
                        
                        $allOrders[] = [
                            'id' => $orderId,
                            'reference' => (string)$order->reference,
                            'total_paid' => (float)$order->total_paid,
                            'payment' => (string)$order->payment,
                            'current_state' => $stateName,
                            'state_id' => $stateId,
                            'date_add' => (string)$order->date_add,
                        ];
                    }
                }
            } catch (Exception $e) {
                // Continuer même si la récupération des commandes échoue
            }

            // Récupérer les adresses du client
            try {
                $addressesOpt = [
                    'resource' => 'addresses',
                    'filter[id_customer]' => $customerId,
                    'display' => 'full',
                ];

                $addressesXml = $webService->get($addressesOpt);
                
                if (isset($addressesXml->addresses->address)) {
                    foreach ($addressesXml->addresses->address as $address) {
                        $countryId = (string)$address->id_country;
                        $xmlCountry = $webService->get(['resource' => 'countries', 'id' => $countryId]);
                        $countryName = (string)$xmlCountry->country->iso_code;

                        $fullAddress = (string)$address->address1;
                        if (!empty($address->address2)) {
                            $fullAddress .= ', ' . (string)$address->address2;
                        }
                        $fullAddress .= ', ' . (string)$address->postcode . ' ' . (string)$address->city . ', ' . $countryName;

                        $allAddresses[] = [
                            'id' => (string)$address->id,
                            'alias' => (string)$address->alias,
                            'company' => (string)$address->company,
                            'firstname' => (string)$address->firstname,
                            'lastname' => (string)$address->lastname,
                            'address' => $fullAddress,
                            'phone' => (string)$address->phone,
                            'phone_mobile' => (string)$address->phone_mobile,
                            'vat_number' => (string)$address->vat_number,
                            'date_add' => (string)$address->date_add,
                        ];
                    }
                }
            } catch (Exception $e) {
                // Continuer même si la récupération des adresses échoue
            }
        }
    } else {
        echo '<div class="alert alert-warning">';
        echo '<strong>Aucun client trouvé avec cette référence.</strong>';
        echo '</div>';
        return;
    }

} catch (PrestaShopWebserviceException $e) {
    echo '<div class="alert alert-danger">';
    echo '<strong>Erreur détectée :</strong> ' . $e->getMessage();
    echo '</div>';
    return;
}

// Affichage des données
$customer = $customerList[0]; // On prend le premier client trouvé

?>

<div class="container-fluid">
    <!-- En-tête du client -->
    <div class="customer-header">
        <h1>👤 Type de Client</h1>
        <div class="customer-type-badge">
            <?= $customer['type'] === 'Professionnel' ? '🏢' : '👨' ?> 
            <?= $customer['type'] ?>
        </div>
        
        <!-- <div class="quick-actions">
            <a href="<?= $url ?>/api/customers/<?= $customer['id'] ?>?ws_key=<?= $api ?>" target="_blank" class="action-btn">
                🔗 API PrestaShop
            </a>
            <button onclick="window.print()" class="action-btn">
                🖨️ Imprimer
            </button>
        </div> -->
    </div>

    <!-- Statistiques principales -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">Commandes</div>
            <div class="stat-value"><?= count($allOrders) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Adresses</div>
            <div class="stat-value"><?= count($allAddresses) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total dépensé</div>
            <div class="stat-value"><?= Yii::$app->formatter->asCurrency(array_sum(array_column($allOrders, 'total_paid')), 'EUR') ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Client depuis</div>
            <div class="stat-value" style="font-size: 1.1rem;"><?= Yii::$app->formatter->asDate($customer['date_add'], 'php:Y') ?></div>
        </div>
    </div>

    <!-- Informations du client -->
    <div class="info-card">
        <h3>📋 Informations du client</h3>
        
        <div class="data-grid">
            <div class="data-item">
                <div class="data-item-label">ID Client</div>
                <div class="data-item-value">#<?= $customer['id'] ?></div>
            </div>
            <?php if (!empty($customer['company'])): ?>
            <div class="data-item">
                <div class="data-item-label">Société</div>
                <div class="data-item-value"><?= $customer['company'] ?></div>
            </div>
            <?php endif; ?>
            <div class="data-item">
                <div class="data-item-label">Nom complet</div>
                <div class="data-item-value"><?= $customer['firstname'] ?> <?= $customer['lastname'] ?></div>
            </div>
            <div class="data-item">
                <div class="data-item-label">Email</div>
                <div class="data-item-value">
                    <a href="mailto:<?= $customer['email'] ?>" style="color: var(--primary-color); text-decoration: none;">
                        <?= $customer['email'] ?>
                    </a>
                </div>
            </div>
            <?php if (!empty($customer['siret'])): ?>
            <div class="data-item">
                <div class="data-item-label">SIRET</div>
                <div class="data-item-value"><?= $customer['siret'] ?></div>
            </div>
            <?php endif; ?>
            <div class="data-item">
                <div class="data-item-label">Statut</div>
                <div class="data-item-value">
                    <span class="status-badge <?= $customer['active'] ? 'active' : 'inactive' ?>">
                        <?= $customer['active'] ? 'Actif' : 'Inactif' ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="info-row">
                    <span class="info-label">📅 Inscription</span>
                    <span class="info-value"><?= Yii::$app->formatter->asDatetime($customer['date_add'], 'php:d/m/Y à H:i') ?></span>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="info-row">
                    <span class="info-label">🔄 Dernière mise à jour</span>
                    <span class="info-value"><?= Yii::$app->formatter->asDatetime($customer['date_upd'], 'php:d/m/Y à H:i') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Groupes du client -->
    <?php if (!empty($allGroups)): ?>
        <div class="info-card">
            <h3>👥 Groupes du client (<?= count($allGroups) ?>)</h3>
            <div style="padding: 1rem 0;">
                <?php foreach ($allGroups as $group): ?>
                    <span class="group-badge <?= $group['is_default'] ? 'default' : '' ?>">
                        <?= $group['name'] ?>
                        <span class="group-id">#<?= $group['id'] ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
            <?php 
            $defaultGroup = array_filter($allGroups, function($g) { return $g['is_default']; });
            if (!empty($defaultGroup)): 
                $defaultGroup = reset($defaultGroup);
            ?>
            <div style="margin-top: 1rem; padding: 1rem; background: #fef9f0; border-radius: 8px; border-left: 3px solid var(--primary-color);">
                <strong style="color: var(--primary-color);">⭐ Groupe par défaut :</strong> 
                <?= $defaultGroup['name'] ?> (#<?= $defaultGroup['id'] ?>)
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Adresses du client -->
    <?php if (!empty($allAddresses)): ?>
        <div class="info-card">
            <h3>📍 Adresses du client (<?= count($allAddresses) ?>)</h3>
            <div class="row">
                <?php foreach ($allAddresses as $address): ?>
                    <div class="col-md-6 mb-3">
                        <div class="address-card">
                            <div class="address-type">
                                📦 <?= $address['alias'] ?>
                            </div>
                            <?php if ($address['company']): ?>
                                <p style="margin: 0.5rem 0; font-weight: 600; color: var(--primary-color);">
                                    <?= $address['company'] ?>
                                </p>
                            <?php endif; ?>
                            <p style="margin: 0.3rem 0;">
                                <strong><?= $address['firstname'] ?> <?= $address['lastname'] ?></strong>
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
                            <?php if ($address['phone_mobile']): ?>
                                <p style="margin: 0.3rem 0;">
                                    <span style="color: var(--primary-color);">📱</span> 
                                    <a href="tel:<?= $address['phone_mobile'] ?>" style="color: var(--text-color); text-decoration: none;">
                                        <?= $address['phone_mobile'] ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if ($address['vat_number']): ?>
                                <p style="margin: 0.3rem 0; font-size: 0.9rem;">
                                    <span style="color: var(--primary-color);">💼</span> 
                                    TVA: <?= $address['vat_number'] ?>
                                </p>
                            <?php endif; ?>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: var(--text-color); opacity: 0.7;">
                                Ajoutée le <?= Yii::$app->formatter->asDate($address['date_add'], 'php:d/m/Y') ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="info-card">
            <h3>📍 Adresses du client</h3>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <p>Aucune adresse enregistrée pour ce client</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Historique des commandes -->
    <?php if (!empty($allOrders)): ?>
        <div class="table-wrapper">
            <div class="table-header">
                <h3>📦 Historique des commandes (<?= count($allOrders) ?>)</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Référence</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th style="text-align: right;">Montant</th>
                            <th>Date</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allOrders as $order): ?>
                            <tr>
                                <td>
                                    <strong>#<?= $order['id'] ?></strong>
                                </td>
                                <td>
                                    <a href="<?= Url::to(['orderresults', 'id' => $db_id, 'ref' => $order['reference']]) ?>" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                                        <?= $order['reference'] ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="order-status" style="background: <?= $order['state_id'] == 2 ? '#27ae60' : ($order['state_id'] == 6 ? '#e74c3c' : '#f39c12') ?>; color: white;">
                                        <?= $order['current_state'] ?>
                                    </span>
                                </td>
                                <td><?= $order['payment'] ?></td>
                                <td style="text-align: right;">
                                    <strong class="currency"><?= Yii::$app->formatter->asCurrency($order['total_paid'], 'EUR') ?></strong>
                                </td>
                                <td><?= Yii::$app->formatter->asDatetime($order['date_add'], 'php:d/m/Y H:i') ?></td>
                                <td style="text-align: center;">
                                    <a href="<?= Url::to(['orderresults', 'id' => $db_id, 'ref' => $order['reference']]) ?>" class="btn btn-sm" style="background: var(--primary-color); color: white; border: none; padding: 0.4rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                        Voir détails
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Résumé des commandes -->
            <div style="padding: 1.5rem; background: linear-gradient(135deg, #fef9f0 0%, white 100%); border-top: 2px solid var(--primary-color);">
                <div class="row">
                    <div class="col-md-4 text-center" style="padding: 1rem;">
                        <div style="font-size: 0.9rem; color: var(--text-color); margin-bottom: 0.5rem;">Total des commandes</div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary-color);">
                            <?= Yii::$app->formatter->asCurrency(array_sum(array_column($allOrders, 'total_paid')), 'EUR') ?>
                        </div>
                    </div>
                    <div class="col-md-4 text-center" style="padding: 1rem;">
                        <div style="font-size: 0.9rem; color: var(--text-color); margin-bottom: 0.5rem;">Panier moyen</div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--secondary-color);">
                            <?= Yii::$app->formatter->asCurrency(array_sum(array_column($allOrders, 'total_paid')) / count($allOrders), 'EUR') ?>
                        </div>
                    </div>
                    <div class="col-md-4 text-center" style="padding: 1rem;">
                        <div style="font-size: 0.9rem; color: var(--text-color); margin-bottom: 0.5rem;">Dernière commande</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--text-color);">
                            <?= Yii::$app->formatter->asDate($allOrders[0]['date_add'], 'php:d/m/Y') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="info-card">
            <h3>📦 Historique des commandes</h3>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <p>Aucune commande trouvée pour ce client</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Analyse du comportement client -->
    <?php if (!empty($allOrders)): ?>
        <div class="info-card">
            <h3>📊 Analyse du comportement</h3>
            <div class="data-grid">
                <div class="data-item">
                    <div class="data-item-label">Première commande</div>
                    <div class="data-item-value">
                        <?= Yii::$app->formatter->asDate(end($allOrders)['date_add'], 'php:d/m/Y') ?>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Dernière commande</div>
                    <div class="data-item-value">
                        <?= Yii::$app->formatter->asDate($allOrders[0]['date_add'], 'php:d/m/Y') ?>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Fréquence d'achat</div>
                    <div class="data-item-value">
                        <?php
                        $firstOrder = strtotime(end($allOrders)['date_add']);
                        $lastOrder = strtotime($allOrders[0]['date_add']);
                        $daysDiff = ($lastOrder - $firstOrder) / (60 * 60 * 24);
                        if (count($allOrders) > 1 && $daysDiff > 0) {
                            $frequency = round($daysDiff / (count($allOrders) - 1));
                            echo "Tous les " . $frequency . " jours";
                        } else {
                            echo "Client unique";
                        }
                        ?>
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-item-label">Moyen de paiement favori</div>
                    <div class="data-item-value">
                        <?php
                        $payments = array_column($allOrders, 'payment');
                        $paymentCounts = array_count_values($payments);
                        arsort($paymentCounts);
                        echo array_key_first($paymentCounts);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>