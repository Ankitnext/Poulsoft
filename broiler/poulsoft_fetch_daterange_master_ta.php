<?php
//poulsoft_fetch_daterange_master.php
include "newConfig.php";
$e_code = $_SESSION['userid'];
$database_name = $_SESSION['dbase'];

/*Check for Table Availability*/
$table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("dataentry_daterange_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.dataentry_daterange_master LIKE poulso6_admin_broiler_broilermaster.dataentry_daterange_master;"; mysqli_query($conn,$sql1); }

global $rng_sdate;
global $rng_edate;
$s_days = $e_days = 0; $today = date("Y-m-d"); $ir_date = "2001-01-01"; $fr_date = date("Y-m-d", strtotime($ir_date." +35 years"));

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '$drng_furl' AND `field_function` LIKE 'Date Range Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $drange_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '$drng_furl' AND `field_function` LIKE 'Financial Year Range Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $finyear_flag = mysqli_num_rows($query);

if((int)$drange_flag == 0 && (int)$finyear_flag == 0){
    $rng_sdate = date("d.m.Y", strtotime($ir_date));
    if((int)$drng_cday == 1){ $rng_edate = date("d.m.Y"); } else{ $rng_edate = date("d.m.Y", strtotime($fr_date)); }
}
else{
    $drng_sdate = $drng_edate = $frng_sdate = $frng_edate = "";
    if((int)$drange_flag > 0){
        $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `file_name` LIKE '$drng_furl' AND `user_code` LIKE '$e_code' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $r_cnt = mysqli_num_rows($query);
        if($r_cnt > 0){
            while($row = mysqli_fetch_assoc($query)){ $s_days = $row['min_days']; $e_days = $row['max_days']; }
            $drng_sdate = date('d.m.Y', strtotime('-'.$s_days.' days', strtotime($today)));
            $drng_edate = date('d.m.Y', strtotime('+'.$e_days.' days', strtotime($today)));
        }
    }
    if((int)$finyear_flag > 0){
        $sql = "SELECT MIN(fdate) as fydate,MAX(tdate) as tydate FROM `main_financialyear` WHERE `flag` = '0' AND `active` = '1'";
        $query = mysqli_query($conn,$sql); $r_cnt = mysqli_num_rows($query); $fydate = $tydate = "";
        if($r_cnt > 0){
            while($row = mysqli_fetch_assoc($query)){ $fydate = $row['fydate']; $tydate = $row['tydate']; }
            $frng_sdate = date("d.m.Y", strtotime($fydate));
            $frng_edate = date("d.m.Y", strtotime($tydate));
        }
    }

    //Start Date Calculations
    if($drng_sdate != "" && $frng_sdate != ""){
        if(strtotime($drng_sdate) > strtotime($frng_sdate)){ $rng_sdate = date("d.m.Y", strtotime($drng_sdate)); }
        else{ $rng_sdate = date("d.m.Y", strtotime($frng_sdate)); }
    }
    else if($drng_sdate != "" && $frng_sdate == ""){ $rng_sdate = date("d.m.Y", strtotime($drng_sdate)); }
    else if($drng_sdate == "" && $frng_sdate != ""){ $rng_sdate = date("d.m.Y", strtotime($frng_sdate)); }
    else{ $rng_sdate = date("d.m.Y", strtotime($ir_date)); }
    
    //End Date Calculations
    if($drng_edate != "" && $frng_edate != ""){
        if(strtotime($drng_edate) < strtotime($frng_edate)){ $rng_edate = date("d.m.Y", strtotime($drng_edate)); }
        else{ $rng_edate = date("d.m.Y", strtotime($frng_edate)); }
    }
    else if($drng_edate != "" && $frng_edate == ""){ $rng_edate = date("d.m.Y", strtotime($drng_edate)); }
    else if($drng_edate == "" && $frng_edate != ""){ $rng_edate = date("d.m.Y", strtotime($frng_edate)); }
    else{ if((int)$drng_cday == 1){ $rng_edate = date("d.m.Y"); } else{ $rng_edate = date("d.m.Y", strtotime($fr_date)); } }
}