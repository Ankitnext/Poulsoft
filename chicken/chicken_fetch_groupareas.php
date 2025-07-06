<?php
//chicken_fetch_groupareas.php
session_start();
include "newConfig.php";
include "number_format_ind.php";
date_default_timezone_set("Asia/Kolkata");
	
/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("chicken_designation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_designation LIKE poulso6_admin_chickenmaster.chicken_designation;"; mysqli_query($conn,$sql1); }
if(in_array("chicken_employee", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_employee LIKE poulso6_admin_chickenmaster.chicken_employee;"; mysqli_query($conn,$sql1); }
if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
if(in_array("main_areas", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_areas LIKE poulso6_admin_chickenmaster.main_areas;"; mysqli_query($conn,$sql1); }
if(in_array("main_areagroup_map", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_areagroup_map LIKE poulso6_admin_chickenmaster.main_areagroup_map;"; mysqli_query($conn,$sql1); }
    
$grp_code = $_GET['grp_code'];
$type = $_GET['type'];
$ara_code = $_GET['ara_code'];

$area_opt = '';
$sql = "SELECT * FROM `main_areagroup_map` WHERE `group_code` = '$grp_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn, $sql); $g_cnt = mysqli_num_rows($query); $area_alist = array();
if($g_cnt > 0){
    while($row = mysqli_fetch_array($query)){ $area_alist[$row['area_code']] = $row['area_code']; }
    $area_list = implode("','", $area_alist);
    $sql = "SELECT * FROM `main_areas` WHERE `code` IN ('$area_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
    $query = mysqli_query($conn, $sql); $g_cnt = mysqli_num_rows($query);
    if($g_cnt > 0){
        $area_opt = '<option value="select">-select-</option>';
        while($row = mysqli_fetch_array($query)){
            $code = $row['code']; $name = $row['description'];
            if($type == "edit" && $ara_code == $code){
                $area_opt .= '<option value="'.$code.'" selected>'.$name.'</option>';
            }
            else{
                $area_opt .= '<option value="'.$code.'">'.$name.'</option>';
            }
        }
    }
}
echo $area_opt;
?>