<?php

// Configuration de la connexion à la base de données SQLite pour l'application Yii2
// Assure-toi que le fichier SQLite existe à l'emplacement spécifié
// Si tu souhaites utiliser une base de données SQLite, tu peux utiliser le code suivant :
// Base de données SQLite pour l'application Yii2

 return [
     'class' => 'yii\db\Connection',
     'dsn' => 'sqlite:' . __DIR__ . '/../db/ws.sqlite', // Chemin vers ton fichier SQLite
     'charset' => 'utf8',

//     // Si nécessaire, tu peux activer le cache de schéma pour améliorer la performance
//     //'enableSchemaCache' => true,
//     //'schemaCacheDuration' => 60,
//     //'schemaCache' => 'cache',
 ];
