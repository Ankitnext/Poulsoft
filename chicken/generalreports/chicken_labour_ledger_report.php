<?php
//chicken_labour_ledger1.php
$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
	
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../config.php";
    $dbname = $_SESSION['dbase'];
    $users_code = $_SESSION['userid'];

    $form_reload_page = "chicken_labour_ledger_report.php";
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
$file_name = "Labour Ledger Report";

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

$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Customer Details
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunits = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunits[$row['code']] = $row['cunits']; }

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

$sql = "SELECT * FROM `acc_coa` WHERE `driver_flag` ='1' AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $driver_code = $driver_name = array();
while($row = mysqli_fetch_assoc($query)){ $driver_code[$row['code']] = $row['code']; $driver_name[$row['code']] = $row['description']; }


$fdate = $tdate = date("Y-m-d"); $sectors = "all"; $fstyles = $fsizes = "default"; $exports = "display";
if(isset($_POST['submit']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $sectors = $_POST['sectors'];
    $fstyles = $_POST['fstyles'];
    $fsizes = $_POST['fsizes'];
    $exports = $_POST['exports'];


}
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
                                        <h6>STATEMENT FROM DATE <?php echo date("d.m.Y",strtotime($fdate)); ?> - TO DATE <?php echo date("d.m.Y",strtotime($tdate)); ?></h6>
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
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label for="sectors">Labour</label>
                                            <select name="sectors" id="sectors" class="form-control select2" style="width:180px;">
                                                <!-- <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option> -->
											    <?php foreach($driver_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($sectors == $scode){ echo "selected"; } ?>><?php echo $driver_name[$scode]; ?></option><?php } ?>
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
                          //  $html = '';

                            $html = $nhtml = $fhtml = '';

                            // $html .= '<thead class="thead2" id="head_names">';
                            // $nhtml .= '<th style="text-align:center;">Sl.No.</th>'; $fhtml .= '<th style="text-align:center; id="order_num">Sl.No.</th>';
                            // $nhtml .= '<th style="text-align:center;">Date</th>'; $fhtml .= '<th style="text-align:center;" id="order_date">Date</th>';
                            // $nhtml .= '<th style="text-align:center;" >Transation No.</th>'; $fhtml .= '<th style="text-align:center;" id="order">Transation No.</th>';
                            // $nhtml .= '<th style="text-align:center;" >Customer</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Customer</th>';
                            // $nhtml .= '<th style="text-align:center;" >Items</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Items</th>';
                            // $nhtml .= '<th style="text-align:center;" >Jals</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Jals</th>';
                            // $nhtml .= '<th style="text-align:center;" >Birds</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Birds</th>';
                            // $nhtml .= '<th style="text-align:center;" >Quantity</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Quantity</th>';
                            // $nhtml .= '<th style="text-align:center;" >Price</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Price</th>';
                            // $nhtml .= '<th style="text-align:center;" >Amount</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">Amount</th>';
                            // $nhtml .= '<th style="text-align:center;" >Warehouse</th>'; $fhtml .= '<th style="text-align:center;" id="order">Warehouse</th>';
                            // $nhtml .= '<th style="text-align:center;" >Remarks</th>'; $fhtml .= '<th style="text-align:center;" id="order">Remarks</th>';
                            // $nhtml .= '</tr>';
                            // $fhtml .= '</tr>';
                            // $html .= $fhtml;
                            // $html .= '</thead>';

                            //Sales
                            $html .= '<thead class="thead2" id="head_names">';
                            $nhtml .= '<th style="text-align:center;">S.No.</th>';  $fhtml .= '<th style="text-align:center; id="order_num">Sl.No.</th>';
                            $nhtml .= '<th style="text-align:center;">DATE.</th>';  $fhtml .= '<th style="text-align:center;" id="order_date">DATE</th>';
                            $nhtml .= '<th style="text-align:center;">WAREHOUSE</th>'; $fhtml .= '<th style="text-align:center;" id="order">WAREHOUSE</th>';
                            $nhtml .= '<th style="text-align:center;">KG</th>';   $fhtml .= '<th style="text-align:center;" id="order_num">KG</th>';
                            $nhtml .= '<th style="text-align:center;">AMOUNT</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">AMOUNT</th>';
                            $nhtml .= '<th style="text-align:center;">SUPERVISOR</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">SUPERVISOR</th>';
                            $nhtml .= '<th style="text-align:center;">ADVANCE</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">ADVANCE</th>';
                            $nhtml .= '<th style="text-align:center;">PAYMENT</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">PAYMENT</th>';

                            $nhtml .= '<th style="text-align:center;">DEBIT</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">DEBIT</th>';
                            $nhtml .= '<th style="text-align:center;">CREDIT</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">CREDIT</th>';

                            $nhtml .= '<th style="text-align:center;">RUNNING BALANCE</th>'; $fhtml .= '<th style="text-align:center;" id="order_num">RUNNING BALANCE</th>';
                            $nhtml .= '</tr>';
                            $fhtml .= '</tr>';
                            $html .= $fhtml;
                            $html .= '</thead>';
                           // $html .= '<tbody >';

            
                            $sec_fltr = ""; if($sectors != "all"){ $sec_fltr = " AND `tcoa` = '$sectors'"; } 
                            $sec_fltr1 = ""; if($sectors != "all"){ $sec_fltr1 = " AND `from_coa` = '$sectors'"; }
             

                           $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Labour Advance' AND `vouexp_flag` = '1' AND `active` = '1' ORDER BY `description` ASC";
                            $query = mysqli_query($conn,$sql); $labour_fcoa = "";
                            while($row = mysqli_fetch_assoc($query)){ $labour_fcoa = $row['code']; }

                            $sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Employee Salaries & Wages' AND `active` = '1'";
                            $query = mysqli_query($conn, $sql); $ewage_code = "";
                            while($row = mysqli_fetch_assoc($query)){ $ewage_code = $row['code']; }

                            $sql = "SELECT * FROM `acc_vouchers` WHERE `date` < '$fdate' AND `fcoa` = '$labour_fcoa' AND `active` = '1'".$sec_fltr."";
                            $query = mysqli_query($conn,$sql); $op_adv = 0;
                            while($row = mysqli_fetch_assoc($query)){
                               // $key = $row['date'];
                                $op_adv += (float)$row['advance_amt'] + (float)$row['sup_eamt'];
                            }

                            $sql = "SELECT * FROM `employee_sale_wages` WHERE `date` < '$fdate' AND `to_coa` = '$ewage_code' AND `active` = '1'".$sec_fltr1." ";
                            $query = mysqli_query($conn,$sql); $opamount = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $opamount += (float)$row['amount'];
                            }


                            // $sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` = '$labour_fcoa' AND `active` = '1' ".$sec_fltr.""; echo $sql;
                            // $query = mysqli_query($conn,$sql); $advn = array();
                            // while($row = mysqli_fetch_assoc($query)){
                            //     $key = $row['date'];
                            //     $advn[$key] += (float)$row['advance_amt'];
                            //     $sup_amt[$key] += (float)$row['sup_eamt'];

                            // }
                            // $opening_bal = $opamount - $op_adv;
                            
                            // $sql = "SELECT * FROM `employee_sale_wages` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_coa` = '$ewage_code' AND `active` = '1' AND `dflag` = '0'".$sec_fltr1." ";
                            // $query = mysqli_query($conn,$sql); $slno = 0;
                            // while($row = mysqli_fetch_assoc($query)){
                            //       $key = $row['date'];
                            //       $labour = $row['from_coa'];
                            //       $esw_sale_qty = (float)$row['sale_qty'];
                            //       $esw_rate = (float)$row['rate'];
                            //       $esw_amount = (float)$row['amount'];
                            //       $opadv = $op_adv[$key];
                            //        $slno++;
                            //       if($slno == 1){
                            //            $o_bal = $opening_bal + $esw_amount - $advn[$key];
                            //             $html .= '<tr>';
                            //             $html .= '<td colspan="6"><b>Opening Balance</b></td>';
                            //             $html .= '<td style="text-align:right">'.$opening_bal.'</td>'; 
                            //             $html .= '</tr>';
                            //       }else{
                            //            $o_bal += $esw_amount - $advn[$key];
                            //       }            
                            //         $html .= '<tr>';
                            //         $html .= '<td style="text-align:center">'.$slno.'</td>';
                            //         $html .= '<td style="text-align:right">'.$key.'</td>';
                            //         $html .= '<td style="text-align:right">'.$sector_name[$labour].'</td>';
                            //         $html .= '<td style="text-align:right">'.$esw_sale_qty.'</td>';
                            //         $html .= '<td style="text-align:right">'.$esw_amount.'</td>';
                            //         $html .= '<td style="text-align:right">'.$sup_amt[$key].'</td>';
                            //         $html .= '<td style="text-align:right">'.$advn[$key].'</td>';
                            //         $html .= '<td style="text-align:right">'.$o_bal.'</td>';
                            //         $html .= '</tr>';
                            // }

                        //labour payment

                        $sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$sectors'";
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['date'];
                            if($row['mode'] == 'LCN'){
                                $lcn[$key] += (float)$row['amount'];
                            }else if($row['mode'] == 'LDN'){
                                $ldn[$key] += (float)$row['amount'];
                            }
                        }

                    //    print_r($lcn);
                    //    print_r($ldn);

                        $sql = "SELECT * FROM `pur_payments` WHERE `ccode` LIKE '$sectors' AND `trlink` LIKE 'chicken_display_loabourpayment2.php' AND `active` = '1'";
                        $query = mysqli_query($conn,$sql); $old_date = ""; $i = 0;
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_date == "" || $old_date != $row['date']){
                                $i = 0; $old_date = $row['date'];
                            }
                            $i++;
                            $key = $row['date']."@".$i;
                            $labourpay[$key] = $row['amount'];
                            $lpdate[$row['date']] = $i;
                        }
                            
                       
                        //wages array
                        $sql = "SELECT * FROM `employee_sale_wages` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `to_coa` = '$ewage_code' AND `active` = '1' AND `dflag` = '0'".$sec_fltr1." ";
                        $query = mysqli_query($conn,$sql); $slno = 0; $i = 0; $old_date = "";
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_date == "" || $old_date != $row['date']){
                                $i = 0; $old_date = $row['date'];
                            }
                            $i++;
                            $key = $row['date']."@".$i;
                            $wdt_cnt[$row['date']] = $i;
                            $wg_sect[$key] = $row['from_coa'];
                            $wg_rate[$key] = $row['rate'];
                            $wg_amt[$key] = $row['kgs'];
                            $wage_sale_qty[$key] = (float)$row['amount'];
                            $link_trnum[$row['date']] = $row['link_trnum'];
                        }
                       // print_r($link_trnum);

                        $sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` = '$labour_fcoa' AND `active` = '1' ".$sec_fltr."";  //echo $sql;
                        $query = mysqli_query($conn,$sql); $advn = array(); $i = 0; $old_date = "";
                        while($row = mysqli_fetch_assoc($query)){
                            if($old_date == "" || $old_date != $row['date']){
                                $i = 0; $old_date = $row['date'];
                            }
                            $i++;
                            $key = $row['date']."@".$i;
                            $vocdt_cnt[$row['date']] = $i;
                            $advn[$key] += (float)$row['advance_amt'];
                            $sup_amtt[$key] += (float)$row['sup_eamt'];
                            $warehouse[$row['date']] = $row['warehouse'];
                        }
 //print_r($sup_amtt);
                            $slno = 1;
                            $opening_bal = $opamount - $op_adv;
                            $ftotalkg = $famttot = $samt = $advtot = 0;
                            for($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate = strtotime("+1 day", $cdate) ){
                                $date = date("Y-m-d", $cdate);
                                $max_count = max($vocdt_cnt[$date],$wdt_cnt[$date]);
                                
                                $sale_qty = $esw_amount = $sup_amt = $advamt = 0;
                                for($i=1;$i<=$max_count;$i++){
                                    $key = $date."@".$i;
                                  //  echo "<br/>".$key."@".$sup_amtt[$key];
                                  //  $labour = $wg_sect[$key];
                                    $sale_qty += $wage_sale_qty[$key];
                                    $esw_amount += $wg_amt[$key];
                                    $sup_amt += $sup_amtt[$key];
                                    $advamt += $advn[$key];
                                  // echo $sup_amt;
                                }

                                $lpdc = $lpdate[$date];
                                $lpayment = 0;
                                for($i=1;$i<=$lpdc;$i++){
                                    $key = $date."@".$i;
                                    $lpayment += $labourpay[$key];
                                }
                                $totlabopay += $lpayment;
                                if($warehouse[$date] == ""){
                                           $link_trnum = $link_trnum[$date];
                                    $sql = "SELECT * FROM `customer_sales` WHERE `active` = '1' AND `cmn_trnum` = '$link_trnum'";
                                    $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){
                                        $labour = $row['warehouse'];
                                    }
                                }else{
                                    $labour = $warehouse[$date];
                                }
                                
                                if($slno == 1){
                                    $o_bal = $opening_bal + $sale_qty - $advamt;
                                   // $html .= '<tr>';
                                   $html .= '<thead>';
                                    $html .= '<tr class="opening-balance">';
                                    $html .= '<td colspan="6"><b>Opening Balance</b></td>';
                                    $html .= '<td style="text-align:right">'.$opening_bal.'</td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';
                                    $html .= '<tbody class="tbody1">';
                                }else{
                                    $o_bal += $sale_qty - $advamt - $lpayment;
                                }       
                            //    if($esw_amount == 0 && $advamt == 0 && $lpayment == 0 && $ldn[$date] == 0 && $lcn[$date] == 0 && $sup_amt == 0){ 
                            //    } else{    
                                $ftotalkg += $sale_qty;
                                $famttot += $esw_amount;
                                $samt += $sup_amt;
                                $advtot += $advamt;
                                $html .= '<tr>';
                                $html .= '<td style="text-align:center">'.$slno.'</td>';
                                $html .= '<td style="text-align:right">'.date("d.m.Y", strtotime($date)).'</td>';
                                $html .= '<td style="text-align:right">'.$sector_name[$labour].'</td>';
                                $html .= '<td style="text-align:right">'.$esw_amount.'</td>';
                                $html .= '<td style="text-align:right">'.$sale_qty.'</td>';
                                $html .= '<td style="text-align:right">'.$sup_amt.'</td>';
                                $html .= '<td style="text-align:right">'.$advamt.'</td>';
                                $html .= '<td style="text-align:right">'.$lpayment.'</td>';
                                $html .= '<td style="text-align:right">'.$ldn[$date].'</td>';
                                $html .= '<td style="text-align:right">'.$lcn[$date].'</td>';
                                $html .= '<td style="text-align:right">'.$o_bal.'</td>';
                                $html .= '</tr>';
                                
                          //  }
                            $slno++;
                           }
                           $html .= '</tbody>';
                          
                           // Add totals row $totlabopay $lcn[$key]
                           $html .= '<thead class="tfoot1">';
                           $html .= '<tr>';
                                $html .= '<td colspan="3" style="text-align:center"><b>Total</b></td>';
                                $html .= '<td style="text-align:right"><b>'.$famttot.'</b></td>';
                                $html .= '<td style="text-align:right"><b>'.$ftotalkg.'</b></td>';
                                $html .= '<td style="text-align:right"><b>'.$samt.'</b></td>';
                                $html .= '<td style="text-align:right"><b>'.$advtot.'</b></td>';
                                $html .= '<td style="text-align:right"><b>'.$totlabopay.'</b></td>';
                                $html .= '<td style="text-align:right"></td>';
                                $html .= '<td style="text-align:right"></td>';
                                $html .= '<td style="text-align:right"></td>';
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
         <script src="sort_table_columns.js"></script>
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
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
        </script>
		<?php if($exports == "display" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
