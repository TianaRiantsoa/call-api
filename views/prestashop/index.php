<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\PrestashopSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Prestashop';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prestashop-index">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3" style="border-bottom: 3px solid #f1ac16;">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0 text-dark">
                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                    <?= Html::encode($this->title) ?>
                </h1>
                <div>
                    <?= Html::a('<i class="fas fa-plus me-1"></i> Créer un client', ['create'], [
                        'class' => 'btn btn-primary btn-sm',
                        'style' => 'background-color: #f1ac16; border-color: #f1ac16; border-radius: 20px;'
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{items}\n<div class='card-footer bg-white py-3'>{summary}\n{pager}</div>",
                'tableOptions' => [
                    'class' => 'table table-hover mb-0',
                    'style' => 'margin-bottom: 0;'
                ],
                'options' => [
                    'class' => 'table-responsive'
                ],
                'headerRowOptions' => [
                    'class' => 'table-dark'
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'class' => $index % 2 == 0 ? 'table-light' : 'table-white',
                        'style' => 'cursor: pointer; transition: all 0.2s ease;'
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'url',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none;'],
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                        'value' => function ($model) {
                            return Html::a(
                                '<i class="fas fa-external-link-alt me-2 text-info"></i>' . Html::encode($model->url),
                                ['view', 'id' => $model->id],
                                [
                                    'target' => '_blank',
                                    'class' => 'text-decoration-none',
                                    'data-pjax' => 0
                                ]
                            );
                        },
                    ],
                    [
                        'attribute' => 'api_key',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none;'],
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                        'value' => function ($model) {
                            $displayKey = strlen($model->api_key) > 20 ?
                                substr($model->api_key, 0, 10) . '...' . substr($model->api_key, -10) :
                                $model->api_key;

                            return Html::a(
                                '<i class="fas fa-key me-2 text-warning"></i>' . Html::encode($displayKey),
                                ['view', 'id' => $model->id],
                                [
                                    'target' => '_blank',
                                    'class' => 'text-decoration-none',
                                    'title' => 'Cliquez pour voir les détails',
                                    'data-pjax' => 0
                                ]
                            );
                        },
                    ],

                    // nouvelle colonne Version
                    [
                        'label' => 'Version',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'bg-dark text-white', 'style' => 'border: none; width:120px;'],
                        'contentOptions' => ['style' => 'vertical-align: middle;'],
                        'value' => function ($model) {
                            try {
                                $serviceUrl = prepareServiceUrl($model->url);
                                $version = getPrestaShopWsVersion($serviceUrl, $model->api_key);
                                return Html::tag('span', Html::encode($version), ['class' => 'badge bg-light text-dark']);
                            } catch (\Throwable $e) {
                                return Html::tag('span', 'n/a', ['class' => 'badge bg-light text-muted']);
                            }
                        },
                    ],

                    // ...existing columns or action columns...
                ],
                'pager' => [
                    'options' => [
                        'class' => 'pagination justify-content-center mb-0',
                        'style' => 'margin-top: 0; margin-bottom: 0;'
                    ],
                    'linkContainerOptions' => [
                        'class' => 'page-item'
                    ],
                    'linkOptions' => [
                        'class' => 'page-link',
                        'style' => 'color: #5c5c5c; border-color: #dee2e6;'
                    ],
                    'activePageCssClass' => 'active',
                    'disabledPageCssClass' => 'disabled',
                    'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
                ],
            ]); ?>
            
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<style>
    .prestashop-index .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .prestashop-index .table-hover tbody tr:hover {
        background-color: rgba(241, 172, 22, 0.05) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .prestashop-index .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .prestashop-index .table td, 
    .prestashop-index .table th {
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }
    
    .prestashop-index .btn {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .prestashop-index .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(241, 172, 22, 0.3);
    }
    
    .prestashop-index .card-footer {
        border-top: 1px solid #e9ecef !important;
    }
    
    .prestashop-index .pagination .page-item.active .page-link {
        background-color: #f1ac16 !important;
        border-color: #f1ac16 !important;
        color: white !important;
    }
    
    .prestashop-index .pagination .page-link:hover {
        background-color: #f1ac16 !important;
        border-color: #f1ac16 !important;
        color: white !important;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au chargement
    $('.prestashop-index .card').css('opacity', '0').animate({opacity: 1}, 500);
    
    // Effet de survol sur les lignes du tableau
    $('.prestashop-index .table tbody tr').hover(
        function() {
            $(this).css({
                'transform': 'translateY(-2px)',
                'box-shadow': '0 4px 12px rgba(0,0,0,0.15)'
            });
        },
        function() {
            $(this).css({
                'transform': 'translateY(0)',
                'box-shadow': 'none'
            });
        }
    );
    
    // Animation pour les boutons d'action
    $('.prestashop-index .action-column a').hover(
        function() {
            $(this).animate({fontSize: '1.2em'}, 100);
        },
        function() {
            $(this).animate({fontSize: '1em'}, 100);
        }
    );
});
</script>

<?php
// helpers pour récupérer la version PS Webservice (inspiré de view.php)
// if (!function_exists('prepareServiceUrl')) {
    function prepareServiceUrl(string $rawUrl): string
    {
        $url = trim($rawUrl);
        $url = preg_replace('#^https?://#', '', $url);
        if (stripos($url, 'localhost') !== false) {
            return "http://$url";
        }
        return "https://$url";
    }
// }

// if (!function_exists('getPrestaShopWsVersion')) {
    function getPrestaShopWsVersion(string $serviceUrl, string $apiKey): string
    {
        $endpoint = rtrim($serviceUrl, '/') . '/api/?ws_key=' . $apiKey;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER => ['Expect:'],
        ]);
        $resp = @curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($resp === false || $resp === null) {
            return 'n/a';
        }

        // séparer header/body et chercher l'en-tête psws-version
        $pos = strpos($resp, "\r\n\r\n");
        $header = $pos !== false ? substr($resp, 0, $pos) : $resp;
        $lines = preg_split("/\r\n|\n|\r/", $header);
        foreach ($lines as $line) {
            if (stripos($line, 'psws-version:') !== false) {
                $parts = explode(':', $line, 2);
                return trim($parts[1]);
            }
        }

        // fallback : essayer d'extraire version dans le body si XML contient <prestashop> infos (rare)
        if ($pos !== false) {
            $body = substr($resp, $pos + 4);
            if (stripos($body, 'psws-version') !== false) {
                if (preg_match('#psws-version[^\>]*\>([^<]+)\<#i', $body, $m)) {
                    return trim($m[1]);
                }
            }
        }

        return 'n/a';
    }
// }