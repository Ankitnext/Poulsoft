<?php
//broiler_display_requests.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['requests'] = $cid; } else{ $cid = $_SESSION['requests']; }
$href = explode("/", $_SERVER['REQUEST_URI']); $url = $href[1]; $file_name = explode("?", $href[1]);
$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC";
$query = mysqli_query($conn,$sql); $link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $dlink = $alink = $elink = $ulink = $flink = array();
    while($row = mysqli_fetch_assoc($query)){
        $dlink = str_replace(",","','",$row['displayaccess']);
        $alink = str_replace(",","','",$row['addaccess']);
        $elink = str_replace(",","','",$row['editaccess']);
        $rlink = str_replace(",","','",$row['deleteaccess']);
        $plink = str_replace(",","','",$row['printaccess']);
        $ulink = str_replace(",","','",$row['otheraccess']);
        $sector_access = $row['loc_access'];
        $cgroup_access = $row['cgroup_access'];
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
    }
    $aid = 0;
    $flink = explode("','",$dlink); $acount = 0; foreach($flink as $flinks){ if($flinks == $cid){ $aid = 1; } }
    if($user_type == "S"){ $acount = 1; }
    else if($aid == 1){ $acount = 1; }
    else{ $acount = 0; }
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        .hide{
            visibility:hidden;
        }
    </style>
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            $gp_id = $gc_id = $gp_name = $gp_link = $gp_link = $p_id = $c_id = $p_name = $p_link = array(); $tickett_status = "all";
            $sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $gp_id = $row['parentid'];
                $gc_id[$row['childid']] = $row['childid'];
                $gp_name[$row['childid']] = $row['name'];
                $gp_link[$row['childid']] = $row['href'];
            }
            $add_link_acc = $edt_link_acc = $del_link_acc = $pnt_link_acc = $upd_link_acc = "";
            $alink = explode("','",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; $add_link_acc = $add_link_acc.",".$alink1; }
            $elink = explode("','",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; $edt_link_acc = $edt_link_acc.",".$elink1; }
            $rlink = explode("','",$rlink); foreach($rlink as $rlink1){ $del_acc[$rlink1] = $rlink1; $del_link_acc = $del_link_acc.",".$rlink1; }
            $plink = explode("','",$plink); foreach($plink as $plink1){ $pnt_acc[$plink1] = $plink1; $pnt_link_acc = $pnt_link_acc.",".$plink1; }
            $ulink = explode("','",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; $upd_link_acc = $upd_link_acc.",".$ulink1; }
            if(!empty($add_acc[$gp_id."A"])){ $add_flag = 1; $add_link = $gp_link[$gp_id."A"]; } else { $add_link = ""; $add_flag = 0; }
            if(!empty($edt_acc[$gp_id."E"])){ $edit_flag = 1; $edit_link = $gp_link[$gp_id."E"]; } else { $edit_link = ""; $edit_flag = 0; }
            if(!empty($del_acc[$gp_id."R"])){ $delete_flag = 1; $delete_link = $gp_link[$gp_id."R"]; } else { $delete_link = ""; $delete_flag = 0; }
            if(!empty($pnt_acc[$gp_id."P"])){ $print_flag = 1; $print_link = $gp_link[$gp_id."P"]; } else { $print_link = ""; $print_flag = 0; }
            if(!empty($upd_acc[$gp_id."U"])){ $update_flag = 1; $update_link = $gp_link[$gp_id."U"]; } else { $update_link = ""; $update_flag = 0; }
            
            if($_SERVER['REMOTE_ADDR'] != "49.205.135.183"){
                $fsdate = $cid."-fdate"; $tsdate = $cid."-tdate"; $psdate = $cid."-pdate"; $btype = $cid."-type"; 
                if(isset($_POST['submit']) == true){
                    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
                    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
                    $pdate = date("Y-m-d",strtotime($_POST['pdate']));
                    $based_on = $_POST['based_on'];
                    $tickett_status = $_POST['tickett_status']; if($tickett_status == "all"){ $status_filter = ""; } else{ $status_filter = " AND `ticket_status` = '$tickett_status'"; }
                    $_SESSION[$fsdate] = $fdate;
                    $_SESSION[$tsdate] = $tdate;
                    $_SESSION[$psdate] = $pdate;
                    $_SESSION[$btype] = $based_on;
                }
                else {
                    $fdate = $tdate = date("Y-m-d");
                    if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                    if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }
                    if(!empty($_SESSION[$psdate])){ $pdate = date("Y-m-d",strtotime($_SESSION[$psdate])); }
                    if(!empty($_SESSION[$btype])){ $based_on = $_SESSION[$btype]; }
                }
                if($based_on == "bw_dates"){
                    $ticket_filter = " AND `date` >= '$fdate' AND `date` <= '$tdate'";
                }
                else if($based_on == "sg_dates"){
                    $ticket_filter = " AND `date` = '$pdate'";
                }
                else if($based_on == "trnums"){
                    $trnums = str_replace(",","','",$_POST['tck_no']);
                    if($trnums == ""){ $ticket_filter = ""; } else{ $ticket_filter = " AND (`trnum` IN ('$trnums') || `trnum` LIKE '%$trnums%')"; }
                }
                else{
                    $based_on = "bw_dates";
                    $ticket_filter = " AND `date` >= '$fdate' AND `date` <= '$tdate'";
                }
            }
            else{
                $ticket_filter = "";
            }

            $sql = "SELECT * FROM `ticket_work_status` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $status_name[$row['description']] = $row['description']; }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <?php if($_SERVER['REMOTE_ADDR'] != "49.205.135.183"){ ?>
                            <form action="<?php echo $url; ?>" method="post" onsubmit="return checkval();">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fdate">Based On: </label>
                                            <select name="based_on" id="based_on" class="form-control select2" onchange="update_filter_details();">
                                                <option value="bw_dates" <?php if($based_on == "bw_dates"){ echo "selected"; } ?>>B/w Dates</option>
                                                <option value="sg_dates" <?php if($based_on == "sg_dates"){ echo "selected"; } ?>>Single Date</option>
                                                <option value="trnums" <?php if($based_on == "trnums"){ echo "selected"; } ?>>Tickets</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-12" id="bw_dates">
                                            <div class="row">
                                                <div class="form-group" style="width:100px;"><label for="fdate">From Date: </label><input type="text" class="form-control datepicker" name="fdate" id="fdate" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:90px;" readonly ></div>
                                                <div class="form-group" style="width:100px;"><label for="tdate">To Date: </label><input type="text" class="form-control datepicker" name="tdate" id="tdate" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="width:90px;" readonly ></div>
                                                <div class="form-group" style="width:100px;"><label for="tickett_status">Status: </label> 
                                                    <select name="tickett_status" id="tickett_status" class="form-control select2">
                                                        <option value="all" <?php if($tickett_status == "all"){ echo "selected"; } ?>>-All-</option>
                                                        <?php foreach($status_name as $sname){ if($sname != ""){ ?>
                                                        <option value="<?php echo $sname; ?>" <?php if($tickett_status == $sname){ echo "selected"; } ?>><?php echo $sname; ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group" style="width:100px;"><br/><button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button></div>
                                            </div>
                                            </div>
                                            <div class="col-md-12" id="sg_dates" style="display:none;">
                                            <div class="row">
                                                <div class="form-group" style="width:100px;"><label for="pdate">Date: </label><input type="text" class="form-control datepicker" name="pdate" id="pdate" value="<?php echo date("d.m.Y",strtotime($pdate)); ?>" style="width:90px;" readonly ></div>
                                                <div class="form-group" style="width:100px;"><br/><button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button></div>
                                            </div>
                                            </div>
                                            <div class="col-md-12" id="trnums" style="display:none;">
                                            <div class="row">
                                                <div class="form-group" style="width:200px;"><label for="tck_no">Ticket No.: </label><input type="text" class="form-control " name="tck_no" id="tck_no" value="<?php echo $trnums; ?>" style="width:190px;"></div>
                                                <div class="form-group" style="width:100px;"><br/><button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button></div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2"><br/>
                                        
                                    </div>
                                    <div class="col-md-4" align="right">
                                    <?php if($add_flag == 1){ ?>
                                        <button type="button" class="btn bg-green" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                            <?php } else{ ?>
                           <div class="float-left"><h3 class="card-title">Requests</h3></div>
                            <div class="float-right">
                            <?php if($add_flag == 1){ ?>
                                <button type="button" class="btn bg-green" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Ticket No</th>
										<th>Date</th>
										<th>Client</th>
										<th>Work</th>
										<th>Created By</th>
										<th>Assigned To</th>
                                        <th>Priority</th>
										<th>Status</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                         $sql = "SELECT * FROM `log_useraccess` WHERE `account_access` = 'ATS'"; $query = mysqli_query($conns,$sql);
                                         while($row = mysqli_fetch_assoc($query)){ $usr_code[$row['empcode']] = $row['empcode']; $usr_name[$row['empcode']] = $row['username']; }                                         

                                        $sql = "SELECT * FROM `ticket_management_system` WHERE `dflag` = '0'".$ticket_filter."".$status_filter." ORDER BY `id` DESC"; $query = mysqli_query($sconn,$sql); $c = 0; //`addedemp` = '$user_code' OR `assignee` LIKE '$user_code' AND 
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&id=".$id;
                                            $delete_url = $delete_link."?utype=delete&id=".$id;
                                            $print_url = $print_link."?utype=print&id=".$id;
                                            $authorize_url = $update_link."?utype=authorize&id=".$id;
                                            $request_flag = $update_link."?utype=request_flag&id=".$id;
                                            $accept_flag = $update_link."?utype=accept_flag&id=".$id;
                                            $dev_status = $update_link."?utype=dev_status&id=".$id;
                                            $test_status = $update_link."?utype=test_status&id=".$id;
                                            $closure = $update_link."?utype=closure&id=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&id=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&id=".$id;
                                            }
                                            $id2 = $row['trnum']."@".$row['ticket_status'];
                                    ?>
                                    <tr>
                                        <td><?php echo $row['trnum']; ?></td>
										<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
										<td><?php echo $row['client_name']; ?></td>
										<td><?php echo $row['requirement_list1']; ?></td>
										<td><?php echo $usr_name[$row['received_by']]; ?></td>
										<td><?php echo $row['assignee']; ?></td>
                                        <td><?php echo $row['ticket_priority']; ?></td>
										<td><?php echo $row['ticket_status']; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                    //echo '<a href="javascript:void(0)" data-toggle="modal" data-target="#modal-10" id="'.$id2.'" onclick="fetch_editticket_details(this.id);"><i class="fa fa-pen" style="color:brown;"></i></a>&ensp;&ensp;';
                                                }
                                                if($edit_flag == 1 && $row['closure'] == 0){
                                                    //echo "<a href='".$request_flag."'><i class='fa fa-arrow-right-from-bracket' style='color:blue;' title='Request'></i></a>&ensp;&ensp;";
                                                    echo '<a href="javascript:void(0)" data-toggle="modal" data-target="#modal-11" id="'.$id2.'" onclick="fetch_ticket_details(this.id);"><i class="far fa-eye" style="color:blue;"></i></a>&ensp;&ensp;';
                                                    //echo "<a href='".$accept_flag."'><i class='fa fa-check-double' style='color:green;' title='Accept'></i></a>&ensp;&ensp;";
                                                    //echo "<a href='javascript:void(0)' id='devstat@".$id."' value='".$dev_status."' onclick='update_status(this.id)'><i class='fa fa-circle-check' style='color:purple;' title='Development Status'></i></a>&ensp;&ensp;";
                                                    //echo "<a href='javascript:void(0)' id='teststat@".$id."' value='".$test_status."' onclick='update_status(this.id)'><i class='fa fa-user-check' style='color:red;' title='Testing Status'></i></a>&ensp;&ensp;";
                                                    //echo "<a href='".$closure."'><i class='fa fa-arrow-rotate-right' style='color:green;' title='Closure'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
                                                    //echo "<a href='".$delete_url."'><i class='fa fa-trash' style='color:red;' title='delete'></i></a>&ensp;&ensp;";
                                                }
                                                if($print_flag == 1){
                                                    //echo "<a href='".$print_url."'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                                if($update_flag == 1){
                                                    /*if($row['active'] == 1){
                                                        echo "<a href='".$update_url."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;&ensp;";
                                                    }
                                                    else{
                                                        echo "<a href='".$update_url."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;&ensp;";
                                                    }*/
                                                    //echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
                                                    //echo "<a href='".$authorize_url."'><i class='far fa-eye' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
                                                }
                                                echo '<a href="javascript:void(0)" data-toggle="modal" data-target="#modal-13" id="'.$id2.'" onclick="add_status(this.id);"><i class="fa-solid fa-square-up-right" style="color:blue;" title="Request"></i></a>&ensp;&ensp;';
                                                echo '<a href="javascript:void(0)" data-toggle="modal" data-target="#modal-12" id="'.$id2.'" onclick="fetch_ticket_comments(this.id);"><i class="fa-solid fa-comments" style="color:green;"></i></a>&ensp;&ensp;';
                                            }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <div class="modal fade" id="modal-13" role="dialog" aria-labelledby="modal-13">
                                <div class="modal-dialog modal-dialog-centered modal-min" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body text-center">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h3>Update Work Status</h3>
                                            <p>Select Work Status &amp; Click on modify</p>
                                            <form action="broiler_update_ticket_status.php" method="post" onsubmit="return modify_ticket_status()" enctype="multipart/form-data">
                                                <div class="form-group" style="visibility:visible;text-align:center;"><br/>
                                                    <table class="table">
                                                        <tr>
                                                            <th><label>Ticket<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td><input type="text" name="trnum" id="trnum" class="form-control" value="" style="width:120px;" readonly ></td>
                                                        </tr>
                                                        <tr>
                                                            <th><label>Status<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td>
                                                                <select name="ticket_status" id="ticket_status" class="form-control select2" style="width: 200px;">
                                                                    <option value="select">Select</option>
                                                                    <?php
                                                                        $sql = "SELECT * FROM `ticket_work_status` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                                        while($row = mysqli_fetch_assoc($query)){
                                                                    ?>
                                                                        <option value="<?php echo $row['description']; ?>"><?php echo $row['description']; ?></option>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </select>
                                                                
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th><label>Remarks</label></th>
                                                            <td><textarea name="remarks" id="remarks" class="form-control" value="" style="width:200px;"></textarea></td>
                                                        </tr>
                                                        <tr>
                                                            <th><label>File</label></th>
                                                            <td>
                                                                <div class="clip-upload">
                                                                    <label for="file_status1">
                                                                    <i class="fa fa-paperclip" aria-hidden="true"></i></label>
                                                                    <input type="file" class="file-input" name="file_status1" id="file_status1" style="width:10px;visibility:hidden;">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr style="visibility:hidden;">
                                                            <th><label>Old Status</label></th>
                                                            <td><input type="text" name="old_status" id="old_status" class="form-control" value="" style="width:120px;" readonly ></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <button type="submit" name="submit1" value="submit1" class="btn btn-primary shadow-none">Modify</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="modal-12" role="dialog" aria-labelledby="modal-12">
                                <div class="modal-dialog modal-dialog-centered modal-min" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body text-center">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form action="broiler_add_ticket_comments.php" method="post" onsubmit="return add_newcomment()" enctype="multipart/form-data">
                                                        <div class="card card-success card-outline direct-chat direct-chat-success">
                                                            <div class="card-header">
                                                                <h3 class="card-title"><input type="text" name="chat_ticket" id="chat_ticket" class="form-control" readonly /></h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="direct-chat-messages" id="comment_body">
                                                                </div>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="input-group">
                                                                    <div class="clip-upload">
                                                                        <label for="file_status2">
                                                                        <i class="fa fa-paperclip" aria-hidden="true"></i></label>
                                                                        <input type="file" class="file-input" name="file_status2" id="file_status2" style="width:10px;visibility:hidden;">
                                                                    </div>
                                                                    <textarea name="new_comment" id="new_comment" placeholder="Type Message ..." class="form-control" style="height:38px;"></textarea>
                                                                    <span class="input-group-append">
                                                                    <button type="submit" class="btn btn-success">Send</button>
                                                                    <button type="button" class="btn btn-warning" onclick="refresh_ticket_comments();">Re-load</button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="modal-11" role="dialog" aria-labelledby="modal-11">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body text-center">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card card-primary card-outline direct-chat direct-chat-primary">
                                                        <div class="card-header">
                                                            <h3 class="card-title"><input type="text" name="view_ticket" id="view_ticket" class="form-control" readonly /></h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="direct-chat-messages" id="view_body">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="modal-10" role="dialog" aria-labelledby="modal-10">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body text-center">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card card-primary card-outline direct-chat direct-chat-primary">
                                                        <div class="card-header">
                                                            <h3 class="card-title"><input type="text" name="edit_ticket" id="edit_ticket" class="form-control" readonly /></h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="direct-chat-messages" id="edit_body">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            /*function loadFile(a){
                var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                var b =document.getElementById(a).value;
                var c = b.replace(/^.*[\\\/]/, '');
                Toast.fire({ icon: 'success', title: ' file selected successfully.' });
            }*/
            function update_status(x){
                alert(x);
            }
            function add_status(a){
                document.getElementById("trnum").value = "";
                document.getElementById("old_status").value = "";
                document.getElementById("remarks").innerHTML = "";
                document.getElementById("file_status1").innerHTML = "";
                var b = a.split("@");
                document.getElementById("trnum").value = b[0];
                document.getElementById("old_status").value = b[1];
                $('.select2').select2();
                document.getElementById("ticket_status").value = b[1];
                $('.select2').select2();
            }
            function fetch_ticket_comments(a){
                document.getElementById("chat_ticket").value = "";
                document.getElementById("new_comment").value = "";
                document.getElementById("comment_body").innerHTML = "";
                var b = a.split("@");
                document.getElementById("chat_ticket").value = b[0];

                var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

                var trnum = b[0];
                if(trnum == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate trnum.' });
                }
                else{
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_ticket_comments.php?trnum="+trnum;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var item_list = this.responseText;
                                if(item_list != ""){
                                    $('#comment_body').append(item_list);
                                }
                                else{
                                    Toast.fire({ icon: 'success', title: 'No Comments!.' });
                                }
                            }
                        }
                }
            }
            function fetch_ticket_details(a){
                document.getElementById("view_ticket").value = "";
                document.getElementById("view_body").innerHTML = "";
                var b = a.split("@");
                document.getElementById("view_ticket").value = b[0];

                var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

                var trnum = b[0];
                if(trnum == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate trnum.' });
                }
                else{
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_view_ticket_details.php?trnum="+trnum;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var item_list = this.responseText;
                                if(item_list != ""){
                                    $('#view_body').append(item_list);
                                }
                                else{
                                    Toast.fire({ icon: 'success', title: 'No Comments!.' });
                                }
                            }
                        }
                }
            }
            function fetch_editticket_details(a){
                document.getElementById("edit_ticket").value = "";
                document.getElementById("edit_body").innerHTML = "";
                var b = a.split("@");
                document.getElementById("edit_ticket").value = b[0];

                var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

                var trnum = b[0];
                if(trnum == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate trnum.' });
                }
                else{
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_edit_ticket_details.php?trnum="+trnum;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var item_list = this.responseText;
                                if(item_list != ""){
                                    $('#edit_body').append(item_list);
                                }
                                else{
                                    Toast.fire({ icon: 'success', title: 'No Comments!.' });
                                }
                            }
                        }
                }
            }
            function modify_ticket_status(){
                var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
                });
                var trnum = document.getElementById("trnum").value;
                var ticket_status = document.getElementById("ticket_status").value;
                var old_status = document.getElementById("old_status").value;
                var l = true;
                if(trnum == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate trnum.' });
                    l = false;
                }
                else if(ticket_status == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate Status.' });
                    l = false;
                }
                else if(ticket_status == old_status){
                    Toast.fire({ icon: 'error', title: 'Change Ticket Status to make changes.' });
                    l = false;
                }
                else{
                    l = true;
                    /*
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_update_ticket_status.php?trnum="+trnum+"&ticket_status="+ticket_status+"&remarks="+remarks+"&file_status1="+file_status1;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var item_list = this.responseText;
                                if(item_list.match("failed")){
                                    Toast.fire({ icon: 'error', title: 'Failed to update.' });
                                }
                                else if(item_list.match("success")){
                                    Toast.fire({ icon: 'success', title: 'modified successfully.' });
                                    location.reload();
                                }
                                else{
                                    Toast.fire({ icon: 'success', title: 'Update status not available, try again.' });
                                }
                            }
                        }
                    */
                }
                if(l = true){
                    return true;
                }
                else{
                    return false;
                }
            }
            function add_newcomment(){
                var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
                });
                var trnum = document.getElementById("chat_ticket").value;
                var new_comment = document.getElementById("new_comment").value;
                var file_status2 = document.getElementById("file_status2").value;
                if(trnum == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate trnum.' });
                    return false;
                }
                else if(new_comment == "" && file_status2 == ""){
                    Toast.fire({ icon: 'error', title: 'Enter Message to send.' });
                    return false;
                }
                else{
                    return true;
                    /*var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_add_ticket_comments.php?trnum="+trnum+"&new_comment="+new_comment;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var item_list = this.responseText;
                                if(item_list.match("failed")){
                                    Toast.fire({ icon: 'error', title: 'Failed to Send.' });
                                }
                                else if(item_list.match("success")){
                                    Toast.fire({ icon: 'success', title: 'Sent successfully.' });
                                    document.getElementById("new_comment").value = "";
                                    refresh_ticket_comments();
                                }
                                else{
                                    Toast.fire({ icon: 'success', title: 'Update status not available, try again.' });
                                }
                            }
                        }*/
                }
            }
            function refresh_ticket_comments(){
                document.getElementById("new_comment").value = "";
                document.getElementById("comment_body").innerHTML = "";

                var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

                var trnum = document.getElementById("chat_ticket").value
                if(trnum == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate trnum.' });
                }
                else{
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_ticket_comments.php?trnum="+trnum;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var item_list = this.responseText;
                                if(item_list != ""){
                                    $('#comment_body').append(item_list);
                                }
                                else{
                                    Toast.fire({ icon: 'success', title: 'No Comments!.' });
                                }
                            }
                        }
                }
            }
            function checkval(){
                var based_on = document.getElementById("based_on").value;
                var l = true;
                if(based_on == "trnums"){
                    var trnums = document.getElementById("tck_no").value;
                    if(trnums == ""){
                        alert("Please enter Ticket No.");
                        document.getElementById("tck_no").focus();
                        l = false;
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
            function update_filter_details(){
                var based_on = document.getElementById("based_on").value;
                if(based_on == "bw_dates"){
                    document.getElementById("sg_dates").style.display = "none";
                    document.getElementById("trnums").style.display = "none";
                    document.getElementById("bw_dates").style.display = "inline";
                }
                else if(based_on == "sg_dates"){
                    document.getElementById("bw_dates").style.display = "none";
                    document.getElementById("trnums").style.display = "none";
                    document.getElementById("sg_dates").style.display = "inline";
                }
                else if(based_on == "trnums"){
                    document.getElementById("bw_dates").style.display = "none";
                    document.getElementById("sg_dates").style.display = "none";
                    document.getElementById("trnums").style.display = "inline";
                }
                else{
                    document.getElementById("sg_dates").style.display = "none";
                    document.getElementById("trnums").style.display = "none";
                    document.getElementById("bw_dates").style.display = "inline";
                }
            }
            update_filter_details();
        </script>
        <?php
            }
            else{
        ?>
        <script>
            var x = confirm("You don't have access to this file\folder \n Kindly contact your admin for more details\support");
            if(x == true){
                window.location.href="logout.php";
            }
            else{
                window.location.href="logout.php";
            }
        </script>
        <?php
            }
        ?>
        <script>
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
		</script>
    <?php include "header_foot.php"; ?>
    </body>
</html>
<?php
}
else{
     header('location:index.php');
}
?>