<?php
	session_start(); include "newConfig.php";
	include "xendorheadlink.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$user_name = $_SESSION['users'];
	$user_code = $_SESSION['userid'];
	if($_GET['cid'] == "" && $_SESSION['dispcmsale'] != ""){ $cid = $_SESSION['dispcmsale']; } else if($_GET['cid'] != "" && $_SESSION['dispcmsale'] == ""){ $cid = $_GET['cid']; $_SESSION['dispcmsale'] = $cid; } else { $cid = $_GET['cid']; $_SESSION['dispcmsale'] = $cid; }
	$sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `activate` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$gp_id = $row['parentid'];
		$gc_id[$row['childid']] = $row['childid'];
		$gp_name[$row['childid']] = $row['name'];
		$gp_link[$row['childid']] = $row['href'];
	}
	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$dlink = $row['displayaccess'];
		$alink = $row['addaccess'];
		$elink = $row['editaccess'];
		$ulink = $row['otheraccess'];
		$sa = $row['supadmin_access'];
		$aa = $row['admin_access'];
		$na = $row['normal_access'];
		$cgroup_access = $row['cgroup_access']; $loc_access = $row['loc_access'];
	}
	$sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$emp_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ }
	if($loc_access == "all" || $loc_access == "" || $loc_access == NULL){
		$warehouse_codes = "";
	}
	else{
		$whs_code = "";
		$crp_codes = explode(",",$loc_access);
		foreach($crp_codes as $whs){
			if($whs_code == ""){
				$whs_code = $whs;
			}
			else{
				$whs_code = $whs_code."','".$whs;
			}
		}
		if($whs_code != ""){
			$warehouse_codes = " AND `code` IN ('$whs_code')";
		}
		else{
			$warehouse_codes = "";
		}
	}
	if($cgroup_access == "all" || $cgroup_access == "" || $cgroup_access == NULL){
		$cgroup_codes = "";
	}
	else{
		$crp_code = "";
		$crp_codes = explode(",",$cgroup_access);
		foreach($crp_codes as $cgrps){
			if($crp_code == ""){
				$crp_code = $cgrps;
			}
			else{
				$crp_code = $crp_code."','".$cgrps;
			}
		}
		if($crp_code != ""){
			$cgroup_codes = " AND `groupcode` IN ('$crp_code')";
		}
		else{
			$cgroup_codes = "";
		}
	}
	//echo $alink;
	$dlink = explode(",",$dlink); foreach($dlink as $dlink1){ $dis_acc[$dlink1] = $dlink1; $dis_link_acc = $dis_link_acc.",".$dlink1; }
	$alink = explode(",",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; $add_link_acc = $add_link_acc.",".$alink1; }
	$elink = explode(",",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; $edt_link_acc = $edt_link_acc.",".$elink1; }
	$ulink = explode(",",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; $upd_link_acc = $upd_link_acc.",".$ulink1; }
	if($add_acc[$gp_id."-A"] != ""){ $add_flag = 1; $add_link = $gp_link[$gp_id."-A"]; } else { $add_flag = 0; }
	if($edt_acc[$gp_id."-E"] != ""){ $edt_flag = 1; $edit_link = $gp_link[$gp_id."-E"]; } else { $edt_flag = 0; }
	if($upd_acc[$gp_id."-U"] != ""){ $upd_flag = 1; $upd_link = $gp_link[$gp_id."-U"]; } else { $upd_link = 0; }
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $print3_flag = $row['print3_flag']; }
	if($_POST['dateselection'] ==""){
		$date = date("m/d/Y");
	}
	else {
		$date = date("m/d/Y",strtotime($_POST['dateselection']));
	}
	if(isset($_POST['bdates']) == true){
		$pbdates = $_POST['bdates'];
		$bdates = explode(" - ",$_POST['bdates']);
		$fdate = date("Y-m-d",strtotime($bdates[0]));
		$tdate = date("Y-m-d",strtotime($bdates[1]));
		$_SESSION['dispsfdate'] = $fdate;
		$_SESSION['dispstdate'] = $tdate;
		$_SESSION['dispsbdate'] = $pbdates;
	}
	else {
		$fdate = $tdate = date("Y-m-d");
		$pbdates = date("d.m.Y")." - ".date("d.m.Y");
	}
	if($_SESSION['dispsbdate'] != "" || $_SESSION['dispsbdate'] != NULL){ $pbdates = $_SESSION['dispsbdate']; }
	if($_SESSION['dispsfdate'] != "" || $_SESSION['dispsfdate'] != NULL){ $fdate = date("Y-m-d",strtotime($_SESSION['dispsfdate'])); }
	if($_SESSION['dispstdate'] != "" || $_SESSION['dispstdate'] != NULL){ $tdate = date("Y-m-d",strtotime($_SESSION['dispstdate'])); }
	
	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$user_code'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$saccess = $row['supadmin_access'];
		$aaccess = $row['admin_access'];
		$naccess = $row['normal_access'];
	}
	$utype = "NA";
	if($saccess == 1){
		$utype = "S";
	}
	else if($aaccess == 1){
		$utype = "A";
	}
	else if($naccess == 1){
		$utype = "N";
	}
	if($utype = "S" || $utype = "A"){
		$idate = "2001-01-01";
		$from_date = date('d.m.Y', strtotime($idate));
	}
	else{
		$sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = 'Sales' OR `type` = 'all' AND `active` = '1' ORDER BY `type` ASC";
		$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $days = $row['days']; }
		if($days == ""){ $from_date = date('d.m.Y'); } else{ $from_date = date('d.m.Y', strtotime('-'.$days.' days')); }
	}
	$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name;
	$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'extra_access';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
	if($tcount > 0){ } else{ $sql1 = "CREATE TABLE $database_name.extra_access LIKE vpspoulsoft_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
	$sql1 = "SELECT * FROM `extra_access` WHERE `field_name` = 'Multi-Sale Invoices' AND `field_function` = 'Print Format' AND `user_access` = 'all'";
	$query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1); $printformat_id = 1;
	if($tcount > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $printformat_id = $row1['flag']; } }
	else{ $sql1 = "INSERT INTO `extra_access` (`field_name`,`field_function`,`user_access`,`flag`) VALUES ('Multi-Sale Invoices','Print Format','all','1');"; mysqli_query($conn,$sql1); }
	if($printformat_id == ''){
		$printformat_id = 1;
	}
	$sql = "SELECT * FROM `master_chiken_sale_prints` WHERE id = $printformat_id";
	$query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$print_link = $row['link'];
		$print_firstparam = $row['first_param'];
	}
	//Check Column Availability
	$sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL AFTER `invoice`"; mysqli_query($conn,$sql); }
	if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
	if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }

	//Check Column Availability
	$sql='SHOW COLUMNS FROM `main_crdrnote`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_crdrnote` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL AFTER `trnum`"; mysqli_query($conn,$sql); }

	/*Check for Table Availability*/
	$database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $etn_val = array(); $i = 0;
	$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $etn_val[$i] = $row1[$table_head]; $i++; }
	if(in_array("Item_wise_jbirds_count", $etn_val, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.Item_wise_jbirds_count LIKE poulso6_admin_chickenmaster.Item_wise_jbirds_count;"; mysqli_query($conn,$sql1); }

?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Multi-Sale Invoices</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Multi-Sales</a></li>
				<li class="active">Display Multi-sales</li>
			</ol>
		</section><br/>
		<div class="row" style="margin: 10px 10px 0 10px;">
		<form action="cus_displaymultisales.php?cid=<?php echo $cid; ?>" method="post">
			<div align="left" class="col-md-6">
				<div class="input-group"><div class="input-group-addon">Date: </div><input type="text" class="form-control pull-right" name="bdates" id="reservation" value="<?php echo $pbdates; ?>"></div>
			</div>
			<div align="left" class="col-md-2">
				<div class="input-group"><button class="btn btn-success btn-sm" name="submit" id="submit" type="submit">Submit</button></div>
			</div>
		</form>
		<div align="right">
			<?php if($edt_flag == 1){ ?><button type="button" class="btn btn-info" id="editpage" value="cus_editmultisales.php" onClick="add_page(this.id)" ><i class="fa fa-align-left"></i> Edit Multiple</button><?php } ?>
			<?php if($add_flag == 1){ ?><button type="button" class="btn btn-warning" id="addpage" value="<?php echo $add_link; ?>" onClick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button><?php } ?>
		</div>
		</div>
		<section class="content">
			<div class="row">
				<div class="col-lg-19">
					<div class="box">
					<?php
						$cuscode = $whcode = "";
						$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'".$cgroup_codes." ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
						$cname[$row['code']] = $row['name'];
							if($cuscode == ""){
								$cuscode = $row['code'];
							}
							else{
								$cuscode = $cuscode."','".$row['code'];
							}
						}
						if($cuscode != ""){
							$customercodes = " AND `customercode` IN ('$cuscode')";
						}
						else{
							$customercodes = "";
						}
						//echo "<br/>".
						$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Multiple Sales' AND `active` = '1' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){ $p3inch_flag = $row['print_3inch']; } if($p3inch_flag == 0 || $p3inch_flag == ""){ $p3inch_flag = 0; }
						$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
						$description[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Warehouse"){ if($branches == ""){ $branches = $row['code']; } else{ $branches = $branches."','".$row['code']; } } }
						$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_codes." AND `type` IN ('$branches') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
							$wcode[$row['code']] = $row['code'];
							$wdesc[$row['code']] = $row['description'];
							if($whcode == ""){
								$whcode = $row['code'];
							}
							else{
								$whcode = $whcode."','".$row['code'];
							}
						}
						if($whcode != ""){
							$whcodes = " AND `warehouse` IN ('$whcode')";
						}
						else{
							$whcodes = "";
						}
						$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$customercodes."".$whcodes." AND `link_trnum` IS NULL ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); // AND `trtype` = 'multiplesales-1' AND `trlink` = 'cus_displaymultisales.php'
					?>
						
						
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Date</th>
										<th>Customer</th>
										<th>Dc. No.</th>
										<th>Invoice</th>
										<th>Item Description</th>
										<th>Item Quantities</th>
										<th>Item Prices</th>
										<th>Amount</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								<?php $c = 0; while($row = mysqli_fetch_assoc($query)){ $c = $c + 1; ?>
										<tr>
											<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
											<td><?php echo $cname[$row['customercode']]; ?></td>
											<td><?php echo $row['bookinvoice']; ?></td>
											<td><?php echo $row['invoice']; ?></td>
											<td><?php echo	$description[$row['itemcode']]; ?></td>
											<td><?php echo $row['netweight']; ?></td>
											<td><?php echo $row['itemprice']; ?></td>
											<td><?php echo $row['finaltotal']; ?></td>
											<td style="width:15%;" align="left">
											<?php
												if($print_firstparam == 'date'){
													$id = $row['date']."@".$row['customercode'];
												}else{
													$id = $row['invoice'];
												}
												$del_id = $row['invoice'];
												$inv_trnum = $row['invoice'];
												$path = "\printformatlibrary\Examples".$print_link."?id=$id@inv";
												$path2 = "\printformatlibrary\Examples\generateinvoice2.php?id=$id@inv";
												
												$path3 = "\printformatlibrary\Examples\salesinvoice2.php?id=$id@inv";
												$p3_path = "\printformatlibrary\Examples\salesinvoice4.php?id=$id@inv";
												$daysalekey = $row['date']."@".$row['customercode']."@"."inv";
												$daysalespath = "\printformatlibrary\Examples\cus_daysalesinvoices.php?id=$daysalekey";
												$daysalespath2 = "\printformatlibrary\Examples\cus_daysalesinvoices2.php?id=$daysalekey";
												$daysalespath3 = "\printformatlibrary\Examples\generateinvoice_common2.php?id=$id@inv";
												$daysalespath4 = "\printformatlibrary/Examples/generateinvoice2.php?id=$id@inv";
												$tpath = "\cus_thermalprint3inchformat.php?id=$id";
												if($sa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$inv_trnum'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $del_id; ?>' value='<?php echo $del_id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
														else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
														if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													echo "<a href='$path' target='_BLANK'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
													echo "<a href='$daysalespath' target='_BLANK'><i class='fa fa-print' style='color:green;' title='Day Sales'></i></a>";
													if($printformat_id == 1){
														echo "<a href='$path2' target='_BLANK'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
														
														echo "<a href='$path3' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='print invoice'></i></a>";
														echo "<a href='$tpath' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='print invoice'></i></a>";
														echo "<a href='$daysalespath' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath2' target='_BLANK'><i class='fa fa-print' style='color:gray;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath3' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath4' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='Day Sales'></i></a>";
													}
												}
												else if($aa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$inv_trnum'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $del_id; ?>' value='<?php echo $del_id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
														else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
														if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
														else {echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else {echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													echo "<a href=$path' target='_BLANK'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
													echo "<a href='$daysalespath' target='_BLANK'><i class='fa fa-print' style='color:green;' title='Day Sales'></i></a>";
													echo "<a href='$daysalespath4' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='Day Sales'></i></a>";
													if($printformat_id == 1){
														echo "<a href=$path2' target='_BLANK'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
														
														echo "<a href=$path3' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='print invoice'></i></a>";
														echo "<a href='$daysalespath' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath2' target='_BLANK'><i class='fa fa-print' style='color:gray;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath3' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath4' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='Day Sales'></i></a>";
													}
													if($p3inch_flag == 1){ echo "<a href='$tpath' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='print invoice'></i></a>"; }
												}
												else {
													if($edt_flag == 1 && $row['flag'] == 0){
														if(strtotime($row['date']) >= strtotime($from_date)){
															echo "<a href='$edit_link?id=$inv_trnum'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														}
													}
													if($upd_flag == 1){
														if(strtotime($row['date']) >= strtotime($from_date)){
															if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $del_id; ?>' value='<?php echo $del_id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
															if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
															else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
															if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
															else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
														}
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													echo "<a href='$path' target='_BLANK'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
													echo "<a href='$daysalespath' target='_BLANK'><i class='fa fa-print' style='color:green;' title='Day Sales'></i></a>";
													echo "<a href='$daysalespath4' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='Day Sales'></i></a>";
													if($printformat_id == 1){
														echo "<a href='$path2' target='_BLANK'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
														
														echo "<a href='$path3' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='print invoice'></i></a>";
														echo "<a href='$daysalespath' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath2' target='_BLANK'><i class='fa fa-print' style='color:gray;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath3' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='Day Sales'></i></a>";
														echo "<a href='$daysalespath4' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='Day Sales'></i></a>";
													}
													if($p3inch_flag == 1){ echo "<a href='$tpath' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='print invoice'></i></a>"; }
												}
												if($print3_flag == 1){ echo "<a href='$p3_path' target='_BLANK'><i class='fa fa-print' style='color:green;' title='print invoice'></i></a>"; echo "<a href='$daysalespath4' target='_BLANK'><i class='fa fa-print' style='color:orange;' title='Day Sales'></i></a>"; }
											?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<?php //echo $path; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php include "xendorfootlink.php"; ?>
		<script>
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
			function checkdelete(a){
				var b = "<?php echo $upd_link.'?page=delete&id='; ?>"+a;
				var c = confirm("are you sure you want to delete the transaction "+a+" ?");
				if(c == true){
					window.location.href = b;
				}
				else{ }
			}
		</script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>