<?php
//broiler_view_ticket_details.php
if(!isset($_SESSION)){ session_start(); } include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$username = $_SESSION['users'];
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$trnum = $_GET['trnum'];

$sql = "SELECT * FROM `ticket_transaction_types`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $dev_types[$row['code']] = $row['description'];}

$sql = "SELECT * FROM `log_useraccess` WHERE `account_access` = 'ATS'"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){ $usr_name[$row['empcode']] = $row['username']; }

$ticket_comments = $file_references = '';
$sql = "SELECT * FROM `ticket_management_system` WHERE `trnum` = '$trnum'"; $query = mysqli_query($conn,$sql); 
while($row = mysqli_fetch_assoc($query)){
    if($row['file_path1'] != ""){
        $file_references .= '<a href="'.$row["file_path1"].'" download>Reference-1</a>&ensp;';
        $file_references2 .= '<a href="'.$row["file_path1"].'" target="_BLANK">View-1</a>&ensp;';
    }
    if($row['file_path2'] != ""){
        $file_references .= '<a href="'.$row["file_path2"].'" download>Reference-1</a>&ensp;';
        $file_references2 .= '<a href="'.$row["file_path2"].'" target="_BLANK">View-1</a>&ensp;';
    }
    if($row['file_path3'] != ""){
        $file_references .= '<a href="'.$row["file_path3"].'" download>Reference-1</a>&ensp;';
        $file_references2 .= '<a href="'.$row["file_path3"].'" target="_BLANK">View-1</a>&ensp;';
    }
    if($row['file_path4'] != ""){
        $file_references .= '<a href="'.$row["file_path4"].'" download>Reference-1</a>&ensp;';
        $file_references2 .= '<a href="'.$row["file_path4"].'" target="_BLANK">View-1</a>&ensp;';
    }
    $ticket_comments .= '
    <table class="table table-bordered">
    <tr>
        <th>Date</th><td>'.date("d.m.Y",strtotime($row["date"])).'</td>
        <th>Client</th><td>'.$row["client_name"].'</td>
    </tr>
    <tr>
        <th>Transaction Type</th><td>'.$row["ticket_name"].'</td>
        <th>Assign To</th><td>'.$row["assignee"].'</td>
    </tr>
    <tr>
        <th>Priority</th><td>'.$row["ticket_priority"].'</td>
        <th>Development Type</th><td>'.$dev_types[$row["development_type"]].'</td>
    </tr>
    <tr>
        <th>DB Type</th><td>'.$row["requirement_list2"].'</td>
        <th>Created By</th><td>'.$usr_name[$row['received_by']].'</td>
    </tr>
    ';
    if($row['requirement_list1'] != ""){ $ticket_comments .= '<tr><th colspan="1">Message</th><td colspan="3">'.$row["requirement_list1"].'</td></tr>'; }
    if($row['link_details'] != ""){ $ticket_comments .= '<tr><th colspan="1">Link</th><td colspan="3">'.$row["link_details"].'</td></tr>'; }
    if($row['remarks'] != ""){ $ticket_comments .= '<tr><th colspan="1">Remakrs</th><td colspan="3">'.$row["remarks"].'</td></tr>'; }
    if($file_references != ""){
        $ticket_comments .= '<tr><th colspan="1">Files</th><td colspan="3">'.$file_references.'</td></tr>';
    }
    if($file_references2 != ""){
        $ticket_comments .= '<tr><th colspan="1">View</th><td colspan="3">'.$file_references2.'</td></tr>';
    }
    $ticket_comments .= '</table>';
}
echo $ticket_comments;
?>