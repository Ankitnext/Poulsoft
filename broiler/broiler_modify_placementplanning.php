<?php
//broiler_modify_placementplanning.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['placementplanning'];

$sql='SHOW COLUMNS FROM `broiler_placementplan`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("lb_gc_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_placementplan` ADD `lb_gc_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `lb_mean_age`"; mysqli_query($conn,$sql); }
if(in_array("blb_gc_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_placementplan` ADD `blb_gc_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `blb_mean_age`"; mysqli_query($conn,$sql); }
if(in_array("olb_gc_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_placementplan` ADD `olb_gc_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `olb_mean_age`"; mysqli_query($conn,$sql); }

$trnum = $_POST['idvalue'];

$date = date("Y-m-d");

$week_no = $_POST['week_no'];
$from_date = date("Y-m-d",strtotime($_POST['from_date']));
$to_date = date("Y-m-d",strtotime($_POST['to_date']));
$i = 0; foreach($_POST['farm_code'] as $fcode){ $fmcode[$i] = $fcode; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$flag = $dflag = 0; $active = 1;

$sql = "SELECT * FROM `broiler_placementplan` WHERE `trnum` = '$trnum' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $elb_code[$row['farm_code']] = $row['lb_code'];
    $eblb_code[$row['farm_code']] = $row['blb_code'];
    $incr = $row['incr'];
    $prefix = $row['prefix'];
}
$sql = "DELETE FROM `broiler_placementplan` WHERE `trnum` = '$trnum' AND `dflag` = '0'"; mysqli_query($conn,$sql);

$dsize = sizeof($fmcode);
for($i = 0;$i < $dsize;$i++){
    $branch_code = $line_code = $supervisor_code = $farm_capacity = $area_name = "";
    $lb_fcr = $lb_mort_per = $lb_avg_bodywt = $lb_mean_age = $lb_gc_date = $blb_fcr = $blb_mort_per = $blb_avg_bodywt = $blb_mean_age = $blb_gc_date = $olb_fcr = $olb_cfcr = $olb_mort_per = $olb_avg_bodywt = $olb_mean_age = $olb_gc_date = "";
    $sql = "SELECT * FROM `broiler_farm` WHERE `code` = '$fmcode[$i]' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $branch_code = $row['branch_code'];
        $line_code = $row['line_code'];
        $supervisor_code = $row['supervisor_code'];
        $farm_capacity = $row['farm_capacity'];
        $area_name = $row['area_name'];
    }
    if(!empty($elb_code[$fmcode[$i]])){
        $elb1 = $elb_code[$fmcode[$i]];
        $sql = "SELECT MAX(batch_no) as batch_no FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `code` IN ('elb1') AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
    }
    else{
        $sql = "SELECT MAX(batch_no) as batch_no FROM `broiler_batch` WHERE `farm_code` = '$fmcode[$i]' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
    }
    
    if($count > 0){
        while($row = mysqli_fetch_assoc($query)){ $lb_batch_no = $row['batch_no']; }
        if($lb_batch_no > 2){
            $blb_batch_no = $lb_batch_no - 1;
            $olb_batch_no = $blb_batch_no - 1;
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$lb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $lb_code = $row['code']; }
    
            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$lb_code' AND `dflag` = '0' AND `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $lb_fcr = $row['fcr'];
                $lb_cfcr = $row['cfcr'];
                $lb_mort_per = $row['total_mort'];
                $lb_avg_bodywt = $row['avg_wt'];
                $lb_mean_age = $row['mean_age'];
                $lb_gc_date = $row['date'];
            }
    
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$blb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $blb_code = $row['code']; }
    
            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$blb_code' AND `dflag` = '0' AND `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $blb_fcr = $row['fcr'];
                $blb_cfcr = $row['cfcr'];
                $blb_mort_per = $row['total_mort'];
                $blb_avg_bodywt = $row['avg_wt'];
                $blb_mean_age = $row['mean_age'];
                $blb_gc_date = $row['date'];
            }
    
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$olb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $olb_code = $row['code']; }
    
            $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$olb_code' AND `dflag` = '0' AND `active` = '1'";
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

    $sql = "INSERT INTO `broiler_placementplan` (incr,prefix,trnum,date,week_no,from_date,to_date,farm_code,branch_code,village_code,sq_feet,line_code,supervisor_code,lb_batch_code,lb_fcr,lb_cfcr,lb_mort,lb_avg_bodywt,lb_mean_age,lb_gc_date,blb_batch_code,blb_fcr,blb_cfcr,blb_mort,blb_avg_bodywt,blb_mean_age,blb_gc_date,olb_batch_code,olb_fcr,olb_cfcr,olb_mort,olb_avg_bodywt,olb_mean_age,olb_gc_date,remarks,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES('$incr','$prefix','$trnum','$from_date','$week_no','$from_date','$to_date','$fmcode[$i]','$branch_code','$area_name','$farm_capacity','$line_code','$supervisor_code','$lb_code','$lb_fcr','$lb_cfcr','$lb_mort_per','$lb_avg_bodywt','$lb_mean_age','$lb_gc_date','$blb_code','$blb_fcr','$blb_cfcr','$blb_mort_per','$blb_avg_bodywt','$blb_mean_age','$blb_gc_date','$olb_code','$olb_fcr','$olb_cfcr','$olb_mort_per','$olb_avg_bodywt','$olb_mean_age','$olb_gc_date','$remarks[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else{ }
}

header('location:broiler_display_placementplanning.php?ccid='.$ccid);

?>
