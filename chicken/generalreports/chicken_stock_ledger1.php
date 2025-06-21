<?php
    //chicken_stock_ledger1.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "number_format_ind.php";
		$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid'];

        $form_reload_page = "chicken_stock_ledger1.php";
	}
	else{
		include "APIconfig.php";
		include "number_format_ind.php";
		$dbname = $db;
		$users_code = $_GET['emp_code'];
        $form_reload_page = "chicken_stock_ledger1.php?db=".$db;
	}
    $file_name = "Item Stock Ledger Report";

	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$loc_access = $row['loc_access'];
		$cgroup_access = $row['cgroup_access'];
		if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $utype = "S"; }
		else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $utype = "A"; }
		else if($row['normal_access'] == 1 || $row['normal_access'] == "1"){ $utype = "N"; }
		else{ $utype = "N"; }
	}
	if($utype == "S" || $utype == "A"){
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
	else{
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
    //Usr access Based Sector Filter
	if($loc_access == "all" || $loc_access == "All" || $loc_access == "" || $loc_access == NULL){ $user_sector_filter = ""; }
	else{ $wcode = str_replace(",","','",$loc_access); $user_sector_filter = " AND code IN ('$wcode')"; }
	
    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
    while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$user_sector_filter." ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $vendor_name = array();
	while($row = mysqli_fetch_assoc($query)){ $vendor_name[$row['code']] = $row['name']; }

	$fdate = $tdate = date("Y-m-d"); $sectors = $items = "select";
    $exports = "display";
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fdate']));
		$tdate = date("Y-m-d",strtotime($_POST['tdate']));
		$items = $_POST['items'];
		$sectors = $_POST['sectors'];
		$exports = $_POST['exports'];
	}
	//$url = "../PHPExcel/Examples/SalesReportMaster-Excel.php?fdate=".$fdate."&tdate=".$tdate."&items=".$items."&sectors=".$sectors;
	
?>
<html>
	<head>
		<!--<script>
			var exptype = '<?php //echo $exports; ?>';
			var url = '<?php //echo $url; ?>';
			if(exptype.match("excel")){
				window.open(url,'_BLANK');
			}
		</script>-->
        <?php include "header_head2.php"; ?>
	</head>
	<body>
	    <?php if($exports == "display" || $exports == "print") { ?>
			<table align="center">
				<tr>
					<td><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
					<td><?php echo $cdetails; ?></td>
					<td align="center">
						<h3><?php echo $file_name; ?></h3>
						<label><b style="color: green;">Item:</b>&nbsp;<?php echo $item_name[$items]; ?></label><br/>
						<label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
					</td>
				</tr>
			</table>
	    <?php } ?>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table class="main-table table-sm table-hover">
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:150px;">
                                            <label for="items">Item</label>
                                            <select name="items" id="items" class="form-control select2" style="width:140px;">
                                                <option value="select" <?php if($items == "select"){ echo "selected"; } ?>>-select-</option>
                                                <?php
                                                    foreach($item_code as $icode){
                                                    ?>
                                                    <option value="<?php echo $icode; ?>" <?php if($items == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                    <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($sectors == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:150px;">
                                            <label>Export</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                                <option value="display" <?php if($exports == "display"){ echo "selected"; } ?>>-Display-</option>
                                                <option value="excel" <?php if($exports == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                                <option value="print" <?php if($exports == "print"){ echo "selected"; } ?>>-Print-</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                            $sdate = $edate = "";
                            $sql = "SELECT * FROM `pur_purchase` WHERE `date` <= '$tdate' AND `itemcode` = '$items' AND `warehouse` = '$sectors' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                            $query = mysqli_query($conn,$sql); $pur_oqty = $pur_oamt = $pur_bqty = $pur_bamt = array(); $i = $j = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$j;
                                    $pur_oqty[$key] += (float)$row['netweight'];
                                    $pur_oamt[$key] += (float)$row['totalamt'];
                                    
                                    if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                                    if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                                    $j++;
                                }
                                else if(strtotime($row['date']) >= strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$i;
                                    $pur_bqty[$key] += (float)$row['netweight'];
                                    $pur_bamt[$key] += (float)$row['totalamt'];

                                    $pur_trnum[$key] = $row['invoice'];
                                    $pur_frdt[$key] = $vendor_name[$row['vendorcode']];
                                    $pur_Remarks[$key] = $row['remarks'];
                                    $i++;
                                } else{ }
                            }
                            
                            $sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$tdate' AND `itemcode` = '$items' AND `warehouse` = '$sectors' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
                            $query = mysqli_query($conn,$sql); $sale_oqty = $sale_oamt = $sale_bqty = $sale_bamt = array(); $i = $j = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$j;
                                    $sale_oqty[$key] += (float)$row['netweight'];
                                    $sale_oamt[$key] += (float)$row['totalamt'];
                                    
                                    if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                                    if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                                    $j++;
                                }
                                else if(strtotime($row['date']) >= strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$i;
                                    $sale_bqty[$key] += (float)$row['netweight'];
                                    $sale_bamt[$key] += (float)$row['totalamt'];

                                    $sale_trnum[$key] = $row['invoice'];
                                    $sale_frdt[$key] = $vendor_name[$row['customercode']];
                                    $sale_Remarks[$key] = $row['remarks'];
                                    $i++;
                                } else{ }
                            }
                            
                            $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` = '$items' AND `towarehouse` = '$sectors' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $tin_oqty = $tin_oamt = $tin_bqty = $tin_bamt = array(); $i = $j = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['code']; $key = $idate."@".$icode."@".$j;
                                    $tin_oqty[$key] += (float)$row['quantity'];
                                    $tin_oamt[$key] += ((float)$row['quantity'] * (float)$row['price']);
                                    
                                    if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                                    if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                                    $j++;
                                }
                                else if(strtotime($row['date']) >= strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['code']; $key = $idate."@".$icode."@".$i;
                                    $tin_bqty[$key] += (float)$row['quantity'];
                                    $tin_bamt[$key] += ((float)$row['quantity'] * (float)$row['price']);

                                    $tin_trnum[$key] = $row['trnum'];
                                    $tin_frdt[$key] = $sector_name[$row['fromwarehouse']];
                                    $tin_Remarks[$key] = $row['remarks'];
                                    $i++;
                                } else{ }
                            }
                            
                            $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` = '$items' AND `fromwarehouse` = '$sectors' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $tout_oqty = $tout_oamt = $tout_bqty = $tout_bamt = array(); $i = $j = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['code']; $key = $idate."@".$icode."@".$j;
                                    $tout_oqty[$key] += (float)$row['quantity'];
                                    $tout_oamt[$key] += ((float)$row['quantity'] * (float)$row['price']);
                                    
                                    if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                                    if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                                    $j++;
                                }
                                else if(strtotime($row['date']) >= strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['code']; $key = $idate."@".$icode."@".$i;
                                    $tout_bqty[$key] += (float)$row['quantity'];
                                    $tout_bamt[$key] += ((float)$row['quantity'] * (float)$row['price']);

                                    $tout_trnum[$key] = $row['trnum'];
                                    $tout_frdt[$key] = $sector_name[$row['fromwarehouse']];
                                    $tout_Remarks[$key] = $row['remarks'];
                                    $i++;
                                } else{ }
                            }
                            
                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$tdate' AND `itemcode` = '$items' AND `warehouse` = '$sectors' AND `mode` = 'customer' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $srtn_oqty = $srtn_oamt = $srtn_bqty = $srtn_bamt = array(); $i = $j = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$j;
                                    $srtn_oqty[$key] += (float)$row['quantity'];
                                    $srtn_oamt[$key] += (float)$row['amount'];
                                    
                                    if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                                    if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                                    $j++;
                                }
                                else if(strtotime($row['date']) >= strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$i;
                                    $srtn_bqty[$key] += (float)$row['quantity'];
                                    $srtn_bamt[$key] += (float)$row['amount'];

                                    $srtn_trnum[$key] = $row['trnum'];
                                    $srtn_frdt[$key] = $vendor_name[$row['vcode']];
                                    $srtn_Remarks[$key] = $row['remarks'];
                                    $i++;
                                } else{ }
                            }
                            
                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$tdate' AND `itemcode` = '$items' AND `warehouse` = '$sectors' AND `mode` = 'supplier' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $prtn_oqty = $prtn_oamt = $prtn_bqty = $prtn_bamt = array(); $i = $j = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$j;
                                    $prtn_oqty[$key] += (float)$row['quantity'];
                                    $prtn_oamt[$key] += (float)$row['amount'];
                                    
                                    if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                                    if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                                    $j++;
                                }
                                else if(strtotime($row['date']) >= strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['itemcode']; $key = $idate."@".$icode."@".$i;
                                    $prtn_bqty[$key] += (float)$row['quantity'];
                                    $prtn_bamt[$key] += (float)$row['amount'];

                                    $prtn_trnum[$key] = $row['trnum'];
                                    $prtn_frdt[$key] = $vendor_name[$row['vcode']];
                                    $prtn_Remarks[$key] = $row['remarks'];
                                    $i++;
                                } else{ }
                            }
                            
                            $sql = "SELECT * FROM `item_closingstock` WHERE `date` <= '$tdate' AND `code` = '$items' AND `warehouse` = '$sectors' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $cstk_oqty = $cstk_oprc = $cstk_oamt = $cstk_bqty = $cstk_bprc = $cstk_bamt = array(); $i = $j = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                if(strtotime($row['date']) < strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['code']; $key = $idate."@".$icode."@".$j;
                                    $cstk_oqty[$key] += (float)$row['closedquantity'];
                                    $cstk_oprc[$key] = (float)$row['price'];
                                    $cstk_oamt[$key] += (float)$row['amount'];

                                    if($sdate == ""){ $sdate = $idate; } else{ if(strtotime($sdate) >= strtotime($idate)){ $sdate = $idate; } }
                                    if($edate == ""){ $edate = $idate; } else{ if(strtotime($edate) <= strtotime($idate)){ $edate = $idate; } }
                                    $j++;
                                }
                                else if(strtotime($row['date']) >= strtotime($fdate)){
                                    $idate = $row['date']; $icode = $row['code']; $key = $idate."@".$icode."@".$i;
                                    $cstk_bqty[$key] += (float)$row['closedquantity'];
                                    $cstk_bprc[$key] = (float)$row['price'];
                                    $cstk_bamt[$key] += (float)$row['amount'];

                                    $cstk_trnum[$key] = $row['trnum'];
                                    $cstk_frdt[$key] = NULL;
                                    $cstk_Remarks[$key] = $row['remarks'];
                                    $i++;
                                } else{ }
                            }

                            $opn_qty = $opn_prc = $opn_amt = 0;
                            $pur_size = sizeof($pur_oqty);
                            $tin_size = sizeof($tin_oqty);
                            $srtn_size = sizeof($srtn_oqty);
                            $sale_size = sizeof($sale_oqty);
                            $tout_size = sizeof($tout_oqty);
                            $prtn_size = sizeof($prtn_oqty);
                            $cstk_size = sizeof($cstk_oqty);

                            for($cdate = strtotime($sdate); $cdate <= strtotime($edate); $cdate += (86400)){
                                $adate = date("Y-m-d",$cdate);
                                //Purchases
                                for($i = 0;$i < $pur_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($pur_oqty[$key]) || $pur_oqty[$key] == ""){ $pur_oqty[$key] = 0; }
                                    if(empty($pur_oamt[$key]) || $pur_oamt[$key] == ""){ $pur_oamt[$key] = 0; }

                                    $stk_qty += (float)round($pur_oqty[$key],2);
                                    $stk_amt += (float)round($pur_oamt[$key],2);
                                    if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }
                                }
                                //Stk-In
                                for($i = 0;$i < $tin_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($tin_oqty[$key]) || $tin_oqty[$key] == ""){ $tin_oqty[$key] = 0; }
                                    if(empty($tin_oamt[$key]) || $tin_oamt[$key] == ""){ $tin_oamt[$key] = 0; }

                                    $stk_qty += (float)round($tin_oqty[$key],2);
                                    $stk_amt += (float)round($tin_oamt[$key],2);
                                    if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }
                                }
                                //Sale-Return
                                for($i = 0;$i < $srtn_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($srtn_oqty[$key]) || $srtn_oqty[$key] == ""){ $srtn_oqty[$key] = 0; }
                                    if(empty($srtn_oamt[$key]) || $srtn_oamt[$key] == ""){ $srtn_oamt[$key] = 0; }

                                    $stk_qty += (float)round($srtn_oqty[$key],2);
                                    $stk_amt += (float)round($srtn_oamt[$key],2);
                                    if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }
                                }
                                //Sales
                                for($i = 0;$i < $sale_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($sale_oqty[$key]) || $sale_oqty[$key] == ""){ $sale_oqty[$key] = 0; }
                                    if(empty($sale_oamt[$key]) || $sale_oamt[$key] == ""){ $sale_oamt[$key] = 0; }

                                    $stk_qty = (float)$stk_qty - (float)round($sale_oqty[$key],2);
                                    $amt1 = 0; $amt1 = (float)$sale_oqty[$key] * $stk_prc;
                                    $stk_amt = (float)$stk_amt - (float)$amt1;
                                }
                                //Transfer-Out
                                for($i = 0;$i < $tout_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($tout_oqty[$key]) || $tout_oqty[$key] == ""){ $tout_oqty[$key] = 0; }
                                    if(empty($tout_oamt[$key]) || $tout_oamt[$key] == ""){ $tout_oamt[$key] = 0; }

                                    $stk_qty = (float)$stk_qty - (float)round($tout_oqty[$key],2);
                                    $amt1 = 0; $amt1 = (float)$tout_oqty[$key] * $stk_prc;
                                    $stk_amt = (float)$stk_amt - (float)$amt1;
                                }
                                //Purchase-Return
                                for($i = 0;$i < $prtn_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($prtn_oqty[$key]) || $prtn_oqty[$key] == ""){ $prtn_oqty[$key] = 0; }
                                    if(empty($prtn_oamt[$key]) || $prtn_oamt[$key] == ""){ $prtn_oamt[$key] = 0; }

                                    $stk_qty = (float)$stk_qty - (float)round($prtn_oqty[$key],2);
                                    $amt1 = 0; $amt1 = (float)$prtn_oqty[$key] * $stk_prc;
                                    $stk_amt = (float)$stk_amt - (float)$amt1;
                                }
                                //Closing Stock
                                for($i = 0;$i < $cstk_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($cstk_oqty[$key]) || $cstk_oqty[$key] == ""){ $cstk_oqty[$key] = 0; }
                                    if(empty($cstk_oamt[$key]) || $cstk_oamt[$key] == ""){ $cstk_oamt[$key] = 0; }

                                    if((float)$cstk_oqty[$key] > 0){
                                        $stk_qty = (float)round($cstk_oqty[$key],2);
                                        if((float)$cstk_oamt[$key] > 0){
                                            $stk_amt = (float)round($cstk_oamt[$key],2);
                                        }
                                        else{
                                            if(empty($cstk_oprc[$key]) || $cstk_oprc[$key] == ""){ $cstk_oprc[$key] = 0; }
                                            $stk_amt = (float)$cstk_oqty[$key] * (float)$cstk_oprc[$key];
                                        }
                                        if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }
                                    }
                                    else{ }
                                }
                            }
                            $opn_qty = $stk_qty; $opn_amt = $stk_amt;
                            if((float)$opn_qty != 0){ $opn_prc = round(((float)$opn_amt / (float)$opn_qty),2); }
                            
                            $html = '';
                            $html .= '<thead class="thead2" id="head_names">';
                            $nhead_html .= '<tr>';
                            $nhead_html .= '<th colspan="6"></th>';
                            $nhead_html .= '<th colspan="3">Purchase/Transferred IN</th>';
                            $nhead_html .= '<th colspan="3">Sale/Transferred OUT</th>';
                            $nhead_html .= '<th colspan="3">Closing</th>';
                            $nhead_html .= '</tr>';

                            $nhead_html .= '<tr>';
                            $nhead_html .= '<th>Sl.No.</th>';
                            $nhead_html .= '<th>Date</th>';
                            $nhead_html .= '<th>Type</th>';
                            $nhead_html .= '<th>Trnum</th>';
                            $nhead_html .= '<th>Location</th>';
                            $nhead_html .= '<th>Remarks</th>';
                            $nhead_html .= '<th>Quantity</th>';
                            $nhead_html .= '<th>Price</th>';
                            $nhead_html .= '<th>Amount</th>';
                            $nhead_html .= '<th>Quantity</th>';
                            $nhead_html .= '<th>Price</th>';
                            $nhead_html .= '<th>Amount</th>';
                            $nhead_html .= '<th>Quantity</th>';
                            $nhead_html .= '<th>Price</th>';
                            $nhead_html .= '<th>Amount</th>';
                            $nhead_html .= '</tr>';
                            
                            $fhead_html .= '<tr>';
                            $fhead_html .= '<th colspan="6"></th>';
                            $fhead_html .= '<th colspan="3">Purchase/Transferred IN</th>';
                            $fhead_html .= '<th colspan="3">Sale/Transferred OUT</th>';
                            $fhead_html .= '<th colspan="3">Closing</th>';
                            $fhead_html .= '</tr>';

                            $fhead_html .= '<tr>';
                            $fhead_html .= '<th id="order_num">Sl.No.</th>';
                            $fhead_html .= '<th id="order_date">Date</th>';
                            $fhead_html .= '<th id="order">Type</th>';
                            $fhead_html .= '<th id="order">Trnum</th>';
                            $fhead_html .= '<th id="order">Location</th>';
                            $fhead_html .= '<th id="order">Remarks</th>';
                            $fhead_html .= '<th id="order_num">Quantity</th>';
                            $fhead_html .= '<th id="order_num">Price</th>';
                            $fhead_html .= '<th id="order_num">Amount</th>';
                            $fhead_html .= '<th id="order_num">Quantity</th>';
                            $fhead_html .= '<th id="order_num">Price</th>';
                            $fhead_html .= '<th id="order_num">Amount</th>';
                            $fhead_html .= '<th id="order_num">Quantity</th>';
                            $fhead_html .= '<th id="order_num">Price</th>';
                            $fhead_html .= '<th id="order_num">Amount</th>';
                            $fhead_html .= '</tr>';

                            $html .= $fhead_html;

                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1">';
                            $html .= '<tr>';
                            $html .= '<td colspan="6" style="text-align:right;">Opening Stock</td>';
                            $html .= '<td colspan="3"></td>';
                            $html .= '<td colspan="3"></td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($opn_qty).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($opn_prc).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($opn_amt).'</td>';
                            $html .= '</tr>';

                            $cls_qty = $cls_prc = $cls_amt = 0;
                            $pur_size = sizeof($pur_bqty);
                            $tin_size = sizeof($tin_bqty);
                            $srtn_size = sizeof($srtn_bqty);
                            $sale_size = sizeof($sale_bqty);
                            $tout_size = sizeof($tout_bqty);
                            $prtn_size = sizeof($prtn_bqty);
                            $cstk_size = sizeof($cstk_bqty);
                            $slno = 0;
                            for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)){
                                $adate = date("Y-m-d",$cdate);
                                //Purchases
                                for($i = 0;$i < $pur_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($pur_bqty[$key]) || $pur_bqty[$key] == ""){ $pur_bqty[$key] = 0; }
                                    if(empty($pur_bamt[$key]) || $pur_bamt[$key] == ""){ $pur_bamt[$key] = 0; }
                                    $pur_bprc = 0; if((float)$pur_bqty[$key] != 0){ $pur_bprc = ((float)$pur_bamt[$key] / (float)$pur_bqty[$key]); }

                                    $stk_qty += (float)round($pur_bqty[$key],2);
                                    $stk_amt += (float)round($pur_bamt[$key],2);
                                    if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }

                                    $tot_pts_qty += (float)round($pur_bqty[$key],2);
                                    $tot_pts_amt += (float)round($pur_bamt[$key],2);

                                    if((float)$pur_bqty[$key] > 0){
                                        $slno++; $ddate = date("d.m.Y",strtotime($adate));
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$ddate.'</td>';
                                        $html .= '<td style="text-align:left;">Purchases</td>';
                                        $html .= '<td style="text-align:left;">'.$pur_trnum[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$pur_frdt[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$pur_Remarks[$key].'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($pur_bqty[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($pur_bprc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($pur_bamt[$key]).'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_amt).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                //Stk-In
                                for($i = 0;$i < $tin_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($tin_bqty[$key]) || $tin_bqty[$key] == ""){ $tin_bqty[$key] = 0; }
                                    if(empty($tin_bamt[$key]) || $tin_bamt[$key] == ""){ $tin_bamt[$key] = 0; }
                                    $tin_bprc = 0; if((float)$tin_bqty[$key] != 0){ $tin_bprc = ((float)$tin_bamt[$key] / (float)$tin_bqty[$key]); }

                                    $stk_qty += (float)round($tin_bqty[$key],2);
                                    $stk_amt += (float)round($tin_bamt[$key],2);
                                    if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }

                                    $tot_pts_qty += (float)round($tin_bqty[$key],2);
                                    $tot_pts_amt += (float)round($tin_bamt[$key],2);

                                    if((float)$tin_bqty[$key] > 0){
                                        $slno++; $ddate = date("d.m.Y",strtotime($adate));
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$ddate.'</td>';
                                        $html .= '<td style="text-align:left;">Transfer-In</td>';
                                        $html .= '<td style="text-align:left;">'.$tin_trnum[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$tin_frdt[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$tin_Remarks[$key].'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($tin_bqty[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($tin_bprc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($tin_bamt[$key]).'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_amt).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                //Sale-Return
                                for($i = 0;$i < $srtn_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($srtn_bqty[$key]) || $srtn_bqty[$key] == ""){ $srtn_bqty[$key] = 0; }
                                    if(empty($srtn_bamt[$key]) || $srtn_bamt[$key] == ""){ $srtn_bamt[$key] = 0; }
                                    $srtn_bprc = 0; if((float)$srtn_bqty[$key] != 0){ $srtn_bprc = ((float)$srtn_bamt[$key] / (float)$srtn_bqty[$key]); }

                                    $stk_qty += (float)round($srtn_bqty[$key],2);
                                    $stk_amt += (float)round($srtn_bamt[$key],2);
                                    if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }

                                    $tot_pts_qty += (float)round($srtn_bqty[$key],2);
                                    $tot_pts_amt += (float)round($srtn_bamt[$key],2);

                                    if((float)$srtn_bqty[$key] > 0){
                                        $slno++; $ddate = date("d.m.Y",strtotime($adate));
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$ddate.'</td>';
                                        $html .= '<td style="text-align:left;">Sales-Return</td>';
                                        $html .= '<td style="text-align:left;">'.$srtn_trnum[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$srtn_frdt[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$srtn_Remarks[$key].'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($srtn_bqty[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($srtn_bprc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($srtn_bamt[$key]).'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_amt).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                //Sales
                                for($i = 0;$i < $sale_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($sale_bqty[$key]) || $sale_bqty[$key] == ""){ $sale_bqty[$key] = 0; }
                                    if(empty($sale_bamt[$key]) || $sale_bamt[$key] == ""){ $sale_bamt[$key] = 0; }

                                    $stk_qty = (float)$stk_qty - (float)round($sale_bqty[$key],2);
                                    $amt1 = 0; $amt1 = (float)$sale_bqty[$key] * $stk_prc;
                                    $stk_amt = (float)$stk_amt - (float)$amt1;

                                    $tot_stp_qty += (float)round($sale_bqty[$key],2);
                                    $tot_stp_amt += (float)round($sale_bamt[$key],2);

                                    if((float)$sale_bqty[$key] > 0){
                                        $slno++; $ddate = date("d.m.Y",strtotime($adate));
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$ddate.'</td>';
                                        $html .= '<td style="text-align:left;">Sales</td>';
                                        $html .= '<td style="text-align:left;">'.$sale_trnum[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$sale_frdt[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$sale_Remarks[$key].'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($sale_bqty[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amt1).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_amt).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                //Transfer-Out
                                for($i = 0;$i < $tout_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($tout_bqty[$key]) || $tout_bqty[$key] == ""){ $tout_bqty[$key] = 0; }
                                    if(empty($tout_bamt[$key]) || $tout_bamt[$key] == ""){ $tout_bamt[$key] = 0; }

                                    $stk_qty = (float)$stk_qty - (float)round($tout_bqty[$key],2);
                                    $amt1 = 0; $amt1 = (float)$tout_bqty[$key] * $stk_prc;
                                    $stk_amt = (float)$stk_amt - (float)$amt1;

                                    $tot_stp_qty += (float)round($tout_bqty[$key],2);
                                    $tot_stp_amt += (float)round($tout_bamt[$key],2);

                                    if((float)$tout_bqty[$key] > 0){
                                        $slno++; $ddate = date("d.m.Y",strtotime($adate));
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$ddate.'</td>';
                                        $html .= '<td style="text-align:left;">Transfer-Out</td>';
                                        $html .= '<td style="text-align:left;">'.$tout_trnum[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$tout_frdt[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$tout_Remarks[$key].'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($tout_bqty[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amt1).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_amt).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                //Purchase-Return
                                for($i = 0;$i < $prtn_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($prtn_bqty[$key]) || $prtn_bqty[$key] == ""){ $prtn_bqty[$key] = 0; }
                                    if(empty($prtn_bamt[$key]) || $prtn_bamt[$key] == ""){ $prtn_bamt[$key] = 0; }

                                    $stk_qty = (float)$stk_qty - (float)round($prtn_bqty[$key],2);
                                    $amt1 = 0; $amt1 = (float)$prtn_bqty[$key] * $stk_prc;
                                    $stk_amt = (float)$stk_amt - (float)$amt1;

                                    $tot_stp_qty += (float)round($prtn_bqty[$key],2);
                                    $tot_stp_amt += (float)round($prtn_bamt[$key],2);

                                    if((float)$prtn_bqty[$key] > 0){
                                        $slno++; $ddate = date("d.m.Y",strtotime($adate));
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$ddate.'</td>';
                                        $html .= '<td style="text-align:left;">Purchase-Return</td>';
                                        $html .= '<td style="text-align:left;">'.$prtn_trnum[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$prtn_frdt[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$prtn_Remarks[$key].'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($prtn_bqty[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($amt1).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_amt).'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                                //Closing Stock
                                for($i = 0;$i < $cstk_size;$i++){
                                    $key = $adate."@".$items."@".$i;
                                    if(empty($cstk_bqty[$key]) || $cstk_bqty[$key] == ""){ $cstk_bqty[$key] = 0; }
                                    if(empty($cstk_bamt[$key]) || $cstk_bamt[$key] == ""){ $cstk_bamt[$key] = 0; }

                                    if((float)$cstk_bqty[$key] > 0){
                                        $stk_qty = (float)round($cstk_bqty[$key],2);
                                        if((float)$cstk_bamt[$key] > 0){
                                            $stk_amt = (float)round($cstk_bamt[$key],2);
                                        }
                                        else{
                                            if(empty($cstk_bprc[$key]) || $cstk_bprc[$key] == ""){ $cstk_bprc[$key] = 0; }
                                            $stk_amt = (float)$cstk_bqty[$key] * (float)$cstk_bprc[$key];
                                            $cstk_bamt[$key] = $stk_amt;
                                        }
                                        if((float)$stk_qty != 0){ $stk_prc = ((float)$stk_amt / (float)$stk_qty); }
                                        $cstk_bprc = (float)$stk_prc;

                                        $slno++; $ddate = date("d.m.Y",strtotime($adate));
                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                                        $html .= '<td style="text-align:left;">'.$ddate.'</td>';
                                        $html .= '<td style="text-align:left;">Closing Stock</td>';
                                        $html .= '<td style="text-align:left;">'.$cstk_trnum[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$cstk_frdt[$key].'</td>';
                                        $html .= '<td style="text-align:left;">'.$cstk_Remarks[$key].'</td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:left;"></td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($cstk_bqty[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($cstk_bprc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($cstk_bamt[$key]).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_qty).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_prc).'</td>';
                                        $html .= '<td style="text-align:right;">'.number_format_ind($stk_amt).'</td>';
                                        $html .= '</tr>';
                                    }
                                    else{ }
                                }
                            }
                            $html .= '</tbody>';
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="6">Total</th>';
                            $html .= '<th class="text-right">'.number_format_ind($tot_pts_qty).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($tot_pts_prc).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($tot_pts_amt).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($tot_stp_qty).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($tot_stp_prc).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($tot_stp_amt).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($stk_qty).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($stk_prc).'</th>';
                            $html .= '<th class="text-right">'.number_format_ind($stk_amt).'</th>';
                            $html .= '</tr>';
                            $html .= '</tfoot>';

                            echo $html;
                        }
                            ?>
						</table>
					</form>
				</div>
		</section>
        <script>
            function checkval(){
                var groups = document.getElementById('groups[0]').value;
                var l = true;
                if(groups == ""){
                    alert("Please select Group");
                    document.getElementById('groups[0]').focus();
                    l = false;
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script>
            function filter_group_customers(){
                var selected = []; var fcode = ""; var all_flag = 0;
                
                removeAllOptions(document.getElementById("customers"));
                myselect = document.getElementById("customers");
                theOption1=document.createElement("OPTION");
                theText1=document.createTextNode("-All-");
                theOption1.value = "all";
                theOption1.appendChild(theText1);
                myselect.appendChild(theOption1);

                for(var option of document.getElementById('groups[0]').options){
                    if(option.selected){
                        fcode = option.value;
                        if(fcode == "all"){
                            all_flag = 1;
                        }
                    }
                }
                if(parseInt(all_flag) == 1){
                    <?php
                    foreach($cus_code as $vcode){
                    ?> 
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
                    theOption1.value = "<?php echo $vcode; ?>";
                    theOption1.appendChild(theText1);
                    myselect.appendChild(theOption1);	
                    <?php
                    }
                    ?>
                }
                else{
                    <?php
                    foreach($cus_code as $vcode){
                        $gcode = $cus_group[$vcode];
                    ?>
                    for(var option of document.getElementById('groups[0]').options){
                        if(option.selected){
                            fcode = option.value;
                            <?php
                            echo "if(fcode == '$gcode'){";
                            ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
                            theOption1.value = "<?php echo $vcode; ?>";
                            theOption1.appendChild(theText1);
                            myselect.appendChild(theOption1);
                            <?php
                            echo "}";
                            ?>
                        }
                    }
                    <?php
                    }
                    ?>
                }
            }
            function fetch_item_list(){
                var fcode = document.getElementById("item_cat").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(fcode != "all"){
                <?php
                    foreach($item_code as $icode){
                        $icats = $item_category[$icode];
                        echo "if(fcode == '$icats'){";
                ?> 
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                <?php
                        echo "}";
                    }
                ?>
                }
                else{
                    <?php
                        foreach($item_code as $icode){
                            $icats = $item_category[$icode];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $nhead_html; ?>';
                    $('#head_names').append(html);

                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html += '<?php echo $fhead_html; ?>';
                    document.getElementById("head_names").innerHTML = html;
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
        </script>
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
