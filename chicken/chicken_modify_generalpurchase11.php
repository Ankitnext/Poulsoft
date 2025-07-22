<?php
//chicken_modify_generalpurchase11.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Purchase Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$warehouse = $_POST['warehouse'];
$tcds_per = $_POST['tcds_per'];

$old_trnum = $vcode = $bookinvoice = $icode = $jals = $birds = $tweight = $eweight = $nweight = $price = $item_amt = $tcds_chk = $tcds_amt = $rndoff_chk = $roundoff = $finaltotal = $remarks = array();
$i = 0; foreach($_POST['trnum'] as $trnums){ $old_trnum[$i] = $trnums; $i++; }
$i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i] = $vcodes; $i++; }
$i = 0; foreach($_POST['bookinvoice'] as $bookinvoices){ $bookinvoice[$i] = $bookinvoices; $i++; }
$i = 0; foreach($_POST['icode'] as $icodes){ $icode[$i] = $icodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['item_amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
$i = 0; foreach($_POST['tcds_chk'] as $tcds_chks){ $tcds_chk[$i] = $tcds_chks; $i++; }
$i = 0; foreach($_POST['tcds_amt'] as $tcds_amts){ $tcds_amt[$i] = $tcds_amts; $i++; }
$i = 0; foreach($_POST['rndoff_chk'] as $rndoff_chks){ $rndoff_chk[$i] = $rndoff_chks; $i++; }
$i = 0; foreach($_POST['roundoff'] as $roundoffs){ $roundoff[$i] = $roundoffs; $i++; }
$i = 0; foreach($_POST['finaltotal'] as $finaltotals){ $finaltotal[$i] = $finaltotals; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$tcds_type1 = $roundoff_type1 = "auto";
$tcds_type2 = "add";
$transporter_code = $driver = $vehicle = "";
$freight_amt = 0;

$tot_jals = $_POST['tot_jals'];
$tot_birds = $_POST['tot_birds'];
$tot_tweight = $_POST['tot_tweight'];
$tot_eweight = $_POST['tot_eweight'];
$tot_nweight = $_POST['tot_nweight'];
$tot_item_amt = $_POST['tot_item_amt'];
$tot_tcds_amt = $_POST['tot_tcds_amt'];
$tot_finl_amt = $_POST['tot_finl_amt'];

$authorization = 0;
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "generalpurchase11";
$trlink = "chicken_display_generalpurchase11.php";
$trip_trnum = $_POST['idvalue'];

//Update deletion flag via Trip No.
$trno_list = implode("','",$old_trnum);

$sql = "UPDATE `pur_purchase` SET `flag` = '0',`active` = '0',`tdflag` = '2',`pdflag` = '2' WHERE `invoice` NOT IN ('$trno_list') AND `link_trnum` = '$trip_trnum' AND `flag` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
mysqli_query($conn,$sql);

//Save Purchase
$dsize = sizeof($icode);
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($item_amt[$i] == ""){ $item_amt[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt[$i] == ""){ $tcds_amt[$i] = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($finaltotal[$i] == ""){ $finaltotal[$i] = 0; }
    if($roundoff[$i] == ""){ $roundoff[$i] = 0; }

    $net_amt1 = (float)$item_amt[$i] + (float)$tcds_amt[$i];
    if((float)$roundoff[$i] >= 0){ $roundoff_type2 = "add"; } else{ $roundoff_type2 = "deduct"; }

    if($old_trnum[$i] != ""){
        $ids = $old_trnum[$i]; $incr = 0; $prefix = $trnum = $aemp = $atime = "";
        $sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['invoice']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
        }
        if($trnum != ""){

            include_once("poulsoft_store_chngmaster.php");
            $chng_type = "Edit";
            $edit_file = "chicken_modify_generalpurchase11.php";
            $mtbl_name = "pur_purchase";
            $tno_cname = "invoice";
            $msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
            $message = json_encode($msg1);
            store_modified_details($message);

            $sql3 = "DELETE FROM `pur_purchase` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'";
            if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
        }
    }
    else{
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($date,"generalpurchase11","PTI","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2];
        $aemp = $addedemp; $atime = $addedtime;
    }
    $sql = "INSERT INTO `pur_purchase` (date,incr,d,m,y,fy,invoice,link_trnum,bookinvoice,vendorcode,itemcode,jals,birds,totalweight,emptyweight,netweight,itemprice,totalamt,tcdsper,tcds_type1,tcds_type2,tcdsamt,net_amt1,transporter_code,freight_amount,roundoff_type1,roundoff_type2,roundoff,finaltotal,balance,amtinwords,warehouse,flag,active,authorization,tdflag,pdflag,drivercode,vehiclecode,discounttype,discountvalue,taxtype,taxvalue,discountamt,taxamount,taxcode,discountcode,remarks,addedemp,addedtime,updatedemp,updated,client,trtype,trlink) 
    VALUES ('$date','$incr','$d','$m','$y','$prefix','$trnum','$trip_trnum','$bookinvoice[$i]','$vcode[$i]','$icode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$item_amt[$i]','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt[$i]','$net_amt1','$transporter_code','$freight_amt','$roundoff_type1','$roundoff_type2','$roundoff[$i]','$finaltotal[$i]','$finaltotal[$i]',NULL,'$warehouse','$flag','$active','$authorization','$tdflag','$pdflag','$driver','$vehicle',NULL,'0',NULL,'0','0','0',NULL,NULL,'$remarks[$i]','$aemp','$atime','$addedemp','$addedtime','$client','$trtype','$trlink')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

header('location:chicken_display_generalpurchase11.php?ccid='.$ccid);

