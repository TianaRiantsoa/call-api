<?php


use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Woocommerce $model */

$this->title = 'Commandes | ' . Html::encode($ref) . ' | ' . Html::encode($model->url);
$this->params['breadcrumbs'][] = ['label' => 'Woocommerce', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de client', 'url' => ['orders', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);

$url = Html::encode($model->url);

$headers = @get_headers("http://" . $url);
if ($headers && strpos($headers[0], '200') !== false) {
    $url = "https://" . $url;
} else {
    $url = "https://" . $url;
}

$consumer_key = Html::encode($model->consumer_key);
$consumer_secret = Html::encode($model->consumer_secret);
$ref = Html::encode($ref);


use Automattic\WooCommerce\Client;

try {
    // Connexion à l'API WooCommerce
    $woocommerce = new Client(
        $url, // Remplacez par l'URL de votre boutique
        $consumer_key, // Clé consommateur
        $consumer_secret, // Secret consommateur
        [
            'version' => 'wc/v3',
        ]
    );

    // Initialisation pour pagination
    $page = 1;

    do {
        // Récupérer les catégories par page
        $categories = $woocommerce->get('products/categories', [
            'per_page' => 100, // Maximum 100 catégories par page
            'page' => $page,
        ]);

        foreach ($categories as $category) {
            $categoryId = $category->id;

            // Supprimer la catégorie
            $woocommerce->delete("products/categories/$categoryId", ['force' => true]);
            echo "Catégorie ID $categoryId supprimée avec succès.\n";
        }

        $page++;
    } while (!empty($categories)); // Continue tant qu'il y a des catégories

    echo "Toutes les catégories ont été supprimées avec succès.\n";

} catch (Exception $e) {
    // Gestion des erreurs
    echo 'Erreur : ' . $e->getMessage() . "\n";
}
