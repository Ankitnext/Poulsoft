<?php
//broiler_edit_feedsale4.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['feedsale4'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $elink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $elink = explode(",",$row['editaccess']);
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
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        //check and fetch date range
        $file_aurl = str_replace("_edit_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))); $e_code = $_SESSION['userid'];
        $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `file_name` LIKE '$file_aurl' AND `user_code` LIKE '$e_code' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $r_cnt = mysqli_num_rows($query); $s_days = $e_days = 0; $rdate = date("d.m.Y");
        if($r_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $s_days = $row['min_days']; $e_days = $row['max_days']; } }
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '$file_aurl' AND `field_function` LIKE 'Date Range Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $drange_flag = mysqli_num_rows($query); if($drange_flag <= 0){ $s_days = 9999; $e_days = 0; }

		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; $gst_coa[$row['code']] = $row['coa_code']; }
	
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%' AND `description` NOT IN ('Feed Additives') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $fcodes = "";
        while($row = mysqli_fetch_assoc($query)){ if($fcodes == ""){ $fcodes = $row['code']; } else{ $fcodes = $fcodes."','".$row['code']; } }
        
        $sql4 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Sales' AND `field_function` LIKE 'Display Only Feed Items'  AND  `user_access` = 'all' AND `flag` = '1'";
        $query4 = mysqli_query($conn, $sql4); $dfo_flag = mysqli_num_rows($query4); $ft_flag = 0;
        if($dfo_flag > 0){ $feed_fltr = " AND `category` IN ('$fcodes')"; } else { $feed_fltr = "";  }

        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0'".$feed_fltr.""; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $transport_code[$row['code']] = $row['code']; $transport_name[$row['code']] = $row['description']; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Sale' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Sale' AND `field_function` LIKE 'Auto Avg Price'"; $query = mysqli_query($conn,$sql); $autoavgprice_flag = 0; $aapcount = mysqli_num_rows($query);
        if($aapcount > 0){ while($row = mysqli_fetch_assoc($query)){ $autoavgprice_flag = $row['flag']; } } else{ $autoavgprice_flag = 0; } if($autoavgprice_flag == "" || $autoavgprice_flag == 0){ $autoavgprice_flag = 0; }
        //echo $autoavgprice_flag;
        /* Feed Sale Bag flag check*/
        $sql3 = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Sale' AND `field_function` LIKE 'Bags'  AND (`user_access` LIKE '%$addedemp%' OR `user_access` = 'all')";
        $query3 = mysqli_query($conn, $sql3);
        $ccount3 = mysqli_num_rows($query3);
        if($ccount3 > 0){
            while($row3 = mysqli_fetch_assoc($query3)){
                $bag_access_flag = $row3['flag'];
            }
        }
        else{
            mysqli_query($conn, "INSERT INTO `extra_access` ( `field_name`, `field_function`, `user_access`, `flag`) VALUES ( 'Feed Sale', 'Bags', 'all', '0')");
            $bag_access_flag =  0;
        }
        if($bag_access_flag == ''){ $bag_access_flag =  0; }
    
        $sql = "SELECT * FROM `feed_bagcapacity` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bags_code[$row['code']] = $row['code']; $bags_size[$row['code']] = $row['bag_size']; }

        $sql = "SELECT * FROM `broiler_vendor_mastergroup` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $vmg_code = $vmg_name = array();
        while($row = mysqli_fetch_assoc($query)){ $vmg_code[$row['code']] = $row['code']; $vmg_name[$row['code']] = $row['description']; }
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
        <?php
        $trnum = $_GET['trnum'];
        $sql = "SELECT * FROM `broiler_sales` WHERE `trnum` = '$trnum' AND `sale_type` = 'FeedSingleSale'"; $query = mysqli_query($conn,$sql); $i = 0;
        while($row = mysqli_fetch_assoc($query)){
            $date = $row['date'];
            $vcode = $row['vcode'];
            $billno = $row['billno'];
            $icode[$i] = $row['icode'];
            $rcd_qty[$i] = $row['rcd_qty'];
            $fre_qty[$i] = $row['fre_qty'];
            $mrp_amt[$i] = $row['mrp_amt'];
            $dis_per[$i] = $row['dis_per'];
            $nof_bags[$i] = $row['nof_bags'];
            $avg_final_amount = $row['avg_final_amount'];
            $disc_price[$i] = $row['disc_price'];
            $farm_mnu_name[$i] = $row['farm_mnu_name'];
            $batch_mnu_name[$i] = $row['batch_mnu_name'];
            $vg_code = $row['vmg_code'];
            $rate[$i] = $row['rate'];
            $profit = $row['profit'];
            $gst_ecode[$i] = $row['gst_code'];
            $gst_eper[$i] = round($row['gst_per'],5);
            $gst_eamt[$i] = round($row['gst_amt'],5);
            $item_tamt[$i] = $row['item_tamt'];
            $remarks = $row['remarks'];
            $warehouse = $row['warehouse'];
            $vehicle_code = $row['vehicle_code'];
            $driver_code = $row['driver_code'];
            $sale_type = $row['sale_type'];
            $freight_type = $row['freight_type'];
            $freight_amt = $row['freight_amt'];
            $freight_pay_type = $row['freight_pay_type'];
            $freight_pay_acc = $row['freight_pay_acc'];
            $freight_acc = $row['freight_acc'];
            $tcds_type = $row['tcds_type'];
            $tcds_amt = round($row['tcds_amt'],2);
            $finl_amt = round($row['finl_amt'],2);
            $round_off = $row['round_off'];
            $remarks = $row['remarks'];
            $batch_no = $row['batch_no'];
            $exp_date = $row['exp_date'];


            if(!empty($icode[$i]) && !empty($rcd_qty[$i])){
                $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$icode[$i]' AND `active` = '1' AND `dflag` = '0' OR `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
                if($ibag_flag1 > 0 && $bag_access_flag > 0){
                    while($brow = mysqli_fetch_assoc($bquery)){
                        if($ibag_flag1 > 1){
                            if($brow['code'] != "all"){
                                $rcd_qty[$i] = $rcd_qty[$i] / $brow['bag_size'];
                                $fre_qty[$i] = $fre_qty[$i] / $brow['bag_size'];
                                $rate[$i] = $rate[$i] * $brow['bag_size'];
                            }
                        }
                        else{
                            $rcd_qty[$i] = $rcd_qty[$i] / $brow['bag_size'];
                            $fre_qty[$i] = $fre_qty[$i] / $brow['bag_size'];
                            $rate[$i] = $rate[$i] * $brow['bag_size'];
                        }
                    }
                }
            }

            $i++;
        }
        $i = $i - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Feed Sales</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_modify_feedsale4.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo date('d.m.Y',strtotime($date)); ?>" style="width:80px;">
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" value="<?php echo $billno; ?>" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>" <?php if($vcode == $sup_code){ echo "selected"; } ?>><?php echo $ven_name[$sup_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock Point / Feed Mill<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($warehouse == $whouse_code){ echo "selected"; } ?>><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle</label>
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" value="<?php echo $vehicle_code; ?>" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" value="<?php echo $driver_code; ?>" style="width:120px;" onkeyup="validatename(this.id);" />
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>Customer Group<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vmg_code" id="vmg_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($vmg_code as $mcode){ ?><option value="<?php echo $mcode; ?>" <?php if($mcode == $vg_code){ echo "selected"; } ?>><?php echo $vmg_name[$mcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Item<b style="color:red;">&nbsp;*</b></th>
                                                <th>No. of Bags<b style="color:red;">&nbsp;*</b></th>
                                                <?php if($bag_access_flag == 1) { ?>
                                                    <th>Qty (In Bags)</th>
                                                <?php } else{ ?>
                                                    <th>Bags (In Kgs)</th>
                                                <?php } ?>
                                                
                                                <th>MRP<b style="color:red;">&nbsp;*</b></th>
                                                <th title="Discount Per">Disc. %</th>
                                                <th title="Discount Per">Disc. &#8377</th>
                                                <th>Final Rate</th>
                                                <!-- <th>GST</th> -->
                                                <th>Amount</th>
                                                <th></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th>Farm</th>
                                                <th>Batch</th>
                                                <th style="visibility:hidden;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <?php for($c = 0;$c <= $i;$c++){ ?>
                                            <tr id="row_no[<?php echo $c; ?>]">
                                                <td><select name="icode[]" id="icode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_reset(this.id);"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($icode[$c] == $prod_code){ echo "selected"; } ?>><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="fre_qty[]" id="fre_qty[<?php echo $c; ?>]" class="form-control" value="<?php echo $nof_bags[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_kgs(this.id);fetch_discount_amount2(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="rcd_qty[]" id="rcd_qty[<?php echo $c; ?>]" class="form-control" value="<?php echo $rcd_qty[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" readonly></td>
                                                <td><input type="text" name="mrp_amt[]" id="mrp_amt[<?php echo $c; ?>]" class="form-control" value="<?php echo $mrp_amt[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount2(this.id);fetch_prc(this.id);" onchange="validateamount(this.id)"; ></td>
                                                <td><input type="text" name="dis_per[]" id="dis_per[<?php echo $c; ?>]" class="form-control" value="<?php echo $dis_per[$c]; ?>" placeholder="%" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);fetch_discount_amount2(this.id);calculate_total_amt(this.id);"></td>
                                                <td><input type="text" name="dis_amt[]" id="dis_amt[<?php echo $c; ?>]" class="form-control" value="<?php echo $disc_price[$c]; ?>" placeholder="&#8377" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);calculate_total_amt(this.id);fetch_discount_amount2(this.id);" onchange="validateamount(this.id);"></td>
                                                <td><input type="text" name="rate[]" id="rate[<?php echo $c; ?>]" class="form-control" value="<?php echo $rate[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum5(this.id);calculate_total_amt(this.id);" onchange="validateamount5(this.id);" ></td>
                                                <td><input type="text" name="item_tamt[]" id="item_tamt[<?php echo $c; ?>]" class="form-control" value="<?php echo $item_tamt[$c]; ?>" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td id="action[<?php echo $c; ?>]" <?php if($c == $i){ echo 'style="padding-top: 5px;"'; } else{ echo 'style="padding-top: 5px;visibility:hidden;"'; } ?> >
                                                    <?php if($c == 0){ echo '<br/>'; } ?>
                                                    <a href="javascript:void(0);" id="addrow[<?php echo $c; ?>]" onclick="create_row(this.id)" >
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                        &ensp;
                                                    <a href="javascript:void(0);" id="deductrow[<?php echo $c; ?>]" onclick="destroy_row(this.id);" <?php if($c == $i){ } else{ echo 'style="display:none;"'; } ?> >
                                                        <i class="fa fa-minus" style="color:red;"></i>
                                                    </a>
                                                </td>
                                                <td style="visibility:visible;"><input type="text" name="avg_price[]" id="avg_price[<?php echo $c; ?>]" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>
                                                <td style="visibility:visible;"><input type="text" name="avg_amount[]" id="avg_amount[<?php echo $c; ?>]" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>
                                                <td><input type="text" name="farm_mnu_name[]" id="farm_mnu_name[<?php echo $c; ?>]" class="form-control" value="<?php echo $farm_mnu_name[$c]; ?>" style="width:80px;" /></td>
                                                <td><input type="text" name="batch_mnu_name[]" id="batch_mnu_name[<?php echo $c; ?>]" class="form-control" value="<?php echo $batch_mnu_name[$c]; ?>" style="width:80px;" /></td>
                                                <td style="visibility:hidden;"><input type="text" name="gst_amt[]" id="gst_amt[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $gst_eamt[$c]; ?>" style="width:20px;" ></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table><br/>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Type</label>
                                                    <select name="freight_type" id="freight_type" class="form-control select2" onchange="calculate_final_total_amount()">
                                                        <option value="select">select</option>
                                                        <option value="inbill"  <?php if($freight_type == "inbill"){ echo "selected"; } ?>>Paid by Customer</option>
                                                        <option value="exclude" <?php if($freight_type == "exclude"){ echo "selected"; } ?>>Paid by Company</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay Later</label>
                                                    <input type="radio" name="pay_type" id="pay_type1" class="form-control" value="PayLater" style="width:90px;transform: scale(.6);" onclick="fetch_freight_coa_account(this.id)" <?php if($freight_pay_type == "PayLater"){ echo "checked"; } ?> />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay In Bill</label>
                                                    <input type="radio" name="pay_type" id="pay_type2" class="form-control" value="PayInBill" style="width:90px;transform: scale(.6);" onclick="fetch_freight_coa_account(this.id)" <?php if($freight_pay_type == "PayInBill"){ echo "checked"; } ?> />
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
                                                        ?><option value="<?php echo $row['code']; ?>" <?php if($freight_pay_acc == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
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
                                                        ?><option value="<?php echo $row['code']; ?>" <?php if($freight_acc == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Amount</label>
                                                    <input type="text" name="freight_amount" id="freight_amount" class="form-control" value="<?php echo $freight_amt; ?>" placeholder="0.00" onkeyup="validatenum(this.id);calculate_final_total_amount();" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                        </div><br/>
                                        <div class="row">
                                            <div class="col-md-6"></div>
                                            <div class="col-md-4">
                                            <table class="table">
                                                <tr>
                                                    <th>TCS Type</th>
                                                    <th>TCS Amount</th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <input type="radio" name="tcds_type" id="tcds_type1" value="add" onclick="calculate_final_total_amount();" <?php if($tcds_type == "add"){ echo "checked"; } ?> /><label for="tcds_type1">Add</label>&ensp;
                                                        <input type="radio" name="tcds_type" id="tcds_type2" value="deduct" onclick="calculate_final_total_amount();" <?php if($tcds_type == "deduct"){ echo "checked"; } ?> /><label for="tcds_type2">Deduct</label>
                                                    </th>
                                                    <th><input type="text" name="tcds_amt" id="tcds_amt" class="form-control" value="<?php echo $tcds_amt; ?>" style="width:210px;" onkeyup="validatenum(this.id);calculate_final_total_amount();" onchange="validateamount(this.id)" /></th>
                                                </tr>
                                            </table>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                        <div class="row"><br/><br/>
                                            <div class="col-md-2 form-group">
                                                <label>Final Amount</label>
                                                <input type="text" name="final_total" id="final_total" class="form-control" value="<?php echo $finl_amt; ?>" placeholder="0.00" >
                                            </div>
                                            <div class="col-md-2 form-group" style="visibility:visible;">
                                                <label>Avg. Amount</label>
                                                <input type="text" name="final_avg_total" id="final_avg_total" class="form-control" value="<?php echo $avg_final_amount; ?>" placeholder="0.00" >
                                            </div>
                                            <div class="col-md-2 form-group" style="visibility:visible;">
                                                <label>Profit Amount</label>
                                                <input type="text" name="profit_amount" id="profit_amount" class="form-control" value="<?php echo $profit; ?>" placeholder="0.00" >
                                            </div>
                                            <div class="col-md-2 form-group" style="visibility:visible;">
                                                <label>Round Off</label>
                                                <input type="text" name="round_off" id="round_off" class="form-control" value="<?php echo $round_off; ?>" readonly />
                                            </div>
                                        </div>
                                    </div><br/><br/>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Batch No.</label>
                                                    <input type="text" name="batch_no" id="batch_no" class="form-control"
                                                        value="<?php echo $batch_no; ?>" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Expiry Date</label>
                                                    <input type="text" name="exp_date" id="exp_date" class="form-control datepicker" value="<?php echo date('d.m.Y', strtotime($exp_date)); ?>" style="width:100px;" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"><?php echo $remarks; ?></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $i; ?>">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $trnum; ?>">
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
                window.location.href = 'broiler_display_feedsale4.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value; var qty = price = total_amt = avg_amount = c = d = stock = gst_per = gst_amt = 0; var icode = gst_val = "";
                var l = true;
               
                var date = document.getElementById("date").value;
                var vcode = document.getElementById("vcode").value;
                var warehouse = document.getElementById("warehouse").value;
                if(date == ""){
                    alert("Kindly enter/select appropriate date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(vcode.match("select")){
                    alert("Kindly select appropriate Customer");
                    document.getElementById("vcode").focus();
                    l = false;
                }
                else if(warehouse.match("select")){
                    alert("Kindly select appropriate Warehouse");
                    document.getElementById("warehouse").focus();
                    l = false;
                }
                else{
                    
                    //Check Item Details
                    for(d = 0;d <= incrs;d++){
                        if(l == true){
                            c = d + 1;
                            icode = document.getElementById("icode["+d+"]").value;
                            fre_qty = document.getElementById("fre_qty["+d+"]").value;
                            mrp_amt = document.getElementById("mrp_amt["+d+"]").value; if(mrp_amt == ""){ mrp_amt = 0; }
                            qty = document.getElementById("rcd_qty["+d+"]").value; 
                            price = document.getElementById("rate["+d+"]").value;
                            if(icode.match("select")){
                                alert("Kindly select appropriate Item in row: "+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(mrp_amt == "" || mrp_amt == "0.00" || mrp_amt == 0){
                                alert("Kindly enter MRP in row: "+c);
                                document.getElementById("mrp_amt["+d+"]").focus();
                                l = false;
                            }
                            else if(fre_qty == "" || fre_qty == "0.00" || fre_qty == 0){
                                alert("Kindly enter No. Of Bags in row: "+c);
                                document.getElementById("fre_qty["+d+"]").focus();
                                l = false;
                            } 
                            else if(price == "" || price == "0.00" || price == 0){
                                alert("Kindly enter Rate in row: "+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                        }
                    }

                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                    if(stockcheck_flag == 1){
                        for(d = 0;d <= incrs;d++){
                            if(l == true){
                                c = d + 1;
                                qty = document.getElementById("rcd_qty["+d+"]").value;
                                stock = document.getElementById("available_stock["+d+"]").value;
                                if(parseFloat(qty) > parseFloat(stock)){
                                    alert("Stock not Available in row: "+c);
                                    document.getElementById("rcd_qty["+d+"]").focus();
                                    l = false;
                                }
                            }
                        }
                    }
                    else{ }
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
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_reset(this.id);"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="fre_qty[]" id="fre_qty['+d+']" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_kgs(this.id);fetch_discount_amount2(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="mrp_amt[]" id="mrp_amt['+d+']" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_prc(this.id);" onchange="validateamount(this.id)"; ></td>';
                html += '<td><input type="text" name="dis_per[]" id="dis_per['+d+']" class="form-control" placeholder="%" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);fetch_discount_amount2(this.id);calculate_total_amt(this.id);"></td>';
                html += '<td><input type="text" name="dis_amt[]" id="dis_amt['+d+']" class="form-control" placeholder="&#8377" style="width:80px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);fetch_discount_amount2(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);"></td>';
                html += '<td><input type="text" name="rate[]" id="rate['+d+']" class="form-control" placeholder="0.00" style="width:90px;" onkeyup="validatenum5(this.id);calculate_total_amt(this.id);" onchange="validateamount5(this.id);" ></td>';
                html += '<td><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:visible;"><input type="text" name="avg_price[]" id="avg_price['+d+']" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>';
                html += '<td style="visibility:visible;"><input type="text" name="avg_amount[]" id="avg_amount['+d+']" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>';
                html += '<td><input type="text" name="farm_mnu_name[]" id="farm_mnu_name['+d+']" class="form-control" style="width:80px;" /></td>';
                html += '<td><input type="text" name="batch_mnu_name[]" id="batch_mnu_name['+d+']" class="form-control" style="width:80px;" ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="gst_amt[]" id="gst_amt['+d+']" class="form-control text-right" style="width:20px;" ></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_total_amount();
            }
            function calculate_total_amt2(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty = document.getElementById("rcd_qty["+d+"]").value;
                var price = document.getElementById("rate["+d+"]").value;
                var avg_price = document.getElementById("avg_price["+d+"]").value;
                var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                var mrp_amt = document.getElementById("mrp_amt["+d+"]").value;

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                if(avg_price == "" || avg_price.length == 0 || avg_price == "0.00" || avg_price == "0"){ avg_price = 0; }
                if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ dis_amt = 0; }
                if(mrp_amt == "" || mrp_amt.length == 0 || mrp_amt == "0.00" || mrp_amt == "0"){ mrp_amt = 0; }
                
                var avg_amount = parseFloat(qty) * parseFloat(avg_price);

                if(dis_amt > 0){
                    price = parseFloat(mrp_amt) - parseFloat(dis_amt);
                } else if(dis_amt == 0 || dis_amt == ""){
                    var total_amt = parseFloat(qty) * parseFloat(price);
                }
                // document.getElementById("gst_amt["+d+"]").value = gst_amt.toFixed(2);
                document.getElementById("rate["+d+"]").value = price.toFixed(2);
                
                var total_amt = parseFloat(qty) * parseFloat(price);
                document.getElementById("item_tamt["+d+"]").value = total_amt.toFixed(2);
                document.getElementById("avg_amount["+d+"]").value = avg_amount.toFixed(2);
                calculate_total_amt();
                // calculate_final_total_amount();
            }
            function fetch_discount_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty = document.getElementById("rcd_qty["+d+"]").value; 
                var price = document.getElementById("rate["+d+"]").value;

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }

                var total_amt = parseFloat(qty) * parseFloat(price);

                if(total_amt == "" || total_amt.length == 0 || total_amt == "0.00" || total_amt == "0"){ total_amt = 0; }
                if(b[0].match("dis_per")){
                    var dis_per = document.getElementById("dis_per["+d+"]").value;
                    if(dis_per == "" || dis_per.length == 0 || dis_per == "0.00" || dis_per == "0"){ 
                        var total_amt = parseFloat(qty) * parseFloat(price);
                        document.getElementById("item_tamt["+d+"]").value = total_amt;
                     }
                    else{
                        var dis_value = ((parseFloat(dis_per) / 100) * total_amt);
                        if(dis_value == "NaN" || dis_value.length == 0 || dis_value == 0){ dis_value = ""; }
                        document.getElementById("dis_amt["+d+"]").value = dis_value;
                        calculate_total_amt(a);
                    }
                    
                }
                else{
                    var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                    if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){
                        var total_amt = parseFloat(qty) * parseFloat(price);
                        document.getElementById("item_tamt["+d+"]").value = total_amt;
                     }
                    else{
                        var dis_per = ((parseFloat(dis_amt) * 100) / total_amt);
                        //var dis_per = (((parseFloat(dis_amt) * 100) / total_amt) * 100);
                        if(dis_per == "NaN" || dis_per.length == 0 || dis_per == 0){ dis_per = ""; }
                        document.getElementById("dis_per["+d+"]").value = dis_per.toFixed(2);
                        calculate_total_amt(a);
                    }
                }
            }
            function fetch_discount_amount2(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty = document.getElementById("rcd_qty["+d+"]").value; 
                var mrp_amt = document.getElementById("mrp_amt["+d+"]").value; 
                
                var price = document.getElementById("rate["+d+"]").value;

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }

               // var total_amt = parseFloat(qty) * parseFloat(price);

                //if(total_amt == "" || total_amt.length == 0 || total_amt == "0.00" || total_amt == "0"){ total_amt = 0; }
                if(mrp_amt == "" || mrp_amt.length == 0 || mrp_amt == "0.00" || mrp_amt == "0"){ mrp_amt = 0; }
                if(b[0].match("dis_per")){
                    var dis_per = document.getElementById("dis_per["+d+"]").value;
                    if(dis_per == "" || dis_per.length == 0 || dis_per == "0.00" || dis_per == "0"){ 
                        var total_amt = parseFloat(qty) * parseFloat(price);
                        document.getElementById("item_tamt["+d+"]").value = total_amt;
                        // document.getElementById("dis_amt["+d+"]").value = 0;
                     }
                    else{
                        var dis_value = ((parseFloat(dis_per) / 100) * mrp_amt);
                        if(dis_value == "NaN" || dis_value.length == 0 || dis_value == 0){ dis_value = ""; }
                        document.getElementById("dis_amt["+d+"]").value = dis_value;
                        calculate_total_amt2(a);
                    }
                    
                }
                else{
                    var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                    if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){
                        var total_amt = parseFloat(qty) * parseFloat(price);
                        document.getElementById("item_tamt["+d+"]").value = total_amt;
                        document.getElementById("dis_per["+d+"]").value = 0;
                     }
                    else{
                        var dis_per = ((parseFloat(dis_amt) * 100) / mrp_amt);
                        //var dis_per = (((parseFloat(dis_amt) * 100) / total_amt) * 100);
                        if(dis_per == "NaN" || dis_per.length == 0 || dis_per == 0){ dis_per = ""; }
                        document.getElementById("dis_per["+d+"]").value = dis_per.toFixed(2);
                        calculate_total_amt2(a);
                    }
                }
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty = document.getElementById("rcd_qty["+d+"]").value;
                var price = document.getElementById("rate["+d+"]").value;
                var avg_price = document.getElementById("avg_price["+d+"]").value;
                var dis_amt = document.getElementById("dis_amt["+d+"]").value;

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                if(avg_price == "" || avg_price.length == 0 || avg_price == "0.00" || avg_price == "0"){ avg_price = 0; }
                if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ dis_amt = 0; }
                var total_amt = parseFloat(qty) * parseFloat(price);
                var avg_amount = parseFloat(qty) * parseFloat(avg_price);

                if(dis_amt > 0){
                    total_amt = parseFloat(total_amt) - parseFloat(dis_amt);
                } else if(dis_amt == 0 || dis_amt == ""){
                    var total_amt = parseFloat(qty) * parseFloat(price);
                }
                // document.getElementById("gst_amt["+d+"]").value = gst_amt.toFixed(2);
                document.getElementById("item_tamt["+d+"]").value = total_amt.toFixed(2);
                document.getElementById("avg_amount["+d+"]").value = avg_amount.toFixed(2);
                calculate_final_total_amount();
            }
            function calculate_final_total_amount(){
                var incr = document.getElementById("incr").value; var i = amount = avg_amount = final_amount = final_avg_amount = profit_amount = 0;
                for(i = 0;i <= incr;i++){
                    amount = document.getElementById("item_tamt["+i+"]").value;
                    final_amount = parseFloat(final_amount) + parseFloat(amount);

                    avg_amount = document.getElementById("avg_amount["+i+"]").value;
                    final_avg_amount = parseFloat(final_avg_amount) + parseFloat(avg_amount);
                }
                var freight_amount = document.getElementById("freight_amount").value;
                var freight_type = document.getElementById("freight_type").value;
                if(freight_amount == "" || freight_amount.length == 0 || freight_amount == "0.00" || freight_amount == "0"){ freight_amount = 0; }
                if(freight_type == "inbill"){
                    //final_amount = parseFloat(final_amount) + parseFloat(freight_amount);
                    final_amount = parseFloat(final_amount);
                    profit_amount = parseFloat(final_amount) - parseFloat(final_avg_amount);
                }
                else if(freight_type == "include"){
                    final_amount = parseFloat(final_amount) - parseFloat(freight_amount);
                    profit_amount = parseFloat(final_amount) - parseFloat(freight_amount);
                }
                else if(freight_type == "exclude"){
                    final_amount = parseFloat(final_amount);
                    profit_amount = parseFloat(final_amount) - parseFloat(final_avg_amount) - parseFloat(freight_amount);
                }
                else{
                    final_amount = parseFloat(final_amount);
                    profit_amount = parseFloat(final_amount) - parseFloat(final_avg_amount);
                }

                var tcds_type1 = document.getElementById("tcds_type1");
                var tcds_type2 = document.getElementById("tcds_type2");
                var tcds_amt = document.getElementById("tcds_amt").value; if(tcds_amt == ""){ tcds_amt = 0; }
                if(tcds_type1.checked == true){
                    final_amount = parseFloat(final_amount) + parseFloat(tcds_amt);
                }
                else if(tcds_type2.checked == true){
                    final_amount = parseFloat(final_amount) - parseFloat(tcds_amt);
                }
                else{
                    final_amount = parseFloat(final_amount) + parseFloat(tcds_amt);
                }
                var round_off = 0;
                var tmp1 = parseFloat(final_amount).toFixed(0);
                var round_off = parseFloat(tmp1) - parseFloat(final_amount);
                
                document.getElementById("round_off").value = round_off.toFixed(3);
                document.getElementById("final_total").value = final_amount.toFixed(0);
                document.getElementById("final_avg_total").value = final_avg_amount.toFixed(0);
                document.getElementById("profit_amount").value = profit_amount.toFixed(0);
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
            function fetch_stock_master(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date").value;
                var sector = document.getElementById("warehouse").value;
                var item_code = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&trtype=Sale"+"&etype=sale";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        if(item_price.length > 0){
                            var item_details = item_price.split("@");
                            document.getElementById("available_stock["+d+"]").value = item_details[0];
                            var autoavgprice_flag = '<?php echo $autoavgprice_flag; ?>';
                            if(autoavgprice_flag == 1){
                                document.getElementById("rate["+d+"]").value = item_details[1];
                            }
                            document.getElementById("avg_price["+d+"]").value = item_details[1];
                        }
                        else{
                            alert("Item Stock not available, Kindly check before saving ...!");
                            document.getElementById("available_stock["+d+"]").value = 0;
                            document.getElementById("rate["+d+"]").value = 0;
                            document.getElementById("avg_price["+d+"]").value = 0;
                        }
                    }
                }
            }
            function fetch_multiple_item_stock_master(){
                var incr = document.getElementById("incr").value;
                var date = document.getElementById("date").value;
                var sector = document.getElementById("warehouse").value;
                var item_code = fetch_items = method = url = asynchronous = item_price = item_details = ""; var autoavgprice_flag = rcd_qty = avg_amount = 0;
                var trnum = '<?php echo $trnum; ?>';
                for(var d = 0;d <= incr;d++){
                    item_code = document.getElementById("icode["+d+"]").value;
                    fetch_items = new XMLHttpRequest();
                    method = "GET";
                    url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&id="+trnum+"&row_count="+d+"&trtype=Sale"+"&etype=sale";
                    //window.open(url);
                    asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            item_price = this.responseText;
                            if(item_price.length > 0){
                                item_details = item_price.split("@");
                                document.getElementById("available_stock["+item_details[3]+"]").value = item_details[0];
                                document.getElementById("avg_price["+item_details[3]+"]").value = item_details[1];
                                rcd_qty = document.getElementById("rcd_qty["+item_details[3]+"]").value;
                                avg_amount = parseFloat(rcd_qty) * parseFloat(item_details[1]);
                                document.getElementById("avg_amount["+item_details[3]+"]").value = parseFloat(avg_amount).toFixed(2);
                                calculate_final_total_amount();
                            }
                            else{
                                alert("Item Stock not available, Kindly check before saving ...!");
                                document.getElementById("available_stock["+d+"]").value = 0;
                                document.getElementById("avg_price["+d+"]").value = 0;
                                document.getElementById("avg_amount["+d+"]").value = 0;
                            }
                        }
                    }
                }
            }
            fetch_multiple_item_stock_master(); 
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function validatenum5(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount5(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(5); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
        </script>
         <script>
                // Generate JavaScript array from PHP for bag sizes
                var bagsSize = <?php echo json_encode($bags_size); ?>;

                function fetch_reset(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("fre_qty[" + d + "]").value = 0;
                    document.getElementById("rcd_qty[" + d + "]").value = 0;

                }

                function fetch_kgs(a) {
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icd = document.getElementById("icode[" + d + "]").value;
                    var rd_qty = bagsSize[icd];
                    var fre_qty = document.getElementById("fre_qty[" + d + "]").value;

                    // Update rcd_qty field based on user input
                    if (rd_qty) {
                        if (fre_qty == 1) {
                            // If user enters 1, directly use rd_qty
                            document.getElementById("rcd_qty[" + d + "]").value = rd_qty;
                        } else if (!isNaN(fre_qty) && fre_qty > 0) {
                            // If user enters a valid number greater than 1, multiply by rd_qty
                            document.getElementById("rcd_qty[" + d + "]").value = rd_qty * fre_qty;
                        } else {
                            alert("Please enter a valid number in 'fre_qty'.");
                        }
                    } else {
                    alert("Bag size not found for the selected item code.");
                    }
                }
                function fetch_prc(a) {
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var mrp_amt = document.getElementById("mrp_amt[" + d + "]").value;
                    document.getElementById("rate[" + d + "]").value = mrp_amt;
                }
        </script>
        <script>
            //Date Range selection
            var s_date = '<?php echo date('d.m.Y', strtotime('-'.$s_days.' days', strtotime($rdate))); ?>';
            var e_date = '<?php echo date('d.m.Y', strtotime('+'.$e_days.' days', strtotime($rdate))); ?>';
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