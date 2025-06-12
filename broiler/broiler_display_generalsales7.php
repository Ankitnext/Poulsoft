<?php
//broiler_display_generalsales7.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['generalsales7'] = $cid; } else{ $cid = $_SESSION['generalsales7']; }
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
            if(in_array("broiler_tcds_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_tcds_master LIKE poulso6_admin_broiler_broilermaster.broiler_tcds_master;"; mysqli_query($conn,$sql1); }
            if(in_array("prefix_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.prefix_master LIKE poulso6_admin_broiler_broilermaster.prefix_master;"; mysqli_query($conn,$sql1); }
            if(in_array("master_generator", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_generator LIKE poulso6_admin_broiler_broilermaster.master_generator;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_broiler_broilermaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }
            
            $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'E-Invoices' AND `field_function` = 'Generate Auto E-Invoices' AND `user_access` = 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $einv_gflag = mysqli_num_rows($query);

            /*Check for Column Availability*/
            $sql='SHOW COLUMNS FROM `broiler_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("mnu_tcs_edit", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `mnu_tcs_edit` INT(100) NOT NULL DEFAULT '0' COMMENT 'Manual TCS Edit Flag' AFTER `tcds_amt`"; mysqli_query($conn,$sql); }
            if(in_array("tloss_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `tloss_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Transit Loss' AFTER `trnum`"; mysqli_query($conn,$sql); }
            if(in_array("srtn_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `srtn_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Sales Return' AFTER `tloss_trnum`"; mysqli_query($conn,$sql); }
            if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
            if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `trtype`"; mysqli_query($conn,$sql); }
            if(in_array("dmobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `dmobile_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `driver_code`"; mysqli_query($conn,$sql); }
            if(in_array("fbatch_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `fbatch_no` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `batch_no`"; mysqli_query($conn,$sql); }
            if(in_array("fmake_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `fmake_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `fbatch_no`"; mysqli_query($conn,$sql); }
            if(in_array("fexp_date", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `fexp_date` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `fmake_date`"; mysqli_query($conn,$sql); }
            if(in_array("sale_pono", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `sale_pono` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `fexp_date`"; mysqli_query($conn,$sql); }
            if(in_array("sale_podate", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `sale_podate` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `sale_pono`"; mysqli_query($conn,$sql); }
            if(in_array("file_path1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `file_path1` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-1' AFTER `remarks`"; mysqli_query($conn,$sql); }
            if(in_array("file_path2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `file_path2` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-2' AFTER `file_path1`"; mysqli_query($conn,$sql); }
            if(in_array("file_path3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `file_path3` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'Production Document-3' AFTER `file_path2`"; mysqli_query($conn,$sql); }
            
            if((int)$einv_gflag > 0){
                if(in_array("einv_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `einv_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'E-Invoice Status' AFTER `flag`"; mysqli_query($conn,$sql); }
                if(in_array("ewb_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `ewb_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'E-way Bill Status' AFTER `einv_flag`"; mysqli_query($conn,$sql); }
                if(in_array("eprint_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_sales` ADD `eprint_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'E-way Bill Print Status' AFTER `ewb_flag`"; mysqli_query($conn,$sql); }
            }

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
            $plink = explode("','",$plink); foreach($plink as $plink1){ $pnt_acc[$plink1] = $plink1; $pnt_link_acc = $pnt_link_acc.",".$plink1; }
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

            $sale_print1 = 0;
            $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'broiler_display_generalsales7.php' AND `field_function` = 'Sale Invoice Format-1' AND `user_access` LIKE 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $sale_print1 = mysqli_num_rows($query);
            
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
                                        <th>Invoice</th>
                                        <th>DC No</th>
                                        <th>Customer</th>
										<th>Item</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
										<th>Farm/Warehouse</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $item_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
                                        $sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql); $vendor_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $vendor_name[$row['code']] = $row['name']; }
                                        $sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql); $sector_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
                                        $sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
                                        
                                        $delete_url = $delete_link."?utype=delete&trnum=";
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND (`trlink` = 'broiler_display_generalsales5.php' OR `trlink` = 'broiler_display_generalsales7.php') AND `dflag` = '0' ORDER BY `id` DESC";
                                        $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum']; $c++;
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            $print_url = "print/Examples/".$print_link."?utype=print&trnum=".$id;
                                            $print_url2 = "print/Examples/broiler_saleinvoice.php?id=".$row['trnum']."@inv";
                                            $print_url3 = "print/Examples/broiler_saleinvoice1.php?id=".$row['trnum']."@inv";
                                            $print_url4 = "print/Examples/broiler_saleinvoice2.php?id=".$row['trnum']."@inv";
                                            $print_url5 = "print/Examples/broiler_saleinvoice5.php?id=".$row['trnum']."@inv";

                                            $edit_url2 = "broiler_return_generalsales7.php?utype=return&trnum=".$id;
                                            $print_dt = "?vcode=".$row['vcode']."&trnum=".$id."&warehouse=".$row['warehouse']."&date=".$row['date']."&id=".$row['trnum']."@inv";

                                            $ref_docs1 = "";
                                            if($row['file_path1'] != ""){
                                                $ref_docs1 = '<a href="../'.$row["file_path1"].'" download title="download"><i class="fa-solid fa-angles-down" style="font-size:15px;"></i></a>&ensp;';
                                            }
                                            $ref_docs2 = "";
                                            if($row['file_path2'] != ""){
                                                $ref_docs2 = '<a href="../'.$row["file_path2"].'" download title="download"><i class="fa-solid fa-angles-down" style="font-size:15px;"></i></a>&ensp;';
                                            }
                                            $ref_docs3 = "";
                                            if($row['file_path3'] != ""){
                                                $ref_docs3 = '<a href="../'.$row["file_path3"].'" download title="download"><i class="fa-solid fa-angles-down" style="font-size:15px;"></i></a>&ensp;';
                                            }
                                    ?>
                                    <tr>
										<td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $row['billno']; ?></td>
										<td><?php if(!empty($vendor_name[$row['vcode']])){ echo $vendor_name[$row['vcode']]; } else{ echo $sector_name[$row['warehouse']]; } ?></td>
										<td><?php echo $item_name[$row['icode']]; ?></td>
										<td><?php echo $row['rcd_qty']; ?></td>
										<td><?php echo $row['rate']; ?></td>
										<td><?php echo $row['item_tamt']; ?></td>
										<td><?php echo $sector_name[$row['warehouse']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>&ensp;&ensp;";
                                            }
                                            else if($row['gc_flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='GC processed'></i></a>&ensp;&ensp;";
                                            }
                                            else if($row['einv_flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='E-Invoice Generated'></i></a>&ensp;&ensp;";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";

                                                    if($_SERVER['REMOTE_ADDR'] == "49.207.226.103"){
                                                        if($row['tloss_trnum'] != "" || $row['srtn_trnum'] != ""){
                                                            echo "<a href='".$edit_url2."'><i class='fa-solid fa-pen-to-square' style='color:green;' title='Edit'></i></a>&ensp;&ensp;";
                                                        }
                                                        else{
                                                            echo "<a href='".$edit_url2."'><i class='fa-solid fa-pen-to-square' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                        }
                                                    }
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
                                                //echo $ref_docs;
                                            }
                                            if($ref_docs1 != ""){
                                                echo $ref_docs1;
                                            }
                                            if($ref_docs2 != ""){
                                                echo $ref_docs2;
                                            }
                                            if($ref_docs3 != ""){
                                                echo $ref_docs3;
                                            }
                                            
                                            if((int)$einv_gflag > 0){
                                                $id_list = ""; //else if((int)$row['einv_flag'] == 1 && (int)$row['ewb_flag'] == 1 && (int)$row['eprint_flag'] == 1){ }
                                                $id_list = $row['trnum']."[@$&]sales[@$&]".$row['einv_flag']."[@$&]".$row['ewb_flag']."[@$&]".$row['eprint_flag']."[@$&]".$href."[@$&]".$c;
                                                if((int)$row['eprint_flag'] == 1){
                                                    echo '<a href="javascript:void(0);" id="'.$id_list.'" onclick="ebill_system(this.id)"><i class="fa-solid fa-truck-fast" style="color:green;" title="E-Bill Print"></i></a>&ensp;&ensp;';
                                                }
                                                else{
                                                    echo '<a href="javascript:void(0);" id="'.$id_list.'" onclick="ebill_system(this.id)"><i class="fa-solid fa-truck-medical" style="color:green;" title="E-Bill Generate"></i></a>&ensp;&ensp;';
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
        <script src="poulsoft_loading_screen.js"></script>
        <script src="poulsoft_ebill_generators.js"></script>
    </body>
</html>
<?php
}
else{
     header('location:index.php');
}
?>