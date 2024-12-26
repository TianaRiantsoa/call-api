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

    // Supprimer tous les produits
    $page = 1;
    do {
        $products = $woocommerce->get('products', ['per_page' => 100, 'page' => $page]);

        foreach ($products as $product) {
            $woocommerce->delete("products/{$product->id}", ['force' => true]);
            echo "Produit supprimé : {$product->id} -> {$product->sku} -> {$product->name} <br>";
        }

        $page++;
    } while (count($products) > 0);

    // Supprimer toutes les catégories
    $page = 1;
    do {
        $categories = $woocommerce->get('products/categories', ['per_page' => 100, 'page' => $page]);

        foreach ($categories as $category) {
            $woocommerce->delete("products/categories/{$category->id}", ['force' => true]);
            echo "Catégorie supprimée : {$category->id} -> {$category->name} <br>";
        }

        $page++;
    } while (count($categories) > 0);

    echo "Tous les produits et catégories ont été supprimés définitivement.\n";

} catch (Exception $e) {
    // Gestion des erreurs
    echo 'Erreur : ' . $e->getMessage() . "\n";
}
