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
   foreach ($periodicity as $period) {
      $uuid = $period['table_uuid'];
      foreach (json_decode($period['departments'], true) as $department) {
         try {
            $reports = $dbh->query("SELECT * FROM " . $period['report_table'] . " WHERE user_dep = '$department' AND created_at > '$target_date' AND table_uuid = '$uuid'")->fetchAll();
            if (empty($reports)) {
               var_dump($reports);
            }
         } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
         }
      }
   }
}

reports($daily, $dbh);
