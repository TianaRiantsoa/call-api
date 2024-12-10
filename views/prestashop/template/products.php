<?php
$url = $_GET['url'];
$ref = $_GET['ref'];
$host = 'localhost';
$dbname = 'webservices';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$dbname";

$sql = "SELECT * FROM prestashop WHERE url LIKE '%$url%'";

// SELECT url,configuration FROM `configurations` WHERE url LIKE '%test%' ORDER BY `date` DESC LIMIT 1

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    echo $e->getMessage();
}
$stmt = $pdo->query($sql);

if ($stmt === false) {
    die("Erreur");
}

//récupération 
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $api = $row['api'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/sidebars.css">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/jquery/dist/jquery.min.js"></script>

    <script>
        $(function() {
            $("#includenav").load("content/navbar.html");
        });
        $(function() {
            $("#includesvg").load("content/svg.html");
        });
        $(function() {
            $("#includesidebar").load("content/sidebar.html");
        });
    </script>
    <style>
        body {
            background-color: #f6f4f0;
            min-height: 75rem;
            padding-top: 6rem;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        nav.navbar {
            background-color: #5c5c5c;
        }

        div.menu-side {
            background-color: #5c5c5c;
        }

        thead {
            color: #5c5c5c !important;
            background-color: #f1ac16 !important;
        }
    </style>

</head>

<body>
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;" id="includesvg"></svg>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" aria-label="Offcanvas navbar large" id="includenav"></nav>

    <main class="d-flex flex-nowrap">
        <!-- <div id="includesidebar"></div> -->

        <div class="container-fluid" style="position: absolute;">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            Détail du produit
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table class="table align-middle table-striped table-hover">
                                <thead class="thead-inverse">
                                    <tr>
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
                                $req_prod = "https://$url/api/products/?filter[reference]=$ref&display=full&language=1&ws_key=$api&output_format=JSON";
                                $get_product = file_get_contents($req_prod);
                                $decode = json_decode($get_product);

                                echo $req_prod;

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
                                    $url_category = "https://$url/api/categories/$category&language=1&ws_key=$api&output_format=JSON";
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
                                            <td scope="row"><a href="<?php echo $image ?>" target="_blank" rel="noopener noreferrer"><img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" style="width: 50px;"></a></td>
                                            <td><?php echo $id; ?></td>
                                            <td><?php echo $reference; ?></td>
                                            <td><?php echo $name; ?></td>
                                            <td><?php echo $categorie; ?></td>
                                            <td><?php echo $price; ?> &euro;</td>
                                            <td><?php echo $pricettc; ?> &euro;</td>
                                            <td><?php echo $state; ?></td>
                                            <td><?php echo $date_add; ?></td>
                                            <td><?php echo $date_upd; ?></td>
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
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                            Quantité en stock
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
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
                                <table class="table align-middle table-striped table-hover">
                                    <thead class="thead-inverse">
                                        <tr>
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
                                                <td scope="row"><?php echo $id; ?></td>
                                                <td><?php echo $id_product; ?></td>
                                                <td><?php echo $quantity; ?></td>
                                                <td><?php echo $type; ?></td>
                                                <td><?php echo $attr; ?></td>
                                                <td><?php echo $id_shop; ?></td>
                                                <td><?php echo $id_shop_group; ?></td>
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
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                            Déclinaison
                        </button>
                    </h2>
                    <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#accordionExample">
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
                                    <table class="table align-middle table-striped table-hover">
                                        <thead class="thead-inverse">
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">ID Parent</th>
                                                <th scope="col">Référence</th>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Prix</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        while ($cmb <= $nbr_comb) {
                                            $id_comb = $decode->products[$prod]->associations->combinations[$cmb]->id;
                                            $url_comb = "https://$url/api/combinations/$id_comb&language=1&ws_key=$api&output_format=JSON";
                                            $get_comb = file_get_contents($url_comb);
                                            $decode_comb = json_decode($get_comb);

                                            $id = $decode_comb->combination->id;
                                            $id_parent = $decode_comb->combination->id_product;
                                            $reference = $decode_comb->combination->reference;
                                            $price_comb = sprintf('%.2f', $decode_comb->combination->price);
                                        ?>
                                            <tbody>
                                                <tr>
                                                    <td scope="row"><?php echo $id; ?></td>
                                                    <td><?php echo $id_parent; ?></td>
                                                    <td><?php echo $reference; ?></td>
                                                    <td><?php echo $url_comb; ?></td>
                                                    <td><?php echo $price_comb; ?> &euro;</td>
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
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                            Grille de Prix
                        </button>
                    </h2>
                    <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <?php
                            //Interrogation de produit
                            $nb_prod = sizeof($decode->products);
                            $nbr_prod = $nb_prod - 1;
                            $prod = 0;

                            while ($prod <= $nbr_prod) {
                                // ini_set('display_errors', 0);
                                $id_product = $decode->products[$prod]->id;
                                $req_grille = "https://$url/api/specific_prices/&filter[id_product]=$id_product&display=full&sort=id_asc&ws_key=$api&output_format=JSON";
                                $get_grille = file_get_contents($req_grille);
                                $decode_grille = json_decode($get_grille);

                                $nb_grille = sizeof($decode_grille->specific_prices);
                                $nbr_grille = $nb_grille - 1;
                                $grille = 0;
                            ?>
                                <table class="table align-middle table-striped table-hover">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">ID Produit</th>
                                            <th scope="col">ID Groupe</th>
                                            <th scope="col">ID Client</th>
                                            <th scope="col">Adresse mail</th>
                                            <th scope="col">Prix original</th>
                                            <th scope="col">Prix remisé</th>
                                            <th scope="col">Réduction</th>
                                            <th scope="col">Type de réduction</th>
                                            <th scope="col">De</th>
                                            <th scope="col">&Agrave;</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    while ($grille <= $nbr_grille) {

                                        $id = $decode_grille->specific_prices[$grille]->id;
                                        $id_product = $decode_grille->specific_prices[$grille]->id_product;
                                        $id_customer = $decode_grille->specific_prices[$grille]->id_customer;
                                        $id_group = $decode_grille->specific_prices[$grille]->id_group;
                                        $url_group = "https://$url/api/groups/$id_group&language=1&ws_key=$api&output_format=JSON";
                                        $original_price = sprintf('%.2f', $decode->products[$prod]->price);

                                        $reduction_type = $decode_grille->specific_prices[$grille]->reduction_type;

                                        //Interrogation customer
                                        if ($id_customer != 0) {
                                            $url_customer = "https://$url/api/customers/$id_customer&ws_key=$api&output_format=JSON";
                                            $get_customer = file_get_contents($url_customer);
                                            $decode_customer = json_decode($get_customer);

                                            $email = $decode_customer->customer->email;
                                            $id_group = "-";
                                            $group_name = "-";
                                        } else {
                                            $id_customer = "-";
                                            $email = "-";

                                            $get_group = file_get_contents($url_group);
                                            $decode_group = json_decode($get_group);
                                            $group_name = $decode_group->group->name;
                                        }

                                        if ($reduction_type == "percentage") {
                                            $calc_reduc = $original_price - ($original_price * $decode_grille->specific_prices[$grille]->reduction);
                                            $percent = $decode_grille->specific_prices[$grille]->reduction * 100;
                                            $final_price = sprintf('%.2f', $calc_reduc);
                                            $reduction_type = "Pourcentage ($percent %)";
                                            $reduction = $original_price - $final_price . " &euro;";
                                        } elseif ($reduction_type == "amount") {
                                            $reduction_type = "Montant";
                                            $final_price = sprintf('%.2f', $decode_grille->specific_prices[$grille]->price);
                                            $calc_reduc = $original_price - $final_price;
                                            $reduction = sprintf('%.2f', $calc_reduc) . " &euro;";
                                        }

                                        $from = $decode_grille->specific_prices[$grille]->from;
                                        $to = $decode_grille->specific_prices[$grille]->to;
                                    ?>
                                        <tbody>
                                            <tr>
                                                <td scope="row"><?php echo $id; ?></td>
                                                <td><?php echo $id_product; ?></td>
                                                <td><a href="<?php echo $url_group; ?>" class="link-secondary" target="_blank" rel="noopener noreferrer"><?php echo $id_group; ?> (<?php echo $group_name; ?>)</a></td>
                                                <td><?php echo $id_customer; ?></td>
                                                <td><?php echo $email; ?></td>
                                                <td><?php echo $original_price; ?> &euro;</td>
                                                <td><?php echo $final_price; ?> &euro;</td>
                                                <td><?php echo $reduction; ?></td>
                                                <td><?php echo $reduction_type; ?></td>
                                                <td><?php echo $from; ?></td>
                                                <td><?php echo $to; ?></td>
                                            </tr>
                                        </tbody>
                                    <?php
                                        $grille++;
                                    }

                                    ?>
                                </table>
                            <?php
                                $prod++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../assets/js/sidebars.js"></script>
</body>

</html>