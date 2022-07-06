<?php

$dbh = new PDO('mysql:host=localhost; dbname=laravel; charset=utf8mb4', 'root', '');


$sql = $dbh->query("SELECT * FROM tables");

$data = $sql->fetchAll();

var_dump($data);