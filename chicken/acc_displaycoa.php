<?php
	session_start(); include "newConfig.php";
	include "xendorheadlink.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
			$user_name = $_SESSION['users'];
			$user_code = $_SESSION['userid'];
			if($_GET['ccid'] == "" && $_SESSION['dispCoA'] != ""){
				$cid = $_SESSION['dispCoA'];
			}
			else if($_GET['ccid'] != "" && $_SESSION['dispCoA'] == ""){
				$cid = $_GET['ccid']; $_SESSION['dispCoA'] = $cid;
			}
			else {
				$cid = $_GET['ccid']; $_SESSION['dispCoA'] = $cid;
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
			
			/*Check Column Availability*/
			$sql='SHOW COLUMNS FROM `acc_vouchers`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
			while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
			if(in_array('link_trnum', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_vouchers`  ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL COMMENT ''  AFTER `trnum`;"; mysqli_query($conn,$sql); }
			if(in_array('vtype', $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_vouchers`  ADD `vtype` VARCHAR(300) NULL DEFAULT NULL COMMENT ''  AFTER `link_trnum`;"; mysqli_query($conn,$sql); }

			//Extra access
			$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'CoA Master' AND `field_function` LIKE 'Purchase: Supplier Add-On Fields Flag'"; $query = mysqli_query($conn,$sql); $cnt = mysqli_num_rows($query);
			if($cnt > 0){ } else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`field_value`,`user_access`,`flag`) VALUES ('CoA Master','Purchase: Supplier Add-On Fields Flag',NULL,'all','0');"; mysqli_query($conn,$sql); }
			
			//Extra access for Labour
			$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'acc_displaycoa.php' AND `field_function` LIKE 'Add Labour'"; $query = mysqli_query($conn,$sql); $lab_flag = mysqli_num_rows($query);
			mysqli_query($conn,$sql); 
			// if($lab_flag > 0) { echo "Labour Flag";}
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<div class="row">
			<div class="col-12" style="text-align: right; margin: 10px 10px 0 10px;">
				<?php if($lab_flag == 1){ ?>
					<button type="button1" class="btn btn-success" id="addpage1" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD Labour</button>
				<?php } ?>
				<?php if($add_flag == 1){ ?>
					<button type="button" class="btn btn-warning" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
				<?php } ?>
			</div>
		</div>
		<section class="content">
			<div class="row">
				<div class="col-lg-19">
					<div class="box">
					<?php
						
						$sql ="SELECT * FROM `acc_types`"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
							$name[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `acc_schedules` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
							$sname[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `acc_coa` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					?>
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th>Type</th>
										<th>Schedule</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $c = 0; while($row = mysqli_fetch_assoc($query)){ $c = $c + 1; ?>
									<tr>
										<td><?php echo $row['code']; ?></td>
										<td><?php echo $row['description']; ?></td>
										<td><?php echo $name[$row['type']]; ?></td>
										<td><?php echo $sname[$row['schedules']]; ?></td>
										<td style="width:15%;" align="left">
											<?php
												$id = $row['id'];
												$id1 = $row['id']."@".$row['description'];
												if($sa == 1){
													echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
													if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id1; ?>' value='<?php echo $id1; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
													if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
													else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
													if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
												}
												else if($aa == 1){
													echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
													if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id1; ?>' value='<?php echo $id1; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
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
														if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id1; ?>' value='<?php echo $id1; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
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
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
			function checkdelete(a){
				var a1 = a.split("@");
				var b = "<?php echo $upd_link.'?page=delete&id='; ?>"+a1[0];
				var c = confirm("are you sure you want to delete the COA Details: "+a1[1]+" ?");
				if(c == true){
					window.location.href = b;
				}
				else{ }
				}
		</script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>