<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Créer un client PrestaShop';
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prestashop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
