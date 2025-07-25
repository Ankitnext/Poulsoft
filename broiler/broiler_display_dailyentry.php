<?php
//broiler_display_dailyentry.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['dailyentry'] = $cid; } else{ $cid = $_SESSION['dailyentry']; }
$href = explode("/", $_SERVER['REQUEST_URI']); $url = $href[1]; $file_name = explode("?", $href[1]);
$sql = "SELECT * FROM `master_form_tableaccess` WHERE `href` = '$file_name[0]' AND `active` = '1'"; $query = mysqli_query($sconn,$sql);
while($row = mysqli_fetch_assoc($query)){ $table_name = $row['table_name']; } $table_session = $cid."tbl_access"; $_SESSION[$table_session] = $table_name;
$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC";
$query = mysqli_query($conn,$sql); $link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; }

//     /*Check for Column Availability*/
// $sql='SHOW COLUMNS FROM `'.$table_name.'`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
// while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
// if(in_array("egg_weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `'..'` ADD `egg_weight` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `remarks`"; mysqli_query($conn,$sql); }


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
    
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    
    }

    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

    $ab = 0;

    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ 

        if($ab == 0) {
            $assigned_farms = "'".$row['code']."'";
        }else{
            $assigned_farms .= ",'".$row['code']."'";
        }

        $ab++;
       
    }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ 

        if($ab == 0) {
            $assigned_farms = "'".$row['code']."'";
        }else{
            $assigned_farms .= ",'".$row['code']."'";
        }
        
        $ab++;
    }

    if($assigned_farms != '' ){
        $cond_assigned = " AND  farm_code IN ( $assigned_farms ) ";
    }else{
        $cond_assigned = "";
    }
    $aid = 0;
    $flink = explode("','",$dlink); $acount = 0; foreach($flink as $flinks){ if($flinks == $cid){ $aid = 1; } }
    if($user_type == "S"){ $acount = 1; }
    else if($aid == 1){ $acount = 1; }
    else{ $acount = 0; }

    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'broiler_display_dailyentry.php' AND `field_function` LIKE 'Hide Columns' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $fieldvalue = $row["field_value"];
    }
    if (!empty($fieldvalue)) {
        $hidecolumns = explode(",", $fieldvalue);
    } else {
        $hidecolumns = [];
    }
   // echo $fieldvalue; 
   // print_r($hidecolumns);
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
            $gp_id = $gc_id = $gp_name = $gp_link = $gp_link = $p_id = $c_id = $p_name = $p_link = array();
            $sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $gp_id = $row['parentid'];
                $gc_id[$row['childid']] = $row['childid'];
                $gp_name[$row['childid']] = $row['name'];
                $gp_link[$row['childid']] = $row['href'];
            }
            $add_link_acc = $edt_link_acc = $del_link_acc = $pnt_link_acc = $upd_link_acc = "";
            $alink = explode("','",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; }
            $elink = explode("','",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; }
            $rlink = explode("','",$rlink); foreach($rlink as $rlink1){ $del_acc[$rlink1] = $rlink1; }
            $plink = explode("','",$plink); foreach($plink as $plink1){ $pnt_acc[$rlink1] = $plink1; }
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

            $fsdate = $cid."-fdate"; $tsdate = $cid."-tdate"; $tsfarms = $cid."-farms";
            if(isset($_POST['submit']) == true){
                $fdate = date("Y-m-d",strtotime($_POST['fdate']));
                $tdate = date("Y-m-d",strtotime($_POST['tdate']));
                $farms = $_POST['farms'];
                $_SESSION[$fsdate] = $fdate;
                $_SESSION[$tsdate] = $tdate;
                $_SESSION[$tsfarms] = $farms;
            }
            else {
                $fdate = $tdate = date("Y-m-d"); $farms = "all";
                if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }
                if(!empty($_SESSION[$tsfarms])){ $farms = $_SESSION[$tsfarms]; }
            }
            
            $sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
            while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
                    
            $sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
            while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

            $sql = "SELECT * FROM `item_details` WHERE  `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $kcount = mysqli_num_rows($query);
            while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['name']; }

            $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $farmbranch[$row['code']] = $row['branch_code']; }

            $sql = "SELECT * FROM `location_branch` WHERE `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $branchn[$row['code']] = $row['description'];
            }

            $sql = "SELECT * FROM `main_access` WHERE `active` = '1'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $employ[$row['empcode']] = $row['db_emp_code'];
            }
           // print_r($employ);

            $sql = "SELECT * FROM `broiler_employee` WHERE `active` = '1' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql); $empname = array();
            while($row = mysqli_fetch_assoc($query)){
                $empname[$row['code']] = $row['name'];
            }
          //  print_r($empname);
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                        <form action="<?php echo $url; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row" align="left">
                                            <div class="form-group" style="width:100px;">
                                                <label for="fdate">From Date: </label>
                                                <input type="text" name="fdate" id="fdate" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <label for="tdate">To Date: </label>
                                                <input type="text" name="tdate" id="tdate" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:250px;">
                                                <label for="tdate">Farm: </label>
                                                <select name="farms" id="farms" class="form-control select2" style="width:240px;">
                                                    <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($farm_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <br/>
                                                <button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right">
                                    <?php if($add_flag == 1){ ?>
                                        <button type="button" class="btn bg-purple" id="addpage" value="<?php echo $add_link; ?>" onClick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
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
                                        <th>Branch</th>
                                        <th>Farm</th>
										<th>Batch</th>
										<th>Age</th>
										<th>Mortality</th>

                                        <?php echo in_array("feed1", $hidecolumns) ? "" : "<th>Feed 1</th>"; ?>
										<?php echo in_array("qty1", $hidecolumns) ? "" : "<th>Qty 1</th>"; ?>
										<?php echo in_array("feed2", $hidecolumns) ? "" : "<th>Feed 2</th>"; ?>
										<?php echo in_array("qty2", $hidecolumns) ? "" : "<th>Qty 2</th>"; ?>
										

										<th>Avg Wt.(Gms)</th>

                                        <?php echo in_array("entryby", $hidecolumns) ? "" : "<th>Entry By</th>"; ?>
                                        <?php echo in_array("entrytime", $hidecolumns) ? ""  : "<th>Entry Time</th>"; ?>
                                        
                                        
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; }
                                        $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $item_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
                                        $sql = "SELECT MAX(brood_age) as brood_age,batch_code FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `gc_flag` = '0' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $brood_ages[$row['batch_code']] = $row['brood_age']; }
                                        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Daily Entry' AND `field_function` LIKE 'Bags' AND `flag` = '1'";
                                        $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

                                        $delete_url = $delete_link."?utype=delete&trnum=";
                                        if($farms == "all"){ $farm_filter = ""; } else{ $farm_filter = " AND `farm_code` = '$farms'"; }
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$farm_filter." AND `gc_flag` = '0' AND `dflag` = '0' $cond_assigned ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            $print_url = $print_link."?utype=print&trnum=".$id;
                                            $authorize_url = $update_link."?utype=authorize&trnum=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&trnum=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&trnum=".$id;
                                            }
                                    ?>
                                    <tr>
										<td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
                                        <td><?php echo $branchn[$farmbranch[$row['farm_code']]]; ?></td>
										<td><?php echo $farm_name[$row['farm_code']]; ?></td>
										<td><?php echo $batch_name[$row['batch_code']]; ?></td>
										<td><?php echo round($row['brood_age']); ?></td>
										<td><?php echo round($row['mortality']); ?></td>
                                        <?php echo in_array("feed1", $hidecolumns) ? "" : "<td>".$item_name[$row['item_code1']]."</td>"; ?>
										
                                        <?php  if(!in_array("qty1", $hidecolumns)){  ?>
										<td>
                                            <?php
                                            if($bag_access_flag > 0){
                                                $items = ""; $available_stock_qty = 0;
                                                $items = $row['item_code1'];
                                                $available_stock_qty = round($row['kgs1'],2);
                                                $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$items' AND `active` = '1' AND `dflag` = '0'";
                                                $bquery = mysqli_query($conn,$bsql); $bcount = mysqli_num_rows($bquery);
                                                if($bcount > 0){
                                                    while($brow = mysqli_fetch_assoc($bquery)){
                                                        $available_stock_qty = $available_stock_qty / $brow['bag_size'];
                                                    }
                                                }
                                                else{
                                                    $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'";
                                                    $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
                                                    if($ibag_flag1 > 0){
                                                        while($brow = mysqli_fetch_assoc($bquery)){
                                                            $available_stock_qty = $available_stock_qty / $brow['bag_size'];
                                                        }
                                                    }
                                                    else{ }
                                                }
                                            }
                                            else{
                                                $available_stock_qty = 0;
                                                $available_stock_qty = round($row['kgs1'],2);
                                            }
                                                echo round($available_stock_qty,2);
                                            ?>
                                        </td>
                                        <?php  }  ?>

                                        <?php echo in_array("feed2", $hidecolumns) ? "" : "<td>".$item_name[$row['item_code2']]."</td>"; ?>
                                       

                                        <?php  if(!in_array("qty2", $hidecolumns)){  ?>
										<td>
                                            <?php
                                            if($bag_access_flag > 0){
                                                $items = ""; $available_stock_qty = 0;
                                                $items = $row['item_code2'];
                                                $available_stock_qty = round($row['kgs2'],2);
                                                $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$items' AND `active` = '1' AND `dflag` = '0'";
                                                $bquery = mysqli_query($conn,$bsql); $bcount = mysqli_num_rows($bquery);
                                                if($bcount > 0){
                                                    while($brow = mysqli_fetch_assoc($bquery)){
                                                        $available_stock_qty = $available_stock_qty / $brow['bag_size'];
                                                    }
                                                }
                                                else{
                                                    $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'";
                                                    $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
                                                    if($ibag_flag1 > 0){
                                                        while($brow = mysqli_fetch_assoc($bquery)){
                                                            $available_stock_qty = $available_stock_qty / $brow['bag_size'];
                                                        }
                                                    }
                                                    else{ }
                                                }
                                            }
                                            else{
                                                $available_stock_qty = 0;
                                                $available_stock_qty = round($row['kgs2'],2);
                                            }
                                                echo round($available_stock_qty,2);
                                            ?>
                                        </td>
                                        <?php  }  ?>


										<td><?php echo round($row['avg_wt']); ?></td>
                                        <?php echo in_array("entryby", $hidecolumns) ? "" : "<td>".$empname[$employ[$row['addedemp']]]."</td>"; ?>
                                        <?php echo in_array("entrytime", $hidecolumns) ? ""  : "<td>".date('d.m.Y H:m:s a',strtotime($row['addedtime']))."</td>"; ?>
                                      
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
                                                if($delete_flag == 1 && $brood_ages[$row['batch_code']] == $row['brood_age']){
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
                                                if($print_flag == 1){
                                                    echo "<a href='".$print_url."'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                }
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
				var b = "<?php echo $delete_url; ?>"+a;
				var c = confirm("are you sure you want to delete the transaction "+a+" ?");
				if(c == true){
					window.location.href = b;
				}
				else{ }
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