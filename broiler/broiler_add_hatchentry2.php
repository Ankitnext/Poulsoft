<?php
//broiler_add_hatchentry2.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['hatchentry2'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $date = date("Y-m-d");
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $hatch_entry = $row['hatch_entry']; } $incr = $hatch_entry + 1;
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'hatch_entry' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
        $a = 1; $tr_code = $prefix;
        for($i = 0;$i <= 16;$i++){
            if($inv_format[$i.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8'); }
            else if($inv_format[$i.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-"; }
            else if($inv_format[$i.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8'); }
            else if($inv_format[$i.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-"; }
            else if($inv_format[$i.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
            else if($inv_format[$i.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
            else if($inv_format[$i.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
            else if($inv_format[$i.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
            else if($inv_format[$i.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
            else if($inv_format[$i.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
            else if($inv_format[$i.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
            else if($inv_format[$i.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
            else if($inv_format[$i.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
            else if($inv_format[$i.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
            else if($inv_format[$i.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
            else if($inv_format[$i.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
            else{ }
        }
        $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }

		$hcode = "";
        $sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Hatchery%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $hcode = "";
        while($row = mysqli_fetch_assoc($query)){ if($hcode == "") { $hcode = $row['code']; } else{ $hcode = $hcode."','".$row['code']; } }

        $sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$hcode') AND `active` = '1' ".$sector_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = $ven_name = array();
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $ven_name[$row['code']] = $row['description']; }
		
        $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $ven_name[$row['code']] = $row['name']; }
		
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `dflag` = '0' AND (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%')";
        $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `dflag` = '0' AND `description` LIKE '%reject%'";
        $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $reject_code[$row['code']] = $row['code']; $reject_name[$row['code']] = $row['description']; }
        
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
        <style>
        body{
        overflow: auto;
        }
        .form-control{
        padding-left: 1px;
        padding-right: 1px;
        margin-right: 10px;
        height: 25px;
        }
        </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title"> Add Hatch Entry</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_hatchentry2.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group bg-primary" style="width:440px; text-align:center;">
                                            <label>Hatch Details</label>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group" style="width:220px;">
                                            <label>Hatchery<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:210px;" onchange="fetch_trayset_trnums();">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:220px;">
                                            <label>Transaction No.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum; ?>" style="width:210px;" readonly >
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group" style="width:220px;">
                                            <label>Tray Set No.<b style="color:red;">&nbsp;*</b></label>
                                            <select name="link_trnum" id="link_trnum" class="form-control select2" style="width:210px;" onchange="fetch_tray_setting_details();fetch_hatchery_expenses();"></select>
                                        </div>
                                        <div class="form-group" style="width:220px;">
                                            <label>Supplier<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:210px;"></select>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label>Hatch Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="hatch_date" id="hatch_date" class="form-control" style="width:110px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Transfer Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="transfer_date" id="transfer_date" class="form-control" style="width:110px;" readonly >
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label>Setting Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="setting_date" id="setting_date" class="form-control" style="width:110px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>No. of Eggs Set<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="nof_egg_set" id="nof_egg_set" class="form-control text-right" style="width:110px;" readonly >
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group" style="width:210px;">
                                            <label>Item<b style="color:red;">&nbsp;*</b></label>
                                            <select name="item_code" id="item_code" class="form-control select2" style="width:200px;"></select>
                                        </div>
                                        <div class="form-group">
                                            <label>Avg.Prc<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="avg_price" id="avg_price" class="form-control text-right" style="width:110px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Avg.Amt<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="avg_amount" id="avg_amount" class="form-control text-right" style="width:110px;" readonly >
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group bg-primary" style="width:440px; text-align:center;">
                                            <label>Hatch Rejections</label>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Rejection Type</th>
                                                    <th>No. of Eggs</th>
                                                    <th>Rejection%</th>
                                                    <th style="text-align:center;">Add to<br/>Stock</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body2">
                                                <tr>
                                                    <td><select name="rejection_type[]" id="rejection_type[0]" class="form-control select2" style="width:210px;" ><option value="select">select</option><?php foreach($reject_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $reject_name[$scode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="reject_egg_nos[]" id="reject_egg_nos[0]" class="form-control text-right" style="width:110px;" onkeyup="calculate_row_total()" /></td>
                                                    <td><input type="text" name="reject_egg_per[]" id="reject_egg_per[0]" class="form-control text-right" style="width:110px;" readonly /></td>
                                                    <td><input type="checkbox" name="reject_egg_stk[]" id="reject_egg_stk[0]" onchange="update_chkbox_val(this.id);" /><input type="text" name="stk_val[]" id="stk_val[0]" value="0" style="width:10px;visibility:hidden;" readonly /></td>
                                                    <td id="action2[0]"><a href="javascript:void(0);" id="addrow2[0]" onclick="create_row2(this.id)" class="form-control" style="padding-top:15px;width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label>Hatch<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="hatch_nos" id="hatch_nos" class="form-control text-right" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Hatch%<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="hatch_per" id="hatch_per" class="form-control text-right" style="width:110px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label>Death in Hatch<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="deathin_hatch_nos" id="deathin_hatch_nos" class="form-control text-right" style="width:110px;" onkeyup="calculate_row_total()" />
                                        </div>
                                        <div class="form-group">
                                            <label>Death in Hatch%<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="deathin_hatch_per" id="deathin_hatch_per" class="form-control text-right" style="width:110px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label>Culls<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="culls_nos" id="culls_nos" class="form-control text-right" style="width:110px; text-right" onkeyup="calculate_row_total()" />
                                        </div>
                                        <div class="form-group">
                                            <label>Culls%<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="culls_per" id="culls_per" class="form-control text-right" style="width:110px; text-right" readonly />
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label>salable Chicks<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="salable_chick_nos" id="salable_chick_nos" class="form-control text-right" style="width:110px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>salable Chicks%<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="salable_chick_per" id="salable_chick_per" class="form-control text-right" style="width:110px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label>Avg. Chick Weight<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="avg_chick_weight" id="avg_chick_weight" class="form-control text-right" style="width:110px;" />
                                        </div>
                                    </div>
                                    <div class="row" Style="" align="center">
                                        <div class="col-md-6" align="center">
                                           <table class="w-80" style="width:70%;">
                                                <thead>
                                                    <tr class="bg-primary" style="text-align:center;">
                                                        <th>Description</th>
                                                        <th>Total / Type</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                       <td>Eggs Details</td>
                                                       <td><input type="text" name="total_eggs" id="total_eggs" class="form-control text-right" style="width:110px;" readonly /></td>
                                                       <td><input type="text" name="total_eggs_rate" id="total_eggs_rate" class="form-control text-right" style="width:110px;" readonly /></td>
                                                       <td><input type="text" name="total_eggs_amt" id="total_eggs_amt" class="form-control text-right" style="width:110px;" readonly /></td>
                                                    </tr>
                                                    <tr>
                                                       <td>Chicks Details</td>
                                                       <td><input type="text" name="total_chicks" id="total_chicks" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" readonly /></td>
                                                       <td><input type="text" name="chicks_rate" id="chicks_rate" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" readonly /></td>
                                                       <td><input type="text" name="chicks_amount" id="chicks_amount" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" readonly /></td>
                                                    </tr>
                                              </tbody>
                                                <tbody id="exp_body"></tbody>
                                                <tbody id="row_body">
                                                    <tr class="bg-primary" style="text-align:center;">
                                                        <th colspan="4">Vaccine Consumptions</th>
                                                    </tr>
                                                    <tr>
                                                        <td><table><tr><td><select name="vaccine_code[]" id="vaccine_code[0]" class="form-control select2" style="width:300px;border:1px solid #ccc;" onchange="fetch_stock_master(this.id);calculate_vaccine_amount(this.id);"><option value="select">select</option><?php foreach($medvac_code as $mcode){ ?><option value="<?php echo $mcode; ?>"><?php echo $medvac_name[$mcode]; ?></option><?php } ?></select></td><td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td></tr></table></td>
                                                        <td><input type="text" name="vaccine_qty[]" id="vaccine_qty[0]" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);calculate_vaccine_amount(this.id);" onchange="validateamount(this.id);" /></td>
                                                        <td><input type="text" name="vaccine_rate[]" id="vaccine_rate[0]" class="form-control text-right" style="width:110px;" readonly /></td>
                                                        <td><input type="text" name="vaccine_amount[]" id="vaccine_amount[0]" class="form-control text-right" style="width:110px;" readonly /></td>
                                                    </tr>
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                       <th>Net Chicks Rate</th>
                                                       <td></td>
                                                       <td><input type="text" name="avg_chick_price" id="avg_chick_price" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" readonly /></td>
                                                       <td><input type="text" name="avg_chick_amount" id="avg_chick_amount" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" readonly /></td>
                                                    </tr>
                                              </tbody>
                                         </table>
                                       </div>
                                    </div>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:55px;"></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>E-incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="eincr" id="eincr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>V-incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="vincr" id="vincr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>R-incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="rincr" id="rincr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" align="center">
                                                <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                                <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_hatchentry2.php?ccid='+ccid;
            }
            function checkval(){
                var item = from_loc = to_loc = ""; var c = quantity = 0; var l = true;
                var a = document.getElementById("incr").value;
                for(var b = 0;b <= a;b++){
                    c = b + 1;
                    item = document.getElementById("code["+b+"]").value;
                    quantity = document.getElementById("quantity["+b+"]").value;
                    from_loc = document.getElementById("fromwarehouse["+b+"]").value;
                    to_loc = document.getElementById("towarehouse["+b+"]").value;
                    if(l == true){
                        if(item.match("select")){
                            alert("Kindly select Item in row: "+c);
                            document.getElementById("code["+b+"]").focus();
                            l = false;
                        }
                        else if(quantity.length == 0 || quantity == 0 || quantity == "" || quantity == "0.00" || quantity == "0" || quantity == 0.00){
                            alert("Kindly enter Quantity in row: "+c);
                            document.getElementById("quantity["+b+"]").focus();
                            l = false;
                        }
                        else if(from_loc.match("select")){
                            alert("Kindly select From Location in row: "+c);
                            document.getElementById("fromwarehouse["+b+"]").focus();
                            l = false;
                        }
                        else if(to_loc.match("select")){
                            alert("Kindly select To Location in row: "+c);
                            document.getElementById("towarehouse["+b+"]").focus();
                            l = false;
                        }
                        else{
                            l = true;
                        }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("vincr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><table><tr><td>';
                html += '<select name="vaccine_code[]" id="vaccine_code['+d+']" class="form-control select2" style="width:300px;border:1px solid #ccc;" onchange="fetch_stock_master(this.id);calculate_vaccine_amount(this.id);"><option value="select">select</option><?php foreach($medvac_code as $mcode){ ?><option value="<?php echo $mcode; ?>"><?php echo $medvac_name[$mcode]; ?></option><?php } ?></select></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr></table></td>';
                html += '<td><input type="text" name="vaccine_qty[]" id="vaccine_qty['+d+']" class="form-control" style="width:110px;" onkeyup="validate_count(this.id);calculate_vaccine_amount(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="vaccine_rate[]" id="vaccine_rate['+d+']" class="form-control" style="width:110px;" readonly /></td>';
                html += '<td><input type="text" name="vaccine_amount[]" id="vaccine_amount['+d+']" class="form-control" style="width:110px;" readonly /></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("vincr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_total_amount();
            }
            function create_row2(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action2["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("rincr").value = d;
                html += '<tr id="row_no2['+d+']">';
                html += '<td><select name="rejection_type[]" id="rejection_type['+d+']" class="form-control select2" style="width:210px;"><option value="select">select</option><?php foreach($reject_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $reject_name[$scode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="reject_egg_nos[]" id="reject_egg_nos['+d+']" class="form-control text-right" style="width:110px;" onkeyup="calculate_row_total()" /></td>';
                html += '<td><input type="text" name="reject_egg_per[]" id="reject_egg_per['+d+']" class="form-control text-right" style="width:110px;" readonly /></td>';
                html += '<td><input type="checkbox" name="reject_egg_stk[]" id="reject_egg_stk['+d+']" onchange="update_chkbox_val(this.id);" /><input type="text" name="stk_val[]" id="stk_val['+d+']" value="0" style="width:10px;visibility:hidden;" readonly /></td>';
                html += '<td id="action2['+d+']"><a href="javascript:void(0);" id="addrow2['+d+']" onclick="create_row2(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow2['+d+']" onclick="destroy_row2(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
                $('#row_body2').append(html);
                $('.select2').select2();
            }
            function destroy_row2(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no2["+d+"]").remove();
                d--;
                document.getElementById("rincr").value = d;
                document.getElementById("action2["+d+"]").style.visibility = "visible";
            }
            function calculate_vaccine_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var vaccine_qty = document.getElementById("vaccine_qty["+d+"]").value;
                var vaccine_rate = document.getElementById("vaccine_rate["+d+"]").value;
                if(vaccine_qty == ""){ vaccine_qty = 0; } if(vaccine_rate == ""){ vaccine_rate = 0; }
                var vaccine_amount = parseFloat(vaccine_qty) * parseFloat(vaccine_rate);
                document.getElementById("vaccine_amount["+d+"]").value = parseFloat(vaccine_amount).toFixed(2);
                calculate_final_total();
            }
            function fetch_hatch_date(){
                var setting_date = document.getElementById("setting_date").value;
                var fetch_hdate = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_hatchdate.php?setting_date="+setting_date;
                //window.open(url);
				var asynchronous = true;
				fetch_hdate.open(method, url, asynchronous);
				fetch_hdate.send();
				fetch_hdate.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var hatch_date = this.responseText;
                        document.getElementById("hatch_date").value = hatch_date;
                    }
                }
            }
            function fetch_trayset_trnums(){
                var warehouse = document.getElementById("warehouse").value;
                removeAllOptions(document.getElementById("link_trnum"));
                myselect = document.getElementById("link_trnum"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_traysetmaster_trnums.php?warehouse="+warehouse;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        if(item_list.length > 0){
                            $('#link_trnum').append(item_list);
                        }
                        else{
                            alert("There are no Tray Settings available \n Kindly check and try again ...!");
                        }
                    }
                }
            }
            function fetch_tray_setting_details(){
                var link_trnum = document.getElementById("link_trnum").value;
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_traysetmaster_details.php?trnum="+link_trnum;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        var ts_dt = item_list.split("@$&");
                        var ven_dt1 = ts_dt[0]; var ven_dt2 = ven_dt1.split("@"); var vcode = ven_dt2[0]; var vname = ven_dt2[1];
                        var setting_date = ts_dt[1];
                        var transfer_date = ts_dt[2];
                        var hatch_date = ts_dt[3];
                        var nof_egg_set = ts_dt[4]; if(nof_egg_set == ""){ nof_egg_set = 0; }
                        var itm_dt1 = ts_dt[5]; var itm_dt2 = itm_dt1.split("@"); var icode = itm_dt2[0]; var iname = itm_dt2[1];
                        var avg_price = ts_dt[6]; if(avg_price == ""){ avg_price = 0; }
                        var avg_amount = ts_dt[7]; if(avg_amount == ""){ avg_amount = 0; }
                        
                        myselect1 = document.getElementById("vcode");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode(vname);
                        theOption1.value = vcode;
                        theOption1.appendChild(theText1);
                        myselect1.appendChild(theOption1);
                
                        document.getElementById("setting_date").value = setting_date;
                        document.getElementById("transfer_date").value = transfer_date;
                        document.getElementById("hatch_date").value = hatch_date;
                        document.getElementById("nof_egg_set").value = parseInt(nof_egg_set).toFixed(0);

                        myselect1 = document.getElementById("item_code");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode(iname);
                        theOption1.value = icode;
                        theOption1.appendChild(theText1);
                        myselect1.appendChild(theOption1);

                        document.getElementById("avg_price").value = parseInt(avg_price).toFixed(2);
                        document.getElementById("avg_amount").value = parseInt(avg_amount).toFixed(2);
                        calculate_row_total();
                    }
                }
            }
            function fetch_hatchery_expenses(){
                var warehouse = document.getElementById("warehouse").value;
                document.getElementById("exp_body").innerHTML = "";
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_hatchery_expenses2.php?warehouse="+warehouse;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        if(item_list.length > 0){
                            var elist = item_list.split("@");
                            $('#exp_body').append(elist[0]);
                            document.getElementById("eincr").value = elist[1];
                            cal_hexp_aall();
                            calculate_row_total();
                        }
                        else{
                            alert("There are no Expenses available \n Kindly check and try again ...!");
                            cal_hexp_aall();
                            calculate_row_total();
                        }
                    }
                }
            }
            function cal_hexp_amt(a){
                var b = a.split("exp_value"); var d = b[1];
                var total_eggs = document.getElementById("nof_egg_set").value;  if(total_eggs == ""){ total_eggs = 0; }
                var total_chicks = document.getElementById("salable_chick_nos").value;  if(total_chicks == ""){ total_chicks = 0; }

                var etype = "exp_type"; var evalue = "exp_value"; var eamount = "exp_amount";
                var exp_type = document.getElementById(etype+""+d).value;
                var exp_value = document.getElementById(evalue+""+d).value; if(exp_value == ""){ exp_value = 0; }
                if(exp_type.match("on_eggs")){
                    exp_amount = parseFloat(total_eggs) * parseFloat(exp_value);
                    document.getElementById(eamount+""+d).value = parseFloat(exp_amount).toFixed(2);
                }
                else if(exp_type.match("on_chicks")){
                    exp_amount = parseFloat(total_chicks) * parseFloat(exp_value);
                    document.getElementById(eamount+""+d).value = parseFloat(exp_amount).toFixed(2);
                }
                else{ }
                calculate_final_total();
            }
            function cal_hexp_prc(a){
                var b = a.split("exp_amount"); var d = b[1];
                var total_eggs = document.getElementById("nof_egg_set").value;  if(total_eggs == ""){ total_eggs = 0; }
                var total_chicks = document.getElementById("salable_chick_nos").value;  if(total_chicks == ""){ total_chicks = 0; }

                var etype = "exp_type"; var eamount = "exp_amount"; var evalue = "exp_value";
                var exp_type = document.getElementById(etype+""+d).value;
                var exp_amt = document.getElementById(eamount+""+d).value; if(exp_amt == ""){ exp_amt = 0; }

                if(exp_type.match("on_eggs")){
                    exp_amount = 0; if(parseFloat(total_eggs) > 0){ exp_amount = parseFloat(exp_amt) / parseFloat(total_eggs); }
                    document.getElementById(evalue+""+d).value = parseFloat(exp_amount).toFixed(2);
                }
                else if(exp_type.match("on_chicks")){
                    exp_amount = 0; if(parseFloat(total_chicks) > 0){ exp_amount = parseFloat(exp_amt) / parseFloat(total_chicks); }
                    document.getElementById(evalue+""+d).value = parseFloat(exp_amount).toFixed(2);
                }
                else{ }
                calculate_final_total();
            }
            function cal_hexp_aall(){
                var total_eggs = document.getElementById("nof_egg_set").value;  if(total_eggs == ""){ total_eggs = 0; }
                var total_chicks = document.getElementById("salable_chick_nos").value;  if(total_chicks == ""){ total_chicks = 0; }

                var eincr = document.getElementById("eincr").value;
                var etype = "exp_type"; var evalue = "exp_value"; var eamount = "exp_amount";
                for(d = 0;d <=eincr;d++){
                    exp_type = document.getElementById(etype+""+d).value;
                    exp_value = document.getElementById(evalue+""+d).value; if(exp_value == ""){ exp_value = 0; }

                    if(exp_type.match("on_eggs")){
                        exp_amount = parseFloat(total_eggs) * parseFloat(exp_value);
                        document.getElementById(eamount+""+d).value = parseFloat(exp_amount).toFixed(2);
                    }
                    else if(exp_type.match("on_chicks")){
                        exp_amount = parseFloat(total_chicks) * parseFloat(exp_value);
                        document.getElementById(eamount+""+d).value = parseFloat(exp_amount).toFixed(2);
                    }
                    else{ }
                }
                calculate_final_total();
            }
            function calculate_row_total(){
                var nof_egg_set = document.getElementById("nof_egg_set").value; if(nof_egg_set == ""){ nof_egg_set = 0; }
                var rincr = document.getElementById("rincr").value;
                var reject_egg_nos = tot_reject_egg_count = reject_egg_per = 0;
                for(var i = 0;i <= rincr;i++){
                    reject_egg_nos = document.getElementById("reject_egg_nos["+i+"]").value; if(reject_egg_nos == ""){ reject_egg_nos = 0; }
                    reject_egg_per = ((parseFloat(reject_egg_nos) / parseFloat(nof_egg_set)) * 100);
                    document.getElementById("reject_egg_per["+i+"]").value = parseFloat(reject_egg_per).toFixed(2);
                    tot_reject_egg_count += parseFloat(reject_egg_nos);
                }
                var hatch_nos = hatch_per = deathin_hatch_nos = deathin_hatch_per = culls_nos = culls_per = salable_chick_nos = salable_chick_per = 0;
                
                hatch_nos = parseFloat(nof_egg_set) - parseFloat(tot_reject_egg_count);
                hatch_per = ((parseFloat(hatch_nos) / parseFloat(nof_egg_set)) * 100);
                document.getElementById("hatch_nos").value = hatch_nos;
                document.getElementById("hatch_per").value = parseFloat(hatch_per).toFixed(2);
                
                deathin_hatch_nos = document.getElementById("deathin_hatch_nos").value; if(deathin_hatch_nos == ""){ deathin_hatch_nos = 0; }
                deathin_hatch_per = ((parseFloat(deathin_hatch_nos) / parseFloat(nof_egg_set)) * 100);
                document.getElementById("deathin_hatch_per").value = parseFloat(deathin_hatch_per).toFixed(2);
                
                culls_nos = document.getElementById("culls_nos").value; if(culls_nos == ""){ culls_nos = 0; }
                culls_per = ((parseFloat(culls_nos) / parseFloat(nof_egg_set)) * 100);
                document.getElementById("culls_per").value = culls_per.toFixed(2);
                
                salable_chick_nos = parseFloat(nof_egg_set) - parseFloat(tot_reject_egg_count) - parseFloat(deathin_hatch_nos) - parseFloat(culls_nos);
                salable_chick_per = ((parseFloat(salable_chick_nos) / parseFloat(nof_egg_set)) * 100);
                document.getElementById("salable_chick_nos").value = salable_chick_nos;
                document.getElementById("salable_chick_per").value = parseFloat(salable_chick_per).toFixed(2);
                calculate_final_total();
            }
            function calculate_final_total(){
                var total_eggs = document.getElementById("nof_egg_set").value;  if(total_eggs == ""){ total_eggs = 0; }
                var chicks_amt = document.getElementById("avg_amount").value;  if(chicks_amt == ""){ chicks_amt = 0; }
                var total_chicks = document.getElementById("salable_chick_nos").value;  if(total_chicks == ""){ total_chicks = 0; }

                //Total Expense Amount Calculations
                var eincr = document.getElementById("eincr").value;
                var etype = "exp_type"; var evalue = "exp_value"; var eamount = "exp_amount";
                var exp_type = ""; var exp_value = exp_amount = hatch_expense = 0;
                
                for(d = 0;d <=eincr;d++){
                    if(document.getElementById(eamount+""+d)){
                        exp_amount = 0; exp_amount = document.getElementById(eamount+""+d).value; if(exp_amount == ""){ exp_amount = 0; }
                        hatch_expense = parseFloat(hatch_expense) + parseFloat(exp_amount);
                    }
                }

                /* Calculate Vaccine Consumption amount */
                var vincr = document.getElementById("vincr").value;
                var vaccine_expense = vac_exp = 0;
                for(d = 0;d <=vincr;d++){
                    vac_exp = document.getElementById("vaccine_amount["+d+"]").value;
                    if(vac_exp == ""){ vac_exp = 0; }
                    vaccine_expense = parseFloat(vaccine_expense) + parseFloat(vac_exp);
                }
                if(parseFloat(vaccine_expense) > 0){ hatch_expense = parseFloat(hatch_expense) + parseFloat(vaccine_expense); }

                var salable_chick_amt = parseFloat(hatch_expense) + parseFloat(chicks_amt);
                
                var total_eggs_rate = parseFloat(chicks_amt) / parseFloat(total_eggs);
                document.getElementById("total_eggs").value = parseFloat(total_eggs).toFixed(0);
                document.getElementById("total_eggs_rate").value = parseFloat(total_eggs_rate).toFixed(2);
                document.getElementById("total_eggs_amt").value = parseFloat(chicks_amt).toFixed(2);

                var chicks_rate = parseFloat(chicks_amt) / parseFloat(total_chicks);
                document.getElementById("total_chicks").value = parseFloat(total_chicks).toFixed(2);
                document.getElementById("chicks_amount").value = parseFloat(chicks_amt).toFixed(2);
                document.getElementById("chicks_rate").value = parseFloat(chicks_rate).toFixed(2);

                var avg_chick_amount = (parseFloat(hatch_expense) + parseFloat(chicks_amt));
                var avg_chick_price = (parseFloat(avg_chick_amount) / parseFloat(total_chicks));
                document.getElementById("avg_chick_amount").value = parseFloat(avg_chick_amount).toFixed(2);
                document.getElementById("avg_chick_price").value = parseFloat(avg_chick_price).toFixed(2);
            }
            function fetch_stock_master(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("hatch_date").value;
                var sector = document.getElementById("warehouse").value;
                var item_code = document.getElementById(a).value;

                document.getElementById("vaccine_qty["+d+"]").value = "";
                document.getElementById("vaccine_rate["+d+"]").value = "";
                document.getElementById("vaccine_amount["+d+"]").value = "";

                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date;
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        if(item_price.length > 0){
                            var item_details = item_price.split("@");
                            //document.getElementById("available_stock["+d+"]").value = item_details[0];
                            document.getElementById("vaccine_rate["+d+"]").value = item_details[1];
                        }
                        else{
                            //alert("Item Stock not available, Kindly check before saving ...!");
                            //document.getElementById("available_stock["+d+"]").value = 0;
                            //document.getElementById("rate["+d+"]").value = 0;
                            document.getElementById("vaccine_rate["+d+"]").value = 0;
                        }
                    }
                }
            }
            function update_chkbox_val(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var reject_egg_stk = document.getElementById("reject_egg_stk["+d+"]");
                if(reject_egg_stk.checked == true){
                    document.getElementById("stk_val["+d+"]").value = 1;
                }
                else{
                    document.getElementById("stk_val["+d+"]").value = 0; 
                }
            }
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>