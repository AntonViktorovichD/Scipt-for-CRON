<?php

$dbh = new PDO('mysql:host=localhost; dbname=laravel; charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$data = $dbh->query("SELECT * FROM tables")->fetchAll();


function launching_script($data, $dbh) {
   $target_date = date('Y-m-d');

   foreach ($data as $key => $table) {
      if ($table['periodicity'] == 1) {
         $daily[$key] = $table;
         $daily[$key]['report_table'] = 'daily_reports';
         $serially = 1;
      } elseif ($table['periodicity'] == 2) {
         $weekly[$key] = $table;
         $weekly[$key]['report_table'] = 'weekly_reports';
         $serially = 2;
      } elseif ($table['periodicity'] == 3) {
         $monthly[$key] = $table;
         $monthly[$key]['report_table'] = 'monthly_reports';
         $serially = 3;
      } elseif ($table['periodicity'] == 4) {
         $quarterly[$key] = $table;
         $quarterly[$key]['report_table'] = 'quarterly_reports';
         $serially = 4;
      }
   }

   reports($daily, $dbh, $target_date, $serially);

   if (date("w", mktime(0, 0, 0, date("m"), date("d"), date("Y"))) == 1) {
      reports($weekly, $dbh, $target_date, $serially);

   }
   if (date('j') == 1) {
      reports($monthly, $dbh, $target_date, $serially);

   }
   if (date('n') % 3 == 0) {
      reports($quarterly, $dbh, $target_date, $serially);
   }
}

function reports($periodicity, $dbh, $target_date, $serially) {
   date_default_timezone_set('Europe/Moscow');
   $clear_reports = [];
   $counter = 0;
   foreach ($periodicity as $period) {
      $uuid = $period['table_uuid'];
      foreach (json_decode($period['departments'], true) as $department) {
         try {
            $reports = $dbh->query("SELECT * FROM " . $period['report_table'] . " WHERE user_dep = '$department' AND created_at > '$target_date' AND table_uuid = '$uuid'")->fetchAll();
            if (empty($reports)) {
               $counter++;
               $clear_reports[$counter]['report_table'] = $period['report_table'];
               $clear_reports[$counter]['table_name'] = $period['table_name'];
               $clear_reports[$counter]['table_uuid'] = $period['table_uuid'];
               $clear_reports[$counter]['row_uuid'] = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
               $clear_reports[$counter]['department'] = $department;
               $users = $dbh->query("SELECT * FROM users WHERE department = '$department'")->fetchAll();
               foreach ($users as $user) {
                  $clear_reports[$counter]['user_id'] = $user['id'];
                  $clear_reports[$counter]['created_at'] = date('Y-m-d, H:i:s');
               }
               if ($serially == 3) {
                  $clear_reports[$counter]['month'] = date('n');
                  $clear_reports[$counter]['year'] = date('Y');
               }
               if ($serially == 4) {
                  $clear_reports[$counter]['quarter'] = floor(date('n') / 3);
                  $clear_reports[$counter]['year'] = date('Y');
               }
            }
         } catch (Exception $e) {
            echo 'Выброшено исключение: ', $e->getMessage(), "\n";
         }
      }
   }
   foreach ($clear_reports as $clear_report) {
      $table_name = $clear_report['report_table'];
      $report_table_name = $clear_report['table_name'];
      $table_uuid = $clear_report['table_uuid'];
      $row_uuid = $clear_report['row_uuid'];
      $user_id = $clear_report['user_id'];
      $department = $clear_report['department'];
      $created_at = $clear_report['created_at'];
      if ($serially == 1 || $serially == 2) {
         $dbh->exec("insert into `$table_name` (table_name, table_uuid, row_uuid, user_id, user_dep, created_at) values ('$report_table_name', '$table_uuid', '$row_uuid', '$user_id', '$department', '$created_at')");
      }
      if ($serially == 3) {
         $month = $clear_reports['month'];
         $year = $clear_reports['year'];
         $dbh->exec("insert into `$table_name` (table_name, table_uuid, row_uuid, user_id, user_dep, month, year, created_at) values ('$report_table_name', '$table_uuid', '$row_uuid', '$user_id', '$department', '$month', '$year', '$created_at')");
      }
      if ($serially == 4) {
         $quarter = $clear_reports['quarter'];
         $year = $clear_reports['year'];
         $dbh->exec("insert into `$table_name` (table_name, table_uuid, row_uuid, user_id, user_dep, quarter, year, created_at) values ('$report_table_name', '$table_uuid', '$row_uuid', '$user_id', '$department', '$quarter', '$year', '$created_at')");
      }
   }
}

launching_script($data, $dbh);


