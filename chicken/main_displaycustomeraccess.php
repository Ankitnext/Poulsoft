<?php

	//main_displaycustomeraccess.php

	session_start();

	include "newConfig.php";

	include "xendorheadlink.php";

	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;

	$user_name = $_SESSION['users'];

	$user_code = $_SESSION['userid'];

	$dbase = $_SESSION['dbase'];

	if($_GET['ccid'] == "" && $_SESSION['cusacc'] != ""){

		$cid = $_SESSION['cusacc'];

	}

	else if($_GET['ccid'] != "" && $_SESSION['cusacc'] == ""){

		$cid = $_GET['ccid']; $_SESSION['cusacc'] = $cid;

	}

	else {

		$cid = $_GET['ccid']; $_SESSION['cusacc'] = $cid;

	}

	$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);

	while($row = mysqli_fetch_assoc($query)){ 

		$cus_name[$row['code']] = $row['name'];

		$cus_code[$row['code']] = $row['code'];

	}

	$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` LIKE '%$cid%' AND `activate` = '1' ORDER BY `sortorder` DESC"; $query = mysqli_query($conn,$sql);

	while($row = mysqli_fetch_assoc($query)){

		if($row['sortorder'] == "3"){

			$gp_id = $row['parentid'];

		}

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

	

	if(isset($_POST['bdates']) == true){

		$pbdates = $_POST['bdates'];

		$bdates = explode(" - ",$_POST['bdates']);

		$fdate = date("Y-m-d",strtotime($bdates[0]));

		$tdate = date("Y-m-d",strtotime($bdates[1]));

	}

	else {

		$fdate = $tdate = date("Y-m-d");

		$pbdates = date("d.m.Y")." - ".date("d.m.Y");

	}

	$utype = "NA";

	if($sa == 1){

		$utype = "S";

	}

	else if($aa == 1){

		$utype = "A";

	}

	else if($na == 1){

		$utype = "N";

	}

?>

<html>

	<body class="hold-transition skin-blue sidebar-mini">

		<div class="row" style="margin: 10px 10px 0 10px;">

			<div align="right">

				<?php if($edt_flag == 1){ ?><button type="button" class="btn btn-warning" id="editpage" value="main_editcustomeraccess_multiple.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Edit-Multiple</button><?php } ?>

				<?php if($add_flag == 1){ ?><button type="button" class="btn btn-warning" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button><?php } ?>

			</div>

		</div>

		<section class="content">

			<div class="row">

				<div class="col-lg-19">

					<div class="box">

					<?php

						$sql = "SELECT * FROM `common_customeraccess` WHERE `db_name` = '$dbase'"; $query = mysqli_query($conns,$sql);

					?>

						<div class="box-body">

							<table id="example1" class="table table-bordered table-striped">

								<thead>

									<tr>

										<th>Customer Name</th>

										<th>Mobile</th>

										<th>Accesses</th>

										<th>Status</th>

										<th>Type</th>

										<th>Action</th>

									</tr>

								</thead>

								<tbody>

								<?php

									$c = 0;

									while($row = mysqli_fetch_assoc($query)){ $c = $c + 1;

								?>

										<tr>

											<td><?php echo $cus_name[$row['ccode']]; ?></td>

											<td><?php echo $row['mobile']; ?></td>

											<td><?php 

											$scdet = "";

											$screenstwo_Array = explode(',', $row['screenstwo']);

											if($row['screens'] != ""){ if($scdet == ""){ $scdet = "Sales Order"; } else{ $scdet = $scdet.", Sales Order"; } }

											if($screenstwo_Array != null && in_array("cl", $screenstwo_Array)){ if($scdet == ""){ $scdet = "Ledger Report"; } else{ $scdet = $scdet.", Ledger Report"; } }

											if($screenstwo_Array != null && in_array("cl_per", $screenstwo_Array)){ if($scdet == ""){ $scdet = "Ledger Report New"; } else{ $scdet = $scdet.", Ledger Report New"; } }

											
											if($row['screensthree'] != ""){ if($scdet == ""){ $scdet = "Receipt Report"; } else{ $scdet = $scdet.", Receipt Report"; } }

											if($row['screensfour']!= ""){ if($scdet == ""){ $scdet = "Sales Report"; } else{ $scdet = $scdet.", Sales Report"; } }

											if($row['screensfive'] != ""){ if($scdet == ""){ $scdet = "Sales Order Report"; } else{ $scdet = $scdet.", Sales Order Report"; } }

											echo $scdet; ?></td>

											<td><?php echo $row['active_status']; ?></td>

											<td><?php echo $row['user_type']; ?></td>

											<td style="width:15%;" align="left">

												<?php

												$id = $row['id'];

												if($edt_flag == 1){

													echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";

												}

												if($upd_flag == 1){

													if($row['active_status'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }

													else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }

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

			</div>

		</section>

		<?php include "xendorfootlink.php"; ?>

		<script>

			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }

		</script>

		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>

	</body>

</html>

