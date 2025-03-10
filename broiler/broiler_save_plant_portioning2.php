<?php
//broiler_save_plant_portioning2.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['plant_portioning2'];
include "number_format_ind.php";

/*Check send message flag*/
$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'AutoWapp:broiler_display_plant_portioning2.php' AND `user_access` LIKE '%$addedemp%'"; $query1 = mysqli_query($conn,$sql1); $ccount1 = mysqli_num_rows($query1);
$sql2 = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'AutoWapp:broiler_display_plant_portioning2.php' AND `user_access` = 'all'"; $query2 = mysqli_query($conn,$sql2); $ccount2 = mysqli_num_rows($query2);
if($ccount1 > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $wapp_flag = $row1['flag']; } }
else if($ccount2 > 0){ while($row2 = mysqli_fetch_assoc($query2)){ $wapp_flag = $row2['flag']; } }
else{ $wapp_flag = 0; } if($wapp_flag == "" || $wapp_flag == 0 || $wapp_flag == "0.00" || $wapp_flag == NULL){ $wapp_flag = 0; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }

$sql = "SELECT *  FROM `acc_coa` WHERE `description` LIKE '%Stock-Wastage%' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $coa_wastage = "";
while($row = mysqli_fetch_assoc($query)){ $coa_wastage = $row['code']; }

$sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql); $farm_name = array();
while($row = mysqli_fetch_assoc($query)){ $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $icat_code[$row['code']] = $row['category']; $iscat_code[$row['code']] = $row['sub_category']; }

$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_iac[$row['code']] = $row['iac']; }

$sql = "SELECT * FROM `item_sizes` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`size` ASC";
$query = mysqli_query($conn,$sql); $isize_name = array();
while($row = mysqli_fetch_assoc($query)){ $isize_name[$row['code']] = round($row['size'],5); }

$receive_type = $_POST['receive_type'];
$cus_code = $_POST['cus_code'];
$batch_no = $_POST['batch_no'];
$mnubch_no = $_POST['mnubch_no'];
$billno = $_POST['billno'];
$date = date("Y-m-d",strtotime($_POST['date']));

$item_code = $isize_code = $isize_birds = $isize_weight = array();
$i = 0; foreach($_POST['item_code'] as $icode){ $item_code[$i] = $icode; $i++; }
$i = 0; foreach($_POST['isize_code'] as $icode){ $isize_code[$i] = $icode; $i++; }
$i = 0; foreach($_POST['isize_birds'] as $icode){ $isize_birds[$i] = $icode; $i++; }
$i = 0; foreach($_POST['isize_weight'] as $icode){ $isize_weight[$i] = $icode; $i++; }

$port_icode = $port_weight = $port_yield = array();
$i = 0; foreach($_POST['port_icode'] as $icode){ $port_icode[$i] = $icode; $i++; }
$i = 0; foreach($_POST['port_weight'] as $icode){ $port_weight[$i] = $icode; $i++; }
$i = 0; foreach($_POST['port_yield'] as $icode){ $port_yield[$i] = $icode; $i++; }
$i = 0; foreach($_POST['remarks2'] as $icode){ $remarks2[$i] = $icode; $i++; }

$tot_port_weight = $_POST['tot_port_weight']; if($tot_port_weight == ""){ $tot_port_weight = 0; }
$tot_pl_weight = $_POST['tot_pl_weight']; if($tot_pl_weight == ""){ $tot_pl_weight = 0; }
$remarks = $_POST['remarks'];

$trtype = "plant_portioning2";
$trlink = "broiler_display_plant_portioning2.php";
$flag = $dflag = $processed_flag = 0; $active = 1;

$link_trnum = $warehouse = "";
$sql = "SELECT * FROM `plant_bird_grading_details` WHERE `batch_no` = '$batch_no' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $link_trnum = $row['trnum']; $warehouse = $row['warehouse']; }

