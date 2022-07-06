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
      $periodicity_report = $dbh->query("SELECT * FROM " . $period['report_table'] . "")->fetchAll();
      foreach ($periodicity_report as $reports) {
         if ($reports['table_uuid'] == $period['table_uuid']) {
            foreach (json_decode($period['departments']) as $department) {
               if ($department == $reports['user_dep']) {
                  preg_match('#(\d+)\-(\d+)\-(\d+)#', $reports['created_at'], $create_date);
                  if (strtotime($create_date[1] . '-' . $create_date[2] . '-' . $create_date[3]) < strtotime($target_date)) {
                     $table_name = $period['report_table'];
                     $report_table_name = $reports['table_name'];
                     $table_uuid = $period['table_uuid'];
                     $row_uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                     $user_id = $reports['user_id'];
                     $user_dep = $reports['user_dep'];
                     $created_at = date('Y-m-d, H:i:s');
                     //$dbh->exec("insert into `$table_name` (table_name, table_uuid, row_uuid, user_id, user_dep, created_at) values ('$report_table_name', '$table_uuid', '$row_uuid', '$user_id', '$user_dep', '$created_at')");
                  }
               }
            }
         } else {
//            $period['table_uuid'];
         }
      }
   }
}

reports($daily, $dbh);

