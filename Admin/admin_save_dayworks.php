<?php
//admin_save_dayworks.php
session_start();
include "newConfig.php";

$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['dayworks'];

// Ensure `$date` is being processed correctly
$date = date("Y-m-d", strtotime($_POST['date']));

$allowTypes = ['jpg', 'png', 'jpeg', 'gif'];

// Loop through each record in arrays and insert
for ($i = 0; $i < count($_POST['tic_no']); $i++) {
    $filepaths = $filepaths2 = $filepaths3 = NULL;

    // Handle logo_image
    if (!empty($_FILES['logo_image']['name'][$i])) {
        $filename = basename($_FILES['logo_image']['name'][$i]);
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array($filetype, $allowTypes)) {
            $filetmp = $_FILES['logo_image']['tmp_name'][$i];
            $filepaths = "images/" . $filename;
            move_uploaded_file($filetmp, $filepaths);
        }
    }

   // Handle logo_image2
   if (!empty($_FILES['logo_image2']['name'][$i])) {
    $filename2 = basename($_FILES['logo_image2']['name'][$i]);
    $filetype2 = pathinfo($filename2, PATHINFO_EXTENSION);
    if (in_array($filetype2, $allowTypes)) {
        $filetmp2 = $_FILES['logo_image2']['tmp_name'][$i];
        $filepaths2[$i] = "images/" . $filename2;
        move_uploaded_file($filetmp2, $filepaths2[$i]);
    }
}

// Handle logo_image3
if (!empty($_FILES['logo_image3']['name'][$i])) {
    $filename3 = basename($_FILES['logo_image3']['name'][$i]);
    $filetype3 = pathinfo($filename3, PATHINFO_EXTENSION);
    if (in_array($filetype3, $allowTypes)) {
        $filetmp3 = $_FILES['logo_image3']['tmp_name'][$i];
        $filepaths3[$i] = "images/" . $filename3;
        move_uploaded_file($filetmp3, $filepaths3[$i]);
    }
}

    // Prepare values for insertion
    $tic_no = $_POST['tic_no'][$i];
    $mod_type = $_POST['mod_type'][$i];
    $cl_name = $_POST['cl_name'][$i];
    $wok_type = $_POST['wok_type'][$i];
    $gdate = date("Y-m-d", strtotime($_POST['gdate'][$i]));
    $fl_type = $_POST['fl_type'][$i];
    $fl_link = $_POST['fl_link'][$i];
    $wdate = date("Y-m-d", strtotime($_POST['wdate'][$i]));
    $t_taken = $_POST['t_taken'][$i];
    $statuses = $_POST['statuses'][$i];
    $remarks = $_POST['remarks'][$i];

    $sql = "INSERT INTO `emp_daily_works` (date, tic_no, mod_type, cl_name, wok_type, gdate, fl_type, fl_link, wdate, t_taken, statuses, remarks, file_path, file_path2, file_path3, addedemp, addedtime) 
            VALUES ('$date', '$tic_no', '$mod_type', '$cl_name', '$wok_type', '$gdate', '$fl_type', '$fl_link', '$wdate', '$t_taken', '$statuses', '$remarks', '$filepaths', '$filepaths2', '$filepaths3', '$addedemp', '$addedtime')";

    if (!mysqli_query($conn, $sql)) {
        die("Error: " . mysqli_error($conn));
    }
}

header('Location: admin_display_dayworks.php?ccid=' . $ccid);


?>
