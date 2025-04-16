<?php
//broiler_fetch_dp_goodsreceipt_details.php
session_start(); include "newConfig.php";
$vcode = $_GET['vcode'];
$grtrnum = $_GET['grtrnum'];

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
    
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; $sectc[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }

$html = "";

$sql = "SELECT * FROM `broiler_pc_goodsreceipt` WHERE  `trnum` IN ('$grtrnum') AND  `active` = '1' AND `dflag` = '0' AND `pinv_flag` = '0' ORDER BY `trnum`,`id` ASC";
$query = mysqli_query($conn,$sql); $c = mysqli_num_rows($query); $c = $c - 1; $i = 0;
while($row = mysqli_fetch_assoc($query)){
    $icode = $row['item_code'];
    $rcvd_qty = round($row['rcvd_qty'],5);
    $rate = round($row['rate'],5);
    $dis_per = round($row['disc_per'],5);
    $dis_amt = round($row['disc_amt'],5);
    $gsts = $row['gst_code']; $gst_cval = $gsts."@".$gst_value[$gsts];
    $item_amt = round($row['item_amt'],5); if($row['item_amt'] == "" || empty($row['item_amt'])){ $item_amt =  $row['item_amt'];}
    $warehouse = $row['warehouse'];
    $po_value = $id."@".$trnum;
    
    $html .= '<tr id="row_no['.$i.']" class="num_field">';
    $html .= '<td style="width:160px;"><select name="icode[]" id="icode['.$i.']" class="form-control select2" style="width:160px;padding:0;padding-right:2px;text-align:right;"><option value="'.$icode.'">'.$item_name[$icode].'</option></select></td>';
    $html .= '<td style="width:85px;"><input type="text" name="snt_qty[]" id="snt_qty['.$i.']" class="form-control text-right" value="'.$rcvd_qty.'" style="width:80px;padding:0;padding-right:2px;text-align:right;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" /></td>';
    $html .= '<td style="width:85px;"><input type="text" name="rcd_qty[]" id="rcd_qty['.$i.']" class="form-control text-right" value="'.$rcvd_qty.'" style="width:80px;padding:0;padding-right:2px;text-align:right;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" /></td>';
    $html .= '<td style="width:85px;"><input type="text" name="fre_qty[]" id="fre_qty['.$i.']" class="form-control text-right" style="width:80px;padding:0;padding-right:2px;text-align:right;"  /></td>';
    $html .= '<td style="width:85px;"><input type="text" name="short_qty[]" id="short_qty['.$i.']" class="form-control text-right" style="width:80px;padding:0;padding-right:2px;text-align:right;" readonly /></td>';
    $html .= '<td style="width:85px;"><input type="text" name="rate[]" id="rate['.$i.']" class="form-control text-right" value="'.$rate.'" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" style="width:80px;padding:0;padding-right:2px;text-align:right;" /></td>';
    $html .= '<td style="width:85px;"><input type="checkbox" name="debit_flag[]" id="debit_flag['.$i.']" style="text-align:right;" /></td>';
    $html .= '<td style="width:85px;"><input type="text" name="short_amt[]" id="short_amt['.$i.']" class="form-control text-right" value="" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" style="width:80px;padding:0;padding-right:2px;text-align:right;" readonly /></td>';
    $html .= '<td style="width:85px;"><input type="text" name="dis_per[]" id="dis_per['.$i.']" class="form-control text-right" value="'.$dis_per.'" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);calculate_total_amt(this.id);" style="width:80px;padding:0;padding-right:2px;text-align:right;" /></td>';
    $html .= '<td style="width:85px;"><input type="text" name="dis_amt[]" id="dis_amt['.$i.']" class="form-control text-right" value="'.$dis_amt.'" style="width:80px;padding:0;padding-right:2px;text-align:right;" /></td>';
   // $html .= '<td style="width:130px;"><select name="gst_per[]" id="gst_per['.$i.']" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;"><option value="'.$gst_cval.'" selected>'.$gst_name[$gsts].'</option></select></td>';
   // $html .= '<td style="width:130px;"><select name="gst_per[]" id="gst_per['.$i.']" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;"><option value="select">select</option>'; foreach($gst_code as $gsts) { $gst_cval = $gsts."@".$gst_value[$gsts]; $html .= '<option value="'.$gst_cval.'">'.$gst_name[$gsts].'</option>'; } $html .= '</select></td>';
    $html .= '<td style="width:130px;">
    <select name="gst_per[]" id="gst_per['.$i.']" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;">';
    foreach ($gst_code as $gsts1) {
        $gst_cval = $gsts."@".$gst_value[$gsts1];
                if ($gsts1 == $gsts) {
            $html .= '<option value="'.$gst_cval.'" selected>'.$gst_name[$gsts1].'</option>';
        } else {
            $html .= '<option value="'.$gst_cval.'">'.$gst_name[$gsts1].'</option>';
        }
    }
    $html .= '</select></td>';

    $html .= '<td style="width:85px;"><input type="text" name="item_tamt[]" id="item_tamt['.$i.']" class="form-control text-right" value="'.$item_amt.'" style="width:80px;padding:0;padding-right:2px;text-align:right;" readonly /></td>';
   // $html .= '<td style="width:160px;"><select name="warehouse[]" id="warehouse['.$i.']" class="form-control select2" style="width:160px;"><option value="'.$warehouse.'">'.$sector_name[$warehouse].'</option></select></td>';

   $html .= '<td style="width:160px;"><select name="warehouse[]" id="warehouse['.$i.']" class="form-control select2" style="width:160px;">';
    foreach ($sectc as $id) {
        $selected = ($id == $warehouse) ? 'selected' : '';
        $html .= '<option value="'.$id.'" '.$selected.'>'.$sector_name[$id].'</option>';
    }
    $html .= '</select></td>';

    $html .= '</tr>';
    
    $i++;
}
$i = $i - 1;
echo $html."@$&".$i;
?>