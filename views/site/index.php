<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Webservices Vaisonet';

?>
<div class="site-index" style="margin-top: 1.5rem !important;">
    <div class="container py-5">
        <!-- Hero Section -->
        <section class="hero-section text-center mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <img src="https://lille.wordcamp.org/2018/files/2018/11/Vaisonet.png?w=640" 
                         class="img-fluid mx-auto d-block mb-4 rounded shadow" 
                         width="500px" 
                         alt="Vaisonet"
                         style="border: 5px solid #f1ac16;">
                    <h1 class="display-4 fw-bold text-dark mb-4">
                        <span style="color: #f1ac16;">Vaisonet</span> est un éditeur de solutions e-commerce
                    </h1>
                    <p class="lead text-muted mb-4">
                        Spécialiste de la gestion des flux e-commerce et de l'optimisation des processus métier multicanal
                    </p>
                    <div class="mb-5">
                        <p class="text-uppercase text-muted mb-3">Sélectionnez votre CMS</p>
                        <div class="vr mx-3 d-none d-md-inline"></div>
                        <i class="fas fa-chevron-down" style="color: #f1ac16;"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- CMS Cards Section -->
        <section class="cms-section">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 cms-card" style="transition: all 0.3s ease; border-radius: 15px;">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <img src="<?= Yii::getAlias('@web'); ?>/prestashop.png" 
                                     class="img-fluid mb-3" 
                                     width="150px" 
                                     alt="PrestaShop"
                                     style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                            </div>
                            <!-- <h3 class="card-title text-dark mb-3">PrestaShop</h3> -->
                            <p class="card-text text-muted">
                                PrestaShop est une application Web open source permettant de créer une boutique en ligne 
                                dans le but de réaliser du commerce électronique. Solution complète et flexible pour 
                                les boutiques en ligne.
                            </p>
                            <a href="prestashop/index" class="btn btn-primary btn-lg" 
                               style="background-color: #f1ac16; border-color: #f1ac16; border-radius: 25px;">
                                <i class="fas fa-rocket me-2"></i>Commencer avec PrestaShop
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 cms-card" style="transition: all 0.3s ease; border-radius: 15px;">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <img src="<?= Yii::getAlias('@web'); ?>/woocommerce-logo.png" 
                                     class="img-fluid mb-3" 
                                     width="150px" 
                                     alt="WooCommerce"
                                     style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                            </div>
                            <!-- <h3 class="card-title text-dark mb-3">WooCommerce</h3> -->
                            <p class="card-text text-muted">
                                Extension open source pour WordPress permettant de créer une boutique en ligne. 
                                Parfait pour les sites WordPress existants, facile à installer et à personnaliser.
                            </p>
                            <a href="woocommerce/index" class="btn btn-primary btn-lg" 
                               style="background-color: #f1ac16; border-color: #f1ac16; border-radius: 25px;">
                                <i class="fab fa-wordpress me-2"></i>Commencer avec WooCommerce
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 cms-card" style="transition: all 0.3s ease; border-radius: 15px;">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <img src="<?= Yii::getAlias('@web'); ?>/shopify.svg" 
                                     class="img-fluid mb-3" 
                                     width="120px" 
                                     alt="Shopify"
                                     style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                            </div>
                            <!-- <h3 class="card-title text-dark mb-3">Shopify</h3> -->
                            <p class="card-text text-muted">
                                Plate-forme de commerce électronique en mode SaaS qui permet aux individus et 
                                aux entreprises de créer et d'animer leur commerce en ligne facilement.
                            </p>
                            <a href="shopify/index" class="btn btn-primary btn-lg" 
                               style="background-color: #f1ac16; border-color: #f1ac16; border-radius: 25px;">
                                <i class="fas fa-store me-2"></i>Commencer avec Shopify
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section mt-5">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold text-dark">Nos Avantages</h2>
                    <hr class="w-25 mx-auto" style="border-color: #f1ac16; height: 3px;">
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="p-4">
                        <i class="fas fa-bolt fa-3x text-warning mb-3"></i>
                        <h4 class="text-dark">Rapide & Efficace</h4>
                        <p class="text-muted">Intégration rapide et performances optimales pour votre e-commerce</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-4">
                        <i class="fas fa-sync-alt fa-3x text-warning mb-3"></i>
                        <h4 class="text-dark">Synchronisation</h4>
                        <p class="text-muted">Gestion fluide des flux entre vos différentes plateformes</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-4">
                        <i class="fas fa-headset fa-3x text-warning mb-3"></i>
                        <h4 class="text-dark">Support 24/7</h4>
                        <p class="text-muted">Assistance technique disponible à tout moment</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    .cms-card {
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        border: 1px solid #e9ecef;
    }
    
    .cms-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        border-color: #f1ac16 !important;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(241, 172, 22, 0.3) !important;
    }
    
    .rounded {
        border-radius: 15px !important;
    }
    
    .shadow {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .shadow-sm {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
    }
</style>

<script>
$(document).ready(function() {
    // Animation au survol des cartes
    $('.cms-card').hover(
        function() {
            $(this).css('transform', 'translateY(-10px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
    
    // Animation au chargement des cartes
    $('.cms-card').each(function(index) {
        $(this).css('opacity', '0').delay(200 * index).animate({opacity: 1}, 600);
    });
    
    // Effet de parallaxe léger sur le header
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        $('.hero-section').css('transform', 'translateY(' + scroll * 0.3 + 'px)');
    });
});
</script>