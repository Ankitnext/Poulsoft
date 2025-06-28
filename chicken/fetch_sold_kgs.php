<?php
include "newConfig.php";

$date = date("Y-m-d", strtotime($_POST['date']));
$warehouse = $_POST['warehouse'] ?? '';

// Sector lookup
$sector_name = [];
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = 1 ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $sector_name[$row['code']] = $row['description'];
}

// If warehouse is not set: return options
if (empty($warehouse) || $warehouse === 'select') {
    $sql = "SELECT * FROM `customer_sales` WHERE `date` = '$date' ORDER BY `id`";
    $query = mysqli_query($conn, $sql);
    $options = "<option value='select'>--Select Warehouse--</option>";
    while ($row = mysqli_fetch_assoc($query)) {
        $code = htmlspecialchars($row['warehouse']);
        $name = htmlspecialchars($sector_name[$code] ?? $code);
        $options .= "<option value='$code'>$name</option>";
    }
    echo $options;
} else {
    // Otherwise, return the sold weight
    $sql = "SELECT * FROM `customer_sales` WHERE `date` = '$date' AND `warehouse` = '$warehouse' ORDER BY `id` LIMIT 1";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);
    echo $row['netweight'] ?? 0;
}
