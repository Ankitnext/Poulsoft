<?php
//chicken_weekly_cussales1_nb.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
// $cuss = $_GET['cuss'];
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_weekly_cussales1_new.php";
}
else{
    include "APIconfig.php";
    $dbname = $db;
    $users_code = $_GET['emp_code'];
    $form_reload_page = "chicken_weekly_cussales1_nb.php?db=".$db;
}
include "number_format_ind.php";
include "decimal_adjustments.php";
$file_name = "Customer Weekly Balance Report";

/*Check for Column Availability*/
$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $ecn_val = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $ecn_val[$i] = $row['Field']; $i++; }
if(in_array("dflag", $ecn_val, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `dflag` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }

/*Company Profile*/
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; $cmpy_fname = $row['fullcname']; }

// Logo Flag
$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

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
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sec_fltr." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

//Fetch User Details
if((int)$adm_aflag == 1){
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `dflag` = '0' ORDER BY `username` ASC";
}
else{
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$dbname' AND `empcode` LIKE '$emp_code' AND `dflag` = '0' ORDER BY `username` ASC";
}
$query = mysqli_query($conns,$sql); $usr_code = $usr_name = array();
while($row = mysqli_fetch_assoc($query)){ $usr_code[$row['empcode']] = $row['empcode']; $usr_name[$row['empcode']] = $row['username']; }

// Default date
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'chicken_weekly_cussales1_nb.php' AND `field_function` = 'sec_default date' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dt_flag = mysqli_num_rows($query); 
while($row = mysqli_fetch_assoc($query)){ if($dt_flag > 0) { $fdate = date("Y-m-d",strtotime($row['field_value'])); } }
//if($dt_flag > 0) { }
//Font-Styles
$sql = "SELECT * FROM `font_style_master` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `font_name1` ASC";
$query = mysqli_query($conn,$sql); $font_id = $font_name = array();
while($row = mysqli_fetch_assoc($query)){ $font_id[$row['id']] = $row['id']; if($row['font_name2'] != ""){ $font_name[$row['id']] = $row['font_name1'].",".$row['font_name2']; } else{ $font_name[$row['id']] = $row['font_name1']; } }
if(sizeof($font_id) > 0){ $font_fflag = 1; } else { $font_fflag = 0; }
for($i = 0;$i <= 30;$i++){ $font_sizes[$i."px"] = $i."px"; }

if($dt_flag > 0) { } else { $fdate = date("Y-m-d"); } $sectors = $cuss = "select"; $fstyles = $fsizes = "default"; $exports = "display"; $no_weeks = 12;
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $cuss = $_POST['cus_code'];
    $no_weeks = $_POST['no_weeks'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
    $currentIndex = array_search($cuss, $cus_code);
}



