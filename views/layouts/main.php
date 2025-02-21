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
//$this->registerLinkTag(['rel' => 'stylesheet', 'href' => Yii::getAlias('@web/css/main.css')]);
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
        .navbar_brand img {
            width: 120px !important;
        }
        nav.bg-dark{
            background-color: #5c5c5c !important;
        }
    </style>
    <link rel="stylesheet" href="https://d11lu0htm9h2oc.cloudfront.net/back/v2/css/v1.17.css">
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
            text: '" . Yii::$app->session->getFlash('error') . "',
        });
    ");
        ?>
    <?php elseif (Yii::$app->session->hasFlash('warning')): ?>
        <?php
        $this->registerJs("
        Swal.fire({
            icon: 'warning',
            title: 'Alerte',
            text: '" . Yii::$app->session->getFlash('warning') . "',
        });
    ");
        ?>
    <?php elseif (Yii::$app->session->hasFlash('success')): ?>
        <?php
        $this->registerJs("
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '" . Yii::$app->session->getFlash('success') . "',
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
            'brandOptions' => ['width' => '150px !important', 'alt' => 'Vaisonet'],
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                ['label' => 'PrestaShop', 'url' => ['/prestashop/index']],
                ['label' => 'WooCommerce', 'url' => ['/woocommerce/index']],
                ['label' => 'Shopify', 'url' => ['/shopify/index']],
                // Yii::$app->user->isGuest
                //     ? ['label' => 'Login', 'url' => ['/site/login']]
                //     : '<li class="nav-item">'
                //     . Html::beginForm(['/site/logout'])
                //     . Html::submitButton(
                //         'Logout (' . Yii::$app->user->identity->username . ')',
                //         ['class' => 'nav-link btn btn-link logout']
                //     )
                //     . Html::endForm()
                //     . '</li>'
            ]
        ]);
        NavBar::end();
        ?>
    </header>



    <main id="main" class="flex-shrink-0" role="main">
        <div class="container-fluid" style="padding-top:5em">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            <?php endif ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy; Vaisonet
                    <?= date('Y') ?>
                </div>
                <div class="col-md-6 text-center text-md-end">Propulsé par Tiana</div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // On s'assure que Bootstrap a bien été initialisé
    var accordions = document.querySelectorAll('.accordion-button');
    
    accordions.forEach(function (accordion) {
      accordion.addEventListener('click', function() {
        console.log("Accordéon cliqué : ", this);
      });
    });
  });
</script>

</html>
<?php $this->endPage() ?>