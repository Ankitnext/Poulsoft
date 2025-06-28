<?php
if(!isset($_SESSION)){ session_start(); } include "newConfig.php";

// $wcode = $_GET['wcode'];
// echo $date = date("Y-m-d", strtotime($_POST['date']));
$date = date("Y-m-d", strtotime($_POST['date']));
// $date = date("Y-m-d",$_POST['date']);

//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `customer_sales` WHERE `date` = '$date' ORDER BY `id` ;";
$query = mysqli_query($conn,$sql);
// $fetchrate = mysqli_fetch_assoc($query);
while ($row = mysqli_fetch_assoc($query)) {
    $code = htmlspecialchars($row['warehouse']);
    $name = htmlspecialchars($sector_name[$row['warehouse']]);
    $options .= "<option value='$code'>$name</option>";
}

echo $options;

?>