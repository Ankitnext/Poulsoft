<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);

	if(!isset($_SESSION)){ session_start(); }
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] =  $_GET['db']; } else { $db = ''; }
	if($db == ''){
		include "../config.php";
		$reloadPageLink = "cus_manualinvoicesms.php";
		$reloadPageLinkSending = "cus_sendmanualmsg.php";
	}
	else{
		include "APIconfig.php";
		$reloadPageLink = "cus_manualinvoicesms.php?db=".$db;
		$reloadPageLinkSending = "cus_sendmanualmsg.php?db=".$db;
	}

	include "header_head.php";
	include "number_format_ind.php";
	//echo "<br/>".$IP = $_SERVER['REMOTE_ADDR'];
	$today = date("Y-m-d");
			
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$cus_code[$row['code']] = $row['code'];
		$cus_name[$row['code']] = $row['name'];
		$cus_mobile[$row['code']] = $row['mobileno'];
		$cus_mob[$row['code']] = $row['mobileno'];
	}
	// Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname']; $wname = $_POST['wname'];
	if($cname == "all") { $cnames = ""; } else { $cnames = " AND `ccode` = '$cname'"; }
	if($wname == "all") { $wnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; }
	$module_detail = $_POST['module'];
	//$msgtype = $_POST['msgtype'];
			
	$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){
		while($row = mysqli_fetch_assoc($query)){
			$sales_sms = $row['sale_mnu_sms'];
			$sales_wapp = $row['sale_mnu_wapp'];
			$rct_sms = $row['rct_mnu_sms'];
			$rct_wapp = $row['rct_mnu_wapp'];
		}
	}
	else{
		$sales_sms = $rct_sms = $sales_wapp = $rct_wapp = '0';
	}
	if($sales_sms == '1' || $sales_sms == 1){ $ss_checked = "checked"; }
	else if($sales_wapp == '1' || $sales_wapp == 1){ $sw_checked = "checked"; }
	else if($rct_sms == '1' || $rct_sms == 1){ $rs_checked = "checked"; }
	else if($rct_wapp == '1' || $rct_wapp == 1){ $rw_checked = "checked"; }

	$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'PDF' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
