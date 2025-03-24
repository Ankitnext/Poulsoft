<?php
//breeder_save_receive_stocks1.php
session_start(); include "newConfig.php";
include "broiler_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = $today = date('Y-m-d H:i:s');
$ccid = $_SESSION['receive_stocks1'];

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

$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE '%wastage%' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $wst_code = "";
while($row = mysqli_fetch_assoc($query)){ $wst_code = $row['code']; } 

$date = date("Y-m-d",strtotime($_POST['date']));
$dcno = $_POST['dcno'];
$trnum = $_POST['link_trnum'];
$fromwarehouse = $_POST['fromwarehouse']; 
$code = $_POST['code'];
$sent_qty = $_POST['sent_qty'];
$rcv_qty = $_POST['rcv_qty'];
$srt_qty = $_POST['srt_qty'];
$amount = $_POST['amount'];
$price = (float)$amount / (float)$sent_qty;
$towarehouse = $_POST['towarehouse'];
$remarks = $_POST['remarks'];
$item_sqty = $_POST['item_sqty']; 
$item_sprc = $_POST['item_sprc']; 

$flag = $dflag = 0; $active = 1;
$trtype = "receive_stocks1";
$trlink = "breeder_display_receive_stocks1.php";

    if($sent_qty == ""){ $sent_qty = 0; }
    if($rcv_qty == ""){ $rcv_qty = 0; }
    if($srt_qty == ""){ $srt_qty = 0; }
    if($item_sqty == ""){ $item_sqty = 0; }
    if($item_sprc == ""){ $item_sprc = 0; }
    
    //Feed Consumption in Bags
    if((int)$bfstk_bags == 1){
        $fsql1 = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$code' AND `active` = '1' AND `dflag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $fquery1 = mysqli_query($conn,$fsql1); $f_cnt1 = mysqli_num_rows($fquery1);
        if($f_cnt1 > 0){ while($frow1 = mysqli_fetch_assoc($fquery1)){ $sent_qty = $sent_qty * $frow1['bag_size']; $price = $price / $frow1['bag_size'];} }
    }
    if($rcv_qty < $sent_qty){
        $qnt = (float)$sent_qty - (float)$rcv_qty;
        $sql = "UPDATE `item_stocktransfers`  SET `quantity` = '$rcv_qty',`short_qty` = '$srt_qty',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`dflag` = '$dflag',`addedemp` = '$addedemp',`addedtime` = '$addedtime',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            $coa_Cr = $coa_Dr = $icat_iac[$icat_code[$code]];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('DR','$coa_Dr','$date','$trnum','$code','$rcv_qty','$price','$amount','$towarehouse','$remarks','0','Breeder-Receive Stock In','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            if((float)$qnt > 0){
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$wst_code','$date','$trnum','$code','$qnt','$price','$amount','$towarehouse','$remarks','0','Breeder-Shortage/Excess','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
    }
    else{
        $qnt = (float)$rcv_qty - (float)$sent_qty;
        $sql = "UPDATE `item_stocktransfers`  SET `quantity` = '$rcv_qty',`excess_qty` = '$srt_qty',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`dflag` = '$dflag',`addedemp` = '$addedemp',`addedtime` = '$addedtime',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error 2:-".mysqli_error($conn)); }
        else{
            $coa_Cr = $coa_Dr = $icat_iac[$icat_code[$code]];
            $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
            VALUES ('DR','$coa_Dr','$date','$trnum','$code','$rcv_qty','$price','$amount','$towarehouse','$remarks','0','Breeder-Receive Stock In','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            if((float)$qnt > 0){
                $from_post = "INSERT INTO `account_summary` (`crdr`,`coa_code`,`date`,`trnum`,`item_code`,`quantity`,`price`,`amount`,`location`,`remarks`,`gc_flag`,`etype`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
                VALUES ('DR','$wst_code','$date','$trnum','$code','$qnt','$price','$amount','$towarehouse','$remarks','0','Breeder-Shortage/Excess','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
            }
        }
    }
   

?>
<script>
    var a = '<?php echo $ccid; ?>';
    var x = confirm("Would you like add more Stock Transfers?");
    if(x == true){
        window.location.href = "breeder_add_receive_stocks1.php";
    }
    else if(x == false) {
        window.location.href = "breeder_display_receive_stocks1.php?ccid="+a;
    }
    else {
        window.location.href = "breeder_display_receive_stocks1.php?ccid="+a;
    }
</script>