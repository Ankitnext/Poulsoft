<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;

	
	$requested_data = json_decode(file_get_contents('php://input'),true);


    
session_start();
    
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){

    include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";

	
	$dbname = $_SESSION['dbase'];
}else{

    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";

	$dbname =  $_GET['db'];
}
	
			
	$today = date("Y-m-d");
			
	$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
	
	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_grp_code[$row['code']] = $row['code']; $cus_grp_name[$row['code']] = $row['description']; }
			
	$idisplay = ''; $ndisplay = 'style="display:none;"';
			/*
			$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; $cus_code[$row['code']] = $row['code']; }
			*/
	$fromdate = $_POST['fromdate'];
	if($fromdate == ""){ $fromdate = $today; } else { $fromdate = $_POST['fromdate']; }
	$todate = date('d.m.Y', strtotime($fromdate.'+6 days'));
	$fudate = date('d.m.Y', strtotime($todate.'+1 days'));
	$tudate = date('d.m.Y', strtotime($todate.'+7 days'));
	if(isset($_POST['cgroup'])){ $cus_group = $_POST['cgroup']; } else{ $cus_group = "all"; }
?>
<?php $expoption = "displaypage"; if(isset($_POST['submit'])) { $expoption = $_POST['export']; } if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; }; ?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=cus_salesorderreport($fromdate-$todate).xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<style>
			.formcontrol {
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				padding-right: 5px;
				text-align: right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					if($dlogo_flag > 0) { ?>
						<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
					<?php }
					else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;" class="reportheaderlabel">Weekly Balance Report</label>&ensp;
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Group:</b>&nbsp;<?php if($cus_group != "all"){ echo $cus_grp_name[$cus_group]; } else{ echo "All"; } ?></label>&ensp;
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
				<form action="cus_weeklybalancereport2.php" method="post">
					<?php } else { ?>
					<form action="cus_weeklybalancereport2.php?db=<?php echo $db; ?>" method="post">
					<?php } ?>
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="18">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="cgroup" id="cgroup" class="form-control select2">
											<option value="all" <?php if($cus_group == $row['code']) { echo 'selected'; } ?>>-All-</option>
											<?php
												foreach($cus_grp_code as $cgcode){
											?>
													<option <?php if($cus_group == $cus_grp_code[$cgcode]) { echo 'selected'; } ?> value="<?php echo $cus_grp_code[$cgcode]; ?>"><?php echo $cus_grp_name[$cgcode]; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<!--<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												//foreach($cus_code as $cuscode){
											?>
													<option <?php //if($cus_group == $cus_code[$cuscode]) { echo 'selected'; } ?> value="<?php //echo $cus_code[$cuscode]; ?>"><?php //echo $cus_name[$cuscode]; ?></option>
											<?php
												//}
											?>
										</select>
									&ensp;&ensp;-->
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } if(isset($_POST['submit']) == true){ ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th>Sl.No.</th>
								<th>Customer</th>
								<th>Mobile</th>
								<th><?php echo date("d.m.Y",strtotime($fromdate))." - ".date("d.m.Y",strtotime($todate)); ?></th>
								<th>Last week Balance</th>
								<th>Demand on <?php echo date("d.m.Y",strtotime($todate)); ?></th>
								<th>Upto <?php echo date("d.m.Y",strtotime($tudate)); ?></th>
								<th>Weekly Balance</th>
								<th>Balance</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								$fromdate = date("Y-m-d",strtotime($fromdate));
								$todate = date('Y-m-d', strtotime($fromdate.'+6 days'));
								$fudate = date('Y-m-d', strtotime($todate.'+1 days'));
								$tudate = date('Y-m-d', strtotime($todate.'+7 days'));
								
								if($_POST['cgroup'] == true){ if($_POST['cgroup'] == "all"){ $gcode = ""; } else{ $gcode = " AND `groupcode` LIKE '".$_POST['cgroup']."'"; } } else{ $gcode = ""; }
								$cus_codes = array();
								$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%c%' AND `active` = '1'".$gcode." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$cus_name[$row['code']] = $row['name']; $cus_code[$row['code']] = $row['code']; $cus_mobile[$row['code']] = $row['mobileno'];
									$obtype[$row['code']] = $row['obtype']; $obamt[$row['code']] = $row['obamt'];
									if($row['obtype'] == "Cr"){ $ob_cramt[$row['code']] = $row['obamt']; $ob_dramt[$row['code']] = 0; } else{ $ob_dramt[$row['code']] = $row['obamt']; $ob_cramt[$row['code']] = 0; }
									if($cus_codes == array()){ $cus_codes = $row['code']; } else{ $cus_codes = $cus_codes."','".$row['code']; }
								}
								
								// Opening Balances
								$old_inv = "";
								$obsql = "SELECT * FROM `customer_sales` WHERE `date` < '$fromdate' AND `customercode` IN ('$cus_codes') AND `active` = '1' ORDER BY `date` ASC"; $obquery = mysqli_query($conn,$obsql);
								while($obrow = mysqli_fetch_assoc($obquery)){ if($old_inv != $obrow['invoice']){$ob_sales[$obrow['customercode']] = $ob_sales[$obrow['customercode']] + $obrow['finaltotal']; $old_inv = $obrow['invoice']; } }
								
								$obsql = "SELECT * FROM `customer_receipts` WHERE `date` < '$fromdate' AND `ccode` IN ('$cus_codes') AND `active` = '1' ORDER BY `ccode` ASC";
								$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $ob_receipt[$obrow['ccode']] = $ob_receipt[$obrow['ccode']] + $obrow['amount']; }
								
								$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fromdate' AND `ccode` IN ('$cus_codes') AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `ccode` ASC"; $obquery = mysqli_query($conn,$obsql);
								while($obrow = mysqli_fetch_assoc($obquery)){ if($obrow['mode'] == "CCN"){ $ob_ccn[$obrow['ccode']] = $ob_ccn[$obrow['ccode']] + $obrow['amount']; } else { $ob_cdn[$obrow['ccode']] = $ob_cdn[$obrow['ccode']] + $obrow['amount']; } }
								
								
								//Between dates sales 
								$old_inv = "";
								$sql = "SELECT * FROM `customer_sales` WHERE `date` >='$fromdate' AND `date` <= '$todate' AND `customercode` IN ('$cus_codes') ORDER BY `customercode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $bw_sales_amt[$row['customercode']] = $bw_sales_amt[$row['customercode']] + $row['finaltotal']; $old_inv = $row['invoice']; } }
								
								//after between dates, next 7 days receipts
								$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fudate' AND `date` <= '$tudate' AND `ccode` IN ('$cus_codes') ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ $nw_rct_amt[$row['ccode']] = $nw_rct_amt[$row['ccode']] + $row['amount']; }
								$c = $total_sales = $total_bal = $cur_cls_bal = $total_rct = $total_bw_bal = $final_total = 0;
								foreach($cus_code as $cscode){
									//OB calculations
									$ob_rcv = $ob_sales[$cscode] + $ob_cdn[$cscode] + $ob_dramt[$cscode];
									$ob_pid = $ob_receipt[$cscode] + $ob_ccn[$cscode] + $ob_cramt[$cscode];
									//echo "<br/>".$ob_sales[$cscode]."@".$ob_cdn[$cscode]."@".$ob_dramt[$cscode]."<-->".$ob_receipt[$cscode]."@".$ob_ccn[$cscode]."@".$ob_cramt[$cscode];
									$c = $c + 1;
									$final_ob_amt = $ob_rcv - $ob_pid;
									
									$total_sales = $total_sales + $bw_sales_amt[$cscode];
									$total_bal = $total_bal + $final_ob_amt;
									$cur_cls_bal = $cur_cls_bal + ($final_ob_amt + $bw_sales_amt[$cscode]);
									$total_rct = $total_rct + $nw_rct_amt[$cscode];
									$total_bw_bal = $total_bw_bal + ($bw_sales_amt[$cscode] - $nw_rct_amt[$cscode]);
									$final_total = $final_total + (($final_ob_amt + $bw_sales_amt[$cscode]) - $nw_rct_amt[$cscode]);
									echo "<tr>";
									echo "<td style='text-align:center;'>".$c."</td>";
									echo "<td style='text-align:left;'>".$cus_name[$cscode]."</td>";
									echo "<td style='text-align:left;'>".$cus_mobile[$cscode]."</td>";
									echo "<td>".number_format_ind($bw_sales_amt[$cscode])."</td>";
									echo "<td>".number_format_ind($final_ob_amt)."</td>";
									echo "<td>".number_format_ind($final_ob_amt + $bw_sales_amt[$cscode])."</td>";
									echo "<td>";
										if(number_format_ind($nw_rct_amt[$cscode]) == ".00"){ echo "0.00"; } else{ echo number_format_ind($nw_rct_amt[$cscode]); }
									echo "</td>";
									echo "<td>".number_format_ind($bw_sales_amt[$cscode] - $nw_rct_amt[$cscode])."</td>";
									echo "<td>".number_format_ind(($final_ob_amt + $bw_sales_amt[$cscode]) - $nw_rct_amt[$cscode])."</td>";
									echo "</tr>";
								}
							?>
								<tr class="foottr" style="background-color: #98fb98;">
									<td colspan="3" align="center"><b>Grand Total</b></td>
									<td><?php echo number_format_ind($total_sales); ?></td>
									<td><?php echo number_format_ind($total_bal); ?></td>
									<td><?php echo number_format_ind($cur_cls_bal); ?></td>
									<td><?php echo number_format_ind($total_rct); ?></td>
									<td><?php echo number_format_ind($total_bw_bal); ?></td>
									<td><?php echo number_format_ind($final_total); ?></td>
								</tr>
							</tbody>
						<?php } ?>
						</table>
					</form>
				</div>
		</section>
		<script>
			function fetchcustomerdetails(){
				var a = document.getElementById("cgroup").value;
				removeAllOptions(document.getElementById("cname"));
				myselect = document.getElementById("cname"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(a.match("all")){
					<?php
					$sql="SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['name']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else{
					<?php
					$sql="SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
						if(a == "<?php echo $row['groupcode']; ?>"){
							theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['name']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
						}
					<?php } ?>
				}
			}
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
