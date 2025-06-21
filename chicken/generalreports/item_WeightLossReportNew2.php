<?php
    //item_WeightLossReportNew2.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; } else{ $db = ''; }
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php"; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; }
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemcodes[$row['code']] = $row['code']; $itemnames[$row['code']] = $row['description']; }
	
	$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Date wise Weight Loss' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
	
	$sql = "SELECT * FROM `inv_sectors` WHERE `flag` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $officecode[$row['code']] = $row['code']; $officename[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $ifwlmba = $row['wlmba']; }
	
    $fdate = date("Y-m-d"); $items = "select"; $sectors = "all"; $exoption = "displaypage";
    if(isset($_POST['submit']) == true) {
        $fdate = date("Y-m-d",strtotime($_POST['fdate']));
        $items = $_POST['items'];
        $sectors = $_POST['sectors'];
        //$exoption = $_POST['export'];
    }
    
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<?php
			if($exoption == "exportexcel") {
				echo header("Content-type: application/xls");
				echo header("Content-Disposition: attachment; filename=item_WeightLossReportNew($fdate).xls");
				echo header("Pragma: no-cache"); echo header("Expires: 0");
			}
		?>
		<style>
			body{
				overflow: auto;
			}
			.contentmenu,.contentmenu thead,.contentmenu tr,.contentmenu th,.contentmenu td {
				font-size: 14px;
				border: 1px solid black;
				border-collapse: collapse;
			}
			.contentmenu {
				width: auto;
			}
			.contentmenu thead {
				text-align:center;
				font-weight:bold;
			}
			.contentmenu td {
				padding: 2px;
			}
			.input_num {
                padding-right:5px;
				text-align:right;
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
            .heading_center{
                text-align:center;
                background-color: #98fb98;
            }
		</style>
	</head>
	<body>
		<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
			<header align="center">
				<table align="center" class="reportheadermenu">
					<tr>
					<?php
						$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
						while($row = mysqli_fetch_assoc($query)){ ?>
						<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
						<td><?php echo $row['cdetails']; ?></td> <?php } ?>
						<td align="center">
							<h3>Weight Loss Report</h3>
							<label class="reportheaderlabel"><b style="color: green;"> Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>
						</td>
						<td>
						
						</td>
					</tr>
				</table>
			</header>
		<?php } ?>
			<section class="content" align="center">
				<div class="col-md-18" align="center">
				<?php if($db == ''){?>
				<form action="item_WeightLossReportNew2.php" method="post">
					<?php } else { ?>
					<form action="item_WeightLossReportNew2.php?db=<?php echo $db; ?>" method="post">
					<?php } ?>
						<table class="contentmenu">
							<?php if($exoption == "displaypage") { ?>
							<thead style="padding:15px; text-align:left;background-color: #98fb98;">
								<tr>
									<td colspan='33'>&ensp;
										<label class="reportselectionlabel">Date</label>&nbsp;
										<input type="text" name="fdate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:100px;" />&ensp;&ensp;
										<?php if($ifwlmba == 1){ ?>		
											<label class="reportselectionlabel">Item</label>&nbsp;
											<select name="items" id="items" class="form-control select2">
												<option value="select">-select-</option>
												<?php
													$icats = $icode = ""; $c = 0; if($ifwlmba == 0){ $icname = 'Broiler Birds'; } else { $icname = '%Birds'; }
													$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '$icname'";
                                                    $query = mysqli_query($conn,$sql);$counta = mysqli_num_rows($query);
													if($counta > 0){
														while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
													}else{
														$sql = "SELECT * FROM `item_category` WHERE `description` LIKE 'egg%'"; $query = mysqli_query($conn,$sql);$counta = mysqli_num_rows($query);
														while($row = mysqli_fetch_assoc($query)){ if($icats == ""){ $icats = "'".$row['code']."'"; } else { $icats = $icats.",'".$row['code']."'"; } }
													}
                                                    
													$sql = "SELECT * FROM `item_details` WHERE `category` IN ($icats)"; $query = mysqli_query($conn,$sql);
													while($row = mysqli_fetch_assoc($query)){
												?>
														<option <?php if($items == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
												<?php
													}
												?>
											</select>&ensp;&ensp;
										<?php } ?>		
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="sectors" id="sectors" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($sectors == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Submit</button>
									</td>
								</tr>
							</thead>
							<?php } ?>
							<?php if($exoption == "exportexcel") { ?>
							<thead>
								<tr>
									<td colspan='20' style='text-align:center;font-weight:bold;font-size:18px;color:red;background-color: #98fb98;'>
										Weight Loss Report for <?php echo $officename[$wname]; ?> on <?php echo $fdate; ?>
									</td>
								</tr>
							</thead>
							<?php } ?>
							<tbody>
								<?php
								if(isset($_POST['submit']) == true){
                                    $pdate = date('Y-m-d', strtotime($fdate.'-1 days'));
                                    if($sectors == "all"){
                                        $sector_filter1 = "";
                                        $sector_filter2 = "";
                                        $sector_filter3 = "";
                                    }
                                    else{
                                        $sector_filter1 = " AND `warehouse` = '$sectors'";
                                        $sector_filter2 = " AND `towarehouse` = '$sectors'";
                                        $sector_filter3 = " AND `fromwarehouse` = '$sectors'";
                                    }
                                    $sql = "SELECT * FROM `item_closingstock` WHERE `date` ='$pdate' AND `code` = '$items'".$sector_filter1." AND `active` = '1'";
                                    $query = mysqli_query($conn,$sql); $opn_qty = $opn_prc = $opn_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $opn_qty = (float)$row['closedquantity'];
                                        $opn_prc = (float)$row['price'];
                                        $opn_amt = ((float)$row['closedquantity'] * (float)$row['price']);
                                    }
                                    
                                    $sql = "SELECT * FROM `item_closingstock` WHERE `date` ='$fdate' AND `code` = '$items'".$sector_filter1." AND `active` = '1'";
                                    $query = mysqli_query($conn,$sql); $cls_qty = $cls_prc = $cls_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $cls_qty = (float)$row['closedquantity'];
                                        $cls_prc = (float)$row['price'];
                                        $cls_amt = ((float)$row['closedquantity'] * (float)$row['price']);
                                    }
                                    
                                    $sql = "SELECT * FROM `pur_purchase` WHERE `date` ='$fdate' AND `itemcode` = '$items'".$sector_filter1." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $pur_qty = $pur_prc = $pur_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $pur_qty += (float)$row['netweight'];
                                        $pur_prc = (float)$row['itemprice'];
                                        $pur_amt += (float)$row['totalamt'];
                                    }
                                    
                                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` ='$fdate' AND `code` = '$items'".$sector_filter2." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $tin_qty = $tin_prc = $tin_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $tin_qty += (float)$row['quantity'];
                                        $tin_prc = (float)$row['price'];
                                        $tin_amt += ((float)$row['quantity'] * (float)$row['price']);
                                    }
                                    
                                    $sql = "SELECT * FROM `customer_sales` WHERE `date` ='$fdate' AND `itemcode` = '$items'".$sector_filter1." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $inv_qty = $inv_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $inv_qty += (float)$row['netweight'];
                                        $inv_amt += (float)$row['totalamt'];
                                    }
                                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` ='$fdate' AND `code` = '$items'".$sector_filter3." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $tou_qty = $tou_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $tou_qty += (float)$row['quantity'];
                                        $tou_amt += ((float)$row['quantity'] * (float)$row['price']);
                                    }
                                    
                                    $sch_code = $ecoa_code = array();
                                    $sql = "SELECT * FROM `acc_schedules` WHERE `subtype` LIKE 'COA-0003'"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $sch_code[$row['code']] = $row['code']; }

                                    $sch_list = implode("','",$sch_code);
                                    $sql = "SELECT * FROM `acc_coa` WHERE `schedules` IN ('$sch_list')"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $ecoa_code[$row['code']] = $row['code']; }

                                    $coa_list = implode("','",$ecoa_code);
                                    $sql = "SELECT * FROM `acc_vouchers` WHERE `date` ='$fdate' AND `prefix` ='PV' AND `tcoa` IN ('$coa_list')".$sector_filter1." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql); $pv_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $pv_amt += (float)$row['amount'];
                                    }

                                    $sql = "SELECT * FROM `acc_vouchers` WHERE `date` ='$fdate' AND `prefix` ='RV' AND `fcoa` IN ('$coa_list')".$sector_filter1." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql); $rv_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $rv_amt += (float)$row['rvamt'];
                                    }
                                    $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$items')"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $iname = $row['description']; }

                                    $tpur_qty = ((float)$pur_qty + (float)$tin_qty);
                                    $tpur_amt = ((float)$pur_amt + (float)$tin_amt);
                                    $tinv_qty = ((float)$inv_qty + (float)$tou_qty);
                                    $tinv_amt = ((float)$inv_amt + (float)$tou_amt);

                                    $acls_qty = (float)$cls_qty;
                                    $acls_amt = (float)$cls_amt;
                                    if((float)$cls_prc > 0){ $acls_prc = (float)$cls_prc; }
                                    else if((float)$pur_prc > 0){ $acls_prc = (float)$pur_prc; }
                                    else if((float)$opn_prc > 0){ $acls_prc = (float)$opn_prc; }
                                    else if((float)$tin_prc > 0){ $acls_prc = (float)$tin_prc; }
                                    else{ $acls_prc = 0; }

                                    $tcls_qty = (((float)$opn_qty + (float)$tpur_qty) - (float)$tinv_qty);
                                    $tcls_amt = ((float)$tcls_qty * (float)$acls_prc);

                                    $exp_amt = ((float)$pv_amt - (float)$rv_amt);

                                    $wht_qty = ((float)$opn_qty + (float)$tpur_qty) - ((float)$tinv_qty + (float)$acls_qty);
                                    $wht_amt = ((float)$wht_qty * (float)$acls_prc);

									 $chkzero = (int)$tpur_qty + (int)$opn_qty;
                                 
                                    if( $chkzero != 0 && $chkzero > 0 ){ $wht_per = round((((float)$wht_qty / ((float)$tpur_qty + (float)$opn_qty)) * 100),2); } else{ $wht_per = 0; }

                                   // if(((float)$tpur_qty + (float)$opn_qty) > 0 && ((float)$tpur_qty + (float)$opn_qty) != 0 ){ $wht_per = round((((float)$wht_qty / (float)$tpur_qty + (float)$opn_qty) * 100),2); } else{ $wht_per = 0; }

                                    $mrg_amt = ((float)$tinv_amt + (float)$acls_amt) - ((float)$opn_amt + (float)$tpur_amt) - ((float)$exp_amt);

                                    if((float)$tinv_qty != 0){ $sale_avg = round(((float)$tinv_amt / $tinv_qty),2); } else{ $sale_avg = 0; }
                                    if((float)$tpur_qty != 0){ $pur_avg = round((((float)$tpur_amt + (float)$exp_amt) / $tpur_qty),2); } else{ $pur_avg = 0; }

                                    if((float)$tinv_qty != 0){ $ppk_prc = round(((float)$mrg_amt / (float)$tinv_qty),2); } else{ $ppk_prc = 0; }
                                ?>
                                <tr><th class="heading_center">Sl No.</th><th class="heading_center">Item</th><th colspan="2" class="heading_center"><?php echo $iname; ?></th></tr>
                                <tr><th class="heading_center"></th><th class="heading_center"></th><th class="heading_center">Quantity</th><th class="heading_center">Amount</th></tr>
                                <tr><th>1</th><th>Opening</th><th class="input_num"><?php echo number_format_ind($opn_qty); ?></th><th class="input_num"><?php echo number_format_ind($opn_amt); ?></th></tr>
                                <tr><th>2</th><th>Purchases</th><th class="input_num"><?php echo number_format_ind($tpur_qty); ?></th><th class="input_num"><?php echo number_format_ind($tpur_amt); ?></th></tr>
                                <tr><th>3</th><th>Sales</th><th class="input_num"><?php echo number_format_ind($tinv_qty); ?></th><th class="input_num"><?php echo number_format_ind($tinv_amt); ?></th></tr>
                                <tr><th>4</th><th>Closing</th><th class="input_num"><?php echo number_format_ind($tcls_qty); ?></th><th class="input_num"><?php echo number_format_ind($tcls_amt); ?></th></tr>
                                <tr><th>5</th><th>Actual Closing</th><th class="input_num"><?php echo number_format_ind($acls_qty); ?></th><th class="input_num"><?php echo number_format_ind($acls_amt); ?></th></tr>
                                <tr><th>6</th><th>Weight Loss (%)</th><th class="input_num"><?php echo number_format_ind($wht_qty)." (".$wht_per."%)"; ?></th><th class="input_num"><?php echo number_format_ind($wht_amt); ?></th></tr>
                                <tr><th>7</th><th>Sale Avg.</th><th class="input_num"></th><th class="input_num"><?php echo number_format_ind($sale_avg); ?></th></tr>
                                <tr><th>8</th><th>Purchase Avg.</th><th class="input_num"></th><th class="input_num"><?php echo number_format_ind($pur_avg); ?></th></tr>
                                <tr><th>9</th><th>Expense</th><th></th><th class="input_num"><?php echo number_format_ind($exp_amt); ?></th></tr>
                                <tr><th>10</th><th>Margin</th><th></th><th class="input_num"><?php echo number_format_ind($mrg_amt); ?></th></tr>
                                <tr><th>11</th><th>Profit per Kg</th><th></th><th class="input_num"><?php echo number_format_ind($ppk_prc); ?></th></tr>
                                <?php
                                }
								?>
							</tbody>
						</table>
					</form>
				</div>
			</section>
	
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	<?php include "header_foot.php"; ?>
</html>
