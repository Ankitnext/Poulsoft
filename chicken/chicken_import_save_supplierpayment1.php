<?php
// chicken_import_save_supplierpayment1.php
session_start();
include "newConfig.php";
include "chicken_generate_trnum_details.php";

$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s'); 
$client = $_SESSION['client'];
 
// Payment Information
$date = $ccode = $mode = $code = $amount = $docno = $sector = $remarks = array();

$i = 0; foreach ($_POST['date'] as $dates) { $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach ($_POST['ccode'] as $ccodes) { $ccode[$i] = $ccodes; $i++; }
$i = 0; foreach ($_POST['mode'] as $modes) { $mode[$i] = $modes; $i++; }
$i = 0; foreach ($_POST['method'] as $methods) { $code[$i] = $methods; $i++; }
$i = 0; foreach ($_POST['amount'] as $amounts) { $amount[$i] = $amounts; $i++; }
$i = 0; foreach ($_POST['docno'] as $docnos) { $docno[$i] = $docnos; $i++; }
$i = 0; foreach ($_POST['sector'] as $sectors) { $sector[$i] = $sectors; $i++; }
$i = 0; foreach ($_POST['remarks'] as $remarkss) { $remarks[$i] = $remarkss; $i++; }

$vtype = "S"; // Supplier Payment
$flag = $active = 1;
$tdflag = $pdflag = 0;
$trtype = "supplierpayment1";
$trlink = "chicken_import_supplierpayment1.php";

// Save Payments
$dsize = sizeof($ccode);
for ($i = 0; $i < $dsize; $i++) {
    // Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date[$i], "supplierpayment1", "SPI", "generate", $_SESSION['dbase']);
    $trno_dt2 = explode("@", $trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

    if ($amount[$i] == "") { $amount[$i] = 0; }

    $sql = "INSERT INTO `pur_payments` (`incr`, `prefix`, `trnum`, `date`, `ccode`, `docno`, `mode`, `method`, `amount`, `vtype`, `warehouse`, `remarks`, `flag`, `active`, `tdflag`, `pdflag`, `trtype`, `trlink`, `addedemp`, `addedtime`, `updatedtime`) 
        VALUES ('$incr', '$prefix', '$trnum', '$date[$i]', '$ccode[$i]', '$docno[$i]', '$mode[$i]', '$code[$i]', '$amount[$i]', '$vtype', '$sector[$i]', '$remarks[$i]', '$flag', '$active', '$tdflag', '$pdflag', '$trtype', '$trlink', '$addedemp', '$addedtime', '$addedtime')";
    if(!mysqli_query($conn, $sql)){ die("Error:- " . mysqli_error($conn)); }
}

header('location:chicken_display_supplierpayment1.php?spid=' . $spid);
?>
