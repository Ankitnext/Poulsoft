<?php
//broiler_save_inventorytransfer.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['inventorytransfer'];
include "number_format_ind.php";

/*Check send message flag*/
$sql1 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'StktransAutoWapp:broiler_display_inventorytransfer.php' AND `user_access` LIKE '%$addedemp%'"; $query1 = mysqli_query($conn,$sql1); $ccount1 = mysqli_num_rows($query1);
$sql2 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'StktransAutoWapp:broiler_display_inventorytransfer.php' AND `user_access` = 'all'"; $query2 = mysqli_query($conn,$sql2); $ccount2 = mysqli_num_rows($query2);
if($ccount1 > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $stktr_wapp = $row1['flag']; } }
else if($ccount2 > 0){ while($row2 = mysqli_fetch_assoc($query2)){ $stktr_wapp = $row2['flag']; } }
else{ $stktr_wapp = 0; }
if($stktr_wapp == "" || $stktr_wapp == 0 || $stktr_wapp == "0.00" || $stktr_wapp == NULL){ $stktr_wapp = 0; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cdetails = $row['cname']." - ".$row['cnum']; }

$sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql); $farm_name = $farm_farmer = array();
while($row = mysqli_fetch_assoc($query)){ $farm_name[$row['code']] = $row['description']; $farm_farmer[$row['code']] = $row['farmer_code']; }

$sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql); $vehicle_name = array();
while($row = mysqli_fetch_assoc($query)){ $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql); $driver_name = array();
while($row = mysqli_fetch_assoc($query)){ $driver_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $este_code = array();
while($row = mysqli_fetch_assoc($query)){ $este_code[$row['code']] = $row['este_code']; }

$today = date("Y-m-d");

$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $icat_iac[$row['code']] = $row['iac'];
    $icat_pvac[$row['code']] = $row['pvac'];
    $icat_pdac[$row['code']] = $row['pdac'];
    $icat_cogsac[$row['code']] = $row['cogsac'];
    $icat_wpac[$row['code']] = $row['wpac'];
    $icat_sac[$row['code']] = $row['sac'];
    $icat_srac[$row['code']] = $row['srac'];
}

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d",strtotime($dates)); $i++; }
$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
$i = 0; foreach($_POST['code'] as $codes){ $code[$i] = $codes; $i++; }
$i = 0; foreach($_POST['quantity'] as $quantitys){ $quantity[$i] = $quantitys; $i++; }
$i = 0; foreach($_POST['avg_price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['fromwarehouse'] as $fromwarehouses){ $fromwarehouse[$i] = $fromwarehouses; $i++; }
$i = 0; foreach($_POST['towarehouse'] as $towarehouses){ $towarehouse[$i] = $towarehouses; $i++; }
$i = 0; foreach($_POST['vehicle_code'] as $vehicle_codes){ $vehicle_code[$i] = $vehicle_codes; $i++; }
$i = 0; foreach($_POST['driver_code'] as $driver_codes){ $driver_code[$i] = $driver_codes; $i++; }
$i = 0; foreach($_POST['driver_mobile'] as $driver_mobiles){ $driver_mobile[$i] = $driver_mobiles; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$i = 0; foreach($_POST['emp_code'] as $emp_codes){ $emp_code[$i] = $emp_codes; $i++; }
$i = 0; foreach($_POST['emp_bcoa'] as $emp_bcoas){ $emp_bcoa[$i] = $emp_bcoas; $i++; }
$i = 0; foreach($_POST['emp_eamt'] as $emp_eamts){ $emp_eamt[$i] = $emp_eamts; $i++; }

//Fetch Feed Details and Feed in Bags Flag
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_feed_code[$row['code']] = $row['code']; $item_feed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Stock Transfer' AND `field_function` LIKE 'Bags' AND `flag` = 1";
$query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

$dsize = sizeof($quantity);
for($i = 0;$i < $dsize;$i++){
    $stk_itemid = $i +1;

    //Generate Invoice transaction number format
    $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $stktransfer = $row['stktransfer']; } $incr = $stktransfer + 1;

    $sql = "UPDATE `master_generator` SET `stktransfer` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

    if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }

    $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'stktransfer' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
    $a = 1; $tr_code = $prefix;
    for($j = 0;$j <= 16;$j++){
        if(!empty($inv_format[$j.":".$a])){
            if($inv_format[$j.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8'); }
            else if($inv_format[$j.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-"; }
            else if($inv_format[$j.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8'); }
            else if($inv_format[$j.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-"; }
            else if($inv_format[$j.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
            else if($inv_format[$j.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
            else if($inv_format[$j.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
            else if($inv_format[$j.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
            else if($inv_format[$j.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
            else if($inv_format[$j.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
            else if($inv_format[$j.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
            else if($inv_format[$j.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
            else if($inv_format[$j.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
            else if($inv_format[$j.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
            else if($inv_format[$j.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
            else if($inv_format[$j.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
            else{ }
        }
    }
    $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }

    if($quantity[$i] == "" || $quantity[$i] == NULL || $quantity[$i] == 0 || $quantity[$i] == "0.00"){ $quantity[$i] = "0.00"; }
    if($price[$i] == "" || $price[$i] == NULL || $price[$i] == 0 || $price[$i] == "0.00"){ $price[$i] = "0.00"; }
    if($emp_eamt[$i] == "" || $emp_eamt[$i] == NULL || $emp_eamt[$i] == 0 || $emp_eamt[$i] == "0.00"){ $emp_eamt[$i] = 0; }
    $amount = $quantity[$i] * $price[$i];

    $feed_item =  $code[$i];
    if(!empty($item_feed_name[$feed_item]) && !empty($quantity[$i]) && $bag_access_flag > 0){
        $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$code[$i]' AND `active` = '1' AND `dflag` = '0'";
        $bquery = mysqli_query($conn,$bsql); $bcount = $ibag_flag1 = mysqli_num_rows($bquery);
        if($bcount > 0){
            if($ibag_flag1 > 0 && $bag_access_flag > 0){
                while($brow = mysqli_fetch_assoc($bquery)){
                    if($brow['code'] != "all"){
                        $quantity[$i] = $quantity[$i] * $brow['bag_size'];
                        $price[$i] = $price[$i] / $brow['bag_size'];
                    }
                    else{
                        $quantity[$i] = $quantity[$i] * $brow['bag_size'];
                        $price[$i] = $price[$i] / $brow['bag_size'];
                    }
                }
            }
        }
        else{
            $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
            if($ibag_flag1 > 0 && $bag_access_flag > 0){
                while($brow = mysqli_fetch_assoc($bquery)){
                    if($brow['code'] != "all"){
                        $quantity[$i] = $quantity[$i] * $brow['bag_size'];
                        $price[$i] = $price[$i] / $brow['bag_size'];
                    }
                    else{
                        $quantity[$i] = $quantity[$i] * $brow['bag_size'];
                        $price[$i] = $price[$i] / $brow['bag_size'];
                    }
                }
            }
        }
    }

    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fromwarehouse[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $from_batch = $frow['code']; } } else{ $from_batch = ''; }

    $tsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$towarehouse[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $tquery = mysqli_query($conn,$tsql); $tcount = mysqli_num_rows($tquery);
    if($tcount > 0){ while($trow = mysqli_fetch_assoc($tquery)){ $to_batch = $trow['code']; } } else{ $to_batch = ''; }

    $sql = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$code[$i]' AND `active` = '1' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `date` <= '$date[$i]' AND `itemcode` = '$code[$i]' AND `active` = '1')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $farmer_price = $row['rate']; }
    if($farmer_price == "" || $farmer_price == 0 || $farmer_price == 0.00){ $farmer_price = "0.00"; }
    $emp_ecoa = $este_code[$emp_code[$i]];
    $sql = "INSERT INTO `item_stocktransfers` (stk_itemid,incr,prefix,trnum,date,dcno,fromwarehouse,from_batch,code,quantity,price,amount,farmer_price,towarehouse,to_batch,vehicle_code,driver_code,driver_mobile,emp_code,emp_ecoa,emp_bcoa,emp_eamt,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES 
    ('$stk_itemid','$incr','$prefix','$trnum','$date[$i]','$dcno[$i]','$fromwarehouse[$i]','$from_batch','$code[$i]','$quantity[$i]','$price[$i]','$amount','$farmer_price','$towarehouse[$i]','$to_batch','$vehicle_code[$i]','$driver_code[$i]','$driver_mobile[$i]','$emp_code[$i]','$emp_ecoa','$emp_bcoa[$i]','$emp_eamt[$i]','$remarks[$i]','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else {
        $coa_Dr = $coa_Cr = $icat_iac[$icat_code[$code[$i]]];
        $from_post = "INSERT INTO `account_summary` (stk_itemid,crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('$stk_itemid','CR','$coa_Cr','$date[$i]','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','$price[$i]','$amount','$fromwarehouse[$i]','$from_batch','$vehicle_code[$i]','$driver_code[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); }
        else{
            $to_post = "INSERT INTO `account_summary` (stk_itemid,crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('$stk_itemid','DR','$coa_Dr','$date[$i]','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','$price[$i]','$amount','$towarehouse[$i]','$to_batch','$vehicle_code[$i]','$driver_code[$i]','$remarks[$i]','0','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$to_post)){ die("Error:-".mysqli_error($conn)); }
            else{ }
        }

        //Employee Stock transfr Expense
        if((float)$emp_eamt[$i] > 0){
            
            $from_post = "INSERT INTO `account_summary` (emp_code,crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedtime) 
            VALUES ('$emp_code[$i]','CR','$emp_ecoa','$date[$i]','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','0','$emp_eamt[$i]','$fromwarehouse[$i]','$from_batch','$vehicle_code[$i]','$driver_code[$i]','$remarks[$i]','0','0','1','0','Employee Stock Transfer Cost','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); }
            else{
                $to_post = "INSERT INTO `account_summary` (emp_code,crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedtime) 
                VALUES ('$emp_code[$i]','DR','$emp_bcoa[$i]','$date[$i]','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','0','$emp_eamt[$i]','$towarehouse[$i]','$to_batch','$vehicle_code[$i]','$driver_code[$i]','$remarks[$i]','0','0','1','0','Employee Stock Transfer Cost','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$to_post)){ die("Error:-".mysqli_error($conn)); }
                else{ }
            }
        }
    }
    
    if($stktr_wapp > 0){
        $mobile_count = 0; $mobile_no_array = array(); $farmercode = $farm_farmer[$towarehouse[$i]]; $from_sector = $farm_name[$fromwarehouse[$i]];
        $sql = "SELECT * FROM `broiler_farmer` WHERE `code` LIKE '$farmercode'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$m1 = explode(",",$row['mobile1']);
			if(sizeof($m1) > 1){ foreach($m1 as $fm1){ $mobile_count++; $mobile_no_array[$mobile_count] = $fm1; } }
			else{ $mobile_count++; $mobile_no_array[$mobile_count] = $row['mobile1']; }
			$cname = $row['name'];
		}
        $msg_header = $msg_footer = "";
        $sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-stktrans' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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
        
        $message = "";
        if(!empty($vehicle_name[$vehicle_code[$i]])){ $vehicles = $vehicle_name[$vehicle_code[$i]]; } else{ $vehicles = $vehicle_code[$i]; } if($vehicles == "select"){ $vehicles = ""; }
        if(!empty($driver_name[$driver_code[$i]])){ $drivers = $driver_name[$driver_code[$i]]; } else{ $drivers = $driver_code[$i]; } if($drivers == "select"){ $drivers = ""; }
        $item_details = "";
        if($item_details == ""){ $item_details = "Item: ".$item_name[$code[$i]].",%0D%0AQuantity: ".number_format_ind($quantity[$i])." Kgs"; }
        else{ $item_details = $item_details.",%0D%0AItem: ".$item_name[$code[$i]].",%0D%0AQuantity: ".number_format_ind($quantity[$i])." Kgs"; }

        $message .= $msg_header;
		if($stktr_wapp == 1){
            $message .= "Dear: ".$cname."%0D%0ADate: ".date('d.m.Y',strtotime($date[$i])).",%0D%0AFrom: ".$from_sector.",%0D%0ADC No: ".$dcno[$i].",%0D%0AVehicle No: ".$vehicles.",%0D%0ADriver: ".$drivers.",%0D%0A".$item_details;	
        }
        else if($stktr_wapp == 2){
            $message .= "Dear: ".$cname."%0D%0ADate: ".date('d.m.Y',strtotime($date[$i])).",%0D%0AFrom: ".$from_sector.",%0D%0ADC No: ".$dcno[$i].",%0D%0AVehicle No: ".$vehicles.",%0D%0ADriver: ".$drivers.",%0D%0A".$item_details;	
        }
        else{ }
        $message .= $msg_footer.",%0D%0AThank You,%0D%0A".$cdetails;
        if($message != ""){
            $message = str_replace(" ","+",$message);
            for($j = 1;$j <= $mobile_count;$j++){
                if(!empty($mobile_no_array[$j])){
                        
                    $sql = "SELECT * FROM `whatsapp_master` WHERE `id` = '$url_id' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $curlopt_url = $row['curlopt_url']; }
    
                    $wapp_date = date("Y-m-d");
                    $ccode = $farmercode;
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
                    $trtype = "BB-AutoStktransWapp";
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
}
header('location:broiler_display_inventorytransfer.php?ccid='.$ccid);
?>