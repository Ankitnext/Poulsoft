<?php
//broiler_save_batch.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['batch'];

$description = $_POST['batch'];
$bnos = explode("-",$_POST['batch']);
$bsize = sizeof($bnos);
$bsize = $bsize - 1;
$batch_no = $bnos[$bsize];
$farm_code = $_POST['farm_code'];
$book_num = $_POST['book_num'];

$sql ="SELECT MAX(incr) as incr FROM `broiler_batch`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "BCH";

if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Create Batch Master' AND `field_function` LIKE 'Batch Authorization Flag' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn, $sql); $bacount = mysqli_num_rows($query);
if($bacount > 0){ $active = 0; } else { $active = 1; }



$sql = "INSERT INTO `broiler_batch` (incr,prefix,code,description,farm_code,batch_no,book_num,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$description','$farm_code','$batch_no','$book_num','0','$active','0','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
else {
    if($bacount > 0){
        /* Batch Push Notification flag check*/
        $sql3 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Push Notifications' AND (`user_access` LIKE '%$addedemp%' OR `user_access` = 'all')";
        $query3 = mysqli_query($conn, $sql3);
        $ccount3 = mysqli_num_rows($query3);
        if($ccount3 > 0){
            while ($row3 = mysqli_fetch_assoc($query3)) {
                $push_noti_flag = $row3['flag'];
            }
        }
        else{
            mysqli_query($conn, "INSERT INTO `extra_access` ( `field_name`, `field_function`, `user_access`, `flag`) VALUES ( 'Push Notifications', 'Sending Notification to mobile', 'all', '0')");
            $push_noti_flag =  0;
        }
        if($push_noti_flag == ''){
            $push_noti_flag =  0;
        }
        
        if($push_noti_flag > 0){
            include "broiler_push_notification.php";
            $count = 0;
            $sql2 = "SELECT branch_code,farm_code,description FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0'";
            $query2 = mysqli_query($conn, $sql2);
            $count = mysqli_num_rows($query2);
            if ($count > 0) {
                while($row2 = mysqli_fetch_assoc($query2)){
                    $all_farm_branch[$row2['farm_code']] = $row2['branch_code'];
                }
            }
            $sql2 = "SELECT * FROM `broiler_farm` WHERE `code` = '$farm_code' AND `active` = '1' AND `dflag` = '0'";
            $query2 = mysqli_query($conn, $sql2);
            while($row2 = mysqli_fetch_assoc($query2)){
                $farm_name = $row2['description'];
            }
            $count = 0;
            $sql = "SELECT db_emp_code,empcode FROM `main_access`";
            $q3 = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($q3);
            
            if($count > 0){
                while($row = mysqli_fetch_assoc($q3)){
                    $all_db_emp_code[$row['empcode']] = $row['db_emp_code'];
                }
            }
            $count = 0;
            $sql2 = "SELECT empcode FROM `notification_master` WHERE transction = 'batch_auth' AND (branch = '$all_farm_branch[$farm_code]' OR branch = 'all')";
            $query2 = mysqli_query($conn, $sql2);
            $count = mysqli_num_rows($query2);
            if ($count > 0) {
                $row2 = mysqli_fetch_assoc($query2);
                $string = $row2['empcode'];
                $i = 0;
                foreach(explode(',', $string) as $li){
                    if($i == 0){
                        $manger_empcodes = "'".$all_db_emp_code[$li]."'";
                    }
                    else{
                        $manger_empcodes .= ",'".$all_db_emp_code[$li]."'";
                    }
                    $i++;
                }
            }
    
            $count = 0;
            if($manger_empcodes != ''){
                $sql = "SELECT * FROM `firebase_device_details` where db = '$dbname' and emp_code IN ($manger_empcodes)";
                $q3 = mysqli_query($conns, $sql);
                $count = mysqli_num_rows($q3);
                if ($count > 0) {
                    while ($row = mysqli_fetch_assoc($q3)) {
                        $all_firebase_tokens[$row['emp_code']] = $row['device_token'];
                        $all_firebase_empcodes[] = $row['emp_code'];
                    }
                }
            }
    
            $title = "Batch Authorization request";
            $body = "Greeings!! Farm Name: $farm_name,Batch: $description has been created. Kindly authorize the batch for CHICKS & FEED PURCHASE";
    
            $today = date("Y-m-d");
            mysqli_query($conn, "INSERT INTO `notification_details`( `date`, `trnum`, `page`, `title`, `body`, `message`, `auth_flag`)
             VALUES ('$today','$code','batch_auth','$title','$body','$message1','1')");
                
            if ($all_firebase_empcodes != null) {
                foreach ($all_db_emp_code as $emp) {
                    send_notification($title, $body, $all_firebase_tokens[$emp]);
                }
            }
        }
    }
    header('location:broiler_display_batch.php?ccid='.$ccid);
}

?>