<?php
	include "newConfig.php";
	include "xendorheadlink.php";
			$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
			$user_name = $_SESSION['users'];
			$user_code = $_SESSION['userid'];
			if($_GET['cid'] == "" && $_SESSION['dispcsaleo'] != ""){ $cid = $_SESSION['dispcsaleo']; } else if($_GET['cid'] != "" && $_SESSION['dispcsaleo'] == ""){ $cid = $_GET['cid']; $_SESSION['dispcsaleo'] = $cid; } else { $cid = $_GET['cid']; $_SESSION['dispcsaleo'] = $cid; }
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
			//echo "<script> alert('$upd_link'); </script>";
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
		?>
	<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Sale Orders</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Sales Orders</a></li>
				<li class="active">Display Invoice</li>
			</ol>
		</section><br/>
		<div class="row" style="margin: 10px 10px 0 10px;">
		<form action="cus_salesorder.php?cid=<?php echo $cid; ?>" method="post">
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
						$cname[$row['code']] = $row['name'];
						}
						$sql = "SELECT * FROM `item_details` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
						$description[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `salesorder` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `isDelete` = '0' ORDER BY `date`,`trnum` DESC"; $query = mysqli_query($conn,$sql);
					?>
						
						
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Date</th>
										<th>Sales Order</th>
										<th>Customer</th>
										<th>Item Description</th>
										<th>Quantity</th>
										<th>Entry Through</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								<?php $c = 0; while($row = mysqli_fetch_assoc($query)){ $c = $c + 1; ?>
										<tr>
											<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
											<td><?php echo $row['trnum']; ?></td>
											<td><?php echo $cname[$row['ccode']]; ?></td>
											<td><?php echo	$description[$row['itemcode']]; ?></td>
											<td><?php echo round($row['jals'],2)."/".round($row['birds'],2)."/".round($row['twt'],2); ?></td>
											<td><?php if($row['mflag'] == 1){ echo 'Mobile'; } else { echo 'System'; } ?></td>
											<td style="width:15%;" align="left">
											<?php
												$id = $row['trnum'];
												//$idd = $row['trnum']."@"."inv";
												//$path = "\printformatlibrary\Examples\generateinvoice.php?id=$idd";
												if($sa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														if($row['isDelete'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													//echo "<a href='$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
												else if($aa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														if($row['isDelete'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														
													}
													else {echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													//echo "<a href=$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
												}
												else {
													if($edt_flag == 1 && $row['isDelete'] == 0){
														if(strtotime($row['date']) >= strtotime($from_date)){
															echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														}
													}
													if($upd_flag == 1){
														if(strtotime($row['date']) >= strtotime($from_date)){
															if($row['isDelete'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
															
														}
														else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else { echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
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