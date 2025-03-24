<?php
//main_savecompanyprofile.php
session_start(); include "newConfig.php";
$cdetails = $_POST['editor1'];
$type = $_POST['ctype'];
$imagename = $_POST['image'];
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
if($type == "all"){ $imgname = $client."-AL"; }
else if($type == "Company Profile"){ $imgname = $client."-CP"; }
else if($type == "Purchase Invoice"){ $imgname = $client."-PI"; }
else if($type == "Sales Invoice"){ $imgname = $client."-SI"; }
else if($type == "Purchase Report"){ $imgname = $client."-PR"; }
else if($type == "Sales Report"){ $imgname = $client."-SR"; }
else if($type == "Other Transactions"){ $imgname = $client."-OT"; }
else if($type == "Other Report"){ $imgname = $client."-OR"; }
$status = $statusMsg = ''; 
if($_POST["submitcdetails"] == "addpage"){ 
	$status = 'error'; 
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
	$sql = "INSERT INTO `main_companyprofile` (type,imagename,logoname,logopath,cdetails,addedemp,addedtime,client) VALUES ('$type','$imagename','$filename','$filepath','$cdetails','$addedemp','$addedtime','$client')";
	if(!mysqli_query($conn,$sql)){
		die("Error:-".mysqli_error($conn));
	}
	else {
		header('location:companyprofile.php');
	}
}
else if(isset($_POST["updatecdetails"])){
	$status = 'error'; 
	if(!empty($_FILES["logo_image"]["name"])) { 
    // Get file info 
		$fileName = basename($_FILES["logo_image"]["name"]); 
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
        
    // Allow certain file formats 
		$allowTypes = array('jpg','png','jpeg','gif'); 
		if(in_array($fileType, $allowTypes)){ 
			$image = $_FILES['logo_image']['tmp_name']; 
			$imagename = addslashes(file_get_contents($image));
			$filetmp = $_FILES['logo_image']['tmp_name'];
			$filename = $_FILES['logo_image']['name'];
			$filetype = $_FILES['logo_image']['type'];
			$filepath = "images/".$filename;
			move_uploaded_file($filetmp,$filepath);
			$id = $_POST['id'];
			$sql = "UPDATE `main_companyprofile` SET `type` = '$type',`imagename` = '$imagename',`logoname` = '$filename',`logopath` = '$filepath',`cdetails` = '$cdetails',`addedemp` = '$addedemp',`addedtime` = '$addedtime',`client` = '$client' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:companyprofile.php'); }
		}
	}
	else {
		$id = $_POST['id'];
		$sql = "UPDATE `main_companyprofile` SET `type` = '$type',`cdetails` = '$cdetails',`addedemp` = '$addedemp',`addedtime` = '$addedtime',`updated` = '$addedtime',`client` = '$client' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:companyprofile.php'); }
	}
}
else if($_GET['page'] == "delete"){
	$id = $_GET['id'];
	$sql = "DELETE FROM `main_companyprofile` WHERE `id` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:companyprofile.php'); }
}
else if($_GET['page'] == "activate"){
	$id = $_GET['id'];
	$sql = "UPDATE `main_companyprofile` SET `active` = '1' WHERE `id` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:companyprofile.php'); }
}
else if($_GET['page'] == "deactivate"){
	$id = $_GET['id'];
	$sql = "UPDATE `main_companyprofile` SET `active` = '0' WHERE `id` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:companyprofile.php'); }
}
else if($_GET['page'] == "authorize"){
	$id = $_GET['id'];
	$sql = "UPDATE `main_companyprofile` SET `flag` = '1' WHERE `id` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:companyprofile.php'); }
}
else{ 
	$statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; 
} 
?>