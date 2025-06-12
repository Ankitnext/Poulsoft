<?php
//chicken_fetch_daterangemaster.php
if(!isset($_SESSION)){ session_start(); }
$access_database = $_SESSION['dbase'];
$access_usrcode = $_SESSION['userid'];

global $rng_mdate;
$days = 0;
if($access_database != ""){
    $acs_conn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$access_database);
    if($acs_conn){
        //Fetch Data Entry Date Range Details
        if($trns_dtype != ""){
            $asql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$access_usrcode' AND `active` = '1'";
            $aquery = mysqli_query($acs_conn,$asql); $aa_flag = $na_flag = 0; $idate = "2001-01-01";
            while($arow = mysqli_fetch_array($aquery)){
                if($arow['supadmin_access'] == 1 || $arow['admin_access'] == 1){ $aa_flag = 1; } else if($arow['normal_access'] == 1){ $na_flag = 1; }
            }

            $acs_sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = '$trns_dtype' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
            $acs_query = mysqli_query($acs_conn,$acs_sql); $acs_cnt = mysqli_num_rows($acs_query);
            if($acs_cnt > 0){
                while($acs_row = mysqli_fetch_array($acs_query)){ $days = $acs_row['days']; }
            }
            else{
                $acs_sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = 'all' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                $acs_query = mysqli_query($acs_conn,$acs_sql); $acs_cnt = mysqli_num_rows($acs_query);
                if($acs_cnt > 0){
                    while($acs_row = mysqli_fetch_array($acs_query)){ $days = $acs_row['days']; }
                }
                else{
                    $days = 0;
                }
            }

            if((int)$days == 0){
                $rng_mdate = date('d.m.Y', strtotime('-30 days'));
            }
            else{
                $rng_mdate = date('d.m.Y', strtotime('-'.$days.' days'));
            }
            if((int)$aa_flag == 1){ $rng_mdate = date('d.m.Y', strtotime($idate)); }
        }
        else{ }
    }
    else{ }
}
else{ }
if(isset($acs_conn)){ mysqli_close($acs_conn); }
