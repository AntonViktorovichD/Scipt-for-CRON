<?php

$dbh = new PDO('mysql:host=localhost; dbname=laravel; charset=utf8mb4', 'root', '');

$sql = $dbh->query("SELECT * FROM tables");

$data = $sql->fetchAll();

//$sql = null;
//$dbh = null;

$daily = [];
$weekly = [];
$monthly = [];
$quarterly = [];

foreach ($data as $table) {
   if ($table['periodicity'] == 1) {
      $daily[] = $table;
      $daily['report_table'] = 'daily_report';
   } elseif ($table['periodicity'] == 2) {
      $weekly[] = $table;
      $weekly['report_table'] = 'weekly_report';
   } elseif ($table['periodicity'] == 3) {
      $monthly[] = $table;
      $monthly['report_table'] = 'monthly_report';
   } elseif ($table['periodicity'] == 4) {
      $quarterly[] = $table;
      $quarterly['report_table'] = 'quarterly_report';
   }
}

function reports($periodicity) {
   var_dump($periodicity);
}

reports($daily);