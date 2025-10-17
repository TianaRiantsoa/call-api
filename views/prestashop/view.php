<?php

use prestashop\PrestaShopWebservice;
use prestashop\PrestaShopWebserviceException;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

\yii\web\YiiAsset::register($this);

/**
 * Prépare l'URL de service (http pour localhost, sinon https).
 */
function prepareServiceUrl(string $rawUrl): string
{
    $url = trim($rawUrl);
    // ne pas double-encoder
    $url = preg_replace('#^https?://#', '', $url);
    if (stripos($url, 'localhost') !== false) {
        return "http://$url";
    }
    return "https://$url";
}

/**
 * Récupère la version PrestaShop Webservice depuis l'en-tête API.
 * Retourne une chaîne lisible ou un message d'erreur.
 */
function getPrestaShopWsVersion(string $serviceUrl, string $apiKey): string
{
    $endpoint = rtrim($serviceUrl, '/') . '/api/?ws_key=' . $apiKey;
    $defaultOpts = [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => false,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => $apiKey . ':',
        CURLOPT_HTTPHEADER => ['Expect:'],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_MAXREDIRS => 3,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $defaultOpts);
    $resp = @curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $resp === null) {
        return 'Version PSWS : impossible de contacter le service (' . ($err ?: 'erreur inconnue') . ')';
    }

    // séparer en-tête / body
    $pos = strpos($resp, "\r\n\r\n");
    $header = $pos !== false ? substr($resp, 0, $pos) : $resp;
    $lines = preg_split("/\r\n|\n|\r/", $header);
    foreach ($lines as $line) {
        if (stripos($line, 'psws-version:') !== false) {
            $parts = explode(':', $line, 2);
            return 'PrestaShop version : ' . trim($parts[1]);
        }
    }
    return 'PrestaShop version : introuvable (en-tête non présent)';
}

/* ---------- initialisation et préparation ---------- */

$serviceUrl = prepareServiceUrl($model->url);
$apiKey = $model->api_key;
$pswsVersion = getPrestaShopWsVersion($serviceUrl, $apiKey);

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prestashop-view">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0 text-dark">
                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                    <?= Html::encode($this->title) ?> — <small class="text-muted"><?= Html::encode($pswsVersion) ?></small>
                </h1>

                <div>
                    <?= Html::a('<i class="fas fa-edit me-1"></i> Mettre à jour', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-success btn-sm me-2',
                        'style' => 'border-radius:20px'
                    ]) ?>
                    <?= Html::a('<i class="fas fa-trash me-1"></i> Supprimer', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => 'Êtes-vous sûr de vouloir supprimer ce client ?',
                            'method' => 'post',
                        ],
                        'style' => 'border-radius:20px'
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="card-body pb-0">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted"><i class="fas fa-globe me-2 text-primary"></i> URL du site</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light"><i class="fas fa-link text-info"></i></span>
                        <input type="text" class="form-control" value="<?= Html::encode($model->url) ?>" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted"><i class="fas fa-key me-2 text-warning"></i> Clé API</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light"><i class="fas fa-key text-warning"></i></span>
                        <input type="text" class="form-control" value="<?= Html::encode($model->api_key) ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <h2 class="h5 mb-0 text-dark text-center">
                <i class="fas fa-search me-2 text-primary"></i> Options de recherche disponibles
            </h2>
        </div>

        <div class="card-body text-center">
            <div class="row g-3">
                <?php
                $buttons = [
                    ['label' => 'Produits', 'icon' => 'box-open', 'route' => 'products'],
                    ['label' => 'Commandes', 'icon' => 'shopping-cart', 'route' => 'orders'],
                    ['label' => 'Historique de commandes', 'icon' => 'history', 'route' => 'orderhistories'],
                    ['label' => 'Clients', 'icon' => 'users', 'route' => 'customers'],
                ];

                foreach ($buttons as $btn): ?>
                    <div class="col-md-3 col-sm-6">
                        <?= Html::a(
                            "<i class=\"fas fa-{$btn['icon']} fa-2x mb-2 text-primary\"></i><br>{$btn['label']}",
                            [$btn['route'], 'id' => $model->id],
                            [
                                'class' => 'btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center',
                                'style' => 'border-radius:10px; transition:all .3s; border-color:#f1ac16; color:#5c5c5c;'
                            ]
                        ) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Styles et scripts enregistrés proprement via l'API Yii
$this->registerCss(
    <<<CSS
.prestashop-view .card { border-radius:10px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
.prestashop-view .btn:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(241,172,22,0.3) !important; border-color:#f1ac16 !important; }
.prestashop-view .detail-view th { font-weight:600; color:#5c5c5c; }
.prestashop-view .detail-view td { color:#495057; }
.prestashop-view .btn-outline-primary { border-color:#f1ac16; color:#5c5c5c; }
.prestashop-view .btn-outline-primary:hover { background-color:#f1ac16; border-color:#f1ac16; color:white; }
.prestashop-view .card-footer { border-top:1px solid #e9ecef !important; }
CSS
);

$js = <<<JS
jQuery(function($){
    // apparition progressive des cartes
    $('.prestashop-view .card').css('opacity',0).each(function(i){
        $(this).delay(180*i).animate({opacity:1},600);
    });

    // léger agrandissement au hover
    $('.prestashop-view .btn-outline-primary').hover(
        function(){ $(this).stop(true).animate({fontSize:'1.05em'},150); },
        function(){ $(this).stop(true).animate({fontSize:'1em'},150); }
    );

    // pulse discret sur icônes
    setInterval(function(){
        $('.prestashop-view .btn-outline-primary i').each(function(){
            $(this).css('transform','scale(1.08)');
            $(this).animate({opacity:1},200,function(){ $(this).css('transform',''); });
        });
    }, 3000);
});
JS;

$this->registerJs($js);
