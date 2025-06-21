<?php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	include "../config.php";
	include "header_head.php";
	include "number_format_ind.php";
	
	$today = date("Y-m-d");
	
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pname[$row['code']] = $row['name']; }
	$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pmname[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pmename[$row['code']] = $row['description']; }

    // Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $officename[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname']; $wname = $_POST['wname'];
	if($cname == "all") { $cnames = ""; } else { $cnames = " AND `ccode` = '$cname'"; }
	if($wname == "all") { $wnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; }
	$module_detail = $_POST['module'];
	$status_detail = $_POST['status'];
	$expoption = "displaypage";
	if(isset($_POST['submit'])) { $expoption = $_POST['export']; }
	if($expoption == "displaypage") { $exoption = "displaypage"; } else { $exoption = $expoption; };
	$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'PDF' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
	<link rel="stylesheet" type="text/css" href="loading_screen.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=smsreport($fromdate-$todate).xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<style>
			body{
				overflow: auto;
			}
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;				
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
				text-align:right;
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
						<h3>SMS Report</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $pname[$cname]; ?></label><br/>
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<form action="main_smsreport.php" method="post">
						<table class="table1" style="min-width:90%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="12">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
										&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
										&ensp;&ensp;
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($cname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
											<?php
												}
											?>
										</select>
										&ensp;&ensp;
										<label class="reportselectionlabel">Module</label>&nbsp;
										<select name="module" id="module" class="form-control select2">
											<option value="all" <?php if($module_detail == "all"){ echo 'selected'; } ?>>-All-</option>
											<option value="SALES" <?php if($module_detail == "SALE"){ echo 'selected'; } ?>>Sales</option>
											<option value="RECEIPT" <?php if($module_detail == "RECEIPT"){ echo 'selected'; } ?>>Receipts</option>
											<option value="Outstad" <?php if($module_detail == "Outstad"){ echo 'selected'; } ?>>Balance</option>
											<option value="PaperRate" <?php if($module_detail == "PaperRate"){ echo 'selected'; } ?>>Paper Rate</option>
											<option value="ledger" <?php if($module_detail == "ledger"){ echo 'selected'; } ?>>Ledger</option>
											<!--<option value="otp" <?php if($module_detail == "otp"){ echo 'selected'; } ?>>OTP</option>-->
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Status</label>&nbsp;
										<select name="status" id="status" class="form-control select2">
											<option value="all" <?php if($status_detail == "all"){ echo 'selected'; } ?>>-All-</option>
											<option value="success" <?php if($status_detail == "success"){ echo 'selected'; } ?>>Success</option>
											<option value="failed" <?php if($status_detail == "failed"){ echo 'selected'; } ?>>Failed</option>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;

										<label class="reportselectionlabel">Wapp Failed All</label>&nbsp;
										<input type="checkbox" name="check_all_wapp" id="check_all_wapp" onclick="check_all_wapp_failed();" />&ensp;&ensp;
										<button type="button" class="btn btn-success btn-sm" name="send_failed_wapp" id="send_failed_wapp" onclick="resend_failed_wapps();">Re-send Wapp</button>&ensp;&ensp;

										<label class="reportselectionlabel">SMS All</label>&nbsp;
										<input type="checkbox" name="check_all_sms" id="check_all_sms" onclick="check_all_sms();" />&ensp;&ensp;
										<button type="button" class="btn btn-success btn-sm" name="send_all_sms" id="send_all_sms" onclick="resend_all_sms();">Re-send SMS</button>&ensp;&ensp;

										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
						<?php
							if(isset($_POST['submit']) == true){
								$fromdate = date("Y-m-d",strtotime($fromdate));
								$todate = date("Y-m-d",strtotime($todate. '+1 days'));
								$module_detail = $_POST['module']; if($module_detail == "all"){ $module_code = " AND `smsto` NOT IN ('otp','user')"; } else if($module_detail == "otp"){ $module_code = " AND `smsto` IN ('otp','user')"; } else{ $module_code = " AND `smsto` LIKE '%$module_detail%'"; }
								$status_detail = $_POST['status']; if($status_detail == "all"){ $status_code = ""; } else if($status_detail == "success"){ $status_code = " AND `sms_status` = '$status_detail'"; } else{ $status_code = " AND `sms_status` != 'success'"; }
								$cus_detail = $_POST['cname'];
						?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th>Sl No.</th>
								<th>Select</th>
								<th>Date</th>
								<th>Type</th>
								<th>Module</th>
								<?php //if($cus_detail == "all"){ ?><th>Customer</th> <?php //} ?>
								<th>Mobile No.</th>
								<!--<th>transaction No.</th>-->
								<th>Message</th>
								<th>Status</th>
								<th>SMS Count</th>
								<th>SMS sent<br/>Date &amp; Time</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								
								$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'";
								$query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$cus_code[$row['mobileno']] = $row['code'];
									$cus_name[$row['code']] = $row['name'];
									$cus_mobile[$row['mobileno']] = $row['mobileno'];
									$cus_mob[$row['code']] = $row['mobileno'];
								}
								if($cus_detail == "all"){ $mobilenos ==""; } else{ $mobileno = $cus_mob[$cus_detail]; }
								//if($mobilenos == ""){ $mobileno = ""; } else if(strlen($mobilenos) == 10){ $mobileno = "91".$mobilenos; } else if(strlen($mobilenos) == 12){ $mobileno = $mobilenos; } else{ $mobileno = ""; }
								if($mobileno == ""){ $mobile_code = ""; } else{ $mobile_code = " AND `mobile` LIKE '%$mobileno'"; }
								
								$seq = "SELECT * FROM `sms_details` WHERE `addedtime` >= '$fromdate' AND `addedtime` <= '$todate' AND `smsto` NOT LIKE '%otp%'";
								$orderby = " ORDER BY `id` ASC";
								$sql = $seq."".$mobile_code."".$module_code."".$status_code."".$orderby;
								$query = mysqli_query($conn,$sql);
								$sms_success = $sms_failed = 0; $flno = $smsno = 0; $sl = 1;
								while($row = mysqli_fetch_assoc($query)){
									$msgtypekey = array();
									$msgtypekey = explode("-",$row['trnum']);
									$id = $row['id'];

									echo "<tr>";
									echo "<td style='text-align:left;'>".$sl++."</td>";
									if($msgtypekey[0] == "WAPP" && strtolower($row['sms_status']) != "success" && $row['sms_status'] != "" && strpos($row['smsto'],"Ledger") == 0){
										echo "<td style='text-align:center;'><input type='checkbox' name='wapp_failed_ids[]' id='wapp_failed_ids[".$flno."]' value='".$id."' /></td>";
										$flno++;
									}
									else if($msgtypekey[0] == "SMS"){
										echo "<td style='text-align:center;'><input type='checkbox' name='sms_all_ids[]' id='sms_all_ids[".$smsno."]' value='".$id."' /></td>";
										$smsno++;
									}
									else{ echo "<td></td>"; }
									
									echo "<td>".date("d.m.Y",strtotime($row['addedtime']))."</td>";
									echo "<td style='text-align:left;'>".$msgtypekey[0]."</td>";
									echo "<td style='text-align:left;'>".$row['smsto']."</td>";
									if(strlen($row['mobile']) > 10){
										$mob_code = substr($row['mobile'],2);
									}
									else{
										$mob_code = $row['mobile'];
									}
									echo "<td style='text-align:left;'>".$cus_name[$row['ccode']]."</td>";
									echo "<td style='text-align:left;'>".$mob_code."</td>";
									if($msgtypekey[0] == "WAPP") {
										$sms_sent1 = explode("&message=",$row['sms_sent']);
										$wapp_sent = explode("&instance_id=",$sms_sent1[1]);
										$wapp_sent2 = explode("&media_url=",$wapp_sent[0]);
										$wapp_sent2[0] = str_replace("%0D%0A","",str_replace("+"," ",$wapp_sent2[0]));
										echo "<td style='width:350px;text-align:left;over-flow:auto;'>".$wapp_sent2[0]."</td>";
										if($row['sms_status'] != "success" && $row['sms_status'] != ""){ $wapp_failed = $wapp_failed + 1; }
										else if($row['sms_status'] == "" && $row['msg_response'] == "" && $row['ccode'] == ""){ $wapp_count = 1; $wapp_success = $wapp_success + $wapp_count; }
										else{ $wapp_count = 1; $wapp_success = $wapp_success + $wapp_count; }
										/*else if(strlen($wapp_sent[0]) <= 160){ $wapp_count = 1; $wapp_success = $wapp_success + $wapp_count; }
										else if(strlen($wapp_sent[0]) > 160 && strlen($wapp_sent[0]) <= 320){ $wapp_count = 2; $wapp_success = $wapp_success + $wapp_count; }
										else if(strlen($wapp_sent[0]) > 320 && strlen($wapp_sent[0]) <= 480){ $wapp_count = 3; $wapp_success = $wapp_success + $wapp_count; }
										else if(strlen($wapp_sent[0]) > 480 && strlen($wapp_sent[0]) <= 600){ $wapp_count = 4; $wapp_success = $wapp_success + $wapp_count; }
										else{ $wapp_count = 1; $wapp_success = $wapp_success + $wapp_count; }*/
										
										echo "<td style='text-align:left;'>".$row['sms_status']."</td>";
										//echo "<td style='text-align:center;'>".$wapp_count."(".strlen($wapp_sent[0]).")"."</td>";
										echo "<td style='text-align:center;'>".$wapp_count."</td>";
									}
									else{
										$sms_sent1 = explode("<message>",$row['sms_sent']);
										$sms_sent = explode("<accusage>",$sms_sent1[1]);
										echo "<td style='width:350px;text-align:left;over-flow:auto;'>".$sms_sent[0]."</td>";
										if($row['sms_status'] != "success"){
											$sms_failed = $sms_failed + 1;
										}
										else if(strlen($sms_sent[0]) <= 160){
											$sms_count = 1;
											$sms_success = $sms_success + $sms_count;
										}
										else if(strlen($sms_sent[0]) > 160 && strlen($sms_sent[0]) <= 320){
											$sms_count = 2;
											$sms_success = $sms_success + $sms_count;
										}
										else if(strlen($sms_sent[0]) > 320 && strlen($sms_sent[0]) <= 480){
											$sms_count = 3;
											$sms_success = $sms_success + $sms_count;
										}
										else if(strlen($sms_sent[0]) > 480 && strlen($sms_sent[0]) <= 600){
											$sms_count = 4;
											$sms_success = $sms_success + $sms_count;
										}
										else{
											$sms_count = 1;
											$sms_success = $sms_success + $sms_count;
										}
										echo "<td style='text-align:left;'>".$row['sms_status']."</td>";
										//echo "<td style='text-align:center;'>".$sms_count."(".strlen($sms_sent[0]).")"."</td>";
										echo "<td style='text-align:center;'>".$sms_count."</td>";
									}
									
									echo "<td style='max-width:110px;'>".date("d.m.Y g:i A",strtotime($row['addedtime']))."</td>";
									echo "</tr>";
								}
							?>
								<tr class="foottr" style="background-color: #98fb98;">
									<td colspan="3" align="center"><b>Grand Total</b></td>
									<td colspan="1"><?php echo number_format_ind($sms_success + $sms_failed + $wapp_success + $wapp_failed); ?></td>
									<td colspan="1" align="center"><b>Total Success</b></td>
									<td colspan="1"><?php echo number_format_ind($sms_success + $wapp_success); ?></td>
									<td colspan="2" align="center"><b>Total Failed</b></td>
									<td colspan="1"><?php echo number_format_ind($sms_failed + $wapp_failed); ?></td>
									<td colspan="1"></td>
								</tr>
							</tbody>
						<?php
							}
						?>
						</table>
					</form>
				</div>
			<div class="ring"><?php echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>
		</section>
		<script>
			function check_all_wapp_failed(){
				var failed_achk = document.getElementById("check_all_wapp");
				var checkboxes = document.querySelectorAll('input[name="wapp_failed_ids[]"]');
				
				if(failed_achk.checked == true){
					for(var i = 0; i < checkboxes.length; i++){
						checkboxes[i].checked = true;
					}
				}
				else{
					for(var i = 0; i < checkboxes.length; i++){
						checkboxes[i].checked = false;
					}
				}
			}
			function check_all_sms(){
				var failed_achk = document.getElementById("check_all_sms");
				var checkboxes = document.querySelectorAll('input[name="sms_all_ids[]"]');
				
				if(failed_achk.checked == true){
					for(var i = 0; i < checkboxes.length; i++){
						checkboxes[i].checked = true;
					}
				}
				else{
					for(var i = 0; i < checkboxes.length; i++){
						checkboxes[i].checked = false;
					}
				}
			}
			function resend_failed_wapps(){
				var checkboxes = document.querySelectorAll('input[name="wapp_failed_ids[]"]');
				var fdate = document.getElementById("datepickers").value;
				var tdate = document.getElementById("datepickers1").value;

				var id_list = ""; var id_cnt = 0;
				for(var i = 0; i < checkboxes.length; i++){
					if(checkboxes[i].checked == true){
						if(document.getElementById("wapp_failed_ids["+i+"]")){
							if(id_list == ""){ id_list = document.getElementById("wapp_failed_ids["+i+"]").value; }
							else{ id_list = id_list+","+document.getElementById("wapp_failed_ids["+i+"]").value; }
							id_cnt = id_cnt + 1;
						}
					}
				}
				if(fdate == ""){
					alert("Please select/enter appropriate From Date");
					document.getElementById("datepickers").focus();
				}
				else if(tdate == ""){
					alert("Please select/enter appropriate To Date");
					document.getElementById("datepickers1").focus();
				}
				else if(parseInt(id_cnt) == 0){
					alert("Please select atleast one failed message checkbox to Re-send WhatsApp");
				}
				else{
					document.getElementById("send_failed_wapp").style.visibility = "hidden";
					document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					
					var resend_wapp = new XMLHttpRequest();
					var method = "GET";
					var url = "chicken_resend_failed_wapp_master1.php?fdate="+fdate+"&tdate="+tdate+"&id_list="+id_list+"&wapp_stype=failed_waap_resend";
					//window.open(url);
					var asynchronous = true;
					resend_wapp.open(method, url, asynchronous);
					resend_wapp.send();
					resend_wapp.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var fwapp_dt1 = this.responseText;
							var fwapp_dt2 = fwapp_dt1.split("[@$&]");
							var err_flag = fwapp_dt2[0];
							var err_msg = fwapp_dt2[1];
							if(parseInt(err_flag) == 1){
								alert(err_msg);
							}
							else{
								alert("Re-send WhatsApp completed Successfully.")
							}
							document.getElementById("send_failed_wapp").style.visibility = "visible";
							document.getElementsByClassName("ring")[0].style.display = "none";
							document.getElementsByClassName("ring_status")[0].style.display = "none";
						}
					}
				}
			}
			function resend_all_sms(){
				var checkboxes = document.querySelectorAll('input[name="sms_all_ids[]"]');
				var fdate = document.getElementById("datepickers").value;
				var tdate = document.getElementById("datepickers1").value;

				var id_list = ""; var id_cnt = 0;
				for(var i = 0; i < checkboxes.length; i++){
					if(checkboxes[i].checked == true){
						if(document.getElementById("sms_all_ids["+i+"]")){
							if(id_list == ""){ id_list = document.getElementById("sms_all_ids["+i+"]").value; }
							else{ id_list = id_list+","+document.getElementById("sms_all_ids["+i+"]").value; }
							id_cnt = id_cnt + 1;
						}
					}
				}
				if(fdate == ""){
					alert("Please select/enter appropriate From Date");
					document.getElementById("datepickers").focus();
				}
				else if(tdate == ""){
					alert("Please select/enter appropriate To Date");
					document.getElementById("datepickers1").focus();
				}
				else if(parseInt(id_cnt) == 0){
					alert("Please select atleast one SMS message checkbox to Re-send SMS");
				}
				else{
					document.getElementById("send_all_sms").style.visibility = "hidden";
					document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";

					var resend_wapp = new XMLHttpRequest();
					var method = "GET";
					var url = "chicken_resend_all_sms_master1.php?fdate="+fdate+"&tdate="+tdate+"&id_list="+id_list+"&sms_stype=all_sms_resend";
					//window.open(url);
					var asynchronous = true;
					resend_wapp.open(method, url, asynchronous);
					resend_wapp.send();
					resend_wapp.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var fwapp_dt1 = this.responseText;
							var fwapp_dt2 = fwapp_dt1.split("[@$&]");
							var err_flag = fwapp_dt2[0];
							var err_msg = fwapp_dt2[1];
							if(parseInt(err_flag) == 1){
								alert(err_msg);
							}
							else{
								alert("Re-send SMS completed Successfully.")
							}
							document.getElementById("send_all_sms").style.visibility = "visible";
							document.getElementsByClassName("ring")[0].style.display = "none";
							document.getElementsByClassName("ring_status")[0].style.display = "none";
						}
					}
				}
			}
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
