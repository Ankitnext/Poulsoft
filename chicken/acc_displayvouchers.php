<?php
//acc_displayvouchers.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
include "xendorheadlink.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid'];

if($_GET['cid'] == "" && $_SESSION['dispvouch'] != ""){ $cid = $_SESSION['dispvouch']; } else if($_GET['cid'] != "" && $_SESSION['dispvouch'] == ""){ $cid = $_GET['cid']; $_SESSION['dispvouch'] = $cid; } else { $cid = $_GET['cid']; $_SESSION['dispvouch'] = $cid; }
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
	$_SESSION['disvfdate'] = $fdate;
	$_SESSION['disvtdate'] = $tdate;
	$_SESSION['disvpbdate'] = $pbdates;
}
else {
	$fdate = $tdate = date("Y-m-d");
	$pbdates = date("d.m.Y")." - ".date("d.m.Y");
}
if($_SESSION['disvpbdate'] != "" || $_SESSION['disvpbdate'] != NULL){ $pbdates = $_SESSION['disvpbdate']; }
if($_SESSION['disvfdate'] != "" || $_SESSION['disvfdate'] != NULL){ $fdate = date("Y-m-d",strtotime($_SESSION['disvfdate'])); }
if($_SESSION['disvtdate'] != "" || $_SESSION['disvtdate'] != NULL){ $tdate = date("Y-m-d",strtotime($_SESSION['disvtdate'])); }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$saccess = $row['supadmin_access'];
	$aaccess = $row['admin_access'];
	$naccess = $row['normal_access'];
}
$utype = "NA";
if($saccess == 1){
	$utype = "S"; $addedemp = "";
}
else if($aaccess == 1){
	$utype = "A"; $addedemp = "";
}
else if($naccess == 1){
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Vouchers' AND `field_function` LIKE 'Display all vouchers' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $ausr_aflag = mysqli_num_rows($query);
	if((int)$ausr_aflag > 0){ $addedemp = ""; } else{ $addedemp = " AND `addedemp` LIKE '$user_code'"; }
	$utype = "N"; 
}
if($utype = "S" || $utype = "A"){
	$idate = "2001-01-01";
	$from_date = date('d.m.Y', strtotime($idate));
}
else{
	$sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = 'Vouchers' OR `type` = 'all' AND `active` = '1'";
	$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $days = $row['days']; }
	if($days == ""){ $from_date = date('d.m.Y'); } else{ $from_date = date('d.m.Y', strtotime('-'.$days.' days')); }
}
$sql='SHOW COLUMNS FROM `master_itemfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("salary_voucher_wapp", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_itemfields` ADD `salary_voucher_wapp` INT(100) NOT NULL DEFAULT '0' AFTER `type`"; mysqli_query($conn,$sql); }
                                    
$sql = "SELECT * FROM `master_itemfields` WHERE `salary_voucher_wapp` = '1' AND `active` = '1'";
$query = mysqli_query($conn,$sql); $empsal_wapp = mysqli_num_rows($query);
	
$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'PDF' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
		
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="loading_screen.css">
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Vouchers</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transactions</a></li>
				<li class="active">Display Vouchers</li>
			</ol>
		</section><br/>
		<div class="row" style="margin: 10px 10px 0 10px;">
		<form action="acc_displayvouchers.php?cid=<?php echo $cid; ?>" method="post">
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
						
						$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
							$wcode[$row['code']] = $row['code'];
							$wdesc[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `acc_coa` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){
						$adesc[$row['code']] = $row['description'];
						}
						$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `tdflag` = '0' AND `pdflag` = '0'".$addedemp." ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					?>
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Date</th>
										<th>Transaction No.</th>
										<th>From CoA</th>
										<th>To CoA</th>
										<th>Doc No.</th>
										<th>Sector</th>
										<th>Amount</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								<?php $c = 0; while($row = mysqli_fetch_assoc($query)){ $c = $c + 1; ?>
										<tr>
											<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
											<td><?php echo $row['trnum']; ?></td>
											<td><?php echo $adesc[$row['fcoa']]; ?></td>
											<td><?php echo $adesc[$row['tcoa']]; ?></td>
											<td><?php echo $row['dcno']; ?></td>
											<td><?php echo $wdesc[$row['warehouse']]; ?></td>
											<td style="text-align:right;"><?php echo number_format_ind($row['amount']); ?></td>
											<td style="width:15%;" align="left">
											<?php
												$id = $row['trnum'];
												$print_link = "\printformatlibrary\Examples\acc_voucherprint.php";
												
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
													echo "<a href='$print_link?id=$id' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
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
													echo "<a href='$print_link?id=$id' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
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
													else {
														echo "<i class='fa fa-check' style='color:green;' title='approved'></i></a>";
														echo "<a href='$print_link?id=$id' target='_new'><i class='fa fa-print' style='color:black;' title='print invoice'></i></a>";
													}
												}
												if((int)$empsal_wapp > 0){
											?>
											<a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='send_wapp_direct_msg(this.id)'><i class='fa-brands fa-whatsapp' style='color:green;' title='Send WhatsApp'></i></a>
											<?php } ?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="ring"><?php echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>
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
			function send_wapp_direct_msg(trnum){
                if(trnum != ""){
					document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = 'Sending WhatsApp...';

					var file_name = '<?php echo $href; ?>';
					var inv_items = new XMLHttpRequest();
					var method = "GET";
					var url = "chicken_send_wapp_direct1.php?trnum="+trnum+"&file_name="+file_name+"&etype=voucher_salary";
					//window.open(url);
					var asynchronous = true;
					inv_items.open(method, url, asynchronous);
					inv_items.send();
					inv_items.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var cus_dt1 = this.responseText;
							if(cus_dt1 == "success"){
								alert("WhatsApp sent successfully.");
								document.getElementsByClassName("ring")[0].style.display = "none";
								document.getElementsByClassName("ring_status")[0].style.display = "none";
							}
							else{
								alert("WhatsApp sending failed.");
							}
						}
					}
				}
			}
		</script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>