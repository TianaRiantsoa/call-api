<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Webservices Vaisonet';

?>
<div class="site-index">
    <div class="body-content">

    <div>
        <img src="https://lille.wordcamp.org/2018/files/2018/11/Vaisonet.png?w=640" class="img-fluid mx-auto d-block" width="500px" alt="Vaisonet">
        <h1 class="text-center"><b>Vaisonet</b> est un éditeur de solutions e-commerce, spécialiste de la gestion des flux e-commerce et de l'optimisation des processus métier multicanal.</h1>
        <br>
        <p class="text-center">Sélectionnez votre CMS</p>
    </div>

        <div class="row">
            <div class="col-lg-4 mb-3 cms">
                <a href="prestashop/index"><img src="<?= Yii::getAlias('@web'); ?>/prestashop.png" class="img-fluid" width="250px" alt="PrestaShop"></a>
                <br><br>
                <p>
                    PrestaShop est une application Web open source permettant de créer une boutique en ligne dans le but de réaliser du commerce électronique.
                    <br><br>
                    PrestaShop est aussi le nom de la société éditrice de cette solution.
                </p>

                <p><a class="btn btn-outline-secondary" href="prestashop/index">Commencer &raquo;</a></p>
            </div>
            <div class="col-lg-4 mb-3 cms">
                <a href="woocommerce/index"><img src="<?= Yii::getAlias('@web'); ?>/woocommerce-logo.png" class="img-fluid" width="250px" alt="WooCommerce"></a>
                <br><br>
                <p>
                    WooCommerce est une extension open source pour WordPress permettant de créer une boutique en ligne. Il est conçu pour les petites et grandes entreprises en ligne utilisant WordPress. Lancé le 27 septembre 2011, le plugin est rapidement devenu célèbre pour sa simplicité d'installation et de personnalisation.
                    <br><br>
                    WooCommerce et WooCommerce Multilingual sont dans le répertoire des extensions de WordPress.
                </p>

                <p><a class="btn btn-outline-secondary" href="woocommerce/index">Commencer &raquo;</a></p>
            </div>
            <div class="col-lg-4 mb-3 cms">
                <a href="shopify/index"><img src="<?= Yii::getAlias('@web'); ?>/shopify.svg" class="img-fluid" width="200px" alt="Shopify"></a>
                <br><br>
                <p>
                    Shopify est une plate-forme de commerce électronique en mode SaaS, basée sur un modèle propriétaire, qui permet aux individus et aux entreprises de créer et d'animer leur propre commerce en ligne, lesquels sont hébergés contre une redevance mensuelle.
                </p>

                <p><a class="btn btn-outline-secondary" href="shopify/index">Commencer &raquo;</a></p>
            </div>
        </div>

    </div>
</div>