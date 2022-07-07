<?php

$dbh = new PDO('mysql:host=localhost; dbname=laravel; charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$sql = $dbh->query("SELECT * FROM tables");

$data = $sql->fetchAll();

//$sql = null;
//$dbh = null;

$daily = [];
$weekly = [];
$monthly = [];
$quarterly = [];

foreach ($data as $key => $table) {
   if ($table['periodicity'] == 1) {
      $daily[$key] = $table;
      $daily[$key]['report_table'] = 'daily_reports';
   } elseif ($table['periodicity'] == 2) {
      $weekly[$key] = $table;
      $weekly[$key]['report_table'] = 'weekly_reports';
   } elseif ($table['periodicity'] == 3) {
      $monthly[$key] = $table;
      $monthly[$key]['report_table'] = 'monthly_reports';
   } elseif ($table['periodicity'] == 4) {
      $quarterly[$key] = $table;
      $quarterly[$key]['report_table'] = 'quarterly_reports';
   }
}

function reports($periodicity, $dbh) {
   date_default_timezone_set('Europe/Moscow');
   $target_date = date('Y-m-d');
   $isset_reports = [];
   $reports = [];
   foreach ($periodicity as $period) {
      foreach (json_decode($period['departments'], true) as $department) {
         $reports = $dbh->query("SELECT * FROM " . $period['report_table'] . " WHERE user_dep = '$department' AND created_at > '$target_date' AND table_uuid = `{$period['table_uuid']}`")->fetchAll();
         var_dump($reports);
      }
   }
   $count = 0;

}

reports($daily, $dbh);
