<?php
//broiler_save_pc_goodsreceipt.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['pc_goodsreceipt'];
$user_code = $_SESSION['userid'];

$sql='SHOW COLUMNS FROM `master_generator`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("goods_receipt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `goods_receipt` INT(100) NOT NULL DEFAULT '0' COMMENT 'Purchase Order' AFTER `wapp`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'goods_receipt' AND `active` = '1'";
$query = mysqli_query($conn,$sql); $prx_entry_count = mysqli_num_rows($query);
if($prx_entry_count > 0){ } else{ $sql = "INSERT INTO `prefix_master` (`format`, `transaction_type`, `prefix`, `incr_wspb_flag`, `sfin_year_flag`, `sfin_year_wsp_flag`, `efin_year_flag`, `efin_year_wsp_flag`, `day_flag`, `day_wsp_flag`, `month_flag`, `month_wsp_flag`, `year_flag`, `year_wsp_flag`, `hour_flag`, `hour_wsp_flag`, `minute_flag`, `minute_wsp_flag`, `second_flag`, `second_wsp_flag`, `active`) VALUES ('column:flag', 'goods_receipt', 'PGR-', '0', '1:1', '1', '0', '2:1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1');"; mysqli_query($conn,$sql); }

$po_id = $po_trnum = $item_code = $req_qty = $rcvd_qty = $rate = $amount = $disc_per = $disc_amt = $gst_val = $gst_amt = $item_amt = $warehouse = array(); $tot_rcd_qty = 0;
$wpo1_flag = 0;
if($_POST['indent_status'] == "wpo"){
    $i = 0; 
    foreach($_POST['slno'] as $slno){
        $opt_details = explode("@",$_POST['po_val'][$slno]);
        $po_id[$i] = $opt_details[0];
        $po_trnum[$i] = $opt_details[1];
        $item_code[$i] = $_POST['item_code'][$slno];
        $req_qty[$i] = $_POST['req_qty'][$slno];
        $rcvd_qty[$i] = $_POST['rcvd_qty'][$slno];
        $rate[$i] = $_POST['rate'][$slno];
        $amount[$i] = $_POST['amount1'][$slno];
        $disc_per[$i] = $_POST['disc_per'][$slno];
        $disc_amt[$i] = $_POST['disc_amt'][$slno];
        $gst_val[$i] = $_POST['gst_per'][$slno];
        $gst_amt[$i] = $_POST['gst_amt'][$slno];
        $item_amt[$i] = $_POST['item_amt'][$slno];
        $warehouse[$i] = $_POST['warehouse'][$slno];

        $tot_rcd_qty = (float)$tot_rcd_qty + (float)$_POST['rcvd_qty'][$slno];
        $i++;
    }
    $wpo1_flag = 1;
}
else if($_POST['indent_status'] == "wopo"){
    $i = 0; foreach($_POST['item_code'] as $item_codes){ $item_code[$i] = $item_codes; $i++; }
    $i = 0; foreach($_POST['rcvd_qty'] as $rcvd_qtys){ $rcvd_qty[$i] = $rcvd_qtys; $tot_rcd_qty = (float)$tot_rcd_qty + (float)$rcvd_qtys; $i++; }
    $i = 0; foreach($_POST['rate'] as $rates){ $rate[$i] = $rates; $i++; }
    $i = 0; foreach($_POST['amount1'] as $amount1s){ $amount[$i] = $amount1s; $i++; }
    $i = 0; foreach($_POST['disc_per'] as $disc_pers){ $disc_per[$i] = $disc_pers; $i++; }
    $i = 0; foreach($_POST['disc_amt'] as $disc_amts){ $disc_amt[$i] = $disc_amts; $i++; }
    $i = 0; foreach($_POST['gst_per'] as $gst_pers){ $gst_val[$i] = $gst_pers; $i++; }
    $i = 0; foreach($_POST['gst_amt'] as $gst_amts){ $gst_amt[$i] = $gst_amts; $i++; }
    $i = 0; foreach($_POST['item_amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
    $i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }
}
else{ }

$vcode = $_POST['vcode'];
$billno = $_POST['billno'];
$remarks = $_POST['remarks'];
$date = date('Y-m-d',strtotime($_POST['date']));

$freight_type = $_POST['freight_type'];
$freight_pay_type = $_POST['pay_type'];
$freight_pay_acc = $_POST['freight_pay_acc'];
$freight_acc = $_POST['freight_acc'];
$freight_amt = $_POST['freight_amount']; if($freight_amt == ""){ $freight_amt = 0; }

$round_off = $_POST['round_off']; if($round_off == ""){ $round_off = 0; }
$finl_amt = $_POST['finl_amt']; if($finl_amt == ""){ $finl_amt = 0; }

//Freight Price Calculations
if($tot_rcd_qty == ""){ $tot_rcd_qty = 0; }
$freight_price = 0;
if((float)$freight_amt > 0 && (float)$tot_rcd_qty > 0){
    $freight_price = (float)$freight_amt / (float)$tot_rcd_qty;
} if($freight_price == ""){ $freight_price = 0; }
$active = 1;
$flag = $dflag = $pinv_flag = 0;
$trtype = "pc_goodsreceipt";
$trlink = "broiler_display_pc_goodsreceipt.php";

//Generate Invoice transaction number format
$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $goods_receipt = $row['goods_receipt']; } $incr = $goods_receipt + 1;

