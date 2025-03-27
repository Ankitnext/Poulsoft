<?php
    //SalesReportMaster_ta.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "number_format_ind.php";
		$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid'];

        $form_reload_page = "SalesReportMaster_ta.php";
	}
	else{
		include "APIconfig.php";
		include "number_format_ind.php";
		$dbname = $db;
		$users_code = $_GET['emp_code'];
        $form_reload_page = "SalesReportMaster_ta.php?db=".$db;
	}

	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$loc_access = $row['loc_access'];
		$cgroup_access = $row['cgroup_access'];
		if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $utype = "S"; }
		else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $utype = "A"; }
		else if($row['normal_access'] == 1 || $row['normal_access'] == "1"){ $utype = "N"; }
		else{ $utype = "N"; }
	}
	if($utype == "S" || $utype == "A"){
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
	else{
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
    //Usr access Based Sector Filter
	if($loc_access == "all" || $loc_access == "All" || $loc_access == "" || $loc_access == NULL){ $user_sector_filter = ""; }
	else{ $wcode = str_replace(",","','",$loc_access); $user_sector_filter = " AND code IN ('$wcode')"; }
	
    //Usr access Based Customer Group Filter
	if($cgroup_access == "all" || $cgroup_access == "All" || $cgroup_access == "" || $cgroup_access == NULL){ $user_cusgrp_filter = ""; }
	else{ $gcode = str_replace(",","','",$cgroup_access); $user_cusgrp_filter = " AND code IN ('$gcode')"; }
	
    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
    while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'".$user_cusgrp_filter." ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
	while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

    $grp_list = implode("','",$grp_code);
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `groupcode` IN ('$grp_list') ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_group = array();
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }

	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$user_sector_filter." ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }
    
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

    //Fetch Master Details
	$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_jalsfreight_flag = $row['cus_jalsfreight_flag']; }
	if($cus_jalsfreight_flag == ""){ $cus_jalsfreight_flag = 0; }

	$sql = "SELECT *  FROM `main_linkdetails` WHERE `href` LIKE '%SalesReportMaster_ta.php%'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cid = $row['childid']; }

	$sql='SHOW COLUMNS FROM `master_reportfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("purcus_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `purcus_flag` varchar(300) NULL DEFAULT NULL COMMENT 'Pur-Sale Customer Name' AFTER `vendor_flag`"; mysqli_query($conn,$sql); }
	if(in_array("salesup_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `salesup_flag` varchar(300) NULL DEFAULT NULL COMMENT 'Pur-Sale Supplier Name' AFTER `purcus_flag`"; mysqli_query($conn,$sql); }
	if(in_array("atime_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `atime_flag` varchar(300) NULL DEFAULT NULL COMMENT 'addedemp time' AFTER `salesup_flag`"; mysqli_query($conn,$sql); }
	
    // Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

    //Report Master Access Filter
    $sql = "SELECT * FROM `master_reportfields` WHERE `code` = '$cid' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $frt_amt_flag = 0;
    while($row = mysqli_fetch_assoc($query)){
        $type = "type";
        $code = "code";
        $pattern = "pattern";
        $field_details[$row['date_flag']] = "date_flag";
        $field_details[$row['inv_flag']] = "inv_flag";
        $field_details[$row['binv_flag']] = "binv_flag";
        $field_details[$row['vendor_flag']] = "vendor_flag";
		$field_details[$row['salesup_flag']] = "salesup_flag";
        $field_details[$row['item_flag']] = "item_flag";
        $field_details[$row['jals_flag']] = "jals_flag";
        $field_details[$row['birds_flag']] = "birds_flag";
        $field_details[$row['tweight_flag']] = "tweight_flag";
        $field_details[$row['eweight_flag']] = "eweight_flag";
        $field_details[$row['nweight_flag']] = "nweight_flag";
        $field_details[$row['farm_weight']] = "farm_weight"; $t1 = explode(":",$row['farm_weight']); if($t1[1] == 1 || $t1[1] == "1"){ $farm_wt_flag = 1; }
        $field_details[$row['aweight_flag']] = "aweight_flag";
        $field_details[$row['prate_flag']] = "prate_flag"; $pdet = explode(":",$row['prate_flag']); if($pdet[1] == 1 || $pdet[1] == "1"){ $prate_flag = 1; }
        $field_details[$row['price_flag']] = "price_flag";
        $field_details[$row['freightamt_flag']] = "freightamt_flag"; $t1 = explode(":",$row['freightamt_flag']); if($t1[1] == 1 || $t1[1] == "1"){ $frt_amt_flag = 1; }
        $field_details[$row['jfreight_flag']] = "jfreight_flag";
        $field_details[$row['tcds_flag']] = "tcds_flag";
        $field_details[$row['discount_flag']] = "discount_flag";
        $field_details[$row['tamt_flag']] = "tamt_flag";
        $field_details[$row['sector_flag']] = "sector_flag";
        $field_details[$row['remarks_flag']] = "remarks_flag";
        $field_details[$row['vehicle_flag']] = "vehicle_flag";
        $field_details[$row['driver_flag']] = "driver_flag";
        $field_details[$row['weighton_flag']] = "weighton_flag";
        $field_details[$row['cr_flag']] = "cr_flag";
        $field_details[$row['dr_flag']] = "dr_flag";
        $field_details[$row['rb_flag']] = "rb_flag";
        $field_details[$row['user_flag']] = "user_flag";
        $field_details[$row['atime_flag']] = "atime_flag";
        $note_flag = $row['note_flag'];
        $note_code = $row['note_code'];
        $vsign_flag = $row['vsign_flag'];
        $csign_flag = $row['csign_flag'];
        $qr_img_flag = $row['qr_img_flag'];
        $col_count = $row['count'];
    }
	$fdate = $tdate = date("Y-m-d"); $suppliers = $customers = $sectors = $item_cat = $items = $users = "all"; $billnos = $prices = "";
    $groups = array(); $groups['all'] = "all"; $grp_all_flag = 1;
    $exports = "displaypage";
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fdate']));
		$tdate = date("Y-m-d",strtotime($_POST['tdate']));
		$suppliers = $_POST['suppliers'];
		$customers = $_POST['customers'];
		$billnos = $_POST['billnos'];
		$sectors = $_POST['sectors'];
		$item_cat = $_POST['item_cat'];
		$items = $_POST['items'];
		$users = $_POST['users'];
		$prices = $_POST['prices'];
		$exports = $_POST['exports'];

        $groups = array(); $grp_all_flag = 0;
        foreach($_POST['groups'] as $grps){ $groups[$grps] = $grps; if($grps == "all"){ $grp_all_flag = 1; } }
        $grp_list = implode("@",$groups);
	}
	$url = "../PHPExcel/Examples/SalesReportMaster_ta-Excel.php?fdate=".$fdate."&tdate=".$tdate."&customers=".$customers."&billnos=".$billnos."&item_cat=".$item_cat."&items=".$items."&sectors=".$sectors."&users=".$users."&groups=".$grp_list."&prices=".$prices;
	
?>
<html>
	<head>
		<script>
			var exptype = '<?php echo $exports; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
        <?php include "header_head2.php"; ?>
	</head>
	<body>
	    <?php if($exports == "displaypage" || $exports == "printerfriendly") { ?>
			<table align="center">
				<tr>
                    <?php
                    if($dlogo_flag > 0) { ?>
                        <td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                    <?php }
                    else{ 
                     ?>
					<td><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
					<td><?php echo $cdetails; } ?></td>
					<td align="center">
						<h3>Sales Report</h3>
						<?php if($customers == "all" || $customers == "select" || $customers == ""){ ?><label><b style="color: green;">Customer:</b>&nbsp;<?php echo "All"; ?></label><br/><?php }
                        else{ ?><label><b style="color: green;">Customer:</b>&nbsp;<?php echo $cus_name[$customers]; ?></label><br/><?php } ?>
						<label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
					</td>
				</tr>
			</table>
	    <?php } ?>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table class="main-table table-sm table-hover" id="main_table">
						<?php if($exports == "displaypage" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:290px;">
                                            <label for="groups[0]">Group</label>
                                            <select name="groups[]" id="groups[0]" class="form-control select2" style="width:280px;" multiple onchange="filter_group_customers()">
                                                <option value="all" <?php foreach($groups as $grps){ if($grps == "all"){ echo "selected"; } } ?>>All</option>
											    <?php foreach($grp_code as $gcode){ ?><option value="<?php echo $gcode; ?>" <?php foreach($groups as $grps){ if($grps == $gcode){ echo "selected"; } } ?>><?php echo $grp_name[$gcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label for="suppliers">Supplier</label>
                                            <select name="suppliers" id="suppliers" class="form-control select2" style="width:160px;">
                                                <option value="all" <?php if($suppliers == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($sup_code as $vcode){ ?><option value="<?php echo $vcode; ?>" <?php if($suppliers == $vcode){ echo "selected"; } ?>><?php echo $sup_name[$vcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label for="customers">Customer</label>
                                            <select name="customers" id="customers" class="form-control select2" style="width:160px;">
                                                <option value="all" <?php if($customers == "all"){ echo "selected"; } ?>>All</option>
											    <?php
                                                if($grp_all_flag == 1){ foreach($cus_code as $vcode){ ?><option value="<?php echo $vcode; ?>" <?php if($customers == $vcode){ echo "selected"; } ?>><?php echo $cus_name[$vcode]; ?></option><?php } }
                                                else{ foreach($cus_code as $vcode){ if(!empty($groups[$cus_group[$vcode]])){ ?><option value="<?php echo $vcode; ?>" <?php if($customers == $vcode){ echo "selected"; } ?>><?php echo $cus_name[$vcode]; ?></option><?php } } }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Category</label>
                                            <select name="item_cat" id="item_cat" class="form-control select2" style="width:160px;" onchange="fetch_item_list();">
                                                <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                                <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:150px;">
                                            <label for="items">Item</label>
                                            <select name="items" id="items" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php if($item_cat == "all"){ ?>
                                                <?php foreach($item_code as $icode){ if($item_name[$icode] != ""){ ?>
                                                <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                <?php } } }
                                                else{
                                                    foreach($item_code as $icode){
                                                        if($item_cat == $item_category[$icode]){
                                                        ?>
                                                        <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:150px;">
                                            <label for="users">User</label>
                                            <select name="users" id="users" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($users == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($user_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($users == $ucode){ echo "selected"; } ?>><?php echo $user_name[$ucode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:150px;">
                                            <label for="exports">Export To</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;">
                                                <option <?php if($exports == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
                                                <option <?php if($exports == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
                                                <option <?php if($exports == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 160px;">
                                            <label for="billnos">Book Invoice</label>
                                            <input type="text" name="billnos" id="billnos" class="form-control" value="<?php echo $billnos; ?>" style="padding:0;padding-left:2px;width:150px;" />
                                        </div>
                                        <div class="form-group" style="width: 160px;">
                                            <label for="prices">Price</label>
                                            <input type="text" name="prices" id="prices" class="form-control" value="<?php echo $prices; ?>" style="padding:0;padding-left:2px;width:150px;" />
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                            $bwtd_det_col = 0; $aflag = 1; $html = '';

                            $html .= '<thead class="thead2">';
                            $html .= '<tr>';
                            for($i = 1;$i <= $col_count;$i++){
                                if(!empty($field_details[$i.":".$aflag])){
                                    if($field_details[$i.":".$aflag] == "date_flag"){ $html .= '<th id="order_date">Date</th>'; $bwtd_det_col++; }
                                    else if($field_details[$i.":".$aflag] == "inv_flag"){ $html .= '<th id="order">Invoice</th>'; $bwtd_det_col++; }
                                    else if($field_details[$i.":".$aflag] == "binv_flag"){ $html .= '<th id="order">Book Invoice</th>'; $bwtd_det_col++; }
                                    else if($field_details[$i.":".$aflag] == "vendor_flag"){ $html .= '<th id="order">Customer</th>'; $bwtd_det_col++; }
                                    else if($field_details[$i.":".$aflag] == "salesup_flag"){ $html .= '<th id="order">Company</th>'; $bwtd_det_col++; }
                                    else if($field_details[$i.":".$aflag] == "item_flag"){ $html .= '<th id="order">Item</th>'; $bwtd_det_col++; }
                                    else if($field_details[$i.":".$aflag] == "jals_flag"){ $html .= '<th id="order_num">Jals</th>'; }
                                    else if($field_details[$i.":".$aflag] == "birds_flag"){ $html .= '<th id="order_num">Birds</th>'; }
                                    else if($field_details[$i.":".$aflag] == "tweight_flag"){ $html .= '<th id="order_num">T. Weight</th>'; }
                                    else if($field_details[$i.":".$aflag] == "eweight_flag"){ $html .= '<th id="order_num">E. Weight</th>'; }
                                    else if($field_details[$i.":".$aflag] == "nweight_flag"){ $html .= '<th id="order_num">N. Weight</th>'; }
                                    else if($field_details[$i.":".$aflag] == "farm_weight"){ $html .= '<th id="order">Farm Weight</th>'; }
                                    else if($field_details[$i.":".$aflag] == "aweight_flag"){ $html .= '<th id="order_num">Avg. Weight</th>'; }
                                    else if($field_details[$i.":".$aflag] == "prate_flag"){ $html .= '<th id="order_num">Paper Rate</th>'; }
                                    else if($field_details[$i.":".$aflag] == "price_flag"){ $html .= '<th id="order_num">Price</th>'; }
                                    else if($field_details[$i.":".$aflag] == "freightamt_flag"){ $html .= '<th id="order_num">Freight</th>'; }
                                    else if($field_details[$i.":".$aflag] == "tcds_flag"){ $html .= '<th id="order_num">TCS</th>'; }
                                    else if($field_details[$i.":".$aflag] == "jfreight_flag"){ $html .= '<th id="order_num">Freight</th>'; }
                                    else if($field_details[$i.":".$aflag] == "discount_flag"){ $html .= '<th id="order_num">Discount</th>'; }
                                    else if($field_details[$i.":".$aflag] == "tamt_flag"){ $html .= '<th id="order_num">Total Amount</th>'; }
                                    else if($field_details[$i.":".$aflag] == "sector_flag"){ $html .= '<th id="order">Warehouse</th>'; }
                                    else if($field_details[$i.":".$aflag] == "remarks_flag"){ $html .= '<th id="order">Remarks</th>'; }
                                    else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $html .= '<th id="order">Vehicle</th>'; }
                                    else if($field_details[$i.":".$aflag] == "driver_flag"){ $html .= '<th id="order">Driver</th>'; }
                                    else if($field_details[$i.":".$aflag] == "cr_flag"){ $html .= '<th id="order_num">Sales</th>'; }
                                    else if($field_details[$i.":".$aflag] == "dr_flag"){ $html .= '<th id="order_num">Receipts</th>'; }
                                    else if($field_details[$i.":".$aflag] == "rb_flag"){ $html .= '<th id="order_num">Running Balance</th>'; }
                                    else if($field_details[$i.":".$aflag] == "user_flag"){ $html .= '<th id="order">User</th>'; }
                                    else if($field_details[$i.":".$aflag] == "atime_flag"){ $html .= '<th id="order">Added Time</th>'; }
                                    else{ }
                                }
                            }
                            $html .= '</tr>';
                            $html .= '</thead>';

                            if($prate_flag == 1 || $prate_flag == "1"){
                                $sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['cgroup']."@".$row['code']] = $row['new_price']; }
                            }

                            $cus_filter = $cus_list = $sec_list = "";
                            if($customers != "all"){ $cus_filter = " AND `customercode` IN ('$customers')"; }
                            else if($grp_all_flag == 0){
                                foreach($cus_code as $ccode){
                                    $gcode = $cus_group[$ccode];
                                    if(empty($groups[$gcode]) || $groups[$gcode] == ""){ }
                                    else{ if($cus_list == ""){ $cus_list = $ccode; } else{ $cus_list = $cus_list."','".$ccode; } }
                                }
                                $cus_filter = " AND `customercode` IN ('$cus_list')";
                            }
                            else{ $cus_list = implode("','",$cus_code); $cus_filter = " AND `customercode` IN ('$cus_list')"; }

                            if($items != "all"){ $item_filter = " AND `itemcode` IN ('$items')"; }
                            else if($item_cat == "all"){ $item_filter = ""; }
                            else{
                                $icat_list = $item_filter = "";
                                foreach($item_code as $icode){
                                    $item_category[$icode];
                                    if(!empty($item_category[$icode]) && $item_category[$icode] == $item_cat){
                                        if($icat_list == ""){ $icat_list = $icode; } else{ $icat_list = $icat_list."','".$icode; }
                                    }
                                }
                                $item_filter = " AND `itemcode` IN ('$icat_list')";
                            }
                            
                            if($billnos == "") { $binv_filter = ""; } else { $binv_filter = " AND `bookinvoice` = '$billnos'"; }
                            if($prices == "") { $rate_filter = ""; } else { $rate_filter = " AND `itemprice` = '$prices'"; }
                            if($users == "all"){ $user_filter = ""; } else{ $user_filter = " AND `addedemp` IN ('$users')"; }

                            if($sectors == "all"){ $sec_list = implode("','",$sector_code); $sector_filter = " AND `warehouse` IN ('$sec_list')"; }
                            else{ $sector_filter = " AND `warehouse` IN ('$sectors')"; }
                            if($_SESSION['dbase'] == "poulso6_chicken_tg_lsfi"){ $sector_filter = ""; }

                            $html .= '<tbody class="tbody1">';

                            $pur_fltr = "";
                            if($suppliers != "all"){
                                $sql = "SELECT * FROM `pur_purchase` WHERE `vendorcode` IN ('$suppliers') ORDER BY `date`,`invoice` ASC";
                                $query = mysqli_query($conn,$sql); $pur_alist = array();
                                while($row = mysqli_fetch_assoc($query)){ $pur_alist[$row['invoice']] = $row['invoice']; }
                                $ptrno_list = implode("','", $pur_alist);
                                if(sizeof($pur_alist) > 0){ $pur_fltr = " AND `link_trnum` IN ('$ptrno_list')"; } else{ $pur_fltr = " AND `link_trnum` IN ('none')"; }
                            }
                            
                            $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$pur_fltr."".$cus_filter."".$binv_filter."".$rate_filter."".$item_filter."".$sector_filter."".$user_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                            $query = mysqli_query($conn,$sql); $link_trnum = array();
                            while($row = mysqli_fetch_assoc($query)){ if($row['link_trnum'] != ""){ $link_trnum[$row['link_trnum']] = $row['link_trnum']; } }

                            if(sizeof($link_trnum) > 0){
                                $ltno_list = implode("','", $link_trnum);
                                $sql = "SELECT * FROM `pur_purchase` WHERE `invoice` IN ('$ltno_list') ORDER BY `date`,`invoice` ASC";
                                $query = mysqli_query($conn,$sql); $ltno_vname = array();
                                while($row = mysqli_fetch_assoc($query)){ $ltno_vname[$row['invoice']] = $sup_name[$row['vendorcode']]; }
                            }

                            $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$pur_fltr."".$cus_filter."".$binv_filter."".$rate_filter."".$item_filter."".$sector_filter."".$user_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                            $query = mysqli_query($conn,$sql); $i = 0; $sales = $inv_count = $slc_freightamt = $slc_tcdsamt = $slc_roundoff = $slc_finaltotal = array();
                            while($row = mysqli_fetch_assoc($query)){
                               
                                $sales[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['customercode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode']."@".$row['drivercode']."@".$row['addedemp']."@".$row['freight_amt']."@".$row['farm_weight']."@".$row['link_trnum']."@".$row['addedtime'];

                                if(empty($inv_count[$row['invoice']]) || $inv_count[$row['invoice']] == ""){
                                    $inv_count[$row['invoice']] = 1;
                                    if($row['freight_amount_jal'] == "" || $row['freight_amount_jal'] == NULL){ $slc_freightamt[$row['invoice']] = 0; } else{ $slc_freightamt[$row['invoice']] = (float)$row['freight_amount_jal']; }
                                    if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){ $slc_tcdsamt[$row['invoice']] = 0.00; } else{ $slc_tcdsamt[$row['invoice']] = $row['tcdsamt']; }
                                    if($row['roundoff'] == "" || $row['roundoff'] == NULL){ $slc_roundoff[$row['invoice']] = 0.00; } else{ if(($row['itotal'] + $row['tcdsamt']) <= $row['finaltotal']){ $slc_roundoff[$row['invoice']] = $row['roundoff']; } else{ $slc_roundoff[$row['invoice']] = -1 *($row['roundoff']); } }
                                    $slc_finaltotal[$row['invoice']] = $row['finaltotal'];
                                }
                                else{
                                    $inv_count[$row['invoice']] = $inv_count[$row['invoice']] + 1;
                                }
                                $i++;
                            }
                            $ccount = sizeof($sales); $exi_inv = "";
                            for($cdate = strtotime($fdate);$cdate <= strtotime($tdate);$cdate += (86400)){
                                $adate = date('Y-m-d', $cdate);
                                for($j = 0;$j <= $ccount;$j++){
                                    if($sales[$adate."@".$j] != ""){
                                        $sales_details = explode("@",$sales[$adate."@".$j]);
                                        $html .= '<tr>';
                                        $tacount = $tacount + (float)$sales_details[11];
                                        if($exi_inv != $sales_details[1]){
                                            $exi_inv = $sales_details[1];
                                            if(number_format_ind($slc_finaltotal[$sales_details[1]]) == number_format_ind($rb_amt)){
                                                $rb_amt = 0;
                                            }
                                            else{
                                                $rb_amt = $rb_amt + $slc_finaltotal[$sales_details[1]];
                                            }
                                            $ft_jfrgt = (float)$ft_jfrgt + (float)$slc_freightamt[$sales_details[1]];
                                            $ft_tcds = $ft_tcds + $slc_tcdsamt[$sales_details[1]];
                                            $ft_roundoff = $ft_roundoff + $slc_roundoff[$sales_details[1]];
                                            $fst_famt = $fst_famt + $slc_finaltotal[$sales_details[1]];
                                            
                                            for($i = 1;$i <= $col_count;$i++){
                                                if($field_details[$i.":".$aflag] == "date_flag"){ $html .= '<td>'.date("d.m.Y",strtotime($sales_details[0])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "inv_flag"){ $html .= '<td>'.$sales_details[1].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "binv_flag"){ $html .= '<td>'.$sales_details[2].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "vendor_flag"){ $html .= '<td>'.$cus_name[$sales_details[3]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "salesup_flag"){ $html .= '<td>'.$ltno_vname[$sales_details[26]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "item_flag"){ $html .= '<td>'.$item_name[$sales_details[7]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "jals_flag"){ $html .= '<td class="text-right">'.str_replace(".00","",number_format_ind($sales_details[4])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "birds_flag"){ $html .= '<td class="text-right">'.str_replace(".00","",number_format_ind($sales_details[8])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "tweight_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[5]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "eweight_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[6]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "nweight_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[9]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "farm_weight"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[25]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "aweight_flag"){
                                                    if($sales_details[9] > 0 && $sales_details[8] > 0){
                                                        $html .= '<td class="text-right">'.number_format_ind($sales_details[9] / $sales_details[8]).'</td>';
                                                    }
                                                    else{
                                                        $html .= '<td class="text-right">'.number_format_ind(0).'</td>';
                                                    }
                                                }
                                                else if($field_details[$i.":".$aflag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]."@".$sales_details[7]; $ppr_count++; $ppr_amt = $ppr_amt + $prates[$prate_index]; $html .= '<td class="text-right">'.number_format_ind($prates[$prate_index]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "price_flag"){ $html .= '<td class="text-right">'.$sales_details[10].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "freightamt_flag"){ $html .= '<td class="text-right">'.$sales_details[24].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "tamt_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[11]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "sector_flag"){ $html .= '<td>'.$sector_name[$sales_details[16]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "remarks_flag"){ $html .= '<td>'.$sales_details[20].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $html .= '<td>'.$sales_details[21].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "driver_flag"){ $html .= '<td>'.$sales_details[22].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "discount_flag"){ $html .= '<td>'.$sales_details[18].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "user_flag"){ $html .= '<td>'.$user_name[$sales_details[23]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "atime_flag"){ $html .= '<td>'.date("d.m.Y H:i:s",strtotime($sales_details[27])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "jfreight_flag"){ $html .= '<td class="text-right">'.number_format_ind($slc_freightamt[$sales_details[1]]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "tcds_flag"){ $html .= '<td class="text-right">'.number_format_ind($slc_tcdsamt[$sales_details[1]]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "cr_flag"){ $html .= '<td class="text-right">'.number_format_ind($slc_finaltotal[$sales_details[1]]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "dr_flag"){ $html .= '<td></td>'; }
                                                else if($field_details[$i.":".$aflag] == "rb_flag"){ $html .= '<td class="text-right">'.number_format_ind($rb_amt).'</td>'; }
                                                else{ }
                                            }
                                        }
                                        else{
                                            for($i = 1;$i <= $col_count;$i++){
                                                if($field_details[$i.":".$aflag] == "date_flag"){ $html .= '<td>'.date("d.m.Y",strtotime($sales_details[0])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "inv_flag"){ $html .= '<td>'.$sales_details[1].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "binv_flag"){ $html .= '<td>'.$sales_details[2].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "vendor_flag"){ $html .= '<td>'.$cus_name[$sales_details[3]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "salesup_flag"){ $html .= '<td>'.$ltno_vname[$sales_details[26]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "item_flag"){ $html .= '<td>'.$item_name[$sales_details[7]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "jals_flag"){ $html .= '<td class="text-right">'.str_replace(".00","",number_format_ind($sales_details[4])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "birds_flag"){ $html .= '<td class="text-right">'.str_replace(".00","",number_format_ind($sales_details[8])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "tweight_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[5]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "eweight_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[6]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "nweight_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[9]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "farm_weight"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[25]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "aweight_flag"){
                                                    if(!empty($sales_details[9]) && $sales_details[9] > 0 && !empty($sales_details[8]) && $sales_details[8] > 0){
                                                        $html .= '<td class="text-right">'.number_format_ind($sales_details[9] / $sales_details[8]).'</td>';
                                                    }
                                                    else{
                                                        $html .= '<td class="text-right">'.number_format_ind(0).'</td>';
                                                    }
                                                }
                                                else if($field_details[$i.":".$aflag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]; $ppr_count++; $ppr_amt = $ppr_amt + $prates[$prate_index]; $html .= '<td class="text-right">'.number_format_ind($prates[$prate_index]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "price_flag"){ $html .= '<td class="text-right">'.$sales_details[10].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "freightamt_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[24]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "tamt_flag"){ $html .= '<td class="text-right">'.number_format_ind($sales_details[11]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "sector_flag"){ $html .= '<td>'.$sector_name[$sales_details[16]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "remarks_flag"){ $html .= '<td>'.$sales_details[20].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $html .= '<td>'.$sales_details[21].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "driver_flag"){ $html .= '<td>'.$sales_details[22].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "discount_flag"){ $html .= '<td>'.$sales_details[18].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "user_flag"){ $html .= '<td>'.$user_name[$sales_details[23]].'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "atime_flag"){ $html .= '<td>'.date("d.m.Y H:i:s",strtotime($sales_details[27])).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "jfreight_flag"){ $html .= '<td></td>'; }
                                                else if($field_details[$i.":".$aflag] == "tcds_flag"){ $html .= '<td></td>'; }
                                                else if($field_details[$i.":".$aflag] == "cr_flag"){ $html .= '<td class="text-right">'.number_format_ind($slc_finaltotal[$sales_details[1]]).'</td>'; }
                                                else if($field_details[$i.":".$aflag] == "dr_flag"){ $html .= '<td></td>'; }
                                                else if($field_details[$i.":".$aflag] == "rb_flag"){ $html .= '<td class="text-right">'.number_format_ind($rb_amt).'</td>'; }
                                                else{ }
                                            }
                                        }
                                            
                                            $tot_farm_wt += (float)$sales_details[25];
                                            $tot_net_wt += (float)$sales_details[9];

                                            $tbcount = $tbcount + (float)$sales_details[8];
                                            $tjcount = $tjcount + (float)$sales_details[4];
                                            $tncount = $tncount + (float)$sales_details[9];
                                            $twcount = $twcount + (float)$sales_details[5];
                                            $tecount = $tecount + (float)$sales_details[6];
                                            $tdcount = $tdcount + (float)$sales_details[18];
                                            $ttcount = $ttcount + (float)$sales_details[19];
                                            $tfritcount = $tfritcount + (float)$sales_details[24];
                                            
                                        $html .= '</tr>';
                                    }
                                }
							}
                            $html .= '</tbody>';
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="'.$bwtd_det_col.'">Total</th>';
                            for($i = 1;$i <= $col_count;$i++){
                                if($field_details[$i.":".$aflag] == "jals_flag"){ $html .= '<th class="text-right">'.str_replace(".00","",number_format_ind($tjcount)).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "birds_flag"){ $html .= '<th class="text-right">'.str_replace(".00","",number_format_ind(round($tbcount))).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "tweight_flag"){ $html .= '<th class="text-right">'.number_format_ind($twcount).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "eweight_flag"){ $html .= '<th class="text-right">'.number_format_ind($tecount).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "nweight_flag"){ $html .= '<th class="text-right">'.number_format_ind($tot_net_wt).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "farm_weight"){ $html .= '<th class="text-right">'.number_format_ind($tot_farm_wt).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "aweight_flag"){
                                    if($tbcount > 0){
                                        $html .= '<th class="text-right">'.number_format_ind($tncount / $tbcount).'</th>';
                                    }
                                    else{
                                        $html .= '<th class="text-right">'.number_format_ind(0).'</th>';
                                    }
                                }
                                else if($field_details[$i.":".$aflag] == "prate_flag"){
                                    if($ppr_count > 0){
                                        $html .= '<th class="text-right">'.number_format_ind($ppr_amt / $ppr_count).'</th>';
                                    }
                                    else{
                                        $html .= '<th class="text-right">'.number_format_ind(0).'</th>';
                                    }
                                }
                                else if($field_details[$i.":".$aflag] == "price_flag"){
                                    if($tncount > 0){
                                        $html .= '<th class="text-right" title="'.$tacount.'--'.$tncount.'">'.number_format_ind(round(((((float)$tacount)) / $tncount),2)).'</th>';
                                    }
                                    else{
                                        $html .= '<th class="text-right">'.number_format_ind(0).'</th>';
                                    }
                                    
                                }
                                else if($field_details[$i.":".$aflag] == "freightamt_flag"){ $html .= '<th class="text-right">'.number_format_ind($tfritcount).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "jfreight_flag"){ $html .= '<th class="text-right">'.number_format_ind($ft_jfrgt).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "tcds_flag"){ $html .= '<th class="text-right">'.number_format_ind($ft_tcds).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "discount_flag"){ $html .= '<th>'.number_format_ind($tdcount).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "tamt_flag"){ $html .= '<th class="text-right">'.number_format_ind($tacount).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "sector_flag"){ $html .= '<th></th>'; }
                                else if($field_details[$i.":".$aflag] == "remarks_flag"){ $html .= '<th></th>'; }
                                else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $html .= '<th></th>'; }
                                else if($field_details[$i.":".$aflag] == "driver_flag"){ $html .= '<th></th>'; }
                                else if($field_details[$i.":".$aflag] == "weighton_flag"){ $html .= '<th class="text-right">'.number_format_ind($tot_farm_wt).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "cr_flag"){ $html .= '<th class="text-right">'.number_format_ind($fst_famt + $fdt_famt).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "dr_flag"){ $html .= '<th class="text-right">'.number_format_ind($frt_famt + $fct_famt).'</th>'; }
                                else if($field_details[$i.":".$aflag] == "rb_flag"){ $html .= '<th></th>'; }
                                else if($field_details[$i.":".$aflag] == "user_flag"){ $html .= '<th></th>'; }
                                else if($field_details[$i.":".$aflag] == "atime_flag"){ $html .= '<th></th>'; }
                                else{ }
                            }
                            $html .= '</tr>';
                            $html .= '</tfoot>';

                            echo $html;
                        }
                            ?>
						</table>
					</form>
				</div>
		</section>
        <script>
            function checkval(){
                var groups = document.getElementById('groups[0]').value;
                var l = true;
                if(groups == ""){
                    alert("Please select Group");
                    document.getElementById('groups[0]').focus();
                    l = false;
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script>
            function filter_group_customers(){
                var selected = []; var fcode = ""; var all_flag = 0;
                
                removeAllOptions(document.getElementById("customers"));
                myselect = document.getElementById("customers");
                theOption1=document.createElement("OPTION");
                theText1=document.createTextNode("-All-");
                theOption1.value = "all";
                theOption1.appendChild(theText1);
                myselect.appendChild(theOption1);

                for(var option of document.getElementById('groups[0]').options){
                    if(option.selected){
                        fcode = option.value;
                        if(fcode == "all"){
                            all_flag = 1;
                        }
                    }
                }
                if(parseInt(all_flag) == 1){
                    <?php
                    foreach($cus_code as $vcode){
                    ?> 
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
                    theOption1.value = "<?php echo $vcode; ?>";
                    theOption1.appendChild(theText1);
                    myselect.appendChild(theOption1);	
                    <?php
                    }
                    ?>
                }
                else{
                    <?php
                    foreach($cus_code as $vcode){
                        $gcode = $cus_group[$vcode];
                    ?>
                    for(var option of document.getElementById('groups[0]').options){
                        if(option.selected){
                            fcode = option.value;
                            <?php
                            echo "if(fcode == '$gcode'){";
                            ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
                            theOption1.value = "<?php echo $vcode; ?>";
                            theOption1.appendChild(theText1);
                            myselect.appendChild(theOption1);
                            <?php
                            echo "}";
                            ?>
                        }
                    }
                    <?php
                    }
                    ?>
                }
            }
            function fetch_item_list(){
                var fcode = document.getElementById("item_cat").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(fcode != "all"){
                <?php
                    foreach($item_code as $icode){
                        $icats = $item_category[$icode];
                        echo "if(fcode == '$icats'){";
                ?> 
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                <?php
                        echo "}";
                    }
                ?>
                }
                else{
                    <?php
                        foreach($item_code as $icode){
                            $icats = $item_category[$icode];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
		<?php if($exports == "displaypage" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
