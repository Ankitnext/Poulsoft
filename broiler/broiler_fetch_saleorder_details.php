<?php
//broiler_fetch_saleorder_details.php
session_start(); include "newConfig.php";
$date = date("Y-m-d",strtotime($_GET['date']));

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_name = array();
while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0' AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Edit Values Fetch
$trnum = $_GET['trnum']; $rp_trnum = $so_alist = $sorder_nos = array();
if($trnum != ""){
    $sql = "SELECT * FROM `broiler_routeplan` WHERE `trnum` = '$trnum' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['so_date']."@".$row['so_trnum']."@".$row['item_code'];
        $rp_trnum[$key] = $row['trnum'];
        $so_alist[$row['so_trnum']] = $row['so_trnum'];
        $sorder_nos[$key] = $row['sorder_no'];
    }
}

$so_info = '';
$so_info .= '<table>';
$so_info .= '<tr>';
$so_info .= '<th style="width:50px;text-align:center;">select<br/><input type="checkbox" name="check_all" id="check_all" onchange="check_all_access();" /></th>';
//$so_info .= '<th style="width:120px;text-align:center;">Order Date</th>';
$so_info .= '<th style="width:120px;text-align:center;">Order No.</th>';
$so_info .= '<th style="width:290px;text-align:center;">Customer</th>';
$so_info .= '<th style="width:250px;text-align:center;">Item</th>';
$so_info .= '<th style="width:90px;text-align:center;">Box/Crates</th>';
$so_info .= '<th style="width:90px;text-align:center;">Order Qty</th>';
$so_info .= '<th style="width:110px;text-align:center;">Delivery Date</th>';
$so_info .= '<th style="width:110px;text-align:center;">Supply Order</th>';
$so_info .= '<th style="width:110px;text-align:center;">Sector</th>';
$so_info .= '</tr>';

if(sizeof($so_alist) > 0){ $so_list = implode("','",$so_alist); $so_fltr = " AND (`rp_flag` = '0' OR `trnum` IN ('$so_list'))"; }
else{ $so_fltr = " AND `rp_flag` = '0'"; }

$sql = "SELECT * FROM `broiler_sc_saleorder` WHERE `date` = '$date'".$so_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `trnum`,`id` ASC";
$query = mysqli_query($conn,$sql); $acount = mysqli_num_rows($query); $slno = 0;
if($acount > 0){
    while($row = mysqli_fetch_assoc($query)){
        $key = $row['date']."@".$row['trnum']."@".$row['item_code'];
        $chk_flag = ""; if(empty($rp_trnum[$key]) || $rp_trnum[$key] == ""){ } else{ $chk_flag = "checked";}
        $so_date = $row['date'];
        $so_trnum = $row['trnum'];
        $vcode = $row['vcode'];
        $item_code = $row['item_code'];
        $warehouse = $row['warehouse'];
        $boxes = round($row['box_crate_qty'],5);
        $order_qty = round(((float)$row['rcvd_qty'] + (float)$row['free_qty']),5);
        $delivery_date = date("d.m.Y",strtotime($row['delivery_date']));
        $sorder_no = $sorder_nos[$key];

        $so_info .= '<tr>';
        $so_info .= '<td style="width:50px;text-align:center;"><input type="checkbox" name="slno[]" id="slno['.$slno.']" value="'.$slno.'" onchange="calculate_totals();" '.$chk_flag.' /></td>';
        //$so_info .= '<td style="width:120px;"><input type="text" name="so_date[]" id="so_date['.$slno.']" class="form-control" value="'.$so_date.'" readonly /></td>';
        $so_info .= '<td style="width:120px;"><input type="text" name="so_trnum[]" id="so_trnum['.$slno.']" class="form-control" value="'.$so_trnum.'" readonly /></td>';
        $so_info .= '<td style="width:280px;"><select name="vcode[]" id="vcode['.$slno.']" class="form-control select2"><option value="'.$vcode.'">'.$cus_name[$vcode].'</option></select></td>';
        $so_info .= '<td style="width:240px;"><select name="item_code[]" id="item_code['.$slno.']" class="form-control select2"><option value="'.$item_code.'">'.$item_name[$item_code].'</option></select></td>';
        $so_info .= '<td style="width:120px;"><input type="text" name="boxes[]" id="boxes['.$slno.']" class="form-control text-right" value="'.$boxes.'" readonly /></td>';
        $so_info .= '<td style="width:120px;"><input type="text" name="order_qty[]" id="order_qty['.$slno.']" class="form-control text-right" value="'.$order_qty.'" readonly /></td>';
        $so_info .= '<td style="width:120px;"><input type="text" name="delivery_date[]" id="delivery_date['.$slno.']" class="form-control" value="'.$delivery_date.'" readonly /></td>';
        $so_info .= '<td style="width:120px;"><input type="text" name="sorder_no[]" id="sorder_no['.$slno.']" class="form-control" value="'.$sorder_no.'" onkeyup="validatename(this.id);" /></td>';
       // $so_info .= '<td style="width:120px;"><input type="text" name="warehouse[]" id="warehouse['.$slno.']" class="form-control" value="'.$warehouse.'" readonly/></td>';
        $so_info .= '<td style="width:240px;"><select name="warehouse[]" id="warehouse['.$slno.']" class="form-control select2"><option value="'.$warehouse.'">'.$sector_name[$warehouse].'</option></select></td>';
        $so_info .= '</tr>';
        $slno++;
    }
    $so_info .= '<tr>';
    $so_info .= '<th style="text-align:right;" colspan="4">Total</th>';
    $so_info .= '<th style="width:120px;"><input type="text" name="tot_boxes" id="tot_boxes" class="form-control text-right" readonly /></th>';
    $so_info .= '<th style="width:120px;"><input type="text" name="tot_oqty" id="tot_oqty" class="form-control text-right" readonly /></th>';
    $so_info .= '<th style="width:120px;"></th>';
    $so_info .= '<th style="width:120px;"></th>';
    $so_info .= '</tr>';
    $slno = $slno - 1;
}
$so_info .= '</table>';
echo $so_info."[@$%&]".$slno;