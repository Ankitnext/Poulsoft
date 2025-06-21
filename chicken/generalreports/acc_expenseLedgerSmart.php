<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
		
	$requested_data = json_decode(file_get_contents('php://input'),true);

	if(!isset($_SESSION)){ session_start(); }
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; } else { $db = ''; }
if($db == ''){
	include "../config.php";
	// include "header_head.php";
	include "number_format_ind.php";
	
}else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    // include "header_head.php";
}
	
			
	$today = date("Y-m-d"); $coa_cat = "";
	$sql = "SELECT * FROM `acc_category` WHERE `code`='CAT-0008' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		if($coa_cat == ""){
			$coa_cat = $row['code'];
		}
		else{
			$coa_cat = $coa_cat."','".$row['code'];
		}
	}
	$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `type` IN ('COA-0003') AND `categories` IN ('CAT-0008') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $allCoA = "";
	while($row = mysqli_fetch_assoc($query)){ $coaname[$row['code']] = $row['description']; $coacode[$row['code']] = $row['code']; if($allCoA == "") { $allCoA = $row['code']; } else { $allCoA = $allCoA."','".$row['code']; } }
	
	// Logo Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $coaname[$row['code']] = $row['description']; }
	
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $whname[$row['code']] = $row['description']; $whcode[$row['code']] = $row['code']; }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cpname[$row['code']] = $row['name']; $cpcode[$row['code']] = $row['code']; }
	$fromdate = $_POST['fromdate']; $todate = $_POST['todate']; $pcoa = $_POST['coa']; $pwhname = $_POST['whname'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	if($pcoa == ""){ $pcoa = "all"; } else { $pcoa = $_POST['coa']; }
	if($pwhname == ""){ $pwhname = "all"; } else { $pwhname = $_POST['whname']; }
		
	$exoption = "displaypage"; $tr_type = "all";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; if($excel_type == "exportexcel"){ $exoption = "displaypage"; } else{ $exoption = $_POST['export']; } } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$tr_type = $_POST['tr_type'];
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_coa = $_POST['coa']; $exl_whname = $_POST['whname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_sname =  $exl_coa = $exl_whname = "all";
	}
	$url = "../PHPExcel/Examples/acc_expenseLedgerSmart-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&coa=".$exl_coa."&whname=".$exl_whname;
