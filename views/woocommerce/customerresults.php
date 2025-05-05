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


    // URL de l’image à utiliser pour tous les produits
    $image_url = 'https://www.vaisonet.com/wp-content/uploads/2024/02/IMAGE-PRODUIT.webp';

    // Récupérer tous les produits (par lots si nécessaire)
    $page = 1;
    $per_page = 100; // Ajuster selon vos besoins

    do {
        $products = $woocommerce->get('products', [
            'per_page' => $per_page,
            'page'     => $page,
        ]);

        foreach ($products as $product) {
            // Mettre à jour l'image du produit
            $data = [
                'images' => [
                    ['src' => $image_url]
                ]
            ];

            $woocommerce->put('products/' . $product->id, $data);
            echo "Produit ID " . $product->id . " mis à jour avec la nouvelle image.\n";
        }

        $page++;
    } while (!empty($products));

    echo "Mise à jour terminée !";
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . "\n";
}
