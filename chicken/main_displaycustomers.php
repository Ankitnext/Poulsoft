<?php
	session_start(); include "newConfig.php";
	include "xendorheadlink.php";
	$dis_link_acc = $add_link_acc = $edt_link_acc = $upd_link_acc = "";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$user_name = $_SESSION['users'];
	$user_code = $_SESSION['userid'];
	if($_GET['ccid'] == "" && $_SESSION['dispcus'] != ""){
		$cid = $_SESSION['dispcus'];
	}
	else if($_GET['ccid'] != "" && $_SESSION['dispcus'] == ""){
		$cid = $_GET['ccid']; $_SESSION['dispcus'] = $cid;
	}
	else {
		$cid = $_GET['ccid']; $_SESSION['dispcus'] = $cid;
	}
	$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` LIKE '%$cid%' AND `activate` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
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
		$la = $row['loc_access'];
	}
	$dlink = explode(",",$dlink); foreach($dlink as $dlink1){ $dis_acc[$dlink1] = $dlink1; $dis_link_acc = $dis_link_acc.",".$dlink1; }
	$alink = explode(",",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; $add_link_acc = $add_link_acc.",".$alink1; }
	$elink = explode(",",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; $edt_link_acc = $edt_link_acc.",".$elink1; }
	$ulink = explode(",",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; $upd_link_acc = $upd_link_acc.",".$ulink1; }
	$la1 = explode(",",$la); foreach($la1 as $la2){ $loc_det[$la2] = $la2; }
	if($add_acc[$gp_id."-A"] != ""){ $add_flag = 1; $add_link = $gp_link[$gp_id."-A"]; } else { $add_flag = 0; }
	if($edt_acc[$gp_id."-E"] != ""){ $edt_flag = 1; $edit_link = $gp_link[$gp_id."-E"]; } else { $edt_flag = 0; }
	if($upd_acc[$gp_id."-U"] != ""){ $upd_flag = 1; $upd_link = $gp_link[$gp_id."-U"]; } else { $upd_link = 0; }
	//echo "<script> alert('$cid'); </script>";
	//echo "<script> alert('$edit_link'); </script>";
	//echo "<script> alert('$upd_link'); </script>";
	$sql="SELECT * FROM `main_groups` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $gr_code[$row['code']] = $row['code']; $gr_name[$row['code']] = $row['description'];}
			
	$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array();
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("fixed_qty", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `fixed_qty` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Customer Min Quantity' AFTER `active`"; mysqli_query($conn,$sql); }
	
	$group = "all";
	if(isset($_POST['submit']) == true){
		$active_status = $_POST['active_status'];
		 $group = $_POST['group'];
	}
	else{
		$active_status = "1";
		// $group = "";
	}
	if($active_status == "all"){ $active_filter = ""; } else{ $active_filter = " AND `active` = '$active_status'"; }
	if($group == "all"){ $grp_filter = ""; } else{ $grp_filter = " AND `groupcode` = '$group'"; }
			
    /*Check for Table Availability*/
    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
    if(in_array("chicken_designation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_designation LIKE poulso6_admin_chickenmaster.chicken_designation;"; mysqli_query($conn,$sql1); }
    if(in_array("chicken_employee", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_employee LIKE poulso6_admin_chickenmaster.chicken_employee;"; mysqli_query($conn,$sql1); }
    if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
	if(in_array("main_areas", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_areas LIKE poulso6_admin_chickenmaster.main_areas;"; mysqli_query($conn,$sql1); }
	if(in_array("main_areagroup_map", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_areagroup_map LIKE poulso6_admin_chickenmaster.main_areagroup_map;"; mysqli_query($conn,$sql1); }
		
	
	/*Check for Column Availability*/
	$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
	if(in_array("area_code", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `area_code` VARCHAR(300) NULL DEFAULT NULL AFTER `groupcode`"; mysqli_query($conn,$sql); }
	if(in_array("sman_code", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `sman_code` VARCHAR(300) NULL DEFAULT NULL AFTER `area_code`"; mysqli_query($conn,$sql); }
	if(in_array("supr_code", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `supr_code` VARCHAR(300) NULL DEFAULT NULL AFTER `sman_code`"; mysqli_query($conn,$sql); }
	if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }
    if(in_array("aadhar_no", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `aadhar_no` VARCHAR(300) NULL DEFAULT NULL AFTER `groupcode`"; mysqli_query($conn,$sql); }
    if(in_array("pan_no", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `pan_no` VARCHAR(300) NULL DEFAULT NULL AFTER `aadhar_no`"; mysqli_query($conn,$sql); }
    if(in_array("cust_code", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `cust_code` VARCHAR(300) NULL DEFAULT NULL AFTER `pan_no`"; mysqli_query($conn,$sql); }
    //Check Import Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Customer Master' AND `field_function` LIKE 'Import Customers' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $iport_cflag = mysqli_num_rows($query);
?>
	<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<div class="row">
			<div class="col-md-6">
				<form action="main_displaycustomers.php?cid=<?php echo $cid; ?>" method="post">
					<div class="col-md-3 form-group float-left">
						<label for="active_status">Active Status</label>
						<select name="active_status" id="active_status" class="form-control select2" style="width:100px;">
							<option value="all" <?php if($active_status == "all"){ echo "selected"; } ?>>-All-</option>
							<option value="1" <?php if($active_status == "1"){ echo "selected"; } ?>>-Active-</option>
							<option value="0" <?php if($active_status == "0"){ echo "selected"; } ?>>-In-active-</option>
						</select>
					</div>
					<div class="col-md-5 form-group float-left">
						<label for="group">Group</label>
						<select name="group" id="group" class="form-control select2" style="width:200px;">
							<option value="all" <?php if($group == "all"){ echo "selected"; } ?>>-All-</option>
							<?php foreach($gr_code as $grp){ ?> <option value="<?php echo $grp; ?>" <?php if($group == $grp){ echo "selected"; } ?>> <?php echo $gr_name[$grp]; ?></option> <?php } ?>
						</select>
					</div>
					<div class="col-md-4 form-group float-left"><br/>
						<div class="input-group"><button class="btn btn-success btn-sm" name="submit" id="submit" type="submit">Submit</button></div>
					</div>
				</form>
			</div>
			<?php if($add_flag == 1){ ?>
			<div class="col-md-6"><br/>
				<div align="right" class="form-group float-right">
					<?php if((int)$iport_cflag == 1){ ?><button type="button" class="btn btn-success" id="import_page" value="chicken_import_customers1.php" onclick="add_page(this.id)" ><i class="fa-solid fa-circle-down"></i> Import</button><?php } ?>
					<button type="button" class="btn btn-warning" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
					<button type="button" class="btn btn-warning" id="addmultiplepage" value="main_addmultiplecustomers.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD Multiple</button>
				</div>
			</div>
			<?php } ?>
		</div>
		<section class="content">
			<div class="row">
				<div class="col-lg-19">
					<div class="box">
					<?php
						
						$sql = "SELECT * FROM `main_groups` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){ $group_name[$row['code']] = $row['description']; }
						
						$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ".$active_filter."".$grp_filter." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
					?>
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Name</th>
										<th>Code</th>
										<th>Phone/Mobile</th>
										<th>Type</th>
										<th>Group</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $c = 0; while($row = mysqli_fetch_assoc($query)){ $c = $c + 1; ?>
										<tr id="row_id[<?php echo $c; ?>]">
											<td><?php echo $row['name']; ?></td>
											<td><?php echo $row['code']; ?></td>
											<td><?php echo $row['mobileno']; ?></td>
											<td><?php echo $row['contacttype']; ?></td>
											<td><?php echo $group_name[$row['groupcode']]; ?></td>
											<td style="width:15%;" align="left">
											<?php
												$id = $row['id'];
												$cus_code = $row['id']."@".$row['code']."@".$c;
												if($sa == 1){
													echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
													if($row['flag'] == 0){ ?><a href='javascript:void(0)' id='<?php echo $cus_code; ?>' onclick='delete_vendordetails(this.id);'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
													if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
													else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
													if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
												}
												else if($aa == 1){
													echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
													if($row['flag'] == 0){ ?><a href='javascript:void(0)' id='<?php echo $cus_code; ?>' onclick='delete_vendordetails(this.id);'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
													if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
													else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
													if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
												}
												else {
													if($edt_flag == 1 && $row['flag'] == 0){
														echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
													}
													if($upd_flag == 1){
														if($row['flag'] == 0){ ?><a href='javascript:void(0)' id='<?php echo $cus_code; ?>' onclick='delete_vendordetails(this.id);'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
														else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
														if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else {
														echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>";
													}
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
			function delete_vendordetails(a){
				var b = a.split("@");
				var del_confirm = confirm("Are you sure to delete: "+b[1]+"--"+b[2]);
				if(del_confirm == true){
					var delete_status = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_delete_vendors.php?cus_code="+b[0];
					//window.open(url);
					var asynchronous = true;
					delete_status.open(method, url, asynchronous);
					delete_status.send();
					delete_status.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var del_flag = this.responseText;
							if(del_flag == 1){
								location.reload();
							}
							else{
								alert("Active Transactions are available for the customer \n kindly check and try again ...!");
							}
						}
					}
				}
			}
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
		</script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>