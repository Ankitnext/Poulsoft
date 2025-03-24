<?php
//breeder_save_stocktransfer1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['stocktransfer1'];

$sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Stock Transfer' AND `field_function` = 'Feed Stock in Bags' AND `user_access` = 'all' AND `flag` = '1'";
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

$date = $dcno = $fromwarehouse = $code = $quantity = $price = $towarehouse = $remarks = $item_sqty = $item_sprc = array();
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d",strtotime($dates)); $i++; }
$i = 0; foreach($_POST['dcno'] as $dcnos){ $dcno[$i] = $dcnos; $i++; }
$i = 0; foreach($_POST['fromwarehouse'] as $fromwarehouses){ $fromwarehouse[$i] = $fromwarehouses; $i++; }
$i = 0; foreach($_POST['code'] as $codes){ $code[$i] = $codes; $i++; }
$i = 0; foreach($_POST['quantity'] as $quantitys){ $quantity[$i] = $quantitys; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['towarehouse'] as $towarehouses){ $towarehouse[$i] = $towarehouses; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$i = 0; foreach($_POST['item_sqty'] as $item_sqtys){ $item_sqty[$i] = $item_sqtys; $i++; }
$i = 0; foreach($_POST['item_sprc'] as $item_sprcs){ $item_sprc[$i] = $item_sprcs; $i++; }

$flag = $dflag = 0; $active = 1; 
$trtype = "stocktransfer1";
$trlink = "breeder_display_stocktransfer1.php";

$dsize = sizeof($code);
for($i = 0;$i < $dsize;$i++){
    if($quantity[$i] == ""){ $quantity[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($item_sqty[$i] == ""){ $item_sqty[$i] = 0; }
    if($item_sprc[$i] == ""){ $item_sprc[$i] = 0; }

    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $fyear = "";
    $trno_dt1 = generate_transaction_details($date[$i],"stocktransfer1","BST","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];
    
    //Feed Consumption in Bags
    if((int)$bfstk_bags == 1){
        $fsql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$code[$i]' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $fquery1 = mysqli_query($conn,$fsql1); $f_cnt1 = mysqli_num_rows($fquery1);
        if($f_cnt1 > 0){ while($frow1 = mysqli_fetch_assoc($fquery1)){ $quantity[$i] = $quantity[$i] * $frow1['bag_size']; $price[$i] = $price[$i] / $frow1['bag_size']; } }
    }

    $amount = round(((float)$quantity[$i] * (float)$price[$i]),2);
    $sql = "INSERT INTO `item_stocktransfers` (`incr`,`prefix`,`trnum`,`date`,`dcno`,`fromwarehouse`,`code`,`quantity`,`price`,`amount`,`towarehouse`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES('$incr','$prefix','$trnum','$date[$i]','$dcno[$i]','$fromwarehouse[$i]','$code[$i]','$quantity[$i]','$price[$i]','$amount','$towarehouse[$i]','$remarks[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
    else{
        $coa_Cr = $coa_Dr = $icat_iac[$icat_code[$code[$i]]];
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('CR','$coa_Cr','$date[$i]','$trnum','$code[$i]','$quantity[$i]','$price[$i]','$amount','$fromwarehouse[$i]','$remarks[$i]','0','Breeder-Stock Transfer Out','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
        $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
        VALUES ('DR','$coa_Dr','$date[$i]','$trnum','$code[$i]','$quantity[$i]','$price[$i]','$amount','$towarehouse[$i]','$remarks[$i]','0','Breeder-Stock Transfer In','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
    }
}
?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Stock Transfers?");
    if(x == true){
        window.location.href = "breeder_add_stocktransfer1.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_stocktransfer1.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_stocktransfer1.php?ccid="+a;
    }
</script>