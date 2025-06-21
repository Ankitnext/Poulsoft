<?php
//cus_wise_salesres1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "cus_wise_salesres1.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_vehiclewise_ledger1.php?db=".$db;
}
include "number_format_ind.php";

function decimal_adjustments($a,$b){
    if($a == ""){ $a = 0; }
    $a = round($a,$b);
    $c = explode(".",$a);
    $ed = "";
    $iv = 0;
    if($c[1] == ""){ $iv = 1; }
    else{ $iv = strlen($c[1]); }
    if($iv == 0){ $iv = 1; }
    for($d = $iv;$d < $b;$d++){ if($ed == ""){ $ed = "0"; } else{ $ed .= "0"; } }
    return $a."".$ed;
}
$file_name = "Date Wise Customer Sales And Receipts Report";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $loc_access = ""; $adm_aflag = 0;
while($row = mysqli_fetch_assoc($query)){ $loc_access = $row['loc_access']; if((int)$row['supadmin_access'] == 1 || (int)$row['admin_access'] == 1){ $adm_aflag = 1; } }

//Sector Access Filter
if($loc_access == "" || $loc_access == "all"){ $sec_fltr = ""; }
else{
    $loc1 = explode(",",$loc_access); $loc_list = "";
    foreach($loc1 as $loc2){ if($loc_list = ""){ $loc_list = $loc2; } else{ $loc_list = $loc_list."','".$loc2; } }
    $sec_fltr = " AND `code` IN ('$loc_list')";
}

//Sector Details
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Item Details
$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

//Supervisor Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $csup_alist = array();
while($row = mysqli_fetch_assoc($query)){ $csup_alist[$row['supr_code']] = $row['supr_code']; }
$supv_list = implode("','",$csup_alist);
$sql = "SELECT * FROM `chicken_employee` WHERE `code` IN ('$supv_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $supv_code = $supv_name = array();
while($row = mysqli_fetch_assoc($query)){ $supv_code[$row['code']] = $row['code']; $supv_name[$row['code']] = $row['name']; }

//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

// Logo Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query);
if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

$sale_fdate = $sale_tdate = $rct_fdate = $rct_tdate = date("Y-m-d"); $supervisors = "select"; $area_acode = array(); $aa_flag = 0;
$fstyles = $fsizes = "default"; $exports = "display";
if(isset($_POST['submit']) == true){
    $sale_fdate = date("Y-m-d",strtotime($_POST['sale_fdate']));
    $sale_tdate = date("Y-m-d",strtotime($_POST['sale_tdate']));
    $rct_fdate = date("Y-m-d",strtotime($_POST['rct_fdate']));
    $rct_tdate = date("Y-m-d",strtotime($_POST['rct_tdate']));
    $supervisors = $_POST['supervisors'];
    foreach($_POST['areas'] as $t1){ $area_acode[$t1] = $t1; if($t1 == "all" || $t1 == ""){ $aa_flag = 1; } }
    if($aa_flag == 0){ $area_list = implode("','",$area_acode); $area_fltr = " AND `area_code` IN ('$area_list')"; }

    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
}
//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `supr_code` IN ('$supervisors')".$area_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = $ven_mobile = $cus_alist = $carea_alist = $obcramt = $obdramt = array();
while($row = mysqli_fetch_assoc($query)){
    $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; $ven_mobile[$row['code']] = $row['mobileno'];
    $cus_alist[$row['code']] = $row['code']; $carea_alist[$row['area_code']] = $row['area_code'];
    if($row['obtype'] == "Cr"){ $obcramt[$row['code']] = $row['obamt']; } else if($row['obtype'] == "Dr"){ $obdramt[$row['code']] = $row['obamt']; } else{ }
}
//Area Details
$area_list = implode("','",$carea_alist);
$sql = "SELECT * FROM `main_areas` WHERE `code` IN ('$area_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $area_code = $area_name = array();
while($row = mysqli_fetch_assoc($query)){ $area_code[$row['code']] = $row['code']; $area_name[$row['code']] = $row['description']; }

