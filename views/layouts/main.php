<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
    <style>
        body {
            background-color: #f6f4f0 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        nav.bg-dark {
            background-color: #5c5c5c !important;
        }

        .navbar-brand img {
            width: 100px !important;
            height: auto !important;
            max-height: 40px !important;
            transition: transform 0.3s ease;
        }

        .navbar-brand {
            padding: 0.5rem 1rem !important;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            position: relative;
            padding: 0.5rem 1rem !important;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #f1ac16 !important;
            background-color: rgba(241, 172, 22, 0.1) !important;
            border-radius: 4px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #f1ac16;
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 70%;
            left: 15%;
        }

        .container-fluid {
            background-color: #f6f4f0 !important;
        }

        .breadcrumb {
            background-color: rgba(246, 244, 240, 0.8) !important;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        /* STYLES POUR LES BREADCRUMBS */
        .breadcrumb {
            background-color: rgba(246, 244, 240, 0.8) !important;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: #f1ac16 !important;
        }
        
        .breadcrumb-item a {
            color: #5c5c5c !important;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: #f1ac16 !important;
            text-decoration: underline;
        }
        
        .breadcrumb-item.active {
            color: #f1ac16 !important;
        }

        /* FOOTER STYLES - RENOMMÉ POUR ÉVITER LES CONFLITS */
        .custom-footer {
            background-color: #5c5c5c !important;
            color: white !important;
            margin-top: auto;
        }

        .custom-footer a {
            color: #f1ac16 !important;
        }

        .custom-footer a:hover {
            color: #e69500 !important;
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* STYLES POUR LES ENTÊTES DE TABLEAU */
        .table thead th {
            background: linear-gradient(145deg, #f1ac16, #e69500) !important;
            color: white !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.85rem !important;
            letter-spacing: 0.5px !important;
            border: none !important;
            padding: 1rem 1.25rem !important;
            position: relative !important;
            box-shadow: 0 2px 4px rgba(241, 172, 22, 0.3) !important;
        }

        .table thead th a {
            color: #5c5c5c !important;
            text-decoration: none !important;
        }

        .table thead th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #5c5c5c, transparent);
        }

        .table thead th:first-child {
            border-top-left-radius: 8px !important;
        }

        .table thead th:last-child {
            border-top-right-radius: 8px !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(241, 172, 22, 0.05) !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="d-flex flex-column h-100">
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?php
        $this->registerJs("
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '" . addslashes(Yii::$app->session->getFlash('error')) . "',
            confirmButtonColor: '#dc3545',
            customClass: {
                popup: 'rounded-swal'
            }
        });
    ");
        ?>
    <?php elseif (Yii::$app->session->hasFlash('warning')): ?>
        <?php
        $this->registerJs("
        Swal.fire({
            icon: 'warning',
            title: 'Alerte',
            text: '" . addslashes(Yii::$app->session->getFlash('warning')) . "',
            confirmButtonColor: '#ffc107',
            customClass: {
                popup: 'rounded-swal'
            }
        });
    ");
        ?>
    <?php elseif (Yii::$app->session->hasFlash('success')): ?>
        <?php
        $this->registerJs("
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '" . addslashes(Yii::$app->session->getFlash('success')) . "',
            confirmButtonColor: '#28a745',
            customClass: {
                popup: 'rounded-swal'
            }
        });
    ");
        ?>
    <?php endif; ?>
    <?php $this->beginBody() ?>

    <header id="header">
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'brandImage' => "@web/vaisonet.webp",
            'brandOptions' => ['width' => '180px', 'class' => 'navbar-brand'],
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top shadow-sm']
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto'],
            'items' => [
                ['label' => '<i class="fas fa-shopping-cart me-1"></i> PrestaShop', 'url' => ['/prestashop/index'], 'encode' => false],
                ['label' => '<i class="fab fa-wordpress me-1"></i> WooCommerce', 'url' => ['/woocommerce/index'], 'encode' => false],
                ['label' => '<i class="fas fa-store me-1"></i> Shopify', 'url' => ['/shopify/index'], 'encode' => false],
            ]
        ]);
        NavBar::end();
        ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <div class="container-fluid py-4" style="padding-top:5em">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <div class="mb-3">
                    <?= Breadcrumbs::widget([
                        'links' => $this->params['breadcrumbs'],
                        'options' => ['class' => 'breadcrumb']
                    ]) ?>
                </div>
            <?php endif ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="custom-footer mt-auto py-4">
        <div class="container">
            <div class="row text-center text-md-start">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="text-white mb-3"></i>Vaisonet</h5>
                    <p class="text-light mb-0"><i class="fas fa-copyright me-2"></i> <?= date('Y') ?> Tous droits réservés</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="text-white mb-3"><i class="fas fa-map-marker-alt me-2"></i>Adresse</h5>
                    <p class="text-light mb-0">
                        35 rue Des Ormeaux<br>
                        84110 Vaison-la-Romaine<br>
                        France
                    </p>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white mb-3"><i class="fas fa-envelope me-2"></i>Contact</h5>
                    <p class="text-light mb-0">
                        <i class="fas fa-envelope me-2 d-none d-md-inline"></i>contact@vaisonet.com<br>
                        <i class="fas fa-phone me-2 d-none d-md-inline"></i>+33 (0)4 65 02 08 20
                    </p>
                </div>
            </div>
            <hr class="my-3 bg-secondary">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-light mb-0">Propulsé par <span class="fw-bold" style="color: #f1ac16;">Tiana</span></p>
                </div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Animation au chargement
        $('main').css('opacity', '0').animate({
            opacity: 1
        }, 500);

        // Smooth scrolling pour les liens internes
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            var target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 800);
            }
        });
    });
</script>

</html>
<?php $this->endPage() ?>