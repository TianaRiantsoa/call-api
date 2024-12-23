<?php
// Redirection vers le dossier 'web' pour accéder à 'index.php'
header('Location: web/index.php');
exit(); // Assurez-vous d'utiliser exit() après une redirection pour arrêter l'exécution du script.
?>