?>
<html>
	<head>
          <title><?php echo $file_name; ?></title>
        <?php include "header_head2.php"; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <style>
            .main-table { white-space: nowrap; }
            .tbody1{
                color: black;
            }
        </style>
	</head>
	<body>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table <?php if($exports == "print") { echo ' class="main-table"'; } else{ echo ' class="table-sm table-hover main-table2"'; } ?>>
                        <thead class="thead1">
                            <?php
                            if($dlogo_flag > 0) { ?>
                                <tr>
                                    <td rowspan="2" colspan="4"><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                                    
                                    <td colspan="15" align="center">
                                        <h6><?php echo $file_name; ?></h6>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="15" align="center">
                                        <!-- <h6>STATEMENT FROM DATE <?php echo date("d.m.Y",strtotime($fdate)); ?> - TO DATE <?php echo date("d.m.Y",strtotime($tdate)); ?></h6> -->
                                    </td>
                                </tr>
                            <?php }
                            else{ 
                            ?>
                            <tr>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                
                                <td colspan="15" align="center">
                                    <h3><?php echo $file_name; ?></h3>
                                </td>
                            </tr>
                            <?php } ?>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="sale_fdate">Sales From</label>
                                            <input type="text" name="sale_fdate" id="sale_fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($sale_fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" onchange="fetch_dates(this.id);" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="sale_tdate">Sales To</label>
                                            <input type="text" name="sale_tdate" id="sale_tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($sale_tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>

                                        <div class="form-group" style="width:110px;">
                                            <label for="rct_fdate">Receipt From</label>
                                            <input type="text" name="rct_fdate" id="rct_fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($rct_fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" onchange="fetch_dates(this.id);" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="rct_tdate">Receipt To</label>
                                            <input type="text" name="rct_tdate" id="rct_tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($rct_tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="m-2 form-group" style="width:200px;">
                                            <label for="supervisors">Supervisor</label>
                                            <select name="supervisors" id="supervisors" class="form-control select2" style="width:190px;" onchange="fetch_careas();">
                                                <option value="select" <?php if($supervisors == "select"){ echo "selected"; } ?>>-Select-</option>
                                                <?php foreach($supv_code as $gcode){ if($supv_name[$gcode] != ""){ ?>
                                                <option value="<?php echo $gcode; ?>" <?php if($supervisors == $gcode){ echo "selected"; } ?>><?php echo $supv_name[$gcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="m-2 form-group" style="width:200px;">
                                            <label for="areas">Area</label>
                                            <select name="areas[]" id="areas" class="form-control select2" style="width:190px;" multiple >
                                                <option value="all" <?php foreach($area_acode as $areas){ if($areas == "all"){ echo "selected"; } } ?>>-All-</option>
                                                <?php foreach($area_code as $gcode){ if($area_name[$gcode] != ""){ ?>
                                                <option value="<?php echo $gcode; ?>" <?php foreach($area_acode as $areas){ if($areas == $gcode){ echo "selected"; } } ?>><?php echo $area_name[$gcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <?php if((int)$font_fflag == 1){ ?>
                                        <div class="form-group" style="width:190px;">
                                            <label for="fstyles">Font-Family</label>
                                            <select name="fstyles" id="fstyles" class="form-control select2" style="width:180px;">
                                                <option value="default" <?php if($fstyles == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_id as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fstyles == $scode){ echo "selected"; } ?>><?php echo $font_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:70px;">
                                            <label for="fsizes">Font-Size</label>
                                            <select name="fsizes" id="fsizes" class="form-control select2" style="width:60px;">
                                                <option value="default" <?php if($fsizes == "default"){ echo "selected"; } ?>>-Default-</option>
											    <?php foreach($font_sizes as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($fsizes == $scode){ echo "selected"; } ?>><?php echo $font_sizes[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
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
                    <?php if($exports == "display" || $exports == "exportpdf"){ ?>
                    </table>
                    <table class="main-table table-sm table-hover" id="main_table">
                    <?php } ?>
						<?php
                        }
                        if(isset($_POST['submit']) == true){
                            $html = '';
                            //Sales
                            $html .= '<thead class="thead2">';
                            $html .= '<tr>';
                            $html .= '<th style="text-align:center;">S.No.</th>';
                            $html .= '<th style="text-align:center;" id="order">Customer Name</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Mobile</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Opening</th>';
                            $html .= '<th style="text-align:center;" id="order_num">KGS</th>';
                            $html .= '<th style="text-align:center;" id="order_num">SALES AMT</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Receipt</th>';
                            $html .= '<th style="text-align:center;" id="order_num">Balance</th>';
                            $html .= '<th style="text-align:center;width:100px;" id="order_num">Closing</th>';
                            $html .= '</thead>';
                            $html .= '</tr>';
                            $html .= '<tbody>';
                            
                            $cus_list = implode("','",$cus_alist);
                            //Opening Balances
                            $old_inv = ""; $oinv = $orct = $ocdn = $occn = $omortality = $oreturns = array();
                            $sql = "SELECT * FROM `customer_sales` WHERE `date` < '$sale_fdate' AND `customercode` IN ('$cus_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv[$row['customercode']] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
                            $sql = "SELECT * FROM `customer_receipts` WHERE  `date` < '$sale_fdate' AND `ccode` IN ('$cus_list') AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $orct[$row['ccode']] += (float)$row['amount']; } }
                            $sql = "SELECT * FROM `main_crdrnote` WHERE  `date` < '$sale_fdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CCN','CDN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn[$row['ccode']] += (float)$row['amount']; } else { $occn[$row['ccode']] += (float)$row['amount']; } } }
                            $sql = "SELECT * FROM `main_mortality` WHERE `date` < '$sale_fdate' AND `ccode` IN ('$cus_list') AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $omortality[$row['ccode']] += (float)$row['amount']; } }
                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$sale_fdate' AND `vcode` IN ('$cus_list') AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $oreturns[$row['vcode']] += (float)$row['amount']; } }

                            if($supv_name[$supervisors] == "Vehicle"){
                                //Between Days Balances
                                $old_inv = ""; $bqty = $binv = $brct = $bcdn = $bccn = $bmort = $brtn = array();
                                $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `customercode` IN ('$cus_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bqty[$row['customercode']] += (float)$row['netweight']; if($old_inv != $row['invoice']){ $binv[$row['customercode']] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
                                $sql = "SELECT * FROM `customer_receipts` WHERE  `date` >= '$sale_fdate' AND `date` <= '$rct_tdate' AND `ccode` IN ('$cus_list') AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $brct[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_crdrnote` WHERE  `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CDN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bcdn[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$sale_fdate' AND `date` <= '$rct_tdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CCN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bccn[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_mortality` WHERE `date` >= '$sale_fdate' AND `date` <= '$rct_tdate' AND `ccode` IN ('$cus_list') AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bmort[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$sale_fdate' AND `date` <= '$rct_tdate' AND `vcode` IN ('$cus_list') AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $brtn[$row['vcode']] += (float)$row['amount']; } }
                            }else{
                              //Between Days Balances
                              $old_inv1 = ""; $bqty1 = $binv1 = $brct1 = $bcdn1 = $bccn1 = $bmort1 = $brtn1 = array();
                              $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `customercode` IN ('$cus_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                              if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bqty1[$row['customercode']] += (float)$row['netweight']; if($old_inv1 != $row['invoice']){ $binv1[$row['customercode']] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
                              $sql = "SELECT * FROM `customer_receipts` WHERE  `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `ccode` IN ('$cus_list') AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                              if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $brct1[$row['ccode']] += (float)$row['amount']; } }
                              $sql = "SELECT * FROM `main_crdrnote` WHERE  `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CDN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                              if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bcdn1[$row['ccode']] += (float)$row['amount']; } }
                              $sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CCN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                              if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bccn1[$row['ccode']] += (float)$row['amount']; } }
                              $sql = "SELECT * FROM `main_mortality` WHERE `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `ccode` IN ('$cus_list') AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                              if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bmort1[$row['ccode']] += (float)$row['amount']; } }
                              $sql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `vcode` IN ('$cus_list') AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                              if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $brtn1[$row['vcode']] += (float)$row['amount']; } }


                                //Between Days Balances
                                $old_inv = ""; $bqty = $binv = $brct = $bcdn = $bccn = $bmort = $brtn = array();
                                $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$sale_fdate' AND `date` <= '$sale_tdate' AND `customercode` IN ('$cus_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bqty[$row['customercode']] += (float)$row['netweight']; if($old_inv != $row['invoice']){ $binv[$row['customercode']] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
                                $sql = "SELECT * FROM `customer_receipts` WHERE  `date` >= '$rct_fdate' AND `date` <= '$rct_tdate' AND `ccode` IN ('$cus_list') AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $brct[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_crdrnote` WHERE  `date` >= '$rct_fdate' AND `date` <= '$rct_tdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CDN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bcdn[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$rct_fdate' AND `date` <= '$rct_tdate' AND `ccode` IN ('$cus_list') AND `mode` IN ('CCN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bccn[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_mortality` WHERE `date` >= '$rct_fdate' AND `date` <= '$rct_tdate' AND `ccode` IN ('$cus_list') AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $bmort[$row['ccode']] += (float)$row['amount']; } }
                                $sql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$rct_fdate' AND `date` <= '$rct_tdate' AND `vcode` IN ('$cus_list') AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                                if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $brtn[$row['vcode']] += (float)$row['amount']; } }
                            }

                           

                            $c = 0;
                            foreach($ven_code as $ccode){
                               
                                $sales = (float)$oinv[$ccode] + (float)$ocdn[$ccode] + (float)$obdramt[$ccode];
                                $receipts = (float)$orct[$ccode] + (float)$omortality[$ccode] + (float)$oreturns[$ccode] + (float)$occn[$ccode] + (float)$obcramt[$ccode];
                                $opn_bal = $sales - $receipts;

                                $sale_wht = 0; if(!empty($bqty[$ccode]) && $bqty[$ccode] != ""){ $sale_wht = (float)$bqty[$ccode]; }
                                $sale_amt = 0; if(!empty($binv[$ccode]) && $binv[$ccode] != ""){ $sale_amt = (float)$binv[$ccode]; }
                                $cdn_amt = 0; if(!empty($bcdn[$ccode]) && $bcdn[$ccode] != ""){ $cdn_amt = (float)$bcdn[$ccode]; }
                                $rct_amt = 0; if(!empty($brct[$ccode]) && $brct[$ccode] != ""){ $rct_amt = (float)$brct[$ccode]; }
                                $ccn_amt = 0; if(!empty($bccn[$ccode]) && $bccn[$ccode] != ""){ $ccn_amt = (float)$bccn[$ccode]; }
                                $mrt_amt = 0; if(!empty($bmort[$ccode]) && $bmort[$ccode] != ""){ $mrt_amt = (float)$bmort[$ccode]; }
                                $rtn_amt = 0; if(!empty($brtn[$ccode]) && $brtn[$ccode] != ""){ $rtn_amt = (float)$brtn[$ccode]; }
                                if($supv_name[$supervisors] == "Vehicle"){

                                    $bsal_amt = (float)$sale_amt + (float)$cdn_amt;
                                    $brct_amt = (float)$rct_amt + (float)$ccn_amt + (float)$rtn_amt + (float)$mrt_amt;
                                    $bcls_amt = (float)$bsal_amt - (float)$brct_amt; if((float)$bcls_amt < 0){ $bcls_amt = 0; }
                                    $cls_amt = (((float)$opn_bal + (float)$bsal_amt) - (float)$brct_amt);

                                }else{
                                    $rct_amt1 = 0; if(!empty($brct1[$ccode]) && $brct1[$ccode] != ""){ $rct_amt1 = (float)$brct1[$ccode]; }
                                    $ccn_amt1 = 0; if(!empty($bccn1[$ccode]) && $bccn1[$ccode] != ""){ $ccn_amt1 = (float)$bccn1[$ccode]; }
                                    $rtn_amt1 = 0; if(!empty($brtn1[$ccode]) && $brtn1[$ccode] != ""){ $rtn_amt1 = (float)$brtn1[$ccode]; }
                                    $mrt_amt1 = 0; if(!empty($bmort1[$ccode]) && $bmort1[$ccode] != ""){ $mrt_amt1 = (float)$bmort1[$ccode]; }
                                    $brct_amt = (float)$rct_amt + (float)$ccn_amt + (float)$rtn_amt + (float)$mrt_amt;
                                    $bsal_amt = (float)$sale_amt + (float)$cdn_amt;
                                    $brct_amt1 = (float)$rct_amt1 + (float)$ccn_amt1 + (float)$rtn_amt1 + (float)$mrt_amt1;
                                    $bcls_amt = (float)$bsal_amt - (float)$brct_amt; if((float)$bcls_amt < 0){ $bcls_amt = 0; }
                                  

                                    

                                    $opn_bal = $opn_bal - $brct_amt1;

                                    $cls_amt = (((float)$opn_bal + (float)$bsal_amt) - (float)$brct_amt);

                                  
                                }
                                $total_open += $opn_bal;
                                $total_sale_wht += $sale_wht;
                                $total_bsal_amt += $bsal_amt;
                                $total_brct_amt += $brct_amt;
                                $total_bcls_amt += $bcls_amt;
                                $total_cls_amt += $cls_amt;

                                $c++;
                                $html .= '<tr>';
                                $html .= '<td style="text-align:center">'.$c.'</td>';
                                $html .= '<td style="text-align:left;" >'.$ven_name[$ccode].'</td>';
                                $html .= '<td style="text-align:left;">'.$ven_mobile[$ccode].'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($opn_bal).'</td>';
                                $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($sale_wht,2))).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bsal_amt).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($brct_amt).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($bcls_amt).'</td>';
                                $html .= '<td style="text-align:right;width:100px;">'.number_format_ind($cls_amt).'</td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>';
                            $html .= '<thead class="tfoot1">';
                            
                            $html .= '<tr>';
                            $html .= '<th colspan="3" style="text-align:center;">Total</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($total_open).'</th>';
                            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind(round($total_sale_wht,2))).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($total_bsal_amt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($total_brct_amt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($total_bcls_amt).'</th>';
                            $html .= '<th style="text-align:right;">'.number_format_ind($total_cls_amt).'</th>';
                            $html .= '</tr>';

                            $html .= '</thead>';
                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>

            function fetch_careas(){
                var supervisors = document.getElementById("supervisors").value;
                removeAllOptions(document.getElementById("areas"));
                //if(supervisors == "" || supervisors == "select"){ } else{}
                var fetch_areas = new XMLHttpRequest();
                var method = "GET";
                var url = "chicken_fetch_customer_areas.php?supervisors="+supervisors+"&type=from_emp";
                //window.open(url);
                var asynchronous = true;
                fetch_areas.open(method, url, asynchronous);
                fetch_areas.send();
                fetch_areas.onreadystatechange = function(){
                    if (this.readyState == 4 && this.status == 200) {
                        var area_list = this.responseText;
                        $('#areas').append(area_list);
                    }
                }
            }
            function checkval() {
                var users = document.getElementById("users").value;
                var sectors = document.getElementById("sectors").value;
                var l = true;
                if(users == "select"){
                    alert("Kindly select User");
                    l = false;
                }
                else if(sectors == "select"){
                    alert("Kindly select Shop/Outlet");
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
        <script src="searchbox.js"></script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var table = document.getElementById("main_table");
                    var workbook = XLSX.utils.book_new();
                    var worksheet = XLSX.utils.table_to_sheet(table);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
                    XLSX.writeFile(workbook, filename+".xlsx");
                    
                    $('#exports').select2();
                    document.getElementById("exports").value = "display";
                    $('#exports').select2();
                }
                else{ }
            }
            function cdate_format1() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split(".");
                    cell.textContent = adate[2]+"-"+adate[1]+"-"+adate[0];
                });
            }
            function cdate_format2() {
                const dateCells = document.querySelectorAll('#main_table .dates');
                var adate = [];
                dateCells.forEach(cell => {
                    let originalString = cell.textContent;
                    adate = []; adate = originalString.split("-");
                    cell.textContent = adate[2]+"."+adate[1]+"."+adate[0];
                });
            }
            function fetch_dates(a) {
                var type = "";
                if(a == "sale_fdate"){
                    type = "sale_rct_date";
                    var sale_fdate = document.getElementById("sale_fdate").value;
                }
                else{
                    type = "days_7";
                    var sale_fdate = document.getElementById("rct_fdate").value;
                }
                if(sale_fdate != ""){
                    var ven_bals = new XMLHttpRequest();
                    var method = "GET";
                    var url = "chicken_fetch_dates.php?fdate="+sale_fdate+"&type="+type;
                        //window.open(url);
                        var asynchronous = true;
                        ven_bals.open(method, url, asynchronous);
                        ven_bals.send();
                        ven_bals.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var date_dt1 = this.responseText;
                                if(a == "sale_fdate"){
                                    var date_dt2 = date_dt1.split("[@$&]");
                                    var sale_tdate = date_dt2[0];
                                    var rct_fdate = date_dt2[1];
                                    var rct_tdate = date_dt2[2];
                                    document.getElementById("sale_tdate").value = sale_tdate;
                                    document.getElementById("rct_fdate").value = rct_fdate;
                                    document.getElementById("rct_tdate").value = rct_tdate;
                                }
                                else{
                                    document.getElementById("rct_tdate").value = date_dt1;
                                }
                            }
                        }
                }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
         <script src="sort_table_columns.js"></script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
</html>
