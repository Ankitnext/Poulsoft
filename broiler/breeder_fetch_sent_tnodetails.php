<?php
//breeder_fetch_sent_tnodetails.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$link_trnum = $_GET['link_trnum'];
$count = 0;

 //Breeder
 $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
 $query = mysqli_query($conn,$sql); $bfeed_scnt = mysqli_num_rows($query); $sector_code = $sector_name = array();
 if((int)$bfeed_scnt > 0){
     $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
     if($bfeed_stkon == "FARM"){
         $bsql = "SELECT * FROM `breeder_farms` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
         while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
     }
     else if($bfeed_stkon == "UNIT"){
         $bsql = "SELECT * FROM `breeder_units` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
         while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
     }
     else if($bfeed_stkon == "SHED"){
         $bsql = "SELECT * FROM `breeder_sheds` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
         while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
     }
     else if($bfeed_stkon == "BATCH"){
         $bsql = "SELECT * FROM `breeder_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
         while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
     }
     else if($bfeed_stkon == "FLOCK"){
         $bsql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
         while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
     }
     else{ }
 }

//Breeder Feed Details
$sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND (`bffeed_flag` = '1' OR `bmfeed_flag` = '1' OR `bmv_flag` = '1') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_alist = array();
while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
$icat_list = implode("','", $icat_alist);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $bitem_code = $bitem_name = array();
while($row = mysqli_fetch_assoc($query)){ $bitem_code[$row['code']] = $row['code']; $bitem_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$link_trnum' AND `active` = '1' AND `quantity` = '0' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
$date = $row['date'];
$dcno = $row['dcno'];

$fscode = $row['fromwarehouse'];
$fsname = $sector_name[$fscode];
$fs_opt = '<option value="'.$fscode.'">'.$fsname.'</option>';

$is_code = $row['code'];
$is_name = $bitem_name[$is_code];
$cs_opt = '<option value="'.$is_code.'">'.$is_name.'</option>';
$sent_qty = $row['sent_qty'];
$amount = $row['amount'];

$ts_code = $row['towarehouse'];
$ts_name = $sector_name[$ts_code];
$ts_opt = '<option value="'.$ts_code.'">'.$ts_name.'</option>';
$remarks = $row['remarks'];
}
echo $date."[@$&]".$dcno."[@$&]".$fs_opt."[@$&]".$cs_opt."[@$&]".$sent_qty."[@$&]".$amount."[@$&]".$ts_opt."[@$&]".$remarks;
?>
