<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = 'Nouveau client Woocommerce';
$this->params['breadcrumbs'][] = ['label' => 'Woocommerces', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="woocommerce-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
