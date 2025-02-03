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
