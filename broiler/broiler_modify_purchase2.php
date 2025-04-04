<?php
//broiler_modify_purchase2.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['purchase2'];
$user_code = $_SESSION['userid'];

$ids = $_POST['idvalue']; $opur_doc_1 = $opur_doc_2 = $opur_doc_3 = "";
$sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$ids' AND `dflag` = '0' GROUp BY `trnum` ORDER BY `trnum` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $opur_doc_1 = $row['purdoc_path1']; $opur_doc_2 = $row['purdoc_path2']; $opur_doc_3 = $row['purdoc_path3'];
}
/*Check for Table Availability*/
$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("broiler_farm", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_farm LIKE poulso6_admin_broiler_broilermaster.broiler_farm;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_gc_standard", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_standard LIKE poulso6_admin_broiler_broilermaster.broiler_gc_standard;"; mysqli_query($conn,$sql1); }
if(in_array("farmer_item_price", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.farmer_item_price LIKE poulso6_admin_broiler_broilermaster.farmer_item_price;"; mysqli_query($conn,$sql1); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `broiler_purchases`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("amt_cal_basedon", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `amt_cal_basedon` VARCHAR(300) NULL DEFAULT 'RcvdQty' COMMENT 'Amount Calculation on' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("gst_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `gst_code` VARCHAR(100) NULL DEFAULT NULL COMMENT 'GST Code' AFTER `dis_amt`"; mysqli_query($conn,$sql); }
if(in_array("farmer_price", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `farmer_price` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Farmer Price' AFTER `rate`"; mysqli_query($conn,$sql); }
if(in_array("driver_mobile", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `driver_mobile` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Driver Mobile No' AFTER `driver_code`"; mysqli_query($conn,$sql); }
if(in_array("mnu_tds_edit", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `mnu_tds_edit` INT(100) NOT NULL DEFAULT '0' COMMENT 'Manual TDS Edit Flag' AFTER `gst_amt`"; mysqli_query($conn,$sql); }
if(in_array("ocharge_coa", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `ocharge_coa` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Other Charges CoA' AFTER `round_off`"; mysqli_query($conn,$sql); }
if(in_array("ocharge_amt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `ocharge_amt` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT 'Other Charges Amount' AFTER `ocharge_coa`"; mysqli_query($conn,$sql); }
if(in_array("purdoc_path1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `purdoc_path1` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Purchase Document-1' AFTER `driver_mobile`"; mysqli_query($conn,$sql); }
if(in_array("purdoc_path2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `purdoc_path2` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Purchase Document-2' AFTER `purdoc_path1`"; mysqli_query($conn,$sql); }
if(in_array("purdoc_path3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `purdoc_path3` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Purchase Document-3' AFTER `purdoc_path2`"; mysqli_query($conn,$sql); }

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `extra_access`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("field_value", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `extra_access` ADD `field_value` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `field_function`"; mysqli_query($conn,$sql); }

/*Check for TCS on Stock Flag*/
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Purchase' AND `field_function` LIKE 'TCS amount to item stock' AND `user_access` LIKE 'all'";
$query = mysqli_query($conn,$sql); $fcount = mysqli_num_rows($query); $tcson_item_flag = 1;
if($fcount > 0){ while($row = mysqli_fetch_assoc($query)){ $tcson_item_flag = $row['flag']; } }
else{ $sql = "INSERT INTO `extra_access` (`id`, `field_name`, `field_function`, `field_value`, `user_access`, `flag`) VALUES (NULL, 'Purchase', 'TCS amount to item stock', NULL, 'all', '1');"; mysqli_query($conn,$sql); }
if($tcson_item_flag == ""){ $tcson_item_flag = 1; }

//Document Upload
$folder_path = "documents/".$dbname."/purchase_docs"; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }
if(!empty($_FILES["pur_doc_1"]["name"])) {
    $filename = basename($_FILES["pur_doc_1"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['pur_doc_1']['tmp_name'];
    $purdoc_path1 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$purdoc_path1);
}
else{ $purdoc_path1 = $opur_doc_1; }

if(!empty($_FILES["pur_doc_2"]["name"])) {
    $filename = basename($_FILES["pur_doc_2"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['pur_doc_2']['tmp_name'];
    $purdoc_path2 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$purdoc_path2);
}
else{ $purdoc_path2 = $opur_doc_2; }

if(!empty($_FILES["pur_doc_3"]["name"])) {
    $filename = basename($_FILES["pur_doc_3"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['pur_doc_3']['tmp_name'];
    $purdoc_path3 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$purdoc_path3);
}
else{ $purdoc_path3 = $opur_doc_3; }

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
$sql = "SELECT * FROM `main_groups`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $control_acc_group[$row['code']] = $row['sup_controller_code']; }
$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bag_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `tax_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $gst_coa[$row['code']] = $row['coa_code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `description` = 'Purchase Discount'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $discount_code = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_feed_code[$row['code']] = $row['code']; $item_feed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Purchases' AND `field_function` LIKE 'Bags' AND `flag` = 1"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }

$id = $_POST['idvalue'];
$date = date("Y-m-d",strtotime($_POST['date']));
$vcode = $_POST['vcode'];
$billno = $_POST['billno'];
$tcds_per = $_POST['tcds_per']; if($tcds_per == ""){ $tcds_per = 0; }
$tcds_amt = $_POST['tcds_amount']; if($tcds_amt == ""){ $tcds_amt = 0; }
$mnu_tds_edit = $_POST['mnu_tds_edit']; if($mnu_tds_edit == ""){ $mnu_tds_edit = 0; }
$freight_type = $_POST['freight_type'];
$freight_amt = $_POST['freight_amount']; if($freight_amt == ""){ $freight_amt = 0; }
$freight_pay_type = $_POST['pay_type'];
$freight_pay_acc = $_POST['freight_pay_acc'];
$freight_acc = $_POST['freight_acc'];
$ocharge_coa = $_POST['ocharge_coa'];
$ocharge_amt = $_POST['ocharge_amt']; if($ocharge_amt == ""){ $ocharge_amt = 0; }
$round_off = $_POST['round_off']; if($round_off == ""){ $round_off = 0; }
$finl_amt = $_POST['finl_amt']; if($finl_amt == ""){ $finl_amt = 0; }
$remarks = $_POST['remarks'];
$bag_code = $_POST['bag_code'];
$bag_count = $_POST['bag_count'];
$batch_no = $_POST['batch_no'];
$exp_date = date("Y-m-d",strtotime($_POST['exp_date']));
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$driver_mobile = $_POST['driver_mobile'];
$amt_cal_basedon = $_POST['amt_cal_basedon'];

$tot_rcd_qty = 0;
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
$i = 0; foreach($_POST['snt_qty'] as $snt_qtys){ $snt_qty[$i] = $snt_qtys; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i] = $rcd_qtys; $tot_rcd_qty = $tot_rcd_qty + $rcd_qtys; $i++; }
$i = 0; foreach($_POST['fre_qty'] as $fre_qtys){ $fre_qty[$i] = $fre_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i] = $rates; $i++; }
$i = 0; foreach($_POST['dis_per'] as $dis_pers){ $dis_per[$i] = $dis_pers; $i++; }
$i = 0; foreach($_POST['dis_amt'] as $dis_amts){ $dis_amt[$i] = $dis_amts; $i++; }
$i = 0; foreach($_POST['gst_per'] as $gst_pers){ $gst_per[$i] = $gst_pers; $i++; }
$i = 0; foreach($_POST['item_tamt'] as $item_tamts){ $item_tamt[$i] = $item_tamts; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }
$i = 0; foreach($_POST['farm_batch'] as $farm_batchs){ $farm_batch[$i] = $farm_batchs; $i++; }
$flag = 0;
$active = 1;
$dflag = 0;

//Freight Price Calculations
$freight_price = 0;
if($freight_amt > 0 && $tot_rcd_qty > 0){
    $freight_price = $freight_amt / $tot_rcd_qty;
}
if($freight_price == ""){ $freight_price = 0; }
//TDS Price Calculations
$tds_price = 0;
if($tcds_amt > 0 && $tot_rcd_qty > 0){
    $tds_price = $tcds_amt / $tot_rcd_qty;
}
if($tds_price == ""){ $tds_price = 0; }

//Verify Financial Year
$sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$id' GROUP BY `trnum` ORDER BY `trnum` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $old_date = $row['date']; $old_incr = $row['incr']; $old_prefix = $row['prefix']; $old_trnum = $row['trnum']; }

$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$old_date' AND `tdate` >= '$old_date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $old_pfx = $row['prefix']; }

$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

if($old_pfx == $pfx){
    $incr = $old_incr;
    $prefix = $old_prefix;
    $trnum = $old_trnum;
}
else{
    //Generate Invoice transaction number format
    $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $purchases = $row['purchases']; } $incr = $purchases + 1;
    
    $sql = "UPDATE `master_generator` SET `purchases` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
    
    $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'purchases' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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
}

$sql = "DELETE FROM `broiler_purchases` WHERE `trnum` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error Deletion 1:-".mysqli_error($conn)); }

$sql = "DELETE FROM `account_summary` WHERE `trnum` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error Deletion 2:-".mysqli_error($conn)); }

$dsize = sizeof($icode);
for($i = 0;$i < $dsize;$i++){
    if($snt_qty[$i] == "" || $snt_qty[$i] == NULL || $snt_qty[$i] == 0 || $snt_qty[$i] == "0.00"){ $snt_qty[$i] = 0; }
    if($rcd_qty[$i] == "" || $rcd_qty[$i] == NULL || $rcd_qty[$i] == 0 || $rcd_qty[$i] == "0.00"){ $rcd_qty[$i] = 0; }
    if($fre_qty[$i] == "" || $fre_qty[$i] == NULL || $fre_qty[$i] == 0 || $fre_qty[$i] == "0.00"){ $fre_qty[$i] = 0; }
    if($rate[$i] == "" || $rate[$i] == NULL || $rate[$i] == 0 || $rate[$i] == "0.00"){ $rate[$i] = 0; }
    if($dis_per[$i] == "" || $dis_per[$i] == NULL || $dis_per[$i] == 0 || $dis_per[$i] == "0.00"){ $dis_per[$i] = 0; }
    if($dis_amt[$i] == "" || $dis_amt[$i] == NULL || $dis_amt[$i] == 0 || $dis_amt[$i] == "0.00"){ $dis_amt[$i] = 0; }
    if($gst_per[$i] == "select" || $gst_per[$i] == ""){ $gst_amt[$i] = $gst_per[$i] = 0; }
    else{
        $gst_per1 = explode("@",$gst_per[$i]);
        $gst_code[$i] = $gst_per1[0];
        $gst_value[$i] = $gst_per1[1];
        if($qty_on_sqty_flag == 1 || $qty_on_sqty_flag == "1"){ $gst_amt[$i] = (((float)$gst_value[$i] / 100) * (((float)$snt_qty[$i] * (float)$rate[$i]) - (float)$dis_amt[$i])); }
        else{ $gst_amt[$i] = (((float)$gst_value[$i] / 100) * (((float)$rcd_qty[$i] * (float)$rate[$i]) - (float)$dis_amt[$i])); }
    }
    if($gst_value[$i] == "" || $gst_value[$i] == NULL || $gst_value[$i] == 0 || $gst_value[$i] == "0.00"){ $gst_value[$i] = 0; }
    if($gst_amt[$i] == "" || $gst_amt[$i] == NULL || $gst_amt[$i] == 0 || $gst_amt[$i] == "0.00"){ $gst_amt[$i] = 0; }
    if($item_tamt[$i] == "" || $item_tamt[$i] == NULL || $item_tamt[$i] == 0 || $item_tamt[$i] == "0.00"){ $item_tamt[$i] = 0; }
    if($tcds_amt <= 0){ $tcds_per = $tcds_amt = 0; }
    if($freight_amt <= 0){ $freight_type = $freight_pay_type = $freight_acc = ""; $freight_amt = 0; }
    if($finl_amt == "" || $finl_amt == NULL || $finl_amt == 0 || $finl_amt == "0.00"){ $finl_amt = 0; }
    if($bag_count == "" || $bag_count == NULL || $bag_count == 0 || $bag_count == "0.00"){ $bag_count = 0; }

    if($freight_amt > 0){ if($freight_type == "include"){ $item_freight_amt = 0; } if($freight_type == "exclude" || $freight_type == "inbill"){ $item_freight_amt = $freight_price * $rcd_qty[$i]; } else{ $item_freight_amt = 0; } } else{ $item_freight_amt = 0; }
    if($tcds_amt > 0 && (int)$tcson_item_flag == 1){ $item_tds_amt = $tds_price * $rcd_qty[$i]; } else{ $item_tds_amt = 0; }
    
    $feed_item =  $icode[$i];
    if(!empty($item_feed_name[$feed_item]) && !empty($rcd_qty[$i] && $bag_access_flag > 0)){
        $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$icode[$i]' AND `active` = '1' AND `dflag` = '0'";
        $bquery = mysqli_query($conn,$bsql); $bcount1 = $ibag_flag1 = mysqli_num_rows($bquery);
        if($bcount1 > 0){
            if($ibag_flag1 > 0 && $bag_access_flag > 0){
                while($brow = mysqli_fetch_assoc($bquery)){
                    if($brow['code'] != "all"){
                        $snt_qty[$i] = $snt_qty[$i] * $brow['bag_size'];
                        $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size'];
                        $fre_qty[$i] = $fre_qty[$i] * $brow['bag_size'];
                        $rate[$i] = $rate[$i] / $brow['bag_size'];
                    }
                    else{
                        $snt_qty[$i] = $snt_qty[$i] * $brow['bag_size'];
                        $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size'];
                        $fre_qty[$i] = $fre_qty[$i] * $brow['bag_size'];
                        $rate[$i] = $rate[$i] / $brow['bag_size'];
                    }
                }
            }
        }
        else{
            $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
            if($ibag_flag1 > 0 && $bag_access_flag > 0){
                while($brow = mysqli_fetch_assoc($bquery)){
                    if($brow['code'] != "all"){
                        $snt_qty[$i] = $snt_qty[$i] * $brow['bag_size'];
                        $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size'];
                        $fre_qty[$i] = $fre_qty[$i] * $brow['bag_size'];
                        $rate[$i] = $rate[$i] / $brow['bag_size'];
                    }
                    else{
                        $snt_qty[$i] = $snt_qty[$i] * $brow['bag_size'];
                        $rcd_qty[$i] = $rcd_qty[$i] * $brow['bag_size'];
                        $fre_qty[$i] = $fre_qty[$i] * $brow['bag_size'];
                        $rate[$i] = $rate[$i] / $brow['bag_size'];
                    }
                }
            }
        }
    }

    //$fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    //if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }
    
    //Fetch farmer Medicine Prices
    $region_code = $branch_code = $medicine_cost = ""; $med_price = $farmer_price = 0;
    $sql = "SELECT * FROM `broiler_farm` WHERE `code` LIKE '$warehouse[$i]'"; $query = mysqli_query($conn,$sql); $mdcount = mysqli_num_rows($query);
    if($mdcount > 0){
        while($row = mysqli_fetch_assoc($query)){ $region_code = $row['region_code']; $branch_code = $row['branch_code']; }
        $sql = "SELECT * FROM `broiler_gc_standard` WHERE `region_code` = '$region_code' AND `branch_code` = '$branch_code' AND `from_date` <= '$date' AND `to_date` >= '$date' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql);  $mdcount = mysqli_num_rows($query);
        if($mdcount > 0){
            while($row = mysqli_fetch_assoc($query)){ $medicine_cost = $row['medicine_cost']; $med_price = $row['med_price']; }
            if($medicine_cost == "M"){
                $sql = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$icode[$i]' AND `active` = '1' AND `dflag` = '0' AND `id` IN (SELECT MAX(id) as id FROM `farmer_item_price` WHERE `itemcode` = '$icode[$i]' AND `date` <= '$date' AND `active` = '1' AND `dflag` = '0')";
                $query = mysqli_query($conn,$sql); $mdcount = mysqli_num_rows($query);
                if($mdcount > 0){ while($row = mysqli_fetch_assoc($query)){ $farmer_price = $row['rate']; } }
            }
            else if($medicine_cost == "F"){ $farmer_price = $med_price; } else if($medicine_cost == "A"){ $farmer_price = $rate[$i]; } else{ $farmer_price = $rate[$i]; }
        }
    }
    
    $sql = "INSERT INTO `broiler_purchases` (incr,prefix,trnum,date,vcode,billno,icode,snt_qty,rcd_qty,fre_qty,rate,farmer_price,dis_per,dis_amt,gst_code,gst_per,gst_amt,mnu_tds_edit,tcds_per,tcds_amt,item_tamt,freight_type,freight_amt,freight_pay_type,freight_pay_acc,freight_acc,round_off,ocharge_coa,ocharge_amt,finl_amt,bal_qty,bal_amt,remarks,warehouse,farm_batch,bag_code,bag_count,batch_no,exp_date,vehicle_code,driver_code,driver_mobile,purdoc_path1,purdoc_path2,purdoc_path3,active,flag,dflag,amt_cal_basedon,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date','$vcode','$billno','$icode[$i]','$snt_qty[$i]','$rcd_qty[$i]','$fre_qty[$i]','$rate[$i]','$farmer_price','$dis_per[$i]','$dis_amt[$i]','$gst_code[$i]','$gst_value[$i]','$gst_amt[$i]','$mnu_tds_edit','$tcds_per','$tcds_amt','$item_tamt[$i]','$freight_type','$freight_amt','$freight_pay_type','$freight_pay_acc','$freight_acc','$round_off','$ocharge_coa','$ocharge_amt','$finl_amt','$rcd_qty[$i]','$item_tamt[$i]','$remarks','$warehouse[$i]','$farm_batch[$i]','$bag_code','$bag_count','$batch_no','$exp_date','$vehicle_code','$driver_code','$driver_mobile','$purdoc_path1','$purdoc_path2','$purdoc_path3','$active','$flag','$dflag','$amt_cal_basedon','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }
    else {
        $coa_Dr = $icat_iac[$icat_code[$icode[$i]]]; $coa_Cr = $control_acc_group[$contact_group[$vcode]];
        $amount = 0;
        if($amt_cal_basedon == "SentQty"){
            $amount = $rate[$i] * $snt_qty[$i];
        }
        else{
            $amount = $rate[$i] * $rcd_qty[$i];
        }
        $gst_acc = $gst_coa[$gst_code[$i]];
        $prices = 0; if((float)$rcd_qty[$i] != 0){ $prices = $amount / $rcd_qty[$i]; }
        /* ***** Supplier Quantity ***** */
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$icode[$i]','$rcd_qty[$i]','$prices','$amount','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-RcvQty','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        /* ***** Free Quantity ***** */
        if($fre_qty[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$icode[$i]','$fre_qty[$i]','0','0','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-FreeQty','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 2:-".mysqli_error($conn)); } else{ }
        }

        /* ***** Stock Quantity ***** */
        if($fre_qty[$i] > 0){ $item_stock_qty = $rcd_qty[$i] + $fre_qty[$i]; } else{ $item_stock_qty = $rcd_qty[$i]; }
        $item_avg_amount = $item_tamt[$i] + $item_freight_amt + $item_tds_amt;
        $item_avg_price = $item_avg_amount / $item_stock_qty;
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$coa_Dr','$date','$vcode','$trnum','$icode[$i]','$item_stock_qty','$item_avg_price','$item_avg_amount','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-RcvQty','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }

        /* ***** GST ***** */
        if($gst_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$icode[$i]','0','$gst_value[$i]','$gst_amt[$i]','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 1:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$gst_acc','$date','$vcode','$trnum','$icode[$i]','0','$gst_value[$i]','$gst_amt[$i]','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 2:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$gst_acc','$date','$vcode','$trnum','$icode[$i]','0','$gst_value[$i]','$gst_amt[$i]','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 3:-".mysqli_error($conn)); } else{ }
        }

        /* ***** Discount ***** */
        if($dis_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$discount_code','$date','$vcode','$trnum','$icode[$i]','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$discount_code','$date','$vcode','$trnum','$icode[$i]','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$coa_Cr','$date','$vcode','$trnum','$icode[$i]','0','$dis_per[$i]','$dis_amt[$i]','$warehouse[$i]','$farm_batch[$i]','$remarks','0','Purchase-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
        }
        
    }
}

/* ***** Other Charges ***** */
if($ocharge_amt > 0){
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$coa_Cr','$date','$vcode','$trnum',NULL,'0','0','$ocharge_amt','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-OtherCharges','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 2:-".mysqli_error($conn)); } else{ }
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('DR','$ocharge_coa','$date','$vcode','$trnum',NULL,'0','0','$ocharge_amt','$warehouse[$i]','$farm_batch','$remarks','0','Purchase-OtherCharges','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Free Qty Error 2:-".mysqli_error($conn)); } else{ }
}

/* ***** Freight ***** */
if($freight_amt > 0){
    $coa_Cr = $control_acc_group[$contact_group[$vcode]];
    if($freight_type == "include"){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 1:-".mysqli_error($conn)); }
        else{
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Freight Error 2:-".mysqli_error($conn)); } else{ }

            /*Freight Value to Item Account
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Freight Error 3:-".mysqli_error($conn)); } else{ }*/
        }
    }
    else if($freight_type == "exclude"){
        //Payment method From Cash/Bank
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_pay_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 5:-".mysqli_error($conn)); } else{ }
        //Freight Value to Freight Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 4:-".mysqli_error($conn)); } else{ }
        //Freight Value to Item Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 6:-".mysqli_error($conn)); } else{ }
    }
    else if($freight_type == "inbill"){
        //Freight Value from Supplier Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 8:-".mysqli_error($conn)); } else{ }
        //Freight Value to Freight Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 7:-".mysqli_error($conn)); } else{ }
        //Freight Value to Item Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','Purchase-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 8:-".mysqli_error($conn)); } else{ }
        
    }
    else{ }
}

/* ***** TDS ***** */
if($tcds_amt > 0){
    $sql = "SELECT * FROM `main_tcds` WHERE `type` = 'TDS' AND `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $tds_code = $row['coa']; }
    $coa_Cr = $control_acc_group[$contact_group[$vcode]];
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Purchase-TDS','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("TDS Error 1:-".mysqli_error($conn)); }
    else{
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$tds_code','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Purchase-TDS','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("TDS Error 2:-".mysqli_error($conn)); } else{ }
        if((int)$tcson_item_flag == 1){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$tds_code','$date','$vcode','$trnum','','0','0','$tcds_amt','','','$remarks','0','Purchase-TDS','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("TDS Error 2:-".mysqli_error($conn)); } else{ }
        }
    }
}

header('location:broiler_display_purchase2.php?ccid='.$ccid);
?>