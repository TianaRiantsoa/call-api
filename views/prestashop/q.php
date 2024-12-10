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