?>
<html>
	<head>
		<title>Expense Ledger</title>
        <?php include "header_head.php"; ?>
		<link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;				
			}
			.thead2,.tbody1 {
				padding: 1px;
				font-size: 12px;
			}
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
				text-align: left;
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
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Expense Ledger</h3>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label><br/>
						<label class="reportheaderlabel"><b style="color: green;">CoA:</b>&nbsp;<?php if($pcoa == "all") { echo $pcoa; } else { echo $coaname[$pcoa]; } ?></label><br/>
						<label class="reportheaderlabel"><b style="color: green;">Warehouse:</b>&nbsp;<?php if($pwhname == "all") { echo $pwhname; } else { echo $whname[$pwhname]; }?></label>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
					<form action="acc_expenseLedgerSmart.php" method="post" onSubmit="return checkval()">
					<?php } else { ?>
					<form action="acc_expenseLedgerSmart.php?db=<?php echo $db; ?>" method="post" onSubmit="return checkval()">
					<?php } ?>
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<!--<td style='visibility:hidden;'></td>-->
									<td colspan="16">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">CoA</label>&nbsp;
										<select name="coa" id="coa" class="form-control select2">
											<option value="all" <?php if($pcoa == "all") { echo 'selected'; } ?> >-All-</option>
											<?php
												foreach($coacode as $accode){
											?>
													<option <?php if($pcoa == $coacode[$accode]) { echo 'selected'; } ?> value="<?php echo $coacode[$accode]; ?>"><?php echo $coaname[$accode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="whname" id="whname" class="form-control select2">
											<option value="all" <?php if($pwhname == "all") { echo 'selected'; } ?> >-All-</option>
											<?php
												foreach($whcode as $seccode){
											?>
													<option <?php if($pwhname == $whcode[$seccode]) { echo 'selected'; } ?> value="<?php echo $whcode[$seccode]; ?>"><?php echo $whname[$seccode]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Transaction Type</label>&nbsp;
										<select name="tr_type" id="tr_type" class="form-control select2">
											<option value="all" <?php if($tr_type == "all") { echo 'selected'; } ?> >-All-</option>
											<option value="pv" <?php if($tr_type == "pv") { echo 'selected'; } ?> >Payments</option>
											<option value="rv" <?php if($tr_type == "rv") { echo 'selected'; } ?> >Receipt</option>
											<option value="jv" <?php if($tr_type == "jv") { echo 'selected'; } ?> >Journal</option>
											
										</select>&ensp;&ensp;
										
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
						<?php }
						if(isset($_POST['submit']) == true){
							$sub_coa = $_POST['coa'];
							$sub_whn = $_POST['whname'];
							$prx_fltr = ""; if($tr_type != "all"){ $prx_fltr = " AND `prefix` = '$tr_type'"; }
							//echo "<script> alert('$sub_coa'); </script>";
							//echo $allCoA;
							if($_POST['coa'] == "all"){
								$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
								$tdate = date("Y-m-d",strtotime($_POST['todate']));
								$coa = $allCoA;
								if($_POST['whname'] == "all"){ $wname = ""; } else{ $wname = "AND `warehouse` = '".$_POST['whname']."'"; }
								
								$c = $d = $e = $f = $g = 0;
								
								$bt_fpv_amt = $bt_frv_amt = $bt_fjv_amt = $bt_foth_vou_amt = $bt_tpv_amt = $bt_trv_amt = $bt_tjv_amt = $bt_toth_vou_amt = array();

								$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$prx_fltr." AND `active` = '1' AND `fcoa` IN ('$coa')".$wname." ORDER BY `fcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){
										$c = $c + 1;
										$bt_fpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['fcoa'];
									}
									else if($row['prefix'] == "RV"){
										$d = $d + 1;
										$bt_frv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['fcoa'];
									}
									else if($row['prefix'] == "JV"){
										$e = $e + 1;
										$bt_fjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['fcoa'];
									}
									else {
										$f = $f + 1;
										$bt_foth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['fcoa'];
									}
								}
								$c = $d = $e = $f = $g = 0;
								$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$prx_fltr." AND `active` = '1' AND `tcoa` IN ('$coa')".$wname." ORDER BY `tcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){
										$c = $c + 1;
										$bt_tpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['tcoa'];
									}
									else if($row['prefix'] == "RV"){
										$d = $d + 1;
										$bt_trv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['tcoa'];
									}
									else if($row['prefix'] == "JV"){
										$e = $e + 1;
										$bt_tjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['tcoa'];
									}
									else {
										$f = $f + 1;
										$bt_toth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks']."@".$row['tcoa'];
									}
								}
							?>
								<thead class="thead2" style="background-color: #98fb98;">
									<th>Sl No.</th>
									<th>Date</th>
									<th>CoA</th>
									<th>Transaction No</th>
									<th>Transaction Type</th>
									<th>Doc. No.</th>
									<th>Paid From/To</th>
									<th>From Warehouse</th>
									<th>Narrations</th>
									<th>Debit</th>
									<th>Credit</th>
								</thead>
								<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
									<?php
										$fdate = strtotime($fdate);
										$tdate = strtotime($tdate);
										$bt_paid = $bt_received = $c = 0; $sl = 1;
										for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
											$date_asc = date('Y-m-d', $currentDate);
											$ccount = sizeof($bt_fpv_amt);
											
											for($i = 1;$i <=$ccount;$i++){
												if($bt_fpv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_fpv_val = explode("@",$bt_fpv_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_fpv_val[1]))."</td>";
													echo "<td>".$coaname[$bt_fpv_val[7]]."</td>";
													echo "<td>".$bt_fpv_val[0]."</td>";
													echo "<td>Payment Voucher</td>";
													echo "<td>".$bt_fpv_val[3]."</td>";
													echo "<td>".$coaname[$bt_fpv_val[2]]."</td>";
													echo "<td>".$whname[$bt_fpv_val[5]]."</td>";
													echo "<td>".$bt_fpv_val[6]."</td>";
													$bt_paid = $bt_paid + $bt_fpv_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_fpv_val[4])."</td>";
													echo "<td></td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											
											$ccount = sizeof($bt_frv_amt);

											for($i = 1;$i <=$ccount;$i++){
												if($bt_frv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_frv_val = explode("@",$bt_frv_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_frv_val[1]))."</td>";
													echo "<td>".$coaname[$bt_frv_val[7]]."</td>";
													echo "<td>".$bt_frv_val[0]."</td>";
													echo "<td>Receipt Voucher</td>";
													echo "<td>".$bt_frv_val[3]."</td>";
													echo "<td>".$coaname[$bt_frv_val[2]]."</td>";
													echo "<td>".$whname[$bt_frv_val[5]]."</td>";
													echo "<td>".$bt_frv_val[6]."</td>";
													$bt_paid = $bt_paid + $bt_frv_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_frv_val[4])."</td>";
													echo "<td></td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											
											$ccount = sizeof($bt_fjv_amt);

											for($i = 1;$i <=$ccount;$i++){
												if($bt_fjv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_fjv_val = explode("@",$bt_fjv_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_fjv_val[1]))."</td>";
													echo "<td>".$coaname[$bt_fjv_val[7]]."</td>";
													echo "<td>".$bt_fjv_val[0]."</td>";
													echo "<td>Journal Voucher</td>";
													echo "<td>".$bt_fjv_val[3]."</td>";
													echo "<td>".$coaname[$bt_fjv_val[2]]."</td>";
													echo "<td>".$whname[$bt_fjv_val[5]]."</td>";
													echo "<td>".$bt_fjv_val[6]."</td>";
													$bt_paid = $bt_paid + $bt_fjv_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_fjv_val[4])."</td>";
													echo "<td></td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											
											$ccount = sizeof($bt_foth_vou_amt);

											for($i = 1;$i <=$ccount;$i++){
												if($bt_foth_vou_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_foth_vou_val = explode("@",$bt_foth_vou_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_foth_vou_val[1]))."</td>";
													echo "<td>".$coaname[$bt_foth_vou_val[7]]."</td>";
													echo "<td>".$bt_foth_vou_val[0]."</td>";
													echo "<td>OTH VOC</td>";
													echo "<td>".$bt_foth_vou_val[3]."</td>";
													echo "<td>".$coaname[$bt_foth_vou_val[2]]."</td>";
													echo "<td>".$whname[$bt_foth_vou_val[5]]."</td>";
													echo "<td>".$bt_foth_vou_val[6]."</td>";
													$bt_paid = $bt_paid + $bt_foth_vou_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_foth_vou_val[4])."</td>";
													echo "<td></td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											
											$ccount = sizeof($bt_tpv_amt);

											for($i = 1;$i <=$ccount;$i++){
												if($bt_tpv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_tpv_val = explode("@",$bt_tpv_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_tpv_val[1]))."</td>";
													echo "<td>".$coaname[$bt_tpv_val[7]]."</td>";
													echo "<td>".$bt_tpv_val[0]."</td>";
													echo "<td>Payment Voucher</td>";
													echo "<td>".$bt_tpv_val[3]."</td>";
													echo "<td>".$coaname[$bt_tpv_val[2]]."</td>";
													echo "<td>".$whname[$bt_tpv_val[5]]."</td>";
													echo "<td>".$bt_tpv_val[6]."</td>";
													echo "<td></td>";
													$bt_received = $bt_received + $bt_tpv_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_tpv_val[4])."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											
											$ccount = sizeof($bt_trv_amt);

											for($i = 1;$i <=$ccount;$i++){
												if($bt_trv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_trv_val = explode("@",$bt_trv_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_trv_val[1]))."</td>";
													echo "<td>".$coaname[$bt_trv_val[7]]."</td>";
													echo "<td>".$bt_trv_val[0]."</td>";
													echo "<td>Receipt Voucher</td>";
													echo "<td>".$bt_trv_val[3]."</td>";
													echo "<td>".$coaname[$bt_trv_val[2]]."</td>";
													echo "<td>".$whname[$bt_trv_val[5]]."</td>";
													echo "<td>".$bt_trv_val[6]."</td>";
													echo "<td></td>";
													$bt_received = $bt_received + $bt_trv_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_trv_val[4])."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											
											$ccount = sizeof($bt_tjv_amt);

											for($i = 1;$i <=$ccount;$i++){
												if($bt_tjv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_tjv_val = explode("@",$bt_tjv_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_tjv_val[1]))."</td>";
													echo "<td>".$coaname[$bt_tjv_val[7]]."</td>";
													echo "<td>".$bt_tjv_val[0]."</td>";
													echo "<td>Journal Voucher</td>";
													echo "<td>".$bt_tjv_val[3]."</td>";
													echo "<td>".$coaname[$bt_tjv_val[2]]."</td>";
													echo "<td>".$whname[$bt_tjv_val[5]]."</td>";
													echo "<td>".$bt_tjv_val[6]."</td>";
													echo "<td></td>";
													$bt_received = $bt_received + $bt_tjv_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_tjv_val[4])."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											
											$ccount = sizeof($bt_toth_vou_amt);

											for($i = 1;$i <=$ccount;$i++){
												if($bt_toth_vou_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_toth_vou_val = explode("@",$bt_toth_vou_amt[$date_asc."@".$i]);
													echo "<tr style='text-align:left;'>";
													echo "<td>".$sl++."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_toth_vou_val[1]))."</td>";
													echo "<td>".$coaname[$bt_toth_vou_val[7]]."</td>";
													echo "<td>".$bt_toth_vou_val[0]."</td>";
													echo "<td>OTH VOC</td>";
													echo "<td>".$bt_toth_vou_val[3]."</td>";
													echo "<td>".$coaname[$bt_toth_vou_val[2]]."</td>";
													echo "<td>".$whname[$bt_toth_vou_val[5]]."</td>";
													echo "<td>".$bt_toth_vou_val[6]."</td>";
													echo "<td></td>";
													$bt_received = $bt_received + $bt_toth_vou_val[4];
													echo "<td style='text-align:right;'>".number_format_ind($bt_toth_vou_val[4])."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
										}
									?>
								</tbody>
								<thead style="background-color: #98fb98;">
									<tr class="foottr">
										<td colspan="9" align="center"><b>Between Days Total</b></td>
										<td style='padding-right: 5px;text-align:right;' align="right"><b><?php echo number_format_ind($bt_paid); ?></b></td>
										<td style='padding-right: 5px;text-align:right;' align="right"><b><?php echo number_format_ind($bt_received); ?></b></td>
									</tr>
									<tr class="foottr">
										<td colspan="9" align="center"><b>Closing Balance</b></td>
										<td colspan="2" style='padding-right: 5px;text-align:right;' align="right"><b><?php echo number_format_ind(($pre_received - $pre_paid)+($bt_received - $bt_paid)); ?></b></td>
									</tr>
								</thead>
							<?php
								
							}
							else if($_POST['coa'] != "all" && $_POST['whname'] == "all"){
								$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
								$tdate = date("Y-m-d",strtotime($_POST['todate']));
								if($_POST['coa'] == "all"){ $coa = $allCoA; } else{ $coa = $_POST['coa']; } $wname = $_POST['whname'];
								/*$sql = "SELECT SUM(amount) as tamt FROM `pur_payments` WHERE `date` < '$fdate' AND `active` = '1' AND `method` IN ('$coa') ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ $pre_pur_pay_amt = $row['tamt']; }
								$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE `date` < '$fdate' AND `active` = '1' AND `method` IN ('$coa') ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ $pre_cus_rct_amt = $row['tamt']; }
								$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE `date` < '$fdate' AND `active` = '1' AND `coa` IN ('$coa') GROUP BY `mode` ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['mode'] == "CCN"){ $pre_ccn_amt = $row['tamt']; }
									else if($row['mode'] == "CDN"){ $pre_cdn_amt = $row['tamt']; }
									else if($row['mode'] == "SCN"){ $pre_scn_amt = $row['tamt']; }
									else if($row['mode'] == "SDN"){ $pre_sdn_amt = $row['tamt']; }
									else { $pre_oth_amt = $row['tamt']; }
								}*/
								$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate'".$prx_fltr." AND `active` = '1' AND `fcoa` IN ('$coa') GROUP BY `prefix` ORDER BY `fcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){ $pre_fpv_amt = $row['tamt']; }
									else if($row['prefix'] == "RV"){ $pre_frv_amt = $row['tamt']; }
									else if($row['prefix'] == "JV"){ $pre_fjv_amt = $row['tamt']; }
									else { $pre_foth_vou_amt = $row['tamt']; }
								}
								$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate'".$prx_fltr." AND `active` = '1' AND `tcoa` IN ('$coa') GROUP BY `prefix` ORDER BY `tcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){ $pre_tpv_amt = $row['tamt']; }
									else if($row['prefix'] == "RV"){ $pre_trv_amt = $row['tamt']; }
									else if($row['prefix'] == "JV"){ $pre_tjv_amt = $row['tamt']; }
									else { $pre_toth_vou_amt = $row['tamt']; }
								}
								//echo number_format_ind($pre_pur_pay_amt)."".number_format_ind($pre_cdn_amt)."".number_format_ind($pre_sdn_amt)."".number_format_ind($pre_fpv_amt)."".number_format_ind($pre_frv_amt)."".number_format_ind($pre_fjv_amt)."".number_format_ind($pre_foth_vou_amt);
								$pre_paid = $pre_pur_pay_amt + $pre_cdn_amt + $pre_sdn_amt + $pre_fpv_amt + $pre_frv_amt + $pre_fjv_amt + $pre_foth_vou_amt;
								$pre_received = $pre_cus_rct_amt + $pre_ccn_amt + $pre_scn_amt + $pre_tpv_amt + $pre_trv_amt + $pre_tjv_amt + $pre_toth_vou_amt;
								if($pre_paid > $pre_received){
									$pending_pay = $pre_paid - $pre_received;
									$closing_amt = $pre_received - $pre_paid;
								}
								else {
									$closing_amt = $pending_rct = $pre_received - $pre_paid;
								}
								$c = 0;
								/*$sql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `method` IN ('$coa') ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$c = $c + 1;
									$bt_pur_pay_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
								}
								$c = 0;
								$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `method` IN ('$coa') ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$c = $c + 1;
									$bt_cus_rct_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
								}
								$c = $d = $e = $f = $g = 0;
								$sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `coa` IN ('$coa') ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['mode'] == "CCN"){
										$c = $c + 1;
										$bt_ccn_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['mode'] == "CDN"){
										$d = $d + 1;
										$bt_cdn_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['mode'] == "SCN"){
										$e = $e + 1;
										$bt_scn_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['mode'] == "SDN"){
										$f = $f + 1;
										$bt_sdn_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else {
										$g = $g + 1;
										$bt_oth_amt[$row['date']."@".$g] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
								}*/
								$c = $d = $e = $f = $g = 0;
								$bt_fpv_amt = array();$bt_frv_amt=array();$bt_fjv_amt = array();$bt_foth_vou_amt = array();
								$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$prx_fltr." AND `active` = '1' AND `fcoa` IN ('$coa') ORDER BY `fcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){
										$c = $c + 1;
										$bt_fpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "RV"){
										$d = $d + 1;
										$bt_frv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "JV"){
										$e = $e + 1;
										$bt_fjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else {
										$f = $f + 1;
										$bt_foth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
								}
								$c = $d = $e = $f = $g = 0;
								$bt_tpv_amt = array();$bt_trv_amt=array();$bt_tjv_amt = array();$bt_toth_vou_amt = array();
								$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$prx_fltr." AND `active` = '1' AND `tcoa` IN ('$coa') ORDER BY `tcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){
										$c = $c + 1;
										$bt_tpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "RV"){
										$d = $d + 1;
										$bt_trv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "JV"){
										$e = $e + 1;
										$bt_tjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else {
										$f = $f + 1;
										$bt_toth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
								}
						?>
								<thead class="thead2" style="background-color: #98fb98;">
									<th>Sl.No.</th>
									<th>Date</th>
									<th>Transaction No</th>
									<th>Transaction Type</th>
									<th>Doc. No.</th>
									<th>Paid From/To</th>
									<th>From Warehouse</th>
									<!--<th>Paid/Received</th>
									<th>Cheque No</th>
									<th>Cheque Date</th>-->
									<th>Remarks</th>
									<th>Paid</th>
									<th>Received</th>
									<th>Running Balance</th>
								</thead>
								
								<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
									<tr>
										<td colspan="2"><b>Previous Balance</b></td>
										<td colspan="6"></td>
										<td><?php echo number_format_ind($pre_paid); ?></td>
										<td><?php echo number_format_ind($pre_received); ?></td>
										<td><?php echo number_format_ind($closing_amt); ?></td>
									</tr>
									<?php
										$fdate = strtotime($fdate);
										$tdate = strtotime($tdate);
										$bt_paid = $bt_received = $c = 0;
										for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
											$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']
											."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
											$date_asc = date('Y-m-d', $currentDate); 
											/*$ccount = sizeof($bt_pur_pay_amt); 
											for($i = 1;$i <=$ccount;$i++){
												if($bt_pur_pay_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_pur_pay_val = explode("@",$bt_pur_pay_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_pur_pay_val[1]))."</td>";
													echo "<td>".$bt_pur_pay_val[0]."</td>";
													echo "<td>PMT</td>";
													echo "<td>".$bt_pur_pay_val[3]."</td>";
													echo "<td>".$whname[$bt_pur_pay_val[8]]."</td>";
													echo "<td>".$cpname[$bt_pur_pay_val[2]]."</td>";
													echo "<td>".$bt_pur_pay_val[5]."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_pur_pay_val[4]))."</td>";
													echo "<td>".$bt_pur_pay_val[9]."</td>";
													echo "<td>".number_format_ind($bt_pur_pay_val[6])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_pur_pay_val[6];
													$closing_amt = $closing_amt - $bt_pur_pay_val[6];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_cdn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_cdn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_cdn_val = explode("@",$bt_cdn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_cdn_val[1]))."</td>";
													echo "<td>".$bt_cdn_val[0]."</td>";
													echo "<td>CDN</td>";
													echo "<td>".$bt_cdn_val[3]."</td>";
													echo "<td>".$whname[$bt_cdn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_cdn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_cdn_val[7]."</td>";
													echo "<td>".number_format_ind($bt_cdn_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_cdn_val[4];
													$closing_amt = $closing_amt - $bt_cdn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_sdn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_sdn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_sdn_val = explode("@",$bt_sdn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_sdn_val[1]))."</td>";
													echo "<td>".$bt_sdn_val[0]."</td>";
													echo "<td>SDN</td>";
													echo "<td>".$bt_sdn_val[3]."</td>";
													echo "<td>".$whname[$bt_sdn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_sdn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_sdn_val[7]."</td>";
													echo "<td>".number_format_ind($bt_sdn_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_sdn_val[4];
													$closing_amt = $closing_amt - $bt_sdn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}*/
											$ccount = sizeof($bt_fpv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_fpv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_fpv_val = explode("@",$bt_fpv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_fpv_val[1]))."</td>";
													echo "<td>".$bt_fpv_val[0]."</td>";
													echo "<td>Payment Voucher</td>";
													echo "<td>".$bt_fpv_val[3]."</td>";
													echo "<td>".$coaname[$bt_fpv_val[2]]."</td>";
													echo "<td>".$whname[$bt_fpv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_fpv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_fpv_val[6]."</td>";
													echo "<td>".number_format_ind($bt_fpv_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_fpv_val[4];
													$closing_amt = $closing_amt - $bt_fpv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_frv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_frv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_frv_val = explode("@",$bt_frv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_frv_val[1]))."</td>";
													echo "<td>".$bt_frv_val[0]."</td>";
													echo "<td>Receipt Voucher</td>";
													echo "<td>".$bt_frv_val[3]."</td>";
													echo "<td>".$coaname[$bt_frv_val[2]]."</td>";
													echo "<td>".$whname[$bt_frv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_frv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_frv_val[6]."</td>";
													echo "<td>".number_format_ind($bt_frv_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_frv_val[4];
													$closing_amt = $closing_amt - $bt_frv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_fjv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_fjv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_fjv_val = explode("@",$bt_fjv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_fjv_val[1]))."</td>";
													echo "<td>".$bt_fjv_val[0]."</td>";
													echo "<td>Journal Voucher</td>";
													echo "<td>".$bt_fjv_val[3]."</td>";
													echo "<td>".$coaname[$bt_fjv_val[2]]."</td>";
													echo "<td>".$whname[$bt_fjv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_fjv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_fjv_val[6]."</td>";
													echo "<td>".number_format_ind($bt_fjv_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_fjv_val[4];
													$closing_amt = $closing_amt - $bt_fjv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_foth_vou_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_foth_vou_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_foth_vou_val = explode("@",$bt_foth_vou_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_foth_vou_val[1]))."</td>";
													echo "<td>".$bt_foth_vou_val[0]."</td>";
													echo "<td>OTH VOC</td>";
													echo "<td>".$bt_foth_vou_val[3]."</td>";
													echo "<td>".$coaname[$bt_foth_vou_val[2]]."</td>";
													echo "<td>".$whname[$bt_foth_vou_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_foth_vou_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_foth_vou_val[6]."</td>";
													echo "<td>".number_format_ind($bt_foth_vou_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_foth_vou_val[4];
													$closing_amt = $closing_amt - $bt_foth_vou_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											/*$ccount = sizeof($bt_cus_rct_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_cus_rct_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_cus_rct_val = explode("@",$bt_cus_rct_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_cus_rct_val[1]))."</td>";
													echo "<td>".$bt_cus_rct_val[0]."</td>";
													echo "<td>RCT</td>";
													echo "<td>".$bt_cus_rct_val[3]."</td>";
													echo "<td>".$whname[$bt_cus_rct_val[8]]."</td>";
													echo "<td>".$cpname[$bt_cus_rct_val[2]]."</td>";
													echo "<td>".$bt_cus_rct_val[5]."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_cus_rct_val[4]))."</td>";
													echo "<td>".$bt_cus_rct_val[9]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_cus_rct_val[6])."</td>";
													$bt_received = $bt_received + $bt_cus_rct_val[6];
													$closing_amt = $closing_amt + $bt_cus_rct_val[6];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_ccn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_ccn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_ccn_val = explode("@",$bt_ccn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_ccn_val[1]))."</td>";
													echo "<td>".$bt_ccn_val[0]."</td>";
													echo "<td>CCN</td>";
													echo "<td>".$bt_ccn_val[3]."</td>";
													echo "<td>".$whname[$bt_ccn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_ccn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_ccn_val[7]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_ccn_val[4])."</td>";
													$bt_received = $bt_received + $bt_ccn_val[4];
													$closing_amt = $closing_amt + $bt_ccn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_scn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_scn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_scn_val = explode("@",$bt_scn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_scn_val[1]))."</td>";
													echo "<td>".$bt_scn_val[0]."</td>";
													echo "<td>SCN</td>";
													echo "<td>".$bt_scn_val[3]."</td>";
													echo "<td>".$whname[$bt_scn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_scn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_scn_val[7]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_scn_val[4])."</td>";
													$bt_received = $bt_received + $bt_scn_val[4];
													$closing_amt = $closing_amt + $bt_scn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}*/
											$ccount = sizeof($bt_tpv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_tpv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_tpv_val = explode("@",$bt_tpv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_tpv_val[1]))."</td>";
													echo "<td>".$bt_tpv_val[0]."</td>";
													echo "<td>Payment Voucher</td>";
													echo "<td>".$bt_tpv_val[3]."</td>";
													echo "<td>".$coaname[$bt_tpv_val[2]]."</td>";
													echo "<td>".$whname[$bt_tpv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_tpv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_tpv_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_tpv_val[4])."</td>";
													$bt_received = $bt_received + $bt_tpv_val[4];
													$closing_amt = $closing_amt + $bt_tpv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_trv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_trv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_trv_val = explode("@",$bt_trv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_trv_val[1]))."</td>";
													echo "<td>".$bt_trv_val[0]."</td>";
													echo "<td>Receipt Voucher</td>";
													echo "<td>".$bt_trv_val[3]."</td>";
													echo "<td>".$coaname[$bt_trv_val[2]]."</td>";
													echo "<td>".$whname[$bt_trv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_trv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_trv_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_trv_val[4])."</td>";
													$bt_received = $bt_received + $bt_trv_val[4];
													$closing_amt = $closing_amt + $bt_trv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_tjv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_tjv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_tjv_val = explode("@",$bt_tjv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_tjv_val[1]))."</td>";
													echo "<td>".$bt_tjv_val[0]."</td>";
													echo "<td>Journal Voucher</td>";
													echo "<td>".$bt_tjv_val[3]."</td>";
													echo "<td>".$coaname[$bt_tjv_val[2]]."</td>";
													echo "<td>".$whname[$bt_tjv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_tjv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_tjv_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_tjv_val[4])."</td>";
													$bt_received = $bt_received + $bt_tjv_val[4];
													$closing_amt = $closing_amt + $bt_tjv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_toth_vou_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_toth_vou_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_toth_vou_val = explode("@",$bt_toth_vou_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_toth_vou_val[1]))."</td>";
													echo "<td>".$bt_toth_vou_val[0]."</td>";
													echo "<td>OTH VOC</td>";
													echo "<td>".$bt_toth_vou_val[3]."</td>";
													echo "<td>".$coaname[$bt_toth_vou_val[2]]."</td>";
													echo "<td>".$whname[$bt_toth_vou_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_toth_vou_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_toth_vou_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_toth_vou_val[4])."</td>";
													$bt_received = $bt_received + $bt_toth_vou_val[4];
													$closing_amt = $closing_amt + $bt_toth_vou_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
										}
									?>
								</tbody>
								<thead style="background-color: #98fb98;">
									<tr class="foottr">
										<td colspan="9" align="center" style="background-color: #98fb98;"><b>Between Days Total</b></td>
										<td style='padding-right: 5px;text-align:right;'><b><?php echo number_format_ind($bt_paid); ?></b></td>
										<td style='padding-right: 5px;text-align:right;'><b><?php echo number_format_ind($bt_received); ?></b></td>
										<td></td>
									</tr>
									<tr class="foottr">
										<td colspan="9" align="center" style="background-color: #98fb98;"><b>Closing Balance</b></td>
										<td colspan="2" style='padding-right: 5px;text-align:right;'><b><?php echo number_format_ind(($pre_received - $pre_paid)+($bt_received - $bt_paid)); ?></b></td>
										<td></td>
									</tr>
								</thead>
						<?php
							}
							else if($_POST['coa'] != "all" AND $_POST['whname'] != "all"){
								$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
								$tdate = date("Y-m-d",strtotime($_POST['todate']));
								if($_POST['coa'] == "all"){ $coa = $allCoA; } else{ $coa = $_POST['coa']; } $wname = $_POST['whname'];
								/*$sql = "SELECT SUM(amount) as tamt FROM `pur_payments` WHERE `date` < '$fdate' AND `active` = '1' AND `method` IN ('$coa') AND `warehouse` = '$wname' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ $pre_pur_pay_amt = $row['tamt']; }
								$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE `date` < '$fdate' AND `active` = '1' AND `method` IN ('$coa') AND `warehouse` = '$wname' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){ $pre_cus_rct_amt = $row['tamt']; }
								$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE `date` < '$fdate' AND `active` = '1' AND `coa` IN ('$coa') AND `warehouse` = '$wname' GROUP BY `mode` ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['mode'] == "CCN"){ $pre_ccn_amt = $row['tamt']; }
									else if($row['mode'] == "CDN"){ $pre_cdn_amt = $row['tamt']; }
									else if($row['mode'] == "SCN"){ $pre_scn_amt = $row['tamt']; }
									else if($row['mode'] == "SDN"){ $pre_sdn_amt = $row['tamt']; }
									else { $pre_oth_amt = $row['tamt']; }
								}*/
								$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate'".$prx_fltr." AND `active` = '1' AND `fcoa` IN ('$coa') AND `warehouse` = '$wname' GROUP BY `prefix` ORDER BY `fcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){ $pre_fpv_amt = $row['tamt']; }
									else if($row['prefix'] == "RV"){ $pre_frv_amt = $row['tamt']; }
									else if($row['prefix'] == "JV"){ $pre_fjv_amt = $row['tamt']; }
									else { $pre_foth_vou_amt = $row['tamt']; }
								}
								$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate'".$prx_fltr." AND `active` = '1' AND `tcoa` IN ('$coa') AND `warehouse` = '$wname' GROUP BY `prefix` ORDER BY `tcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){ $pre_tpv_amt = $row['tamt']; }
									else if($row['prefix'] == "RV"){ $pre_trv_amt = $row['tamt']; }
									else if($row['prefix'] == "JV"){ $pre_tjv_amt = $row['tamt']; }
									else { $pre_toth_vou_amt = $row['tamt']; }
								}
								//echo number_format_ind($pre_pur_pay_amt)."".number_format_ind($pre_cdn_amt)."".number_format_ind($pre_sdn_amt)."".number_format_ind($pre_fpv_amt)."".number_format_ind($pre_frv_amt)."".number_format_ind($pre_fjv_amt)."".number_format_ind($pre_foth_vou_amt);
								$pre_paid = $pre_pur_pay_amt + $pre_cdn_amt + $pre_sdn_amt + $pre_fpv_amt + $pre_frv_amt + $pre_fjv_amt + $pre_foth_vou_amt;
								$pre_received = $pre_cus_rct_amt + $pre_ccn_amt + $pre_scn_amt + $pre_tpv_amt + $pre_trv_amt + $pre_tjv_amt + $pre_toth_vou_amt;
								if($pre_paid > $pre_received){
									$pending_pay = $pre_paid - $pre_received;
									$closing_amt = $pre_received - $pre_paid;
								}
								else {
									$closing_amt = $pending_rct = $pre_received - $pre_paid;
								}
								/*$c = 0;
								$sql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `method` IN ('$coa') AND `warehouse` = '$wname' ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$c = $c + 1;
									$bt_pur_pay_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
								}
								$c = 0;
								$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `method` IN ('$coa') AND `warehouse` = '$wname' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$c = $c + 1;
									$bt_cus_rct_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
								}
								$c = $d = $e = $f = $g = 0;
								$sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `coa` IN ('$coa') AND `warehouse` = '$wname' ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['mode'] == "CCN"){
										$c = $c + 1;
										$bt_ccn_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['mode'] == "CDN"){
										$d = $d + 1;
										$bt_cdn_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['mode'] == "SCN"){
										$e = $e + 1;
										$bt_scn_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['mode'] == "SDN"){
										$f = $f + 1;
										$bt_sdn_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
									else {
										$g = $g + 1;
										$bt_oth_amt[$row['date']."@".$g] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
									}
								}*/
								$c = $d = $e = $f = $g = 0;
								$bt_fpv_amt = $bt_frv_amt = $bt_fjv_amt = $bt_foth_vou_amt = $bt_tpv_amt = $bt_trv_amt = $bt_tjv_amt = $bt_toth_vou_amt = array();
								$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$prx_fltr." AND `active` = '1' AND `fcoa` IN ('$coa') AND `warehouse` = '$wname' ORDER BY `fcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){
										$c = $c + 1;
										$bt_fpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "RV"){
										$d = $d + 1;
										$bt_frv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "JV"){
										$e = $e + 1;
										$bt_fjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else {
										$f = $f + 1;
										$bt_foth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
								}
								$c = $d = $e = $f = $g = 0;
								$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$prx_fltr." AND `active` = '1' AND `tcoa` IN ('$coa') AND `warehouse` = '$wname' ORDER BY `tcoa` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									if($row['prefix'] == "PV"){
										$c = $c + 1;
										$bt_tpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "RV"){
										$d = $d + 1;
										$bt_trv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else if($row['prefix'] == "JV"){
										$e = $e + 1;
										$bt_tjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
									else {
										$f = $f + 1;
										$bt_toth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
									}
								}
						?>
								<thead class="thead2" style="background-color: #98fb98;">
									<th>Sl.No.</th>
									<th>Date</th>
									<th>Transaction No</th>
									<th>Transaction Type</th>
									<th>Doc. No.</th>
									<th>Paid From/To</th>
									<th>From Warehouse</th>
									<!--<th>Paid/Received</th>
									<th>Cheque No</th>
									<th>Cheque Date</th>-->
									<th>Remarks</th>
									<th>Paid</th>
									<th>Received</th>
									<th>Running Balance</th>
								</thead>
								
								<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
									<tr>
										<td colspan="2"><b>Previous Balance</b></td>
										<td colspan="6"></td>
										<td><?php echo number_format_ind($pre_paid); ?></td>
										<td><?php echo number_format_ind($pre_received); ?></td>
										<td><?php echo number_format_ind($closing_amt); ?></td>
									</tr>
									<?php
										$fdate = strtotime($fdate);
										$tdate = strtotime($tdate);
										$bt_paid = $bt_received = $c = 0;
										for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
											$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']
											."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
											$date_asc = date('Y-m-d', $currentDate); 
											/*$ccount = sizeof($bt_pur_pay_amt); 
											for($i = 1;$i <=$ccount;$i++){
												if($bt_pur_pay_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_pur_pay_val = explode("@",$bt_pur_pay_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_pur_pay_val[1]))."</td>";
													echo "<td>".$bt_pur_pay_val[0]."</td>";
													echo "<td>PMT</td>";
													echo "<td>".$bt_pur_pay_val[3]."</td>";
													echo "<td>".$whname[$bt_pur_pay_val[8]]."</td>";
													echo "<td>".$cpname[$bt_pur_pay_val[2]]."</td>";
													echo "<td>".$bt_pur_pay_val[5]."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_pur_pay_val[4]))."</td>";
													echo "<td>".$bt_pur_pay_val[9]."</td>";
													echo "<td>".number_format_ind($bt_pur_pay_val[6])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_pur_pay_val[6];
													$closing_amt = $closing_amt - $bt_pur_pay_val[6];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_cdn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_cdn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_cdn_val = explode("@",$bt_cdn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_cdn_val[1]))."</td>";
													echo "<td>".$bt_cdn_val[0]."</td>";
													echo "<td>CDN</td>";
													echo "<td>".$bt_cdn_val[3]."</td>";
													echo "<td>".$whname[$bt_cdn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_cdn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_cdn_val[7]."</td>";
													echo "<td>".number_format_ind($bt_cdn_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_cdn_val[4];
													$closing_amt = $closing_amt - $bt_cdn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_sdn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_sdn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_sdn_val = explode("@",$bt_sdn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_sdn_val[1]))."</td>";
													echo "<td>".$bt_sdn_val[0]."</td>";
													echo "<td>SDN</td>";
													echo "<td>".$bt_sdn_val[3]."</td>";
													echo "<td>".$whname[$bt_sdn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_sdn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_sdn_val[7]."</td>";
													echo "<td>".number_format_ind($bt_sdn_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_sdn_val[4];
													$closing_amt = $closing_amt - $bt_sdn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}*/
											$ccount = sizeof($bt_fpv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_fpv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_fpv_val = explode("@",$bt_fpv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_fpv_val[1]))."</td>";
													echo "<td>".$bt_fpv_val[0]."</td>";
													echo "<td>Payment Voucher</td>";
													echo "<td>".$bt_fpv_val[3]."</td>";
													echo "<td>".$coaname[$bt_fpv_val[2]]."</td>";
													echo "<td>".$whname[$bt_fpv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_fpv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_fpv_val[6]."</td>";
													echo "<td>".number_format_ind($bt_fpv_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_fpv_val[4];
													$closing_amt = $closing_amt - $bt_fpv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_frv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_frv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_frv_val = explode("@",$bt_frv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_frv_val[1]))."</td>";
													echo "<td>".$bt_frv_val[0]."</td>";
													echo "<td>Receipt Voucher</td>";
													echo "<td>".$bt_frv_val[3]."</td>";
													echo "<td>".$coaname[$bt_frv_val[2]]."</td>";
													echo "<td>".$whname[$bt_frv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_frv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_frv_val[6]."</td>";
													echo "<td>".number_format_ind($bt_frv_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_frv_val[4];
													$closing_amt = $closing_amt - $bt_frv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_fjv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_fjv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_fjv_val = explode("@",$bt_fjv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_fjv_val[1]))."</td>";
													echo "<td>".$bt_fjv_val[0]."</td>";
													echo "<td>Journal Voucher</td>";
													echo "<td>".$bt_fjv_val[3]."</td>";
													echo "<td>".$coaname[$bt_fjv_val[2]]."</td>";
													echo "<td>".$whname[$bt_fjv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_fjv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_fjv_val[6]."</td>";
													echo "<td>".number_format_ind($bt_fjv_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_fjv_val[4];
													$closing_amt = $closing_amt - $bt_fjv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_foth_vou_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_foth_vou_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_foth_vou_val = explode("@",$bt_foth_vou_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_foth_vou_val[1]))."</td>";
													echo "<td>".$bt_foth_vou_val[0]."</td>";
													echo "<td>OTH VOC</td>";
													echo "<td>".$bt_foth_vou_val[3]."</td>";
													echo "<td>".$coaname[$bt_foth_vou_val[2]]."</td>";
													echo "<td>".$whname[$bt_foth_vou_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_foth_vou_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_foth_vou_val[6]."</td>";
													echo "<td>".number_format_ind($bt_foth_vou_val[4])."</td>";
													echo "<td></td>";
													$bt_paid = $bt_paid + $bt_foth_vou_val[4];
													$closing_amt = $closing_amt - $bt_foth_vou_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											/*$ccount = sizeof($bt_cus_rct_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_cus_rct_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_cus_rct_val = explode("@",$bt_cus_rct_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_cus_rct_val[1]))."</td>";
													echo "<td>".$bt_cus_rct_val[0]."</td>";
													echo "<td>RCT</td>";
													echo "<td>".$bt_cus_rct_val[3]."</td>";
													echo "<td>".$whname[$bt_cus_rct_val[8]]."</td>";
													echo "<td>".$cpname[$bt_cus_rct_val[2]]."</td>";
													echo "<td>".$bt_cus_rct_val[5]."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_cus_rct_val[4]))."</td>";
													echo "<td>".$bt_cus_rct_val[9]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_cus_rct_val[6])."</td>";
													$bt_received = $bt_received + $bt_cus_rct_val[6];
													$closing_amt = $closing_amt + $bt_cus_rct_val[6];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_ccn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_ccn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_ccn_val = explode("@",$bt_ccn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_ccn_val[1]))."</td>";
													echo "<td>".$bt_ccn_val[0]."</td>";
													echo "<td>CCN</td>";
													echo "<td>".$bt_ccn_val[3]."</td>";
													echo "<td>".$whname[$bt_ccn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_ccn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_ccn_val[7]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_ccn_val[4])."</td>";
													$bt_received = $bt_received + $bt_ccn_val[4];
													$closing_amt = $closing_amt + $bt_ccn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_scn_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_scn_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_scn_val = explode("@",$bt_scn_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_scn_val[1]))."</td>";
													echo "<td>".$bt_scn_val[0]."</td>";
													echo "<td>SCN</td>";
													echo "<td>".$bt_scn_val[3]."</td>";
													echo "<td>".$whname[$bt_scn_val[6]]."</td>";
													echo "<td>".$cpname[$bt_scn_val[2]]."</td>";
													echo "<td></td>";
													echo "<td></td>";
													echo "<td>".$bt_scn_val[7]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_scn_val[4])."</td>";
													$bt_received = $bt_received + $bt_scn_val[4];
													$closing_amt = $closing_amt + $bt_scn_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}*/
											$ccount = sizeof($bt_tpv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_tpv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_tpv_val = explode("@",$bt_tpv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_tpv_val[1]))."</td>";
													echo "<td>".$bt_tpv_val[0]."</td>";
													echo "<td>Payment Voucher</td>";
													echo "<td>".$bt_tpv_val[3]."</td>";
													echo "<td>".$coaname[$bt_tpv_val[2]]."</td>";
													echo "<td>".$whname[$bt_tpv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_tpv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_tpv_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_tpv_val[4])."</td>";
													$bt_received = $bt_received + $bt_tpv_val[4];
													$closing_amt = $closing_amt + $bt_tpv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_trv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_trv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_trv_val = explode("@",$bt_trv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_trv_val[1]))."</td>";
													echo "<td>".$bt_trv_val[0]."</td>";
													echo "<td>Receipt Voucher</td>";
													echo "<td>".$bt_trv_val[3]."</td>";
													echo "<td>".$coaname[$bt_trv_val[2]]."</td>";
													echo "<td>".$whname[$bt_trv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_trv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_trv_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_trv_val[4])."</td>";
													$bt_received = $bt_received + $bt_trv_val[4];
													$closing_amt = $closing_amt + $bt_trv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_tjv_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_tjv_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_tjv_val = explode("@",$bt_tjv_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_tjv_val[1]))."</td>";
													echo "<td>".$bt_tjv_val[0]."</td>";
													echo "<td>Journal Voucher</td>";
													echo "<td>".$bt_tjv_val[3]."</td>";
													echo "<td>".$coaname[$bt_tjv_val[2]]."</td>";
													echo "<td>".$whname[$bt_tjv_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_tjv_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_tjv_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_tjv_val[4])."</td>";
													$bt_received = $bt_received + $bt_tjv_val[4];
													$closing_amt = $closing_amt + $bt_tjv_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
											$ccount = sizeof($bt_toth_vou_amt);
											for($i = 1;$i <=$ccount;$i++){
												if($bt_toth_vou_amt[$date_asc."@".$i] != ""){
													$c = $c + 1;
													$bt_toth_vou_val = explode("@",$bt_toth_vou_amt[$date_asc."@".$i]);
													echo "<tr>";
													echo "<td>".$c."</td>";
													echo "<td>".date("d.m.Y",strtotime($bt_toth_vou_val[1]))."</td>";
													echo "<td>".$bt_toth_vou_val[0]."</td>";
													echo "<td>OTH VOC</td>";
													echo "<td>".$bt_toth_vou_val[3]."</td>";
													echo "<td>".$coaname[$bt_toth_vou_val[2]]."</td>";
													echo "<td>".$whname[$bt_toth_vou_val[5]]."</td>";
													//echo "<td>".$coaname[$bt_toth_vou_val[2]]."</td>";
													//echo "<td></td>";
													//echo "<td></td>";
													echo "<td>".$bt_toth_vou_val[6]."</td>";
													echo "<td></td>";
													echo "<td>".number_format_ind($bt_toth_vou_val[4])."</td>";
													$bt_received = $bt_received + $bt_toth_vou_val[4];
													$closing_amt = $closing_amt + $bt_toth_vou_val[4];
													echo "<td>".number_format_ind($closing_amt)."</td>";
													echo "</tr>";
												}
												else{
													
												}
											}
										}
									?>
								</tbody>
								<thead style="background-color: #98fb98;">
									<tr class="foottr">
										<td colspan=9" align="center"><b>Between Days Total</b></td>
										<td style='padding-right: 5px;text-align:right;' align="right"><b><?php echo number_format_ind($bt_paid); ?></b></td>
										<td style='padding-right: 5px;text-align:right;' align="right"><b><?php echo number_format_ind($bt_received); ?></b></td>
										<td></td>
									</tr>
									<tr class="foottr">
										<td colspan="9" align="center"><b>Closing Balance</b></td>
										<td colspan="2" style='padding-right: 5px;text-align:right;' align="right"><b><?php echo number_format_ind(($pre_received - $pre_paid)+($bt_received - $bt_paid)); ?></b></td>
										<td></td>
									</tr>
								</thead>
						<?php
							}
							else {}
						}
							?>
							
						</table>
					</form>
				</div>
		</section>
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("checkcname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select customer ..!");
					return false;
				}
				else if(a.match("all")){
					alert("Please select customer ..!");
					return false;
				}
				else {
					return true;
				}
			}
			function sortTable(n) {
			  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			  table = document.getElementById("myTable");
			  switching = true;
			  //Set the sorting direction to ascending:
			  dir = "asc"; 
			  /*Make a loop that will continue until
			  no switching has been done:*/
			  while (switching) {
				//start by saying: no switching is done:
				switching = false;
				rows = table.rows;
				/*Loop through all table rows (except the
				first, which contains table headers):*/
				for (i = 2; i < (rows.length - 1); i++) {
				  //start by saying there should be no switching:
				  shouldSwitch = false;
				  /*Get the two elements you want to compare,
				  one from current row and one from the next:*/
				  x = rows[i].getElementsByTagName("TD")[n];
				  y = rows[i + 1].getElementsByTagName("TD")[n];
				  /*check if the two rows should switch place,
				  based on the direction, asc or desc:*/
				  if (dir == "asc") {
					if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch= true;
					  break;
					}
				  } else if (dir == "desc") {
					if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
					  //if so, mark as a switch and break the loop:
					  shouldSwitch = true;
					  break;
					}
				  }
				}
				if (shouldSwitch) {
				  /*If a switch has been marked, make the switch
				  and mark that a switch has been done:*/
				  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				  switching = true;
				  //Each time a switch is done, increase this count by 1:
				  switchcount ++;
				  
				} else {
				  /*If no switching has been done AND the direction is "asc",
				  set the direction to "desc" and run the while loop again.*/
				  if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				  }
				}
			  }
			}
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
		<?php
			if($cname == ""){
				
			}
			else {
				echo "<script> sortTable(0); </script>";
			}
		?>
	</body>
	
</html>
<?php include "header_foot.php"; ?>