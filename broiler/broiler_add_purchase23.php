<?php
//broiler_add_purchase23.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['purchase23'];
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
        //check and fetch date range
        // $file_aurl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))); $e_code = $_SESSION['userid'];
        // $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `file_name` LIKE '$file_aurl' AND `user_code` LIKE '$e_code' AND `active` = '1' AND `dflag` = '0'";
        // $query = mysqli_query($conn,$sql); $r_cnt = mysqli_num_rows($query); $s_days = $e_days = 0; $rdate = date("d.m.Y");
        // if($r_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $s_days = $row['min_days']; $e_days = $row['max_days']; } }
        // $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '$file_aurl' AND `field_function` LIKE 'Date Range Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
        // $query = mysqli_query($conn,$sql); $drange_flag = mysqli_num_rows($query); if($drange_flag <= 0){ $s_days = 9999; $e_days = 0; }
          
        //check and fetch date range
        global $drng_cday; $drng_cday = 1; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        include "poulsoft_fetch_daterange_master.php";

        $date = date("Y-m-d");
        //Generate Invoice transaction number format
        $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
        
        $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $purchases = $row['purchases']; } $incr = $purchases + 1;
        
        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'purchases' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $prefix = $row['prefix']; $incr_wspb_flag = $row['incr_wspb_flag']; $inv_format[$row['sfin_year_flag']] = "sfin_year_flag"; $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag"; $inv_format[$row['efin_year_flag']] = "efin_year_flag"; $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag"; $inv_format[$row['day_flag']] = "day_flag"; $inv_format[$row['day_wsp_flag']] = "day_wsp_flag"; $inv_format[$row['month_flag']] = "month_flag"; $inv_format[$row['month_wsp_flag']] = "month_wsp_flag"; $inv_format[$row['year_flag']] = "year_flag"; $inv_format[$row['year_wsp_flag']] = "year_wsp_flag"; $inv_format[$row['hour_flag']] = "hour_flag"; $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag"; $inv_format[$row['minute_flag']] = "minute_flag"; $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag"; $inv_format[$row['second_flag']] = "second_flag"; $inv_format[$row['second_wsp_flag']] = "second_wsp_flag"; }
        $a = 1; $tr_code = $prefix;
            for($j = 0;$j <= 16;$j++){
                if(!empty($inv_format[$j.":".$a])){
                    if($inv_format[$j.":".$a] == "sfin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8'); }
                    else if($inv_format[$j.":".$a] == "sfin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-"; }
                    else if($inv_format[$j.":".$a] == "efin_year_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8'); }
                    else if($inv_format[$j.":".$a] == "efin_year_wsp_flag"){ $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-"; }
                    else if($inv_format[$j.":".$a] == "day_flag"){ $tr_code = $tr_code."".date("d"); }
                    else if($inv_format[$j.":".$a] == "day_wsp_flag"){ $tr_code = $tr_code."".date("d")."-"; }
                    else if($inv_format[$j.":".$a] == "month_flag"){ $tr_code = $tr_code."".date("m"); }
                    else if($inv_format[$j.":".$a] == "month_wsp_flag"){ $tr_code = $tr_code."".date("m")."-"; }
                    else if($inv_format[$j.":".$a] == "year_flag"){ $tr_code = $tr_code."".date("Y"); }
                    else if($inv_format[$j.":".$a] == "year_wsp_flag"){ $tr_code = $tr_code."".date("Y")."-"; }
                    else if($inv_format[$j.":".$a] == "hour_flag"){ $tr_code = $tr_code."".date("H"); }
                    else if($inv_format[$j.":".$a] == "hour_wsp_flag"){ $tr_code = $tr_code."".date("H")."-"; }
                    else if($inv_format[$j.":".$a] == "minute_flag"){ $tr_code = $tr_code."".date("i"); }
                    else if($inv_format[$j.":".$a] == "minute_wsp_flag"){ $tr_code = $tr_code."".date("i")."-"; }
                    else if($inv_format[$j.":".$a] == "second_flag"){ $tr_code = $tr_code."".date("s"); }
                    else if($inv_format[$j.":".$a] == "second_wsp_flag"){ $tr_code = $tr_code."".date("s")."-"; }
                    else{ }
                }
            }
        if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
        $trnum = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $trnum = $tr_code."-".$incr; } else{ $trnum = $tr_code."".$incr; }
        
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC";
        $query = mysqli_query($conn,$sql); $ven_code = $ven_name = array();
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
				
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }
				
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
        $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $farm_list = "";
        while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; } }
        
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `code` IN ('$farm_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
		$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; } $tds_eflag = 1;
		
		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql);
        $jcount = mysqli_num_rows($query); $gst_code = $gst_name = $gst_value = array();
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bag%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bcodes = "";
        while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $bag_code = $bag_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; $bag_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $ocoa_code = $ocoa_name = array();
        while($row = mysqli_fetch_assoc($query)){ $ocoa_code[$row['code']] = $row['code']; $ocoa_name[$row['code']] = $row['description']; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Purchase TDS' AND `field_function` LIKE 'after 50L TDS Auto' AND `flag` = '1' AND (`user_access` LIKE '%$user_code%' || `user_access` LIKE 'all');";
        $query = mysqli_query($conn,$sql); $auto_tds_flag = mysqli_num_rows($query);

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Purchases' AND `field_function` LIKE 'Display Brand Selection Dropdown' AND `flag` = '1' AND (`user_access` LIKE '%$user_code%' || `user_access` LIKE 'all');";
        $query = mysqli_query($conn,$sql); $brand_flag = mysqli_num_rows($query);
        //if($brand_flag > 0) { echo "Brand Flag"; }

        $sql = "SELECT * FROM `broiler_item_brands` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $brand_code = $brand_name = array();
        while($row = mysqli_fetch_assoc($query)){ $brand_code[$row['code']] = $row['code']; $brand_name[$row['code']] = $row['description']; }
       
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
            padding-left: 2px;
            padding-right: 0px;
        }
        .form-group{
            margin: 0 3px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Purchases</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_purchase23.php" method="post" role="form" enctype="multipart/form-data" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;" <?php if($auto_tds_flag == "1"){ echo 'onchange="broiler_fetch_Supplierpurchases(this.id);"'; } ?>>
                                        </div>
                                        <div class="form-group">
                                            <label>Supplier<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:180px;" <?php if($auto_tds_flag == "1"){ echo 'onchange="broiler_fetch_Supplierpurchases(this.id);"'; } ?>>
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:85px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>trnum</label>
                                            <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum; ?>" style="width:130px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle</label>
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" style="width:85px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" style="width:85px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Driver Mobile</label>
                                            <input type="text" name="driver_mobile" id="driver_mobile" class="form-control" style="width:115px;" >
                                        </div>
                                        <div class="form-group">
                                            <label for="">Sent Qty</label>
                                            <input type="radio" name="amt_cal_basedon" id="amt_cal_basedon1" class="form-control" value="SentQty" style="transform: scale(.7);" onchange="cal_totamt_multiple();">
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label for="">Received Qty</label>
                                            <input type="radio" name="amt_cal_basedon" id="amt_cal_basedon2" class="form-control" value="RcvdQty" style="transform: scale(.7);" onchange="cal_totamt_multiple();" checked >
                                        </div>
                                        <div class="form-group" <?php if($auto_tds_flag == "1"){ echo 'style="visibility:visible;"'; } else{ echo 'style="visibility:hidden;"'; } ?>>
                                            <label>Pur Amt</label>
                                            <input type="text" name="ven_pur_totamt" id="ven_pur_totamt" class="form-control"style="width:90px;" readonly >
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>Mnu TDS</label>
                                            <input type="text" name="mnu_tds_uchk" id="mnu_tds_uchk" class="form-control"style="width:90px;" value = "1" readonly >
                                        </div>
                                    </div><br/><br/>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="form-group">
                                            <label>Item<b style="color:red;">&nbsp;*</b></label>
                                            <select name="icode[]" id="icode[0]" class="form-control select2" style="width:110px;" onchange="fetch_itemuom(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php if($brand_flag > 0) { ?>
                                        <div class="form-group">
                                            <label>Brand</label>
                                            <select name="brand_code[]" id="brand_code[0]" class="form-control select2" style="width:110px;" onchange="">
                                                <option value="select">select</option>
                                                <?php foreach($brand_code as $bd_code){ ?><option value="<?php echo $bd_code; ?>"><?php echo $brand_name[$bd_code]; ?></option><?php } ?>
                                            </select>
                                        </div> <?php } ?>
                                        <div class="form-group">
                                            <label>UOM</label>
                                            <td><input type="text" name="uom[]" id="uom[0]" class="form-control" style="width:80px;" readonly /></td>
                                        </div>
                                        <div class="form-group">
                                            <label>Sent Qty</label>
                                            <input type="text" name="snt_qty[]" id="snt_qty[0]" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Rcv Qty<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rcd_qty[]" id="rcd_qty[0]" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Free Qty</label>
                                            <input type="text" name="fre_qty[]" id="fre_qty[0]" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Rate<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rate[]" id="rate[0]" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum_rate(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount_rate(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label title="Discount Percentage">Disc. %</label>
                                            <input type="text" name="dis_per[]" id="dis_per[0]" class="form-control" placeholder="%" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);">
                                        </div>
                                        <div class="form-group">
                                            <label title="Discount Amount">Disc. &#8377</label>
                                            <input type="text" name="dis_amt[]" id="dis_amt[0]" class="form-control" placeholder="&#8377" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);">
                                        </div>
                                        <div class="form-group">
                                            <label>GST</label>
                                            <select name="gst_per[]" id="gst_per[0]" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?>
                                        </select>
                                        </div>
                                        <!--<div class="form-group" style="padding-left:0;">
                                            <label>GST &#8377</label>
                                            <input type="text" name="gst_amt[]'.'" id="gst_amt[0]'.'" class="form-control" placeholder="&#8377" style="width:90px;" readonly >
                                        </div>-->
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="text" name="item_tamt[]" id="item_tamt[0]" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);cal_prc(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Sector/Farm<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse[]" id="warehouse[0]" class="form-control select2" style="width:200px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                        </select>
                                        </div>
                                        <!--<div class="form-group">
                                            <label>Farm Batch</label>
                                            <select name="farm_batch[]" id="farm_batch[0]" class="form-control select2" style="width:100px;">
                                                <option value="select">select</option>
                                            </select>
                                        </div>-->
                                        <div class="form-group" id="action[0]"><br/>
                                            <a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="padding-top:15px;width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-18" id="row_body"></div><br/><br/>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Type</label>
                                                    <select name="freight_type" id="freight_type" class="form-control select2" onchange="calculate_netpay()">
                                                        <option value="select">select</option>
                                                        <option value="include">Include</option>
                                                        <option value="exclude">Exclude</option>
                                                        <option value="inbill">In Bill</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay Later</label>
                                                    <input type="radio" name="pay_type" id="pay_type1" class="form-control" value="PayLater" style="width:90px;transform: scale(.7);" onclick="fetch_freight_coa_account(this.id)" checked />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay In Bill</label>
                                                    <input type="radio" name="pay_type" id="pay_type2" class="form-control" value="PayInBill" style="width:90px;transform: scale(.7);" onclick="fetch_freight_coa_account(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pay Account</label>
                                                    <select name="freight_pay_acc" id="freight_pay_acc" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                        ?><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Account</label>
                                                    <select name="freight_acc" id="freight_acc" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `acc_coa` WHERE `freight_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                        ?><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Amount</label>
                                                    <input type="text" name="freight_amount" id="freight_amount" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id);calculate_netpay();" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                        </div>
                                    </div><br/><br/>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Bag Type</label>
                                                    <select name="bag_code" id="bag_code" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php foreach($bag_code as $carrier){ ?><option value="<?php echo $carrier; ?>"><?php echo $bag_name[$carrier]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>No.of Bags</label>
                                                    <input type="text" name="bag_count" id="bag_count" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Batch No.</label>
                                                    <input type="text" name="batch_no" id="batch_no" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Expiry Date</label>
                                                    <input type="text" name="exp_date" id="exp_date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1" style="visibility:hidden;"><input type="text" name="mnu_tds_edit" id="mnu_tds_edit" value="0"/></div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>TCS</label>
                                                    <input type="checkbox" name="tcds_per" id="tcds_per" class="form-control" value="<?php echo $tdsper; ?>" style="transform: scale(.7);" <?php if($auto_tds_flag == "1"){ echo 'onchange="manual_uncheck();"'; } else{ echo 'onchange="calculate_netpay();"'; } ?> />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>TCS Amount</label>
                                                    <input type="text" name="tcds_amount" id="tcds_amount" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id);update_manualtds_flag();" onchange="validateamount(this.id);" <?php if($tds_eflag == 0){ echo "readonly"; } ?> />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Other Charges</label>
                                                    <select name="ocharge_coa" id="ocharge_coa" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php foreach($ocoa_code as $ocode){ ?><option value="<?php echo $ocode; ?>"><?php echo $ocoa_name[$ocode]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input type="text" name="ocharge_amt" id="ocharge_amt" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id);calculate_netpay();" onchange="validateamount(this.id);" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Roundoff</label>
                                                    <input type="text" name="round_off" id="round_off" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Net Amount</label>
                                                    <input type="text" name="finl_amt" id="finl_amt" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="width:200px;">
                                                <label>Reference Document-1</label>
                                                <input type="file" name="pur_doc_1" id="pur_doc_1" class="form-control1" onchange="show_delete_btn(this.id,'clearButton')" style="width:180px;">&nbsp;
                                                <i class="fa fa-close" style="color:red;visibility:hidden;" title="delete" id="clearButton" onclick="clear_file(this.id, 'pur_doc_1')"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="width:200px;">
                                                <label>Reference Document-2</label>
                                                <input type="file" name="pur_doc_2" id="pur_doc_2" class="form-control1" onchange="show_delete_btn(this.id,'clearButton1')" style="width:180px;">&nbsp;
                                                <i class='fa fa-close' style='color:red; visibility: hidden;' title='delete' id="clearButton1" onclick="clear_file(this.id,'pur_doc_2')"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                             <div class="form-group" style="width:200px;">
                                                <label>Reference Document-3</label>
                                                <input type="file" name="pur_doc_3" id="pur_doc_3" class="form-control1" onchange="show_delete_btn(this.id,'clearButton2')" style="width:180px;">&nbsp;
                                                <i class='fa fa-close' style='color:red; visibility: hidden;' title='delete' id="clearButton2" onclick="clear_file(this.id,'pur_doc_3')"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
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
                window.location.href = 'broiler_display_purchase23.php?ccid='+ccid;
            }
            function clear_file(a,b) { 
               document.getElementById(b).value = '';
               document.getElementById(a).style.visibility = 'hidden';
            }
            function show_delete_btn(a,b) {
                var selected_file = document.getElementById(a);
                var hidedeletebutton = document.getElementById(b);

                if (selected_file.files.length > 0) {
                hidedeletebutton.style.visibility = 'visible'; 
                } else {
                hidedeletebutton.style.visibility = 'hidden'; 
                }
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var sup_code = document.getElementById("vcode").value;
                var ocharge_coa = document.getElementById("ocharge_coa").value;
                var ocharge_amt = document.getElementById("ocharge_amt").value; if(ocharge_amt == ""){ ocharge_amt = 0; }
                var incrs = document.getElementById("incr").value;
                var item_code = warehouse = ""; var rcd_qty = fre_qty = rate = c = rate_count = 0;
                var l = true;
                if(sup_code.match("select")){
                    alert("Please select Supplier");
                    document.getElementById("vcode").focus();
                    l = false;
                }
                else if(ocharge_coa == "select" && parseFloat(ocharge_amt) > 0){
                    alert("Please select Other Charges CoA Account");
                    document.getElementById("ocharge_coa").focus();
                    l = false;
                }
                else if(ocharge_coa != "select" && parseFloat(ocharge_amt) == 0){
                    alert("Please enter Other Charges Amount");
                    document.getElementById("ocharge_amt").focus();
                    l = false;
                }
                else{
                    for(var d = 0;d <= incrs;d++){
                        if(l == true){
                            c = d + 1;
                            item_code = document.getElementById("icode["+d+"]").value;
                            rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                            fre_qty = document.getElementById("fre_qty["+d+"]").value; if(fre_qty == ""){ fre_qty = 0; }
                            rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                            warehouse = document.getElementById("warehouse["+d+"]").value;
                            if(item_code.match("select")){
                                alert("Please select Item in row:-"+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(rcd_qty) == 0 && parseFloat(fre_qty) == 0){
                                alert("Please enter Rcd Qty / Free qty in row:-"+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(rcd_qty) > 0 && parseFloat(rate) == 0){
                                alert("Please enter Rate in row:-"+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            else if(warehouse.match("select")){
                                alert("Please select Sector/Farm in row:-"+c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else{ }

                            if(parseFloat(rate) < 2){
                                rate_count++;
                            }
                        }
                    }
                }
                
                if(l == true){
                    if(parseFloat(rate_count) > 0){
                        var x = confirm("Rate entered as 1/-\nAre you sure you want to save the transaction?");
                        if(x == true){
                            return true;
                        }
                        else{
                            document.getElementById("submit").style.visibility = "visible";
                            document.getElementById("ebtncount").value = "0";
                            return false;
                        }
                    }
                    else{
                        return true;
                    }
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                var brand_flag = '<?php echo $brand_flag; ?>';

                html += '<div class="row" id="row_no['+d+']">';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Item<b style="color:red;">&nbsp;*</b></label><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:110px;" onchange="fetch_itemuom(this.id);"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></div>';
                if(parseInt(brand_flag) > 0){ html += '<div class="form-group"><label class="labelrow" style="display:none;">Brand<b style="color:red;">&nbsp;*</b></label><select name="brand_code[]" id="brand_code['+d+']" class="form-control select2" style="width:110px;" onchange=""><option value="select">select</option><?php foreach($brand_code as $bd_code){ ?><option value="<?php echo $bd_code; ?>"><?php echo $brand_name[$bd_code]; ?></option><?php } ?></select></div>'; }
                html += '<div class="form-group"><label class="labelrow" style="display:none;">UOM</label><input type="text" name="uom[]" id="uom['+d+']" class="form-control" placeholder="0.00" style="width:80px;" readonly /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Sent Qty</label><input type="text" name="snt_qty[]" id="snt_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rcv Qty<b style="color:red;">&nbsp;*</b></label><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Free Qty</label><input type="text" name="fre_qty[]" id="fre_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rate<b style="color:red;">&nbsp;*</b></label><input type="text" name="rate[]" id="rate['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum_rate(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount_rate(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Disc. %</label><input type="text" name="dis_per[]" id="dis_per['+d+']" class="form-control" placeholder="%" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);"></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Disc. &#8377</label><input type="text" name="dis_amt[]" id="dis_amt['+d+']" class="form-control" placeholder="&#8377" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);"></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">GST</label><select name="gst_per[]" id="gst_per['+d+']" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;"><option value="select">select</option><?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?></select></div>';
                //html += '<div class="form-group"><label class="labelrow" style="display:none;">GST &#8377</label><input type="text" name="gst_amt[]'.'" id="gst_amt['+d+']'.'" class="form-control" placeholder="&#8377" style="width:90px;" readonly ></div>'; cal_prc
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Amount</label><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control" placeholder="0.00" style="width:90px;"  onkeyup="validatenum(this.id);cal_prc(this.id);"></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Sector/Farm<b style="color:red;">&nbsp;*</b></label><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:200px;"><option value="select">select</option><?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?></select></div>';
                //html += '<div class="form-group"><label class="labelrow" style="display:none;">Farm Batch</label><select name="farm_batch[]" id="farm_batch['+d+']" class="form-control select2" style="width:100px;"><option value="select">select</option></select></div>';
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '</div>';
                html += '<hr class="labelrow" style="display:none;" />';
                $('#row_body').append(html); $('.select2').select2(); calculate_netpay();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_netpay();
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(document.getElementById("amt_cal_basedon1").checked == true){ var qty_on_sqty_flag = 1; } else{ var qty_on_sqty_flag = 0; }
                
                if(qty_on_sqty_flag == 1 || qty_on_sqty_flag == "1"){ var qty = document.getElementById("snt_qty["+d+"]").value; }
                else{ var qty = document.getElementById("rcd_qty["+d+"]").value; }
                
                var price = document.getElementById("rate["+d+"]").value;
                //var camt = document.getElementById("item_tamt["+d+"]").value;
                var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                var gst_per1 = document.getElementById("gst_per["+d+"]").value;
                if(!gst_per1.match("select")){
                    var gst_per2 = gst_per1.split("@");
                    var gst_per = gst_per2[1];
                }
                else{
                    var gst_per = 0; 
                }

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                //if(camt == "" || camt.length == 0 || camt == "0.00" || camt == "0"){ camt = 0; }
                if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ dis_amt = 0; }
                if(gst_per == "" || gst_per.length == 0 || gst_per == "0.00" || gst_per == "0"){ gst_per = 0; }

                var total_amt = parseFloat(qty) * parseFloat(price);
                
                if(dis_amt > 0){
                    total_amt = parseFloat(total_amt) - parseFloat(dis_amt);
                }
                if(gst_per > 0){
                    var gst_value = ((parseFloat(gst_per) / 100) * total_amt);
                    total_amt = parseFloat(total_amt) + parseFloat(gst_value);
                }
                document.getElementById("item_tamt["+d+"]").value = parseFloat(total_amt).toFixed(2);
                
                calculate_netpay();
            }
            function cal_prc(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(document.getElementById("amt_cal_basedon1").checked == true){ var qty_on_sqty_flag = 1; } else{ var qty_on_sqty_flag = 0; }

                var camt = document.getElementById("item_tamt["+d+"]").value;
                if(qty_on_sqty_flag == 1 || qty_on_sqty_flag == "1"){ var qty = document.getElementById("snt_qty["+d+"]").value; }
                else{ var qty = document.getElementById("rcd_qty["+d+"]").value; }

                if(camt == "" || camt.length == 0 || camt == "0.00" || camt == "0"){ camt = 0; }
                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }

                var total_prc = parseFloat(camt) / parseFloat(qty);
                document.getElementById("rate["+d+"]").value = parseFloat(total_prc).toFixed(2);

            }
            function cal_totamt_multiple(){
                var incr =document.getElementById("incr").value;
                if(document.getElementById("amt_cal_basedon1").checked == true){ var qty_on_sqty_flag = 1; } else{ var qty_on_sqty_flag = 0; }
                var qty = price = dis_amt = gst_per = total_amt = gst_value = 0;
                var gst_per1 = "";
                var gst_per2 = new Array();

                for(var d = 0;d <= incr;d++){
                    if(qty_on_sqty_flag == 1 || qty_on_sqty_flag == "1"){ qty = document.getElementById("snt_qty["+d+"]").value; }
                    else{ qty = document.getElementById("rcd_qty["+d+"]").value; }
                    
                    price = document.getElementById("rate["+d+"]").value;
                    dis_amt = document.getElementById("dis_amt["+d+"]").value;
                    gst_per1 = document.getElementById("gst_per["+d+"]").value;
                    if(!gst_per1.match("select")){ gst_per2 = gst_per1.split("@"); gst_per = gst_per2[1]; }
                    else{ gst_per = 0; }

                    if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                    if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                    if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ dis_amt = 0; }
                    if(gst_per == "" || gst_per.length == 0 || gst_per == "0.00" || gst_per == "0"){ gst_per = 0; }

                    total_amt = parseFloat(qty) * parseFloat(price);
                    if(dis_amt > 0){
                        total_amt = parseFloat(total_amt) - parseFloat(dis_amt);
                    }
                    if(gst_per > 0){
                        gst_value = ((parseFloat(gst_per) / 100) * total_amt);
                        total_amt = parseFloat(total_amt) + parseFloat(gst_value);
                    }
                    document.getElementById("item_tamt["+d+"]").value = parseFloat(total_amt).toFixed(2);
                }
                calculate_netpay();
            }
            function fetch_discount_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty_on_sqty_flag = '<?php echo $qty_on_sqty_flag; ?>';
                if(qty_on_sqty_flag == 1 || qty_on_sqty_flag == "1"){ var qty = document.getElementById("snt_qty["+d+"]").value; }
                else{ var qty = document.getElementById("rcd_qty["+d+"]").value; }
                
                var price = document.getElementById("rate["+d+"]").value;

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }

                var total_amt = parseFloat(qty) * parseFloat(price);

                if(total_amt == "" || total_amt.length == 0 || total_amt == "0.00" || total_amt == "0"){ total_amt = 0; }
                if(b[0].match("dis_per")){
                    var dis_per = document.getElementById("dis_per["+d+"]").value;
                    if(dis_per == "" || dis_per.length == 0 || dis_per == "0.00" || dis_per == "0"){ }
                    else{
                        var dis_value = ((parseFloat(dis_per) / 100) * total_amt);
                        if(dis_value == "NaN" || dis_value.length == 0 || dis_value == 0){ dis_value = ""; }
                        document.getElementById("dis_amt["+d+"]").value = dis_value;
                        calculate_total_amt(a);
                    }
                    
                }
                else{
                    var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                    if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ }
                    else{
                        var dis_per = ((parseFloat(dis_amt) * 100) / total_amt);
                        //var dis_per = (((parseFloat(dis_amt) * 100) / total_amt) * 100);
                        if(dis_per == "NaN" || dis_per.length == 0 || dis_per == 0){ dis_per = ""; }
                        document.getElementById("dis_per["+d+"]").value = dis_per.toFixed(2);
                        calculate_total_amt(a);
                    }
                }
            }
            function calculate_netpay(){
                var incr = parseInt(document.getElementById("incr").value);
                var total_amount = 0; var tamt = 0; var net_amount = 0;
                for(var d = 0; d <= incr; d++){
                    tamt = document.getElementById("item_tamt["+d+"]").value;
                    if(tamt == "" || tamt == "0" || tamt.length == 0 || tamt == "0.00"){ tamt = 0; }
                    total_amount = parseFloat(total_amount) + parseFloat(tamt);
                }
                var freight_amount = document.getElementById("freight_amount").value;
                if(freight_amount == "" || freight_amount == "0" || freight_amount.length == 0 || freight_amount == "0.00"){ freight_amount = 0; }
                if(freight_amount > 0){
                    var freight_type = document.getElementById("freight_type").value;
                    if(!freight_type.match("select")){
                        if(freight_type.match("include")){
                            net_amount = parseFloat(total_amount) - parseFloat(freight_amount);
                        }
                        else if(freight_type.match("exclude")){
                            net_amount = parseFloat(total_amount);
                        }
                        else if(freight_type.match("inbill")){
                            net_amount = parseFloat(total_amount) + parseFloat(freight_amount);
                        }
                        else{
                            net_amount = parseFloat(total_amount);
                        }
                    }
                    else{
                        net_amount = total_amount;
                    }
                }
                else{
                    net_amount = total_amount;
                }
                //TDS Calculations
                var mnu_tds_edit = document.getElementById("mnu_tds_edit").value;
                if(parseInt(mnu_tds_edit) == 1){
                    var tcds_amount = document.getElementById("tcds_amount").value;
                    net_amount = parseFloat(net_amount) + parseFloat(tcds_amount);
                }
                else{
                    var auto_tds_flag = '<?php echo $auto_tds_flag; ?>';
                    if(auto_tds_flag == 1 || auto_tds_flag == "1"){
                        var mnu_tds_uchk = document.getElementById("mnu_tds_uchk").value;
                        if(mnu_tds_uchk == 1 || mnu_tds_uchk == "1"){
                            var rqty = rprc = ftamt = total_item_amount = 0;
                            for(d = 0; d <= incr; d++){
                                /*rqty = document.getElementById("rcd_qty["+d+"]").value;
                                rprc = document.getElementById("rate["+d+"]").value;
                                if(rqty == "" || rqty == "0" || rqty.length == 0 || rqty == "0.00"){ rqty = 0; }
                                if(rprc == "" || rprc == "0" || rprc.length == 0 || rprc == "0.00"){ rprc = 0; }
                                if(rqty > 0 && rprc > 0){
                                    total_item_amount = (parseFloat(total_item_amount) + (parseFloat(rqty) * parseFloat(rprc)));
                                }*/
                                ftamt = document.getElementById("item_tamt["+d+"]").value;
                                total_item_amount = parseFloat(total_item_amount) + parseFloat(ftamt);
                            }
                            var ven_pur_totamt = document.getElementById("ven_pur_totamt").value; if(ven_pur_totamt == ""){ ven_pur_totamt = 0; }
                            var ptot_amt = parseFloat(total_item_amount) + parseFloat(ven_pur_totamt);
                            var tcds_flag = document.getElementById("tcds_per");
                            var tdsper = '<?php echo $tdsper; ?>'; if(tdsper == ""){ tdsper = 0; }
                            if(parseFloat(ven_pur_totamt) > 5000000 || parseFloat(ptot_amt) > 5000000){
                                tcds_flag.checked = true;
                                
                                if(parseFloat(tdsper) > 0){ var out_ptotamt = parseFloat(ptot_amt) - 5000000; var tcds_amount = ((parseFloat(tdsper) / 100) * out_ptotamt); }
                                else{ var tcds_amount = 0; }

                                document.getElementById("tcds_amount").value = parseFloat(tcds_amount).toFixed(2);
                                net_amount = parseFloat(net_amount) + parseFloat(tcds_amount);
                            }
                            else{
                                tcds_flag.checked = false;
                                net_amount = parseFloat(net_amount);
                                document.getElementById("tcds_amount").value = 0;
                            }
                        }
                        else{
                            net_amount = parseFloat(net_amount);
                            document.getElementById("tcds_amount").value = 0;
                        }
                    }
                    else{
                        var tcds_flag = document.getElementById("tcds_per");
                        if(tcds_flag.checked == true){
                            var rqty = rprc = ftamt = item_amount = 0;
                            for(d = 0; d <= incr; d++){
                                /*rqty = document.getElementById("rcd_qty["+d+"]").value;
                                rprc = document.getElementById("rate["+d+"]").value;
                                if(rqty == "" || rqty == "0" || rqty.length == 0 || rqty == "0.00"){ rqty = 0; }
                                if(rprc == "" || rprc == "0" || rprc.length == 0 || rprc == "0.00"){ rprc = 0; }
                                if(rqty > 0 && rprc > 0){
                                    item_amount = (parseFloat(item_amount) + (parseFloat(rqty) * parseFloat(rprc)));
                                }*/
                                ftamt = document.getElementById("item_tamt["+d+"]").value;
                                item_amount = parseFloat(item_amount) + parseFloat(ftamt);
                            }
                            var tcds_per = document.getElementById("tcds_per").value;
                            var tcds_amount = ((parseFloat(tcds_per) / 100) * item_amount).toFixed(2);
                            document.getElementById("tcds_amount").value = tcds_amount;
                            net_amount = parseFloat(net_amount) + parseFloat(tcds_amount);
                        }
                        else{
                            document.getElementById("tcds_amount").value = "";
                        }
                    }
                }
                var ocharge_amt = document.getElementById("ocharge_amt").value; if(ocharge_amt == ""){ ocharge_amt = 0; }
                net_amount = parseFloat(net_amount) + parseFloat(ocharge_amt);

                var final_amt = net_amount.toFixed(0);
                var roundoff = parseFloat(final_amt) - parseFloat(net_amount);

                document.getElementById("round_off").value = roundoff.toFixed(2);
                document.getElementById("finl_amt").value = final_amt;
            }
            function fetch_freight_coa_account(a){
                removeAllOptions(document.getElementById("freight_pay_acc"));
                myselect = document.getElementById("freight_pay_acc"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(a.match("pay_type1")){
                    <?php
					$sql="SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
                else{
                    <?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
            }
            function broiler_fetch_Supplierpurchases(a){
                //var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var auto_tds_flag = '<?php echo $auto_tds_flag; ?>';
                if(auto_tds_flag == 1 || auto_tds_flag == "1"){
                    var vcode = document.getElementById("vcode").value;
                    var date = document.getElementById("date").value;
                    if(vcode != "select"){
                        var ven_bals = new XMLHttpRequest();
                        var method = "GET";
                        var url = "broiler_fetch_vendorpurtamt.php?date="+date+"&vcode="+vcode;
                        //window.open(url);
                        var asynchronous = true;
                        ven_bals.open(method, url, asynchronous);
                        ven_bals.send();
                        ven_bals.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var pur_info = this.responseText;
                                var pur_details = pur_info.split("@");
                                if(pur_details[1] == "1" || pur_details[1] == 1){
                                    alert("Appropriate Financial Year not defined \n Kindly check financial year for the date: "+date);
                                    calculate_netpay();
                                }
                                else{
                                    document.getElementById("ven_pur_totamt").value = parseFloat(pur_details[0]).toFixed(2);
                                    calculate_netpay();
                                }
                            }
                        }
                    }
                    else{
                        document.getElementById("ven_pur_totamt").value = 0;
                        calculate_netpay();
                    }
                    
                }
                else{
                    calculate_netpay();
                }
            }
            function manual_uncheck(){
                var tcds_flag = document.getElementById("tcds_per");
                if(tcds_flag.checked == true){
                    document.getElementById("mnu_tds_uchk").value = 1;
                    calculate_netpay();
                }
                else{
                    document.getElementById("mnu_tds_uchk").value = 0;
                    calculate_netpay();
                }
            }
            function update_manualtds_flag(){
                document.getElementById("mnu_tds_edit").value = 1;
                calculate_netpay();
            }
            function fetch_itemuom(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var code = document.getElementById("icode["+d+"]").value;
                var uom = "";
                <?php
                foreach($item_code as $icode){
                    $uom = $item_cunit[$icode];
                    echo "if(code == '$icode'){";
                ?>
                uom = '<?php echo $uom; ?>';
                <?php
                    echo "}";
                }
                ?>
                document.getElementById("uom["+d+"]").value = uom;
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function validatenum_rate(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount_rate(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(3); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                if(window.screen.availWidth <= 400){
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; }
                }
                else{
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; }
                }
            }, 1000);
        </script>
          <script>
            //Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
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