<?php
$_tables = [
    'host' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'name' => 'VARCHAR(32) NOT NULL UNIQUE',
        'password' => 'VARCHAR(64) NOT NULL',
        'auto' => 'VARCHAR(64) NULL', //hashed by sha256
        ],
    'game' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'name' => 'VARCHAR(128) NOT NULL UNIQUE',
        'directory' => 'VARCHAR(32) NOT NULL'
        ],
    'experiment' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'host_id' => 'INT',
        'game_id' => 'INT',
        'password' => 'VARCHAR(6)',
        'status' => 'TINYINT'
        ],
    'participant' => [
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'experiment_id' => 'INT',
        'name' => 'VARCHAR(32)',
        'last_access' => 'INT'
        ]
];

$_ddb = new DiffDB($_pdo);
foreach($_tables as $name => $structure){
    $_ddb->addTable($name, $structure);
}
$_vdb->setup($_ddb);
$_ddb->updateDB();
