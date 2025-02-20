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


    // 1. Supprimer tous les produits et leurs variations
    $page = 1;
        // Récupérer tous les produits
        $products = $woocommerce->get('products/12825', [
            'per_page' => 100,
            'page' => $page,
        ]);

        
            // Supprimer les variations du produit si c'est un produit variable
            if ($products->type === 'variable') {
                $variations = $woocommerce->get("products/12825/variations");
                foreach ($variations as $variation) {
                    $woocommerce->delete("products/{$products->id}/variations/{$variation->id}", ['force' => true]);
                    echo "Variation supprimée : ID {$variation->id}, Produit Parent : {$products->name}<br>";
                }
            }

            // Supprimer le produit principal
            $woocommerce->delete("products/{$products->id}", ['force' => true]);
            echo "Produit supprimé : ID {$products->id}, Nom : {$products->name}<br>";
        

        $page++;

    // // 2. Supprimer toutes les catégories
    // $page = 1;
    // do {
    //     // Récupérer toutes les catégories
    //     $categories = $woocommerce->get('products/attributes/3/terms', [
    //         'per_page' => 100,
    //         'page' => $page,
    //     ]);

    //     foreach ($categories as $category) {
    //         // Supprimer la catégorie
    //         $woocommerce->delete("products/attributes/3/terms/{$category->id}", ['force' => true]);
    //         echo "Catégorie supprimée : ID {$category->id}, Nom : {$category->name}<br>";
    //     }

    //     $page++;
    // } while (count($categories) > 0);
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . "\n";
}
