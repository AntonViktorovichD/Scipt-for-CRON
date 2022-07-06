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
   $target_date = date('Y-m-d, H:i:s');
   foreach ($periodicity as $period) {
      $periodicity_report = $dbh->query("SELECT * FROM " . $period['report_table'] . "")->fetchAll();
      foreach ($periodicity_report as $reports) {
         if ($reports['table_uuid'] == $period['table_uuid']) {
            foreach(json_decode($period['departments']) as $department) {
               if($department == $reports['user_dep']) {
                  var_dump($reports);

               }
            }
         } else {
            $period['table_uuid'];
         }
      }
   }
}

reports($daily, $dbh);

