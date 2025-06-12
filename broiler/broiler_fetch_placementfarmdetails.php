<?php
//broiler_fetch_placementfarmdetails.php
session_start(); $dbname = $_SESSION['dbase'];
date_default_timezone_set("Asia/Kolkata");
$apcn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$dbname) or die('No apcnection');
$farm_code = $_GET['farm_code'];
$row_no = $_GET['row_no'];

$sql = "SELECT * FROM `broiler_farm` WHERE `code` = '$farm_code' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($apcn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $branch_code = $row['branch_code'];
    $line_code = $row['line_code'];
    $supervisor_code = $row['supervisor_code'];
    $farm_capacity = $row['farm_capacity'];
    $area_name = $row['area_name'];
}

$sql = "SELECT * FROM `location_branch` WHERE `code` = '$branch_code' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($apcn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_name = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `code` = '$line_code' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($apcn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_name = $row['description']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `code` = '$supervisor_code' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($apcn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_name = $row['name']; }

$sql = "SELECT MAX(batch_no) as batch_no FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
$query = mysqli_query($apcn,$sql); $count = mysqli_num_rows($query);
if($count > 0){
    while($row = mysqli_fetch_assoc($query)){ $lb_batch_no = $row['batch_no']; }
    if($lb_batch_no > 2){
        $blb_batch_no = $lb_batch_no - 1;
        $olb_batch_no = $blb_batch_no - 1;
        $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$lb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($apcn,$sql); while($row = mysqli_fetch_assoc($query)){ $lb_code = $row['code']; }

        $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$lb_code' AND `dflag` = '0' AND `active` = '1'";
        $query = mysqli_query($apcn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $lb_fcr = $row['fcr'];
            $lb_cfcr = $row['cfcr'];
            $lb_mort_per = $row['total_mort'];
            $lb_avg_bodywt = $row['avg_wt'];
            $lb_mean_age = $row['mean_age'];
            $lb_gc_date = date("d.m.Y",strtotime($row['date']));
        }

        $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$blb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($apcn,$sql); while($row = mysqli_fetch_assoc($query)){ $blb_code = $row['code']; }

        $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$blb_code' AND `dflag` = '0' AND `active` = '1'";
        $query = mysqli_query($apcn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $blb_fcr = $row['fcr'];
            $blb_cfcr = $row['cfcr'];
            $blb_mort_per = $row['total_mort'];
            $blb_avg_bodywt = $row['avg_wt'];
            $blb_mean_age = $row['mean_age'];
            $blb_gc_date = date("d.m.Y",strtotime($row['date']));
        }

        $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$olb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($apcn,$sql); while($row = mysqli_fetch_assoc($query)){ $olb_code = $row['code']; }

        $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$olb_code' AND `dflag` = '0' AND `active` = '1'";
        $query = mysqli_query($apcn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $olb_fcr = $row['fcr'];
            $olb_cfcr = $row['cfcr'];
            $olb_mort_per = $row['total_mort'];
            $olb_avg_bodywt = $row['avg_wt'];
            $olb_mean_age = $row['mean_age'];
            $olb_gc_date = date("d.m.Y",strtotime($row['date']));
        }
    }else if($lb_batch_no > 1){
        $blb_batch_no = $lb_batch_no - 1;
        $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$lb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($apcn,$sql); while($row = mysqli_fetch_assoc($query)){ $lb_code = $row['code']; }

        $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$lb_code' AND `dflag` = '0' AND `active` = '1'";
        $query = mysqli_query($apcn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $lb_fcr = $row['fcr'];
            $lb_cfcr = $row['cfcr'];
            $lb_mort_per = $row['total_mort'];
            $lb_avg_bodywt = $row['avg_wt'];
            $lb_mean_age = $row['mean_age'];
            $lb_gc_date = date("d.m.Y",strtotime($row['date']));
        }

        $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$blb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($apcn,$sql); while($row = mysqli_fetch_assoc($query)){ $blb_code = $row['code']; }

        $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `farm_code` = '$farm_code' AND `batch_code` = '$blb_code' AND `dflag` = '0' AND `active` = '1'";
        $query = mysqli_query($apcn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $blb_fcr = $row['fcr'];
            $blb_cfcr = $row['cfcr'];
            $blb_mort_per = $row['total_mort'];
            $blb_avg_bodywt = $row['avg_wt'];
            $blb_mean_age = $row['mean_age'];
            $blb_gc_date = date("d.m.Y",strtotime($row['date']));
        }
    }
    else{
        $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farm_code' AND `batch_no` = '$lb_batch_no' AND `dflag` = '0' AND `gc_flag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($apcn,$sql); while($row = mysqli_fetch_assoc($query)){ $lb_code = $row['code']; }

        $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `batch_code` = '$lb_code' AND `dflag` = '0' AND `active` = '1'";
        $query = mysqli_query($apcn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $lb_fcr = $row['fcr'];
            $lb_cfcr = $row['cfcr'];
            $lb_mort_per = $row['total_mort'];
            $lb_avg_bodywt = $row['avg_wt'];
            $lb_mean_age = $row['mean_age'];
            $lb_gc_date = date("d.m.Y",strtotime($row['date']));
        }
    }
}

echo $inv_list = $row_no."@".$branch_name."@".$area_name."@".$farm_capacity."@".$line_name."@".$emp_name.
"@".$lb_fcr."@".$lb_mort_per."@".$lb_avg_bodywt."@".$lb_mean_age."@".$blb_fcr."@".$blb_mort_per."@".$blb_avg_bodywt."@".$blb_mean_age."@".$lb_cfcr."@".$blb_cfcr.
"@".$olb_fcr."@".$olb_cfcr."@".$olb_mort_per."@".$olb_avg_bodywt."@".$olb_mean_age."@".$lb_gc_date."@".$blb_gc_date."@".$olb_gc_date;

?>
