<?php
//broiler_save_companydetails.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['companydetails'];

$cdetails = $_POST['editor'];
$type = $_POST['ctype'];

if(!empty($_FILES["logo_image"]["name"])) { 
    // Get file info 
     $filename = basename($_FILES["logo_image"]["name"]); 
     $filetype = pathinfo($filename, PATHINFO_EXTENSION); 
     
     // Allow certain file formats 
     $allowTypes = array('jpg','png','jpeg','gif'); 
     if(in_array($filetype, $allowTypes)){ 
         $image = $_FILES['logo_image']['tmp_name']; 
         $imagename = addslashes(file_get_contents($image)); 
         $filetmp = $_FILES['logo_image']['tmp_name'];
         $filename = $_FILES['logo_image']['name'];
         $filetype = $_FILES['logo_image']['type'];
         $filepath = "images/".$filename;
         move_uploaded_file($filetmp,$filepath);
     }
 }
 if($imagename == ""){
     $fileName = $imagename = $filepath = NULL;
 }
 else{ }
 $sql = "INSERT INTO `main_companyprofile` (type,imagename,logoname,logopath,cdetails,flag,active,dflag,addedemp,addedtime) VALUES ('$type','$imagename','$filename','$filepath','$cdetails','0','1','0','$addedemp','$addedtime')";
 if(!mysqli_query($conn,$sql)){
     die("Error:-".mysqli_error($conn));
 }
 else {
     header('location:broiler_display_companydetails.php?ccid='.$ccid);
 }

 ?>
