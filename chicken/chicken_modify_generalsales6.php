<?php
//chicken_modify_generalsales6.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

$ids = $_POST['idvalue']; $incr = $prefix = $invoice = $aemp = $atime = "";
$sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($invoice == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $invoice = $row['invoice']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($invoice != ""){
    $sql3 = "DELETE FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

//Sale Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$vcode = $_POST['vcode'];
$jali_no = $_POST['jali_no'];
$bookinvoice = $_POST['bookinvoice'];
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];
$warehouse = $_POST['warehouse'];

$itemcode = $jals = $birds = $tweight = $eweight = $nweight = $price = $amount = array();
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }

$tcds_chk = $_POST['tcds_chk'];
$tcds_per = $_POST['tcds_per'];
$tcds_type1 = $_POST['tcds_type1'];
$tcds_type2 = $_POST['tcds_type2'];
$tcds_amt = $_POST['tcds_amt'];
$transporter_code = $_POST['transporter_code'];
$freight_amt = $_POST['freight_amt'];
$dressing_charge = $_POST['dressing_charge'];
$roundoff_type1 = $_POST['roundoff_type1'];
$roundoff_type2 = $_POST['roundoff_type2'];
$roundoff_amt = $_POST['roundoff_amt'];
$finaltotal = $_POST['finaltotal'];
$remarks = $_POST['remarks'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "generalsales6";
$trlink = "chicken_display_generalsales6.php";

//Save Purchase
$dsize = sizeof($itemcode);
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt == ""){ $tcds_amt = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($dressing_charge == ""){ $dressing_charge = 0; }
    if($roundoff_amt == ""){ $roundoff_amt = 0; }
    if($finaltotal == ""){ $finaltotal = 0; }

    $sql = "INSERT INTO `customer_sales` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`jali_no`,`bookinvoice`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`transporter_code`,`freight_amount`,`tcdsper`,`tcds_type1`,`tcds_type2`,`tcdsamt`,`dressing_charge`,`roundoff_type1`,`roundoff_type2`,`roundoff`,`finaltotal`,`balance`,`drivercode`,`vehiclecode`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updated`) 
    VALUES ('$incr','$d','$m','$y','$pfx','$date','$invoice','$jali_no','$bookinvoice','$vcode','$itemcode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$amount[$i]','$transporter_code','$freight_amt','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt','$dressing_charge','$roundoff_type1','$roundoff_type2','$roundoff_amt','$finaltotal','$finaltotal','$driver','$vehicle','$warehouse','$remarks','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$aemp','$atime','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

//Check and save Cash Receipt
$cash_trno = $_POST['cash_trno']; $cash_rcode = $_POST['cash_rcode']; $cash_ramt = $_POST['cash_ramt']; if($cash_ramt == ""){ $cash_ramt = 0; }
if($cash_rcode != "" && $cash_rcode !="select" && (float)$cash_ramt > 0){
    //Fetch Account Modes
    $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $cash_mode = $bank_mode = "";
    while($row = mysqli_fetch_assoc($query)){ $cash_mode = $row['code']; }
    
    if($cash_trno != ""){
        $rct_sql = "UPDATE `customer_receipts` SET `date` = '$date',`ccode` = '$vcode',`docno` = '$bookinvoice',`mode` = '$cash_mode',`method` = '$cash_rcode',`amount` = '$cash_ramt',`warehouse` = '$warehouse',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$cash_trno' AND `link_trnum` = '$invoice' AND `tdflag` = '0' AND `pdflag` = '0'";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    else{
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
        
        $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag)
        VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode','$bookinvoice','$cash_mode','$cash_rcode','$cash_ramt','C','$warehouse','$remarks','$flag','$active','$addedemp','$addedtime','$tdflag','$pdflag')";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}
else if((float)$cash_ramt <= 0 && $cash_trno != ""){
    $rct_sql = "UPDATE `customer_receipts` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$cash_trno' AND `link_trnum` = '$invoice' AND `tdflag` = '0' AND `pdflag` = '0'";
    if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
//Check and save Bank Receipt
$bank_trno = $_POST['bank_trno']; $bank_rcode = $_POST['bank_rcode']; $bank_ramt = $_POST['bank_ramt']; if($bank_ramt == ""){ $bank_ramt = 0; }
if($bank_rcode != "" && $bank_rcode !="select" && (float)$bank_ramt > 0){
    //Fetch Account Modes
    $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $bank_mode = "";
    while($row = mysqli_fetch_assoc($query)){ $bank_mode = $row['code']; }
    
    if($bank_trno != ""){
        $rct_sql = "UPDATE `customer_receipts` SET `date` = '$date',`ccode` = '$vcode',`docno` = '$bookinvoice',`mode` = '$bank_mode',`method` = '$bank_rcode',`amount` = '$bank_ramt',`warehouse` = '$warehouse',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$bank_trno' AND `link_trnum` = '$invoice' AND `tdflag` = '0' AND `pdflag` = '0'";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
    else{
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
        $trno_dt1 = generate_transaction_details($date,"msi_rct1","RSI","generate",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];
        
        $rct_sql = "INSERT INTO `customer_receipts` (incr,prefix,trnum,link_trnum,date,ccode,docno,mode,method,amount,vtype,warehouse,remarks,flag,active,addedemp,addedtime,tdflag,pdflag)
        VALUES ('$incr','$prefix','$trnum','$invoice','$date','$vcode','$bookinvoice','$bank_mode','$bank_rcode','$bank_ramt','C','$warehouse','$remarks','$flag','$active','$addedemp','$addedtime','$tdflag','$pdflag')";
        if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
    }
}
else if((float)$bank_ramt <= 0 && $bank_trno != ""){
    $rct_sql = "UPDATE `customer_receipts` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$bank_trno' AND `link_trnum` = '$invoice' AND `tdflag` = '0' AND `pdflag` = '0'";
    if(!mysqli_query($conn,$rct_sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
// header('location:chicken_display_generalsales6.php?ccid='.$ccid);
if(isset($_POST['sub_pt']) == true){
?>
    <script>
        var invoice = '<?php echo $invoice; ?>';
        window.open('chicken_generate_saleinv_print1.php?trnum='+ invoice,'_blank');
        window.location.href = 'chicken_edit_generalsales6.php';
    </script>
<?php
exit;
}
else{
    header('location:chicken_display_generalsales6.php?ccid='.$ccid);
    exit;
}
