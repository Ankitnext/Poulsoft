<?php
//chicken_upload_purchase_lsfi.php
if(!isset($_SESSION)){ session_start(); } include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$dbname = $_SESSION['dbase'];

$trnum = $_POST['file_trnum'];
$file_remarks = $_POST['file_comment'];

//check folder exist or create a folder
$folder_path = "documents/".$dbname."/Purchase_Docs"; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }
$file_path_1_name = $file_path_1_path = $file_path_2_name = $file_path_2_path = $file_path_3_name = $file_path_3_path = "";
if(!empty($_FILES["file_path1"]["name"])) {
    /*Get File Extension*/ $filename = basename($_FILES["file_path1"]["name"]); $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    /*check file count in a directory*/ $directory = $folder_path."/"; $filecount = count(glob($directory . "*")); $filecount++;
    /*Create new file name*/ $file_name = $trnum."_1.".$filetype;
    $filetmp = $_FILES['file_path1']['tmp_name']; $file_path_1_name = $_FILES['file_path1']['name'];
    $file_path_1_path = $folder_path."/".$file_name; move_uploaded_file($filetmp,$file_path_1_path);
}


$file_set = "";
if($file_path_1_path != ""){ if($file_set == ""){ $file_set = ",`purchase_image` = '$file_path_1_path'"; } }

$sql = "UPDATE `pur_purchase` SET `remarks` = '$file_remarks',`updatedemp` = '$addedemp',`updated` = '$addedtime'".$file_set." WHERE `invoice` = '$trnum' AND `tdflag` = '0'";
if(!mysqli_query($conn,$sql)){ echo mysqli_error($conn); } else{ header('location: chicken_display_generalpurchase10.php'); }
?>