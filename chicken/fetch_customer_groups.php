<?php
include "newConfig.php";

// read from GET, not POST
$groups = isset($_GET['groups']) ? mysqli_real_escape_string($conn, $_GET['groups']) : '';

// build a lookup of group‐descriptions if you still need it
$grp_name = [];
$sql = "SELECT code, description FROM `main_groups` WHERE `gtype` LIKE '%C%' ORDER BY `description`";
$res = mysqli_query($conn, $sql);
while ($r = mysqli_fetch_assoc($res)) {
    $grp_name[$r['code']] = $r['description'];
}
// start your <option> list
$options = "<option value='select'>-- Select Customer --</option>";

// base query
$sql  = "SELECT `code`, `name`, `groupcode` FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = 1";
// only narrow by group when it's neither empty nor “select”
if ($groups !== '' && $groups !== 'select') {
    $sql .= " AND `groupcode` = '{$groups}'";
}

$sql .= " ORDER BY `name`";

$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($query)) {
    $code = htmlspecialchars($row['code'],  ENT_QUOTES);
    $name = htmlspecialchars($row['name'],  ENT_QUOTES);
    $options .= "<option value='{$code}'>{$name}</option>";
}

echo $options;
