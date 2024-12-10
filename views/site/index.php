<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Webservices Vaisonet';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-4 mb-3">
                <a href="prestashop/index"><img src="<?= Yii::getAlias('@web'); ?>/prestashop.png" class="img-fluid" width="250px" alt="PrestaShop"></a>
                <br><br>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-outline-secondary" href="prestashop/index">Commencer &raquo;</a></p>
            </div>
            <div class="col-lg-4 mb-3">
                <a href="woocommerce/index"><img src="<?= Yii::getAlias('@web'); ?>/woocommerce-logo.png" class="img-fluid" width="250px" alt="WooCommerce"></a>
                <br><br>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-outline-secondary" href="woocommerce/index">Commencer &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <a href="shopify/index"><img src="<?= Yii::getAlias('@web'); ?>/shopify.svg" class="img-fluid" width="200px" alt="Shopify"></a>
                <br><br>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.
                </p>

                <p><a class="btn btn-outline-secondary" href="shopify/index">Commencer &raquo;</a></p>
            </div>
        </div>

    </div>
</div>