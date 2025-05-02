<?php
//main_displaymortality.php
	include "newConfig.php";
	include "xendorheadlink.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$user_name = $_SESSION['users'];
	$user_code = $_SESSION['userid'];
	if($_GET['cid'] == "" && $_SESSION['dispmort'] != ""){ $cid = $_SESSION['dispmort']; } else if($_GET['cid'] != "" && $_SESSION['dispmort'] == ""){ $cid = $_GET['cid']; $_SESSION['dispmort'] = $cid; } else { $cid = $_GET['cid']; $_SESSION['dispmort'] = $cid; }
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
		$la = $row['loc_access'];
	}
	//echo $alink;
	$dlink = explode(",",$dlink); foreach($dlink as $dlink1){ $dis_acc[$dlink1] = $dlink1; $dis_link_acc = $dis_link_acc.",".$dlink1; }
	$alink = explode(",",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; $add_link_acc = $add_link_acc.",".$alink1; }
	$elink = explode(",",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; $edt_link_acc = $edt_link_acc.",".$elink1; }
	$ulink = explode(",",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; $upd_link_acc = $upd_link_acc.",".$ulink1; }
	$la1 = explode(",",$la); foreach($la1 as $la2){ $loc_det[$la2] = $la2; }
	if($add_acc[$gp_id."-A"] != ""){ $add_flag = 1; $add_link = $gp_link[$gp_id."-A"]; } else { $add_flag = 0; }
	if($edt_acc[$gp_id."-E"] != ""){ $edt_flag = 1; $edit_link = $gp_link[$gp_id."-E"]; } else { $edt_flag = 0; }
	if($upd_acc[$gp_id."-U"] != ""){ $upd_flag = 1; $upd_link = $gp_link[$gp_id."-U"]; } else { $upd_link = 0; }
	
	if(isset($_POST['bdates']) == true){
		$pbdates = $_POST['bdates'];
		$bdates = explode(" - ",$_POST['bdates']);
		$fdate = date("Y-m-d",strtotime($bdates[0]));
		$tdate = date("Y-m-d",strtotime($bdates[1]));
		
		$_SESSION['dmortfdate'] = $fdate;
		$_SESSION['dmorttdate'] = $tdate;
		$_SESSION['dmortpbdates'] = $pbdates;
	}
	else {
		$fdate = $tdate = date("Y-m-d");
		$pbdates = date("d.m.Y")." - ".date("d.m.Y");
	}
	if($_SESSION['dmortfdate'] != ""){ $fdate = $_SESSION['dmortfdate']; $tdate = $_SESSION['dmorttdate']; $pbdates = $_SESSION['dmortpbdates']; }
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
	/*Fetch Column Availability*/
	$sql='SHOW COLUMNS FROM `main_mortality`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("warehouse", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_mortality` ADD `warehouse` VARCHAR(300) NULL DEFAULT NULL AFTER `amount`"; mysqli_query($conn,$sql); }
	
