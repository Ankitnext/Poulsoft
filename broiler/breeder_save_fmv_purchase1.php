<?php
//breeder_save_generalpurchase2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['generalpurchase2'];

$date = $transportor_name = $billno = $trnum = $vehicle_code = $driver_code = $driver_mobile = "";
$item_code = $uom = $sn_qty = $rcd_qty = $fre_qty = $rate = $farmer_price = $dis_per = $dis_price = $gst_per = $amount = $shed_code =  $batch =  array();
$date = date("Y-m-d",strtotime($_POST['date']));
$transportor_name = $_POST['transportor_name']; 
$billno = $_POST['billno']; 
$trnum = $_POST['trnum']; 
$vehicle_code = $_POST['vehicle_code']; 
$driver_code = $_POST['driver_code']; 
$driver_mobile = $_POST['driver_mobile']; 
 
$i = 0; foreach($_POST['item_code'] as $item_codes){ $item_code[$i] = $item_codes; $i++; }
$i = 0; foreach($_POST['uom'] as $uoms){ $uom[$i] = $uoms; $i++; }
$i = 0; foreach($_POST['sn_qty'] as $sn_qtys){ $sn_qty[$i] = $sn_qtys; $i++; }
$i = 0; foreach($_POST['rcd_qty'] as $rcd_qtys){ $rcd_qty[$i] = $rcd_qtys; $i++; }
$i = 0; foreach($_POST['fre_qty'] as $fre_qtys){ $fre_qty[$i] = $fre_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i] = $rates; $i++; }
$i = 0; foreach($_POST['farmer_price'] as $farmer_prices){ $farmer_price[$i] = $farmer_prices; $i++; }
$i = 0; foreach($_POST['dis_per'] as $dis_pers){ $dis_per[$i] = $dis_pers; $i++; }
$i = 0; foreach($_POST['dis_price'] as $dis_prices){ $dis_price[$i] = $dis_prices; $i++; }
$i = 0; foreach($_POST['gst_per'] as $gst_pers){ $gst_per[$i] = $gst_pers; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['shed_code'] as $shed_codes){ $shed_code[$i] = $shed_codes; $i++; }
$i = 0; foreach($_POST['batch'] as $batchs){ $batch[$i] = $batchs; $i++; }

$flag = $dflag = 0; $active = 1;
$trtype = "generalpurchase2";
$trlink = "breeder_display_fmv_purchase1.php";

$dsize = sizeof($item_code);
for($i = 0;$i < $dsize;$i++){
    if($quantity[$i] == "") { $quantity[$i] = 0; }
    if($price[$i] == "") { $price[$i] = 0; }
    if($amount[$i] == "") { $amount[$i] = 0; }
    
    $sql = "INSERT INTO `broiler_purchases` (`date`,`transportor_name`,`billno`,`trnum`,`vehicle_code`,`driver_code`,`driver_mobile`,`item_code`,`uom`,`sn_qty`,`rcd_qty`,`fre_qty`,`rate`,`farmer_price`,`dis_per`,`dis_price`,`gst_per`,`amount`,`shed_code`,`shed_code`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$date','$transportor_name','$billno','$trnum','$vehicle_code','$driver_code','$driver_mobile','$item_code[$i]','$uom[$i]','$sn_qty[$i]','$rcd_qty[$i]','$fre_qty[$i]','$rate[$i]','$farmer_price[$i]','$dis_per[$i]','$dis_price[$i]','$gst_per[$i]','$amount[$i]','$shed_code[$i]','$batch[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    
    else {
        $coa_Dr = $coa_Cr = $icat_iac[$icat_code[$code[$i]]];
        $from_post = "INSERT INTO account_summary (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','$mgmt_price[$i]','$amount','$fromshed_code','$from_batch','$vehicle_code','$driver_code','$remarks[$i]','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); }
        else{
            $to_post = "INSERT INTO account_summary (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
            VALUES ('DR','$coa_Dr','$date','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','$mgmt_price[$i]','$amount','$toshed_code[$i]','$to_batch','$vehicle_code','$driver_code','$remarks[$i]','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$to_post)){ die("Error:-".mysqli_error($conn)); } else{ }
        }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Stock Transfer?");
    if(x == true){
        window.location.href = "breeder_add_generalpurchase2.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_fmv_purchase1.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_fmv_purchase1.php?ccid="+a;
    }
</script>