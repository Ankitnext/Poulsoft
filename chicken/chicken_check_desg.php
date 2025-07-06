<?php
session_start();
include "newConfig.php";

$tcds = isset($_GET['type']) ? $_GET['type'] : '';
$group = isset($_GET['group']) ? $_GET['group'] : '';

// Only apply group filter if it's neither empty nor "all"
$grp_fltr = "";
if (!empty($group) && strtolower($group) !== "all") {
    $group_safe = mysqli_real_escape_string($conn, $group);
    $grp_fltr = "AND `groupcode` = '$group_safe'";
}

if ($tcds == "CST") {
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' $grp_fltr AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn, $sql);

    if (!$query) {
        http_response_code(500);
        echo "Database query failed: " . mysqli_error($conn);
        exit;
    }

    echo '<option value="select">-select-</option>';

    while ($row = mysqli_fetch_assoc($query)) {
        $code = htmlspecialchars($row['code']);
        $name = htmlspecialchars($row['name']);
        echo "<option value=\"$code\">$name</option>";
    }
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
