<?php
//broiler_edit_purchase2_chicks.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['purchase2_chicks'];
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
        $today = date("Y-m-d");
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
				
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `description` like '%chicks%'   ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'  ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $farm_list = "";
        while($row = mysqli_fetch_assoc($query)){$farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description']; if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; } }
        
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$today' AND `tdate` >= '$today' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
		$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tdsper = $row['tcds']; }
				
		$sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
				
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
				
		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bag%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bcodes = "";
        while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; $bag_name[$row['code']] = $row['description']; }

        //Fetch Feed Details and Feed in Bags Flag
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $item_feed_code[$row['code']] = $row['code']; $item_feed_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Purchases' AND `field_function` LIKE 'Bags' AND `flag` = 1"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }
        if($qty_on_sqty_flag == ""){ $qty_on_sqty_flag = 0; }
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Purchase TDS' AND `field_function` LIKE 'after 50L TDS Auto' AND `flag` = '1' AND (`user_access` LIKE '%$user_code%' || `user_access` LIKE 'all');";
        $query = mysqli_query($conn,$sql); $auto_tds_flag = mysqli_num_rows($query);
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Chick Purchase' AND `field_function` LIKE 'Supplier Hatchery Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $vhs_flag = mysqli_num_rows($query);

        $onchange_filter = "";
        if((int)$auto_tds_flag == 1){ $onchange_filter .= "broiler_fetch_Supplierpurchases();"; }
        if((int)$vhs_flag == 1){ $onchange_filter .= "broiler_fetch_Supplierhatcheries();"; }
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
        $id = $_GET['trnum']; $pcount = 0;
        $sql = "SELECT * FROM `broiler_purchases` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $incr[$pcount] = $row['incr'];
            $prefix[$pcount] = $row['prefix'];
            $trnum[$pcount] = $row['trnum'];
            $date[$pcount] = $row['date'];
            $vcode[$pcount] = $row['vcode'];
            $ven_hat_code = $row['ven_hat_code'];
            $billno[$pcount] = $row['billno'];
            $icode = $row['icode'];
            $mortality[$pcount] = $row['mort'];
            $shortage[$pcount] = $row['shortage'];
            $weeks[$pcount] = $row['weeks'];
            $excess_qty[$pcount] = $row['excess_qty'];
            $rate[$pcount] = $row['item_tamt'] / $row['rcd_qty'];
            $snt_qty[$pcount] = $row['snt_qty'];
            $fre_qper[$pcount] = $row['fre_qper'];
            $rcd_qty[$pcount] = $row['rcd_qty'];
            $fre_qty[$pcount] = $row['fre_qty'];
            $rate[$pcount] = $row['rate'];
            $dis_per[$pcount] = $row['dis_per'];
            $dis_amt[$pcount] = $row['dis_amt'];
            $edit_gst_code[$pcount] = $row['gst_code'];
            $gst_per[$pcount] = $row['gst_per'];
            $gst_amt[$pcount] = $row['gst_amt'];
            $tcds_per[$pcount] = $row['tcds_per'];
            $tcds_amt[$pcount] = $row['tcds_amt'];
            $item_tamt[$pcount] = $row['item_tamt'];
            $freight_type[$pcount] = $row['freight_type'];
            $freight_amt[$pcount] = $row['freight_amt'];
            $freight_pay_type[$pcount] = $row['freight_pay_type'];
            $freight_pay_acc[$pcount] = $row['freight_pay_acc'];
            $freight_acc[$pcount] = $row['freight_acc'];
            $round_off[$pcount] = $row['round_off'];
            $finl_amt[$pcount] = $row['finl_amt'];
            $bal_qty[$pcount] = $row['bal_qty'];
            $bal_amt[$pcount] = $row['bal_amt'];
            $remarks[$pcount] = $row['remarks'];
            $warehouse[$pcount] = $row['warehouse'];
            $farm_batch[$pcount] = $row['farm_batch'];
            $bagcode[$pcount] = $row['bag_code'];
            $bag_count[$pcount] = $row['bag_count'];
            $batch_no[$pcount] = $row['batch_no'];
            $exp_date[$pcount] = $row['exp_date'];
            $vehicle_code[$pcount] = $row['vehicle_code'];
            $dcode[$pcount] = $row['driver_code'];
            $addedemp[$pcount] = $row['addedemp'];
            $addedtime[$pcount] = $row['addedtime'];
            $updatedemp[$pcount] = $row['updatedemp'];
            $updatedtime[$pcount] = $row['updatedtime'];

            $batch = explode("-",$farms_batch[$warehouse[$pcount]]);
            $rename_batch[$pcount] = $batch[1]."-".$batch[2];

            $pcount++;
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Purchases</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_modify_purchase2_chicks.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date[0])); ?>" style="width:100px;" <?php if($auto_tds_flag == "1"){ echo 'onchange="broiler_fetch_Supplierpurchases();"'; } ?>>
                                        </div>
                                        <div class="form-group">
                                            <label>Supplier<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:180px;" <?php if($auto_tds_flag == "1"){ echo 'onchange="broiler_fetch_Supplierpurchases();"'; } ?>>
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>" <?php if($vcode[0] == $sup_code){ echo "selected"; } ?>><?php echo $ven_name[$sup_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php if((int)$vhs_flag == 1){ ?>
                                        <div class="form-group">
                                            <label>Hatchery Name<b style="color:red;">&nbsp;*</b></label>
                                            <select name="ven_hat_code" id="ven_hat_code" class="form-control select2" style="width:180px;">
                                                <option value="select">select</option>
                                                <?php
                                                $sql = "SELECT * FROM `broiler_link_vendor_hatcheries` WHERE `vendor_code` IN ('$vcode[0]') AND `active` = '1' AND `dflag` = '0'";
                                                $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $ha_codes = array();
                                                if($count > 0){
                                                    while($row = mysqli_fetch_assoc($query)){ $ha_codes[$row['hatchery_code']] = $row['hatchery_code']; }
                                                
                                                    $hat_list = implode("','",$ha_codes);
                                                    $sql = "SELECT * FROM `broiler_vendor_hatcheries` WHERE `code` IN ('$hat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                                    if($count > 0){
                                                        while($row = mysqli_fetch_assoc($query)){
                                                            $code = $row['code']; $name = $row['description'];
                                                        ?>
                                                        <option value="<?php echo $code; ?>" <?php if($code == $ven_hat_code){ echo "selected"; } ?>><?php echo $name; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" value="<?php echo $billno[0]; ?>" style="width:85px;" >
                                        </div>
                                        <div class="form-group">
                                            <label >Item<b style="color:red;">&nbsp;*</b></label>
                                            <select name="icode" id="icode" class="form-control select2" style="width:180px;">
                                                <option value="select">select</option>
                                                <?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($icode == $prod_code){ echo "selected"; } ?>><?php echo $item_name[$prod_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>trnum</label>
                                            <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum[0]; ?>" style="width:130px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle</label>
                                            <!--- <select name="vehicle_code" id="vehicle_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($vehicle_code as $truck_code){ ?><option value="<?php echo $truck_code; ?>" <?php if($vehicle_code[0] == $truck_code){ echo "selected"; } ?>><?php echo $vehicle_name[$truck_code]; ?></option><?php } ?>
                                            </select> --->
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" value="<?php echo $vehicle_code[0]; ?>" style="width:85px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <!--- <select name="driver_code" id="driver_code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($emp_code as $driver_code){ ?><option value="<?php echo $driver_code; ?>" <?php if($dcode[0] == $driver_code){ echo "selected"; } ?>><?php echo $emp_name[$driver_code]; ?></option><?php } ?>
                                            </select> --->
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" value="<?php echo $dcode[0]; ?>" style="width:85px;" >
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
                                    <?php
                                        for($i = 0;$i < $pcount;$i++){
                                    ?>
                                    <div class="row" style="margin-bottom:3px;" id="row_no[<?php echo $i; ?>]">
                                       
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Billed Qty<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="snt_qty[]" id="snt_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $snt_qty[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Free Qty %</label>
                                            <input type="text" name="fre_qper[]" id="fre_qper[<?php echo $i; ?>]" class="form-control" value="<?php echo $fre_qper[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Mortality</label>
                                            <input type="text" name="mortality[]" id="mortality[<?php echo $i; ?>]" class="form-control" value="<?php echo $mortality[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Shortage</label>
                                            <input type="text" name="shortage[]" id="shortage[<?php echo $i; ?>]" class="form-control" value="<?php echo $shortage[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Weeks</label>
                                            <input type="text" name="weeks[]" id="weeks[<?php echo $i; ?>]" class="form-control" value="<?php echo $weeks[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Excess Qty<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="excess_qty[]" id="excess_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $excess_qty[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Rcv Qty<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rcd_qty[]" id="rcd_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $rcd_qty[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Free Qty</label>
                                            <input type="text" name="fre_qty[]" id="fre_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $fre_qty[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Total Qty</label>
                                            <input type="text" name="total_qty[]" id="total_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $rcd_qty[$i] + $fre_qty[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id);" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Rate<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rate[]" id="rate[<?php echo $i; ?>]" class="form-control" value="<?php echo $rate[$i]; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Amount</label>
                                            <input type="text" name="item_tamt[]" id="item_tamt[<?php echo $i; ?>]" value="<?php echo round(($rcd_qty[$i] * $rate[$i]),2); ?>" class="form-control" placeholder="0.00" style="width:90px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Sector/Farm<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse[]" id="warehouse[<?php echo $i; ?>]" class="form-control select2" style="width:180px;" onchange="fetch_batch(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($warehouse[$i] == $whouse_code){ echo "selected"; } ?>><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                        </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Batch</label>
                                            <td><input readonly type="text" name="batch[]" id="batch[<?php echo $i; ?>]" value="<?php echo $rename_batch[$i]; ?>" class="form-control" style="width:120px;" /></td>
                                        </div>
                                        <div class="form-group" id="action[<?php echo $i; ?>]" <?php if($pcount == 1 || $i == $pcount - 1){ echo 'style="padding-top: 5px;"'; } else{ echo 'style="padding-top: 5px;visibility:hidden;"'; } ?> > <?php if($i == 0){ echo '<br/>'; } ?><a href="javascript:void(0);" id="addrow[<?php echo $i; ?>]" onclick="create_row(this.id)" ><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow[<?php echo $i; ?>]" onclick="destroy_row(this.id)" <?php if($pcount > 1 && $i > 0){ } else{ echo 'style="display:none;"'; } ?> ><i class="fa fa-minus" style="color:red;"></i></a></div>
                                        
                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?> title="Discount Percentage">Disc. %</label>
                                            <input type="text" name="dis_per[]" id="dis_per[<?php echo $i; ?>]" class="form-control" value="<?php echo $dis_per[$i]; ?>" placeholder="%" style="width:20px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);">
                                        </div>
                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?> title="Discount Amount">Disc. &#8377</label>
                                            <input type="text" name="dis_amt[]" id="dis_amt[<?php echo $i; ?>]" class="form-control" value="<?php echo $dis_amt[$i]; ?>" placeholder="&#8377" style="width:20px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);">
                                        </div>
                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>GST</label>
                                            <select name="gst_per[]" id="gst_per[<?php echo $i; ?>]" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:20px;">
                                                <option value="select">select</option>
                                                <?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>" <?php if($edit_gst_code[$i] == $gsts){ echo "selected"; } ?>><?php echo $gst_name[$gsts]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="col-md-18" id="row_body"></div><br/><br/>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Type</label>
                                                    <select name="freight_type" id="freight_type" class="form-control select2" onchange="calculate_netpay()">
                                                        <option value="select" <?php if($freight_type[0] == "select"){ echo "selected"; } ?>>select</option>
                                                        <option value="include" <?php if($freight_type[0] == "include"){ echo "selected"; } ?>>Include</option>
                                                        <option value="exclude" <?php if($freight_type[0] == "exclude"){ echo "selected"; } ?>>Exclude</option>
                                                        <option value="inbill" <?php if($freight_type[0] == "inbill"){ echo "selected"; } ?>>In Bill</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay Later</label>
                                                    <input type="radio" name="pay_type" id="pay_type1" class="form-control" value="PayLater" style="width:90px;transform: scale(.7);" onclick="fetch_freight_coa_account(this.id)" <?php if($freight_pay_type[0] == "PayLater"){ echo "checked"; } ?> />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>Pay In Bill</label>
                                                    <input type="radio" name="pay_type" id="pay_type2" class="form-control" value="PayInBill" style="width:90px;transform: scale(.7);" onclick="fetch_freight_coa_account(this.id)" <?php if($freight_pay_type[0] == "PayInBill"){ echo "checked"; } ?> />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pay Account</label>
                                                    <select name="freight_pay_acc" id="freight_pay_acc" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php
                                                        if($freight_pay_type == "PayLater"){
                                                            $sql = "SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                                                        }
                                                        else{
                                                            $sql="SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                                                        }
                                                        $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                        ?><option value="<?php echo $row['code']; ?>" <?php if($freight_pay_acc[0] == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
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
                                                        ?><option value="<?php echo $row['code']; ?>" selected><?php echo $row['description']; ?></option>
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
                                                    <input type="text" name="freight_amount" id="freight_amount" class="form-control" value="<?php echo $freight_amt[0]; ?>" placeholder="0.00" onkeyup="validatenum(this.id);calculate_netpay();" onchange="validateamount(this.id)" />
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
                                                        <option value="select" <?php if($bagcode[0] == "select"){ echo "selected"; } ?>>select</option>
                                                        <?php foreach($bag_code as $carrier){ ?><option value="<?php echo $carrier; ?>" <?php if($bagcode[0] == $carrier){ echo "selected"; } ?>><?php echo $bag_name[$carrier]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>No.of Bags</label>
                                                    <input type="text" name="bag_count" id="bag_count" class="form-control" value="<?php echo $bag_count[0]; ?>" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Batch No.</label>
                                                    <input type="text" name="batch_no" id="batch_no" value="<?php echo $batch_no[0]; ?>" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Expiry Date</label>
                                                    <input type="text" name="exp_date" id="exp_date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($exp_date[0])); ?>" style="width:100px;"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>TDS</label>
                                                    <input type="checkbox" name="tcds_per" id="tcds_per" class="form-control" value="<?php echo $tdsper; ?>" style="transform: scale(.7);" <?php if($tcds_amt[0] > 0){ echo "checked"; } ?> <?php if($auto_tds_flag == "1"){ echo 'onchange="manual_uncheck();"'; } else{ echo 'onchange="calculate_netpay();"'; } ?> />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>TDS Amount</label>
                                                    <input type="text" name="tcds_amount" id="tcds_amount" class="form-control" value="<?php echo $tcds_amt[0]; ?>" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Roundoff</label>
                                                    <input type="text" name="round_off" id="round_off" class="form-control" value="<?php echo $round_off[0]; ?>" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Net Amount</label>
                                                    <input type="text" name="finl_amt" id="finl_amt" class="form-control" value="<?php echo round($finl_amt[0],$decimal_no); ?>" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>">
                                        </div>
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $pcount - 1; ?>">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
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
                window.location.href = 'broiler_display_purchase2_chicks.php?ccid='+ccid;
            }
            function fetch_batch(a){

                var b = a.split("["); var c = b[1].split("]"); var d = c[0];

                document.getElementById("batch["+d+"]").value = "";
                var to_sector = document.getElementById("warehouse["+d+"]").value;

                <?php
                    foreach($farms as $fcode){
                    
                        echo "if(to_sector == '$fcode'){";
                ?>
                        //alert('<?php echo $farms_batch[$fcode]; ?>');
                        //GEt value from array
                        var batch = '<?php echo $farms_batch[$fcode]; ?>';
                        // Split using a space character
                        let arr = batch.split('-');
                        var rename_batch = arr[1] + '-' + arr[2]
                        document.getElementById("batch["+d+"]").value = rename_batch;

                <?php
                    echo "}";
                    }
                ?>


            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var vhs_flag = '<?php echo $vhs_flag; ?>'; var ven_hat_code = ""; if(parseInt(vhs_flag) == 1){ ven_hat_code = document.getElementById("ven_hat_code").value; }
                var sup_code = document.getElementById("vcode").value;
                var incrs = document.getElementById("incr").value;
                item_code = document.getElementById("icode").value;
                var item_code = warehouse = ""; var snt_qty = rcd_qty = rate = c = 0;
                var l = true;
                if(item_code.match("select")){
                    alert("Please select Item");
                    document.getElementById("icode").focus();
                    l = false;
                }
                else if(sup_code.match("select")){
                    alert("Please select Supplier");
                    document.getElementById("vcode").focus();
                    l = false;
                }
                else if(parseInt(vhs_flag) == 1 && (ven_hat_code == "" || ven_hat_code == "select")){
                    alert("Please select Hatchery Name");
                    document.getElementById("ven_hat_code").focus();
                    l = false;
                }
                else{
                    for(var d = 0;d <= incrs;d++){
                        if(l == true){
                            c = d + 1;
                            
                            snt_qty = document.getElementById("snt_qty["+d+"]").value;
                            rcd_qty = document.getElementById("rcd_qty["+d+"]").value;
                            rate = document.getElementById("rate["+d+"]").value;
                            warehouse = document.getElementById("warehouse["+d+"]").value;
                            if(item_code.match("select")){
                                alert("Please select Item in row:-"+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(snt_qty == "" || snt_qty.length == 0 || snt_qty == "0" || snt_qty == 0 || snt_qty == "0.00"){
                                alert("Please enter Billed Qty in row:-"+c);
                                document.getElementById("snt_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                                alert("Please enter Rcd Qty in row:-"+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(rate == "" || rate.length == 0 || rate == "0" || rate == 0 || rate == "0.00"){
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
                        }
                    }
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
                
                html += '<div class="row" id="row_no['+d+']">';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Billed Qty</label><input type="text" name="snt_qty[]" id="snt_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">free Qty %</label><input type="text" name="fre_qper[]" id="fre_qper['+d+']" class="form-control text-right" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Mortality</label><input type="text" name="mortality[]" id="mortality['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Shortage</label><input type="text" name="shortage[]" id="shortage['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Weeks</label><input type="text" name="weeks[]" id="weeks['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Excess Qty<b style="color:red;">&nbsp;*</b></label><input type="text" name="excess_qty[]" id="excess_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rcv Qty<b style="color:red;">&nbsp;*</b></label><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Free Qty</label><input type="text" name="fre_qty[]" id="fre_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Total Qty</label><input type="text" name="total_qty[]" id="total_qty['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" readonly /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rate<b style="color:red;">&nbsp;*</b></label><input type="text" name="rate[]" id="rate['+d+']" class="form-control" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Amount</label><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control" placeholder="0.00" style="width:90px;" readonly ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Sector/Farm<b style="color:red;">&nbsp;*</b></label><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_batch(this.id);"><option value="select">select</option><?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Batch</label><td><input readonly type="text" name="batch[]" id="batch['+d+']"  class="form-control" style="width:120px;" /></td></div>';
                //html += '<div class="form-group"><label class="labelrow" style="display:none;">Farm Batch</label><select name="farm_batch[]" id="farm_batch['+d+']" class="form-control select2" style="width:100px;"><option value="select">select</option></select></div>';
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '<div class="form-group" style="width:20px;visibility:hidden;"><label class="labelrow" style="display:none;">Disc. %</label><input type="text" name="dis_per[]" id="dis_per['+d+']" class="form-control" placeholder="%" style="width:20px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);"></div>';
                html += '<div class="form-group" style="width:20px;visibility:hidden;"><label class="labelrow" style="display:none;">Disc. &#8377</label><input type="text" name="dis_amt[]" id="dis_amt['+d+']" class="form-control" placeholder="&#8377" style="width:20px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);"></div>';
                html += '<div class="form-group" style="width:20px;visibility:hidden;"><label class="labelrow" style="display:none;">GST</label><select name="gst_per[]" id="gst_per['+d+']" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:20px;"><option value="select">select</option><?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?></select></div>';
                //html += '<div class="form-group" style="width:20px;visibility:hidden;"><label class="labelrow" style="display:none;">GST &#8377</label><input type="text" name="gst_amt[]'.'" id="gst_amt['+d+']'.'" class="form-control" placeholder="&#8377" style="width:20px;" readonly ></div>';
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
                var qty_on_sqty_flag = '<?php echo $qty_on_sqty_flag; ?>';
                var sent_qty = document.getElementById("snt_qty["+d+"]").value;
                var fre_qper = document.getElementById("fre_qper["+d+"]").value;
                var mortality_qty = document.getElementById("mortality["+d+"]").value;
                var shortage_qty = document.getElementById("shortage["+d+"]").value;
                var weeks_qty = document.getElementById("weeks["+d+"]").value;
                var excess_qty = document.getElementById("excess_qty["+d+"]").value;
                if(sent_qty == "" || sent_qty.length == 0 || sent_qty == "0.00" || sent_qty == "0"){ sent_qty = 0; }
                if(fre_qper == "" || fre_qper.length == 0 || fre_qper == "0.00" || fre_qper == "0"){ fre_qper = 0; }
                if(mortality_qty == "" || mortality_qty.length == 0 || mortality_qty == "0.00" || mortality_qty == "0"){ mortality_qty = 0; }
                if(shortage_qty == "" || shortage_qty.length == 0 || shortage_qty == "0.00" || shortage_qty == "0"){ shortage_qty = 0; }
                if(weeks_qty == "" || weeks_qty.length == 0 || weeks_qty == "0.00" || weeks_qty == "0"){ weeks_qty = 0; }
                if(excess_qty == "" || excess_qty.length == 0 || excess_qty == "0.00" || excess_qty == "0"){ excess_qty = 0; }
                var fre_qty = fqty_per = 0;
                if(parseFloat(fre_qper) > 0){
                    fqty_per = (parseFloat(fre_qper) / 100);
                    fre_qty = parseFloat((parseFloat(sent_qty) * parseFloat(fqty_per))).toFixed(0);
                    if(fre_qty == "" || fre_qty.length == 0 || fre_qty == "0.00" || fre_qty == "0"){ fre_qty = 0; }
                    document.getElementById("fre_qty["+d+"]").value = parseFloat(fre_qty).toFixed(2);
                }
                fre_qty = document.getElementById("fre_qty["+d+"]").value; if(fre_qty == ""){ fre_qty = 0; }
                
                var tot_minusqty_qty = parseFloat(mortality_qty) + parseFloat(shortage_qty) + parseFloat(weeks_qty);
                var tot_rec_qty = parseFloat(sent_qty) - parseFloat(tot_minusqty_qty) + parseFloat(excess_qty);
                if(tot_rec_qty == "" || tot_rec_qty.length == 0 || tot_rec_qty == "0.00" || tot_rec_qty == "0"){ tot_rec_qty = 0; }
                document.getElementById("rcd_qty["+d+"]").value = parseFloat(tot_rec_qty).toFixed(2);
                
                if(qty_on_sqty_flag == 1 || qty_on_sqty_flag == "1"){ var qty = document.getElementById("snt_qty["+d+"]").value; }
                else{ var qty = document.getElementById("rcd_qty["+d+"]").value; }
                
                var total_qty = parseFloat(tot_rec_qty) + parseFloat(fre_qty);
                document.getElementById("total_qty["+d+"]").value = parseFloat(total_qty).toFixed(2);

                var price = document.getElementById("rate["+d+"]").value;
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
                if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ dis_amt = 0; }
                if(gst_per == "" || gst_per.length == 0 || gst_per == "0.00" || gst_per == "0"){ gst_per = 0; }

                var total_amt = parseFloat(qty) * parseFloat(price);
                /*if(dis_amt > 0){
                    total_amt = parseFloat(total_amt) - parseFloat(dis_amt);
                }
                if(gst_per > 0){
                    var gst_value = ((parseFloat(gst_per) / 100) * total_amt);
                    total_amt = parseFloat(total_amt) + parseFloat(gst_value);
                }*/
                document.getElementById("item_tamt["+d+"]").value = total_amt;
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
                var auto_tds_flag = '<?php echo $auto_tds_flag; ?>';
                if(auto_tds_flag == 1 || auto_tds_flag == "1"){
                    var mnu_tds_uchk = document.getElementById("mnu_tds_uchk").value;
                    if(mnu_tds_uchk == 1 || mnu_tds_uchk == "1"){
                        var rqty = rprc = total_item_amount = 0;
                        for(d = 0; d <= incr; d++){
                            rqty = document.getElementById("rcd_qty["+d+"]").value;
                            rprc = document.getElementById("rate["+d+"]").value;
                            if(rqty == "" || rqty == "0" || rqty.length == 0 || rqty == "0.00"){ rqty = 0; }
                            if(rprc == "" || rprc == "0" || rprc.length == 0 || rprc == "0.00"){ rprc = 0; }
                            if(rqty > 0 && rprc > 0){
                                total_item_amount = (parseFloat(total_item_amount) + (parseFloat(rqty) * parseFloat(rprc)));
                            }
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
                        var rqty = rprc = item_amount = 0;
                        for(d = 0; d <= incr; d++){
                            rqty = document.getElementById("rcd_qty["+d+"]").value;
                            rprc = document.getElementById("rate["+d+"]").value;
                            if(rqty == "" || rqty == "0" || rqty.length == 0 || rqty == "0.00"){ rqty = 0; }
                            if(rprc == "" || rprc == "0" || rprc.length == 0 || rprc == "0.00"){ rprc = 0; }
                            if(rqty > 0 && rprc > 0){
                                item_amount = (parseFloat(item_amount) + (parseFloat(rqty) * parseFloat(rprc)));
                            }
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
            function broiler_fetch_Supplierpurchases(){
                var auto_tds_flag = '<?php echo $auto_tds_flag; ?>';
                var trnum = '<?php echo $id; ?>';
                if(auto_tds_flag == 1 || auto_tds_flag == "1"){
                    var vcode = document.getElementById("vcode").value;
                    var date = document.getElementById("date").value;
                    if(vcode != "select"){
                        var ven_bals = new XMLHttpRequest();
                        var method = "GET";
                        var url = "broiler_fetch_vendorpurtamt.php?date="+date+"&vcode="+vcode+"&trnum="+trnum;
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
            broiler_fetch_Supplierpurchases();
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
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