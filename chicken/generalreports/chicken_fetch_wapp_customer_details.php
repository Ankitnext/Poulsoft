<?php
//chicken_fetch_wapp_customer_details.php
session_start(); include "../newConfig.php";
$today = date("Y-m-d");
$fdate = date("Y-m-d",strtotime($_GET['fdate']));
$tdate = date("Y-m-d",strtotime($_GET['tdate']));
$groups = $_GET['groups'];
$tsale_flag = $_GET['tsale_flag'];

$sector_param = $_GET['sectors'] ?? 'all';
$sectors_array = explode(',', $sector_param);

// Now apply your existing filter logic
$sects = [];
$sec_all_flag = 0;
foreach ($sectors_array as $scts) {
    $sects[$scts] = $scts;
    if ($scts === "all") {
        $sec_all_flag = 1;
    }
}

$sects_list = implode("','", array_map('addslashes', $sects));
$secct_fltr = ($sec_all_flag === 1) ? "" : "AND `warehouse` IN ('$sects_list')";

if($groups == "all"){ $grp_filter = ""; } else{ $grp_filter = " AND `groupcode` = '$groups'"; }

$cus_filter = ""; $cus_name = $cus_code = $cus_gcode = $cus_mobile = array();
if((int)$tsale_flag == 1){
	$sql = "SELECT * FROM `customer_sales` WHERE `date` = '$today' AND `active` = '1'".$secct_fltr." AND `tdflag` = '0' AND `pdflag` = '0' GROUP BY `customercode` ORDER BY `customercode` ASC";
	$query = mysqli_query($conn,$sql); $cus_alist = array();
	while($row = mysqli_fetch_assoc($query)){ $cus_alist[$row['customercode']] = $row['customercode']; }

	if(sizeof($cus_alist) > 0){
		$cus_list = implode("','",$cus_alist);
		$cus_filter = " AND `code` IN ('$cus_list')";
	}
}
if($tsale_flag == 0 || $tsale_flag == 1 && $cus_filter != ""){
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cus_filter."".$grp_filter." AND `active` = '1' ORDER BY `name` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		$cus_code[$row['code']] = $row['code'];
		$cus_name[$row['code']] = $row['name'];
		//$cus_gcode[$row['code']] = $row['groupcode'];
		$cus_mobile[$row['code']] = $row['mobileno'];
	}
}

$csize = sizeof($cus_code);
$climit = 25; $d = 1;
if($csize > $climit){ $crowsd = $csize / $climit; $crows = (int)$crowsd; $cmod = $csize % $climit; if($cmod > 0){ $crows = $crows + 1; } }

$chkbox_opt = $html = '';
$chkbox_opt .= '<option value="select">-select-</option>';
if ($csize <= 75) { $chkbox_opt .= '<option value="'.$d."-".$csize.'">-All-</option>'; }

$ini_val = $fnl_val = 0;
for ($i = 1; $i <= $crows; $i++) {
	if($ini_val == 0){ $ini_val = 1; $fnl_val = $fnl_val + $climit; }
	else if($crows != $i){ $ini_val = $fnl_val + 1; $fnl_val = $fnl_val + $climit; }
	else{ $ini_val = $fnl_val + 1; if($cmod == 0){ $fnl_val = $fnl_val + $climit; } else{ $fnl_val = $fnl_val + $cmod; } }
	$sel_val = $ini_val . "-" . $fnl_val;
	$chkbox_opt .= '<option value="'.$sel_val.'">'.$sel_val.'</option>';
}

$html .= '<tr style="padding:0;padding:5px;">';
$html .= '<td colspan="4" style="padding:0;padding:5px;">';
$html .= '<div class="form-group" style="width:130px;">';
$html .= '<label for="cus_select">Select</label>';
$html .= '<select name="cus_select" id="cus_select" class="form-control select2" onchange="checkedall()">';
$html .= $chkbox_opt;
$html .= '</select>';
$html .= '</td>';
$html .= '</tr>';

$c = 0;
foreach($cus_code as $ccode){
	$c = $c + 1;
	$html .= '<tr id="tblrow['.$c.']" style="padding:0;padding:5px;">';
	$html .= '<td style="padding:0;padding:5px;text-align:center;">'.$c.'</td>';
	$html .= '<td style="padding:0;padding:5px;text-align:center;"><input type="checkbox" name="c_det[]" id="c_det['.$c.']" value="'.$ccode.'" style="height:18px;" /></td>';
	$html .= '<td style="padding:0;padding:5px;width: auto;text-align:left;">'.$cus_name[$ccode].'</td>';
	$html .= '<td style="padding:0;padding:5px;text-align:left;"><input type="text" name="cmob[]" id="cmob['.$c.']" class="form-control" style="height:18px;background:inherit;border:none;" value="'.$cus_mobile[$ccode].'" readonly /></td>';
	$html .= '</tr>';
}

$html .= '<tr>';
$html .= '<th colspan="4" style="padding:10px;text-align:center;"><button type="submit" name="sendsms" id="sendsms" class="btn btn-success btn-md" value="sendsuccess">Send Ledger</button></th>';
$html .= '</tr>';

echo $html."[@$%&]".$c;