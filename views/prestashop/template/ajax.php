<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>

    <div class="accordion" id="accordionExample">
        <!-- Accordéon 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Accordéon 1
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body" id="contentAccordion1">
                    <!-- Contenu de l'inclusion PHP ici -->
                </div>
            </div>
        </div>

        <!-- Accordéon 2 -->
        <!-- Répétez la structure pour les autres accordéons -->

    </div>

    <script src="path/to/bootstrap.bundle.js"></script>
    <script src="path/to/jquery.js"></script>
    <script>
        $(document).ready(function () {
            // Écouteur d'événement pour chaque bouton d'accordéon
            $('.accordion-button').click(function () {
                // Récupérer l'ID de l'accordéon sélectionné
                var accordionID = $(this).attr('data-bs-target');

                // Récupérer le contenu actuel de l'accordéon
                var accordionContent = $(accordionID).find('.accordion-body');

                // Vérifier si le contenu a déjà été chargé
                if (!accordionContent.hasClass('loaded')) {
                    // Charger le contenu via AJAX
                    $.ajax({
                        url: 'charger_contenu.php', // Remplacez par le chemin de votre script PHP
                        type: 'POST',
                        data: {accordionID: accordionID},
                        success: function (data) {
                            // Mettre à jour le contenu de l'accordéon
                            accordionContent.html(data);
                            // Ajouter une classe pour indiquer que le contenu a été chargé
                            accordionContent.addClass('loaded');
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>
