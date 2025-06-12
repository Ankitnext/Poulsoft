<?php
	//chicken_display_pursale1.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
	if(!isset($_SESSION)){ session_start(); } include "newConfig.php";
	$user_name = $_SESSION['users']; $user_code = $_SESSION['userid'];
	include "xendorheadlink.php";

    if(!empty($_SESSION['pursale1'])){ $gp_id = $cid = $_SESSION['pursale1']; }
    else{
        $sql = "SELECT *  FROM `main_linkdetails` WHERE `href` LIKE '%$href%' AND `activate` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $gp_id = $cid = $_SESSION['pursale1'] = $row['childid']; }
    }

	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $sup_code = $sup_name = array();
	while($row = mysqli_fetch_assoc($query)){
		if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; } else{ }
		if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
	}
	$cus_list = implode("','",$cus_code);

	$sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `activate` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $gp_link[$row['childid']] = $row['href']; }

	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$dlink = $row['displayaccess']; $alink = $row['addaccess']; $elink = $row['editaccess']; $ulink = $row['otheraccess'];
		$saccess = $row['supadmin_access']; $aaccess = $row['admin_access']; $naccess = $row['normal_access']; $la = $row['loc_access'];
	}
	$utype = "NA"; if($saccess == 1){ $utype = "S"; } else if($aaccess == 1){ $utype = "A"; } else if($naccess == 1){ $utype = "N"; } else{ $utype = "N"; }
	//echo $alink;
	$dlink = explode(",",$dlink); foreach($dlink as $dlink1){ $dis_acc[$dlink1] = $dlink1; }
	$alink = explode(",",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; }
	$elink = explode(",",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; }
	$ulink = explode(",",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; }
	$la1 = explode(",",$la); foreach($la1 as $la2){ $loc_det[$la2] = $la2; }

	if($add_acc[$gp_id."-A"] != ""){ $add_flag = 1; $add_link = $gp_link[$gp_id."-A"]; } else { $add_flag = 0; }
	if($edt_acc[$gp_id."-E"] != ""){ $edt_flag = 1; $edit_link = $gp_link[$gp_id."-E"]; } else { $edt_flag = 0; }
	if($upd_acc[$gp_id."-U"] != ""){ $upd_flag = 1; $upd_link = $gp_link[$gp_id."-U"]; } else { $upd_link = 0; }

	if($utype = "S" || $utype = "A"){ $idate = "2001-01-01"; $from_date = date('d.m.Y', strtotime($idate)); }
	else{
		$sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = 'Sales' OR `type` = 'all' AND `active` = '1' ORDER BY `type` ASC"; $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $days = $row['days']; }
		if($days == ""){ $from_date = date('d.m.Y'); } else{ $from_date = date('d.m.Y', strtotime('-'.$days.' days')); }
	}
    $cnamess = ""; $tslocs = $cid."-locs";
	if(isset($_POST['bdates']) == true){
		$pbdates = $_POST['bdates'];
		$cnamess = $_POST['cnames'];
		$bdates = explode(" - ",$_POST['bdates']);
		$fdate = date("Y-m-d",strtotime($bdates[0]));
		$tdate = date("Y-m-d",strtotime($bdates[1]));
		$_SESSION['ps1fdate'] = $fdate;
		$_SESSION['ps1tdate'] = $tdate;
		$_SESSION['ps1pbdate'] = $pbdates;
		$_SESSION[$tslocs] = $cnamess;
	}
	else {
		$fdate = $tdate = date("Y-m-d"); $cnamess = "all";
		$cnamess = $_POST['cnames'];
		$pbdates = date("d.m.Y")." - ".date("d.m.Y");
		if(!empty($_SESSION['ps1fdate'])){ $fdate = $_SESSION['ps1fdate']; }
		if(!empty($_SESSION['ps1tdate'])){ $tdate = $_SESSION['ps1tdate']; }
		if(!empty($_SESSION['ps1pbdate'])){ $pbdates = $_SESSION['ps1pbdate']; }
		if(!empty($_SESSION[$tslocs])){ $cnamess = $_SESSION[$tslocs]; }
	}
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Purchase-Sale Invoices</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Purchase-Sales</a></li>
				<li class="active">Display Purchase-sales</li>
			</ol>
		</section><br/>
		<div class="row" style="margin: 10px 10px 0 10px;">
		<form action="<?php echo $href."?cid=".$cid; ?>" method="post">
			<div align="left" class="col-md-5">
				<div class="input-group"><div class="input-group-addon">Date: </div><input type="text" class="form-control pull-right" name="bdates" id="reservation" value="<?php echo $pbdates; ?>"></div>
			</div>
			<div class="col-md-1">
				<div class="input-group"><div class="input-group-addon">Customer: </div>
				<select name="cnames" id="cnames" class="form-control select2" style="width: 150px;"><option value="all" style="display: flex;justify-content: center;">-Select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cc; ?>" <?php if($cc == $cnamess){ echo "selected";} ?>><?php echo $cus_name[$cc]; ?></option><?php } ?></select></div>
			</div>
			<div align="right" class="col-md-2">
				<div class="input-group"><button class="btn btn-success btn-sm" name="submit" id="submit" type="submit">Submit</button></div>
			</div>
		</form>
		<div align="right">
			<?php if($edt_flag == 1){ ?><button type="button" class="btn btn-info" id="editpage" value="chicken_edit_pursale1m.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Edit-Multiple</button><?php } ?>
			<?php if($add_flag == 1){ ?><button type="button" class="btn btn-warning" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button><?php } ?>
			
		</div>
		</div>
		<section class="content">
			<div class="row">
				<div class="col-lg-19">
					<div class="box">
					<?php
                        $sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                        if(in_array("supbrh_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `supbrh_code` VARCHAR(300) NULL DEFAULT NULL AFTER `vendorcode`"; mysqli_query($conn,$sql); }
                        
						$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC";
                        $query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
						while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

						$sql = "SELECT * FROM `item_details` ORDER BY `description` ASC";
                        $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
						while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

						$sql = "SELECT * FROM `chicken_supplier_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
						$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $br_name = array();
						while($row = mysqli_fetch_assoc($query)){
							 $br_name[$row['code']] = $row['description'];
						 }

						$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trtype` = 'PST' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
						$query = mysqli_query($conn,$sql); $link_trnum = $link_vname = array();
						while($row = mysqli_fetch_assoc($query)){ $link_trnum[$row['link_trnum']] = $row['link_trnum']; }

						if(sizeof($link_trnum) > 0){
							$tno_list = implode("','", $link_trnum);
							$sql = "SELECT * FROM `pur_purchase` WHERE `invoice` IN ('$tno_list') AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
							$query = mysqli_query($conn,$sql); 
							while($row = mysqli_fetch_assoc($query)){ $link_vname[$row['invoice']] = $ven_name[$row['vendorcode']]; $link_brname[$row['invoice']] = $br_name[$row['supbrh_code']];  }
	
						}

						$cname_fltr = ""; if($cnamess == "all"){ $cname_fltr = "AND `customercode` IN ('$cus_list')";} else { $cname_fltr = "AND `customercode` = '$cnamess'";}

						$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cname_fltr." AND `trtype` = 'PST' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
						$query = mysqli_query($conn,$sql);
					?>
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Date</th>
										<th>Supplier</th>
										<th>Branch</th>
										<th>P. Invoice</th>
										<th>Customer</th>
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
											<td><?php echo $link_vname[$row['link_trnum']]; ?></td>
											<td><?php echo $link_brname[$row['link_trnum']]; ?></td>
											<td><?php echo $row['link_trnum']; ?></td>
											<td><?php echo $ven_name[$row['customercode']]; ?></td>
											<td><?php echo $row['invoice']; ?></td>
											<td><?php echo $item_name[$row['itemcode']]; ?></td>
											<td><?php echo $row['netweight']; ?></td>
											<td><?php echo $row['itemprice']; ?></td>
											<td><?php echo $row['finaltotal']; ?></td>
											<td style="width:15%;" align="left">
											<?php
												$id = $row['invoice'];
												$path = "\printformatlibrary\Examples\generateinvoice.php?id=$id";
												$path5 = "\printformatlibrary\Examples\generateinvoice5.php?id=$id";
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
													echo "<a href='$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
													echo "<a href='$path5' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='print invoice'></i></a>";
												}
												else if($aa == 1){
													if(strtotime($row['date']) >= strtotime($from_date)){
														echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>";
														if($row['flag'] == 0){ ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-close' style='color:red;' title='delete'></i></a> <?php }
														if($row['active'] == 0){ echo "<a href='$upd_link?id=$id&page=activate'><i class='fa fa-play' style='color:blue;' title='activate'></i></a>"; }
														else { echo "<a href='$upd_link?id=$id&page=pause'><i class='fa fa-pause' style='color:blue;' title='deactivate'></i></a>"; }
														if($row['flag'] == 0){ echo "<a href='$upd_link?id=$id&page=authorize'><i class='fa fa-bell' style='color:red;' title='approve'></i></a>"; }
														else {echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													}
													else {echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>"; }
													echo "<a href=$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
													echo "<a href='$path5' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='print invoice'></i></a>";
												}
												else {
													if($edt_flag == 1){ echo "<a href='$edit_link?id=$id'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>"; }
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
													echo "<a href='$path' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
													echo "<a href='$path5' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='print invoice'></i></a>";
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
			function add_page(a){
				var b = document.getElementById(a).value; window.location.href = b;
			}
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