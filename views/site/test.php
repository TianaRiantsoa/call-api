<?php

use yii\helpers\Url;

$this->title = 'Tableau Dynamique MySQL';
?>

<!-- Bootstrap 5 CDN (si nécessaire) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container mt-4">
    <h2 class="mb-3">Tableau Dynamique MySQL avec AJAX</h2>

    <!-- Champ de recherche -->
    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Rechercher par URL...">
    </div>

    <!-- Zone pour les cases à cocher -->
    <div id="columns-toggle" class="mb-3"></div>

    <!-- Tableau Bootstrap -->
    <div class="table-responsive">
        <table id="data-table" class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr id="table-headers"></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
    // Tableau pour suivre l'état de visibilité des colonnes
    let columnVisibility = {};

    function loadData(search = "") {
        $.post("<?= Url::to(['site/get']) ?>", { search: search }, function(data) {
            let tbody = $("#data-table tbody");
            let thead = $("#table-headers");
            let checkboxes = $("#columns-toggle");

            tbody.empty();
            thead.empty();
            checkboxes.empty();

            if (data.length === 0) {
                tbody.append("<tr><td colspan='100%' class='text-center'>Aucune donnée trouvée</td></tr>");
                return;
            }

            let columns = Object.keys(data[0]);

            // Générer les en-têtes et les cases à cocher
            columns.forEach(col => {
                // Créer les en-têtes de colonne
                thead.append(`<th data-col='${col}' class="sortable">${col.charAt(0).toUpperCase() + col.slice(1)}</th>`);

                // Créer les cases à cocher pour afficher/masquer les colonnes
                checkboxes.append(`
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input col-toggle" data-col="${col}" checked>
                        <label class="form-check-label">${col}</label>
                    </div>
                `);

                // Initialiser l'état des colonnes à "visible" par défaut
                columnVisibility[col] = true;
            });

            // Générer les lignes de la table
            data.forEach(row => {
                let tr = $("<tr>");
                columns.forEach(col => {
                    tr.append(`<td data-col="${col}">${row[col]}</td>`);
                });
                tbody.append(tr);
            });

            // Afficher/masquer les colonnes selon l'état des cases à cocher
            $(".col-toggle").on("change", function() {
                let col = $(this).data("col");
                let isChecked = $(this).is(":checked");

                // Mémoriser l'état de visibilité de la colonne
                columnVisibility[col] = isChecked;

                // Appliquer l'affichage/masquage de la colonne
                $(`[data-col='${col}']`).toggle(isChecked);
            });

            // Tri des colonnes
            $(".sortable").on("click", function() {
                let table = $(this).parents("table").eq(0);
                let rows = table.find("tbody tr").toArray();
                let index = $(this).index();
                let ascending = $(this).data("asc") || false;

                rows.sort(function(a, b) {
                    let valA = $(a).children("td").eq(index).text();
                    let valB = $(b).children("td").eq(index).text();
                    return ascending ? valA.localeCompare(valB) : valB.localeCompare(valA);
                });

                $(this).data("asc", !ascending);
                table.children("tbody").empty().append(rows);
            });
        });
    }

    // Recherche dynamique
    $("#search").on("keyup", function() {
        let searchValue = $(this).val().trim();
        loadData(searchValue);
    });

    loadData(); // Chargement initial

    // Réinitialiser les états des cases à cocher et appliquer l'affichage des colonnes
    $(document).on('change', '.col-toggle', function() {
        let col = $(this).data('col');
        let isChecked = $(this).prop('checked');

        // Gérer l'affichage des colonnes selon l'état des cases
        if (isChecked) {
            $(`[data-col="${col}"]`).show();
        } else {
            $(`[data-col="${col}"]`).hide();
        }
    });

    // Appliquer l'affichage des colonnes selon les états enregistrés
    $(document).on('change', '.col-toggle', function() {
        let col = $(this).data('col');
        let isChecked = $(this).prop('checked');

        // Mettre à jour l'état de la colonne dans le tableau
        columnVisibility[col] = isChecked;

        // Appliquer l'affichage/mise en masquage
        if (isChecked) {
            $(`[data-col="${col}"]`).show();
        } else {
            $(`[data-col="${col}"]`).hide();
        }
    });
});

</script>