<?php
//broiler_save_batchmultiple.php
session_start(); include "newConfig.php";
include "broiler_push_notification.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['batchmultiple'];

//Check Nisan APIs Flag
$psn_count = $psnc_count = $push_psn_flag = 0;
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Nisan Push Batch Master' AND `field_function` = 'Auto:Send Details via API' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $psn_count = mysqli_num_rows($query);
if($psn_count > 0){
    $sql = "SELECT * FROM `broiler_nisan_credentials` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $psnc_count = mysqli_num_rows($query);
    if($psnc_count > 0){ $push_psn_flag = 1; while($row = mysqli_fetch_array($query)){ $psn_version = $row['version']; $psn_company_code = $row['company_code']; $psn_password = $row['password']; } }
}
$description = $batch_no = $farm_code = $book_num = $farm_ccode = $farm_line = $line_name = array();
$i = 0; foreach($_POST['batch'] as $batch){
    $description[$i] = $batch;
    $bnos = explode("-",$batch);
    $bsize = sizeof($bnos);
    $bsize = $bsize - 1;
    $batch_no[$i] = (int)$bnos[$bsize];
    $i++;
}
$i = 0; foreach($_POST['farm_name'] as $fcode){ $farm_code[$i] = $fcode; $i++; }
$i = 0; foreach($_POST['book_num'] as $book_nums){ $book_num[$i] = $book_nums; $i++; }

$flist = implode("','", $farm_code);
$sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$flist')"; $query = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($query)){ $farm_ccode[$row['code']] = $row['farm_code']; $farm_line[$row['code']] = $row['line_code']; }

$llist = implode("','", $farm_line);
$sql = "SELECT * FROM `location_line` WHERE `code` IN ('$llist')"; $query = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($query)){ $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Create Batch Master' AND `field_function` LIKE 'Batch Authorization Flag' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn, $sql); $bacount = mysqli_num_rows($query);
if($bacount > 0){ $active = 0; } else { $active = 1; }

$dsize = sizeof($farm_code);
for ($i = 0; $i < $dsize;$i++){
    $sql ="SELECT MAX(incr) as incr FROM `broiler_batch`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
    $prefix = "BCH";

    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
    $code = $prefix."-".$incr;

    $sql = "INSERT INTO `broiler_batch` (incr,prefix,code,description,farm_code,batch_no,book_num,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$description[$i]','$farm_code[$i]','$batch_no[$i]','$book_num[$i]','0','$active','0','$addedemp','$addedtime','$addedtime')";
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
                $count = 0;
                $sql2 = "SELECT branch_code,farm_code,description FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0'";
                $query2 = mysqli_query($conn, $sql2);
                $count = mysqli_num_rows($query2);
                if ($count > 0) {
                    while($row2 = mysqli_fetch_assoc($query2)){
                        $all_farm_branch[$row2['farm_code']] = $row2['branch_code'];
                    }
                }
                $sql2 = "SELECT * FROM `broiler_farm` WHERE `code` = '$farm_code[$i]' AND `active` = '1' AND `dflag` = '0'";
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
                $count = 0; $bch_code = $all_farm_branch[$farm_code[$i]];
                $sql2 = "SELECT empcode FROM `notification_master` WHERE transction = 'batch_auth' AND (branch = '$bch_code' OR branch = 'all')";
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
                $body = "Greeings!! Farm Name: $farm_name,Batch: $description[$i] has been created. Kindly authorize the batch for CHICKS & FEED PURCHASE";
        
                $today = date("Y-m-d");
                mysqli_query($conn, "INSERT INTO `notification_details`( `date`, `trnum`, `page`, `title`, `body`, `message`, `auth_flag`)
                 VALUES ('$today','$description[$i]','batch_auth','$title','$body','$message1','1')");
                    
                if ($all_firebase_empcodes != null) {
                    foreach ($all_db_emp_code as $emp) {
                        send_notification($title, $body, $all_firebase_tokens[$emp]);
                    }
                }
            }
        }
        if($push_psn_flag == 1){
            $f1 = $farm_ccode[$farm_code[$i]];
            $l1 = $farm_line[$farm_code[$i]]; $l2 = $line_name[$l1];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'nisanapps.com/npickweb/nxinfoservice.asmx?wsdl=null',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                <soapenv:Header/>
                <soapenv:Body>
                    <tem:pushFlockMaster>
                        <tem:version>'.$psn_version.'</tem:version>
                        <tem:password>'.$psn_password.'</tem:password>
                        <tem:finfo>
                            <tem:FlockMaster>
                            <tem:companycode>'.$psn_company_code.'</tem:companycode>
                            <tem:isv_farmercode>'.$f1.'</tem:isv_farmercode>
                            <tem:isv_flockno>'.$description[$i].'</tem:isv_flockno>
                            <tem:isv_sitecode>'.$l2.'</tem:isv_sitecode>
                            <tem:status>A</tem:status>
                            <tem:ReturnCode>1</tem:ReturnCode>
                            <tem:ReturnMessage>S</tem:ReturnMessage>
                            </tem:FlockMaster>
                        </tem:finfo>
                    </tem:pushFlockMaster>
                </soapenv:Body>
                </soapenv:Envelope>',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: text/xml',
                    'charset: utf-8'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $xml = file_get_contents($response);
            $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = simplexml_load_string($xml);
            $json = json_encode($xml);
            $responseArray = json_decode($json,true);

            $slno = 0;
            foreach($responseArray as $r1){
                foreach($r1 as $r2){
                    foreach($r2 as $r3){
                        foreach($r3 as $r4){
                            $status = $r4['ReturnMessage'];
                            $sql2 = "INSERT INTO `broiler_nisan_pushdetail_status` (`type`,`code`,`name`,`status`) VALUES ('Create Batch','$code','$description[$i]','$status')";
                            mysqli_query($conn,$sql2);
                        }
                    }
                }
            }
        }
    }
}
//if($push_psn_flag != 1){
    header('location:broiler_display_batchmultiple.php?ccid='.$ccid);
//}
?>