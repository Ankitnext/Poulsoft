<?php
//chicken_add_crdrnote1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d");
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "CrDr Note"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $method_code = $method_name = array();
    while($row = mysqli_fetch_assoc($query)){ $method_code[$row['code']] = $row['code']; $method_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'CrDr-Note Transaction' AND `field_function` LIKE 'Display: Reason selection' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $rsncrdr_flag = mysqli_num_rows($query);

	if((int)$rsncrdr_flag == 1){
		$sql = "SELECT * FROM `crdr_note_reasons` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
		$query = mysqli_query($conn,$sql); $reason_code = $reason_name = array();
		while($row = mysqli_fetch_assoc($query)){ $reason_code[$row['code']] = $row['code']; $reason_name[$row['code']] = $row['description']; }
	}
?>


<?php
}
else{ include "chicken_error_popup.php"; }