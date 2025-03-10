<?php
//broiler_display_plant_portioning2.php
include "newConfig.php";
include "number_format_ind.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['plant_portioning2'] = $cid; } else{ $cid = $_SESSION['plant_portioning2']; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `master_form_tableaccess` WHERE `href` = '$href' AND `active` = '1'"; $query = mysqli_query($sconn,$sql);
while($row = mysqli_fetch_assoc($query)){ $table_name = $row['table_name']; } $table_session = $cid."tbl_access"; $_SESSION[$table_session] = $table_name;
$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC";
$query = mysqli_query($conn,$sql); $link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $dlink = $alink = $elink = $rlink = $plink = $ulink = $flink = array(); $sector_access = $cgroup_access = $user_type = "";
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
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            /*Check for Table Availability*/
            $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
            $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
            if(in_array("main_item_category", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_item_category LIKE poulso6_admin_broiler_broilermaster.main_item_category;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_bird_transferout", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_transferout LIKE poulso6_admin_broiler_broilermaster.broiler_bird_transferout;"; mysqli_query($conn,$sql1); }
            if(in_array("plant_receive_jobworks", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_receive_jobworks LIKE poulso6_admin_broiler_broilermaster.plant_receive_jobworks;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_bird_receivedin", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_receivedin LIKE poulso6_admin_broiler_broilermaster.broiler_bird_receivedin;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_bird_received_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_bird_received_details LIKE poulso6_admin_broiler_broilermaster.broiler_bird_received_details;"; mysqli_query($conn,$sql1); }
            if(in_array("plant_receivein_types", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_receivein_types LIKE poulso6_admin_broiler_broilermaster.plant_receivein_types;"; mysqli_query($conn,$sql1); }
            if(in_array("plant_hallal_types", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_hallal_types LIKE poulso6_admin_broiler_broilermaster.plant_hallal_types;"; mysqli_query($conn,$sql1); }
            if(in_array("prefix_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.prefix_master LIKE poulso6_admin_broiler_broilermaster.prefix_master;"; mysqli_query($conn,$sql1); }
            if(in_array("master_generator", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_generator LIKE poulso6_admin_broiler_broilermaster.master_generator;"; mysqli_query($conn,$sql1); }
            if(in_array("item_sizes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_sizes LIKE poulso6_admin_broiler_broilermaster.item_sizes;"; mysqli_query($conn,$sql1); }
            if(in_array("plant_bird_grading_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_grading_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_grading_details;"; mysqli_query($conn,$sql1); }
            if(in_array("plant_bird_grading_item_stocks", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_grading_item_stocks LIKE poulso6_admin_broiler_broilermaster.plant_bird_grading_item_stocks;"; mysqli_query($conn,$sql1); }
            if(in_array("plant_bird_portioning_consumed_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_portioning_consumed_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_portioning_consumed_details;"; mysqli_query($conn,$sql1); }
            if(in_array("plant_bird_portioning_produced_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_portioning_produced_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_portioning_produced_details;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_broiler_broilermaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }
            
            /*Check for Column Availability*/
            $sql='SHOW COLUMNS FROM `plant_bird_grading_item_stocks`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("avl_birds", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `plant_bird_grading_item_stocks` ADD `avl_birds` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT 'Available Stock Birds' AFTER `avg_amt`"; mysqli_query($conn,$sql); }
            if(in_array("avl_weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `plant_bird_grading_item_stocks` ADD `avl_weight` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT 'Available Stock Weight' AFTER `avl_birds`"; mysqli_query($conn,$sql); }

            $sql='SHOW COLUMNS FROM `account_summary`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("isize_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `isize_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Plant Item Size Code' AFTER `item_code`"; mysqli_query($conn,$sql); }
            if(in_array("plant_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `plant_batch` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Plant Batch No' AFTER `isize_code`"; mysqli_query($conn,$sql); }

            $sql='SHOW COLUMNS FROM `item_category`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("plant_portioning", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `plant_portioning` INT(100) NOT NULL DEFAULT '0' COMMENT 'Processing Plant Access Flag' AFTER `description`"; mysqli_query($conn,$sql); }
            if(in_array("row_cnt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `row_cnt` INT(100) NOT NULL DEFAULT '0' AFTER `plant_portioning`"; mysqli_query($conn,$sql); }
            if(in_array("plant_sort_order", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `plant_sort_order` INT(100) NOT NULL DEFAULT '0' AFTER `row_cnt`"; mysqli_query($conn,$sql); }
            if(in_array("main_category", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `main_category` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `description`"; mysqli_query($conn,$sql); }
            
            $sql='SHOW COLUMNS FROM `plant_bird_portioning_produced_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("mnubch_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `plant_bird_portioning_produced_details` ADD `mnubch_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `batch_no`"; mysqli_query($conn,$sql); }
            if(in_array("remarks2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `plant_bird_portioning_produced_details` ADD `remarks2` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `remarks`"; mysqli_query($conn,$sql); }
            
            $sql='SHOW COLUMNS FROM `plant_bird_portioning_consumed_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("mnubch_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `plant_bird_portioning_consumed_details` ADD `mnubch_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `batch_no`"; mysqli_query($conn,$sql); }
            if(in_array("remarks2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `plant_bird_portioning_consumed_details` ADD `remarks2` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `remarks`"; mysqli_query($conn,$sql); }
            
            $sql='SHOW COLUMNS FROM `account_summary`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("mnubch_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `mnubch_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `plant_batch`"; mysqli_query($conn,$sql); }
            
            $gp_id = $gc_id = $gp_name = $gp_link = $gp_link = $p_id = $c_id = $p_name = $p_link = array();
            $sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $gp_id = $row['parentid'];
                $gc_id[$row['childid']] = $row['childid'];
                $gp_name[$row['childid']] = $row['name'];
                $gp_link[$row['childid']] = $row['href'];
            }
            $add_link = $edit_link = $delete_link = $print_link = $update_link = "";
            $alink = explode("','",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; }
            $elink = explode("','",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; }
            $rlink = explode("','",$rlink); foreach($rlink as $rlink1){ $del_acc[$rlink1] = $rlink1; }
            $plink = explode("','",$plink); foreach($plink as $plink1){ $pnt_acc[$plink1] = $plink1; }
            $ulink = explode("','",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; }
            if(!empty($add_acc[$gp_id."A"])){ $add_flag = 1; $add_link = $gp_link[$gp_id."A"]; } else { $add_link = ""; $add_flag = 0; }
            if(!empty($edt_acc[$gp_id."E"])){ $edit_flag = 1; $edit_link = $gp_link[$gp_id."E"]; } else { $edit_link = ""; $edit_flag = 0; }
            if(!empty($del_acc[$gp_id."R"])){ $delete_flag = 1; $delete_link = $gp_link[$gp_id."R"]; } else { $delete_link = ""; $delete_flag = 0; }
            if(!empty($pnt_acc[$gp_id."P"])){ $print_flag = 1; $print_link = $gp_link[$gp_id."P"]; } else { $print_link = ""; $print_flag = 0; }
            if(!empty($upd_acc[$gp_id."U"])){ $update_flag = 1; $update_link = $gp_link[$gp_id."U"]; } else { $update_link = ""; $update_flag = 0; }
            
            $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Pause/Active','Authorize') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Pause/Active','Authorize') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Pause/Active"){ $paflag = $row['flag']; } else if($row['field_name'] == "Authorize"){ $autflag = $row['flag']; } else{ } }
            if($paflag == "" || $paflag == NULL || $paflag == 0){ $paflag = 0; }
            if($autflag == "" || $autflag == NULL || $autflag == 0){ $autflag = 0; }

            $fsdate = $cid."-fdate"; $tsdate = $cid."-tdate";
            if(isset($_POST['submit']) == true){
                $fdate = date("Y-m-d",strtotime($_POST['fdate']));
                $tdate = date("Y-m-d",strtotime($_POST['tdate']));
                $_SESSION[$fsdate] = $fdate;
                $_SESSION[$tsdate] = $tdate;
            }
            else {
                $fdate = $tdate = date("Y-m-d");
                if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }
            }

            //Fetch Print-View from Print Master
            $i = $pc = 0; $field_name = $print_path = $icon_type = $icon_path = $icon_color = $target = array();
            $psql = "SELECT * FROM `broiler_printview_master` WHERE `file_name` LIKE '$href' AND `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`id` ASC";
            $pquery = mysqli_query($conn,$psql);
            while($prow = mysqli_fetch_array($pquery)){
                $field_name[$i] = $prow['field_name'];
                $print_path[$i] = $prow['print_path'];
                $icon_type[$i] = $prow['icon_type'];
                $icon_path[$i] = $prow['icon_path'];
                $icon_color[$i] = $prow['icon_color'];
                $target[$i] = $prow['target'];
                $i++;
            }
            $pc = $i - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                        <form action="<?php echo $href; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group"><label for="fdate">From Date: </label><input type="text" class="form-control datepicker" name="fdate" id="fdate" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group"><label for="tdate">To Date: </label><input type="text" class="form-control datepicker" name="tdate" id="tdate" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>"></div>
                                    </div>
                                    <div class="col-md-2"><br/>
                                        <button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button>
                                    </div>
                                    <div class="col-md-6" align="right">
                                    <?php if($add_flag == 1){ ?>
                                        <button type="button" class="btn bg-purple" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Trnum</th>
                                        <th>Batch No.</th>
                                        <th>Customer</th>
										<th>category Code</th>
										<th>category Name</th>
										<th>Weight</th>
										<th>Yield%</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
                                        $query = mysqli_query($conn,$sql); $cus_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; }
                                 
                                        $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
                                        $query = mysqli_query($conn,$sql); $item_name = $item_cat = array();
                                        while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_cat[$row['code']] = $row['category']; }
                                 
                                        $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC";
                                        $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }
                                 
                                        $delete_url = $delete_link."?utype=delete&trnum=";
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trtype` = 'plant_portioning2' AND `trlink` = 'broiler_display_plant_portioning2.php' AND `dflag` = '0' ORDER BY `id` DESC";
                                        $query = mysqli_query($conn,$sql); $c = 0; $icat_pqty = $icat_pyld = array();
                                        while($row = mysqli_fetch_assoc($query)){
                                            $icats = $item_cat[$row['item_code']]; $trnum = $row['trnum']; $key = $trnum."@".$icats;
                                            $icat_pqty[$key] += (float)$row['weight'];
                                            $icat_pyld[$key] += (float)$row['yield_per'];
                                        }
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trtype` = 'plant_portioning2' AND `trlink` = 'broiler_display_plant_portioning2.php' AND `dflag` = '0' GROUP BY `trnum` ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            $print_url = $print_link."?utype=print&trnum=".$id;
                                            $authorize_url = $update_link."?utype=authorize&trnum=".$id;
                                            if($row['active'] == 1){ $update_url = $update_link."?utype=pause&trnum=".$id; } else{ $update_url = $update_link."?utype=activate&trnum=".$id; }
                                            $icats = $item_cat[$row['item_code']]; $trnum = $row['trnum']; $key = $trnum."@".$icats;
                                            if(empty($icat_pqty[$key]) || $icat_pqty[$key] == ""){ $icat_pqty[$key] = 0; }
                                            if(empty($icat_pyld[$key]) || $icat_pyld[$key] == ""){ $icat_pyld[$key] = 0; }
                                            $view_url = "broiler_view_plant_portioning2.php?utype=view&trnum=".$id;
                                    ?>
                                    <tr>

                                        <td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $row['batch_no']; ?></td>
										<td><?php echo $cus_name[$row['cus_code']]; ?></td>
										<td><?php echo $icats; ?></td>
										<td><?php echo $icat_name[$icats]; ?></td>
										<td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind(round($icat_pqty[$key],2))); ?></td>
										<td style="text-align:right;"><?php echo number_format_ind(round($icat_pyld[$key],2)); ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else if($row['gc_flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='GC processed'></i></a>";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'>
                                                    <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                                    </a>&ensp;&ensp;
                                                    <?php
                                                }
                                                if($paflag == 1){
                                                    if($row['active'] == 1){
                                                        echo "<a href='".$update_url."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;&ensp;";
                                                    }
                                                    else{
                                                        echo "<a href='".$update_url."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;&ensp;";
                                                    }
                                                }
                                                if($autflag == 1){
                                                    echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                            echo "<a href='".$view_url."' target='_BLANK'><i class='fa-solid fa-eye' style='color:blue;' title='View'></i></a>&ensp;&ensp;";
                                            if($print_flag == 1){
                                                $printv_list = "";
                                                for($p = 0;$p <= $pc;$p++){
                                                    $ppath = ""; $ppath = $print_path[$p]."".$print_dt;
                                                    if($icon_type[$p] == "image"){
                                                        $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><img src="'.$icon_path[$p].'" style="width:15px;height:15px;" title="'.$field_name[$p].'" /></a>&ensp;&ensp;';
                                                    }
                                                    else if($icon_type[$p] == "icon"){
                                                        $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><i class="'.$icon_path[$p].'" style="color:'.$icon_color[$p].';" title="'.$field_name[$p].'"></i></a>&ensp;&ensp;';
                                                    }
                                                }
                                                echo $printv_list;
                                            }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkdelete(a){
                if(a != ""){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_check_plant_portioning2.php?id="+a;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var count = this.responseText;
                            if(parseFloat(count) > 0){
                                alert("You can't delete the Transaction: "+a+", as Transaction is already in use!");
                            }
                            else{
                                var b = "<?php echo $delete_url; ?>"+a;
                                var c = confirm("are you sure you want to delete the transaction "+a+" ?");
                                if(c == true){
                                    window.location.href = b;
                                }
                                else{ }
                            }
                        }
                    }
                }
			}
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