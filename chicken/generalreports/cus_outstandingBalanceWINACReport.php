<?php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
include "../config.php";
// include "header_head.php";
include "number_format_ind.php";

$sql='SHOW COLUMNS FROM `master_itemfields`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("outstand_wapp", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_itemfields` ADD `outstand_wapp` INT(100) NOT NULL DEFAULT '0' COMMENT 'Outstanding Balance WhatsApp Flag' AFTER `outstand_sms`"; mysqli_query($conn,$sql); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $comp_count = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $client_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; }

// Logo Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE 'C' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $grp_name[$row['code']] = $row['description']; $grp_code[$row['code']] = $row['code']; }

$fdate = date("Y-m-d"); $groups = "all"; $exoption = "displaypage";
if(isset($_POST['submit'])){
	$fdate = date("Y-m-d",strtotime($_POST['fdate']));
	$groups = $_POST['groups'];
}
?>	
<html>
	<head>
		<title>In-active Customer Balance Report</title>
        <?php include "header_head.php"; ?>
		<link rel="stylesheet" type="text/css"href="reportstyle.css">
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
				text-align: right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<header align="center">
			<table align="center" class="reportheadermenu">
				<?php if((int)$comp_count > 0){ ?>
				<tr>
				<?php
                    if($dlogo_flag > 0) { ?>
                        <td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                    <?php }
                    else{ 
                     ?>
					<td><img src="<?php echo $client_logo; ?>" height="150px"/></td>
					<td><?php echo $cdetails; }?></td>
				</tr>
				<?php } ?>
				<tr>
					<td align="center" colspan="2">
						<h3>In-active Customer Balance Report</h3>
						<label class="reportheaderlabel"><b style="color: green;">Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">Group:</b>&nbsp;<?php if($groups != "all"){ echo $grp_name[$groups]; } else{ echo "All"; } ?></label><br/>
					</td>
				</tr>
			</table>
		</header>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<table class="table1" style="min-width:auto;line-height:23px;">
						<form action="cus_outstandingBalanceWINACReport.php" method="post">
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="5">
										<label class="reportselectionlabel">Date</label>&nbsp;
										<input type="text" name="fdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" readonly />&ensp;&ensp;

										<label class="reportselectionlabel">Group</label>&nbsp;
										<select name="groups" id="groups" class="form-control select2">
											<option value="all" <?php if($groups == "all") { echo 'selected'; } ?> >-All-</option>
											<?php foreach($grp_code as $gcode){ ?><option <?php if($groups == $grp_code[$gcode]) { echo 'selected'; } ?> value="<?php echo $grp_code[$gcode]; ?>"><?php echo $grp_name[$gcode]; ?></option><?php } ?>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						</form>
						<?php
							if(isset($_POST['submit']) == true){
						?>
							<form action="cus_sendsmsINAC.php" method="post" onsubmit="return checkval()">
								<thead class="thead2" style="background-color: #98fb98;">
									<tr>
										<th style="width:50px;">Sl.No.</th>
										<th style="width:50px;">Selection</th>
										<th>Customer Name</th>
										<th>Customer Mobile</th>
										<th>OutStanding</th>
									</tr>
								</thead>
								<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
									<tr>
										<td colspan="5" style="text-align:center;"><input type="checkbox" name="checkall" id="checkall" onchange="checkedall()" /><label class="reportselectionlabel">All Customers</label></td>
									</tr>
								<?php
									$grp_list = ""; if($groups == "all"){ $grp_list = implode("','",$grp_code); } else{ $grp_list = $groups; } $group_filter = " AND `groupcode` IN ('$grp_list')";

									$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%c%'".$group_filter." AND `active` = '0' ORDER BY `name` ASC";
									$query = mysqli_query($conn,$sql); $ven_code = $ven_name = $ven_mobile = $obcramt = $obdramt = array();
									while($row = mysqli_fetch_assoc($query)){
										$ven_code[$row['code']] = $row['code'];
										$ven_name[$row['code']] = $row['name'];
										$ven_mobile[$row['code']] = $row['mobileno'];
										if($row['obtype'] == "Cr"){ $obcramt[$row['code']] = $row['obamt']; $obdramt[$row['code']] = 0; }
										else if($row['obtype'] == "Dr"){ $obdramt[$row['code']] = $row['obamt']; $obcram[$row['code']] = 0; }
										else{ $obdramt[$row['code']] = $obcramt[$row['code']] = 0; }
									}

									$cus_list = ""; $cus_list = implode("','",$ven_code);
									$sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$fdate' AND `customercode` IN ('$cus_list') AND `active` = '1' ORDER BY `date`,`invoice` ASC";
									$query = mysqli_query($conn,$sql); $old_inv = ""; $sale_amt = array();
									while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $sale_amt[$row['customercode']] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } }
									
									$sql = "SELECT * FROM `customer_receipts` WHERE `date` <= '$fdate' AND `ccode` IN ('$cus_list') AND `active` = '1' ORDER BY `date`,`ccode` ASC";
									$query = mysqli_query($conn,$sql); $rct_amt = array();
									while($row = mysqli_fetch_assoc($query)){ $rct_amt[$row['ccode']] += (float)$row['amount']; }

									$sql = "SELECT * FROM `main_crdrnote` WHERE `date` <= '$fdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `date`,`ccode` ASC";
									$query = mysqli_query($conn,$sql); $ccn_amt = $cdn_amt = array();
									while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CCN"){ $ccn_amt[$row['ccode']] += (float)$row['amount']; } else { $cdn_amt[$row['ccode']] += (float)$row['amount']; } }
									
									$sql = "SELECT * FROM `main_mortality` WHERE `date` <= '$fdate' AND `ccode` IN ('$cus_list') AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
									$query = mysqli_query($conn,$sql); $mort_amt = array();
									while($row = mysqli_fetch_assoc($query)){ $mort_amt[$row['ccode']] += (float)$row['amount']; }
									
									$sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$fdate' AND `vcode` IN ('$cus_list') AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
									$query = mysqli_query($conn,$sql); $rtn_amt = array();
									while($row = mysqli_fetch_assoc($query)){ $rtn_amt[$row['vcode']] += (float)$row['amount']; }

									$final_out_bal = $c = 0;
									foreach($ven_code as $vcode){
										if(empty($obcramt[$vcode]) || $obcramt[$vcode] == ""){ $obcramt[$vcode] = 0; }
										if(empty($obdramt[$vcode]) || $obdramt[$vcode] == ""){ $obdramt[$vcode] = 0; }
										if(empty($sale_amt[$vcode]) || $sale_amt[$vcode] == ""){ $sale_amt[$vcode] = 0; }
										if(empty($rct_amt[$vcode]) || $rct_amt[$vcode] == ""){ $rct_amt[$vcode] = 0; }
										if(empty($ccn_amt[$vcode]) || $ccn_amt[$vcode] == ""){ $ccn_amt[$vcode] = 0; }
										if(empty($cdn_amt[$vcode]) || $cdn_amt[$vcode] == ""){ $cdn_amt[$vcode] = 0; }
										if(empty($mort_amt[$vcode]) || $mort_amt[$vcode] == ""){ $mort_amt[$vcode] = 0; }
										if(empty($rtn_amt[$vcode]) || $rtn_amt[$vcode] == ""){ $rtn_amt[$vcode] = 0; }

										$tot_sales = (float)$sale_amt[$vcode] + (float)$cdn_amt[$vcode] + (float)$obdramt[$vcode];
										$tot_receipts = (float)$rct_amt[$vcode] + (float)$ccn_amt[$vcode] + (float)$mort_amt[$vcode] + (float)$rtn_amt[$vcode] + (float)$obcramt[$vcode];
										
										$cus_bal = (float)$tot_sales - (float)$tot_receipts;
										$final_out_bal += (float)$cus_bal;

										if((float)$cus_bal != 0){
											$c++;
											echo "<tr>";
											$cus_details = $vcode."@$&".$ven_name[$vcode]."@$&".$ven_mobile[$vcode]."@$&".$cus_bal."@$&".$fdate;
											echo "<td style='width:50px;'>".$c."</td>";
											echo "<td style='width:50px;'><input type='checkbox' name='c_det[]' id='c_det[]' value='$cus_details' /></td>";
											echo "<td style='width: auto;text-align:left;'>$ven_name[$vcode]</td>";
											if(strlen($ven_mobile[$vcode]) == 10){
												echo "<td><input type='text' name='cmob' id='cmob' class='form-control' style='background:inherit;border:none;' value='$ven_mobile[$vcode]' readonly /></td>";
											}
											else{
												echo "<td><input type='text' name='cmob' id='cmob' class='form-control' style='background:inherit;border:none;color:red;' value='$ven_mobile[$vcode]' readonly /></td>";
											}
											
											echo "<td style='padding-right:5px;text-align:right;'>".number_format_ind($cus_bal)."</td>";
											echo "</tr>";
										}
									}
								?>
									<thead class="thead2" style="background-color: #98fb98;">
										<tr>
											<th colspan="4" style="padding:5px;">Final Outstanding Balance</th>
											<th style='padding-right:5px;text-align:right;'><?php echo number_format_ind($final_out_bal); ?></th>
										</tr>
									</thead>
									<tr align="center">
										<th style="padding:10px;text-align:center;visibility:hidden;"><input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly /></th>
										<th colspan="3" style="padding:10px;text-align:center;"><button type="submit" name="send_msg" id="send_msg" class="btn btn-success btn-md" value="sendsuccess">Send Message</button></th>
										<th style="padding:10px;text-align:center;visibility:hidden;"></th>
									</tr>
								</tbody>
							</form>
						<?php
						}
						?>
					</table>
				</div>
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
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("send_msg").style.visibility = "hidden";
				var l = true;
				var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
				if(checkboxes.length == 0){
					alert("Please select customers");
					l = false;
				}

				if(l == true){
					return true;
				}
				else{
					document.getElementById("send_msg").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
				}
			}
			document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#send_msg').click(); }); } } else{ } });
            
		</script>
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
