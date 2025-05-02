<?php
//admin_ticketstatus_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'),true);

if($db == ''){
    include "../newConfig.php";
    include "number_format_ind.php";
    include "header_head.php";
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
}
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); $user_code = $_SESSION['userid'];
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = "";
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}

$sql = "SELECT * FROM `ticket_transaction_types`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $dev_types_code[$row['code']] = $row['code']; $dev_types_name[$row['code']] = $row['description']; }

$sql = "SELECT DISTINCT(received_by) as received_by FROM `ticket_management_system`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $rcvdby_code[$row['received_by']] = $row['received_by']; }

$sql = "SELECT DISTINCT(assignee) as assignee FROM `ticket_management_system`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $asgdto_name[$row['assignee']] = $row['assignee']; }

$sql = "SELECT DISTINCT(requirement_list2) as proj_on FROM `ticket_management_system`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $project_name[$row['proj_on']] = $row['proj_on']; }

$sql = "SELECT * FROM `ticket_work_status` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $status_name[$row['description']] = $row['description']; }

$sql = "SELECT * FROM `log_useraccess` WHERE `account_access` = 'ATS'"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){ $usr_code[$row['empcode']] = $row['empcode']; $usr_name[$row['empcode']] = $row['username']; }

$sql = "SELECT DISTINCT(dblist) as dblist FROM `log_useraccess`"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){ $database_names[$row['dblist']] = $row['dblist']; }
if($_SERVER['REMOTE_ADDR'] == "49.205.133.247" || $user_code == "PST-0757"){
    $assigned_to = "Mallikarjuna";
    $status_notin = array(); $i = 0;
    foreach($status_name as $snin){
        if($snin == "Development Completed" || $snin == "Completed" || $snin == "On Hold" || $snin == "Duplicate" || $snin == "Cancelled" || $snin == "Closed" || $snin == "Waiting for More Info"){
            $status_notin[$i] = $snin; $i++;
        }
    }
    
    $createdby_filter = $status_filter = $dbname_filter = $devtype_filter = $project_filter = $priority_filter = ""; $statusnotin_flag = 1;
    $fdate = "2025-04-01"; $tdate = date("Y-m-d"); $created_by = $ticket_status = $databases = $dev_types = $projects = $priorities = "all"; $excel_type = "display";
}
else{
    $createdby_filter = $assignee_filter = $status_filter = $dbname_filter = $devtype_filter = $project_filter = $priority_filter = ""; $statusnotin_flag = 1;
    $fdate = $tdate = date("Y-m-d"); $created_by = $assigned_to = $ticket_status = $databases = $dev_types = $projects = $priorities = "all"; $excel_type = "display";
}

