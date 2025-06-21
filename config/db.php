<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . __DIR__ . '/../db/ws.sqlite', // Chemin vers ton fichier SQLite
    'charset' => 'utf8',

    // Si nécessaire, tu peux activer le cache de schéma pour améliorer la performance
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];


// return [
//     'class' => 'yii\db\Connection',
//     'dsn' => 'mysql:host=brjozkbwdmpn8ypzkh3b-mysql.services.clever-cloud.com;dbname=brjozkbwdmpn8ypzkh3b',
//     'username' => 'uqsvll7kfttc3d7z',
//     'password' => '6I1TDiKvJZQspDosvUOH',
//     'charset' => 'utf8mb4',

//     // Si nécessaire, tu peux activer le cache de schéma pour améliorer la performance
//     //'enableSchemaCache' => true,
//     //'schemaCacheDuration' => 60,
//     //'schemaCache' => 'cache',
// ];