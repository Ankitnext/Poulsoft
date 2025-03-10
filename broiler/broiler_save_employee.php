<?php
//broiler_save_employee.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['employee'];
$dbname = $_SESSION['dbase'];

//check folder exist or create a folder
$folder_path = "documents/".$dbname; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }

//Fetch Column From CoA Table
$sql='SHOW COLUMNS FROM `broiler_employee`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array('gross_salary', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `gross_salary` VARCHAR(300) NULL DEFAULT NULL AFTER `join_date`;"; mysqli_query($conn,$sql); }
if(in_array("pan_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee` ADD `pan_no` VARCHAR(300) NULL DEFAULT NULL AFTER `gross_salary`"; mysqli_query($conn,$sql); }
if(in_array('aadhar_no', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `aadhar_no` VARCHAR(300) NULL DEFAULT NULL AFTER `pan_no`;"; mysqli_query($conn,$sql); }
if(in_array('uan_no', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `uan_no` VARCHAR(300) NULL DEFAULT NULL AFTER `aadhar_no`;"; mysqli_query($conn,$sql); }
if(in_array('esi_no', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `esi_no` VARCHAR(300) NULL DEFAULT NULL AFTER `uan_no`;"; mysqli_query($conn,$sql); }
if(in_array('bank_acc_no', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `bank_acc_no` VARCHAR(300) NULL DEFAULT NULL AFTER `esi_no`;"; mysqli_query($conn,$sql); }
if(in_array('bank_ifsc_code', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `bank_ifsc_code` VARCHAR(300) NULL DEFAULT NULL AFTER `bank_acc_no`;"; mysqli_query($conn,$sql); }
if(in_array('bank_name', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `bank_name` VARCHAR(300) NULL DEFAULT NULL AFTER `bank_ifsc_code`;"; mysqli_query($conn,$sql); }
if(in_array('bank_branch_name', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `bank_branch_name` VARCHAR(300) NULL DEFAULT NULL AFTER `bank_name`;"; mysqli_query($conn,$sql); }
if(in_array('emp_photo_path', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `emp_photo_path` VARCHAR(300) NULL DEFAULT NULL AFTER `bank_branch_name`;"; mysqli_query($conn,$sql); }
if(in_array('file_path_1', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `file_path_1` VARCHAR(300) NULL DEFAULT NULL AFTER `emp_photo_path`;"; mysqli_query($conn,$sql); }
if(in_array('file_path_2', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `file_path_2` VARCHAR(300) NULL DEFAULT NULL AFTER `file_path_1`;"; mysqli_query($conn,$sql); }
if(in_array('file_path_3', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `file_path_3` VARCHAR(300) NULL DEFAULT NULL AFTER `file_path_2`;"; mysqli_query($conn,$sql); }
if(in_array('file_path_4', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `file_path_4` VARCHAR(300) NULL DEFAULT NULL AFTER `file_path_3`;"; mysqli_query($conn,$sql); }
if(in_array('warehouse', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `warehouse` VARCHAR(300) NULL DEFAULT NULL AFTER `file_path_4`;"; mysqli_query($conn,$sql); }
if(in_array('este_code', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_employee`  ADD `este_code` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `desig_code`;"; mysqli_query($conn,$sql); }

$sql ="SELECT MAX(incr) as incr FROM `broiler_employee`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "BEP";

$title = $_POST['title'];
$name = $_POST['name'];
$emp_id = $_POST['emp_id'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$desig_code = $_POST['desig_code'];
$este_code = $_POST['este_code'];
$birth_date = date("Y-m-d",strtotime($_POST['birth_date']));
$join_date = date("Y-m-d",strtotime($_POST['join_date']));

$gross_salary = $_POST['gross_salary'];
$warehouse = $_POST['warehouse'];
$pan_no = $_POST['pan_no'];
$aadhar_no = $_POST['aadhar_no'];
$uan_no = $_POST['uan_no'];
$esi_no = $_POST['esi_no'];
$bank_acc_no = $_POST['bank_acc_no'];
$bank_ifsc_code = $_POST['bank_ifsc_code'];
$bank_name = $_POST['bank_name'];
$bank_branch_name = $_POST['bank_branch_name'];

$street_name = $_POST['street_name'];
$city_name = $_POST['city_name'];
$state_code = $_POST['state_code'];
$pincode = $_POST['pincode'];
$country = $_POST['country'];
$remarks = $_POST['remarks'];
$vehicle = $_POST['vehicle'];

if(!empty($_FILES["file_path_1"]["name"])) {
    //Get File Extension
    $filename = basename($_FILES["file_path_1"]["name"]);
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

    //check file count in a directory
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;

    //Create new file name
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['file_path_1']['tmp_name'];
    $file_path_1_name = $_FILES['file_path_1']['name'];
    $file_path_1_path = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$file_path_1_path);
}
else{
    $file_path_1_name = $file_path_1_path = "";
}
if(!empty($_FILES["file_path_2"]["name"])) {
    //Get File Extension
    $filename = basename($_FILES["file_path_2"]["name"]);
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

    //check file count in a directory
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;

    //Create new file name
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['file_path_2']['tmp_name'];
    $file_path_2_name = $_FILES['file_path_2']['name'];
    $file_path_2_path = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$file_path_2_path);
}
else{
    $file_path_2_name = $file_path_2_path = "";
}
if(!empty($_FILES["file_path_3"]["name"])) {
    //Get File Extension
    $filename = basename($_FILES["file_path_3"]["name"]);
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

    //check file count in a directory
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;

    //Create new file name
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['file_path_3']['tmp_name'];
    $file_path_3_name = $_FILES['file_path_3']['name'];
    $file_path_3_path = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$file_path_3_path);
}
else{
    $file_path_3_name = $file_path_3_path = "";
}
if(!empty($_FILES["file_path_4"]["name"])) {
    //Get File Extension
    $filename = basename($_FILES["file_path_4"]["name"]);
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

    //check file count in a directory
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;

    //Create new file name
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['file_path_4']['tmp_name'];
    $file_path_4_name = $_FILES['file_path_4']['name'];
    $file_path_4_path = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$file_path_4_path);
}
else{
    $file_path_4_name = $file_path_4_path = "";
}
if(!empty($_FILES["emp_photo_path"]["name"])) {
    //Get File Extension
    $filename = basename($_FILES["emp_photo_path"]["name"]);
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);

    //check file count in a directory
    $directory = $folder_path."/";
    $filecount = count(glob($directory . "*")); $filecount++;

    //Create new file name
    $file_name = $dbname."_".$filecount.".".$filetype;

    $filetmp = $_FILES['emp_photo_path']['tmp_name'];
    $emp_photo_path_name = $_FILES['emp_photo_path']['name'];
    $emp_photo_paths = $folder_path."/".$file_name;
    move_uploaded_file($filetmp,$emp_photo_paths);
}
else{
    $emp_photo_path_name = $emp_photo_paths = "";
}

if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;
$sql = "INSERT INTO `broiler_employee` (incr,prefix,code,title,name,emp_id,mobile,email,gender,desig_code,este_code,birth_date,join_date,gross_salary,warehouse,pan_no,aadhar_no,uan_no,esi_no,bank_acc_no,bank_ifsc_code,bank_name,bank_branch_name,emp_photo_path,file_path_1,file_path_2,file_path_3,file_path_4,street_name,city_name,state_code,pincode,country,remarks,vehicle,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES 
('$incr','$prefix','$code','$title','$name','$emp_id','$mobile','$email','$gender','$desig_code','$este_code','$birth_date','$join_date','$gross_salary','$warehouse','$pan_no','$aadhar_no','$uan_no','$esi_no','$bank_acc_no','$bank_ifsc_code','$bank_name','$bank_branch_name','$emp_photo_paths','$file_path_1_path','$file_path_2_path','$file_path_3_path','$file_path_4_path','$street_name','$city_name','$state_code','$pincode','$country','$remarks','$vehicle','0','1','0','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_employee.php?ccid='.$ccid); }

?>