if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $created_by = $_POST['created_by'];                 if($created_by == "all"){ $createdby_filter = ""; } else{ $createdby_filter = " AND `received_by` = '$created_by'"; }
    $assigned_to = $_POST['assigned_to'];               if($assigned_to == "all"){ $assignee_filter = ""; } else{ $assignee_filter = " AND `assignee` = '$assigned_to'"; }
    $ticket_status = $_POST['ticket_status'];           if($ticket_status == "all"){ $status_filter = ""; } else{ $status_filter = " AND `ticket_status` = '$ticket_status'"; }
    $databases = $_POST['databases'];                   if($databases == "all"){ $dbname_filter = ""; } else{ $dbname_filter = " AND `client_name` = '$databases'"; }
    $dev_types = $_POST['dev_types'];                   if($dev_types == "all"){ $devtype_filter = ""; } else{ $devtype_filter = " AND `development_type` = '$dev_types'"; }
    $projects = $_POST['projects'];                     if($projects == "all"){ $project_filter = ""; } else{ $project_filter = " AND `requirement_list2` = '$projects'"; }
    $priorities = $_POST['priorities'];                 if($priorities == "all"){ $priority_filter = ""; } else{ $priority_filter = " AND `ticket_priority` = '$priorities'"; }

    $status_notin = array(); $statusnotin_list = "";
    $i = 0;
    foreach($_POST['status_notin'] as $snin){
        $status_notin[$i] = $snin;
        if($snin == "none"){
            $statusnotin_flag = 0; $statusnotin_list = "";
        }
        else{
            if($statusnotin_list == ""){ $statusnotin_list = $snin; } else{ $statusnotin_list = $statusnotin_list."','".$snin; }
        }
        $i++;
    }
    if($statusnotin_flag == 0){ $stsnotin_filter = ""; } else{ $stsnotin_filter = " AND `ticket_status` NOT IN ('$statusnotin_list')"; }

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/TicketStatus-Excel.php?fdate=".$fdate."&tdate=".$tdate."&created_by=".$created_by."&assigned_to=".$assigned_to."&ticket_status=".$ticket_status;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script src="../../col/jquery-3.5.1.js"></script>
        <script src="../../col/jquery.dataTables.min.js"></script>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
                max-width: 0%;
            }
            .col-md-5{
                position: relative;  left: 200px;
            }
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }
            table thead,
            table tfoot {
                position: sticky;
            }
            table thead {
            inset-block-start: 0; /* "top" */
            }
            table tfoot {
            inset-block-end: 0; /* "bottom" */
            }
        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
                
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center"   width="1300px">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Ticket Status Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="admin_ticketstatus_masterreport.php" method="post">
            <?php } else { ?>
            <form action="admin_ticketstatus_masterreport.php?db=<?php echo $db; ?>" method="post">
            <?php } ?>
            <form action="admin_ticketstatus_masterreport.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Client Name</label>
                                    <select name="databases" id="databases" class="form-control select2">
                                        <option value="all" <?php if($databases == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($database_names as $dname){ if($dname != ""){ ?>
                                        <option value="<?php echo $dname; ?>" <?php if($databases == $dname){ echo "selected"; } ?>><?php echo $dname; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Created By</label>
                                    <select name="created_by" id="created_by" class="form-control select2">
                                        <option value="all" <?php if($created_by == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($rcvdby_code as $rcode){ if($usr_name[$rcode] != ""){ ?>
                                        <option value="<?php echo $rcode; ?>" <?php if($created_by == $rcode){ echo "selected"; } ?>><?php echo $usr_name[$rcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Assigned To</label>
                                    <select name="assigned_to" id="assigned_to" class="form-control select2">
                                        <option value="all" <?php if($assigned_to == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($asgdto_name as $aname){ if($aname != ""){ ?>
                                        <option value="<?php echo $aname; ?>" <?php if($assigned_to == $aname){ echo "selected"; } ?>><?php echo $aname; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Status</label>
                                    <select name="ticket_status" id="ticket_status" class="form-control select2">
                                        <option value="all" <?php if($ticket_status == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($status_name as $sname){ if($sname != ""){ ?>
                                        <option value="<?php echo $sname; ?>" <?php if($ticket_status == $sname){ echo "selected"; } ?>><?php echo $sname; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div><br/>
                                <div class="m-2 form-group">
                                    <label>Development Type</label>
                                    <select name="dev_types" id="dev_types" class="form-control select2">
                                        <option value="all" <?php if($dev_types == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($dev_types_code as $aname){ if($dev_types_name[$aname] != ""){ ?>
                                        <option value="<?php echo $aname; ?>" <?php if($dev_types == $aname){ echo "selected"; } ?>><?php echo $dev_types_name[$aname]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Project</label>
                                    <select name="projects" id="projects" class="form-control select2">
                                        <option value="all" <?php if($projects == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($project_name as $aname){ if($aname != ""){ ?>
                                        <option value="<?php echo $aname; ?>" <?php if($projects == $aname){ echo "selected"; } ?>><?php echo $aname; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Priority</label>
                                    <select name="priorities" id="priorities" class="form-control select2">
                                        <option value="all" <?php if($priorities == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="low" <?php if($priorities == "low"){ echo "selected"; } ?>>-Low-</option>
                                        <option value="medium" <?php if($priorities == "medium"){ echo "selected"; } ?>>-Medium-</option>
                                        <option value="high" <?php if($priorities == "high"){ echo "selected"; } ?>>-High-</option>
                                        <option value="urgent" <?php if($priorities == "urgent"){ echo "selected"; } ?>>-Urgent-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Status Not In</label>
                                    <select name="status_notin[]" id="status_notin" class="form-control select2" multiple >
                                        <option value="none" <?php foreach($status_notin as $snin){ if($snin == "none"){ echo "selected"; } } ?>>-None-</option>
                                        <?php foreach($status_name as $sname){ if($sname != ""){ ?>
                                        <option value="<?php echo $sname; ?>" <?php foreach($status_notin as $snin){ if($snin == $sname){ echo "selected"; } } ?>><?php echo $sname; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td>
                <div id='control_sh'>
                    <?php
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if($act_col_numbs[$key_id] == "sl_no" || $nac_col_numbs[$key_id1] == "sl_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sl.No</span>'; }
                            else if($act_col_numbs[$key_id] == "date" || $nac_col_numbs[$key_id1] == "date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Date</span>'; }
                            else if($act_col_numbs[$key_id] == "trnum" || $nac_col_numbs[$key_id1] == "trnum"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="trnum" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Ticket No.</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_client" || $nac_col_numbs[$key_id1] == "ticket_client"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_client" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Client</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_trtype" || $nac_col_numbs[$key_id1] == "ticket_trtype"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_trtype" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transaction Type</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_assignee" || $nac_col_numbs[$key_id1] == "ticket_assignee"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_assignee" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Assigned To</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_priority" || $nac_col_numbs[$key_id1] == "ticket_priority"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_priority" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Priority</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_devtype" || $nac_col_numbs[$key_id1] == "ticket_devtype"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_devtype" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Development Type</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_dbtype" || $nac_col_numbs[$key_id1] == "ticket_dbtype"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_dbtype" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Project On</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_work" || $nac_col_numbs[$key_id1] == "ticket_work"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_work" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Work</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_link" || $nac_col_numbs[$key_id1] == "ticket_link"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_link" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Link</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_remarks" || $nac_col_numbs[$key_id1] == "ticket_remarks"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_remarks" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Comments</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_references" || $nac_col_numbs[$key_id1] == "ticket_references"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_references" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Reference</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_status" || $nac_col_numbs[$key_id1] == "ticket_status"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_status" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Status</span>'; }
                            else if($act_col_numbs[$key_id] == "ticket_createdby" || $nac_col_numbs[$key_id1] == "ticket_createdby"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="ticket_createdby" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Created By</span>'; }
                            else{ }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <table id="mine" class="tbl" align="center"  style="width:1300px;">
            <thead class="thead3" align="center" style="width:1212px;">
                <tr align="center">
                <?php
                for($i = 1;$i <= $col_count;$i++){
                    $key_id = "A:1:".$i;
                    if($act_col_numbs[$key_id] == "sl_no"){ echo "<th>Sl.No</th>"; }
                    else if($act_col_numbs[$key_id] == "date"){ echo "<th>Date</th>"; }
                    else if($act_col_numbs[$key_id] == "trnum"){ echo "<th>Ticket No.</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_client"){ echo "<th>Client</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_trtype"){ echo "<th>Transaction Type</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_assignee"){ echo "<th>Assigned To</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_priority"){ echo "<th>Priority</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_devtype"){ echo "<th>Development Type</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_dbtype"){ echo "<th>Project On</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_work"){ echo "<th style='min-width:250px;'>Work</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_link"){ echo "<th>Link</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_remarks"){ echo "<th>Comments</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_references"){ echo "<th>Reference</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_status"){ echo "<th>Status</th>"; }
                    else if($act_col_numbs[$key_id] == "ticket_createdby"){ echo "<th>Created By</th>"; }
                    else{ }
                }
                ?>
                </tr>
            </thead>
            
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                
                $sql_record = "SELECT * FROM `ticket_management_system` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `dflag` = '0'".$createdby_filter."".$assignee_filter."".$status_filter."".$dbname_filter."".$devtype_filter."".$stsnotin_filter."".$project_filter."".$priority_filter." ORDER BY `id` ASC";
                $query = mysqli_query($conn,$sql_record); $c = 0;
                while($row = mysqli_fetch_assoc($query)){ $c++; $file_references = "";
                    echo "<tr>";
                    for($i = 1;$i <= $col_count;$i++){
                        $key_id = "A:1:".$i;
                        if($act_col_numbs[$key_id] == "sl_no"){ echo "<td title='Sl.No'>".$c."</td>"; }
                        else if($act_col_numbs[$key_id] == "date"){ echo "<td title='Date'>".date('d.m.Y',strtotime($row['date']))."</td>"; }
                        else if($act_col_numbs[$key_id] == "trnum"){ echo "<td title='Ticket No.'>".$row['trnum']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_client"){ echo "<td title='Client'>".$row['client_name']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_trtype"){ echo "<td title='Transaction Type'>".$row['ticket_name']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_assignee"){ echo "<td title='Assigned To'>".$row['assignee']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_priority"){ echo "<td title='Priority'>".$row['ticket_priority']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_devtype"){ echo "<td title='Development Type'>".$dev_types_name[$row['development_type']]."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_dbtype"){ echo "<td title='Project On'>".$row['requirement_list2']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_work"){ echo "<td title='Work'>".$row['requirement_list1']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_link"){ echo "<td title='Link'>".$row['link_details']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_remarks"){ echo "<td title='Comments'>".$row['remarks']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_references"){
                            if($row['file_path1'] != ""){
                                $file_references .= '<a href="../'.$row["file_path1"].'" download title="download"><i class="fa-solid fa-angles-down" style="font-size:20px;"></i></a>&ensp;';
                                $file_references .= '<a href="../'.$row["file_path1"].'" target="_BLANK" title="view"><i class="fa-solid fa-eye" style="font-size:20px;"></i></a><br/>';
                            }
                            if($row['file_path2'] != ""){
                                    $file_references .= '<a href="../'.$row["file_path2"].'" download title="download"><i class="fa-solid fa-angles-down" style="font-size:20px;"></i></a>&ensp;';
                                    $file_references .= '<a href="../'.$row["file_path2"].'" target="_BLANK" title="view"><i class="fa-solid fa-eye" style="font-size:20px;"></i></a><br/>';
                            }
                            if($row['file_path3'] != ""){
                                $file_references .= '<a href="../'.$row["file_path3"].'" download title="download"><i class="fa-solid fa-angles-down" style="font-size:20px;"></i></a>&ensp;';
                                $file_references .= '<a href="../'.$row["file_path3"].'" target="_BLANK" title="view"><i class="fa-solid fa-eye" style="font-size:20px;"></i></a><br/>';
                            }
                            if($row['file_path4'] != ""){
                                $file_references .= '<a href="../'.$row["file_path4"].'" download title="download"><i class="fa-solid fa-angles-down" style="font-size:20px;"></i></a>&ensp;';
                                $file_references .= '<a href="../'.$row["file_path4"].'" target="_BLANK" title="view"><i class="fa-solid fa-eye" style="font-size:20px;"></i></a><br/>';
                            } echo "<td title='Status'>".$file_references."</td>";
                        }
                        else if($act_col_numbs[$key_id] == "ticket_status"){ echo "<td title='Status'>".$row['ticket_status']."</td>"; }
                        else if($act_col_numbs[$key_id] == "ticket_createdby"){ echo "<td title='Status'>".$usr_name[$row['received_by']]."</td>"; }
                        else{ }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script>
            function update_masterreport_status(a){
                var file_url = '<?php echo $field_href[0]; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
        $(document).ready(function(){
            var table =  $('#mine').DataTable({
                paging: false,
            });
            
            $("#hide_show_all").on("change",function(){
                var hide = $(this).is(":checked");
                $(".hide_show").prop("checked", hide);
                if(hide){
                    $('#mine tr th').hide(100);
                    $('#mine tr td').hide(100);
                }else{
                    $('#mine tr th').show(100);
                    $('#mine tr td').show(100);
                }
            });

            $(".hide_show").on("change",function(){
                var hide = $(this).is(":checked");
                
                var all_ch = $(".hide_show:checked").length == $(".hide_show").length;

                $("#hide_show_all").prop("checked", all_ch);
                
                var ti = $(this).index(".hide_show");
                
                $('#mine tr').each(function(){
                    if(hide){
                        $('td:eq(' + ti + ')',this).hide(100);
                        $('th:eq(' + ti + ')',this).hide(100);
                    }else{
                        $('td:eq(' + ti + ')',this).show(100);
                        $('th:eq(' + ti + ')',this).show(100);
                    }
                });

            });
            //$('#mine tfoot th').each( function () {
                //var title = $(this).text();
                //$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //} );

            $('#myInput').keyup( function() {
                    table.draw();
                } );
                $('input.column_filter').on( 'keyup click', function () {
                    filterColumn( $(this).parents('tr').attr('data-column') );
                });
            
            });
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>