if (isset($_POST['next'])) {

    $cuss = $_POST['cus_code'];
    $fdate = date("Y-m-d", strtotime($_POST['fdate']));
    $no_weeks = $_POST['no_weeks'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
     $keys = array_keys($cus_code);

    // Find the index of current customer code
    $currentIndex = array_search($cuss, $keys);
     if ($currentIndex !== false && $currentIndex < count($keys) - 1) {
            $cuss = $keys[$currentIndex + 1];
        }
}

if (isset($_POST['pre'])) {

    $cuss = $_POST['cus_code'];
    $fdate = date("Y-m-d", strtotime($_POST['fdate']));
    $no_weeks = $_POST['no_weeks'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];
     $keys = array_keys($cus_code);

    // Find the index of current customer code
    $currentIndex = array_search($cuss, $keys);
     if ($currentIndex !== false && $currentIndex > 0) {
            $cuss = $keys[$currentIndex - 1];
        }
}
?>
<html>
	<head>
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
                            <tr>
                            <?php
                                if($dlogo_flag > 0) { ?>
                                    <td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                                <?php }
                                else{ 
                                ?>
                                <td colspan="2"><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
                                <td colspan="2"><?php echo $cdetails; ?></td>
                                <td colspan="18" align="center">
                                    <h3><?php echo $file_name; }?></h3>
                                </td>
                            </tr>
                        </thead>
						<?php if($exports == "display" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div> 
                                        <div class="form-group" style="width:110px;">
                                            <label for="no_weeks">No. of Weeks</label>
                                            <input type="text" name="no_weeks" id="no_weeks" class="form-control" value="<?php echo $no_weeks; ?>" onkeyup="validatenum(this.id);" style="padding:0;padding-left:2px;width:100px;" />
                                        </div>
                                          <!-- <button type="submit" name="pre" style="border: none; background: none; padding: 0;">
                                             <i class="fa fa-arrow-left" style="font-size: 18px; color: blue;margin-top:8px;"></i>
                                         </button>&nbsp;&nbsp;
                                        <div class="form-group" style="width:290px;">
                                            <label for="cus_code">Customer</label>
                                            <select name="cus_code" id="cus_code" class="form-control select2" style="width:280px;">
                                                <option value="select" <?php if($cuss == "select"){ echo "selected"; } ?>>-select-</option>
											    <?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($cuss == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                       
                                         
                                       <button type="submit" name="next" style="border: none; background: none; padding: 0;">
                                             <i class="fa fa-arrow-right" style="font-size: 18px; color: blue;margin-top:8px;"></i>
                                         </button> &nbsp;&nbsp; -->
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

                                         <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB</label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                                        </div>
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
                        if(isset($_POST['submit']) == true || isset($_POST['pre']) == true || isset($_POST['next']) == true){
                            $html = '';
                            //Sales
                            // $html .= '<tr class="thead2">';
                            // $html .= '<th colspan="8" style="text-align:center;">Customer Weekly Balance Sales</th>';
                            // $html .= '</tr>';
                            // $html .= '<tr class="thead2">';
                          
                            // $html .= '<th style="text-align:center;">Slno</th>';
                            // $html .= '<th style="text-align:center;">Customer</th>';
                            // $html .= '<th style="text-align:center;">Sale Amount</th>';
                            
                            // $html .= '</tr>';

                            $sdate = $edate = $min_sdate = $max_sdate = ""; $week_no = array(); $fdate1 = $fdate;
                            $sales_weeks = [];
                            $collection_weeks = [];

                            $sales_start = date('Y-m-d', strtotime($fdate . ' +1 day'));

                            for ($i = 0; $i < $no_weeks; $i++) {
                                // Calculate sales week
                                $current_sales_start = date('Y-m-d', strtotime($sales_start . ' + ' . ($i * 7) . ' days'));
                                $current_sales_end   = date('Y-m-d', strtotime($current_sales_start . ' + 6 days'));
                                $sales_weeks[] = $current_sales_start . '@' . $current_sales_end;

                                // Calculate collection week (immediately after sales week)
                                $collection_start = date('Y-m-d', strtotime($current_sales_end . ' +1 day'));
                                $collection_end   = date('Y-m-d', strtotime($collection_start . ' + 6 days'));
                                $collection_weeks[] = $collection_start . '@' . $collection_end;
                            }
                          

                            $sql1 = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'";
                            $query = mysqli_query($conn,$sql1); $obcramt = $obdramt = array();
                            while($row = mysqli_fetch_assoc($query)){
                                if($row['obtype'] == "Cr"){ $obcramt[$row['code']] = $row['obamt']; }
                                else if($row['obtype'] == "Dr"){ $obdramt[$row['code']] = $row['obamt']; }
                                else{ }
                            }
                        
                    // opening calculation
                            $openinv = $opencdn = $openrct = $openccn = $openmort = $openrtn = array();
                            $sql = "SELECT * FROM `customer_sales` WHERE `date` <= '$fdate' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC";
                            $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $key2 = $row['customercode']; if($old_inv != $row['invoice']){ $openinv[$key2] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
                          
                            $sql = "SELECT * FROM `main_crdrnote` WHERE  `date` <= '$fdate' AND `mode` IN ('CDN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $key3 = $row['ccode']; $opencdn[$key3] += (float)$row['amount']; } }
                             // rec

                            $sql = "SELECT * FROM `customer_receipts` WHERE  `date` <= '$fdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['ccode']; $openrct[$ky] += (float)$row['amount']; } }

                            $sql = "SELECT * FROM `main_crdrnote` WHERE `date` <= '$fdate' AND `mode` IN ('CCN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['ccode']; $openccn[$ky] += (float)$row['amount']; } }

                            $sql = "SELECT * FROM `main_mortality` WHERE `date` <= '$fdate' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['ccode']; $openmort[$ky] += (float)$row['amount']; } }

                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$fdate' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['vcode']; $openrtn[$ky] += (float)$row['amount']; } }

                            // $open_bal = (((float)$openinv['DBT-0131'] + (float)$opencdn['DBT-0131'] + (float)$obdramt['DBT-0131']) - ((float)$openrct['DBT-0131'] + (float)$openccn['DBT-0131'] + (float)$openrtn['DBT-0131'] + (float)$openmort['DBT-0131'] + (float)$obcramt['DBT-0131']));
                            // echo $open_bal;

                          

                            $bqty = $binv = $brct = $bcdn = $bccn = $bmort = $brtn = array();
                     

                        foreach($sales_weeks as $key => $value){

                            $aa = explode('@',$value);
                            $old_inv = ""; 
                            $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$aa[0]' AND `date` <= '$aa[1]' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; 
                            $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $key2 = $row['customercode']."@".$key; $bqty[$key2] += (float)$row['netweight']; if($old_inv != $row['invoice']){ $binv[$key2] += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }

                            $sql = "SELECT * FROM `main_crdrnote` WHERE  `date` >= '$aa[0]' AND `date` <= '$aa[1]' AND `mode` IN ('CDN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $key3 = $row['ccode']."@".$key; $bcdn[$key3] += (float)$row['amount']; } }

                        }

                        // print_r($binv);
                        // echo "<br/><br/>";
                        // print_r($bcdn);
                        // die();
                         
                        foreach($sales_weeks as $key => $value){
                            $aa = explode('@',$value);

                            $sql = "SELECT * FROM `customer_receipts` WHERE  `date` >= '$aa[0]' AND `date` <= '$aa[1]' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['ccode']."@".$key; $brct[$ky] += (float)$row['amount']; } }

                            $sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$aa[0]' AND `date` <= '$aa[1]' AND `mode` IN ('CCN') AND `active` = '1' ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['ccode']."@".$key; $bccn[$ky] += (float)$row['amount']; } }

                            $sql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$aa[0]' AND `date` <= '$aa[1]' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['vcode']."@".$key; $brtn[$ky] += (float)$row['amount']; } }

                            $sql = "SELECT * FROM `main_mortality` WHERE `date` >= '$aa[0]' AND `date` <= '$aa[1]' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
                            if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ky = $row['ccode']."@".$key; $bmort[$ky] += (float)$row['amount']; } }


                        }
                             
                        

                        $html .= '<tr class="thead2">';
                        $html .= '<th style="text-align:center;">Slno</th>';
                        $html .= '<th style="text-align:center;">Customer</th>';
                        $html .= '<th style="text-align:center;">Upto '.date("d-m-Y", strtotime($fdate)).' Pending Amount</th>';
                        for ($i = 0; $i < count($sales_weeks); $i++) {
                            $sales_range = explode('@', $sales_weeks[$i]);
                            $coll_range = explode('@', $collection_weeks[$i]);
                        
                            $html .= '<th style="text-align:center;">Sales (' . date("d-m-Y", strtotime($sales_range[0])) . ' to ' . date("d-m-Y", strtotime($sales_range[1])) . ')</th>';
                            $html .= '<th style="text-align:center;">Collection (' . date("d-m-Y", strtotime($coll_range[0])) . ' to ' . date("d-m-Y", strtotime($coll_range[1])) . ')</th>';
                        
                            // Optionally track totals
                            $week_totals[$i] = ['sale' => 0, 'coll' => 0];
                        }
                        $html .= '<th style="text-align:center;">Balance</th>';
                        $html .= '</tr>';
                        
                        $slno = 1;
                        $fopentot = 0;
                        foreach($cus_code as $cust){
                            $html .= '<tr>';
                            $html .= '<td style="text-align:center;">'.$slno++.'</td>';
                            $html .= '<td>'.$cus_name[$cust].'</td>';
                            $o_bal = 0;
                            $rbal = 0;
                            $o_bal = (((float)$openinv[$cust] + (float)$opencdn[$cust] + (float)$obdramt[$cust]) - ((float)$openrct[$cust] + (float)$openccn[$cust] + (float)$openrtn[$cust] + (float)$openmort[$cust] + (float)$obcramt[$cust]));
                            $fopentot += $o_bal;
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($o_bal,2)).'</td>';
                            for ($i = 0; $i < count($sales_weeks); $i++) {
                                $ksale = $cust . "@" . $i;
                                $kcoll = $cust . "@" . $i;
                            
                                // --- Sales data ---
                                $sale_amt = (!empty($binv[$ksale])) ? (float)$binv[$ksale] : 0;
                                $cdn_amt  = (!empty($bcdn[$ksale])) ? (float)$bcdn[$ksale] : 0;
                                $bsal_amt = $sale_amt + $cdn_amt;
                            
                                // --- Collection data ---
                                $rct_amt = (!empty($brct[$kcoll])) ? (float)$brct[$kcoll] : 0;
                                $ccn_amt = (!empty($bccn[$kcoll])) ? (float)$bccn[$kcoll] : 0;
                                $rtn_amt = (!empty($brtn[$kcoll])) ? (float)$brtn[$kcoll] : 0;
                                $mrt_amt = (!empty($bmort[$kcoll])) ? (float)$bmort[$kcoll] : 0;
                                $brct_amt = $rct_amt + $ccn_amt + $rtn_amt + $mrt_amt;
                            
                                // Add to totals
                                $week_totals[$i]['sale'] += $bsal_amt;
                                $week_totals[$i]['coll'] += $brct_amt;
                                $rbal += ($bsal_amt - $brct_amt);
                                // Display in HTML
                                $html .= '<td style="text-align:right;">' . number_format_ind(round($bsal_amt, 2)) . '</td>';
                                $html .= '<td style="text-align:right;">' . number_format_ind(round($brct_amt, 2)) . '</td>';
                            }
                            $bftotal += ($rbal + $o_bal);
                            $html .= '<td style="text-align:right;">'.number_format_ind(round($rbal + $o_bal,2)).'</td>';
                            $html .= '</tr>';
                        }
                        $html .= '<tr style="font-weight:bold; background:#f0f0f0;">';
$html .= '<td colspan="2" style="text-align:center;">Total</td>';
$html .= '<td  style="text-align:right;">'.number_format_ind(round($fopentot,2)).'</td>';
foreach ($week_totals as $key => $totals) {
    $total_sale = number_format_ind(round($totals['sale'], 2));
    $total_coll = number_format_ind(round($totals['coll'], 2));
    
    $html .= '<td style="text-align:right;">' . $total_sale . '</td>';
    $html .= '<td style="text-align:right;">' . $total_coll . '</td>';
}
$html .= '<td  style="text-align:right;">'.number_format_ind(round($bftotal,2)).'</td>';
$html .= '</tr>';
                        

                            echo $html;
                        }
                        ?>
					</table>
				</form>
			</div>
		</section>
        <script>
            function checkval() {
                document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var users = document.getElementById("cus_code").value;
                var no_weeks = document.getElementById("no_weeks").value;
                var date =  document.getElementById("fdate").value;
                var exp = document.getElementById("exports").value;
                
                var l = true;
                  if(date == ""){
                        alert("Please select Date");
                        document.getElementById("fdate").focus();
                        l = false;
                    }
                else if(users == "select"){
                    alert("Kindly select Customer");
                    document.getElementById("cus_code").focus();
                    l = false;
                }
                else if(no_weeks == ""){
                    alert("Kindly select Number of Weeks");
                    document.getElementById("no_weeks").focus();
                    l = false;
                }else if(exp == ""){
                     alert("Kindly select Display");
                    document.getElementById("exports").focus();
                    l = false;
                }
                
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
                    document.getElementById("ebtncount").value = "0";
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
            function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
         <script src="../handle_ebtn_as_tbtn.js"></script>
	</body>
	
</html>
