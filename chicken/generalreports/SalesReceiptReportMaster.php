<?php
	//SalesReceiptReportMaster.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php";$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid']; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; $dbname = $db;
		$users_code = $_GET['emp_code'];}
			
	$today = date("Y-m-d");
	$sql = "SELECT *  FROM `main_linkdetails` WHERE `href` LIKE '%SalesReceiptReportMaster.php%'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cid = $row['childid']; }
	
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fromdate'])); $tdate =date("Y-m-d",strtotime( $_POST['todate']));
		$uname = $_POST['ucode']; $wname = $_POST['wname']; $gname = $_POST['gname']; $book_inv = $_POST['book_inv']; $cname = $_POST['cname']; $iname = $_POST['iname'];
	$uname = $wname = $gname =  $iname = "all"; $book_inv = "";
	}
	else{
		$fdate = $tdate = $today;
		$uname = $wname = $gname = $cname =  $iname = "all"; $book_inv = "";
	}
	
	$sql='SHOW COLUMNS FROM `master_reportfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("weighton_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `weighton_flag` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Price on Farm Weight Flag'"; mysqli_query($conn,$sql); }
	if(in_array("farm_weight", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `farm_weight` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Farm Weight'"; mysqli_query($conn,$sql); }
	if(in_array("cus_oba_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `cus_oba_flag` VARCHAR(100) NULL DEFAULT NULL COMMENT '' AFTER `farm_weight`"; mysqli_query($conn,$sql); }
	if(in_array("cus_tba_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `cus_tba_flag` VARCHAR(100) NULL DEFAULT NULL COMMENT '' AFTER `cus_oba_flag`"; mysqli_query($conn,$sql); }
	if(in_array("cus_cba_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_reportfields` ADD `cus_cba_flag` VARCHAR(100) NULL DEFAULT NULL COMMENT '' AFTER `cus_tba_flag`"; mysqli_query($conn,$sql); }

	
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

	//User Access Filter
	if($utype == "S" || $utype == "A"){
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
	}
	else{
		//$sql = "SELECT * FROM `log_useraccess` WHERE `empcode` = '$users_code' AND `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		//while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
	}
	/*if($utype == "S" || $utype == "A"){
		$user_filter = "";
	}
	else{
		//if($uname == "all"){ $ecodes = ""; foreach($user_code as $ecode){ if($ecodes == ""){ $ecodes = $ecode; } else{ $ecodes = $ecodes."','".$ecode; } } $user_filter = " AND a.addedemp IN ('$ecodes')"; }
		//else{ $user_filter = " AND a.addedemp = '".$uname."'"; }
		$user_filter = "";
	}*/
	if($uname == "all"){ $ecodes = ""; foreach($user_code as $ecode){ if($ecodes == ""){ $ecodes = $ecode; } else{ $ecodes = $ecodes."','".$ecode; } } $user_filter = " AND a.addedemp IN ('$ecodes')"; }
	else{ $user_filter = " AND a.addedemp = '".$uname."'"; }

	if($loc_access == "all" || $loc_access == "All" || $loc_access == "" || $loc_access == NULL){
		$warehouse_filter = "";
	}
	else{
		$wh_code = str_replace(",","','",$loc_access);
		$warehouse_filter = " AND code IN ('$wh_code')";
	}
	if($wname == "all"){
		if($loc_access == "all" || $loc_access == "All" || $loc_access == "" || $loc_access == NULL){
			$sector_filter = "";
		}
		else{
			$wh_code = str_replace(",","','",$loc_access);
			$sector_filter = " AND a.warehouse IN ('$wh_code')";
		}
	}
	else{
		$sector_filter = " AND a.warehouse IN ('$wname')";
	}
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_filter." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

	//Customer Access Filter
	if($cgroup_access == "all" || $cgroup_access == "All" || $cgroup_access == "" || $cgroup_access == NULL){
		$cgroup_filter = $user_group_codes = "";
	}
	else{
		$cg_code = str_replace(",","','",$cgroup_access);
		$user_group_codes = " AND code IN ('$cg_code')";
		$cgroup_filter = " AND groupcode IN ('$cg_code')";
	}
	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'".$user_group_codes." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cgroup_filter." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$cus_code[$row['code']] = $row['code'];
		$cus_name[$row['code']] = $row['name'];
		$cus_group[$row['code']] = $row['groupcode'];
		if($row['obtype'] == "Cr"){
			$obcramt[$row['code']] = $row['obamt'];
			$obdramt[$row['code']] = 0;
		}
		else if($row['obtype'] == "Dr"){
			$obdramt[$row['code']] = $row['obamt'];
			$obcramt[$row['code']] = 0;
		}
		else{
			$obdramt[$row['code']] = $obcramt[$row['code']] = 0;
		}
	}
	
	if($gname == "all" && $cname == "all"){ $ccodes = ""; foreach($cus_code as $vcode){ if($ccodes == ""){ $ccodes = $vcode; } else{ $ccodes = $ccodes."','".$vcode; } } $customer_filter = " AND a.customercode IN ('$ccodes')"; }
	else if($gname == "all" && $cname != "all"){ $customer_filter = " AND a.customercode = '$cname'";$customer_filter1 = " AND ccode = '$cname'"; }
	else if($gname != "all" && $cname == "all"){ $ccodes = ""; foreach($cus_code as $vcode){ if($gname == $cus_group[$vcode]){ if($ccodes == ""){ $ccodes = $vcode; } else{ $ccodes = $ccodes."','".$vcode; } } } $customer_filter = " AND a.customercode IN ('$ccodes')"; }
	else if($gname != "all" && $cname != "all"){ $customer_filter = " AND a.customercode = '$cname'";$customer_filter1 = " AND ccode = '$cname'"; }
	else{ $customer_filter = "";$customer_filter1 = ""; }

	//Item Access Filter
	if($iname == "all") { $item_filter = ""; } else { $item_filter = " AND a.itemcode = '$iname'"; }
	if($book_inv == "") { $binv_filter = ""; } else { $binv_filter = " AND a.bookinvoice = '$book_inv'"; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_jalsfreight_flag = $row['cus_jalsfreight_flag']; }
	if($cus_jalsfreight_flag == "" || $cus_jalsfreight_flag == NULL || $cus_jalsfreight_flag == 0 || $cus_jalsfreight_flag == "0.00"){ $cus_jalsfreight_flag = 0; }
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
		$field_details[$row['item_flag']] = "item_flag";
		$field_details[$row['jals_flag']] = "jals_flag";
		$field_details[$row['birds_flag']] = "birds_flag";
		$field_details[$row['tweight_flag']] = "tweight_flag";
		$field_details[$row['eweight_flag']] = "eweight_flag";
		$field_details[$row['nweight_flag']] = "nweight_flag";
		$field_details[$row['farm_weight']] = "farm_weight"; $t1 = explode(":",$row['farm_weight']); if($t1[1] == 1 || $t1[1] == "1"){ $farm_wt_flag = 1; }
		$field_details[$row['aweight_flag']] = "aweight_flag";
		$field_details[$row['prate_flag']] = "prate_flag";
		$pdet = explode(":",$row['prate_flag']); if($pdet[1] == 1 || $pdet[1] == "1"){ $prate_flag = 1; }
		$field_details[$row['price_flag']] = "price_flag";
		$field_details[$row['freightamt_flag']] = "freightamt_flag"; $t1 = explode(":",$row['freightamt_flag']); if($t1[1] == 1 || $t1[1] == "1"){ $frt_amt_flag = 1; }
		$field_details[$row['cus_oba_flag']] = "cus_oba_flag"; $t1 = explode(":",$row['cus_oba_flag']); if($t1[1] == 1 || $t1[1] == "1"){ $cob_flag = 1; }
		$field_details[$row['cus_tba_flag']] = "cus_tba_flag"; $t1 = explode(":",$row['cus_tba_flag']); if($t1[1] == 1 || $t1[1] == "1"){ $ctb_flag = 1; }
		$field_details[$row['cus_cba_flag']] = "cus_cba_flag"; $t1 = explode(":",$row['cus_cba_flag']); if($t1[1] == 1 || $t1[1] == "1"){ $ccb_flag = 1; }
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
		$note_flag = $row['note_flag'];
		$note_code = $row['note_code'];
		$vsign_flag = $row['vsign_flag'];
		$csign_flag = $row['csign_flag'];
		$qr_img_flag = $row['qr_img_flag'];
		$col_count = $row['count'];
	}
	
	$exoption = "displaypage"; $users = "all"; $dcno = "";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$users = $_POST['users'];
		$dcno = $_POST['dcno'];
		$exl_fdate = $_POST['fromdate'];
		$exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname']; $exl_iname = $_POST['iname']; $exl_wname = $_POST['wname']; $exl_user = $_POST['ucode']; $exl_gname = $_POST['gname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname = $exl_iname = $exl_wname = $exl_user = $exl_gname = "all";
	}
	$url = "../PHPExcel/Examples/SalesReceiptReportMaster-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&ccode=".$exl_cname."&item=".$exl_iname."&sector=".$exl_wname."&user=".$exl_user."&gname=".$exl_gname."&cid=".$cid."&users=".$users."&dcno=".$dcno;
	
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
		<style>
			.thead2 th {
 top: 0;
 position: sticky;
 background-color: #98fb98;
				
			}
		</style>
		<style>
			body{
				font-size: 12px;
				color: black;
			}
			.thead2,.tbody1 {
				font-size: 12px;
				padding: 1px;
				color: black;
			}
			.formcontrol {
				font-size: 12px;
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				font-size: 12px;
				color: black;
				padding-right: 5px;
				text-align: right;
			}
			.reportselectionlabel{
				font-size: 12px;
			}
			.table1 table, .table1 thead, .table1 tbody, .table1 tr, .table1 th, .table1 td{
				border: 0.1vh solid black;
				border-collapse: collapse;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini" align="center">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $qr_img_path = $row['qr_img_path']; ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
					<td align="center">
						<h3>Sales Receipt Report</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $cus_name[$cname]; ?></label><br/>
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
				<form action="SalesReceiptReportMaster.php" method="post" onsubmit="return checkval()">
					<?php } else { ?>
					<form action="SalesReceiptReportMaster.php?db=<?php echo $db; ?>&emp_code=<?php echo $_GET['emp_code']; ?>" method="post" onSubmit="return checkval()">
					<?php } ?>
						<table class="table1" id="main_table" style="min-width:100%;line-height:23px;">
							<?php if($exoption == "displaypage") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="25">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>"/>
									&ensp;&ensp;
										<!--- <label class="reportselectionlabel">Group</label>&nbsp;
										<select name="gname" id="gname" class="form-control select2" onChange="groupbycussel()">
											<option value="all">-All-</option>
											<?php
												foreach($grp_code as $gcode){
											?>
													<option <?php if($gname == $gcode) { echo 'selected'; } ?> value="<?php echo $gcode; ?>"><?php echo $grp_name[$gcode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Book Invoice</label>&nbsp;
										<input type="text" name="book_inv" id="book_inv" class="formcontrol" value="<?php echo $book_inv; ?>"/>
									&ensp;&ensp; --->
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($cus_code as $vcode){
											?>
													<option <?php if($cname == $vcode) { echo 'selected'; } ?> value="<?php echo $vcode; ?>"><?php echo $cus_name[$vcode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">User</label>&nbsp;
										<select name="users" id="users" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($user_code as $ucode){
											?>
													<option <?php if($users == $ucode) { echo 'selected'; } ?> value="<?php echo $ucode; ?>"><?php echo $user_name[$ucode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
									<!---	<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="iname" id="iname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($item_code as $icode){
											?>
													<option <?php if($iname == $icode) { echo 'selected'; } ?> value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option>
											<?php
												}
											?>
										</select>
									<br/>
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="wname" id="wname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($sector_code as $vcode){
											?>
													<option <?php if($wname == $vcode) { echo 'selected'; } ?> value="<?php echo $vcode; ?>"><?php echo $sector_name[$vcode]; ?></option>
											<?php
												}
											?>
										</select> --->
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<!--<label class="reportselectionlabel">User</label>&nbsp;
										<select name="ucode" id="ucode" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												foreach($user_code as $ucodes){
											?>
													<option <?php if($_POST['ucode'] == $user_code[$ucodes]) { echo 'selected'; } ?> value="<?php echo $user_code[$ucodes]; ?>"><?php echo $user_name[$ucodes]; ?></option>
											<?php
												}
											?>
										</select> ---><br/>
                                        <label for="dcno" class="reportselectionlabel">Dc. No.</label>
                                        <input type="text" name="dcno" id="dcno" class="formcontrol"value="<?php echo $dcno; ?>" style="padding:0;padding-left:2px;width:150px;" />
                                        &ensp;&ensp;
                                        <label for="search_table" class="reportselectionlabel">Search</label>
                                        <input type="text" name="search_table" id="search_table" class="formcontrol" style="padding:0;padding-left:2px;width:150px;" />
                                        &ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
							<?php }
							if(isset($_POST['submit']) == true){
								if($_POST['export'] != "exportexcel"){
									$bwtd_det_col = 0;
									?>
									<thead class="thead2" style="background-color: #98fb98;">
										<?php
										$active_flag = 1;
										echo "<th id='order_date'>S.No</th>";
										for($i = 1;$i <= $col_count;$i++){
											if(!empty($field_details[$i.":".$active_flag])){
												//echo "<br/>".$field_details[$i.":".$active_flag]."-".$i.":".$active_flag;
												if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<th id='order_date'>Date</th>"; $bwtd_det_col++; }
												else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<th id='order'>Invoice</th>"; $bwtd_det_col++; }
												else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<th id='order'>Book Invoice</th>"; $bwtd_det_col++; }
												else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<th id='order'>Customer</th>"; $bwtd_det_col++; }
												else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<th id='order'>Item</th>"; $bwtd_det_col++; }
												else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<th id='order_num'>Jals</th>"; }
												else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<th id='order_num'>Birds</th>"; }
												else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<th id='order_num'>T. Weight</th>"; }
												else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<th id='order_num'>E. Weight</th>"; }
												else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<th id='order_num'>N. Weight</th>"; }
												else if($field_details[$i.":".$active_flag] == "farm_weight"){ echo "<th id='order'>Farm Weight</th>"; }
												else if($field_details[$i.":".$active_flag] == "aweight_flag"){ echo "<th id='order_num'>Avg. Weight</th>"; }
												else if($field_details[$i.":".$active_flag] == "prate_flag"){ echo "<th id='order_num'>Paper Rate</th>"; }
												else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<th id='order_num'>Price</th>"; }
												else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<th id='order_num'>Freight</th>"; }
												else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<th id='order_num'>TCS</th>"; }
												else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<th id='order_num'>Freight</th>"; }
												else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<th id='order_num'>Discount</th>"; }
												else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<th id='order_num'>Total Amount</th>"; }
												else if($field_details[$i.":".$active_flag] == "cus_oba_flag"){ echo "<th id='order_num'>Old Balance</th>"; }
												else if($field_details[$i.":".$active_flag] == "cus_tba_flag"){ echo "<th id='order_num'>Total</th>"; }
												else if($field_details[$i.":".$active_flag] == "cus_cba_flag"){ echo "<th id='order_num'>Balance</th>"; }
												else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<th id='order'>Warehouse</th>"; }
												else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<th id='order'>Remarks</th>"; }
												else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<th id='order'>Vehicle</th>"; }
												else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<th id='order'>Driver</th>"; }
												else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<th id='order_num'>Sales</th>"; }
												else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<th id='order_num'>Receipts</th>"; }
												else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<th id='order_num'>Running Balance</th>"; }
												else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<th id='order'>User</th>"; }
												else{ }
											}
										}
										
										?>
									</thead>
									<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
									<?php
										if($prate_flag == 1 || $prate_flag == "1"){
											$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' ORDER BY `updated` ASC"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['cgroup']."@".$row['code']] = $row['new_price']; }
										}	
										if($prate_flag == 1 || $prate_flag == "1"){
											$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' ORDER BY `updated` ASC"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['cgroup']."@".$row['code']] = $row['new_price']; }
										}
										if($cus_jalsfreight_flag == 1 || $cus_jalsfreight_flag == "1"){ $jals_frt_filter = " a.freight_amount_jal as freight_amount_jal,"; } else{ $jals_frt_filter = ""; }
										if($frt_amt_flag == 1 || $frt_amt_flag == "1"){ $frt_amt_filter = " a.freight_amt as freight_amt,"; } else{ $frt_amt_filter = ""; }
										if($farm_wt_flag == 1 || $farm_wt_flag == "1"){ $farm_wt_filter = " a.farm_weight as farm_weight,"; } else{ $farm_wt_filter = ""; }

										if($users == "all"){ $user_filter2 = $user_filter3 = ""; } else{ $user_filter2 = " AND a.addedemp = '$users'"; $user_filter3 = " AND addedemp = '$users'"; }
										if($dcno == ""){ $dcno_fltr2 = $dcno_fltr3 = $dcno_fltr4 = ""; } else{ $dcno_fltr2 = " AND a.bookinvoice = '$dcno'"; $dcno_fltr3 = " AND docno = '$dcno'"; }
										$sequence = "SELECT a.date as date, a.invoice as invoice,".$jals_frt_filter."".$frt_amt_filter."".$farm_wt_filter." a.bookinvoice as bookinvoice, a.customercode as customercode, a.jals as jals, a.totalweight as totalweight, a.emptyweight as emptyweight, a.itemcode as itemcode, a.birds as birds, a.netweight as netweight, a.itemprice as itemprice, a.totalamt as totalamt, a.tcdsper as tcdsper, a.tcdsamt as tcdsamt, a.roundoff as roundoff, a.finaltotal as finaltotal, a.balance as balance, a.amtinwords as amtinwords, a.trtype as trtype, a.flag as flag, a.active as active, a.authorization as authorization, a.warehouse as warehouse, a.tdflag as tdflag, a.pdflag as pdflag, a.drivercode as drivercode, a.vehiclecode as vehiclecode, a.narration as narration, a.discounttype as discounttype, a.discountvalue as discountvalue, a.taxtype as taxtype, a.taxvalue as taxvalue, a.discountamt as discountamt, a.taxamount as taxamount, a.taxcode as taxcode, a.discountcode as discountcode, a.remarks as remarks, a.sms_sent as sms_sent, a.sms_modified as sms_modified, a.addedemp as addedemp, a.addedtime as addedtime, b.name as customername FROM `customer_sales` a, `main_contactdetails` b WHERE a.customercode = b.code AND a.date >= '$fdate' AND a.date <= '$tdate'".$user_filter2."".$dcno_fltr2;

										// $flags = " AND a.active = '1' AND a.tdflag = '0' AND a.pdflag = '0' ORDER BY a.date,a.customercode,a.addedtime,a.invoice ASC";
										$flags = " AND a.active = '1' AND a.tdflag = '0' AND a.pdflag = '0' ORDER BY a.invoice ASC";
										$sql = $sequence."".$customer_filter."".$binv_filter."".$item_filter."".$sector_filter."".$user_filter."".$flags;
										$query = mysqli_query($conn,$sql);
										$sii_count = $slc_finaltotal = $sales = $slc_freightamt = $slc_tcdsamt = $slc_roundoff = array();
										$rb_amt = $i = 0; $all_cust_codes = array();
										while($row = mysqli_fetch_assoc($query)){
											$key = $row['date']."@".$row['customercode'];
											if($old_key != $key){
												$i = 0;
											}
											$i = $i + 1; $sales[$row['date']."@".$row['customercode']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['customercode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode']."@".$row['drivercode']."@".$row['addedemp']."@".$row['freight_amt']."@".$row['farm_weight'];
											$sales_count[$key] = $i;
											$old_key = $key;

											$all_cust_codes[$row['customercode']] = $row['customercode'];

											if($sii_count[$row['invoice']] == "" || $sii_count[$row['invoice']] == NULL || $sii_count[$row['invoice']] == 0){
												$sii_count[$row['invoice']] = 1;
												if($row['freight_amount_jal'] == "" || $row['freight_amount_jal'] == NULL){ $slc_freightamt[$row['invoice']] = 0; } else{ $slc_freightamt[$row['invoice']] = (float)$row['freight_amount_jal']; }
												//if($row['freight_amount'] == "" || $row['freight_amount'] == NULL){ $slc_freightamt[$row['invoice']] = 0; } else{ $slc_freightamt[$row['invoice']] = (float)$row['freight_amount']; }
												if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){ $slc_tcdsamt[$row['invoice']] = 0.00; } else{ $slc_tcdsamt[$row['invoice']] = $row['tcdsamt']; }
												if($row['roundoff'] == "" || $row['roundoff'] == NULL){ $slc_roundoff[$row['invoice']] = 0.00; } else{ if(($row['itotal'] + $row['tcdsamt']) <= $row['finaltotal']){ $slc_roundoff[$row['invoice']] = $row['roundoff']; } else{ $slc_roundoff[$row['invoice']] = -1 *($row['roundoff']); } }
												$slc_finaltotal[$row['invoice']] = $row['finaltotal'];
											}
											else{
												$sii_count[$row['invoice']] = $sii_count[$row['invoice']] + 1;
											}
										}
										$i = 0;
										$sql = "SELECT SUM(amount) as receipt_amount,ccode,`date` FROM `customer_receipts` WHERE date BETWEEN '$fdate' AND '$tdate' $customer_filter1 $user_filter3 $dcno_fltr3 GROUP By date,ccode ORDER BY `updatedtime`";
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$key = $row['date']."@".$row['ccode'];
											$receipt_amount[$key] += (float)$row['receipt_amount'];
											$all_cust_codes[$row['ccode']] = $row['ccode'];

											$receipt_array[$key] = $row['date']."@".$row['ccode']."@".$row['receipt_amount'];

											$i = $i + 1;
											$receipt_count[$key] = $i;
										}
										
										$sql = "SELECT SUM(amount) as amount,ccode,`date` FROM `main_mortality` WHERE date BETWEEN '$fdate' AND '$tdate' $customer_filter1 $user_filter3 $dcno_fltr4 GROUP By date,ccode ORDER BY `updatedtime`";
										$query = mysqli_query($conn,$sql);
										while($row = mysqli_fetch_assoc($query)){
											$key = $row['date']."@".$row['ccode'];
											$receipt_amount[$key] += (float)$row['amount'];
											$all_cust_codes[$row['ccode']] = $row['ccode'];

											$receipt_array[$key] = $row['date']."@".$row['ccode']."@".$row['amount'];

											$i = $i + 1;
											$receipt_count[$key] = $i;
										}
										
										//Fetch Customer Outstanding Balance
										if(sizeof($all_cust_codes) > 0){
											if((int)$cob_flag == 1 || (int)$ctb_flag == 1 || (int)$ccb_flag == 1){
												$cus_list = implode("','",$all_cust_codes);
												//Purchases Outstanding Balance
												$old_inv = "";  $pinv = $ppay = $pcdn = $pccn = $preturns = $oinv = $orct = $ocdn = $occn = $omortality = $oreturns = array();
												$sql = "SELECT invoice,finaltotal,vendorcode FROM `pur_purchase` WHERE `date` < '$fdate' AND `vendorcode` IN ('$cus_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `updated`,`invoice` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['vendorcode'];
													if($old_inv != $row['invoice']){
														$pinv[$key] += (float)$row['finaltotal'];
														$old_inv = $row['invoice'];
													}
												}
												$sql = "SELECT SUM(amount) as tamt,ccode FROM `pur_payments` WHERE  `date` < '$fdate' AND `ccode` IN ('$cus_list') AND `active` = '1' GROUP BY `ccode` ORDER BY `updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['ccode'];
													$ppay[$key] += (float)$row['tamt'];
												}
												$sql = "SELECT SUM(amount) as tamt,mode,ccode FROM `main_crdrnote` WHERE  `date` < '$fdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('SCN','SDN') AND `active` = '1' GROUP BY `mode`,`ccode` ORDER BY `mode`,`updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['ccode'];
													if($row['mode'] == "SDN") { $pcdn[$key] += (float)$row['tamt']; }
													else { $pccn[$key] += (float)$row['tamt']; }
												}
												$sql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` IN ('$cus_list') AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['vcode'];
													$preturns[$key] += (float)$row['amount'];
												}
												
												//Sales Outstanding Balance
												$old_inv = "";
												$sql = "SELECT invoice,finaltotal,customercode FROM `customer_sales` WHERE `date` < '$fdate' AND `customercode` IN ('$cus_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													if($old_inv != $row['invoice']){
														$key = $row['customercode'];
														$oinv[$key] += (float)$row['finaltotal'];
														$old_inv = $row['invoice'];
													}
												}
												$sql = "SELECT SUM(amount) as tamt,ccode FROM `customer_receipts` WHERE  `date` < '$fdate' AND `ccode` IN ('$cus_list') AND `active` = '1' GROUP BY `ccode` ORDER BY `updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['ccode'];
													$orct[$key] += (float)$row['tamt'];
												}
												$sql = "SELECT SUM(amount) as tamt,mode,ccode FROM `main_crdrnote` WHERE  `date` < '$fdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode`,`ccode` ORDER BY `mode`,`updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['ccode'];
													if($row['mode'] == "CDN") { $ocdn[$key] += (float)$row['tamt']; }
													else { $occn[$key] += (float)$row['tamt']; }
												}
												$sql = "SELECT * FROM `main_mortality` WHERE `date` < '$fdate' AND `ccode` IN ('$cus_list') AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0' ORDER BY `updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['ccode'];
													$omortality[$key] += (float)$row['amount'];
												}
												$sql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` IN ('$cus_list') AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0' ORDER BY `updated` ASC";
												$query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
													$key = $row['vcode'];
													$oreturns[$key] += (float)$row['amount'];
												}
												$sql1 = "SELECT * FROM `main_contactdetails` WHERE `code` LIKE '$cuscode'"; $query = mysqli_query($conn,$sql1);
												while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; $cnameno = $row['mobileno']; $ctype = $row['contacttype']; if($row['obtype'] == "Cr"){ $obcramt = $row['obamt']; $obdramt = "0.00"; } else if($row['obtype'] == "Dr"){ $obdramt = $row['obamt']; $obcramt = "0.00"; } else{ $obdramt = $obcramt = "0.00"; } }
												
												foreach($all_cust_codes as $cusc){
													if(empty($oinv[$cusc]) || $oinv[$cusc] == ""){ $oinv[$cusc] = 0; }
													if(empty($ocdn[$cusc]) || $ocdn[$cusc] == ""){ $ocdn[$cusc] = 0; }
													if(empty($pccn[$cusc]) || $pccn[$cusc] == ""){ $pccn[$cusc] = 0; }
													if(empty($ppay[$cusc]) || $ppay[$cusc] == ""){ $ppay[$cusc] = 0; }
													if(empty($obdramt[$cusc]) || $obdramt[$cusc] == ""){ $obdramt[$cusc] = 0; }
													if(empty($preturns[$cusc]) || $preturns[$cusc] == ""){ $preturns[$cusc] = 0; }
													if(empty($pinv[$cusc]) || $pinv[$cusc] == ""){ $pinv[$cusc] = 0; }
													if(empty($pcdn[$cusc]) || $pcdn[$cusc] == ""){ $pcdn[$cusc] = 0; }
													if(empty($occn[$cusc]) || $occn[$cusc] == ""){ $occn[$cusc] = 0; }
													if(empty($orct[$cusc]) || $orct[$cusc] == ""){ $orct[$cusc] = 0; }
													if(empty($obcramt[$cusc]) || $obcramt[$cusc] == ""){ $obcramt[$cusc] = 0; }
													if(empty($oreturns[$cusc]) || $oreturns[$cusc] == ""){ $oreturns[$cusc] = 0; }
													if(empty($omortality[$cusc]) || $omortality[$cusc] == ""){ $omortality[$cusc] = 0; }
													if(empty($balance[$cusc]) || $balance[$cusc] == ""){ $balance[$cusc] = 0; }
													$ob_rcv = $ob_paid = 0;
													$ob_rcv = $oinv[$cusc] + $ocdn[$cusc] + $pccn[$cusc] + $ppay[$cusc] + $obdramt[$cusc] + $preturns[$cusc];
													$ob_paid = $pinv[$cusc] + $pcdn[$cusc] + $occn[$cusc] + $orct[$cusc] + $obcramt[$cusc] + $oreturns[$cusc] + $omortality[$cusc];
													$balance[$cusc] = $ob_rcv - $ob_paid;

													/*if($_SERVER['REMOTE_ADDR'] == "49.37.241.142"){
														echo "<br/>$ob_rcv = $oinv[$cusc] + $ocdn[$cusc] + $pccn[$cusc] + $ppay[$cusc] + $obdramt[$cusc] + $preturns[$cusc];
														<br/>$ob_paid = $pinv[$cusc] + $pcdn[$cusc] + $occn[$cusc] + $orct[$cusc] + $obcramt[$cusc] + $oreturns[$cusc] + $omortality[$cusc];
														<br/>$balance[$cusc] = $ob_rcv - $ob_paid;";
													}*/
												}
											}
										}
										
										$sno = 1;
										$fdate = strtotime($_POST['fromdate']); $tdate = strtotime($_POST['todate']); $i = $ppr_count = $ppr_amt = 0; $exi_inv = "";
										$tbcount = $tjcount = $tncount = $tot_farm_wt = $tot_net_wt = $twcount = $tecount = $tdcount = $ttcount = $tfritcount = $tacount = $ft_jfrgt = 0;
										for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
											$date_asc = date('Y-m-d', $currentDate);
											foreach($all_cust_codes as $customer_Code){
												$key1 = $date_asc."@".$customer_Code;
												$j = 0;
												$ccount = 0;
												$ccount = $sales_count[$key1];
												
												if($ccount == '' || $ccount == 0){
													if($receipt_amount[$key1] > 0){
														$ccount = 1;
													}
												}
												
											for($j = 1;$j <=$ccount;$j++){
												
												if($sales[$date_asc."@".$customer_Code."@".$j] != ""){
													$sales_details = explode("@",$sales[$date_asc."@".$customer_Code."@".$j]);
													echo "<tr>";
													$tacount = $tacount + (float)$sales_details[11];
													$key = $sales_details[3]."@".$sales_details[0];	
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

														//Customer Balance Calculations
														$cusc = $sales_details[3]; $cus_ob_amt = $cus_tb_amt = $cus_cb_amt = $cus_ramt = 0;
														if($old_key1 != $key1 ){ $cus_ramt = (float)$receipt_amount[$key1]; }
														if(empty($balance[$cusc]) || $balance[$cusc] == ""){ $balance[$cusc] = 0; }
														$cus_ob_amt = (float)$balance[$cusc];
														$cus_tb_amt = (float)$cus_ob_amt + (float)$sales_details[11];
														$cus_cb_amt = (float)$cus_tb_amt - (float)$cus_ramt;
														$balance[$cusc] = (float)$cus_cb_amt;

														echo "<td>".$sno++."</td>";
														for($i = 1;$i <= $col_count;$i++){
															if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($sales_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$sales_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$sales_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$cus_name[$sales_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$sales_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".str_replace(".00","",number_format_ind($sales_details[4]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".str_replace(".00","",number_format_ind(round($sales_details[8])))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($sales_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($sales_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($sales_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "farm_weight"){ echo "<td>".number_format_ind($sales_details[25])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){
																if($sales_details[9] > 0 && $sales_details[8] > 0){
																	echo "<td>".number_format_ind($sales_details[9] / $sales_details[8])."</td>";
																}
																else{
																	echo "<td>".number_format_ind(0)."</td>";
																}
															}
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]."@".$sales_details[7]; $ppr_count++; $ppr_amt = $ppr_amt + $prates[$prate_index]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".$sales_details[10]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td>".$sales_details[24]."</td>"; }
															//else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($sales_details[15])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cus_oba_flag"){ echo "<td>".number_format_ind($cus_ob_amt)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cus_tba_flag"){ echo "<td>".number_format_ind($cus_tb_amt)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cus_cba_flag"){ echo "<td>".number_format_ind($cus_cb_amt)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$sales_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$sales_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td>".$sales_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td>".$sales_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$sales_details[18]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<td style='text-align:left;'>".$user_name[$sales_details[23]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td>".number_format_ind($slc_freightamt[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td>".number_format_ind($slc_tcdsamt[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td>".number_format_ind($slc_finaltotal[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "dr_flag"){ 
																
																if($old_key1 != $key1 ){
																	echo "<td>".number_format_ind($receipt_amount[$key1])."</td>"; 
																}else{
																	echo "<td>".number_format_ind("0")."</td>";  
																}
																
															}
															else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".number_format_ind($rb_amt)."</td>"; }
															else{ }
														}
													}
													else{
														
														echo "<td>".$sno++."</td>";
														for($i = 1;$i <= $col_count;$i++){
															if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($sales_details[0]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".$sales_details[1]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".$sales_details[2]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$cus_name[$sales_details[3]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".$item_name[$sales_details[7]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".str_replace(".00","",number_format_ind($sales_details[4]))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".str_replace(".00","",number_format_ind(round($sales_details[8])))."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".number_format_ind($sales_details[5])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".number_format_ind($sales_details[6])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".number_format_ind($sales_details[9])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "farm_weight"){ echo "<td>".number_format_ind($sales_details[25])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "aweight_flag"){
																if(!empty($sales_details[9]) && $sales_details[9] > 0 && !empty($sales_details[8]) && $sales_details[8] > 0){
																	echo "<td>".number_format_ind($sales_details[9] / $sales_details[8])."</td>";
																}
																else{
																	echo "<td>".number_format_ind(0)."</td>";
																}
															}
															else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]; $ppr_count++; $ppr_amt = $ppr_amt + $prates[$prate_index]; echo "<td>".number_format_ind($prates[$prate_index])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".$sales_details[10]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td>".number_format_ind($sales_details[24])."</td>"; }
															//else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($sales_details[15])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".number_format_ind($sales_details[11])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cus_oba_flag"){ echo "<td>".number_format_ind(0)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cus_tba_flag"){ echo "<td>".number_format_ind(0)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "cus_cba_flag"){ echo "<td>".number_format_ind(0)."</td>"; }
															else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".$sector_name[$sales_details[16]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".$sales_details[20]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td>".$sales_details[21]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td>".$sales_details[22]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".$sales_details[18]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<td style='text-align:left;'>".$user_name[$sales_details[23]]."</td>"; }
															else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td></td>"; }
															else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td></td>"; }
															else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td>".number_format_ind($slc_finaltotal[$sales_details[1]])."</td>"; }
															else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td></td>"; }
															else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".number_format_ind($rb_amt)."</td>"; }
															else{ }
														}
													}
														
														$tot_farm_wt += (float)$sales_details[25];
														$tot_net_wt += (float)$sales_details[9];
														if($old_key1 != $key1 ){
															$tot_receipt_amt += (float)$receipt_amount[$key1];
														}
														$old_key1 = $key1;
														$tbcount = $tbcount + (float)$sales_details[8];
														$tjcount = $tjcount + (float)$sales_details[4];
														$tncount = $tncount + (float)$sales_details[9];
														$twcount = $twcount + (float)$sales_details[5];
														$tecount = $tecount + (float)$sales_details[6];
														$tdcount = $tdcount + (float)$sales_details[18];
														$ttcount = $ttcount + (float)$sales_details[19];
														$tfritcount = $tfritcount + (float)$sales_details[24];
														
													echo "</tr>";
												}
												else if($receipt_array[$key1] != ""){
													
													$receipt_details = explode("@",$receipt_array[$key1]);
													
													if(number_format_ind($receipt_details[2]) > 0){

														//Customer Balance Calculations
														$cusc = $receipt_details[1]; $cus_ob_amt = $cus_tb_amt = $cus_cb_amt = $cus_ramt = 0;
														if($old_key1 != $key1 ){ $cus_ramt = (float)$receipt_amount[$key1]; }
														if(empty($balance[$cusc]) || $balance[$cusc] == ""){ $balance[$cusc] = 0; }
														$cus_ob_amt = (float)$balance[$cusc];
														$cus_tb_amt = (float)$cus_ob_amt;
														$cus_cb_amt = (float)$cus_tb_amt - (float)$cus_ramt;
														$balance[$cusc] = (float)$cus_cb_amt;

													echo "<tr>";
													echo "<td>".$sno++."</td>";
													for($i = 1;$i <= $col_count;$i++){
														if($field_details[$i.":".$active_flag] == "date_flag"){ echo "<td>".date("d.m.Y",strtotime($receipt_details[0]))."</td>"; }
														else if($field_details[$i.":".$active_flag] == "inv_flag"){ echo "<td style='text-align:left;'>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "binv_flag"){ echo "<td style='text-align:left;'>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vendor_flag"){ echo "<td style='text-align:left;'>".$cus_name[$receipt_details[1]]."</td>"; }
														else if($field_details[$i.":".$active_flag] == "item_flag"){ echo "<td style='text-align:left;'>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "farm_weight"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "aweight_flag"){
															if($sales_details[9] > 0 && $sales_details[8] > 0){
																echo "<td>".""."</td>";
															}
															else{
																echo "<td>".""."</td>";
															}
														}
														else if($field_details[$i.":".$active_flag] == "prate_flag"){ $prate_index = "$sales_details[0]"."@".$cus_group[$sales_details[3]]."@".$sales_details[7]; $ppr_count++; $ppr_amt = $ppr_amt + $prates[$prate_index]; echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "price_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td>".""."</td>"; }
														//else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td rowspan='$sii_count[$exi_inv]'>".number_format_ind($sales_details[15])."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "cus_oba_flag"){ echo "<td>".number_format_ind($cus_ob_amt)."</td>"; }
														else if($field_details[$i.":".$active_flag] == "cus_tba_flag"){ echo "<td>".number_format_ind($cus_tb_amt)."</td>"; }
														else if($field_details[$i.":".$active_flag] == "cus_cba_flag"){ echo "<td>".number_format_ind($cus_cb_amt)."</td>"; }
														else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td style='text-align:left;'>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td style='text-align:left;'>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<td style='text-align:left;'>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td>".""."</td>"; }
														else if($field_details[$i.":".$active_flag] == "dr_flag"){ 
															
															echo "<td>".number_format_ind($receipt_details[2])."</td>";
															
														}
														else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td>".""."</td>"; }
														else{ }
													}

													$tot_receipt_amt += (float)$receipt_details[2];
													echo "</tr>";
												}

												}
												$tob_amt += (float)$cus_ob_amt;
												$ttb_amt += (float)$cus_tb_amt;
												$tcb_amt += (float)$cus_cb_amt;
											}
										}
										}
									?>
									</tbody>
									<thead>
										<tr class="foottr" style="background-color: #98fb98;">
											<td colspan="<?php echo $bwtd_det_col; ?>" align="center"><b>Total</b></td>
											<?php
											echo "<td></td>";
											for($i = 1;$i <= $col_count;$i++){
												if($field_details[$i.":".$active_flag] == "jals_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".str_replace(".00","",number_format_ind($tjcount))."</td>"; }
												else if($field_details[$i.":".$active_flag] == "birds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".str_replace(".00","",number_format_ind(round($tbcount)))."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($twcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "eweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tecount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "nweight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tot_net_wt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "farm_weight"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tot_farm_wt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "aweight_flag"){
													if($tbcount > 0){
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tncount / $tbcount)."</td>";
													}
													else{
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind(0)."</td>";
													}
												}
												else if($field_details[$i.":".$active_flag] == "prate_flag"){
													if($ppr_count > 0){
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($ppr_amt / $ppr_count)."</td>";
													}
													else{
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind(0)."</td>";
													}
												}
												else if($field_details[$i.":".$active_flag] == "price_flag"){
													if($tncount > 0){
														echo "<td style='padding: 0 5px;text-align:right;' title='$tacount--$tncount'>".number_format_ind(round(((((float)$tacount)) / $tncount),2))."</td>";
													}
													else{
														echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind(0)."</td>";
													}
													
												}
												else if($field_details[$i.":".$active_flag] == "freightamt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tfritcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "jfreight_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($ft_jfrgt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tcds_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($ft_tcds)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "discount_flag"){ echo "<td>".number_format_ind($tdcount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "tamt_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tacount)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "cus_oba_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tob_amt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "cus_tba_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($ttb_amt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "cus_cba_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tcb_amt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "sector_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "remarks_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "vehicle_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "driver_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "weighton_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tot_farm_wt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "cr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($fst_famt + $fdt_famt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "dr_flag"){ echo "<td style='padding: 0 5px;text-align:right;'>".number_format_ind($tot_receipt_amt)."</td>"; }
												else if($field_details[$i.":".$active_flag] == "rb_flag"){ echo "<td></td>"; }
												else if($field_details[$i.":".$active_flag] == "user_flag"){ echo "<td></td>"; }
												else{ }
											}
											?>
										</tr>
									</thead>
							<?php
								}
							}
							?>
						</table>
					</form>
				</div>
		</section>
		<?php if($vsign_flag == 1 || $csign_flag == 1 || $qr_img_flag == 1 || $note_flag == 1){ ?>
		<div align="center">
			<table style="width:90%">
				<tr>
					<td colspan="2"><?php if($note_flag == 1){ echo '<footer align="center" style="margin-top:50px;">'.$disclaimer.'</footer>'; } ?></td>
				</tr>
				<tr>
					<td><?php if($vsign_flag == 1){ echo '<center><br/><br/>'.$company_name.'<br/><br/>........................................</center>'; } ?></td>
					<td><?php if($csign_flag == 1){ echo '<center><br/><br/>'.$cus_name[$cname].'<br/><br/>........................................</center>'; } ?></td>
				</tr>
				<?php
					if($qr_img_flag == 1){
				?>
					<tr>
						<td colspan="2"><center><img src="../<?php echo $qr_img_path; ?>" height="150px" /></center></td>
					</tr>
				<?php
					}
				?>
			</table>
		</div>
		<?php } ?>
		<script src="../loading_page_out.js"></script>
		<script>
            function checkval(){
                var items = document.getElementById("items").value;
                if(items == "select"){
                    alert("Please select an item to fetch report");
                    return false;
                }
                else{
                    return true;
                }
            }
			function convertDate(d) {
				var p = d.split(".");
				return (p[2]+p[1]+p[0]);
			}
            function table_sort() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    asc = !asc;
                    })
                });
            }
            function table_sort3() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    asc = !asc;
                    })
                });
            }

            table_sort();
            table_sort2();
            table_sort3();
		</script>
        <script src="searchbox.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