?>
		
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="reportstyle.css">
		<link rel="stylesheet" type="text/css" href="loading_screen.css">
		<style>
			.thead2 th {
 				top: 0;
 				position: sticky;
 				background-color: #98fb98;				
			}
		<style>
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
				text-align:right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
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
						<h3>Send SMS Manually</h3>
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Customer:</b>&nbsp;<?php echo $cus_name[$cname]; ?></label><br/>
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
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<table class="table1" style="min-width:90%;line-height:23px;">
						<form action="<?php echo $reloadPageLink; ?>" method="post">
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="11">
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
												foreach($cus_code as $c_code){
											?>
													<option <?php if($cname == $cus_code[$c_code]) { echo 'selected'; } ?> value="<?php echo $cus_code[$c_code]; ?>"><?php echo $cus_name[$c_code]; ?></option>
											<?php
												}
											?>
										</select>
										&ensp;&ensp;
										<label class="reportselectionlabel">Module</label>&nbsp;
										<select name="module" id="module" class="form-control select2">
											<!--<option value="all" <?php //if($module_detail == "all"){ echo 'selected'; } ?>>-All-</option>-->
											<?php if($sales_sms == 1 || $sales_wapp == 1){ ?><option value="SALES" <?php if($module_detail == "SALES"){ echo 'selected'; } ?>>Sales</option><?php } ?>
											<?php if($rct_sms == 1 || $rct_wapp == 1){ ?><option value="RECEIPT" <?php if($module_detail == "RECEIPT"){ echo 'selected'; } ?>>Receipts</option><?php } ?>
										</select>&ensp;&ensp;
										<!--<label class="reportselectionlabel">Message Type</label>&nbsp;
										<select name="msgtype" id="msgtype" class="form-control select2">
											<option value="sms" <?php //if($msgtype == "sms"){ echo 'selected'; } ?>>SMS</option>
											<option value="wapp" <?php //if($msgtype == "wapp"){ echo 'selected'; } ?>>Whats App</option>
											<option value="all" <?php //if($msgtype == "all"){ echo 'selected'; } ?>>-All-</option>
										</select>&ensp;&ensp;-->
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Submit</button>
									</td>
								</tr>
							</thead>
						</form>
						<?php
							if(isset($_POST['submit']) == true){
								$fromdate = date("Y-m-d",strtotime($fromdate));
								$todate = date("Y-m-d",strtotime($todate));
								$cus_detail = $_POST['cname'];
								$module_detail = $_POST['module'];
								$exist_inv = "";
								
						?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th>Sl No.</th>
								<th>Select<br/><input type="checkbox" name="checkall" id="checkall" onchange="checkedall()" /></th>
								<th>Date</th>
								<?php //if($cus_detail == "all"){ ?><th>Customer</th> <?php //} ?>
								<th>Mobile No.</th>
								<th>Invoice No.</th>
								<th>Invoice Details</th>
								<th>Message Type<br/>(ALL)
									<?php if($sales_sms == 1 && $module_detail == "SALES" || $rct_sms == 1 && $module_detail == "RECEIPT"){ ?><input type="radio" name="checkallradio" id="checkallradio1" value="sms" onclick="checkallmsgtp(this.id)" />SMS&nbsp;<?php } ?>
									<?php if($sales_wapp == 1 && $module_detail == "SALES" || $rct_wapp == 1 && $module_detail == "RECEIPT"){ ?><input type="radio" name="checkallradio" id="checkallradio2" value="wapp" onclick="checkallmsgtp(this.id)" />WhatsApp&nbsp;<?php } ?>
									<?php if($sales_wapp == 1 && $sales_sms == 1 && $module_detail == "SALES" || $rct_wapp == 1 && $rct_sms == 1 && $module_detail == "RECEIPT"){ ?><input type="radio" name="checkallradio" id="checkallradio3" value="both" onclick="checkallmsgtp(this.id)" />Both<?php } ?>
								</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<form action="<?php echo $reloadPageLinkSending; ?>" method="post" onsubmit="return checkval()">
							<?php
							$sl = 1;
								if($module_detail == "SALES"){
									if($cus_detail == "all"){ $cc = ""; } else{ $cc = " AND `customercode` LIKE '$cus_detail'"; }
									$groupby = ""; $orderby = "ORDER BY `date`,`invoice` ASC";
									//Count No. of rows for each Invoice
									$seq = "SELECT * FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1'";
									$sql = $seq."".$cc."".$groupby."".$orderby; $query = mysqli_query($conn,$sql); $scount = mysqli_num_rows($query);
									if($scount > 0){
										$c = 0;
										while($row = mysqli_fetch_assoc($query)){
											if($exist_inv != $row['invoice']){
												$exist_inv = $row['invoice'];
												$item_details[$row['invoice']] = $item_name[$row['itemcode']].": ".$row['birds']."No. ".$row['netweight']."Kgs @ ". $row['itemprice'];
												$sale_amt[$row['invoice']] = $row['finaltotal'];
											}
											else{
												$item_details[$row['invoice']] = $item_details[$row['invoice']].",<br/>".$item_name[$row['itemcode']].": ".$row['birds']."No. ".$row['netweight']."Kgs @ ". $row['itemprice'];
											}
										}
									}
									$seq = "SELECT DISTINCT(invoice) as invoice,customercode,date FROM `customer_sales` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
									$sql = $seq."".$cc."".$orderby; $query = mysqli_query($conn,$sql); $scount = mysqli_num_rows($query);
									if($scount > 0){
										$c = 0; 
										while($row = mysqli_fetch_assoc($query)){
											$c = $c + 1;
											$cus_inv = $row['invoice'];
											$date = date("d.m.Y", strtotime($row['date']));
											$customer_name = $cus_name[$row['customercode']];
											$customer_mobile = $cus_mob[$row['customercode']];
											$cus_val = "";
											$cus_val = $c."&SALE&".$date."&".$customer_name."&".$customer_mobile."&".$cus_inv."&".$item_details[$cus_inv]."&".$sale_amt[$cus_inv]."&".$row['customercode'];
											echo "<tr>";
											echo "<td style='text-align:left;'>".$sl++."</td>";
											echo "<td style='text-align:center;'><input type='checkbox' name='smsdet[]' id='smsdet[]' value='$cus_val' /></td>";
											echo "<td style='text-align:left;'>".$date."</td>";
											echo "<td style='text-align:left;'>".$customer_name."</td>";
											echo "<td style='text-align:left;'>".$customer_mobile."</td>";
											echo "<td style='text-align:left;'>".$cus_inv."</td>";
											echo "<td style='text-align:left;'>".$item_details[$cus_inv]."<br/>Final Total: ".$sale_amt[$cus_inv]."/-</td>";
											echo "<td style='text-align:center;'>";
											if($sales_sms == 1){ echo "<input type='radio' name='msg_type[".$c."]' id='msg_type1[".$c."]' value='sms' ".$ss_checked."/>SMS&nbsp;"; }
											if($sales_wapp == 1){ echo "<input type='radio' name='msg_type[".$c."]' id='msg_type2[".$c."]' value='wapp' ".$sw_checked."/>WhatsApp&nbsp;"; }
											if($sales_wapp == 1 && $sales_sms == 1){ echo "<input type='radio' name='msg_type[".$c."]' id='msg_type3[".$c."]' value='both' />Both"; }
											echo "</td>";
											echo "</tr>";
										}
									}
									else{
										echo "<td colspan='7'>No Records Found ..!</td>";
									}
								}
								else if($module_detail == "RECEIPT"){
									if($cus_detail == "all"){ $cc = ""; } else{ $cc = " AND `ccode` LIKE '$cus_detail'"; }
									$orderby = " ORDER BY `date`,`trnum` ASC";
									$seq = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1'";
									$sql = $seq."".$cc."".$orderby; $query = mysqli_query($conn,$sql); $scount = mysqli_num_rows($query);
									if($scount > 0){
										$c = 0;
										while($row = mysqli_fetch_assoc($query)){
											$c = $c + 1;
											$cus_inv = $row['trnum'];
											$date = date("d.m.Y", strtotime($row['date']));
											$customer_name = $cus_name[$row['ccode']];
											$customer_mobile = $cus_mob[$row['ccode']];
											$cus_val = "";
											$cus_val = $c."&RECEIPT&".$date."&".$customer_name."&".$customer_mobile."&".$cus_inv."&0&".$row['amount']."&".$row['ccode'];
											echo "<tr>";
											echo "<td style='text-align:left;'>".$sl++."</td>";
											echo "<td style='text-align:center;'><input type='checkbox' name='smsdet[]' id='smsdet[]' value='$cus_val' /></td>";
											echo "<td style='text-align:left;'>".$date."</td>";
											echo "<td style='text-align:left;'>".$customer_name."</td>";
											echo "<td style='text-align:left;'>".$customer_mobile."</td>";
											echo "<td style='text-align:left;'>".$cus_inv."</td>";
											echo "<td style='text-align:left;'>".$row['amount']."/-</td>";
											echo "<td style='text-align:center;'>";
											if($rct_sms == 1){ echo "<input type='radio' name='msg_type[".$c."]' id='msg_type1[".$c."]' value='sms' ".$rs_checked."/>SMS&nbsp;"; }
											if($rct_wapp == 1){ echo "<input type='radio' name='msg_type[".$c."]' id='msg_type2[".$c."]' value='wapp' ".$rw_checked."/>WhatsApp&nbsp;"; }
											if($rct_wapp == 1 && $rct_sms == 1){ echo "<input type='radio' name='msg_type[".$c."]' id='msg_type3[".$c."]' value='both' />Both"; }
											echo "</td>";
											echo "</tr>";
										}
									}
									else{
										echo "<td colspan='7'>No Records Found ..!</td>";
									}
								}
								else{
									
								}
							?>
								<tr>
									<th colspan="7" style="line-height:1px;visibility:hidden;">
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</th>
								</tr>
								<tr class="foottr" style="background-color: #98fb98;">
									<th colspan="8" style="padding:10px;text-align:center;"><button type="submit" name="sendsms" id="sendsms" class="btn btn-success btn-md" value="sendsuccess">Send Message</button></th>
								</tr>
							</form>
						</tbody>
						<?php
							}
						?>
					</table>
				</div>
			<div class="ring"><?php echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>
		</section>
		<script>
			function checkedall(){
				var a = document.getElementById("checkall");
				if(a.checked == true){
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 0;c <=b.length;c++){
						b[c].checked = true;
					}
				}
				else{
					var b = document.querySelectorAll('input[type=checkbox]');
					for(var c = 0;c <=b.length;c++){
						b[c].checked = false;
					}
				}
			}
			function checkallmsgtp(a){
				var aa = document.getElementById(a).value;
				var b = '<?php echo $c; ?>';
				if(aa.match("sms")){
					for(var c = 1;c <=b;c++){
						document.getElementById("msg_type1["+c+"]").checked = true;
					}
				}
				else if(aa.match("wapp")){
					for(var c = 1;c <=b;c++){
						document.getElementById("msg_type2["+c+"]").checked = true;
					}
				}
				else if(aa.match("both")){
					for(var c = 1;c <=b;c++){
						document.getElementById("msg_type3["+c+"]").checked = true;
					}
				}
				else{
						
				}
			}
			function checkval(){
				var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
				document.getElementById("ebtncount").value = "1"; document.getElementById("sendsms").style.visibility = "hidden";
				var c = 0;
				if(checkboxes.length == 0){
					alert("Please select Transactions to send Message ..!");
					c = 0;
				}
				else {
					c = checkboxes.length;
				}
				if(c > 0){
					document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = '<?php echo $loading_stitle; ?>';
					return true;
				}
				else{
					document.getElementsByClassName("ring")[0].style.display = "none";
					document.getElementsByClassName("ring_status")[0].style.display = "none";
					document.getElementById("disp_val").innerHTML = "";
					
					document.getElementById("sendsms").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
					return false;
				}
			}
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":sendsms").click(function (){ $('#sendsms').click(); }); } } else{ } });
        </script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
