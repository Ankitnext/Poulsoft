<?php
    //item_WeightLossReportNew3.php
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
	
	
	$icat_fltr = ""; if($ifwlmb == 1){ $icat_fltr = " AND `description` LIKE '%Birds'"; } else{ $icat_fltr = " AND `description` LIKE '%Broiler Birds%'"; }
	$sql = "SELECT * FROM `item_category` WHERE `active` = '1'".$icat_fltr." ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
	while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

	$icat_list = implode("','",$icat_code);
	$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cat = array();
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cat[$row['code']] = $row['category']; }

	//Sector Details
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $fdate = date("Y-m-d"); $items = "all"; $sectors = "all"; $exoption = "displaypage";
    if(isset($_POST['submit']) == true) {
        $fdate = $tdate = date("Y-m-d",strtotime($_POST['fdate']));
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
				<form action="item_WeightLossReportNew4.php" method="post">
					<?php } else { ?>
					<form action="item_WeightLossReportNew3.php?db=<?php echo $db; ?>" method="post">
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
												<option value="all">-select-</option>
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
									$pdate = date('Y-m-d', strtotime($fdate. ' - 1 days'));
									if($items != "all"){ $item_list = $items; } else{ $item_list = implode("','",$item_code); }
									if($sectors != "all"){ $sec_list = $sectors; } else{ $sec_list = implode("','",$sector_code); }
									$pur_sec = $sale_sec = $item_alist = array();
		
									$sql1 = "SELECT * FROM `pur_purchase` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
									$query1 = mysqli_query($conn,$sql1); $iwp_obds = $iwp_oqty = $iwp_oprc = $iwp_opprc = $iwp_oamt = $iwp_bqty = $iwp_bamt = array();
									while($row1 = mysqli_fetch_assoc($query1)){
										if(strtotime($row1['date']) < strtotime($fdate)){
											$key1 = $row1['itemcode'];
											$iwp_obds[$key1] += (float)$row1['birds'];
											$iwp_oqty[$key1] += (float)$row1['netweight'];
											$iwp_oamt[$key1] += (float)$row1['totalamt'];
											$iwp_oprc[$key1] = (float)$row1['itemprice'];
										}
										else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
										if(strtotime($row1['date']) < strtotime($pdate)){
											$iwp_opprc[$key1] = (float)$row1['itemprice'];
										}
									}
									$sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `towarehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
									$query1 = mysqli_query($conn,$sql1); $iwi_oprc = array();
									while($row1 = mysqli_fetch_assoc($query1)){
										if(strtotime($row1['date']) < strtotime($fdate)){
											$key1 = $row1['code'];
											$iwp_obds[$key1] += (float)$row1['birds'];
											$iwp_oqty[$key1] += (float)$row1['quantity'];
											$iwp_oamt[$key1] += ((float)$row1['quantity'] * (float)$row1['price']);
											$iwi_oprc[$key1] = (float)$row1['price'];
										}
										else{ $item_alist[$row1['code']] = $row1['code']; }
									}
									$sql1 = "SELECT * FROM `customer_sales` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
									$query1 = mysqli_query($conn,$sql1); $iws_obds = $iws_oqty = $iws_oamt = $iws_bqty = $iws_bamt = array();
									while($row1 = mysqli_fetch_assoc($query1)){
										if(strtotime($row1['date']) < strtotime($fdate)){
											$key1 = $row1['itemcode'];
											$iws_obds[$key1] += (float)$row1['birds'];
											$iws_oqty[$key1] += (float)$row1['netweight'];
											$iws_oamt[$key1] += (float)$row1['totalamt'];
										}
										else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
									}
									$sql1 = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$item_list') AND `fromwarehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`code`,`trnum` ASC";
									$query1 = mysqli_query($conn,$sql1); $iwi_oprc = array();
									while($row1 = mysqli_fetch_assoc($query1)){
										if(strtotime($row1['date']) < strtotime($fdate)){
											$key1 = $row1['code'];
											$iws_obds[$key1] += (float)$row1['birds'];
											$iws_oqty[$key1] += (float)$row1['quantity'];
											$iws_oamt[$key1] += ((float)$row1['quantity'] * (float)$row1['price']);
										}
										else{ $item_alist[$row1['code']] = $row1['code']; }
									}
									//Stock Adjustment
									$sql1 = "SELECT * FROM `item_stock_adjustment` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`trnum` ASC";
									$query1 = mysqli_query($conn, $sql1); $iwsa_aobds = $iwsa_dobds = $iwsa_aoqty = $iwsa_doqty = $iwsa_aoamt = $iwsa_doamt = array();
									while($row1 = mysqli_fetch_assoc($query1)){
										$key1 = $row1['itemcode'];
										if(strtotime($row1['date']) < strtotime($fdate)){
											if($row1['a_type'] == "add"){
												$iwsa_aobds[$key1] += (float)$row1['birds'];
												$iwsa_aoqty[$key1] += (float)$row1['nweight'];
												$iwsa_aoamt[$key1] += (float)$row1['amount'];
											}
											else if($row1['a_type'] == "deduct"){
												$iwsa_dobds[$key1] += (float)$row1['birds'];
												$iwsa_doqty[$key1] += (float)$row1['nweight'];
												$iwsa_doamt[$key1] += (float)$row1['amount'];
											}
										}
										else{ $item_alist[$row1['itemcode']] = $row1['itemcode']; }
									}
									//Mortality
									$sql1 = "SELECT * FROM `main_mortality` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `ccode` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`code` ASC";
									$query1 = mysqli_query($conn, $sql1); $iwm_obds = $iwm_oqty = $iwm_oamt = array();
									while($row1 = mysqli_fetch_assoc($query1)){
										$key1 = $row1['itemcode'];
										if(strtotime($row1['date']) < strtotime($fdate)){
											$iwm_obds[$key1] += (float)$row1['birds'];
											$iwm_oqty[$key1] += (float)$row1['quantity'];
											$iwm_oamt[$key1] += (float)$row1['amount'];
										}
										else{ }
									}
		
									//Opening Calculations
									$opn_bdss = $opn_qtyy = $opn_amts = array();
									foreach($item_alist as $icode){
										$op_bds = 0; if(!empty($iwp_obds[$icode]) && $iwp_obds[$icode] != ""){ $op_bds = $iwp_obds[$icode]; }
										$os_bds = 0; if(!empty($iws_obds[$icode]) && $iws_obds[$icode] != ""){ $os_bds = $iws_obds[$icode]; }
										$oa_bds = 0; if(!empty($iwsa_aobds[$icode]) && $iwsa_aobds[$icode] != ""){ $oa_bds = $iwsa_aobds[$icode]; }
										$od_bds = 0; if(!empty($iwsa_dobds[$icode]) && $iwsa_dobds[$icode] != ""){ $od_bds = $iwsa_dobds[$icode]; }
										$om_bds = 0; if(!empty($iwm_obds[$icode]) && $iwm_obds[$icode] != ""){ $om_bds = $iwm_obds[$icode]; }
										$ob_bds = 0; $ob_bds = round((((float)$op_bds + (float)$oa_bds) - ((float)$os_bds + (float)$od_bds + (float)$om_bds)),2);
										$opn_bdss[$icode] += (float)$ob_bds;
										$opn_bds = $opn_bdss[$icode];
		
										$op_qty = 0; if(!empty($iwp_oqty[$icode]) && $iwp_oqty[$icode] != ""){ $op_qty = $iwp_oqty[$icode]; }
										$os_qty = 0; if(!empty($iws_oqty[$icode]) && $iws_oqty[$icode] != ""){ $os_qty = $iws_oqty[$icode]; }
										$oa_qty = 0; if(!empty($iwsa_aoqty[$icode]) && $iwsa_aoqty[$icode] != ""){ $oa_qty = $iwsa_aoqty[$icode]; }
										$od_qty = 0; if(!empty($iwsa_doqty[$icode]) && $iwsa_doqty[$icode] != ""){ $od_qty = $iwsa_doqty[$icode]; }
										$om_qty = 0; if(!empty($iwm_oqty[$icode]) && $iwm_oqty[$icode] != ""){ $om_qty = $iwm_oqty[$icode]; }
										$ob_qty = round(((float)$op_qty + (float)$oa_qty) - ((float)$os_qty + (float)$od_qty + (float)$om_qty), 2);
										$opn_qtyy[$icode] += (float)$ob_qty;
										$bird_qty = $opn_qtyy[$icode];
		
										$op_amt = 0; if(!empty($iwp_oamt[$icode]) && $iwp_oamt[$icode] != ""){ $op_amt = $iwp_oamt[$icode]; }
										$os_amt = 0; if(!empty($iws_oamt[$icode]) && $iws_oamt[$icode] != ""){ $os_amt = $iws_oamt[$icode]; }
										$oa_amt = 0; if(!empty($iwsa_aoamt[$icode]) && $iwsa_aoamt[$icode] != ""){ $oa_amt = $iwsa_aoamt[$icode]; }
										$od_amt = 0; if(!empty($iwsa_doamt[$icode]) && $iwsa_doamt[$icode] != ""){ $od_amt = $iwsa_doamt[$icode]; }
										$om_amt = 0; if(!empty($iwm_oamt[$icode]) && $iwm_oamt[$icode] != ""){ $om_amt = $iwm_oamt[$icode]; }
										$ob_amt = 0; $ob_amt = round((((float)$op_amt + (float)$oa_amt) - ((float)$os_amt + (float)$od_amt + (float)$om_amt)),2);
										$bird_amt = $ob_amt;

										  // $birds = $opn_bds[$icode]; if($birds == ""){ $birds = 0; }
                                        $opn_qty = $nweight = $opn_qtyy[$icode]; if($nweight == ""){ $nweight = 0; }
										$opn_prc = $price = $iwp_opprc[$icode]; if($price == ""){ $price = 0; }
										$amount = ((float)$price * (float)$nweight); if($amount == ""){ $amount = 0; }
                                
										$opn_amt = $opn_amts[$icode] += (float)$amount;

										// $birds = round((((float)$opn_bdss[$icode] + (float)$iwsi_bbds[$icode] + (float)$iwsa_abbds[$icode]) - ((float)$iwm_bbds[$icode] + (float)$iwso_bbds[$icode] + (float)$iwsa_dbbds[$icode])),2); if($birds == ""){ $birds = 0; }
										// $nweight = round((((float)$opn_qtyy[$icode] + (float)$iwsi_bqty[$icode] + (float)$iwsa_abqty[$icode]) - ((float)$iwm_bqty[$icode] + (float)$iwso_bqty[$icode] + (float)$iwsa_dbqty[$icode])),2); if($nweight == ""){ $nweight = 0; }
										// $price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
										// $amount = ((float)$price * (float)$nweight);
										// //$amount = round(((float)$iwso_bamt[$icode] - ((float)$opn_amt[$icode] + (float)$iwsi_bamt[$icode] - (float)$iwm_bamt[$icode])),2); if($amount == ""){ $amount = 0; }
										// $cls_amt[$icode] += (float)$amount;
									}

									//Purchases
									$sql1 = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
									$query1 = mysqli_query($conn,$sql1); $iwsi_bbds = $iwsi_bqty = $iwsi_bamt = array();
									while($row1 = mysqli_fetch_assoc($query1)){
										$i++;
										$cname = $ven_name[$row1['vendorcode']];
										$iname = $item_name[$row1['itemcode']];
										$jals = $row1['jals']; if($jals == ""){ $jals = 0; }
										$birds = $row1['birds']; if($birds == ""){ $birds = 0; }
										$tweight = $row1['totalweight']; if($tweight == ""){ $tweight = 0; }
										$eweight = $row1['emptyweight']; if($eweight == ""){ $eweight = 0; }
										$nweight = $row1['netweight']; if($nweight == ""){ $nweight = 0; }
										$price = $row1['itemprice']; if($price == ""){ $price = 0; }
										$amount = $row1['totalamt']; if($amount == ""){ $amount = 0; }
										$avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }
		
										$fti_jals[$row1['itemcode']] += (float)$jals;
										$fti_bds[$row1['itemcode']] += (float)$birds;
										$fti_twt[$row1['itemcode']] += (float)$tweight;
										$fti_ewt[$row1['itemcode']] += (float)$eweight;
										$fti_nwt[$row1['itemcode']] += (float)$nweight;
										$fti_amt[$row1['itemcode']] += (float)$amount;
		
										$iwsi_bbds[$row1['itemcode']] += (float)$birds;
										$iwsi_bqty[$row1['itemcode']] += (float)$nweight;
										$iwsi_bamt[$row1['itemcode']] += (float)$amount;
									}
									 //Stock Adjustment
									 $sql1 = "SELECT * FROM `item_stock_adjustment` WHERE `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`trnum` ASC";
									 $query1 = mysqli_query($conn, $sql1); $iwsa_abbds = $iwsa_dbbds = $iwsa_abqty = $iwsa_dbqty = $iwsa_abamt = $iwsa_dbamt = array();
									 while($row1 = mysqli_fetch_assoc($query1)){
										 $key1 = $row1['itemcode'];
										 if(strtotime($row1['date']) < strtotime($fdate)){ }
										 else{
											 if($row1['a_type'] == "add"){
												 $iwsa_abbds[$key1] += (float)$row1['birds'];
												 $iwsa_abqty[$key1] += (float)$row1['nweight'];
												 $iwsa_abamt[$key1] += (float)$row1['amount'];
											 }
											 else if($row1['a_type'] == "deduct"){
												 $iwsa_dbbds[$key1] += (float)$row1['birds'];
												 $iwsa_dbqty[$key1] += (float)$row1['nweight'];
												 $iwsa_dbamt[$key1] += (float)$row1['amount'];
											 }
										 }
									 }
									 //Mortality
									 $sql1 = "SELECT * FROM `main_mortality` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `ccode` IN ('$sec_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`itemcode`,`code` ASC";
									 $query1 = mysqli_query($conn, $sql1); $iwm_bbds = $iwm_bqty = $iwm_bamt = array();
									 while($row1 = mysqli_fetch_assoc($query1)){
										 $key1 = $row1['itemcode'];
										 if(strtotime($row1['date']) < strtotime($fdate)){ }
										 else{
											 $key1 = $row1['itemcode'];
											 $iwm_bbds[$key1] += (float)$row1['birds'];
											 $iwm_bqty[$key1] += (float)$row1['quantity'];
											 $iwm_bamt[$key1] += (float)$row1['amount'];
										 }
									 }

									 $fto_jals = $fto_bds = $fto_twt = $fto_ewt = $fto_nwt = $fto_amt = array();
									 //Sales
									 $sql1 = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`itemcode`,`invoice` ASC";
									 $query1 = mysqli_query($conn,$sql1); $j = 0; $iwso_bbds = $iwso_bqty = $iwso_bamt = array();
									 while($row1 = mysqli_fetch_assoc($query1)){
										 $j++;
										 $cname = $ven_name[$row1['customercode']];
										 $iname = $item_name[$row1['itemcode']];
										 $jals = $row1['jals']; if($jals == ""){ $jals = 0; }
										 $birds = $row1['birds']; if($birds == ""){ $birds = 0; }
										 $tweight = $row1['totalweight']; if($tweight == ""){ $tweight = 0; }
										 $eweight = $row1['emptyweight']; if($eweight == ""){ $eweight = 0; }
										 $nweight = $row1['netweight']; if($nweight == ""){ $nweight = 0; }
										 $price = $row1['itemprice']; if($price == ""){ $price = 0; }
										 $amount = $row1['totalamt']; if($amount == ""){ $amount = 0; }
										 $avg_wt = 0; if((float)$birds != 0){ $avg_wt = round(((float)$nweight / (float)$birds),2); }
		 
										 $fto_jals[$row1['itemcode']] += (float)$jals;
										 $fto_bds[$row1['itemcode']] += (float)$birds;
										 $fto_twt[$row1['itemcode']] += (float)$tweight;
										 $fto_ewt[$row1['itemcode']] += (float)$eweight;
										 $fto_nwt[$row1['itemcode']] += (float)$nweight;
										 $fto_amt[$row1['itemcode']] += (float)$amount;
		 
										 $iwso_bbds[$row1['itemcode']] += (float)$birds;
										 $iwso_bqty[$row1['itemcode']] += (float)$nweight;
										 $iwso_bamt[$row1['itemcode']] += (float)$amount;
									 }

									 
									 foreach($item_alist as $icode){
										// Closing
										$cls_bds = $birds = round((((float)$opn_bdss[$icode] + (float)$iwsi_bbds[$icode] + (float)$iwsa_abbds[$icode]) - ((float)$iwm_bbds[$icode] + (float)$iwso_bbds[$icode] + (float)$iwsa_dbbds[$icode])),2); if($birds == ""){ $birds = 0; }
										$cls_qty = $nweight = round((((float)$opn_qtyy[$icode] + (float)$iwsi_bqty[$icode] + (float)$iwsa_abqty[$icode]) - ((float)$iwm_bqty[$icode] + (float)$iwso_bqty[$icode] + (float)$iwsa_dbqty[$icode])),2); if($nweight == ""){ $nweight = 0; }
										$price = $iwp_oprc[$icode]; if($price == ""){ $price = 0; }
										$amount = ((float)$price * (float)$nweight);
										//$amount = round(((float)$iwso_bamt[$icode] - ((float)$opn_amt[$icode] + (float)$iwsi_bamt[$icode] - (float)$iwm_bamt[$icode])),2); if($amount == ""){ $amount = 0; }
									    $cls_amt =	$cls_amts[$icode] += (float)$amount;

										// Stock Adjustment 
										$stk_bds = ((float)$iwsa_abbds[$icode] - (float)$iwsa_dbbds[$icode]); if($stk_bds == ""){ $stk_bds = 0; }
										$stk_qty = ((float)$iwsa_abqty[$icode] - (float)$iwsa_dbqty[$icode]); if($stk_qty == ""){ $stk_qty = 0; }
										$stk_amt = ((float)$iwsa_abamt[$icode] - (float)$iwsa_dbamt[$icode]); if($stk_amt == ""){ $stk_amt = 0; }
										
									}
									//old
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
                                    // $sql = "SELECT * FROM `item_closingstock` WHERE `date` ='$pdate' AND `code` = '$items'".$sector_filter1." AND `active` = '1'";
                                    // $query = mysqli_query($conn,$sql); $opn_bds = $opn_qty = $opn_prc = $opn_amt = 0;
                                    // while($row = mysqli_fetch_assoc($query)){
                                    //    // $opn_bds = (float)$row['closedbirds'];
                                    //     $opn_bds = (float)$birds;
                                    //    // $opn_qty = (float)$row['closedquantity'];
                                    //     $opn_qty = (float)$bird_qty;
                                    //     //$opn_prc = (float)$row['price'];
                                    //     $opn_prc = (float)$price;
                                    //     //$opn_amt = ((float)$row['closedquantity'] * (float)$row['price']);
                                    //     $opn_amt = (float)$op_amtt;
                                    // }
                                    
                                    // $sql = "SELECT * FROM `item_closingstock` WHERE `date` ='$fdate' AND `code` = '$items'".$sector_filter1." AND `active` = '1'";
                                    // $query = mysqli_query($conn,$sql); $cls_bds = $cls_qty = $cls_prc = $cls_amt = 0;
                                    // while($row = mysqli_fetch_assoc($query)){
                                    //     $cls_bds = (float)$row['closedbirds'];
                                    //     $cls_qty = (float)$row['closedquantity'];
                                    //     $cls_prc = (float)$row['price'];
                                    //     $cls_amt = ((float)$row['closedquantity'] * (float)$row['price']);item_list
                                    // }
                                    
                                    $sql = "SELECT * FROM `pur_purchase` WHERE `date` ='$fdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $pur_bds = $pur_qty = $pur_prc = $pur_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $pur_bds += (float)$row['birds'];
                                        $pur_qty += (float)$row['netweight'];
                                        $pur_prc = (float)$row['itemprice'];
                                        $pur_amt += (float)$row['totalamt'];
                                    }
                                    
                                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` ='$fdate' AND `code` IN ('$item_list')".$sector_filter2." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $tin_bds = $tin_qty = $tin_prc = $tin_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $tin_bds += (float)$row['birds'];
                                        $tin_qty += (float)$row['quantity'];
                                        $tin_prc = (float)$row['price'];
                                        $tin_amt += ((float)$row['quantity'] * (float)$row['price']);
                                    }
                                    
                                    $sql = "SELECT * FROM `customer_sales` WHERE `date` ='$fdate' AND `itemcode` IN ('$item_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $inv_bds = $inv_qty = $inv_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $inv_bds += (float)$row['birds'];
                                        $inv_qty += (float)$row['netweight'];
                                        $inv_amt += (float)$row['totalamt'];
                                    }
                                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` ='$fdate' AND `code` IN ('$item_list')".$sector_filter3." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
                                    $query = mysqli_query($conn,$sql); $tou_bds = $tou_qty = $tou_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $tou_bds += (float)$row['birds'];
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
                                    $sql = "SELECT * FROM `acc_vouchers` WHERE `date` ='$fdate' AND `prefix` ='PV' AND `tcoa` IN ('$coa_list')AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql); $pv_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $pv_amt += (float)$row['amount'];
                                    }

                                    $sql = "SELECT * FROM `acc_vouchers` WHERE `date` ='$fdate' AND `prefix` ='RV' AND `fcoa` IN ('$coa_list') AND `warehouse` IN ('$sec_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql); $rv_amt = 0;
                                    while($row = mysqli_fetch_assoc($query)){
                                        $rv_amt += (float)$row['rvamt'];
                                    }
                                    $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$items')"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $iname = $row['description']; }

                                    $tpur_bds = ((float)$pur_bds + (float)$tin_bds);
                                    $tpur_qty = ((float)$pur_qty + (float)$tin_qty);
                                    $tpur_amt = ((float)$pur_amt + (float)$tin_amt);
                                    $tinv_bds = ((float)$inv_bds + (float)$tou_bds);
                                    $tinv_qty = ((float)$inv_qty + (float)$tou_qty);
                                    $tinv_amt = ((float)$inv_amt + (float)$tou_amt);

                                    $acls_bds = (float)$cls_bds;
                                    $acls_qty = (float)$cls_qty;
                                    $acls_amt = (float)$cls_amt;
                                    if((float)$cls_prc > 0){ $acls_prc = (float)$cls_prc; }
                                    else if((float)$pur_prc > 0){ $acls_prc = (float)$pur_prc; }
                                    else if((float)$opn_prc > 0){ $acls_prc = (float)$opn_prc; }
                                    else if((float)$tin_prc > 0){ $acls_prc = (float)$tin_prc; }
                                    else{ $acls_prc = 0; }

                                    $tcls_bds = (((float)$opn_bds + (float)$tpur_bds) - (float)$tinv_bds);
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
                                <tr><th class="heading_center">Item</th><th colspan="3" class="heading_center"><?php echo $iname; ?></th></tr>
                                <tr><th class="heading_center"></th><th class="heading_center">Birds</th><th class="heading_center">Quantity</th><th class="heading_center">Amount</th></tr>
                                <tr><th>Opening</th><th class="input_num"><?php echo str_replace(".00","",number_format_ind($opn_bds)); ?></th><th class="input_num"><?php echo number_format_ind($opn_qty); ?></th><th class="input_num"><?php echo number_format_ind($opn_amt); ?></th></tr>
                                <tr><th>Purchases</th><th class="input_num"><?php echo str_replace(".00","",number_format_ind($tpur_bds)); ?></th><th class="input_num"><?php echo number_format_ind($tpur_qty); ?></th><th class="input_num"><?php echo number_format_ind($tpur_amt); ?></th></tr>
                                <tr><th>Sales</th><th class="input_num"><?php echo str_replace(".00","",number_format_ind($tinv_bds)); ?></th><th class="input_num"><?php echo number_format_ind($tinv_qty); ?></th><th class="input_num"><?php echo number_format_ind($tinv_amt); ?></th></tr>
                                <!-- <tr><th>Closing</th><th class="input_num"><?php echo str_replace(".00","",number_format_ind($tcls_bds)); ?></th><th class="input_num"><?php echo number_format_ind($tcls_qty); ?></th><th class="input_num"><?php echo number_format_ind($tcls_amt); ?></th></tr> -->
                                <tr><th>Closing</th><th class="input_num"><?php echo str_replace(".00","",number_format_ind($acls_bds)); ?></th><th class="input_num"><?php echo number_format_ind($acls_qty); ?></th><th class="input_num"><?php echo number_format_ind($acls_amt); ?></th></tr>
                                <tr><th>Stock Adjustment</th><th class="input_num"><?php echo str_replace(".00","",number_format_ind($stk_bds)); ?></th><th class="input_num"><?php echo number_format_ind($stk_qty); ?></th><th class="input_num"><?php echo number_format_ind($stk_amt); ?></th></tr>
                                <tr><th>Weight Loss (%)</th><th class="input_num"></th><th class="input_num"><?php echo number_format_ind($wht_qty)." (".$wht_per."%)"; ?></th><th class="input_num"><?php echo number_format_ind($wht_amt); ?></th></tr>
                                <!-- <tr><th>Sale Avg.</th><th class="input_num"></th><th class="input_num"></th><th class="input_num"><?php echo number_format_ind($sale_avg); ?></th></tr>
                                <tr><th>Purchase Avg.</th><th class="input_num"></th><th class="input_num"></th><th class="input_num"><?php echo number_format_ind($pur_avg); ?></th></tr>
                                <tr><th>Expense</th><th class="input_num"></th><th></th><th class="input_num"><?php echo number_format_ind($exp_amt); ?></th></tr> -->
                                <tr><th>Profit/Losss</th><th class="input_num"></th><th></th><th class="input_num"><?php echo number_format_ind($mrg_amt); ?></th></tr>
                                <!-- <tr><th>Profit per Kg</th><th class="input_num"></th><th></th><th class="input_num"><?php echo number_format_ind($ppk_prc); ?></th></tr> -->
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