//Generate Transaction No.
$incr = 0; $prefix = $trnum = $fyear = "";
$trno_dt1 = generate_transaction_details($date,"plant_portioning2","PPG","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

$msg_details = "";
$msg_details .= "*Processing Plant: Bird Portioning Details*%0D%0APortioning Date: ".date('d.m.Y',strtotime($date))."%0D%0ABatch No: ".$mnubch_no."%0D%0A";
$msg_details .= "%0D%0A*Portioning Bird Size Details*%0D%0A";

$dsize = sizeof($isize_code);
for($i = 0; $i < $dsize;$i++){
    if($isize_birds[$i] == ""){ $isize_birds[$i] = 0; }
    if($isize_weight[$i] == ""){ $isize_weight[$i] = 0; }
    
    if((float)$isize_weight[$i] != 0){
        $item_category = $icat_code[$item_code[$i]];
        $item_subcategory = $iscat_code[$item_code[$i]];
        $sql = "INSERT INTO `plant_bird_portioning_consumed_details` (`trnum`,`link_trnum`,`date`,`receive_type`,`cus_code`,`batch_no`,`mnubch_no`,`billno`,`item_category`,`item_subcategory`,`item_code`,`isize_code`,`birds`,`weight`,`warehouse`,`remarks`,`remarks2`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('$trnum','$link_trnum','$date','$receive_type','$cus_code','$batch_no','$mnubch_no','$billno','$item_category','$item_subcategory','$item_code[$i]','$isize_code[$i]','$isize_birds[$i]','$isize_weight[$i]','$warehouse','$remarks','$remarks2[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{

            $sql = "SELECT * FROM `plant_bird_grading_item_stocks` WHERE `trnum` = '$link_trnum' AND `batch_no` = '$batch_no' AND `item_code` = '$item_code[$i]' AND `isize_code` = '$isize_code[$i]' AND `active` = '1' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql); $abirds = $aweight = 0;
            while($row = mysqli_fetch_assoc($query)){
                if((int)$row['portioning_flag'] == 0){
                    $abirds = (float)$row['birds'];
                    $aweight = (float)$row['weight'];
                    $abirds = (float)$abirds - (float)$isize_birds[$i];
                    $aweight = (float)$aweight - (float)$isize_weight[$i];
                }
                else if((int)$row['portioning_flag'] == 1){
                    $abirds = (float)$row['avl_birds'];
                    $aweight = (float)$row['avl_weight'];
                    $abirds = (float)$abirds - (float)$isize_birds[$i];
                    $aweight = (float)$aweight - (float)$isize_weight[$i];
                }
                else{ }
                $sql = "UPDATE `plant_bird_grading_item_stocks` SET `avl_birds` = '$abirds',`avl_weight` = '$aweight',`portioning_flag` = '1' WHERE `trnum` = '$link_trnum' AND `batch_no` = '$batch_no' AND `item_code` = '$item_code[$i]' AND `isize_code` = '$isize_code[$i]' AND `active` = '1' AND `dflag` = '0'";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
            }

            $coa_code = $icat_iac[$icat_code[$item_code[$i]]];
            $price = $amount = 0;
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,isize_code,plant_batch,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_code','$date','$billno','$trnum','$item_code[$i]','$isize_code[$i]','$batch_no','$isize_weight[$i]','$price','$amount','$warehouse','','','','$remarks','0','$flag','$active','$dflag','Plant Portioning: Item Consumed','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }

            $msg_details .= $isize_name[$isize_code[$i]]."%09-%09Birds: ".str_replace('.00','',number_format_ind($isize_birds[$i]))."%09-%09Weight: ".number_format_ind($isize_weight[$i])."%0D%0A";
            
        }
    }
}

$msg_details .= "%0D%0A*Portioning Item Produced Details*%0D%0A";

$dsize = sizeof($port_icode);
for($i = 0; $i < $dsize;$i++){
    if($port_weight[$i] == ""){ $port_weight[$i] = 0; }
    if($port_yield[$i] == ""){ $port_yield[$i] = 0; }
    
    if((float)$port_weight[$i] != 0){
        $item_category = $icat_code[$port_icode[$i]];
        $item_subcategory = $iscat_code[$port_icode[$i]];
        $sql = "INSERT INTO `plant_bird_portioning_produced_details` (`incr`,`prefix`,`trnum`,`date`,`receive_type`,`cus_code`,`batch_no`,`mnubch_no`,`link_trnum`,`billno`,`item_code`,`weight`,`yield_per`,`warehouse`,`remarks`,`remarks2`,`total_weight`,`process_loss_weight`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('$incr','$prefix','$trnum','$date','$receive_type','$cus_code','$batch_no','$mnubch_no','$link_trnum','$billno','$port_icode[$i]','$port_weight[$i]','$port_yield[$i]','$warehouse','$remarks','$remarks2[$i]','$tot_port_weight','$tot_pl_weight','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{
            $coa_code = $icat_iac[$icat_code[$port_icode[$i]]];
            $price = $amount = 0;
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,plant_batch,mnubch_no,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$coa_code','$date','$billno','$trnum','$port_icode[$i]','$batch_no','$mnubch_no','$port_weight[$i]','$price','$amount','$warehouse','','','','$remarks','0','$flag','$active','$dflag','Plant Portioning: Item Consumed','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }

            $msg_details .= $item_name[$port_icode[$i]]."%09-%09Weight: ".number_format_ind($port_weight[$i])."%09-%09Yield %: ".number_format_ind($port_yield[$i])."%0D%0A";
            
        }
    }
    if((float)$tot_pl_weight != 0){
        $price = $amount = 0;
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,plant_batch,mnubch_no,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$coa_wastage','$date','$billno','$trnum','$item_code[0]','$batch_no','$mnubch_no','$tot_pl_weight','$price','$amount','$warehouse','','','','$remarks','0','$flag','$active','$dflag','Plant Portioning: Item Wastage','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); } else{ }
    }
    $sql = "UPDATE `plant_bird_grading_details` SET `portioning_flag` = '1' WHERE `trnum` = '$link_trnum' AND `active` = '1' AND `dflag` = '0'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ }
}

