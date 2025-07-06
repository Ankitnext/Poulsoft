<?php
//broiler_display_inventorytransfer.php
include "newConfig.php";
include "broiler_create_print_links.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['inventorytransfer'] = $cid; } else{ $cid = $_SESSION['inventorytransfer']; }
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
            //check and fetch date range
            global $drng_cday; $drng_cday = 1; global $drng_furl; $drng_furl = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            include "poulsoft_fetch_daterange_master.php";

            /*Check for Table Availability*/
            $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
            $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
            if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_broiler_broilermaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }
            /*Check for Column Availability*/
            $sql='SHOW COLUMNS FROM `broiler_printview_master`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("module", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_printview_master` ADD `module` varchar(300) NULL DEFAULT NULL AFTER `id`"; mysqli_query($conn,$sql); }
            if(in_array("field_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_printview_master` ADD `field_name` varchar(300) NULL DEFAULT NULL AFTER `module`"; mysqli_query($conn,$sql); }
            if(in_array("file_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_printview_master` ADD `file_name` varchar(300) NULL DEFAULT NULL AFTER `field_name`"; mysqli_query($conn,$sql); }
            
            /*Check for Column Availability*/
            $sql='SHOW COLUMNS FROM `item_stocktransfers`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("stk_itemid", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `stk_itemid` VARCHAR(60) NULL DEFAULT NULL COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
            if(in_array("driver_mobile", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `driver_mobile` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Driver Mobile No' AFTER `driver_code`"; mysqli_query($conn,$sql); }
            if(in_array("emp_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `emp_code` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Employee Code' AFTER `driver_mobile`"; mysqli_query($conn,$sql); }
            if(in_array("emp_ecoa", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `emp_ecoa` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Employee Expense CoA Code' AFTER `emp_code`"; mysqli_query($conn,$sql); }
            if(in_array("emp_bcoa", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `emp_bcoa` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Branch CoA Code' AFTER `emp_ecoa`"; mysqli_query($conn,$sql); }
            if(in_array("emp_eamt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `emp_eamt` DECIMAL(20,5) NOT NULL DEFAULT '0' COMMENT 'Branch CoA Code' AFTER `emp_bcoa`"; mysqli_query($conn,$sql); }
            if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `trtype` VARCHAR(200) NULL DEFAULT NULL COMMENT 'trtype' AFTER `stk_itemid`"; mysqli_query($conn,$sql); }
            if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_stocktransfers` ADD `trlink` VARCHAR(200) NULL DEFAULT NULL COMMENT 'trlink' AFTER `trtype`"; mysqli_query($conn,$sql); }

            $sql='SHOW COLUMNS FROM `broiler_medicine_record`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("stk_itemid", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_medicine_record` ADD `stk_itemid` VARCHAR(60) NULL DEFAULT NULL COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
            
            $sql='SHOW COLUMNS FROM `account_summary`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("stk_itemid", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `stk_itemid` VARCHAR(60) NULL DEFAULT NULL COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
            if(in_array("emp_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_summary` ADD `emp_code` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Employee Code' AFTER `crdr`"; mysqli_query($conn,$sql); }

            //Fetch Column From CoA Table
            $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array('he_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `he_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Hatchery Expense Flag'  AFTER `dflag`;"; mysqli_query($conn,$sql); }
            if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `he_flag`"; mysqli_query($conn,$sql); }
            if(in_array('fe_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `fe_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Feed Expense Flag'  AFTER `he_flag`;"; mysqli_query($conn,$sql); }
            if(in_array('noninv_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `noninv_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Non-Inventory Coas Flag'  AFTER `fe_flag`;"; mysqli_query($conn,$sql); }
            if(in_array('freight_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `freight_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Retail Flag'  AFTER `active`;"; mysqli_query($conn,$sql); }
            if(in_array('transporter_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `transporter_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Transporter Flag'  AFTER `freight_flag`;"; mysqli_query($conn,$sql); }
            if(in_array('expledger_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `expledger_flag` INT(100) NOT NULL DEFAULT '0' COMMENT ''  AFTER `transporter_flag`;"; mysqli_query($conn,$sql); }
            if(in_array('vouexp_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `vouexp_flag` INT(100) NOT NULL DEFAULT '0' COMMENT ''  AFTER `expledger_flag`;"; mysqli_query($conn,$sql); }
            if(in_array('driver_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `driver_flag` INT(100) NOT NULL DEFAULT '0' COMMENT ''  AFTER `vouexp_flag`;"; mysqli_query($conn,$sql); }
            if(in_array('este_flag', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa`  ADD `este_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Employee-Stock Transfer Expense Flag'  AFTER `driver_flag`;"; mysqli_query($conn,$sql); }

            $gp_id = $gc_id = $gp_name = $gp_link = $gp_link = $p_id = $c_id = $p_name = $p_link = array();
            $sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $gp_id = $row['parentid'];
                $gc_id[$row['childid']] = $row['childid'];
                $gp_name[$row['childid']] = $row['name'];
                $gp_link[$row['childid']] = $row['href'];
            }
            $alink = explode("','",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; }
            $elink = explode("','",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; }
            $rlink = explode("','",$rlink); foreach($rlink as $rlink1){ $del_acc[$rlink1] = $rlink1; }
            $plink = explode("','",$plink); foreach($plink as $plink1){ $pnt_acc[$plink1] = $plink1; $pnt_link_acc = $pnt_link_acc.",".$plink1; }
            $ulink = explode("','",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; }
            if(!empty($add_acc[$gp_id."A"]) && $add_acc[$gp_id."A"] != ""){ $add_flag = 1; if(!empty($gp_link[$gp_id."A"])){ $add_link = $gp_link[$gp_id."A"]; } else{ $add_link = ""; } } else { $add_flag = 0; }
            if(!empty($edt_acc[$gp_id."E"]) && $edt_acc[$gp_id."E"] != ""){ $edit_flag = 1; if(!empty($gp_link[$gp_id."E"])){ $edit_link = $gp_link[$gp_id."E"]; } else{ $edit_link = ""; } } else { $edit_flag = 0; }
            if(!empty($del_acc[$gp_id."R"]) && $del_acc[$gp_id."R"] != ""){ $delete_flag = 1; if(!empty($gp_link[$gp_id."R"])){ $delete_link = $gp_link[$gp_id."R"]; } else{ $delete_link = ""; } } else { $delete_flag = 0; }
            if($pnt_acc[$gp_id."P"] != ""){ $print_flag = 1; $print_link = $gp_link[$gp_id."P"]; } else { $print_flag = 0; }
            if(!empty($upd_acc[$gp_id."U"]) && $upd_acc[$gp_id."U"] != ""){ $update_flag = 1; if(!empty($gp_link[$gp_id."U"])){ $update_link = $gp_link[$gp_id."U"]; } else{ $update_link = ""; } } else { $update_flag = 0; }
            //echo $print_flag;
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

                $from_warehouse = $_POST['from_warehouse'];
                $to_warehouse = $_POST['to_warehouse'];
                if($from_warehouse != 'all'){
                    $from_warehouse_condition = " AND fromwarehouse = '$from_warehouse' ";
                }
                if($to_warehouse != 'all'){
                    $to_warehouse_condition = " AND towarehouse = '$to_warehouse' ";
                }
            }
            else {
                $fdate = $tdate = date("Y-m-d");
                if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }

                $from_warehouse_condition = "";
                $to_warehouse_condition = "";
            }

            //Check Print-View Linksmodule
            $module = "Stock Transfer"; $field_name = "Purchase Order-1"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/broiler_stock_transfer_invoice.php";
            $sort_order = "1"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "black"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);
            $module = "Stock Transfer"; $field_name = "Purchase Order-2"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/broiler_stock_transfer_datewise.php";
            $sort_order = "2"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "green"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);
            $module = "Stock Transfer"; $field_name = "Purchase Order-3"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/broiler_stock_transfer_datewise1.php";
            $sort_order = "3"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "orange"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);
            $module = "Stock Transfer"; $field_name = "Purchase Order-4"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/broiler_stock_transfer_invoice_single.php";
            $sort_order = "4"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "brown"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);
            $module = "Stock Transfer"; $field_name = "Purchase Order-5"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/broiler_stock_transfer_datewise_single.php";
            $sort_order = "5"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "gray"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);
            $module = "Stock Transfer"; $field_name = "Purchase Order-6"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/broiler_stock_transfer_datewise2.php";
            $sort_order = "6"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "gray"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);
            $module = "Stock Transfer"; $field_name = "Purchase Order-7"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/broiler_stock_transfer_invoice_p.php";
            $sort_order = "7"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "blue"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);

            $module = "Stock Transfer"; $field_name = "Purchase Order-8"; $file_name = "broiler_display_inventorytransfer.php"; $print_path = "/print/Examples/delivery_challen.php";
            $sort_order = "7"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "pink"; $target = "_BLANK";
            $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
            $print_dt = ""; $print_dt = json_encode($msg1);
            broiler_create_print_links($print_dt);

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

             $sector_code = $sector_name = array();
             $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); 
             while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; }
             $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
             while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; }
             if(sizeof($sector_code) > 0){ $sector_list = implode("','",$sector_code); $cond_assigned = " AND (`fromwarehouse` IN ('$sector_list') OR `towarehouse` IN ('$sector_list'))"; } else{ $cond_assigned = ""; }

             $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
             while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }
                                        
             $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
             while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
             // Driver  Category 
             $sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
             while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
             // Driver Name            
             $sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
             while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
          

             $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
             while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                        <form action="<?php echo $href; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row" align="left">
                                            <div class="form-group" style="width:100px;">
                                                <label for="fdate">From Date: </label>
                                                <input type="text" name="fdate" id="fdate" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <label for="tdate">To Date: </label>
                                                <input type="text" name="tdate" id="tdate" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:250px;">
                                                <label for="tdate">From Warehouse: </label>
                                                <select name="from_warehouse" id="from_warehouse" class="form-control select2" style="width:240px;">
                                                    <option value="all" <?php if($from_warehouse == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($sector_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($from_warehouse == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group" style="width:250px;">
                                                <label for="tdate">To Warehouse: </label>
                                                <select name="to_warehouse" id="to_warehouse" class="form-control select2" style="width:240px;">
                                                    <option value="all" <?php if($to_warehouse == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($sector_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($to_warehouse == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option><?php } ?>
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
                                        <th>Dc No.</th>
                                        <th>From Location</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Driver</th>
										<th>Quantity</th>
										<th>To Location</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                       
                                        
                                        $delete_url = $delete_link."?utype=delete&id=";
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND (`trtype` NOT LIKE '%ChickTransfer%' OR `trtype` != '' OR `trtype` IS NULL) AND `dflag` = '0' $from_warehouse_condition $to_warehouse_condition $cond_assigned ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            if($row['link_trnum'] == ""){
                                            $id = $row['id'];
                                            $stk_itemid = $row['stk_itemid'];
                                            $edit_url = $edit_link."?utype=edit&id=".$id."&stk_itemid=".$stk_itemid;
                                            $delete_key = $row['trnum']."@".$item_name[$row['code']]."@".$id."&stk_itemid=".$stk_itemid;
                                            /*$print_url = "/print/Examples/".$print_link."?utype=print&id=".$id;
                                            $print_url1 = "/print/Examples/broiler_stock_transfer_invoice.php?id=".$row['trnum'];
                                            $print_url2 = "/print/Examples/broiler_stock_transfer_datewise.php?id=".$row['date']."@".$item_category[$row['code']]."@".$row['towarehouse'];
                                            $print_url7 = "/print/Examples/broiler_stock_transfer_datewise1.php?id=".$row['date']."@".$item_category[$row['code']]."@".$row['towarehouse'];
                                            $print_url3 = "/print/Examples/broiler_stock_transfer_invoice_single.php?id=".$row['trnum'];
                                            $print_url4 = "/print/Examples/broiler_stock_transfer_datewise_single.php?id=".$row['date']."@".$item_category[$row['code']]."@".$row['towarehouse'];
                                            $print_url5 = "/print/Examples/broiler_stock_transfer_datewise2.php?id=".$row['date']."@".$item_category[$row['code']]."@".$row['towarehouse'];
                                            $print_url6 = "/print/Examples/broiler_stock_transfer_invoice_p.php?id=".$row['trnum'];*/
                                            $authorize_url = $update_link."?utype=authorize&id=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&id=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&id=".$id;
                                            }
                                            $print_dt = ""; $print_dt = "?id=".$row['id']."&stk_itemid=".$row['stk_itemid']."&trnum=".$row['trnum']."&date=".$row['date']."&icat=".$item_category[$row['code']]."&to_sector=".$row['towarehouse']."&vehicle=".$row['vehicle_code'];
                                    ?>
                                    <tr>
										<td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $row['dcno']; ?></td>
										<td><?php echo $sector_name[$row['fromwarehouse']]; ?></td>
										<td><?php echo $row['code']; ?></td>
										<td><?php echo $item_name[$row['code']]; ?></td>
										<td><?php echo $emp_name[$row['driver_code']]; ?></td>
										<td><?php echo $row['quantity']; ?></td>
										<td><?php echo $sector_name[$row['towarehouse']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>&ensp;&ensp;";
                                            }
                                            else if($row['gc_flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='GC processed'></i></a>&ensp;&ensp;";
                                            }
                                            else if(strtotime($row['date']) < strtotime($rng_sdate) || strtotime($row['date']) > strtotime($rng_edate)){
                                                echo "<i class='fa fa-check' style='color:green;' title='Date Entry Range Closed'></i></a>&ensp;&ensp;";
                                            }
                                            else {
                                                if($edit_flag == 1 && $row['link_trnum'] == ""){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1 && $row['link_trnum'] == ""){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $delete_key; ?>' value='<?php echo $delete_key; ?>' onclick='checkdelete(this.id)'>
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
                                                /*//echo "<a href='".$print_url."' target='_BLANK'><i class='fa fa-print' style='color:yellow;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url1."' target='_BLANK'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url2."' target='_BLANK'><i class='fa fa-print' style='color:green;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url7."' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url3."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
                                                //echo "<a href='".$print_url4."' target='_BLANK'><i class='fa fa-print' style='color:gray;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url5."' target='_BLANK'><i class='fa fa-print' style='color:gray;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url6."' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='Print'></i></a>&ensp;&ensp;";
                                                */
                                                $printv_list = "";
                                                for($p = 0;$p <= $pc;$p++){
                                                    if($icon_path[$p] == "fa-brands fa-whatsapp"){ $ppath = ""; $ppath = $print_path[$p]."".$print_dt."&view_type=send_pdf"; }
                                                    else{ $ppath = ""; $ppath = $print_path[$p]."".$print_dt; }
                                                    
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
                var trdetails = a.split("@");
                var trnum = trdetails[0];
                var item = trdetails[1];
                var link_key = trdetails[2];
				var main_link = "<?php echo $delete_url; ?>"+link_key;
				var c = confirm("are you sure you want to delete the transaction with transaction No: "+trnum+" and item: "+item+" ?");
				if(c == true){
					window.location.href = main_link;
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