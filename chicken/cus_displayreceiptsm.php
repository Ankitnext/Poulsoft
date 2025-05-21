<?php
	include "newConfig.php";
	include "number_format_ind.php";
	include "xendorheadlink.php";
	$dis_link_acc = $add_link_acc = $edt_link_acc = $upd_link_acc = "";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$user_name = $_SESSION['users'];
	$user_code = $_SESSION['userid'];
	if($_GET['cid'] == "" && $_SESSION['dispcrctsm'] != ""){ $cid = $_SESSION['dispcrctsm']; } else if($_GET['cid'] != "" && $_SESSION['dispcrctsm'] == ""){ $cid = $_GET['cid']; $_SESSION['dispcrctsm'] = $cid; } else { $cid = $_GET['cid']; $_SESSION['dispcrctsm'] = $cid; }
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
	$dlink = explode(",",$dlink); foreach($dlink as $dlink1){ $dis_acc[$dlink1] = $dlink1; $dis_link_acc = $dis_link_acc.",".$dlink1; }
	$alink = explode(",",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; $add_link_acc = $add_link_acc.",".$alink1; }
	$elink = explode(",",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; $edt_link_acc = $edt_link_acc.",".$elink1; }
	$ulink = explode(",",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; $upd_link_acc = $upd_link_acc.",".$ulink1; }
	if($add_acc[$gp_id."-A"] != ""){ $add_flag = 1; $add_link = $gp_link[$gp_id."-A"]; } else { $add_flag = 0; }
	if($edt_acc[$gp_id."-E"] != ""){ $edt_flag = 1; $edit_link = $gp_link[$gp_id."-E"]; } else { $edt_flag = 0; }
	if($upd_acc[$gp_id."-U"] != ""){ $upd_flag = 1; $upd_link = $gp_link[$gp_id."-U"]; } else { $upd_link = 0; }
	//echo "<script> alert('$upd_link'); </script>";

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
		$sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = 'Receipt' OR `type` = 'all' AND `active` = '1'";
		$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $days = $row['days']; }
		if($days == ""){ $from_date = date('d.m.Y'); } else{ $from_date = date('d.m.Y', strtotime('-'.$days.' days')); }
	}

	if(isset($_POST['bdates']) == true){
		$pbdates = $_POST['bdates'];
		$bdates = explode(" - ",$_POST['bdates']);
		$fdate = date("Y-m-d",strtotime($bdates[0]));
		$tdate = date("Y-m-d",strtotime($bdates[1]));
		$_SESSION['cfrctsm1'] = $fdate;
		$_SESSION['ctrctsm1'] = $tdate;
		$_SESSION['cbrctsm1'] = $pbdates;
	}
	else {
		$fdate = $tdate = date("Y-m-d");
		$pbdates = date("d.m.Y")." - ".date("d.m.Y");
		if(!empty($_SESSION['cfrctsm1'])){ $fdate = $_SESSION['cfrctsm1']; }
		if(!empty($_SESSION['ctrctsm1'])){ $tdate = $_SESSION['ctrctsm1']; }
		if(!empty($_SESSION['cbrctsm1'])){ $pbdates = $_SESSION['cbrctsm1']; }
	}
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Simple Sales Receipts</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Sales</a></li>
				<li class="active">Display Receipts</li>
			</ol>
		</section><br/>
		<div class="row" style="margin: 10px 10px 0 10px;">
		<form action="cus_displayreceiptsm.php?cid=<?php echo $cid; ?>" method="post">
			<div align="left" class="col-md-6">
				<div class="input-group"><div class="input-group-addon">Date: </div><input type="text" class="form-control pull-right" name="bdates" id="reservation" value="<?php echo $pbdates; ?>"></div>
			</div>
			<div align="left" class="col-md-2">
				<div class="input-group"><button class="btn btn-success btn-sm" name="submit" id="submit" type="submit">Submit</button></div>
			</div>
		</form>
		<?php if($add_flag == 1){ ?>
		<div align="right">
			<button type="button" class="btn btn-warning" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
		</div>
		<?php } ?>
		</div>
		<section class="content">
			<div class="row">
				<div class="col-lg-19">
					<div class="box">
					<?php
                        /*Check for Table Availability*/
                        $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
                        $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
                        if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_broiler_broilermaster.extra_access;"; mysqli_query($conn,$sql1); }
                        
                        //Fetch Column From CoA Table
                        $sql='SHOW COLUMNS FROM `customer_receipts`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array('ccn_trnum', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_receipts`  ADD `ccn_trnum` VARCHAR(100) NULL DEFAULT NULL AFTER `amtinwords`;"; mysqli_query($conn,$sql); }
                        if(in_array('discount_amt', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_receipts`  ADD `discount_amt` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `ccn_trnum`;"; mysqli_query($conn,$sql); }
                         
						$cuscode = $whcode = "";
						$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'".$cgroup_codes." ORDER BY `id` DESC";
						$query = mysqli_query($conn,$sql); $cus_code = array();
						while($row = mysqli_fetch_assoc($query)){ $cname[$row['code']] = $row['name']; $cus_code[$row['code']] = $row['code']; }
						$cus_list = implode("','",$cus_code);
						if(sizeof($cus_code) > 0){ $customercodes = " AND `ccode` IN ('$cus_list')"; } else{ $customercodes = ""; }
						
						$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank')ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){ $cdesc[$row['code']] = $row['description']; }

						$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_codes." ORDER BY `description` ASC";
						$query = mysqli_query($conn,$sql); $sector_alist = array();
						while($row = mysqli_fetch_assoc($query)){ $sector_alist[$row['code']] = $row['code']; }
						$sec_list = implode("','",$sector_alist);
						if(sizeof($sector_alist) > 0){ $whcodes = " AND `warehouse` IN ('$sec_list')"; } else{ $whcodes = ""; }
						
						$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$customercodes."".$whcodes." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
						$query = mysqli_query($conn,$sql); //
					?>
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Date</th>
										<th>Transaction No.</th>
										<th>Customer</th>
										<th>Doc No.</th>
										<th>Mode</th>
										<th>amount</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								<?php $c = 0; while($row = mysqli_fetch_assoc($query)){ $c = $c + 1; ?>
										<tr>
											<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
											<td><?php echo $row['trnum']; ?></td>
											<td><?php echo $cname[$row['ccode']]; ?></td>
											<td><?php echo $row['docno']; ?></td>
											<td><?php echo $cdesc[$row['method']]; ?></td>
											<td style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
											<td style="width:15%;" align="left">
											<?php
												$id = $row['trnum'];
												if($sa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
														else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
														if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													echo "<a href='printformatlibrary\Examples\cus_receiptdetails.php?id=$id' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
												else if($aa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
														else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
														if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													echo "<a href='printformatlibrary\Examples\cus_receiptdetails.php?id=$id' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
												else {
													if($edt_flag == 1 && $row['flag'] == 0){
														if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														}
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													if($upd_flag == 1){
														if(strtotime($row['date']) >= strtotime($from_date)){
															if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
															if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
															else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
															if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
															else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
														}
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													echo "<a href='printformatlibrary\Examples\cus_receiptdetails.php?id=$id' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
											?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
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