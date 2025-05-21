<?php
//chicken_modify_vehexp1.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$dbname = $_SESSION['dbase'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

$ids = $_POST['idvalue']; $incr = $prefix = $invoice = $aemp = $atime = "";
 $sql = "SELECT * FROM `acc_vouchers` WHERE `trnum` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($invoice == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $invoice = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
    
    $existing_image_path = $row['doc1_path'];
    $existing_image_path2 = $row['doc2_path'];
    $existing_image_path3 = $row['doc3_path'];

    // $existing_doc1 = $row['doc1'];
    // $existing_doc2 = $row['doc2'];
    // $existing_doc3 = $row['doc3'];
}

if($invoice != ""){
    $sql3 = "DELETE FROM `acc_vouchers` WHERE `trnum` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

//Sale Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
// $vcode = $_POST['vcode'];
// $bookinvoice = $_POST['bookinvoice'];
$code = $_POST['code'];
$mode = $_POST['mode'];
$warehouse = $_POST['warehouse'];

if(isset($_POST['submit']) == "editpage"){
    //Document Upload
    $folder_path = "documents/".$dbname."/vehicle_exp"; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }
    if(!empty($_FILES["doc1"]["name"])) {
        $filename = basename($_FILES["doc1"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        $directory = $folder_path."/";
        $filecount = count(glob($directory . "*")); $filecount++;
        $file_name = $dbname."_".$invoice."-".$incr."-".$filecount.".".$filetype;

        $filetmp = $_FILES['doc1']['tmp_name'];
        $doc1_path = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$doc1_path);
    }
    else{ $doc1_path = $existing_image_path; }

    if(!empty($_FILES["doc2"]["name"])) {
        $filename = basename($_FILES["doc2"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        $directory = $folder_path."/";
        $filecount = count(glob($directory . "*")); $filecount++;
        $file_name = $dbname."_".$invoice."-".$incr."-".$filecount.".".$filetype;

        $filetmp = $_FILES['doc2']['tmp_name'];
        $doc2_path = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$doc2_path);
    }
    else{ $doc2_path = $existing_image_path2; }

    if(!empty($_FILES["doc3"]["name"])) {
        $filename = basename($_FILES["doc3"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        $directory = $folder_path."/";
        $filecount = count(glob($directory . "*")); $filecount++;
        $file_name = $dbname."_".$invoice."-".$incr."-".$filecount.".".$filetype;

        $filetmp = $_FILES['doc3']['tmp_name'];
        $doc3_path = $folder_path."/".$file_name;
        move_uploaded_file($filetmp,$doc3_path);
    }
    else{ $doc3_path = $existing_image_path3; }
}

$descs = $remark = $amount = array();
$i = 0; foreach($_POST['descs'] as $descss){ $descs[$i] = $descss; $i++; }
$i = 0; foreach($_POST['remark'] as $remarks){ $remark[$i] = $remarks; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "vehexp1";
$trlink = "chicken_display_vehexp1.php";

// echo $doc1;
// die();


//Save Purchase
$dsize = sizeof($descs);
for($i = 0;$i < $dsize;$i++){
    if($amount[$i] == ""){ $amount[$i] = 0; }
    $sql = "INSERT INTO `acc_vouchers` (`incr`,`prefix`,`trnum`,`date`,`fcoa`,`tcoa`,`mode`,`doc1_path`,`doc2_path`,`doc3_path`,`amount`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`) 
    VALUES ('$incr','$prefix','$invoice','$date','$code','$descs[$i]','$mode','$doc1_path','$doc2_path','$doc3_path','$amount[$i]','$warehouse','$remark[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_vehexp1.php?ccid='.$ccid);}
}
// die();
//Check and save Cash Receipt


header('location:chicken_display_vehexp1.php?ccid='.$ccid);

