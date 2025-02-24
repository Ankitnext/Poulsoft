<?php
//breeder_delete_fmv_purchase1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['generalpurchase2'];

$id = $_POST['idvalue'];
$date = $_POST['date'];
$transportor_name = $_POST['transportor_name'];
$billno = $_POST['billno'];
$trnum = $_POST['trnum'];
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$driver_mobile = $_POST['driver_mobile'];
$item_code = $_POST['item_code'];
$uom = $_POST['uom'];
$net_qty = $_POST['net_qty'];
$fre_qty = $_POST['fre_qty'];
$rate = $_POST['rate'];
$farmer_price = $_POST['farmer_price'];
$dis_per = $_POST['dis_per'];
$dis_price = $_POST['dis_price'];
$gst_per = $_POST['gst_per'];
$amount = $_POST['amount'];
$warehouse = $_POST['warehouse'];
$batch = $_POST['batch'];

$sql = "UPDATE `broiler_purchases` SET `date` = '$date',`transportor_name` = '$transportor_name',`billno` = '$billno',`trnum` = '$trnum',`vehicle_code` = '$vehicle_code',`driver_code`='$driver_code',`driver_mobile`='$driver_mobile',`item_code`='$item_code',`uom`='$uom',`net_qty`='$net_qty',`fre_qty`='$fre_qty',`rate`='$rate',`farmer_price`='$farmer_price',`dis_per`='$dis_per',`gst_per`='$gst_per',`amount`='$amount',`warehouse`='$warehouse',`batch`='$batch',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); }

else {
    $coa_Dr = $coa_Cr = $icat_iac[$icat_code[$code[$i]]];
    $from_post = "INSERT INTO account_summary (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
    VALUES ('CR','$coa_Cr','$date','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','$mgmt_price[$i]','$amount','$fromwarehouse','$from_batch','$vehicle_code','$driver_code','$remarks[$i]','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); }
    else{
        $to_post = "INSERT INTO account_summary (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,addedemp,addedtime,updatedtime) 
        VALUES ('DR','$coa_Dr','$date','$dcno[$i]','$trnum','$code[$i]','$quantity[$i]','$mgmt_price[$i]','$amount','$towarehouse[$i]','$to_batch','$vehicle_code','$driver_code','$remarks[$i]','0','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$to_post)){ die("Error:-".mysqli_error($conn)); } else{ }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    window.location.href = "breeder_display_fmv_purchase1.php?ccid="+a;
</script>