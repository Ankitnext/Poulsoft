<?php
//broiler_fetch_feedmill_items2.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase']; $addedemp = $_SESSION['userid'];

$feed_mill = $_GET['feed_mill'];
$feed_code = $_GET['feed_code'];
$formula_code = $_GET['formula_code'];
$total_tons = $_GET['total_tons'];
$trnum = $_GET['trnum']; if($trnum != ""){ $trnum_filter = " AND `trnum` NOT IN ('$trnum')"; } else{ $trnum_filter = ""; }
$date = date("Y-m-d",strtotime($_GET['date']));

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `broiler_feed_formula` WHERE `code` IN (SELECT code FROM `broiler_feed_formula` WHERE `id` IN (SELECT MAX(id) as id FROM `broiler_feed_formula` WHERE `code` = '$formula_code' AND `mill_code` = '$feed_mill' AND `formula_item_code` = '$feed_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC))";
$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
if($count > 0){

}
else{
    $sql = "SELECT * FROM `broiler_feed_formula` WHERE `code` IN (SELECT code FROM `broiler_feed_formula` WHERE `id` IN (SELECT MAX(id) as id FROM `broiler_feed_formula` WHERE `code` = '$formula_code' AND `mill_code` = 'all' AND `formula_item_code` = '$feed_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC))";
    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
}
if($count > 0){
    $item_list = ""; $c = 0; $formula_items = $item_unit = $item_quantity = array();
    while($row = mysqli_fetch_assoc($query)){
        if($item_list == ""){ $item_list = $row['item_code']; } else{ $item_list = $item_list."','".$row['item_code']; }
        $c++;
        $formula_items[$row['item_code']] = $row['item_code'];
        $item_unit[$row['item_code']] = $row['unit_code'];
        $item_quantity[$row['item_code']] = (float)$total_tons * (float)$row['item_qty'];
    }
    $item_list_size = sizeof($formula_items);
    if(sizeof($formula_items) > 0){
        $total_rcvd_items = $total_free_items = $total_amnt_items = $current_stock = $current_price = $current_amount = array();

        $icats = ""; foreach($formula_items as $fitems){ if(!empty($item_category[$fitems])){ if($icats == ""){ $icats = $item_category[$fitems]; } else{ $icats = $icats."','".$item_category[$fitems]; } } }
        $sql = "SELECT * FROM `item_category` WHERE `code` IN ('$icats')"; $query = mysqli_query($conn,$sql); $icat_iac = "";
        while($row = mysqli_fetch_assoc($query)){ if($icat_iac == ""){ $icat_iac = $row['iac']; } else{ $icat_iac = $icat_iac."','".$row['iac']; } }

        $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$icat_iac') AND `date` <= '$date' AND `item_code` IN ('$item_list') AND `location` IN ('$feed_mill')".$trnum_filter." AND `active` = 1 AND `dflag` = 0 ORDER BY `date` ASC,`crdr` DESC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            if($row['crdr'] == "CR"){
                $current_stock[$row['item_code']] = $current_stock[$row['item_code']] - $row['quantity'];
                $current_amount[$row['item_code']] = $current_amount[$row['item_code']] - ($current_price[$row['item_code']] * $row['quantity']);
            }
            else if($row['crdr'] == "DR"){
                $current_stock[$row['item_code']] = $current_stock[$row['item_code']] + $row['quantity'];
                $current_amount[$row['item_code']] = $current_amount[$row['item_code']] + $row['amount'];
                if($current_stock[$row['item_code']] != 0){
                    $current_price[$row['item_code']] = round(($current_amount[$row['item_code']] / $current_stock[$row['item_code']]),2);
                }
                else{
                    $current_price[$row['item_code']] = 0;
                }
                
            }
            else{ }
        }
    }
    $row_values = ""; $total_item_amt = $final_total_amt = $final_total_qty = $c = 0;
    foreach($formula_items as $icds){
        $c++;
        $total_item_qty = $current_stock[$icds];
        $total_item_amt = $item_quantity[$icds] * $current_price[$icds];
        $final_total_qty = $final_total_qty + $item_quantity[$icds];
        $final_total_amt = round(($final_total_amt + $total_item_amt),2);
        if((float)$final_total_qty != 0){ $final_total_price = round(($final_total_amt / $final_total_qty),2); } else{ $final_total_price = 0; }
        $row_values .= "<tr id='row_no[".$c."]' style='padding:1px;'><td colspan='1'></td><td colspan='1' style=''>";
        $row_values .= "<select name='itm_names[]' id='itm_names[".$c."]' class='form-control itm_select2' style='padding:0;width:180px;' onchange='fetch_item_avg_price(this.id)'><option value='select'>select</option>";
        foreach($item_code as $itsc){
            if($itsc == $icds){
                $row_values .= "<option value='".$itsc."' selected >".$item_name[$itsc]."</option>";
            }
            else{
                $row_values .= "<option value='".$itsc."'>".$item_name[$itsc]."</option>";
            }
            
        }
        $row_values .= "</select></td>";
        $row_values .= "<td style='width:90px;'><input type='text' name='itm_qtys[]' id='itm_qtys[".$c."]' class='form-control' value='".round($item_quantity[$icds],3)."' style='padding-right:10px;text-align:right;' onkeyup='calculate_final_feed_amount()' /></td>";
        
        $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='itm_prc[]' id='itm_prc[".$c."]' class='form-control' value='".$current_price[$icds]."' style='padding-right:10px;text-align:right;' readonly /></td>";
        $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='itm_amt[]' id='itm_amt[".$c."]' class='form-control' value='".$total_item_amt."' style='padding-right:10px;text-align:right;' readonly /></td>";
        if($item_quantity[$icds] > $total_item_qty){
            $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='available_stock[]' id='available_stock[".$c."]' class='form-control' value='".$total_item_qty."' style='padding-right:10px;text-align:right;color:red;' readonly /></td>";
        }
        else{
            $row_values .= "<td colspan='1' style='width:90px;visibility:visible;'><input type='text' name='available_stock[]' id='available_stock[".$c."]' class='form-control' value='".$total_item_qty."' style='padding-right:10px;text-align:right;' readonly /></td>";
        }
        
        
        if($item_list_size == 1 && $c == 1){
            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 2px;'><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;</div>";
        }
        else if($c == 1){
            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 2px;visibility:hidden;'><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;</div>";
        }
        else if($item_list_size == $c){
            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 2px;'><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;<a href='javascript:void(0);' id='deductrow[".$c."]' onclick='destroy_row(this.id)'><i class='fa fa-minus' style='color:red;'></i></a></div>";
        }
        else{
            $row_values .= "<td style='width:60px;padding-top: 2px;'><div class='form-group' id='action[".$c."]' style='padding-top: 2px;visibility:hidden;'><a href='javascript:void(0);' id='addrow[".$c."]' onclick='create_row(this.id)'><i class='fa fa-plus'></i></a>&ensp;<a href='javascript:void(0);' id='deductrow[".$c."]' onclick='destroy_row(this.id)'><i class='fa fa-minus' style='color:red;'></i></a></div>";
        }
        $row_values .= "</tr>";
    }
    $final_values = "<tr><td colspan='1'></td><td colspan='1' style=''><b>Total</b></td><td style=''><input type='text' name='final_total_qty' id='final_total_qty' class='form-control' value='".round($final_total_qty,3)."' style='padding-right:10px;text-align:right;border:none;' readonly /></td><td colspan='1' style='visibility:visible;'><input type='text' name='final_avg_price' id='final_avg_price' class='form-control' value='".$final_total_price."'  style='padding-right:10px;text-align:right;visibility:visible;' readonly /></td><td><input type='text' name='final_total_amt' id='final_total_amt' class='form-control' value='".$final_total_amt."'  style='padding-right:10px;text-align:right;visibility:visible;' readonly /></td></tr>";

    $margin_type = ""; $margin_value = 0;
    $sql = "SELECT * FROM `price_master` WHERE `item_code` LIKE '$feed_code' AND `sector_code` LIKE '$feed_mill' AND `price_type` = 'ProductionProfit' AND `active` = '1' AND `dflag` = '0' AND `date` IN (SELECT MAX(date) as date FROM `price_master` WHERE `item_code` LIKE '$feed_code' AND `sector_code` LIKE '$feed_mill' AND `price_type` = 'ProductionProfit' AND `active` = '1' AND `dflag` = '0')"; $query = mysqli_query($conn,$sql); $margin_count = mysqli_num_rows($query);
    if($margin_count > 0){
        while($row = mysqli_fetch_assoc($query)){ $margin_type = $row['value_type']; $margin_value = $row['value']; }
    }
    else{
        $margin_type = ""; $margin_value = 0;
    }
    $final_result = $row_values."@".round($final_total_amt,2)."@".$final_values."@".$c."@".$margin_type."@".$margin_value;
}
else{
    $final_result = "";
}
echo $final_result;
?>