$sql = "UPDATE `master_generator` SET `goods_receipt` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

$sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'goods_receipt' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
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

$icat_iac = $icat_pvac = $icat_pdac = $icat_cogsac = $icat_wpac = $icat_sac = $icat_srac = $control_acc_group = $contact_group = $icat_code = $gst_coa = array();
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

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `tax_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $gst_coa[$row['code']] = $row['coa_code']; }

$discount_code = $supcon_code = "";
$sql = "SELECT * FROM `acc_coa` WHERE `description` = 'Purchase Discount'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $discount_code = $row['code']; }

$sql = "SELECT * FROM `acc_coa` WHERE `description` = 'Supplier Controller Account'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supcon_code = $row['code']; }

$dsize = sizeof($item_code);
for($i = 0;$i < $dsize;$i++){
    if($req_qty[$i] == ""){ $req_qty[$i] = 0; }
    if($rcvd_qty[$i] == ""){ $rcvd_qty[$i] = 0; }
    if($rate[$i] == ""){ $rate[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($disc_per[$i] == ""){ $disc_per[$i] = 0; }
    if($disc_amt[$i] == ""){ $disc_amt[$i] = 0; }
    if($gst_amt[$i] == ""){ $gst_amt[$i] = 0; }
    if($item_amt[$i] == ""){ $item_amt[$i] = 0; }

    if($wpo1_flag == 1 && $po_id[$i] != "direct"){
        $sql = "SELECT * FROM `broiler_pc_purchaseorder` WHERE `id` = '$po_id[$i]'"; $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
        if($count > 0){
            while($row = mysqli_fetch_assoc($query)){ $avl_qty = $row['avl_qty']; }
            $avl_qty = (float)$avl_qty - (float)$rcvd_qty[$i]; if($avl_qty == ""){ $avl_qty = 0; }
            if($avl_qty <= 0){ $cls_flag = 1; } else{ $cls_flag = 0; }
            $sql = "UPDATE `broiler_pc_purchaseorder` SET `gr_flag` = '1',`gr_trnum` = '$trnum',`gr_emp` = '$addedemp',`gr_time` = '$addedtime',`avl_qty` = '$avl_qty',`cls_flag` = '$cls_flag',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$po_id[$i]'";
            if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); } else{ }
        }
        $wpo_flag = 1; $link_id[$i] = $po_id[$i];
    }
    else if($wpo1_flag == 1 && $po_id[$i] == "direct"){ $po_trnum[$i] = $link_id[$i] = NULL; $wpo_flag = 0; }
    else{ $po_trnum[$i] = $link_id[$i] = NULL; $wpo_flag = 0; }

    $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$warehouse[$i]' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
    if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $farm_batch = $frow['code']; } } else{ $farm_batch = ''; }

    //GST Calculations
    if((float)$gst_amt[$i] > 0){ $gst_val2 = explode("@",$gst_val[$i]); $gst_code = $gst_val2[0]; $gst_per = $gst_val2[1]; } else{ $gst_code = ""; $gst_per = 0; }
    if($gst_per == ""){ $gst_per = 0; }
    
    //Freight Calculations
    $avg_price = $avg_amount = 0;
    if($freight_amt > 0){ if($freight_type == "include"){ $item_freight_amt = 0; } if($freight_type == "exclude" || $freight_type == "inbill"){ $item_freight_amt = $freight_price * $rcvd_qty[$i]; } else{ $item_freight_amt = 0; } } else{ $item_freight_amt = 0; }
    
    if($item_freight_amt == ""){ $item_freight_amt = 0; }
    $avg_amount = $item_amt[$i] + $item_freight_amt;
    $avg_price = $avg_amount / $rcvd_qty[$i];
    if($avg_amount == ""){ $avg_amount = 0; }
    if($avg_price == ""){ $avg_price = 0; }

    $sql = "INSERT INTO `broiler_pc_goodsreceipt` (`incr`,`prefix`,`trnum`,`po_id`,`po_trnum`,`billno`,`date`,`vcode`,`item_code`,`order_qty`,`rcvd_qty`,`rate`,`amount`,`disc_per`,`disc_amt`,`gst_code`,`gst_per`,`gst_amt`,`item_amt`,`freight_type`,`freight_amt`,`freight_pay_type`,`freight_pay_acc`,`freight_acc`,`round_off`,`finl_amt`,`avg_price`,`avg_amount`,`warehouse`,`farm_batch`,`remarks`,`flag`,`active`,`dflag`,`pinv_flag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$trnum','$link_id[$i]','$po_trnum[$i]','$billno','$date','$vcode','$item_code[$i]','$req_qty[$i]','$rcvd_qty[$i]','$rate[$i]','$amount[$i]','$disc_per[$i]','$disc_amt[$i]','$gst_code','$gst_per','$gst_amt[$i]','$item_amt[$i]','$freight_type','$freight_amt','$freight_pay_type','$freight_pay_acc','$freight_acc','$round_off','$finl_amt','$avg_price','$avg_amount','$warehouse[$i]','$farm_batch','$remarks','$flag','$active','$dflag','$pinv_flag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 3:-".mysqli_error($conn)); }
    else{
        $coa_Dr = $icat_iac[$icat_code[$item_code[$i]]]; $coa_Cr = $supcon_code;
        /* ***** Supplier Quantity ***** */
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$item_code[$i]','$rcvd_qty[$i]','$rate[$i]','$item_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','GRN-StkIn','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

        /* ***** Stock Quantity ***** */
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$coa_Dr','$date','$vcode','$trnum','$item_code[$i]','$rcvd_qty[$i]','$avg_price','$avg_amount','$warehouse[$i]','$farm_batch','$remarks','0','GRN-StkIn','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }
        
        /* ***** GST ***** */
        if($gst_amt[$i] > 0){
            $gst_acc = $gst_coa[$gst_code];
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','$item_code[$i]','0','$gst_per','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','GRN-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 1:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$gst_acc','$date','$vcode','$trnum','$item_code[$i]','0','$gst_per','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','GRN-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 2:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$gst_acc','$date','$vcode','$trnum','$item_code[$i]','0','$gst_per','$gst_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','GRN-GST','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("GST Error 3:-".mysqli_error($conn)); } else{ }
        }

        /* ***** Discount ***** */
        if($disc_amt[$i] > 0){
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$discount_code','$date','$vcode','$trnum','$item_code[$i]','0','$disc_per[$i]','$disc_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','GRN-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$discount_code','$date','$vcode','$trnum','$item_code[$i]','0','$disc_per[$i]','$disc_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','GRN-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$coa_Cr','$date','$vcode','$trnum','$item_code[$i]','0','$disc_per[$i]','$disc_amt[$i]','$warehouse[$i]','$farm_batch','$remarks','0','GRN-Discount','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Discount Error:-".mysqli_error($conn)); } else{ }
        }
    }
}
/* ***** Freight ***** */
if($freight_amt > 0){
    $coa_Cr = $supcon_code;
    if($freight_type == "include"){
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 1:-".mysqli_error($conn)); }
        else{
            $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightI','0','1','0','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Freight Error 2:-".mysqli_error($conn)); } else{ }
        }
    }
    else if($freight_type == "exclude"){
        //Payment method From Cash/Bank
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_pay_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 5:-".mysqli_error($conn)); } else{ }
        //Freight Value to Freight Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 4:-".mysqli_error($conn)); } else{ }
        //Freight Value to Item Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightE','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 6:-".mysqli_error($conn)); } else{ }
    }
    else if($freight_type == "inbill"){
        //Freight Value from Supplier Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 8:-".mysqli_error($conn)); } else{ }
        //Freight Value to Freight Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 7:-".mysqli_error($conn)); } else{ }
        //Freight Value to Item Account
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,vendor,trnum,item_code,quantity,price,amount,location,batch,remarks,gc_flag,etype,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$freight_acc','$date','$vcode','$trnum','','0','0','$freight_amt','','','$remarks','0','GRN-FreightB','0','1','0','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Freight Error 8:-".mysqli_error($conn)); } else{ }
        
    }
    else{ }
}
header('location:broiler_display_pc_goodsreceipt.php?ccid='.$ccid);

?>