<?php
    //chicken_item_stockreport1.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "number_format_ind.php";
		$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid'];

        $form_reload_page = "chicken_item_stockreport1.php";
	}
	else{
		include "APIconfig.php";
		include "number_format_ind.php";
		$dbname = $db;
		$users_code = $_GET['emp_code'];
        $form_reload_page = "chicken_item_stockreport1.php?db=".$db;
	}
    $file_name = "Item Stock Report";

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
	
    /*Check for Table Availability*/
    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
    if(in_array("item_stock_opening", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stock_opening LIKE poulso6_admin_chickenmaster.item_stock_opening;"; mysqli_query($conn,$sql1); }
    
    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
    while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$user_sector_filter." ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }
    
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $icat_cat[$row['code']] = $row['category']; }

	$fdate = $tdate = date("Y-m-d"); $item_cat = $items = $sectors = array(); $item_cat["all"] = $items["all"] = $sectors["all"] = "all"; $sec_all_flag = 0;
    $exports = "display";
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fdate']));
		$tdate = date("Y-m-d",strtotime($_POST['tdate']));
		// $sectors = $_POST['sectors'];
		// $item_cat = $_POST['item_cat'];
		// $items = $_POST['items'];
		$exports = $_POST['exports'];

        // 1) Collect categories and flag “all”
$item_cat      = array();
$itmc_all_flag = 0;
foreach ($_POST['item_cat'] as $cat) {
    $item_cat[$cat] = $cat;
    if ($cat === 'all') {
        $itmc_all_flag = 1;
    }
}

// 2) Collect items and flag “all”
$items        = array();
$itm_all_flag = 0;
foreach ($_POST['items'] as $itm) {
    $items[$itm] = $itm;
    if ($itm === 'all') {
        $itm_all_flag = 1;
    }
}

// 3) Initialize filters
$item_filter  = '';
$item_filter1 = '';

// 4) Priority A: user picked specific items → filter by those
if ($itm_all_flag === 0) {
    $escaped = array_map('addslashes', array_keys($items));      // use keys to dedupe
    $inList  = "'" . implode("','", $escaped) . "'";
    $item_filter  = " AND `itemcode` IN ($inList)";
    $item_filter1 = " AND `code`     IN ($inList)";

// 5) Priority B: user selected “all” categories → no filter
} elseif ($itmc_all_flag === 1) {
    // leave both filters empty

// 6) Priority C: items=all but specific categories → lookup codes by category
} else {
    // assume you’ve already loaded a map: $item_category_map[code] = category
    // and possibly a master list of codes: $all_codes[]
    $icat_list = '';
    foreach ($item_code as $code) {
        if (isset($icat_cat[$code]) 
            && in_array($icat_cat[$code], array_keys($item_cat), true)
        ) {
            $icat_list .= $icat_list === '' 
                ? addslashes($code) 
                : "','". addslashes($code);
        }
    }
    if ($icat_list !== '') {
        $inCat = "'$icat_list'";
        $item_filter  = " AND `itemcode` IN ($inCat)";
        $item_filter1 = " AND `code`     IN ($inCat)";
    }
}

        $sectors = array(); $sec_list = "";
        foreach($_POST['sectors'] as $scts){ $sectors[$scts] = $scts; if($scts == "all"){ $sec_all_flag = 1; } }
        $sects_list = implode("','", array_map('addslashes', $sectors));
        $sector_filter = $sector_filter1 = $sector_filter2 = "";
        if($sec_all_flag == 1 ){ $sector_filter = $sector_filter1 = $sector_filter2 = ""; $sec_list = "all"; }
        else { $sector_filter = "AND `warehouse` IN ('$sects_list')"; $sector_filter1 = " AND `fromwarehouse` IN ('$sects_list')"; $sector_filter2 = " AND `towarehouse` IN ('$sects_list')"; $sec_list = implode(",",$sectors); }
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
                                        <div class="form-group" style="width:170px;">
                                            <label>Category</label>
                                            <select name="item_cat[]" id="item_cat[0]" class="form-control select2" style="width:160px;" onchange="fetch_item_list();" multiple>
                                                <option value="all" <?php if (in_array("all", $item_cat)) echo "selected"; ?>>-All-</option>
                                                <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                                <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:150px;">
                                            <label for="items">Item</label>
                                            <?php
  // assume you’ve already built these exactly as before:
  //   $item_cat      = [ cat1=>cat1, cat2=>cat2, … ];
  //   $itmc_all_flag = (in_array('all', $_POST['item_cat']) ? 1 : 0);
  //   $items         = [ icodeA=>icodeA, icodeB=>icodeB, … ];
  //   $itm_all_flag  = (in_array('all', $_POST['items'])     ? 1 : 0);
?>

<select name="items[]" id="items" class="form-control select2" style="width:140px;" multiple >
    <!-- “All” option -->
    <option value="all" <?php if (in_array("all", $items)) echo "selected"; ?> >-All-</option>
    <?php
      // If user chose *all* categories, show every code
      if ($itmc_all_flag === 1):
        foreach ($item_code as $icode):
          if ($item_name[$icode] === "") continue;
          $sel = in_array($icode, array_keys($items), true) ? "selected" : "";
    ?>
        <option value="<?php echo $icode; ?>" <?php echo $sel; ?>>
          <?php echo $item_name[$icode]; ?>
        </option>
    <?php
        endforeach;

      // Else: only show codes whose category lives in $item_cat
      else:
        foreach ($item_code as $icode):
          $catOfCode = $item_category[$icode] ?? '';
          if (! isset($item_cat[$catOfCode])) continue;
          $sel = in_array($icode, array_keys($items), true) ? "selected" : "";
    ?>
        <option value="<?php echo $icode; ?>" <?php echo $sel; ?>>
          <?php echo $item_name[$icode]; ?>
        </option>
    <?php
        endforeach;
      endif;
    ?>
</select>

                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Warehouse</label>
                                            <select name="sectors[]" id="sectors[0]" class="form-control select2" style="width:180px;" multiple>
                                                <option value="all" <?php if (in_array("all", $sectors)) echo "selected"; ?>>-All-</option>
											    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if(in_array($scode, $sectors)){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="m-1 p-1 row">
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
                            if($items != "all"){ $item_filter = " AND `itemcode` IN ('$items')"; }
                            else if($item_cat == "all"){ $item_filter = ""; }
                            else{
                                $icat_list = $item_filter = "";
                                foreach($item_code as $icode){
                                    $item_category[$icode];
                                    if(!empty($item_category[$icode]) && $item_category[$icode] == $item_cat){
                                        if($icat_list == ""){ $icat_list = $icode; } else{ $icat_list = $icat_list."','".$icode; }
                                    }
                                }
                                $item_filter = " AND `itemcode` IN ('$icat_list')";
                                $item_filter1 = " AND `code` IN ('$icat_list')";
                            }
                            
                            // if($sectors == "all"){ $sec_list = implode("','",$sector_code); $sector_filter = " AND `warehouse` IN ('$sec_list')"; $sector_filter1 = " AND `fromwarehouse` IN ('$sec_list')"; $sector_filter2 = " AND `towarehouse` IN ('$sec_list')"; }
                            // else{ $sector_filter = " AND `warehouse` IN ('$sectors')"; $sector_filter1 = " AND `fromwarehouse` IN ('$sectors')"; $sector_filter2 = " AND `towarehouse` IN ('$sectors')"; }
                            
                            $html = '';
                            $html .= '<thead class="thead2" id="head_names">';

                            $nhead_html .= '<tr>';
                            $nhead_html .= '<th>Sl&nbsp;No.</th>';
                            $nhead_html .= '<th>Category</th>';
                            $nhead_html .= '<th>Item</th>';
                            $nhead_html .= '<th colspan = "3">Opening</th>';
                            $nhead_html .= '<th colspan = "3">Stock In</th>';
                            $nhead_html .= '<th colspan = "3">Stock Out</th>';
                            $nhead_html .= '<th colspan = "3">Closing</th>';
                            $nhead_html .= '</tr>';

                            $nhead_html .= '<tr>';
                            $nhead_html .= '<th></th>';
                            $nhead_html .= '<th></th>';
                            $nhead_html .= '<th></th>';
                            $nhead_html .= '<th>Quantity</th>';
                            $nhead_html .= '<th>Price</th>';
                            $nhead_html .= '<th>Amount</th>';
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
                            $fhead_html .= '<th id="order">Sl&nbsp;No.</th>';
                            $fhead_html .= '<th id="order">Category</th>';
                            $fhead_html .= '<th id="order">Item</th>';
                            $fhead_html .= '<th colspan = "3" id="order">Opening</th>';
                            $fhead_html .= '<th colspan = "3" id="order">Stock In</th>';
                            $fhead_html .= '<th colspan = "3" id="order">Stock Out</th>';
                            $fhead_html .= '<th colspan = "3" id="order">Closing</th>';
                            $fhead_html .= '</tr>';

                            $fhead_html .= '<tr>';
                            $fhead_html .= '<th id="order"></th>';
                            $fhead_html .= '<th id="order"></th>';
                            $fhead_html .= '<th id="order"></th>';
                            $fhead_html .= '<th id="order_num">Quantity</th>';
                            $fhead_html .= '<th id="order_num">Price</th>';
                            $fhead_html .= '<th id="order_num">Amount</th>';
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

                            $im_st_qnt_opn = $im_st_prc_opn = $im_st_amt_opn = $im_st_qnt_cls = $im_st_prc_cls = $im_st_amt_cls = $im_trans_in_qnt_btw =  $im_trans_in_prc_opn =  $im_trans_in_amt_btw = array();

                            // Opening Table 
                            $sql = "SELECT * FROM `item_stock_opening` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `dflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_st_qnt_opn[$key] = $row['quantity'];
                                    $im_st_prc_opn[$key] = $row['price'];
                                    $im_st_amt_opn[$key] = $row['amount'];
                                } else { 
                                    $im_st_qnt_bet[$key] = $row['quantity'];
                                    $im_st_prc_bet[$key] = $row['price'];
                                    $im_st_amt_bet[$key] = $row['amount'];
                                }
                            }
                            // Closing Table 
                            // $sql = "SELECT * FROM `item_closingstock` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
                            // $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            // while($row = mysqli_fetch_assoc($query)){ 
                            //     $key = $row['code'];
                                
                            //     $im_st_qnt_cls[$key] = $row['closedquantity'];
                            //     $im_st_prc_cls[$key] = $row['price'];
                            //     $im_st_amt_cls[$key] = $row['amount'];
                            // }
                            // Purchase Table 
                            $sql = "SELECT * FROM `pur_purchase` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                // Opening Purchase
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_pur_qnt_opn[$key] += $row['netweight'];
                                    $im_pur_prc_opn[$key] += $row['itemprice'];
                                    $im_pur_amt_opn[$key] += $row['totalamt'];
                                } else {
                                    $im_pur_qnt_btw[$key] += $row['netweight'];
                                    $im_pur_prc_btw[$key] += $row['itemprice'];
                                    $im_pur_amt_btw[$key] += $row['totalamt'];
                                }
                            }
                            // Stock Transfer IN Table 
                            $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate'".$item_filter1."".$sector_filter1." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['code'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_trans_in_qnt_opn[$key] += $row['quantity'];
                                    $im_trans_in_prc_opn[$key] += $row['price'];
                                    $im_trans_in_amt_opn[$key] += $im_trans_in_qnt_opn[$key] * $im_trans_in_prc_opn[$key];
                                } else {
                                    $im_trans_in_qnt_btw[$key] += $row['quantity'];
                                    $im_trans_in_prc_btw[$key] += $row['price'];
                                    $im_trans_in_amt_btw[$key] += $im_trans_in_qnt_btw[$key] * $im_trans_in_prc_btw[$key];
                                }
                            }
                            // Stock Transfer Out Table 
                            $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate'".$item_filter1."".$sector_filter2." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['code'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_trans_out_qnt_opn[$key] += (float)$row['quantity'];
                                    $im_trans_out_prc_opn[$key] += (float)$row['price'];
                                    $im_trans_out_amt_opn[$key] += (float)$im_trans_out_qnt_opn[$key] * (float)$im_trans_out_prc_opn[$key];
                                } else {
                                    $im_trans_out_qnt_btw[$key] += $row['quantity'];
                                    $im_trans_out_prc_btw[$key] += $row['price'];
                                    $im_trans_out_amt_btw[$key] += $im_trans_out_qnt_btw[$key] * $im_trans_out_prc_btw[$key];
                                }
                            }
                            $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				            while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
                            $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `contacttype` LIKE '%S%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				            while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }
                            $cus_list = implode("','", $cus_code); $sup_list = implode("','", $sup_code);

                            // Sales Return Table 
                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `vcode` IN ('$cus_list') AND `rtype` LIKE '%add%' AND `dflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_sal_return_qnt_opn[$key] += $row['quantity'];
                                    $im_sal_return_prc_opn[$key] += $row['price'];
                                    $im_sal_return_amt_opn[$key] += $row['amount'];
                                } else {
                                    $im_sal_return_qnt_btw[$key] += $row['quantity'];
                                    $im_sal_return_prc_btw[$key] += $row['price'];
                                    $im_sal_return_amt_btw[$key] += $row['amount'];
                                }
                            }
                            // Purchase Return Table 
                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `vcode` IN ('$sup_list') AND `rtype` LIKE '%add%' AND `dflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_pur_return_qnt_opn[$key] += $row['quantity'];
                                    $im_pur_return_prc_opn[$key] += $row['price'];
                                    $im_pur_return_amt_opn[$key] += $row['amount'];
                                } else {
                                    $im_pur_return_qnt_btw[$key] += $row['quantity'];
                                    $im_pur_return_prc_btw[$key] += $row['price'];
                                    $im_pur_return_amt_btw[$key] += $row['amount'];
                                }
                            }
                            // Stock Adjustment ADD Table 
                            $sql = "SELECT * FROM `item_stock_adjustment` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `a_type` LIKE '%add%' AND `dflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_adj_add_qnt_opn[$key] += $row['nweight'];
                                    $im_adj_add_prc_opn[$key] += $row['price'];
                                    $im_adj_add_amt_opn[$key] += $row['amount'];
                                } else {
                                    $im_adj_add_qnt_btw[$key] += $row['nweight'];
                                    $im_adj_add_prc_btw[$key] += $row['price'];
                                    $im_adj_add_amt_btw[$key] += $row['amount'];
                                }
                            }
                            // Stock Adjustment Deduct Table 
                            $sql = "SELECT * FROM `item_stock_adjustment` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `a_type` LIKE '%deduct%' AND `dflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_adj_ded_qnt_opn[$key] += $row['nweight'];
                                    $im_adj_ded_prc_opn[$key] += $row['price'];
                                    $im_adj_ded_amt_opn[$key] += $row['amount'];
                                } else {
                                    $im_adj_ded_qnt_btw[$key] += $row['nweight'];
                                    $im_adj_ded_prc_btw[$key] += $row['price'];
                                    $im_adj_ded_amt_btw[$key] += $row['amount'];
                                }
                            }
                            // Sales Table 
                            $sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_sale_qnt_opn[$key] += $row['nweight'];
                                    $im_sale_prc_opn[$key] += $row['price'];
                                    $im_sale_amt_opn[$key] += $row['amount'];
                                } else {
                                    $im_sale_qnt_btw[$key] += $row['nweight'];
                                    $im_sale_prc_btw[$key] += $row['price'];
                                    $im_sale_amt_btw[$key] += $row['amount'];
                                }
                            }
                            // Mortality Table 
                            $sql = "SELECT * FROM `main_mortality` WHERE `date` <= '$tdate'".$item_filter."".$sector_filter." AND `dflag` = '0' ORDER BY `id` DESC";
                            $query = mysqli_query($conn,$sql); $tot_qty = $tot_amt = 0; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){ 
                                $key = $row['itemcode'];
                                // Opening Transfer
                                if(strtotime($row['date']) < strtotime($fdate)){ 
                                    $im_mort_qnt_opn[$key] += $row['nweight'];
                                    $im_mort_prc_opn[$key] += $row['itemprice'];
                                    $im_mort_amt_opn[$key] += $row['finaltotal'];
                                } else {
                                    $im_mort_qnt_btw[$key] += $row['nweight'];
                                    $im_mort_prc_btw[$key] += $row['price'];
                                    $im_mort_amt_btw[$key] += $row['finaltotal'];
                                }
                            }
                            
                            foreach($item_code as $icode){

                                $opening_qty = ((float)$im_st_qnt_opn[$icode] + (float)$im_pur_qnt_opn[$icode] + (float)$im_trans_in_qnt_opn[$icode] + (float)$im_sal_return_qnt_opn[$icode] + (float)$im_adj_add_qnt_opn[$icode]) - ((float)$im_mort_qnt_opn[$icode] + (float)$im_sale_qnt_opn[$icode] + (float)$im_trans_out_qnt_opn[$icode] + (float)$im_pur_return_qnt_opn[$icode] + (float)$im_adj_ded_qnt_opn[$icode]);
                                $opening_prc = ((float)$im_st_prc_opn[$icode] + (float)$im_pur_prc_opn[$icode] + (float)$im_trans_in_prc_opn[$icode] + (float)$im_sal_return_prc_opn[$icode] + (float)$im_adj_add_prc_opn[$icode]) - ((float)$im_mort_prc_opn[$icode] + (float)$im_sale_prc_opn[$icode] + (float)$im_trans_out_prc_opn[$icode] + (float)$im_pur_return_prc_opn[$icode] + (float)$im_adj_ded_prc_opn[$icode]);
                                $opening_amt = ((float)$im_st_amt_opn[$icode] + (float)$im_pur_amt_opn[$icode] + (float)$im_trans_in_amt_opn[$icode] + (float)$im_sal_return_amt_opn[$icode] + (float)$im_adj_add_amt_opn[$icode]) - ((float)$im_mort_amt_opn[$icode] + (float)$im_sale_amt_opn[$icode] + (float)$im_trans_out_amt_opn[$icode] + (float)$im_pur_return_amt_opn[$icode] + (float)$im_adj_ded_amt_opn[$icode]);

                                $bet_in_qty = ((float)$im_st_qnt_bet[$icode] + (float)$im_pur_qnt_btw[$icode] + (float)$im_trans_in_qnt_btw[$icode] + (float)$im_sal_return_qnt_btw[$icode] + (float)$im_adj_add_qnt_btw[$icode]);
                                $bet_in_prc = ((float)$im_st_prc_bet[$icode] + (float)$im_pur_prc_btw[$icode] + (float)$im_trans_in_prc_btw[$icode] + (float)$im_sal_return_prc_btw[$icode] + (float)$im_adj_add_prc_btw[$icode]);
                                $bet_in_amt = ((float)$im_st_amt_bet[$icode] + (float)$im_pur_amt_btw[$icode] + (float)$im_trans_in_amt_btw[$icode] + (float)$im_sal_return_amt_btw[$icode] + (float)$im_adj_add_amt_btw[$icode]);

                                $bet_out_qty = ((float)$im_mort_qnt_btw[$icode] + (float)$im_sale_qnt_btw[$icode] + (float)$im_trans_out_qnt_btw[$icode] + (float)$im_pur_return_qnt_btw[$icode] + (float)$im_adj_ded_qnt_btw[$icode]);
                                $bet_out_prc = ((float)$im_mort_prc_btw[$icode] + (float)$im_sale_prc_btw[$icode] + (float)$im_trans_out_prc_btw[$icode] + (float)$im_pur_return_prc_btw[$icode] + (float)$im_adj_ded_prc_btw[$icode]);
                                $bet_out_amt = ((float)$im_mort_amt_btw[$icode] + (float)$im_sale_amt_btw[$icode] + (float)$im_trans_out_amt_btw[$icode] + (float)$im_pur_return_amt_btw[$icode] + (float)$im_adj_ded_amt_btw[$icode]);

                                $closing_qty = (float)$opening_qty + (float)$bet_in_qty - (float)$bet_out_qty;
                                $closing_prc = (float)$opening_prc + (float)$bet_in_prc - (float)$bet_out_prc;
                                $closing_amt = (float)$opening_amt + (float)$bet_in_amt - (float)$bet_out_amt;

                                $html .= '<tr>';
                                $html .= '<td style="text-align:left;">'.$sl++.'</td>';
                                $html .= '<td style="text-align:left;">'.$icat_name[$icat_cat[$icode]].'</td>';
                                $html .= '<td style="text-align:left;">'.$item_name[$icode].'</td>';
                               
                                $html .= '<td style="text-align:right;">'.number_format_ind($opening_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opening_prc).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opening_amt).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bet_in_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bet_in_prc).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bet_in_amt).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bet_out_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bet_out_prc).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bet_out_amt).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($closing_qty).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($closing_prc).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($closing_amt).'</td>';
                                
                                $html .= '</tr>';
                                
                                $tot_qty += (float)$opening_qty;
                                $tot_st_in_qty += (float)$im_trans_in_qnt_btw[$icode];
                                $tot_st_out_qty += (float)$im_trans_out_qnt_btw[$icode];
                                $tprice += (float)$opening_prc;
                                $tot_in_price += (float)$im_trans_in_prc_btw[$icode];
                                $tot_out_price += (float)$im_trans_out_prc_btw[$icode];
                                $tot_amt += (float)$opening_amt;
                                $tot_in_amt += (float)$im_trans_in_amt_btw[$icode];
                                $tot_out_amt += (float)$im_trans_out_amt_btw[$icode];
                            }

                            $html .= '</tbody>';
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:left;" colspan="3">Grand Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_qty).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tprice).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_amt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_st_in_qty).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_in_price).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_in_amt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_st_out_qty).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_out_price).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_out_amt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_qty).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tprice).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($tot_amt).'</th>';
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
