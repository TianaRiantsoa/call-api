<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Prestashop $model */

$this->title = 'Produits | ' . Html::encode($ref) . ' | ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Prestashop', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Recherche de produit', 'url' => ['products', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($ref)];
\yii\web\YiiAsset::register($this);
$url = Html::encode($model->url);
$api = Html::encode($model->api_key);
$ref = Html::encode($ref);

require('function.php');
?>
<div class="prestashop-products-results">

    <main class="d-flex flex-nowrap">

        <div class="container-fluid">
            <div class="accordion" id="accordionProduct">

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            Détail du produit
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1"
                        data-bs-parent="#accordionProduct">
                        <div class="accordion-body">
                            <table class="table table-striped table-hover">
                                <thead class="thead-inverse">
                                    <tr class="head-table-color">
                                        <th scope="col">#</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">Référence</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Catégorie</th>
                                        <th scope="col">Prix HT</th>
                                        <th scope="col">Prix TTC</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Création</th>
                                        <th scope="col">Mise à jour</th>
                                    </tr>
                                </thead>
                                <?php
                                $req_lang = "https://$url/api/languages/?filter[iso_code]=fr&display=full&ws_key=$api";
                                $decode = extractjson($req_lang);
                                $lang_id = $decode->languages->language->id;

                                $req_prod = "https://$url/api/products/?filter[reference]=$ref&display=full&language=$lang_id&ws_key=$api&output_format=JSON";
                                $get_product = file_get_contents($req_prod);
                                $decode = json_decode($get_product);

                                //Interrogation de produit
                                
                                $nb_prod = sizeof($decode->products);
                                $nbr_prod = $nb_prod - 1;
                                $prod = 0;

                                while ($prod <= $nbr_prod) {

                                    $id = $decode->products[$prod]->id;
                                    $reference = $decode->products[$prod]->reference;
                                    $name = $decode->products[$prod]->name;
                                    $price = sprintf('%.2f', $decode->products[$prod]->price);
                                    $pricettc = sprintf('%.2f', $price + ($price * 0.2));
                                    $description = $decode->products[$prod]->description;
                                    $id_image = $decode->products[$prod]->id_default_image;
                                    $image = "https://$url/api/images/products/$id/$id_image&ws_key=$api";

                                    //Interrogation catégorie
                                    $category = $decode->products[$prod]->id_category_default;
                                    $url_category = "https://$url/api/categories/$category&language=$lang_id&ws_key=$api&output_format=JSON";
                                    $get_category = file_get_contents($url_category);
                                    $decode_category = json_decode($get_category);

                                    $categorie = $decode_category->category->name;

                                    $statut = $decode->products[$prod]->active;

                                    if ($statut == 1) {
                                        $state = "Publié";
                                    } else {
                                        $state = "Non publié";
                                    }

                                    $date_add = $decode->products[$prod]->date_add;
                                    $date_add = strtotime($date_add);
                                    $date_add = date("d/m/Y H:i", $date_add);

                                    $date_upd = $decode->products[$prod]->date_upd;
                                    $date_upd = strtotime($date_upd);
                                    $date_upd = date("d/m/Y H:i", $date_upd);
                                    ?>
                                    <tbody>
                                        <tr>
                                            <td scope="row"><a href="<?php echo $image ?>" target="_blank"
                                                    rel="noopener noreferrer"><img src="<?php echo $image; ?>"
                                                        alt="<?php echo $name; ?>" style="width: 50px;"></a></td>
                                            <td>
                                                <?php echo $id; ?>
                                            </td>
                                            <td>
                                                <?php echo $reference; ?>
                                            </td>
                                            <td>
                                                <?php echo $name; ?>
                                            </td>
                                            <td>
                                                <?php echo $categorie; ?>
                                            </td>
                                            <td>
                                                <?php echo $price; ?> &euro;
                                            </td>
                                            <td>
                                                <?php echo $pricettc; ?> &euro;
                                            </td>
                                            <td>
                                                <?php echo $state; ?>
                                            </td>
                                            <td>
                                                <?php echo $date_add; ?>
                                            </td>
                                            <td>
                                                <?php echo $date_upd; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <?php
                                    $prod++;
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-item quantity-item">
                    <h2 class="accordion-header" id="heading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2"
                            onclick="loadData(2)">
                            Quantité en stock
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2"
                        data-bs-parent="#accordionProduct">
                        <div class="accordion-body quantity-body">
                            <?php
                            //Interrogation de produit
                            
                            $nb_prod = sizeof($decode->products);
                            $nbr_prod = $nb_prod - 1;
                            $prod = 0;

                            while ($prod <= $nbr_prod) {
                                $check_comb = $decode->products[$prod]->id_default_combination;

                                $nb_stock = sizeof($decode->products[$prod]->associations->stock_availables);
                                $nbr_stock = $nb_stock - 1;
                                $stk = 0;
                                ?>
                                <table class="table table-striped table-hover">
                                    <thead class="thead-inverse">
                                        <tr class="head-table-color">
                                            <th scope="col">ID Stock</th>
                                            <th scope="col">ID Produit</th>
                                            <th scope="col">Quantité</th>
                                            <th scope="col">Type de produit</th>
                                            <th scope="col">Attribution</th>
                                            <th scope="col">ID Shop</th>
                                            <th scope="col">ID Group Shop</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    while ($stk <= $nbr_stock) {
                                        $id_stock = $decode->products[$prod]->associations->stock_availables[$stk]->id;
                                        $url_stock = "https://$url/api/stock_availables/$id_stock&ws_key=$api&output_format=JSON";
                                        $get_stock = file_get_contents($url_stock);
                                        $decode_stock = json_decode($get_stock);

                                        $id = $decode_stock->stock_available->id;
                                        $id_product = $decode_stock->stock_available->id_product;
                                        $quantity = $decode_stock->stock_available->quantity;
                                        $type = $decode_stock->stock_available->id_product_attribute;
                                        $id_attr = $decode_stock->stock_available->id_product_attribute;

                                        if ($check_comb != 0) {
                                            if ($type == 0) {
                                                $type = "Produit Variable : Parent";
                                            } else {
                                                $type = "Produit Variable : Déclinaison";
                                            }
                                            if ($id_attr == 0) {
                                                $attr = "Totalité du produit";
                                            } else {
                                                $attr = "Stock déclinaison ID : " . $id_attr;
                                            }
                                        } else {
                                            $type = "Produit simple";

                                            $attr = "Stock simple";
                                        }
                                        $id_shop = $decode_stock->stock_available->id_shop;
                                        $id_shop_group = $decode_stock->stock_available->id_shop_group;
                                        ?>
                                        <tbody>
                                            <tr>
                                                <td scope="row">
                                                    <?php echo $id; ?>
                                                </td>
                                                <td>
                                                    <?php echo $id_product; ?>
                                                </td>
                                                <td>
                                                    <?php echo $quantity; ?>
                                                </td>
                                                <td>
                                                    <?php echo $type; ?>
                                                </td>
                                                <td>
                                                    <?php echo $attr; ?>
                                                </td>
                                                <td>
                                                    <?php echo $id_shop; ?>
                                                </td>
                                                <td>
                                                    <?php echo $id_shop_group; ?>
                                                </td>
                                            </tr>
                                        </tbody>

                                        <?php
                                        $stk++;
                                    }
                                    $prod++;
                            }
                            ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                            Déclinaison
                        </button>
                    </h2>
                    <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3"
                        data-bs-parent="#accordionProduct">
                        <div class="accordion-body">
                            <?php
                            //Interrogation de produit
                            
                            $nb_prod = sizeof($decode->products);
                            $nbr_prod = $nb_prod - 1;
                            $prod = 0;

                            while ($prod <= $nbr_prod) {
                                $check_comb = $decode->products[$prod]->id_default_combination;
                                if ($check_comb != 0) {

                                    $nb_comb = sizeof($decode->products[$prod]->associations->combinations);
                                    $nbr_comb = $nb_comb - 1;
                                    $cmb = 0;
                                    ?>
                                    <table class="table table-striped table-hover">
                                        <thead class="thead-inverse">
                                            <tr class="head-table-color">
                                                <th scope="col">ID</th>
                                                <th scope="col">ID Parent</th>
                                                <th scope="col">Référence</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Prix</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        while ($cmb <= $nbr_comb) {
                                            $id_comb = $decode->products[$prod]->associations->combinations[$cmb]->id;
                                            $url_comb = "https://$url/api/combinations/$id_comb&language=$lang_id&ws_key=$api&output_format=JSON";
                                            $get_comb = file_get_contents($url_comb);
                                            $decode_comb = json_decode($get_comb);

                                            $id = $decode_comb->combination->id;
                                            $id_parent = $decode_comb->combination->id_product;
                                            $reference = $decode_comb->combination->reference;
                                            $price_comb = sprintf('%.2f', $decode_comb->combination->price);

                                            $nb_opv = sizeof($decode_comb->combination->associations->product_option_values);
                                            $nbr_opv = $nb_opv - 1;
                                            $opv = 0;

                                            ?>
                                            <tbody>
                                                <tr>
                                                    <td scope="row">
                                                        <?php echo $id; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $id_parent; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $reference; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        while ($opv <= $nbr_opv) {
                                                            $id_opv = $decode_comb->combination->associations->product_option_values[$opv]->id;

                                                            $url_opv = "https://$url/api/product_option_values/$id_opv&language=$lang_id&ws_key=$api&output_format=JSON";
                                                            $get_opv = file_get_contents($url_opv);
                                                            $decode_opv = json_decode($get_opv);
                                                            $name_opv = $decode_opv->product_option_value->name;
                                                            $id_op = $decode_opv->product_option_value->id_attribute_group;

                                                            $url_op = "https://$url/api/product_options/$id_op&language=$lang_id&ws_key=$api&output_format=JSON";
                                                            $get_op = file_get_contents($url_op);
                                                            $decode_op = json_decode($get_op);
                                                            $name_op = $decode_op->product_option->name;

                                                            echo $name_op . " : " . $name_opv . "<br>";
                                                            $opv++;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $price_comb; ?> &euro;
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <?php
                                            $cmb++;
                                        }

                                        ?>
                                    </table>
                                    <?php
                                } else {
                                    ?>
                                    <strong>Ce produit est un produit simple qui ne contient pas de déclinaison.</strong>
                                    <?php
                                }
                                $prod++;
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>