<?php
//broiler_save_placementplanning.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['placementplanning'];



$colarr = array('lb_cfcr','blb_cfcr','olb_fcr','olb_cfcr','olb_mort','olb_avg_bodywt','olb_mean_age','olb_batch_code');

$q = 'show columns from broiler_placementplan';
$qr = mysqli_query($conn, $q);
$i = 0;
$columns = array();
while ($rw = mysqli_fetch_assoc($qr)) {
	$columns[$i] = $rw['Field'];
	$i++;
}

$diff_array = array_diff($colarr, $columns);



if (in_array('lb_cfcr', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `lb_cfcr` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Last Batch Cfcr' AFTER `lb_fcr`;";
	$qr = mysqli_query($conn, $q);
}

if (in_array('blb_cfcr', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `blb_cfcr` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Before Last Batch Cfcr' AFTER `blb_fcr`;";
	$qr = mysqli_query($conn, $q);
}
if (in_array('olb_batch_code', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `olb_batch_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Old Batch Code' AFTER `blb_mean_age`;";
	$qr = mysqli_query($conn, $q);
}
if (in_array('olb_fcr', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `olb_fcr` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Old Batch fcr' AFTER `olb_batch_code`;";
	$qr = mysqli_query($conn, $q);
}

if (in_array('olb_cfcr', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `olb_cfcr` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Old Batch cfcr' AFTER `olb_fcr`;";
	$qr = mysqli_query($conn, $q);
}

if (in_array('olb_mort', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `olb_mort` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Old Batch Mortality' AFTER `olb_cfcr`;";
	$qr = mysqli_query($conn, $q);
}

if (in_array('olb_avg_bodywt', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `olb_avg_bodywt` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Old Batch Avg Body Weight' AFTER `olb_mort`;";
	$qr = mysqli_query($conn, $q);
}

if (in_array('olb_mean_age', $diff_array)) {
	$q = "ALTER TABLE `broiler_placementplan` ADD `olb_mean_age` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Old Batch Mean Age' AFTER `olb_avg_bodywt`;";
	$qr = mysqli_query($conn, $q);
}

$sql='SHOW COLUMNS FROM `broiler_placementplan`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("lb_gc_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_placementplan` ADD `lb_gc_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `lb_mean_age`"; mysqli_query($conn,$sql); }
if(in_array("blb_gc_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_placementplan` ADD `blb_gc_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `blb_mean_age`"; mysqli_query($conn,$sql); }
if(in_array("olb_gc_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_placementplan` ADD `olb_gc_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `olb_mean_age`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `master_generator`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("placement_plan", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `placement_plan` INT(100) NOT NULL DEFAULT '0' COMMENT 'Placement Planning' AFTER `wapp`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'placement_plan' AND `active` = '1'";
$query = mysqli_query($conn,$sql); $prx_entry_count = mysqli_num_rows($query);
if($prx_entry_count > 0){ } else{ $sql = "INSERT INTO `prefix_master` (`format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES ('column:flag', 'placement_plan', 'FCPP-', '0', '1:1', '1', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1');"; mysqli_query($conn,$sql); }

$date = date("Y-m-d");

$week_no = $_POST['week_no']; $fmcode = $remarks = array();
$from_date = date("Y-m-d",strtotime($_POST['from_date']));
$to_date = date("Y-m-d",strtotime($_POST['to_date']));
$i = 0; foreach($_POST['farm_code'] as $fcode){ $fmcode[$i] = $fcode; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$flag = $dflag = 0; $active = 1;

//Generate Invoice transaction number format
$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $placement_plan = $row['placement_plan']; } $incr = $placement_plan + 1;

$sql = "UPDATE `master_generator` SET `placement_plan` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'placement_plan' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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

$dsize = sizeof($fmcode);
for($i = 0;$i < $dsize;$i++){
    $branch_code = $line_code = $supervisor_code = $farm_capacity = $area_name = $blb_code = $lb_code = "";
    $sql = "SELECT * FROM `broiler_farm` WHERE `code` = '$fmcode[$i]' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $branch_code = $row['branch_code'];
        $line_code = $row['line_code'];
        $supervisor_code = $row['supervisor_code'];
        $farm_capacity = $row['farm_capacity'];
        $area_name = $row['area_name'];
    }

    $lb_fcr = $lb_mort_per = $lb_avg_bodywt = $lb_mean_age = $lb_gc_date = $blb_fcr = $blb_mort_per = $blb_avg_bodywt = $blb_mean_age = $blb_gc_date = $olb_fcr = $olb_cfcr = $olb_mort_per = $olb_avg_bodywt = $olb_mean_age = $olb_gc_date = "";
    $sql = "SELECT MAX(batch_no) as batch_no FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
    if($count > 0){
        while($row = mysqli_fetch_assoc($query)){ $lb_batch_no = $row['batch_no']; }
        if($lb_batch_no > 2){
            $blb_batch_no = $lb_batch_no - 1;
            $olb_batch_no = $blb_batch_no - 1;
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `batch_no` = '$lb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $lb_code = $row['code']; }
    
            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$fmcode[$i]' AND `batch_code` = '$lb_code' AND `dflag` = '0' AND `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $lb_fcr = $row['fcr'];
                $lb_cfcr = $row['cfcr'];
                $lb_mort_per = $row['total_mort'];
                $lb_avg_bodywt = $row['avg_wt'];
                $lb_mean_age = $row['mean_age'];
                $lb_gc_date = $row['date'];
            }
    
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `batch_no` = '$blb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $blb_code = $row['code']; }
    
          echo  $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$fmcode[$i]' AND `batch_code` = '$blb_code' AND `dflag` = '0' AND `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
               echo $blb_fcr = $row['fcr'];
                $blb_cfcr = $row['cfcr'];
                $blb_mort_per = $row['total_mort'];
                $blb_avg_bodywt = $row['avg_wt'];
                $blb_mean_age = $row['mean_age'];
                $blb_gc_date = $row['date'];
            }
    
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `batch_no` = '$olb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $olb_code = $row['code']; }
    
            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$fmcode[$i]' AND `batch_code` = '$olb_code' AND `dflag` = '0' AND `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $olb_fcr = $row['fcr'];
                $olb_cfcr = $row['cfcr'];
                $olb_mort_per = $row['total_mort'];
                $olb_avg_bodywt = $row['avg_wt'];
                $olb_mean_age = $row['mean_age'];
                $olb_gc_date = $row['date'];
            }
        }elseif($lb_batch_no > 1){
            $blb_batch_no = $lb_batch_no - 1;
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `batch_no` = '$lb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $lb_code = $row['code']; }

            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `batch_code` = '$lb_code' AND `dflag` = '0' AND `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $lb_fcr = $row['fcr'];
                $lb_cfcr = $row['cfcr'];
                $lb_mort_per = $row['total_mort'];
                $lb_avg_bodywt = $row['avg_wt'];
                $lb_mean_age = $row['mean_age'];
                $lb_gc_date = $row['date'];
            }

            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `batch_no` = '$blb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $blb_code = $row['code']; }

            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `batch_code` = '$blb_code' AND `dflag` = '0' AND `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $blb_fcr = $row['fcr'];
                $blb_cfcr = $row['cfcr'];
                $blb_mort_per = $row['total_mort'];
                $blb_avg_bodywt = $row['avg_wt'];
                $blb_mean_age = $row['mean_age'];
                $blb_gc_date = $row['date'];
            }
        }
        else{
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `batch_no` = '$lb_batch_no' AND `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $lb_code = $row['code']; }

            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `batch_code` = '$lb_code' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $lb_fcr = $row['fcr'];
                $lb_mort_per = $row['total_mort'];
                $lb_avg_bodywt = $row['avg_wt'];
                $lb_mean_age = $row['mean_age'];
                $lb_gc_date = $row['date'];
            }
        }
    }

    echo  $sql = "INSERT INTO `broiler_placementplan` (incr,prefix,trnum,date,week_no,from_date,to_date,farm_code,branch_code,village_code,sq_feet,line_code,supervisor_code,lb_batch_code,lb_fcr,lb_cfcr,lb_mort,lb_avg_bodywt,lb_mean_age,lb_gc_date,blb_batch_code,blb_fcr,blb_cfcr,blb_mort,blb_avg_bodywt,blb_mean_age,blb_gc_date,olb_batch_code,olb_fcr,olb_cfcr,olb_mort,olb_avg_bodywt,olb_mean_age,olb_gc_date,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES('$incr','$prefix','$trnum','$date','$week_no','$from_date','$to_date','$fmcode[$i]','$branch_code','$area_name','$farm_capacity','$line_code','$supervisor_code','$lb_code','$lb_fcr','$lb_cfcr','$lb_mort_per','$lb_avg_bodywt','$lb_mean_age','$lb_gc_date','$blb_code','$blb_fcr','$blb_cfcr','$blb_mort_per','$blb_avg_bodywt','$blb_mean_age','$blb_gc_date','$olb_code','$olb_fcr','$olb_cfcr','$olb_mort_per','$olb_avg_bodywt','$olb_mean_age','$olb_gc_date','$remarks[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else{ }
}

// header('location:broiler_display_placementplanning.php?ccid='.$ccid);

?>