if($wapp_flag > 0){
    $mobile_count = 0; $mobile_no_array = array();
    $msg_header = $msg_footer = "";
    $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-plant_portioning2' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $instance_id = $row['sms_key'];
        $access_token = $row['msg_key'];
        $msg_header = $row['msg_header'];
        $msg_footer = $row['msg_footer'];
        $url_id = $row['url_id'];
        if(!empty($row['numers'])){
            $m1 = explode(",",$row['numers']);
            if(sizeof($m1) > 1){ foreach($m1 as $fm1){ $mobile_count++; $mobile_no_array[$mobile_count] = $fm1; } }
            else{ $mobile_count++; $mobile_no_array[$mobile_count] = $row['numers']; }
        }
        else{ }
    }
       
    $sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
    while($row = mysqli_fetch_assoc($query)){ $curlopt_url = $row['curlopt_url']; }
    
    $message = "";
    $message .= $msg_header;
    $message .= "".$msg_details."%0D%0A%0D%0ARemarks: ".$remarks."%0D%0A%0D%0A";
    $message .= $msg_footer.",%0D%0AThank You,%0D%0A".$cdetails;

    if($message != ""){
        $message = str_replace(" ","+",$message);
        for($j = 1;$j <= $mobile_count;$j++){
            if(!empty($mobile_no_array[$j])){
                $wapp_date = date("Y-m-d");
                $ccode = "";
                $number = "91".$mobile_no_array[$j]; $type = "text";
    
                if((int)$url_id == 3){ $msg_info = $curlopt_url.''.$instance_id.'/messages/chat?token='.$access_token.'&to='.$number.'&body='.$message; }
                else{ $msg_info = $curlopt_url.'number='.$number.'&type='.$type.'&message='.$message.'&instance_id='.$instance_id.'&access_token='.$access_token; }
                
                $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $wapp = $row['wapp']; } $incr_wapp = $wapp + 1;
                $sql = "UPDATE `master_generator` SET `wapp` = '$incr_wapp' WHERE `fdate` <='$wapp_date' AND `tdate` >= '$wapp_date' AND `type` = 'transactions'";
                if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
                if($incr_wapp < 10){ $incr_wapp = '000'.$incr_wapp; } else if($incr_wapp >= 10 && $incr_wapp < 100){ $incr_wapp = '00'.$incr_wapp; } else if($incr_wapp >= 100 && $incr_wapp < 1000){ $incr_wapp = '0'.$incr_wapp; } else { }
                $wapp_code = "WAPP-".$incr_wapp;

                $database = $_SESSION['dbase'];
                $trtype = "BB-plant_portioning2";
                //$trnum = "";
                $vendor = $ccode;
                $mobile = $number;
                $msg_trnum = $wapp_code;
                $msg_type = "WAPP";
                $msg_project = "BTS";
                $status = "CREATED";
                $trlink = $_SERVER['REQUEST_URI'];
                $sql = "INSERT INTO `master_broiler_pendingmessages` (`database`,`url_id`,`trtype`,`trnum`,`vendor`,`mobile`,`msg_trnum`,`msg_type`,`msg_info`,`msg_project`,`status`,`trlink`,`addedemp`,`addedtime`,`updatedtime`)
                VALUES ('$database','$url_id','$trtype','$trnum','$vendor','$mobile','$msg_trnum','$msg_type','$msg_info','$msg_project','$status','$trlink','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conns,$sql)) { } else{ }
            }
        }
    }
}
header('location:broiler_display_plant_portioning2.php?ccid='.$ccid);
?>