<?php
	//main_stock_report.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
    $requested_data = json_decode(file_get_contents('php://input'),true);

	if(!isset($_SESSION)){ session_start(); }
	if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_SESSION['dbase'] =  $_GET['db']; } else { $db = ''; }
	if($db == ''){
		include "../newConfig.php";
		include "number_format_ind.php"; 
		include "header_head.php"; 
        $pageLink = "main_stock_report.php";
	}
	else{
		
		include "APIconfig.php";
		include "number_format_ind.php";
		include "header_head_new.php";
        $pageLink = "main_stock_report.php?db=".$db;
	}

	$cid = $_GET['cid'];
	$today = date("Y-m-d");

    /*Check for Table Availability*/
    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
    if(in_array("item_stock_adjustment", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stock_adjustment LIKE poulso6_admin_chickenmaster.item_stock_adjustment;"; mysqli_query($conn,$sql1); }
	
	$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $icat_name[$row['code']] = $row['description']; $icat_code[$row['code']] = $row['code']; }

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_code[$row['code']] = $row['code']; $item_category[$row['code']] = $row['category']; $item_unit[$row['code']] = $row['cunits']; }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; $sector_code[$row['code']] = $row['code']; }
	
	if(isset($_POST['submit']) == true){
		$fromdate = $_POST['fromdate'];
		$todate = $_POST['todate'];
		$icategories = $_POST['icategories'];
		$sectors = $_POST['sectors'];
	}
	else{
		$fromdate = $todate = $today;
		$icategories = $sectors = "all";
	}
	
    $stock_report_paflag = 0;
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $stock_report_paflag = $row['stock_report_paflag']; } if($stock_report_paflag == 0 || $stock_report_paflag == ""){ $stock_report_paflag = 0; }
    if($_SERVER['REMOTE_ADDR'] == "49.205.134.69"){ $stock_report_paflag = 1; }
	$exoption = "displaypage";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; if($excel_type == "exportexcel"){ $exoption = "displaypage"; } else{ $exoption = $_POST['export']; } } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname =  "all";
	}
	$url = "../PHPExcel/Examples/StockReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&cname=".$exl_icatname."&sector=".$exl_sname."&cid=".$cid;
	
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
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
			body{
				font-size: 15px;
				font-weight: bold;
				color: black;
			}
			.thead2,.tbody1 {
				font-size: 15px;
				font-weight: bold;
				padding: 1px;
				color: black;
			}
			.formcontrol {
				font-size: 15px;
				font-weight: bold;
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				font-size: 15px;
				font-weight: bold;
				color: black;
				padding-right: 5px;
				text-align: right;
			}
			.reportselectionlabel{
				font-size: 15px;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini" align="center">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $qr_img_path = $row['qr_img_path']; ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;">Stock Report</label>&ensp;&ensp;
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<form action="<?php echo $pageLink; ?>" method="post" onsubmit="return checkval()">
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="25">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Item Category</label>&nbsp;
										<select name="icategories" id="icategories" class="form-control select2" style="width:auto;">
											<option value="all">-All-</option>
											<?php
											foreach($icat_code as $cc){
											?>
													<option <?php if($icategories == $cc) { echo 'selected'; } ?> value="<?php echo $cc; ?>"><?php echo $icat_name[$cc]; ?></option>
											<?php
												}
											?>
										</select>&ensp;&ensp;
										<label class="reportselectionlabel">Sector</label>&nbsp;
										<select name="sectors" id="sectors" class="form-control select2" style="width:auto;">
											<option value="all">-All-</option>
											<?php
											foreach($sector_code as $cc){
											?>
													<option <?php if($sectors == $cc) { echo 'selected'; } ?> value="<?php echo $cc; ?>"><?php echo $sector_name[$cc]; ?></option>
											<?php
												}
											?>
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
								$prev_bal_col = $item_det_col = $bwtd_det_col = $grnd_tot_col = $clsb_tot_col = 0;
								?>
								<thead class="thead2" style="background-color: #98fb98;">
									<tr>
                                        <th>Sl No.</th>
                                        <th>Category</th>
                                        <th>Item Name</th>
                                        <th>Unit</th>
                                        <th>Opening</th>
                                        <th>Purchase/Transfer In</th>
                                        <th>Transferout</th>
                                        <th>Sold</th>
                                        <th>Sale Return</th>
                                        <th>Closing</th>
                                        <?php
                                        if($stock_report_paflag == 1){ echo "<th>Price</th><th>Amount</th>"; }
                                        ?>
                                    </tr>
								</thead>
								<tbody class="tbody1" id="myTable" style="background-color: #f4f0ec;">
								<?php
									$fdate = date("Y-m-d",strtotime($_POST['fromdate']));
									$tdate = date("Y-m-d",strtotime($_POST['todate']));
                                    $item_asort_list = $item_asort_codes = $item_asort_array = $sector_asort_list = $sector_asort_codes = $sector_asort_array = array();
                                    $item_list = $itemcode_filter = $code_filter = "";
                                    if($icategories == "all"){
                                        $itemcode_filter = "";
                                        $code_filter = "";
                                    }
                                    else{
                                        foreach($item_code as $icode){
                                            if($item_category[$icode] == $icategories){
                                                if($item_list == ""){
                                                    $item_list = $icode;
                                                }
                                                else{
                                                    $item_list = $item_list."','".$icode;
                                                }
                                            }
                                        }
                                        $itemcode_filter = " AND `itemcode` IN ('$item_list')";
                                        $code_filter = " AND `code` IN ('$item_list')";
                                    }

                                    $sector_list = $warehouse_filter = $towarehouse_filter = $fromwarehouse_filter = $ccode_filter = "";
                                    if($sectors == "all"){
                                        $warehouse_filter = "";
                                        $towarehouse_filter = "";
                                        $fromwarehouse_filter = "";
                                        $ccode_filter = "";
                                    }
                                    else{
                                        $warehouse_filter = " AND `warehouse` IN ('$sectors')";
                                        $towarehouse_filter = " AND `towarehouse` IN ('$sectors')";
                                        $fromwarehouse_filter = " AND `fromwarehouse` IN ('$sectors')";
                                        $ccode_filter = " AND `vcode` IN ('$sectors')";
                                    }
									
                                    /*30.04.2023 Closing Stock Fetch*/
                                    $key_index = ""; $open_stock_closed_sector_qty = $open_stock_closed_item_qty = array();
                                    $obsql = "SELECT code as itemcode,warehouse,SUM(closedquantity) as quantity FROM `item_closingstock` WHERE `date` = '2023-04-30'".$code_filter."".$warehouse_filter." AND `active` = '1' GROUP BY `code`,`warehouse` ORDER BY `code`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_closed_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_closed_item_qty[$key_index] = $open_stock_closed_item_qty[$key_index] + $obrow['quantity'];

                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}
                                    //Purchases
                                    $key_index = ""; $open_stock_purin_sector_qty = $open_stock_purin_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(netweight) as quantity FROM `pur_purchase` WHERE `date` < '$fdate'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_purin_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_purin_item_qty[$key_index] = $open_stock_purin_item_qty[$key_index] + $obrow['quantity'];

                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Transfer In
                                    $key_index = ""; $open_stock_transin_sector_qty = $open_stock_transin_item_qty = array();
                                    $obsql = "SELECT code as itemcode,towarehouse as warehouse,SUM(quantity) as quantity FROM `item_stocktransfers` WHERE `date` < '$fdate'".$code_filter."".$towarehouse_filter." AND `active` = '1' GROUP BY `code`,`towarehouse` ORDER BY `code`,`towarehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_transin_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_transin_item_qty[$key_index] = $open_stock_transin_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}
                                    $key_index = ""; $open_stock_adja_sector_qty = $open_stock_adja_item_qty = $open_stock_adjd_sector_qty = $open_stock_adjd_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,a_type,SUM(nweight) as quantity FROM `item_stock_adjustment` WHERE `date` < '$fdate'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`a_type`,`warehouse` ORDER BY `itemcode`,`a_type`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        if($obrow['a_type'] == "add"){
                                            //item and sector wise total quantity
                                            $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                            $open_stock_adja_sector_qty[$key_index] = $obrow['quantity'];
                                            
                                            //item wise total quantity
                                            $key_index = $obrow['itemcode'];
                                            $open_stock_adja_item_qty[$key_index] = $open_stock_adja_item_qty[$key_index] + $obrow['quantity'];
                                            
                                            $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                            $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
                                        }
                                        else if($obrow['a_type'] == "deduct"){
                                            //item and sector wise total quantity
                                            $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                            $open_stock_adjd_sector_qty[$key_index] = $obrow['quantity'];
                                            
                                            //item wise total quantity
                                            $key_index = $obrow['itemcode'];
                                            $open_stock_adjd_item_qty[$key_index] = $open_stock_adjd_item_qty[$key_index] + $obrow['quantity'];
                                            
                                            $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                            $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
                                        }
									}

                                    //Purchase Return
                                    $key_index = ""; $open_stock_prtn_sector_qty = $open_stock_prtn_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` < '$fdate' AND `mode` LIKE 'supplier'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_prtn_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_prtn_item_qty[$key_index] = $open_stock_prtn_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}
                                    
                                    //Sales
                                    $key_index = ""; $open_stock_saleout_sector_qty = $open_stock_saleout_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(netweight) as quantity FROM `customer_sales` WHERE `date` < '$fdate'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_saleout_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_saleout_item_qty[$key_index] = $open_stock_saleout_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Transfer Out
                                    $key_index = ""; $open_stock_transout_sector_qty = $open_stock_transout_item_qty = array();
                                    $obsql = "SELECT code as itemcode,fromwarehouse as warehouse,SUM(quantity) as quantity FROM `item_stocktransfers` WHERE `date` < '$fdate'".$code_filter."".$fromwarehouse_filter." AND `active` = '1' GROUP BY `code`,`fromwarehouse` ORDER BY `code`,`fromwarehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_transout_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_transout_item_qty[$key_index] = $open_stock_transout_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Sales Return
                                    $key_index = ""; $open_stock_srtn_sector_qty = $open_stock_srtn_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` < '$fdate' AND `mode` LIKE 'customer'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_srtn_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_srtn_item_qty[$key_index] = $open_stock_srtn_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Sector Mortality
                                    $key_index = ""; $open_stock_smort_sector_qty = $open_stock_smort_item_qty = array();
                                    $obsql = "SELECT itemcode,vcode as warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` < '$fdate' AND `mode` LIKE 'sector'".$itemcode_filter."".$ccode_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`vcode` ORDER BY `itemcode`,`vcode` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_smort_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_smort_item_qty[$key_index] = $open_stock_smort_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Customer Mortality
                                    $key_index = ""; $open_stock_cmort_sector_qty = $open_stock_cmort_item_qty = array();
                                    $obsql = "SELECT itemcode,vcode as warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` < '$fdate' AND `mode` LIKE 'customer'".$itemcode_filter."".$ccode_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`vcode` ORDER BY `itemcode`,`vcode` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $open_stock_cmort_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $open_stock_cmort_item_qty[$key_index] = $open_stock_cmort_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}
                                    /*Between Days Transactions*/
                                    //Purchases
                                    $key_index = ""; $between_stock_purin_sector_qty = $between_stock_purin_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(netweight) as quantity FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_purin_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_purin_item_qty[$key_index] = $between_stock_purin_item_qty[$key_index] + $obrow['quantity'];

                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Transfer In
                                    $key_index = ""; $between_stock_transin_sector_qty = $between_stock_transin_item_qty = array();
                                    $obsql = "SELECT code as itemcode,towarehouse as warehouse,SUM(quantity) as quantity FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$code_filter."".$towarehouse_filter." AND `active` = '1' GROUP BY `code`,`towarehouse` ORDER BY `code`,`towarehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_transin_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_transin_item_qty[$key_index] = $between_stock_transin_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Purchase Return
                                    $key_index = ""; $between_stock_prtn_sector_qty = $between_stock_prtn_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` LIKE 'supplier'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_prtn_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_prtn_item_qty[$key_index] = $between_stock_prtn_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Sales
                                    $key_index = ""; $between_stock_saleout_sector_qty = $between_stock_saleout_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(netweight) as quantity FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_saleout_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_saleout_item_qty[$key_index] = $between_stock_saleout_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Transfer Out
                                    $key_index = ""; $between_stock_transout_sector_qty = $between_stock_transout_item_qty = array();
                                    $obsql = "SELECT code as itemcode,fromwarehouse as warehouse,SUM(quantity) as quantity FROM `item_stocktransfers` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$code_filter."".$fromwarehouse_filter." AND `active` = '1' GROUP BY `code`,`fromwarehouse` ORDER BY `code`,`fromwarehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_transout_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_transout_item_qty[$key_index] = $between_stock_transout_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    $key_index = ""; $btw_stock_adja_sector_qty = $btw_stock_adja_item_qty = $btw_stock_adjd_sector_qty = $btw_stock_adjd_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,a_type,SUM(nweight) as quantity FROM `item_stock_adjustment` WHERE`date` >= '$fdate' AND `date` <= '$tdate'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`a_type`,`warehouse` ORDER BY `itemcode`,`a_type`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        if($obrow['a_type'] == "add"){
                                            //item and sector wise total quantity
                                            $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                            $btw_stock_adja_sector_qty[$key_index] = $obrow['quantity'];
                                            
                                            //item wise total quantity
                                            $key_index = $obrow['itemcode'];
                                            $btw_stock_adja_item_qty[$key_index] = $btw_stock_adja_item_qty[$key_index] + $obrow['quantity'];
                                            
                                            $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                            $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
                                        }
                                        else if($obrow['a_type'] == "deduct"){
                                            //item and sector wise total quantity
                                            $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                            $btw_stock_adjd_sector_qty[$key_index] = $obrow['quantity'];
                                            
                                            //item wise total quantity
                                            $key_index = $obrow['itemcode'];
                                            $btw_stock_adjd_item_qty[$key_index] = $btw_stock_adjd_item_qty[$key_index] + $obrow['quantity'];
                                            
                                            $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                            $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
                                        }
									}
                                    
                                    //Sales Return
                                    $key_index = ""; $between_stock_srtn_sector_qty = $between_stock_srtn_item_qty = array();
                                    $obsql = "SELECT itemcode,warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` LIKE 'customer'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`warehouse` ORDER BY `itemcode`,`warehouse` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_srtn_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_srtn_item_qty[$key_index] = $between_stock_srtn_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Sector Mortality
                                    $key_index = ""; $between_stock_smort_sector_qty = $between_stock_smort_item_qty = array();
                                    $obsql = "SELECT itemcode,vcode as warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` LIKE 'sector'".$itemcode_filter."".$ccode_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`vcode` ORDER BY `itemcode`,`vcode` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_smort_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_smort_item_qty[$key_index] = $between_stock_smort_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}

                                    //Customer Mortality
                                    $key_index = ""; $between_stock_cmort_sector_qty = $between_stock_cmort_item_qty = array();
                                    $obsql = "SELECT itemcode,vcode as warehouse,SUM(quantity) as quantity FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `mode` LIKE 'customer'".$itemcode_filter."".$ccode_filter." AND `active` = '1' AND `dflag` = '0' GROUP BY `itemcode`,`vcode` ORDER BY `itemcode`,`vcode` ASC";
									$obquery = mysqli_query($conn,$obsql); $old_inv = "";
									while($obrow = mysqli_fetch_assoc($obquery)){
                                        //item and sector wise total quantity
                                        $key_index = $obrow['itemcode']."@".$obrow['warehouse'];
                                        $between_stock_cmort_sector_qty[$key_index] = $obrow['quantity'];
                                        
                                        //item wise total quantity
                                        $key_index = $obrow['itemcode'];
                                        $between_stock_cmort_item_qty[$key_index] = $between_stock_cmort_item_qty[$key_index] + $obrow['quantity'];
                                        
                                        $item_asort_array[$obrow['itemcode']] = $obrow['itemcode'];
                                        $sector_asort_array[$obrow['warehouse']] = $obrow['warehouse'];
									}
                                    //Fetch Stock Information

                                    $item_asort_list = implode("','",$item_asort_array);
                                    $sector_asort_list = implode("','",$sector_asort_array);

                                    $i1_list = $item_price = array();
                                    $sql = "SELECT * FROM `pur_purchase` WHERE `active` = '1' AND `id` IN (SELECT MAX(id) as id FROM `pur_purchase` WHERE `itemcode` IN ('$item_asort_list') AND `date` <= '$tdate'".$itemcode_filter."".$warehouse_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `itemcode` ORDER BY `id` ASC)"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $i1_list[$row['itemcode']] = $row['itemcode']; $item_price[$row['itemcode']] = $row['itemprice']; }

                                    $i2_list = implode("','", $i1_list);
                                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `active` = '1' AND `id` IN (SELECT  MAX(id) as id FROM `item_stocktransfers` WHERE `code` NOT IN ('$i2_list') AND `date` <= '$tdate'".$code_filter."".$towarehouse_filter." AND `active` = '1' GROUP BY `code` ORDER BY `code` ASC)"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){ $item_price[$row['code']] = $row['price']; }

                                    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `code` IN ('$item_asort_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $item_asort_codes[$row['code']] = $row['code']; }

                                    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `code` IN ('$sector_asort_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $sector_asort_codes[$row['code']] = $row['code']; }

                                    $sectorwise_separate_flag = 0; $final_total_flag = $item_famount = 0; $sl = 1;
                                    
                                    if($sectorwise_separate_flag == 1){
                                        foreach($sector_asort_codes as $scode){

                                            echo '<tr>';
                                            echo '<th colspan="9" style="text-align:center;color:green;">'.$sector_name[$scode].'</th>';
                                            echo '</tr>';
                                            foreach($item_asort_codes as $icode){
                                                //Calculations
                                                $key_index = $icode."@".$scode;

                                                //Opening
                                                $total_itemin_opening = $open_stock_purin_sector_qty[$key_index] + $open_stock_closed_sector_qty[$key_index] + $open_stock_transin_sector_qty[$key_index] + $open_stock_srtn_sector_qty[$key_index] + $open_stock_adja_sector_qty[$key_index];
                                                $total_itemout_opening = $open_stock_saleout_sector_qty[$key_index] + $open_stock_transout_sector_qty[$key_index] + $open_stock_prtn_sector_qty[$key_index] + $open_stock_adjd_sector_qty[$key_index];// + $open_stock_smort_sector_qty[$key_index] + $open_stock_cmort_sector_qty[$key_index];

                                                $final_item_opening_qty = $total_itemin_opening - $total_itemout_opening;
                                                $final_purtrin_between_item_qty = $between_stock_purin_sector_qty[$key_index] + $between_stock_transin_sector_qty[$key_index] + $btw_stock_adja_sector_qty[$key_index];
                                                $final_transferout_between_item_qty = $between_stock_transout_sector_qty[$key_index] + $between_stock_prtn_item_qty[$key_index] + $btw_stock_adjd_sector_qty[$key_index];
                                                $final_sold_between_item_qty = $between_stock_saleout_sector_qty[$key_index];
                                                $final_salereturn_between_item_qty = $between_stock_srtn_sector_qty[$key_index];

                                                $final_item_closing_qty = (($final_item_opening_qty + $final_purtrin_between_item_qty + $final_salereturn_between_item_qty) - ($final_transferout_between_item_qty + $final_sold_between_item_qty));
                                                
                                                /* Grand Total Stocks */
                                                $gt_opening_stock = $gt_opening_stock + $final_item_opening_qty;
                                                $gt_purtrin_stock = $gt_purtrin_stock + $final_purtrin_between_item_qty;
                                                $gt_trout_stock = $gt_trout_stock + $final_transferout_between_item_qty;
                                                $gt_sold_stock = $gt_sold_stock + $final_sold_between_item_qty;
                                                $gt_sreturn_stock = $gt_sreturn_stock + $final_salereturn_between_item_qty;

                                                echo '<tr>';
                                                echo '<td style="text-align:left;">'.$sl++.'</td>';
                                                echo '<td style="text-align:left;">'.$icat_name[$item_category[$icode]].'</td>';
                                                echo '<td style="text-align:left;">'.$item_name[$icode].'</td>';
                                                echo '<td style="text-align:left;">'.$item_unit[$icode].'</td>';
                                                echo '<td>'.number_format_ind(round($final_item_opening_qty,2)).'</td>';
                                                echo '<td>'.number_format_ind(round($final_purtrin_between_item_qty,2)).'</td>';
                                                echo '<td>'.number_format_ind(round($final_transferout_between_item_qty,2)).'</td>';
                                                echo '<td>'.number_format_ind(round($final_sold_between_item_qty,2)).'</td>';
                                                echo '<td>'.number_format_ind(round($final_salereturn_between_item_qty,2)).'</td>';
                                                echo '<td>'.number_format_ind(round($final_item_closing_qty,2)).'</td>';
                                                if($stock_report_paflag == 1){
                                                    echo '<td style="text-align:right;">'.number_format_ind(round($item_price[$icode],2)).'</td>';
                                                    $item_amount = 0; $item_amount = (float)$item_price[$icode] * (float)$final_item_closing_qty;
                                                    echo '<td>'.number_format_ind(round($item_amount,2)).'</td>';
                                                    $item_famount += (float)$item_amount;
                                                }
                                                echo '</tr>';
                                            }
                                        }
                                    }
                                    else{
                                        foreach($item_asort_codes as $icode){
                                            //Calculations
                                            $key_index = $icode;

                                            //Opening
                                            $total_itemin_opening = $open_stock_purin_item_qty[$key_index] + $open_stock_closed_item_qty[$key_index] + $open_stock_transin_item_qty[$key_index] + $open_stock_srtn_item_qty[$key_index] + $open_stock_adja_item_qty[$key_index];
                                            $total_itemout_opening = $open_stock_saleout_item_qty[$key_index] + $open_stock_transout_item_qty[$key_index] + $open_stock_prtn_item_qty[$key_index] + $open_stock_adjd_item_qty[$key_index];// + $open_stock_smort_item_qty[$key_index] + $open_stock_cmort_item_qty[$key_index];

                                            $final_item_opening_qty = $total_itemin_opening - $total_itemout_opening;
                                            $final_purtrin_between_item_qty = $between_stock_purin_item_qty[$key_index] + $between_stock_transin_item_qty[$key_index] + $btw_stock_adja_item_qty[$key_index];
                                            $final_transferout_between_item_qty = $between_stock_transout_item_qty[$key_index] + $between_stock_prtn_item_qty[$key_index] + $btw_stock_adjd_item_qty[$key_index];
                                            $final_sold_between_item_qty = $between_stock_saleout_item_qty[$key_index];
                                            $final_salereturn_between_item_qty = $between_stock_srtn_item_qty[$key_index];

                                            $final_item_closing_qty = (($final_item_opening_qty + $final_purtrin_between_item_qty + $final_salereturn_between_item_qty) - ($final_transferout_between_item_qty + $final_sold_between_item_qty));
                                            
                                                /* Grand Total Stocks */
                                                $gt_opening_stock = $gt_opening_stock + $final_item_opening_qty;
                                                $gt_purtrin_stock = $gt_purtrin_stock + $final_purtrin_between_item_qty;
                                                $gt_trout_stock = $gt_trout_stock + $final_transferout_between_item_qty;
                                                $gt_sold_stock = $gt_sold_stock + $final_sold_between_item_qty;
                                                $gt_sreturn_stock = $gt_sreturn_stock + $final_salereturn_between_item_qty;
                                                if($_SERVER['REMOTE_ADDR'] == "49.205.134.69"){
                                                    echo "<br/>$item_name[$icode]<br/>";
                                                    echo "<br/>$total_itemin_opening = $open_stock_purin_item_qty[$key_index] + $open_stock_closed_item_qty[$key_index] + $open_stock_transin_item_qty[$key_index] + $open_stock_srtn_item_qty[$key_index] + $open_stock_adja_item_qty[$key_index];<br/>";
                                                    echo "<br/>$total_itemout_opening = $open_stock_saleout_item_qty[$key_index] + $open_stock_transout_item_qty[$key_index] + $open_stock_prtn_item_qty[$key_index] + $open_stock_adjd_item_qty[$key_index];<br/>";
                                                    echo "<br/>$final_item_opening_qty = $total_itemin_opening - $total_itemout_opening;<br/>";
                                                    echo "<br/><br/>";
                                                }
                                            echo '<tr>';
                                            echo '<td style="text-align:left;">'.$sl++.'</td>';
                                            echo '<td style="text-align:left;">'.$icat_name[$item_category[$icode]].'</td>';
                                            echo '<td style="text-align:left;">'.$item_name[$icode].'</td>';
                                            echo '<td style="text-align:left;">'.$item_unit[$icode].'</td>';
                                            echo '<td>'.number_format_ind(round($final_item_opening_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_purtrin_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_transferout_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_sold_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_salereturn_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_item_closing_qty,2)).'</td>';
                                            if($stock_report_paflag == 1){
                                                echo '<td style="text-align:right;">'.number_format_ind(round($item_price[$icode],2)).'</td>';
                                                $item_amount = 0; $item_amount = (float)$item_price[$icode] * (float)$final_item_closing_qty;
                                                if($_SERVER['REMOTE_ADDR'] == "49.205.134.69"){
                                                    echo "<br/>$item_amount = (float)$item_price[$icode] * (float)$final_item_closing_qty;<br/>";
                                                }
                                                echo '<td>'.number_format_ind(round($item_amount,2)).'</td>';
                                                $item_famount += (float)$item_amount;
                                            }
                                            echo '</tr>';
                                        }
                                    }
                                    if($sectorwise_separate_flag == 1 && $final_total_flag == 1 && $sectors == "all"){
                                        echo '<tr>';
                                        echo '<th colspan="9" style="text-align:center;color:green;background-color: #98fb98;">Final Total</th>';
                                        echo '</tr>';
                                        foreach($item_asort_codes as $icode){
                                            //Calculations
                                            $key_index = $icode;

                                            //Opening
                                            $total_itemin_opening = $open_stock_purin_item_qty[$key_index] + $open_stock_closed_item_qty[$key_index] + $open_stock_transin_item_qty[$key_index] + $open_stock_srtn_item_qty[$key_index] + $open_stock_adja_item_qty[$key_index];
                                            $total_itemout_opening = $open_stock_saleout_item_qty[$key_index] + $open_stock_transout_item_qty[$key_index] + $open_stock_prtn_item_qty[$key_index] + $open_stock_adjd_item_qty[$key_index];// + $open_stock_smort_item_qty[$key_index] + $open_stock_cmort_item_qty[$key_index];

                                            $final_item_opening_qty = $total_itemin_opening - $total_itemout_opening;
                                            $final_purtrin_between_item_qty = $between_stock_purin_item_qty[$key_index] + $between_stock_transin_item_qty[$key_index] + $btw_stock_adja_item_qty[$key_index];
                                            $final_transferout_between_item_qty = $between_stock_transout_item_qty[$key_index] + $between_stock_prtn_item_qty[$key_index] + $btw_stock_adjd_item_qty[$key_index];
                                            $final_sold_between_item_qty = $between_stock_saleout_item_qty[$key_index];
                                            $final_salereturn_between_item_qty = $between_stock_srtn_item_qty[$key_index];

                                            $final_item_closing_qty = (($final_item_opening_qty + $final_purtrin_between_item_qty + $final_salereturn_between_item_qty) - ($final_transferout_between_item_qty + $final_sold_between_item_qty));
                                            
                                                /* Grand Total Stocks */
                                                $gt_opening_stock = $gt_opening_stock + $final_item_opening_qty;
                                                $gt_purtrin_stock = $gt_purtrin_stock + $final_purtrin_between_item_qty;
                                                $gt_trout_stock = $gt_trout_stock + $final_transferout_between_item_qty;
                                                $gt_sold_stock = $gt_sold_stock + $final_sold_between_item_qty;
                                                $gt_sreturn_stock = $gt_sreturn_stock + $final_salereturn_between_item_qty;

                                            echo '<tr>';
                                            echo '<td style="text-align:left;">'.$sl++.'</td>';
                                            echo '<td style="text-align:left;">'.$icat_name[$item_category[$icode]].'</td>';
                                            echo '<td style="text-align:left;">'.$item_name[$icode].'</td>';
                                            echo '<td style="text-align:left;">'.$item_unit[$icode].'</td>';
                                            echo '<td>'.number_format_ind(round($final_item_opening_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_purtrin_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_transferout_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_sold_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_salereturn_between_item_qty,2)).'</td>';
                                            echo '<td>'.number_format_ind(round($final_item_closing_qty,2)).'</td>';
                                            if($stock_report_paflag == 1){
                                                echo '<td style="text-align:right;">'.number_format_ind(round($item_price[$icode],2)).'</td>';
                                                $item_amount = 0; $item_amount = (float)$item_price[$icode] * (float)$final_item_closing_qty;
                                                echo '<td>'.number_format_ind(round($item_amount,2)).'</td>';
                                                $item_famount += (float)$item_amount;
                                            }
                                            echo '</tr>';
                                        }
                                    }
                                    $gt_closing_stock = (($gt_opening_stock + $gt_purtrin_stock + $gt_sreturn_stock) - ($gt_trout_stock + $gt_sold_stock));
                                    
                                    echo '<tr>';
                                    echo '<td colspan="4" style="text-align:right;color:green;background-color: #98fb98;border:solid">Total</td>';
                                    echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid">'.number_format_ind(round($gt_opening_stock,2)).'</td>';
                                    echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid">'.number_format_ind(round($gt_purtrin_stock,2)).'</td>';
                                    echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid">'.number_format_ind(round($gt_trout_stock,2)).'</td>';
                                    echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid">'.number_format_ind(round($gt_sold_stock,2)).'</td>';
                                    echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid">'.number_format_ind(round($gt_sreturn_stock,2)).'</td>';
                                    echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid">'.number_format_ind(round($gt_closing_stock,2)).'</td>';
                                    if($stock_report_paflag == 1){
                                        echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid"></td>';
                                        echo '<td style="text-align:right;color:green;background-color: #98fb98;border:solid">'.number_format_ind(round($item_famount,2)).'</td>';
                                    }
                                    echo '</tr>';
								?>
								</tbody>
							<?php
							}
							?>
						</table>
					</form>
				</div>
		</section>
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("cname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select customer ..!");
					return false;
				}
				else {
					return true;
				}
			}
		</script>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
