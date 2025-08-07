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


// Si tu souhaites utiliser une base de données MySQL, tu peux décommenter et modifier la section suivante :$

//Base de données MySQL Clever Cloud https://console.clever-cloud.com

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

// Si tu souhaites utiliser une base de données MySQL, tu peux décommenter et modifier la section suivante :$

//Base de données MySQL Free SQL DataBase https://www.freesqldatabase.com
<<<<<<< Updated upstream
<<<<<<< Updated upstream

/**
=======
/*
>>>>>>> Stashed changes
=======
/*
>>>>>>> Stashed changes
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=sql7.freesqldatabase.com;dbname=sql7786037',
    'username' => 'sql7786037',
    'password' => 'yKiXXJtIK1',
    'charset' => 'utf8mb4',
	

    // Si nécessaire, tu peux activer le cache de schéma pour améliorer la performance
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
<<<<<<< Updated upstream
<<<<<<< Updated upstream
];

*/
=======
];*/
>>>>>>> Stashed changes
=======
];*/
>>>>>>> Stashed changes
