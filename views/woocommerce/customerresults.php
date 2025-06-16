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

// try {
//     // Connexion à l'API WooCommerce
//     $woocommerce = new Client(
//         $url, // Remplacez par l'URL de votre boutique
//         $consumer_key, // Clé consommateur
//         $consumer_secret, // Secret consommateur
//         [
//             'version' => 'wc/v3',
//         ]
//     );


//     // URL de l’image à utiliser pour tous les produits
//     $image_url = 'https://www.vaisonet.com/wp-content/uploads/2024/02/IMAGE-PRODUIT.webp';

//     // Récupérer tous les produits (par lots si nécessaire)
//     $page = 1;
//     $per_page = 100; // Ajuster selon vos besoins

//     do {
//         $products = $woocommerce->get('products', [
//             'per_page' => $per_page,
//             'page'     => $page,
//         ]);

//         foreach ($products as $product) {
//             // Mettre à jour l'image du produit
//             $data = [
//                 'images' => [
//                     ['src' => $image_url]
//                 ]
//             ];

//             $woocommerce->put('products/' . $product->id, $data);
//             echo "Produit ID " . $product->id . " mis à jour avec la nouvelle image.\n";
//         }

//         $page++;
//     } while (!empty($products));

//     echo "Mise à jour terminée !";
// } catch (Exception $e) {
//     echo 'Erreur : ' . $e->getMessage() . "\n";
// }



$woocommerce = new Client(
    $url,
    $consumer_key,
    $consumer_secret,
    [
        'follow_redirects' => false,
            'validate_url' => false,
            'timeout' => 300,
            'user_agent' => 'Vaisonet E-connecteur',
        'version' => 'wc/v3',
        'verify_ssl' => true,
        'wp-api' => false,
        'headers'    => [
            'User-Agent' => 'Vaisonet E-connecteur'
        ],
    ]
);

// 1️⃣ Récupérer la version WooCommerce & WordPress via system_status
try {
    $status = $woocommerce->get('system_status');
    echo "✅ WooCommerce Version: " . $status->environment->version . PHP_EOL;
    echo "✅ WordPress Version: " . $status->environment->wp_version . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Erreur lors de la récupération du system_status: " . $e->getMessage() . PHP_EOL;
}

// 2️⃣ Récupérer les taxes configurées
try {
    $taxes = $woocommerce->get('taxes');
    echo "📌 Taxes configurées:" . PHP_EOL;
    foreach ($taxes as $tax) {
        echo "- " . $tax->name . " (" . $tax->rate . "%)" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de la récupération des taxes: " . $e->getMessage() . PHP_EOL;
}

// 3️⃣ Récupérer tous les produits (pagination automatique)
try {
    $all_products = [];
    $per_page = 100;
    $page = 1;

    do {
        $products = $woocommerce->get('products', ['per_page' => $per_page, 'page' => $page]);
        $all_products = array_merge($all_products, $products);
        echo "🔄 Page $page récupérée, " . count($products) . " produits." . PHP_EOL;
        $page++;
    } while (count($products) === $per_page);

    echo "✅ Total des produits récupérés: " . count($all_products) . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Erreur lors de la récupération des produits: " . $e->getMessage() . PHP_EOL;
}