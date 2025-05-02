<?php
//broiler_edit_multisales2.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['multisales2'];
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
        $today = date("Y-m-d"); $fdate = date("d.m.Y",strtotime($today)); $y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
		$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$today' AND `tdate` >= '$today'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
				
		$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$today' AND `tdate` >= '$today' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $purchases = $row['purchases']; } $incr = $purchases + 1;
		if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
		
        $sql = "SELECT * FROM `prefix_master` WHERE `transaction_type` LIKE 'purchases' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
            $prefix = $row['prefix'];
            $incr_wspb_flag = $row['incr_wspb_flag'];
            $inv_format[$row['sfin_year_flag']] = "sfin_year_flag";
            $inv_format[$row['sfin_year_wsp_flag']] = "sfin_year_wsp_flag";
            $inv_format[$row['efin_year_flag']] = "efin_year_flag";
            $inv_format[$row['efin_year_wsp_flag']] = "efin_year_wsp_flag";
            $inv_format[$row['day_flag']] = "day_flag";
            $inv_format[$row['day_wsp_flag']] = "day_wsp_flag";
            $inv_format[$row['month_flag']] = "month_flag";
            $inv_format[$row['month_wsp_flag']] = "month_wsp_flag";
            $inv_format[$row['year_flag']] = "year_flag";
            $inv_format[$row['year_wsp_flag']] = "year_wsp_flag";
            $inv_format[$row['hour_flag']] = "hour_flag";
            $inv_format[$row['hour_wsp_flag']] = "hour_wsp_flag";
            $inv_format[$row['minute_flag']] = "minute_flag";
            $inv_format[$row['minute_wsp_flag']] = "minute_wsp_flag";
            $inv_format[$row['second_flag']] = "second_flag";
            $inv_format[$row['second_wsp_flag']] = "second_wsp_flag";
        }
        $a = 1; $tr_code = $prefix;
        for($i = 0;$i <= 16;$i++){
            if($inv_format[$i.":".$a] == "sfin_year_flag"){
                $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8');
            }
            else if($inv_format[$i.":".$a] == "sfin_year_wsp_flag"){
                $tr_code = $tr_code."".mb_substr($pfx, 0, 2, 'UTF-8')."-";
            }
            else if($inv_format[$i.":".$a] == "efin_year_flag"){
                $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8');
            }
            else if($inv_format[$i.":".$a] == "efin_year_wsp_flag"){
                $tr_code = $tr_code."".mb_substr($pfx, 2, 2, 'UTF-8')."-";
            }
            else if($inv_format[$i.":".$a] == "day_flag"){
                $tr_code = $tr_code."".date("d");
            }
            else if($inv_format[$i.":".$a] == "day_wsp_flag"){
                $tr_code = $tr_code."".date("d")."-";
            }
            else if($inv_format[$i.":".$a] == "month_flag"){
                $tr_code = $tr_code."".date("m");
            }
            else if($inv_format[$i.":".$a] == "month_wsp_flag"){
                $tr_code = $tr_code."".date("m")."-";
            }
            else if($inv_format[$i.":".$a] == "year_flag"){
                $tr_code = $tr_code."".date("Y");
            }
            else if($inv_format[$i.":".$a] == "year_wsp_flag"){
                $tr_code = $tr_code."".date("Y")."-";
            }
            else if($inv_format[$i.":".$a] == "hour_flag"){
                $tr_code = $tr_code."".date("H");
            }
            else if($inv_format[$i.":".$a] == "hour_wsp_flag"){
                $tr_code = $tr_code."".date("H")."-";
            }
            else if($inv_format[$i.":".$a] == "minute_flag"){
                $tr_code = $tr_code."".date("i");
            }
            else if($inv_format[$i.":".$a] == "minute_wsp_flag"){
                $tr_code = $tr_code."".date("i")."-";
            }
            else if($inv_format[$i.":".$a] == "second_flag"){
                $tr_code = $tr_code."".date("s");
            }
            else if($inv_format[$i.":".$a] == "second_wsp_flag"){
                $tr_code = $tr_code."".date("s")."-";
            }
            else{ }
        }
        $code = ""; if($incr_wspb_flag == 1|| $incr_wspb_flag == "1"){ $code = $tr_code."-".$incr; } else{ $code = $tr_code."".$incr; }

		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $farm_list = "";
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description'];if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; } }
		
		$sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
				
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $supervisor_codes[$row['code']] = $row['code']; $supervisor_names[$row['code']] = $row['name']; }
		
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%bag%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $bcodes = "";
        while($row = mysqli_fetch_assoc($query)){ if($bcodes == ""){ $bcodes = $row['code']; } else{ $bcodes = $bcodes."','".$row['code']; } }
        
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; $bag_name[$row['code']] = $row['description']; }
		
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $decimal_no = $row['flag']; }

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'SalewithRCTAutoWapp:broiler_edit_multisales2.php' AND `field_function` LIKE 'Send WhatsApp on Edit' AND (`user_access` LIKE '%$user_code%' OR `user_access` LIKE 'all') AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $swoe_flag = mysqli_num_rows($query);
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
        $sql = "SELECT * FROM `broiler_sales` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $date = $row['date'];
            $vcode = $row['vcode'];
            $billno = $row['billno'];
            $icode = $row['icode'];
            $birds = str_replace(".00","",$row['birds']);
            $rcd_qty = $row['rcd_qty'];
            if($birds > 0 && $rcd_qty > 0){
                $avg_wt = round($rcd_qty / $birds,2);
            }
            else{
                $avg_wt = 0;
            }
            $rate = $row['rate'];
            $item_tamt = $row['item_tamt'];
            $round_off = $row['round_off'];
            $mnu_rf_flag = $row['mnu_rf_flag'];
            $finl_amt = $row['finl_amt'];
            $remarks = $row['remarks'];
            $warehouse = $row['warehouse'];
            $supervisor_code = $row['supervisor_code'];
            $vhcode = $row['vehicle_code'];
            $dcode = $row['driver_code'];
            $sale_type = $row['sale_type'];

            //$batch = explode("-",$farms_batch[$warehouse]);
            //$rename_batch = $batch[1]."-".$batch[2];
            $rename_batch = $farms_batch[$warehouse];
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
                                <form action="broiler_modify_multisales2.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group" align="center">
                                            <label>Customer Sale</label>
                                            <input type="radio" name="sale_type" id="sale_type1" class="form-control" value="CusSale" style="width:90px;transform: scale(.7);" onclick="fetch_sale_type(this.id)" <?php if($sale_type == "CusMBSale"){ echo "checked"; } ?> />
                                        </div>
                                        <div class="form-group" align="center">
                                            <label>Farmer Sale</label>
                                            <input type="radio" name="sale_type" id="sale_type2" class="form-control" value="FormSale" style="width:90px;transform: scale(.7);" onclick="fetch_sale_type(this.id)" <?php if($sale_type == "FormMBSale"){ echo "checked"; } ?> />
                                        </div>
                                        <?php if((int)$swoe_flag == 1){ ?>
                                        <div class="form-group" align="center">
                                            <label>WhatsApp</label>
                                            <input type="checkbox" name="send_whatsapp" id="send_whatsapp" class="form-control" style="width:90px;transform: scale(.7);" checked />
                                        </div>
                                        <?php } ?>
                                    </div><br/>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date)); ?>" style="width:80px;">
                                        </div>
                                        <div class="form-group" id="customer_sale" <?php if($sale_type != "CusMBSale"){ echo "style='display:none;'"; } ?>>
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:160px;" onchange="fetch_custid(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>" <?php if($vcode == $sup_code){ echo "selected"; } ?>><?php echo $ven_name[$sup_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Customer ID.<b style="color:red;"></b></label>
                                            <input type="text" name="custid" id="custid" class="form-control" value="<?php echo $vcode; ?>" style="width:60px;" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="billno" id="billno" class="form-control" value="<?php echo $billno; ?>" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Farm<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:160px;" onchange="fetch_batch(this.id)">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($warehouse == $whouse_code){ echo "selected"; } ?>><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Batch</label>
                                            <td><input readonly type="text" name="batch" id="batch"  value="<?php echo $rename_batch; ?>" class="form-control" style="width:120px;" /></td>
                                        </div>
                                        <div class="form-group">
                                            <label>Birds</label>
                                            <input type="text" name="birds" id="birds" class="form-control" value="<?php echo $birds; ?>" placeholder="0.00" style="width:65px;" onkeyup="validatenum_birds(this.id);calculate_total_amt();" onchange="validateamount_birds(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Net Wt.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rcd_qty" id="rcd_qty" class="form-control" value="<?php echo $rcd_qty; ?>" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt();" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Avg Wt.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="avg_wt" id="avg_wt" class="form-control" value="<?php echo $avg_wt; ?>" placeholder="0.00" style="width:65px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Rate<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rate" id="rate" class="form-control" value="<?php echo $rate; ?>" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt();" onchange="validateamount(this.id);" />
                                        </div>
                                        <div class="form-group">
                                            <label>RoundOff</label>
                                            <input type="text" name="round_off" id="round_off" class="form-control" value="<?php echo $round_off; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);update_mnurf_flag();" />
                                        </div>
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="text" name="finl_amt" id="finl_amt" class="form-control" value="<?php echo $finl_amt; ?>" placeholder="0.00" style="width:90px;" readonly >
                                        </div>
                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                            <label>IA</label>
                                            <input type="text" name="item_tamt" id="item_tamt" class="form-control" value="<?php echo $item_tamt; ?>" style="width:10px;" readonly >
                                        </div>
                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                            <label>MR</label>
                                            <input type="text" name="mnu_rf_flag" id="mnu_rf_flag" class="form-control" value="<?php echo $mnu_rf_flag; ?>" style="width:10px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Supervisor</label>
                                            <select name="supervisor_code" id="supervisor_code" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($supervisor_codes as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($supervisor_code == $scode){ echo "selected"; } ?>><?php echo $supervisor_names[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label >Vehicle</label>
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" value="<?php echo $vhcode; ?>" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" value="<?php echo $dcode; ?>" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="padding:0;width:120px;height:25px;"><?php echo $remarks; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>">
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
                window.location.href = 'broiler_display_multisales2.php?ccid='+ccid;
            }
            function fetch_batch(a){

                //var b = a.split("["); var c = b[1].split("]"); var d = c[0];

                var to_sector = document.getElementById("warehouse").value;

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
                        document.getElementById("batch").value = rename_batch;

                <?php
                    echo "}";
                    }
                ?>


            }

            function fetch_custid(a){

               // var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var code = document.getElementById("vcode").value;
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
                document.getElementById("custid").value = code;
            }
            function checkval(){
                var warehouse = billno = vehicle_code = cus_code = ""; var rcd_qty = rate = 0;
                var l = true;
                calculate_total_amt();
                billno = document.getElementById("billno").value;
                vehicle_code = document.getElementById("vehicle_code").value;
                rcd_qty = document.getElementById("rcd_qty").value;
                rate = document.getElementById("rate").value;
                warehouse = document.getElementById("warehouse").value;
                if(document.getElementById("sale_type1").checked == true){
                    cus_code = document.getElementById("vcode").value;
                    if(cus_code.match("select")){
                        alert("Please select Customer");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else if(billno == "" || billno.length == 0){
                        alert("Please select Dc No. in row:-"+c);
                        document.getElementById("billno").focus();
                        l = false;
                    }
                    else if(warehouse.match("select")){
                        alert("Please select Sector/Farm");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                        alert("Please enter Rcd Qty");
                        document.getElementById("rcd_qty").focus();
                        l = false;
                    }
                    else if(rate == "" || rate.length == 0 || rate == "0" || rate == 0 || rate == "0.00"){
                        alert("Please enter Rate");
                        document.getElementById("rate").focus();
                        l = false;
                    }
                    else if(vehicle_code == "" || vehicle_code.length == 0){
                        alert("Please enter Vehicle No.");
                        document.getElementById("vehicle_code").focus();
                        l = false;
                    }
                    else{ }
                }
                else{
                    if(billno == "" || billno.length == 0){
                        alert("Please select Dc No. in row:-"+c);
                        document.getElementById("billno").focus();
                        l = false;
                    }
                    else if(warehouse.match("select")){
                        alert("Please select Sector/Farm");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                        alert("Please enter Rcd Qty");
                        document.getElementById("rcd_qty").focus();
                        l = false;
                    }
                    else if(rate == "" || rate.length == 0 || rate == "0" || rate == 0 || rate == "0.00"){
                        alert("Please enter Rate");
                        document.getElementById("rate").focus();
                        l = false;
                    }
                    else if(vehicle_code == "" || vehicle_code.length == 0){
                        alert("Please enter Vehicle No.");
                        document.getElementById("vehicle_code").focus();
                        l = false;
                    }
                    else{ }
                }
                if(l == true){
                    document.getElementById("submit").disabled = true;
                    return true;
                }
                else{
                    return false;
                }
            }
            function calculate_total_amt(){
                var qty = document.getElementById("rcd_qty").value; if(qty == ""){ qty = 0; }
                var price = document.getElementById("rate").value; if(price == ""){ price = 0; }
                var birds = document.getElementById("birds").value; if(birds == ""){ birds = 0; }
                
                var total_amt = parseFloat(qty) * parseFloat(price);
                document.getElementById("item_tamt").value = total_amt;

                /*Round-Off Calculations*/
                var mnu_rf_flag = document.getElementById("mnu_rf_flag").value;
                total_amt = document.getElementById("item_tamt").value;
                var round_off = finl_amt = 0;
                if(parseInt(mnu_rf_flag) == 1){
                    round_off = document.getElementById("round_off").value; if(round_off == "" || round_off == "-"){ round_off = 0; }
                    finl_amt = parseFloat(total_amt) + parseFloat(round_off);
                    document.getElementById("finl_amt").value = parseFloat(finl_amt).toFixed(2);
                }
                else{
                    finl_amt = parseFloat(total_amt).toFixed(0);
                    round_off = parseFloat(finl_amt) - parseFloat(total_amt);
                    document.getElementById("finl_amt").value = parseFloat(finl_amt);
                    document.getElementById("round_off").value = parseFloat(round_off).toFixed(2);
                }

                if(qty > 0 && birds > 0){
                    var avgwt = parseFloat(qty) / parseFloat(birds);
                }
                else{
                    var avgwt = 0;
                }
                if(avgwt == "" || avgwt.length == 0 || avgwt == "0.00" || avgwt == "0"){ avgwt = 0; }
                document.getElementById("avg_wt").value = avgwt.toFixed(2);
            }
            function fetch_sale_type(){
                if(document.getElementById("sale_type1").checked == true){
                    document.getElementById("customer_sale").style.display = "inline";
                }
                else{
                    document.getElementById("customer_sale").style.display = "none";
                } 
            }
            function update_mnurf_flag(){
                document.getElementById("mnu_rf_flag").value = 1;
                calculate_total_amt();
            }
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function validatenum_birds(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount_birds(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(0); document.getElementById(x).value = b; }
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