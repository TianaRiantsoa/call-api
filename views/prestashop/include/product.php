<table class="table table-striped table-hover">
    <thead class="thead-inverse">
        <tr class="head-table-color">
            <th scope="col">ID</th>
            <th scope="col">Nom</th>
            <th scope="col">Référence</th>
            <th scope="col">Montant HT</th>
            <th scope="col">Montant TTC</th>
            <th scope="col">Ecotax</th>
            <th scope="col">Quantité</th>
            <th scope="col">&Eacute;tat</th>
            <th scope="col">Création</th>
            <th scope="col">Mise à jour</th>
        </tr>
    </thead>
    <?php
    for ($i = 0; $i <= (sizeof($p) - 1); $i++) {
        $id = $p[$i]['id'];
        $reference = $p[$i]['reference'];
        $name = $p[$i]['name'];
        $price = sprintf('%.2f', $p[$i]['price']);
        $pttc = sprintf('%.2f', $p[$i]['pttc']);
        $ecotax = sprintf('%.2f', $p[$i]['ecotax']);

        foreach ($p[$i]['stock_available'] as $array) {
            if (is_array($array->stock_available)) {
                foreach ($array->stock_available as $stock) {
                    if ($stock->id_product_attribute == 0) {
                        $sid = $stock->id;
                        break;
                    }
                }
            } else {
                if ($array->stock_available->id_product_attribute == 0) {
                    $sid = $array->stock_available->id;
                }
            }
            $quantity = Product::StockAvailable($url, $api, $sid)['quantity'];
        }

        $statut = $p[$i]['active'];
        if ($statut == 1) {
            $state = "Publié";
        } else {
            $state = "Non publié";
        }
        $date_add = $p[$i]['date_add'];
        $date_upd = $p[$i]['date_upd'];
        ?>
        <tbody>
            <tr>
                <td scope="row"><a href="<?php echo "$url/api/products/$id&ws_key=$api" ?>" target="_blank"
                        rel="noopener noreferrer">
                        <?php echo $id; ?>
                    </a></td>
                <td>
                    <?php echo $name; ?>
                </td>
                <td>
                    <?php echo $reference; ?>
                </td>
                <td>
                    <?php echo $price; ?> &euro;
                </td>
                <td>
                    <?php echo $pttc; ?> &euro;
                </td>
                <td>
                    <?php echo $ecotax; ?> &euro;
                </td>
                <td>
                    <?php echo $quantity; ?>
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
    }
    ?>
</table>