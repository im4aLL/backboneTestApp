<?php
$seed = [
    ['id' => uniqid(), 'taskname' => 'Go to Shooping', 'date' => '15/01/2016'],
    ['id' => uniqid(), 'taskname' => 'Buy daily needs', 'date' => '15/02/2016'],
    ['id' => uniqid(), 'taskname' => 'Hair cut', 'date' => '15/03/2016'],
];

require_once __DIR__.'/class.localdb.php';
$db = new LocalDatabase('db/');
$table = 'todo';

$db->saveArray($table, $seed);
echo 'DB seed succesful!';
