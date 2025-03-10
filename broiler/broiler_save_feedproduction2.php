<?php
//broiler_save_feedproduction2.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedproduction2'];

$sql='SHOW COLUMNS FROM `broiler_feed_production`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("prodoc_path1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_feed_production` ADD `prodoc_path1` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-1' AFTER `remarks`"; mysqli_query($conn,$sql); }
if(in_array("prodoc_path2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_feed_production` ADD `prodoc_path2` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-2' AFTER `prodoc_path1`"; mysqli_query($conn,$sql); }
if(in_array("prodoc_path3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_feed_production` ADD `prodoc_path3` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-3' AFTER `prodoc_path2`"; mysqli_query($conn,$sql); }
if(in_array("other_cost2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_feed_production` ADD `other_cost2` DOUBLE(20,2) NULL DEFAULT NULL COMMENT '' AFTER `other_cost`"; mysqli_query($conn,$sql); }

$folder_path = "documents/".$dbname; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }

if(!empty($_FILES["prod_doc_1"]["name"])) {
    $filename = basename($_FILES["prod_doc_1"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['prod_doc_1']['tmp_name'];
    $prodoc_path1 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$prodoc_path1);
}
else{ $prodoc_path1 = ""; }

if(!empty($_FILES["prod_doc_2"]["name"])) {
    $filename = basename($_FILES["prod_doc_2"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['prod_doc_2']['tmp_name'];
    $prodoc_path2 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$prodoc_path2);
}
else{ $prodoc_path2 = ""; }

if(!empty($_FILES["prod_doc_3"]["name"])) {
    $filename = basename($_FILES["prod_doc_3"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['prod_doc_3']['tmp_name'];
    $prodoc_path3 = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$prodoc_path3);
}
else{ $prodoc_path3 = ""; }

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

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_codes[$row['description']] = $row['code']; $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `main_officetypes` WHERE `description` = 'mill'";  $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $office_code = $row['code']; $office_name = $row['description']; }

$date = date("Y-m-d",strtotime($_POST['date']));
$dcno = $_POST['dcno'];
$feed_mill = $_POST['feed_mill'];
$feed_code = $_POST['feed_code'];
$sql = "SELECT * FROM `price_master` WHERE `transaction_type` LIKE 'Feed Mill' AND `item_code` LIKE '$feed_code' AND `price_type` LIKE 'ProductionProfit' AND `sector_type` LIKE '$office_code' AND `sector_code` LIKE '$feed_mill' AND `active` = '1' AND `dflag` = '0' OR `transaction_type` LIKE 'Feed Mill' AND `item_code` LIKE '$feed_code' AND `price_type` LIKE 'ProductionProfit' AND `sector_type` LIKE '$office_code' AND `sector_code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $feed_margin_count = mysqli_num_rows($query);
if($feed_margin_count > 0){ while($row = mysqli_fetch_assoc($query)){ $feed_margin_type = $row['value_type']; $feed_margin_value = $row['value']; } }
else{ $feed_margin_type = ""; $feed_margin_value = 0; } if($feed_margin_value == ""){ $feed_margin_value = 0; }

$formula_code = $_POST['formula_code'];
$fbatch_no = $_POST['fbatch_no'];
$make_date = date("Y-m-d",strtotime($_POST['make_date']));
$exp_date = date("Y-m-d",strtotime($_POST['exp_date']));
$total_tons = $_POST['total_tons']; if($total_tons == ""){ $total_tons = 0; }
$item_cost = $_POST['input_cost']; if($item_cost == ""){ $item_cost = 0; }
$labour_cost = $_POST['labour_charge']; if($labour_cost == ""){ $labour_cost = 0; }
$packing_cost = $_POST['packing_charge']; if($packing_cost == ""){ $packing_cost = 0; }
$electricity_cost = $_POST['electric_charge']; if($electricity_cost == ""){ $electricity_cost = 0; }
$transport_cost = $_POST['transport_charge']; if($transport_cost == ""){ $transport_cost = 0; }
$other_cost = $_POST['other_charge']; if($other_cost == ""){ $other_cost = 0; }
$other_cost2 = $_POST['other_charge2']; if($other_cost2 == ""){ $other_cost2 = 0; }
$bag_price = $_POST['price_of_bags_feed']; if($bag_price == ""){ $bag_price = 0; }
$bag_amount = $_POST['bag_amount']; if($bag_amount == ""){ $bag_amount = 0; }
$total_cost = $_POST['total_cost']; if($total_cost == ""){ $total_cost = 0; }
$margin_type = $_POST['margin_type'];
$margin_value = $_POST['margin_per']; if($margin_value == ""){ $margin_value = 0; }
$margin_amount = $_POST['margin_amount']; if($margin_amount == ""){ $margin_amount = 0; }
$consumed_quantity = $_POST['consumed_total']; if($consumed_quantity == ""){ $consumed_quantity = 0; }
$produced_quantity = $_POST['produced_total']; if($produced_quantity == ""){ $produced_quantity = 0; }
$produced_price = $_POST['final_item_prod_price']; if($produced_price == ""){ $produced_price = 0; }
$produced_amount = $_POST['final_item_prod_amount']; if($produced_amount == ""){ $produced_amount = 0; }
$wastage_quantity = $_POST['wastage_kg']; if($wastage_quantity == ""){ $wastage_quantity = 0; }
$wastage_per = $_POST['wastage_per']; if($wastage_per == ""){ $wastage_per = 0; }
$wastage_amount = $_POST['wastage_cost']; if($wastage_amount == ""){ $wastage_amount = 0; }
if($wastage_amount > 0 && $wastage_quantity > 0){ $wastage_price = $wastage_amount / $wastage_quantity; } else{ $wastage_price = 0; } if($wastage_price == ""){ $wastage_price = 0; }
$bag_total = $_POST['input_bags']; if($bag_total == ""){ $bag_total = 0; }
$bag_code_feed = $_POST['bag_code_feed'];
$no_of_bags_feed = $_POST['no_of_bags_feed']; if($no_of_bags_feed == ""){ $no_of_bags_feed = 0; }
$bag_code_empty = $_POST['bag_code_empty'];
$no_of_bags_empty = $_POST['no_of_bags_empty']; if($no_of_bags_empty == ""){ $no_of_bags_empty = 0; }
$remarks = $_POST['remarks'];

if(!empty($feed_margin_type)){
    if($feed_margin_type == "Per" || $feed_margin_type == "per"){
        $feed_margin_amount = ($produced_amount * ($feed_margin_value / 100));
    }
    else{
        $feed_margin_amount = $feed_margin_value;
    }
    
}
else{
    $feed_margin_amount = 0;
}
if($feed_margin_amount == ""){ $feed_margin_amount = 0; }

$active = 1; $flag = $dflag = 0;
$item_code = $item_qty = $item_amount = $price = array();
$i = 0; foreach($_POST['itm_names'] as $itm_names){ $item_code[$i] = $itm_names; $i++; }
$i = 0; foreach($_POST['itm_qtys'] as $itm_qtys){ $item_qty[$i] = $itm_qtys; $i++; }
$i = 0; foreach($_POST['itm_amt'] as $itm_amt){ $item_amount[$i] = $itm_amt; $i++; }

$total_quantity = $_POST['final_total_qty'];
$total_amount = $_POST['final_total_amt'];

//Generate Invoice transaction number format
$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_prod = $row['feed_prod']; } $incr = $feed_prod + 1;

$sql = "UPDATE `master_generator` SET `feed_prod` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'feed_prod' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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

$sql = "INSERT INTO `broiler_feed_production` (incr,prefix,code,date,dcno,fbatch_no,make_date,exp_date,feed_mill,feed_code,formula_code,total_tons,consumed_quantity,wastage_quantity,wastage_per,wastage_price,wastage_amount,produced_quantity,produced_price,produced_amount,bag_total,bag_code_feed,no_of_bags_feed,bag_code_empty,no_of_bags_empty,item_cost,labour_cost,packing_cost,electricity_cost,transport_cost,bag_price,bag_amount,other_cost,other_cost2,total_cost,margin_type,margin_value,margin_amount,feed_margin_type,feed_margin_value,feed_margin_amount,remarks,prodoc_path1,prodoc_path2,prodoc_path3,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES 
('$incr','$prefix','$trnum','$date','$dcno','$fbatch_no','$make_date','$exp_date','$feed_mill','$feed_code','$formula_code','$total_tons','$consumed_quantity','$wastage_quantity','$wastage_per','$wastage_price','$wastage_amount','$produced_quantity','$produced_price','$produced_amount','$bag_total','$bag_code_feed','$no_of_bags_feed','$bag_code_empty','$no_of_bags_empty','$item_cost','$labour_cost','$packing_cost','$electricity_cost','$transport_cost','$bag_price','$bag_amount','$other_cost','$other_cost2','$total_cost','$margin_type','$margin_value','$margin_amount','$feed_margin_type','$feed_margin_value','$feed_margin_amount','$remarks','$prodoc_path1','$prodoc_path2','$prodoc_path3','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error-1:-".mysqli_error($conn)); }
else {
    $dsize = sizeof($item_code);
    for($i = 0;$i < $dsize;$i++){
        if($item_qty[$i] == ""){ $item_qty[$i] = 0; }
        if($item_amount[$i] == ""){ $itm_amt[$i] = 0; }
        if($itm_amt[$i] > 0 && $item_qty[$i] > 0){ $price[$i] = round(($item_amount[$i] / $item_qty[$i]),2); } else{ $price[$i] = 0; }
        if($price[$i] == ""){ $price[$i] = 0; }
        if($total_quantity == ""){ $total_quantity = 0; }
        if($total_amount == ""){ $total_amount = 0; }
        $sql = "INSERT INTO `broiler_feed_consumed` (link_trnum,date,feed_mill,feed_code,formula_code,item_code,quantity,price,amount,total_quantity,total_amount,flag,active,dflag) VALUES ('$trnum','$date','$feed_mill','$feed_code','$formula_code','$item_code[$i]','$item_qty[$i]','$price[$i]','$item_amount[$i]','$total_quantity','$total_amount','$flag','$active','$dflag')";
        if(!mysqli_query($conn,$sql)){ die("Error-2:-".mysqli_error($conn)); }
        else {
            $coa_Cr = $icat_iac[$icat_code[$item_code[$i]]];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$dcno','$trnum','$item_code[$i]','$item_qty[$i]','$price[$i]','$item_amount[$i]','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error-3:-".mysqli_error($conn)); }
            else{ }
        }
    }
    
    $sql = "SELECT * FROM `feedmill_expenses_parameters`"; $query = mysqli_query($conn,$sql); $fep_count = mysqli_num_rows($query);
    if($fep_count > 0){
        while($row = mysqli_fetch_assoc($query)){
            if($row['name'] == "labourcharge"){ $labour_cr = $row['coa_code']; }
            if($row['name'] == "packingcharge"){ $packing_cr = $row['coa_code']; }
            if($row['name'] == "transportcharge"){ $transport_cr = $row['coa_code']; }
            if($row['name'] == "electricalcharge"){ $electricity_cr = $row['coa_code']; }
            if($row['name'] == "othercharge"){ $other_cr = $row['coa_code']; }
            if($row['name'] == "othercharge2"){ $other2_cr = $row['coa_code']; }
        }
    }

    $sql = "SELECT * FROM `acc_coa` WHERE `description` IN ('Labour Expenses','Packing Expenses','Transport Expenses','Electricity Expenses','Other Expenses','Other Expenses2','Feed Production (Revenue)') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        if($row['description'] == "Labour Expenses"){
            if($labour_cr == ""){ $labour_cr = $row['code']; }
        }
        else if($row['description'] == "Packing Expenses"){
            if($packing_cr == ""){ $packing_cr = $row['code']; }
        }
        else if($row['description'] == "Electricity Expenses"){
            if($electricity_cr == ""){ $electricity_cr = $row['code']; }
        }
        else if($row['description'] == "Transport Expenses"){
            if($transport_cr == ""){ $transport_cr = $row['code']; }
        }
        else if($row['description'] == "Other Expenses"){
            if($other_cr == ""){ $other_cr = $row['code']; }
        }
        else if($row['description'] == "Other Expenses2"){
            if($other2_cr == ""){ $other2_cr = $row['code']; }
        }
        else if($row['description'] == "Feed Production (Revenue)"){
            if($margin_cr == ""){ $margin_cr = $row['code']; }
        }
        else{ }
    }
    //Labour Summary
    if((float)$labour_cost != 0 && $labour_cost != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$labour_cr','$date','$dcno','$trnum','0.00','0.00','$labour_cost','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-4:-".mysqli_error($conn)); } else{ }
    }
    //Packing Summary
    if((float)$packing_cost != 0 && $packing_cost != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$packing_cr','$date','$dcno','$trnum','0.00','0.00','$packing_cost','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-5:-".mysqli_error($conn)); } else{ }
    }
    //Electricity Summary
    if((float)$electricity_cost != 0 && $electricity_cost != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$electricity_cr','$date','$dcno','$trnum','0.00','0.00','$electricity_cost','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-6:-".mysqli_error($conn)); } else{ }
    }
    //Transport Summary
    if((float)$transport_cost != 0 && $transport_cost != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$transport_cr','$date','$dcno','$trnum','0.00','0.00','$transport_cost','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-7:-".mysqli_error($conn)); } else{ }
    }
    //Bag Summary
    if((float)$no_of_bags_feed != 0 && $bag_code_feed != ""){
        $bag_cr =  $icat_iac[$icat_code[$bag_code_feed]];
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$bag_cr','$date','$dcno','$trnum','$bag_code_feed','$no_of_bags_feed','$bag_price','$bag_amount','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-8:-".mysqli_error($conn)); } else{ }
    }
    //Other Summary
    if((float)$other_cost != 0 && $other_cost != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$other_cr','$date','$dcno','$trnum','0.00','0.00','$other_cost','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-8:-".mysqli_error($conn)); } else{ }
    }
    //Other Summary
    if((float)$other_cost2 != 0 && $other_cost2 != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$other2_cr','$date','$dcno','$trnum','0.00','0.00','$other_cost2','$feed_mill','$remarks','0','FeedConsumption','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-8:-".mysqli_error($conn)); } else{ }
    }
    //Margin Summary
    if((float)$margin_amount != 0 && $margin_amount != ""){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$margin_cr','$date','$dcno','$trnum','0.00','0.00','$margin_amount','$feed_mill','$remarks','0','FeedProdMargin','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error-8:-".mysqli_error($conn)); } else{ }
    }
    //Feed Produced
    $coa_Dr = $icat_iac[$icat_code[$feed_code]];
    $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('DR','$coa_Dr','$date','$dcno','$trnum','$feed_code','$produced_quantity','$produced_price','$produced_amount','$feed_mill','$remarks','0','FeedProduction','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error-9:-".mysqli_error($conn)); } else{ }
}
header('location:broiler_display_feedproduction2.php?ccid='.$ccid);

?>