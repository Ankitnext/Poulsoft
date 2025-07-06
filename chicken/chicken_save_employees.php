<?php
//chicken_save_employees.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['employees'];
$dbname = $_SESSION['dbase'];

//check folder exist or create a folder
$folder_path = "documents/".$dbname; if (!file_exists($folder_path)) { mkdir($folder_path, 0777, true); }

$sql ="SELECT MAX(incr) as incr FROM `chicken_employee`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "CEP";

$title = $_POST['title'];
$name = $_POST['name'];
$emp_id = $_POST['emp_id'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$desig_code = $_POST['desig_code'];
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
$sql = "INSERT INTO `chicken_employee` (incr,prefix,code,title,name,emp_id,mobile,email,gender,desig_code,birth_date,join_date,gross_salary,warehouse,pan_no,aadhar_no,uan_no,esi_no,bank_acc_no,bank_ifsc_code,bank_name,bank_branch_name,emp_photo_path,file_path_1,file_path_2,file_path_3,file_path_4,street_name,city_name,state_code,pincode,country,remarks,vehicle,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$title','$name','$emp_id','$mobile','$email','$gender','$desig_code','$birth_date','$join_date','$gross_salary','$warehouse','$pan_no','$aadhar_no','$uan_no','$esi_no','$bank_acc_no','$bank_ifsc_code','$bank_name','$bank_branch_name','$emp_photo_paths','$file_path_1_path','$file_path_2_path','$file_path_3_path','$file_path_4_path','$street_name','$city_name','$state_code','$pincode','$country','$remarks','$vehicle','0','1','0','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_employees.php?ccid='.$ccid); }

?>