?>
<html>
	<head>
		<style>
			.swal2-popup {
				font-size: 1.3rem !important;
				//font-family: Georgia, serif;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Mortality</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Mortality</a></li>
				<li class="active">Display Invoice</li>
			</ol>
		</section><br/>
		<div class="row" style="margin: 10px 10px 0 10px;">
		<form action="main_displaymortality.php?cid=<?php echo $cid; ?>" method="post">
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
						
						$sql = "SELECT * FROM `main_contactdetails` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
						$ven_name[$row['code']] = $row['name'];
						}
						$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
						$item_name[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
							$sector_name[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `dflag` = '0' ORDER BY `date`,`code` DESC"; $query = mysqli_query($conn,$sql);
					?>
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Date</th>
										<th>Trnum</th>
										<th>Mortality From</th>
										<th>Customer / Warehouse</th>
										<th>Item</th>
										<th>Birds</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								<?php $c = 0; while($row = mysqli_fetch_assoc($query)){ $c = $c + 1; ?>
										<tr id="<?php echo "tblrow[".$c."]"; ?>">
											<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
											<td><?php echo $row['code']; ?></td>
											<td><?php if($row['mtype'] == "sector"){ echo "Warehouse"; } else if($row['mtype'] == "supplier"){ echo "Supplier"; } else{ echo "Customer"; } ?></td>
											<td><?php if($row['mtype'] == "sector"){ echo $sector_name[$row['ccode']]; } else{ echo $ven_name[$row['ccode']]; } ?></td>
											<td><?php echo $item_name[$row['itemcode']]; ?></td>
											<td><?php echo $row['birds']; ?></td>
											<td><?php echo $row['quantity']; ?></td>
											<td><?php echo $row['price']; ?></td>
											<td><?php echo $row['amount']; ?></td>
											<td style="width:15%;" align="left">
											<?php
												$id = $row['code']; $updateid = $row['code']."@".$c;
												if($sa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														if($row['flag'] == 0 && $row['active'] == 1 && $row['flag'] == 0){ ?>
															<a href='<?php echo $edit_link."?id=".$id; ?>'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>
															<a href='javascript:void(0)' id='<?php echo $updateid; ?>' value='<?php echo $updateid; ?>' onclick='updatetrnum(this.id)'><i class='fa fa-bell' style='color:blue;' title='Authorize'></i></a>
															<a href='javascript:void(0)' id='<?php echo $updateid; ?>' value='<?php echo $updateid; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a>
														<?php
														}
														else{
															echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i>";
														}
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i>"; }
													//echo "<a href='$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
												else if($aa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														if($row['flag'] == 0 && $row['active'] == 1 && $row['flag'] == 0){ ?>
															<a href='<?php echo $edit_link."?id=".$id; ?>'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>
															<a href='javascript:void(0)' id='<?php echo $updateid; ?>' value='<?php echo $updateid; ?>' onclick='updatetrnum(this.id)'><i class='fa fa-bell' style='color:blue;' title='Authorize'></i></a>
															<a href='javascript:void(0)' id='<?php echo $updateid; ?>' value='<?php echo $updateid; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a>
														<?php
														}
														else{
															echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i>";
														}
													}
													else {echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i>"; }
													//echo "<a href=$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
												else {
													if($edt_flag == 1 && $row['dflag'] == 0 && $row['flag'] == 0 && $row['active'] == 1){
														if(strtotime($row['date']) >= strtotime($from_date)){
															echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														}
													}
													if($upd_flag == 1){
														if(strtotime($row['date']) >= strtotime($from_date)){
															if($row['flag'] == 0 && $row['active'] == 1 && $row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $updateid; ?>' value='<?php echo $updateid; ?>' onclick='updatetrnum(this.id)'><i class='fa fa-bell' style='color:blue;' title='Authorize'></i></a> <?php }
															if($row['flag'] == 0 && $row['active'] == 1 && $row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $updateid; ?>' value='<?php echo $updateid; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
															
														}
														else { echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i>"; }
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i>"; }
													//echo "<a href='$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
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
				var b = a.split("@");
				Swal.fire({
					title: 'Are you sure?',
					text: "Please confirm to delete the transaction: "+b[0],
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, delete it!'
				}).then((result) => {
					if (result.isConfirmed){
						var tdsper = new XMLHttpRequest();
						var method = "GET";
						var url = "main_updatemortality.php?type=delete&code="+b[0];
						var asynchronous = true;
						tdsper.open(method, url, asynchronous);
						tdsper.send();
						tdsper.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								var c = this.responseText;
								if(c == "" || c == "failed"){
									Swal.fire(
										'Error while deleting!',
										'Kindly check the transaction and try again.',
										'warning'
									);
								}
								else {
									document.getElementById('tblrow['+b[1]+']').remove();
									Swal.fire(
										'Deleted!',
										'Your Transaction has been deleted.',
										'success'
									);
								}
							}
						}
					}
				})
			}
			function updatetrnum(a){
				var b = a.split("@");
				Swal.fire({
					title: 'Are you sure?',
					text: "Please confirm to Authorize the transaction: "+b[0],
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, Authorize it!'
				}).then((result) => {
					if (result.isConfirmed){
						var tdsper = new XMLHttpRequest();
						var method = "GET";
						var url = "main_updatemortality.php?type=authorize&code="+b[0];
						var asynchronous = true;
						tdsper.open(method, url, asynchronous);
						tdsper.send();
						tdsper.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								var c = this.responseText;
								if(c == "" || c == "failed"){
									Swal.fire(
										'Error while Authorizing!',
										'Kindly check the transaction and try again.',
										'warning'
									);
								}
								else {
									document.getElementById('tblrow['+b[1]+']').cells[9].innerHTML = "<i class='fa fa-check' style='color:green;' title='Authorized'></i>";
									Swal.fire(
										'Authorized!',
										'Your Transaction has been Authorized.',
										'success'
									);
								}
							}
						}
					}
				})
			}
		</script>
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>