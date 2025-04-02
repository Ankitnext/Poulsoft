<?php
//broiler_save_dailyentry.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['dailyentry'];

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
while($row = mysqli_fetch_assoc($query)){
    $icat_code[$row['code']] = $row['category'];
    if($row['description'] == "Broiler Chicks"){ $chick_code = $row['code']; } 
}

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Daily Entry' AND `field_function` LIKE 'Bags' AND `flag` = 1"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);
//$sql = "SELECT * FROM `extra_access`"; $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Day Entry" && $row['field_function'] == "2nd feed entry"){ $two_feed_flag = $row['flag']; } }

$date = $farm_code = $batch_code = $brood_age = $mortality = $culls = $item_code1 = $kgs1 = $item_code2 = $kgs2 = $avg_wt = $remarks = array();
$supervisor_code = $_POST['supervisor_code'];
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d",strtotime($dates)); $i++; }
$i = 0; foreach($_POST['farm_code'] as $farm_codes){ $farm_code[$i] = $farm_codes; $i++; }
$i = 0; foreach($_POST['batch_code'] as $batch_codes){ $batch_code[$i] = $batch_codes; $i++; }
$i = 0; foreach($_POST['brood_age'] as $brood_ages){ $brood_age[$i] = $brood_ages; $i++; }
$i = 0; foreach($_POST['mortality'] as $mortalitys){ $mortality[$i] = $mortalitys; $i++; }
$i = 0; foreach($_POST['culls'] as $cullss){ $culls[$i] = $cullss; $i++; }
$i = 0; foreach($_POST['item_code1'] as $item_code1s){ $item_code1[$i] = $item_code1s; $i++; }
$i = 0; foreach($_POST['kgs1'] as $kgs1s){ $kgs1[$i] = $kgs1s; $i++; }
$i = 0; foreach($_POST['available_price_1'] as $available_price_1s){ $available_price_1[$i] = $available_price_1s; $i++; }
$i = 0; foreach($_POST['item_code2'] as $item_code2s){ $item_code2[$i] = $item_code2s; $i++; }
$i = 0; foreach($_POST['kgs2'] as $kgs2s){ $kgs2[$i] = $kgs2s; $i++; }
$i = 0; foreach($_POST['available_price_2'] as $available_price_2s){ $available_price_2[$i] = $available_price_2s; $i++; }
$i = 0; foreach($_POST['avg_wt'] as $avg_wts){ $avg_wt[$i] = $avg_wts; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$flag = 0;
$active = 1;
$dflag = 0;

$dsize = sizeof($farm_code);
for($i = 0;$i < $dsize;$i++){
    $fsql = "SELECT * FROM `broiler_batch` WHERE `description` = '$batch_code[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $from_batch = $frow['code']; } } else{ $from_batch = ''; }
    
    //check for duplicate entry
    $sql = "SELECT * FROM `broiler_daily_record` WHERE `date` = '$date[$i]' AND `brood_age` = '$brood_age[$i]' AND `batch_code` = '$from_batch' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $d_cnt = 0; $d_cnt = mysqli_num_rows($query);

    if((int)$d_cnt > 0){ }
    else{
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
    
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $dayentry = $row['dayentry']; } $incr = $dayentry + 1;
    
        $sql = "UPDATE `master_generator` SET `dayentry` = '$incr' WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]' AND `type` = 'transactions'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'dayrecord' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }
    
        if($mortality[$i] == "" || $mortality[$i] == NULL || $mortality[$i] == 0 || $mortality[$i] == "0.00"){ $mortality[$i] = 0; }
        if($culls[$i] == "" || $culls[$i] == NULL || $culls[$i] == 0 || $culls[$i] == "0.00"){ $culls[$i] = 0; }
        if($kgs1[$i] == "" || $kgs1[$i] == NULL || $kgs1[$i] == 0 || $kgs1[$i] == "0.00"){ $kgs1[$i] = 0; }
        if($kgs2[$i] == "" || $kgs2[$i] == NULL || $kgs2[$i] == 0 || $kgs2[$i] == "0.00"){ $kgs2[$i] = 0; }
        if($avg_wt[$i] == "" || $avg_wt[$i] == NULL || $avg_wt[$i] == 0 || $avg_wt[$i] == "0.00"){ $avg_wt[$i] = 0; }
    
        if(!empty($item_code1[$i]) && !empty($kgs1[$i])){
            $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$item_code1[$i]' AND `active` = '1' AND `dflag` = '0' OR `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
            if($ibag_flag1 > 0 && $bag_access_flag > 0){
                while($brow = mysqli_fetch_assoc($bquery)){
                    if($ibag_flag1 > 1){
                        if($brow['code'] != "all"){
                            $kgs1[$i] = $kgs1[$i] * $brow['bag_size'];
                            $available_price_1[$i] = $available_price_1[$i] / $brow['bag_size'];
                        }
                    }
                    else{
                        $kgs1[$i] = $kgs1[$i] * $brow['bag_size'];
                        $available_price_1[$i] = $available_price_1[$i] / $brow['bag_size'];
                    }
                }
            }
        }
        if(!empty($item_code2[$i]) && !empty($kgs2[$i])){
            $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$item_code2[$i]' AND `active` = '1' AND `dflag` = '0' OR `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag2 = mysqli_num_rows($bquery);
            if($ibag_flag2 > 0 && $bag_access_flag > 0){
                while($brow = mysqli_fetch_assoc($bquery)){
                    if($ibag_flag2 > 1){
                        if($brow['code'] != "all"){
                            $kgs2[$i] = $kgs2[$i] * $brow['bag_size'];
                            $available_price_2[$i] = $available_price_2[$i] / $brow['bag_size'];
                        }
                    }
                    else{
                        $kgs2[$i] = $kgs2[$i] * $brow['bag_size'];
                        $available_price_2[$i] = $available_price_2[$i] / $brow['bag_size'];
                    }
                }
            }
        }
    
        $sql = "INSERT INTO `broiler_daily_record` (incr,prefix,trnum,supervisor_code,date,farm_code,batch_code,brood_age,mortality,culls,item_code1,kgs1,item_code2,kgs2,avg_wt,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$trnum','$supervisor_code','$date[$i]','$farm_code[$i]','$from_batch','$brood_age[$i]','$mortality[$i]','$culls[$i]','$item_code1[$i]','$kgs1[$i]','$item_code2[$i]','$kgs2[$i]','$avg_wt[$i]','$remarks[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }
        else {
            if($mortality[$i] == "0.00" || $mortality[$i] == "" || $mortality[$i] == 0 || $mortality[$i] == "0"){ }
            else{
                $il1 = ""; $il1 = $icat_code[$chick_code];
                $coa_Cr = $icat_iac[$il1];
                $coa_Dr = $icat_srac[$il1];
                $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$chick_code','$mortality[$i]','0','0','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryMortality','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); }
                else{
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$chick_code','$mortality[$i]','0','0','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryMortality','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
                }
            }
            if($culls[$i] == "0.00" || $culls[$i] == "" || $culls[$i] == 0 || $culls[$i] == "0"){ }
            else{
                $il1 = ""; $il1 = $icat_code[$chick_code];
                $coa_Cr = $icat_iac[$il1];
                $coa_Dr = $icat_srac[$il1];
                $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$chick_code','$culls[$i]','0','0','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryCulls','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 4:-".mysqli_error($conn)); }
                else{
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$chick_code','$culls[$i]','0','0','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryCulls','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 5:-".mysqli_error($conn)); } else{ }
                }
            }
            if($kgs2[$i] == "0.00" || $kgs2[$i] == "" || $kgs2[$i] == 0 || $kgs2[$i] == "0" || $item_code2[$i] == "" || $item_code2[$i] == "select"){ }
            else{
                if(empty($available_price_2[$i]) || $available_price_2[$i] == ""){ $available_price_2[$i] = 0; }
                $price2 = $available_price_2[$i];
                $amount2 = (float)$price2 * (float)$kgs2[$i];
    
                $il1 = ""; $il1 = $icat_code[$item_code2[$i]];
                $coa_Cr = $icat_iac[$il1];
                $coa_Dr = $icat_wpac[$il1];
                $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$item_code2[$i]','$kgs2[$i]','$price2','$amount2','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryFeed2','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 6:-".mysqli_error($conn)); }
                else{
                    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                    VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$item_code2[$i]','$kgs2[$i]','$price2','$amount2','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryFeed2','0','1','0','$addedemp','$addedtime','$addedtime')";
                    if(!mysqli_query($conn,$from_post)){ die("Error 7:-".mysqli_error($conn)); } else{ }
                }
            }
            if(empty($available_price_1[$i]) || $available_price_1[$i] == ""){ $available_price_1[$i] = 0; }
            $price1 = $available_price_1[$i];
            $amount1 = (float)$price1 * (float)$kgs1[$i];
    
            $il1 = ""; $il1 = $icat_code[$item_code1[$i]];
            $coa_Cr = $icat_iac[$il1];
            $coa_Dr = $icat_wpac[$il1];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
             VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$item_code1[$i]','$kgs1[$i]','$price1','$amount1','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryFeed','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 8:-".mysqli_error($conn)); }
            else{
                $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
                VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$item_code1[$i]','$kgs1[$i]','$price1','$amount1','$farm_code[$i]','$from_batch','$remarks[$i]','0','DayEntryFeed','0','1','0','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 9:-".mysqli_error($conn)); } else{ }
            }
        }
    }
}
header('location:broiler_display_dailyentry.php?ccid='.$ccid);
?>