<?php
//layer_save_stocktransfer1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['stocktransfer1'];

$ids = $_POST['idvalue']; $incr = $prefix = $trnum = $aemp = $atime = "";
$sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($trnum != ""){
    $sql3 = "DELETE FROM `item_stocktransfers` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
    $sql3 = "DELETE FROM `account_summary` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

$sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Stock Transfer' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $bfstk_bags = mysqli_num_rows($query);

$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_iac = $icat_cogsac = $icat_wpac = $icat_sac = $icat_srac = array();
while($row = mysqli_fetch_assoc($query)){
    $icat_iac[$row['code']] = $row['iac'];
    $icat_cogsac[$row['code']] = $row['cogsac'];
    $icat_wpac[$row['code']] = $row['wpac'];
    $icat_sac[$row['code']] = $row['sac'];
    $icat_srac[$row['code']] = $row['srac'];
}

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$date = date("Y-m-d",strtotime($_POST['date']));
$dcno = $_POST['dcno'];
$fromwarehouse = $_POST['fromwarehouse'];
$code = $_POST['code'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];
$towarehouse = $_POST['towarehouse'];
$remarks = $_POST['remarks'];
$item_sqty = $_POST['item_sqty'];
$item_sprc = $_POST['item_sprc'];

$flag = $dflag = 0; $active = 1;
$trtype = "stocktransfer1";
$trlink = "layer_display_stocktransfer1.php";

if($quantity == ""){ $quantity = 0; }
if($price == ""){ $price = 0; }
if($item_sqty == ""){ $item_sqty = 0; }
if($item_sprc == ""){ $item_sprc = 0; }

//Feed Consumption in Bags
if((int)$bfstk_bags == 1){
    $fsql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$code' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
    $fquery1 = mysqli_query($conn,$fsql1); $f_cnt1 = mysqli_num_rows($fquery1);
    if($f_cnt1 > 0){ while($frow1 = mysqli_fetch_assoc($fquery1)){ $quantity = $quantity * $frow1['bag_size']; $price = $price / $frow1['bag_size']; } }
}

$amount = round(((float)$quantity * (float)$price),2);
$sql = "INSERT INTO `item_stocktransfers` (`incr`,`prefix`,`trnum`,`date`,`dcno`,`fromwarehouse`,`code`,`quantity`,`price`,`amount`,`towarehouse`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
VALUES('$incr','$prefix','$trnum','$date','$dcno','$fromwarehouse','$code','$quantity','$price','$amount','$towarehouse','$remarks','$flag','$active','$dflag','$trtype','$trlink','$aemp','$atime','$addedemp','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
else{
    $coa_Cr = $coa_Dr = $icat_iac[$icat_code[$code]];
    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
    VALUES ('CR','$coa_Cr','$date','$trnum','$code','$quantity','$price','$amount','$fromwarehouse','$remarks','0','layer-Stock Transfer Out','$flag','$active','$dflag','$aemp','$atime','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
    $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedemp`,`updatedtime`) 
    VALUES ('DR','$coa_Dr','$date','$trnum','$code','$quantity','$price','$amount','$towarehouse','$remarks','0','layer-Stock Transfer In','$flag','$active','$dflag','$aemp','$atime','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Stock Transfers?");
    if(x == true){
        window.location.href = "layer_add_stocktransfer1.php";
    }
    else if(x == false) {
        window.location.href = "layer_display_stocktransfer1.php?ccid="+a;
    }
    else {
        window.location.href = "layer_display_stocktransfer1.php?ccid="+a;
    }
</script>