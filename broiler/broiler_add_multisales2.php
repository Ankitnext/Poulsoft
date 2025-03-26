<?php
//broiler_add_multisales2.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['multisales2'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
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
        $today = $fdate = date("d.m.Y"); $y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
        
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $farm_list = "";
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description']; if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; } }
		$sector_code = $sector_name = array();
		$sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `active` = '1'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $supervisor_codes[$row['code']] = $row['code']; $supervisor_names[$row['code']] = $row['name']; }
		
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Multiple Sale') AND `field_function` = 'Vehicle mandatory' AND (`user_access` LIKE '%$user_code%' OR `user_access` LIKE 'all')";
        $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
        if($count > 0){ while($row = mysqli_fetch_assoc($query)){ $veh_mdt = $row['flag']; } if($veh_mdt == ""){ $veh_mdt = 1; } } else{ $veh_mdt = 1; }
        
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('MultipleSale-Receipt2') AND (`user_access` LIKE '%$user_code%' OR `user_access` LIKE 'all') AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $rct2_flag = mysqli_num_rows($query);
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
        .form-group {
            padding-bottom: 10px;
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
                            <div class="float-left"><h3 class="card-title">Add Sales</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_multisales2.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group" align="center">
                                            <label>Customer Sale</label>
                                            <input type="radio" name="sale_type" id="sale_type1" class="form-control" value="CusMBSale" style="width:90px;transform: scale(.7);" onclick="fetch_sale_type(this.id)" checked />
                                        </div>
                                        <div class="form-group" align="center">
                                            <label>Farmer Sale</label>
                                            <input type="radio" name="sale_type" id="sale_type2" class="form-control" value="FormMBSale" style="width:90px;transform: scale(.7);" onclick="fetch_sale_type(this.id)" />
                                        </div>
                                    </div><br/>
                                    <div class="row rows" style="margin-bottom:3px;">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date[]" id="date[0]" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:80px;" readonly />
                                        </div>
                                        <div class="form-group" id="customer_sale[0]">
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode[]" id="vcode[0]" class="form-control select2" style="width:160px;" onchange="fetch_itemuom(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                         <div class="form-group">
                                            <label>Customer ID<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="custid[]" id="custid[0]" class="form-control" style="width:80px;" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Doc No.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="billno[]" id="billno[0]" class="form-control" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Farm<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse[]" id="warehouse[0]" class="form-control select2" style="width:160px;" onchange="fetch_batch(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Batch</label>
                                            <td><input readonly type="text" name="batch[]" id="batch[0]"  class="form-control" style="width:120px;" /></td>
                                        </div>
                                        <div class="form-group">
                                            <label>Birds</label>
                                            <input type="text" name="birds[]" id="birds[0]" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatebirdnum(this.id);calculate_total_amt(this.id);" onchange="validatebirdamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Net Wt.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rcd_qty[]" id="rcd_qty[0]" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>Avg Wt.<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="avg_wt[]" id="avg_wt[0]" class="form-control" placeholder="0.00" style="width:65px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Rate<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rate[]" id="rate[0]" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label>RoundOff</label>
                                            <input type="text" name="round_off[]" id="round_off[0]" class="form-control" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);update_mnurf_flag(this.id);" />
                                        </div>
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="text" name="finl_amt[]" id="finl_amt[0]" class="form-control" placeholder="0.00" style="width:90px;" readonly >
                                        </div>
                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                            <label>IA</label>
                                            <input type="text" name="item_tamt[]" id="item_tamt[0]" class="form-control" style="width:10px;" readonly >
                                        </div>
                                        <div class="form-group" style="width:20px;visibility:hidden;">
                                            <label>MR</label>
                                            <input type="text" name="mnu_rf_flag[]" id="mnu_rf_flag[0]" class="form-control" value="0" style="width:10px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label>Lifting Supervisor</label>
                                            <select name="supervisor_code[]" id="supervisor_code[0]" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($supervisor_codes as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $supervisor_names[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label >Vehicle <?php if($veh_mdt == 1){ echo '<b style="color:red;">&nbsp;*</b>'; } ?></label>
                                            <input type="text" name="vehicle_code[]" id="vehicle_code[0]" class="form-control" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label >Driver</label>
                                            <input type="text" name="driver_code[]" id="driver_code[0]" class="form-control" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:120px;height:25px;"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Receipt</label>
                                            <input type="text" name="receipt[]" id="receipt[0]" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" />
                                        </div>
                                        <?php
                                        if($rct2_flag > 0){
                                        ?>
                                        <div class="form-group">
                                            <label>Method</label>
                                            <select name="coa_code2[]" id="coa_code2[0]" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($coa_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $coa_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Receipt-2</label>
                                            <input type="text" name="receipt2[]" id="receipt2[0]" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" />
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="form-group" id="action[0]"><br/>
                                            <a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="padding-top:15px;width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-18" id="row_body"></div><br/><br/>
                                    <div class="row">
                                        <div class="form-group">
                                            <label >Total Birds</label>
                                            <input type="text" name="total_birds" id="total_birds" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Net Wt.</label>
                                            <input type="text" name="total_netwt" id="total_netwt" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Avg. Wt.</label>
                                            <input type="text" name="total_avgwt" id="total_avgwt" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Avg. Price</label>
                                            <input type="text" name="total_price" id="total_price" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Total Amount</label>
                                            <input type="text" name="total_amount" id="total_amount" class="form-control" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label >Receipt Total</label>
                                            <input type="text" name="total_rct_amount" id="total_rct_amount" class="form-control" style="width:120px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
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
                window.location.href = 'broiler_display_multisales2.php?ccid='+ccid;
            }
            function fetch_batch(a){

                var b = a.split("["); var c = b[1].split("]"); var d = c[0];

                var to_sector = document.getElementById("warehouse["+d+"]").value;

                <?php
                    foreach($farms as $fcode){
                    
                        echo "if(to_sector == '$fcode'){";
                ?>
                        //alert('<?php echo $farms_batch[$fcode]; ?>');
                        //GEt value from array
                        var batch = '<?php echo $farms_batch[$fcode]; ?>';
                        /* Split using a space character
                        let arr = batch.split('-');
                        if(arr[2] != ""){
                            var rename_batch = arr[1] + '-' + arr[2];
                        }
                        else{
                            var rename_batch = batch;
                        }*/
                        document.getElementById("batch["+d+"]").value = batch;

                <?php
                    echo "}";
                    }
                ?>


            }

           function fetch_itemuom(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var code = document.getElementById("vcode["+d+"]").value;
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
                document.getElementById("custid["+d+"]").value = code;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value;
                var a = warehouse = billno = vehicle_code = cus_code = ""; var rcd_qty = rate = item_tamt = c = rate_count = 0;
                var l = true; var veh_mdt = '<?php echo $veh_mdt; ?>';
                for(var d = 0;d <= incrs;d++){
                    rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                    rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                    item_tamt = parseFloat(rcd_qty) * parseFloat(rate);
                    document.getElementById("item_tamt["+d+"]").value = parseFloat(item_tamt).toFixed(2);
                    a = "rcd_qty["+d+"]";
                    calculate_total_amt(a);
                }
                for(var d = 0;d <= incrs;d++){
                    if(l == true){
                        c = d + 1;
                        billno = document.getElementById("billno["+d+"]").value;
                        vehicle_code = document.getElementById("vehicle_code["+d+"]").value;
                        rcd_qty = document.getElementById("rcd_qty["+d+"]").value;
                        rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                        warehouse = document.getElementById("warehouse["+d+"]").value;
                        if(document.getElementById("sale_type1").checked == true){
                            cus_code = document.getElementById("vcode["+d+"]").value;
                            if(cus_code.match("select")){
                                alert("Please select Customer in row:-"+c);
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                            }
                            else if(billno == "" || billno.length == 0){
                                alert("Please select Dc No. in row:-"+c);
                                document.getElementById("billno["+d+"]").focus();
                                l = false;
                            }
                            else if(warehouse.match("select")){
                                alert("Please select Sector/Farm in row:-"+c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                                alert("Please enter Rcd Qty in row:-"+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(rate) == 0){
                                alert("Please enter Rate in row:-"+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(veh_mdt) == 1 && vehicle_code == "" || parseInt(veh_mdt) == 1 && vehicle_code.length == 0){
                                alert("Please enter Vehicle No. in row:-"+c);
                                document.getElementById("vehicle_code["+d+"]").focus();
                                l = false;
                            }
                            else{ }

                            if(parseFloat(rate) < 2){
                                rate_count++;
                            }
                        }
                        else{
                            if(billno == "" || billno.length == 0){
                                alert("Please select Dc No. in row:-"+c);
                                document.getElementById("billno["+d+"]").focus();
                                l = false;
                            }
                            else if(warehouse.match("select")){
                                alert("Please select Sector/Farm in row:-"+c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                                alert("Please enter Rcd Qty in row:-"+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(rate) == 0){
                                alert("Please enter Rate in row:-"+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            else if(vehicle_code == "" || vehicle_code.length == 0){
                                alert("Please enter Vehicle No. in row:-"+c);
                                document.getElementById("vehicle_code["+d+"]").focus();
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
                var date = document.getElementById('date['+d+']').value;
                d++; var html = '';
                document.getElementById("incr").value = d;
                var today = '<?php echo $today; ?>';


                html += '<div class="row rows" id="row_no['+d+']">';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Date<b style="color:red;">&nbsp;*</b></label><input type="text" name="date[]" id="date['+d+']" class="form-control datepicker" value="'+date+'" style="width:80px;" readonly /></div>';
                if(document.getElementById("sale_type1").checked == true){
                    html += '<div class="form-group" id="customer_sale['+d+']"><label class="labelrow" style="display:none;">Customer<b style="color:red;">&nbsp;*</b></label><select onchange="fetch_itemuom(this.id);" name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?></select></div>';
                }
                else{
                    html += '<div class="form-group" id="customer_sale['+d+']" style="display:none;"><label class="labelrow" style="display:none;">Customer<b style="color:red;">&nbsp;*</b></label><select onchange="fetch_itemuom(this.id);" name="vcode[]" id="vcode['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($ven_code as $sup_code){ ?><option value="<?php echo $sup_code; ?>"><?php echo $ven_name[$sup_code]; ?></option><?php } ?></select></div>';
                }

                html += '<div class="form-group"><label class="labelrow" style="display:none;">Customer ID</label><input type="text" name="custid[]" id="custid['+d+']" class="form-control" style="width:80px;" readonly></div>';
            
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Doc No.</label><input type="text" name="billno[]" id="billno['+d+']" class="form-control" style="width:60px;" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Farm<b style="color:red;">&nbsp;*</b></label><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:160px;" onchange="fetch_batch(this.id);"><option value="select">select</option><?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo $sector_name[$whouse_code]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Batch</label><td><input readonly type="text" name="batch[]" id="batch['+d+']"  class="form-control" style="width:120px;" /></td></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Birds</label><input type="text" name="birds[]" id="birds['+d+']" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Net Wt.<b style="color:red;">&nbsp;*</b></label><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Avg Wt.<b style="color:red;">&nbsp;*</b></label><input type="text" name="avg_wt[]" id="avg_wt['+d+']" class="form-control" placeholder="0.00" style="width:65px;" readonly ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Rate<b style="color:red;">&nbsp;*</b></label><input type="text" name="rate[]" id="rate['+d+']" class="form-control" placeholder="0.00" style="width:65px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">RoundOff</label><input type="text" name="round_off[]" id="round_off['+d+']" class="form-control" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);update_mnurf_flag(this.id);" ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Amount</label><input type="text" name="finl_amt[]" id="finl_amt['+d+']" class="form-control" placeholder="0.00" style="width:90px;" readonly ></div>';
                html += '<div class="form-group" style="width:20px;visibility:hidden;"><label class="labelrow" style="display:none;">Amount</label><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control" placeholder="0.00" style="width:10px;" readonly ></div>';
                html += '<div class="form-group" style="width:20px;visibility:hidden;"><label class="labelrow" style="display:none;">Amount</label><input type="text" name="mnu_rf_flag[]" id="mnu_rf_flag['+d+']" class="form-control" value="0" style="width:10px;" readonly ></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Lifting Supervisor</label><select name="supervisor_code[]" id="supervisor_code['+d+']" class="form-control select2" style="width:120px;"><option value="select">select</option><?php foreach($supervisor_codes as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $supervisor_names[$scode]; ?></option><?php } ?></select></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Vehicle</label><input type="text" name="vehicle_code[]" id="vehicle_code['+d+']" class="form-control" style="width:120px;" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Driver</label><input type="text" name="driver_code[]" id="driver_code['+d+']" class="form-control" style="width:120px;" /></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Remarks</label><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:120px;height:25px;"></textarea></div>';
                html += '<div class="form-group"><label class="labelrow" style="display:none;">Receipt</label><input type="text" name="receipt[]" id="receipt['+d+']" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></div>';
                <?php
                if($rct2_flag > 0){
                    ?>
                    html += '<div class="form-group"><label class="labelrow" style="display:none;">Method</label><select name="coa_code2[]" id="coa_code2['+d+']" class="form-control select2" style="width:120px;"><option value="select">select</option><?php foreach($coa_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $coa_name[$scode]; ?></option><?php } ?></select></div>';
                    html += '<div class="form-group"><label class="labelrow" style="display:none;">Receipt-2</label><input type="text" name="receipt2[]" id="receipt2['+d+']" class="form-control" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></div>';
                <?php
                }
                ?>
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '</div>';
                html += '<hr class="labelrow" style="display:none;" />';
                $('#row_body').append(html); $('.select2').select2();
                $( ".datepicker" ).datepicker({
                    inline: true,
                    showButtonPanel: false,
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "dd.mm.yy",
                    maxDate: today,
                    beforeShow: function(){    
                        $(".ui-datepicker").css('font-size', 12) 
                    }
                });
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
                var qty = document.getElementById("rcd_qty["+d+"]").value; if(qty == ""){ qty = 0; }
                var price = document.getElementById("rate["+d+"]").value; if(price == ""){ price = 0; }
                var birds = document.getElementById("birds["+d+"]").value; if(birds == ""){ birds = 0; }

                var total_amt = parseFloat(qty) * parseFloat(price);
                document.getElementById("item_tamt["+d+"]").value = parseFloat(total_amt).toFixed(2);

                /*Round-Off Calculations*/
                var mnu_rf_flag = document.getElementById("mnu_rf_flag["+d+"]").value;
                total_amt = document.getElementById("item_tamt["+d+"]").value;
                var round_off = finl_amt = 0;
                if(parseInt(mnu_rf_flag) == 1){
                    round_off = document.getElementById("round_off["+d+"]").value; if(round_off == "" || round_off == "-"){ round_off = 0; }
                    finl_amt = parseFloat(total_amt) + parseFloat(round_off);
                    document.getElementById("finl_amt["+d+"]").value = parseFloat(finl_amt).toFixed(2);
                }
                else{
                    finl_amt = parseFloat(total_amt).toFixed(0);
                    round_off = parseFloat(finl_amt) - parseFloat(total_amt);
                    document.getElementById("finl_amt["+d+"]").value = parseFloat(finl_amt);
                    document.getElementById("round_off["+d+"]").value = round_off;
                }

                if(qty > 0 && birds > 0){
                    var avgwt = parseFloat(qty) / parseFloat(birds);
                }
                else{
                    var avgwt = 0;
                }
                if(avgwt == "" || avgwt.length == 0 || avgwt == "0.00" || avgwt == "0"){ avgwt = 0; }
                document.getElementById("avg_wt["+d+"]").value = avgwt.toFixed(2);

                var max_incr = document.getElementById("incr").value;
                var total_birds = total_netwt = total_avgwt = total_price = total_amount = rcts = total_rcts = bb = cc = dd = ee = ff = 0;
                for(var a = 0;a <= max_incr;a++){
                    bb = document.getElementById("birds["+a+"]").value; if(bb == ""){ bb = 0; }
                    total_birds = parseFloat(total_birds) + parseFloat(bb);

                    cc = document.getElementById("rcd_qty["+a+"]").value; if(cc == ""){ cc = 0; }
                    total_netwt = parseFloat(total_netwt) + parseFloat(cc);

                    rcts = document.getElementById("receipt["+a+"]").value; if(rcts == ""){ rcts = 0; }
                    total_rcts = parseFloat(total_rcts) + parseFloat(rcts);

                    //dd = document.getElementById("avg_wt["+a+"]").value; if(dd == ""){ dd = 0; }
                    //total_avgwt = parseFloat(total_avgwt) + parseFloat(dd);

                    //ee = document.getElementById("rate["+a+"]").value; if(ee == ""){ ee = 0; }
                    //total_price = parseFloat(total_price) + parseFloat(ee);
                    
                    ff = document.getElementById("item_tamt["+a+"]").value; if(ff == ""){ ff = 0; }
                    total_amount = parseFloat(total_amount) + parseFloat(ff);
                }
                if(total_netwt > 0 && total_birds > 0){
                    total_avgwt = total_netwt / total_birds;
                }
                else{
                    total_avgwt = 0;
                }
                if(total_netwt > 0 && total_amount > 0){
                    total_price = total_amount / total_netwt;
                }
                else{
                    total_price = 0;
                }
                if(total_birds == ""){ total_birds = 0; }
                if(total_netwt == ""){ total_netwt = 0; }
                if(total_avgwt == ""){ total_avgwt = 0; }
                if(total_price == ""){ total_price = 0; }
                if(total_amount == ""){ total_amount = 0; }
                if(total_rcts == ""){ total_rcts = 0; }
                document.getElementById("total_birds").value = total_birds.toFixed(2);
                document.getElementById("total_netwt").value = total_netwt.toFixed(2);
                document.getElementById("total_avgwt").value = total_avgwt.toFixed(2);
                document.getElementById("total_price").value = total_price.toFixed(2);
                document.getElementById("total_amount").value = total_amount.toFixed(2);
                document.getElementById("total_rct_amount").value = total_rcts.toFixed(2);

            }
            function fetch_sale_type(){
                var incrs = document.getElementById("incr").value;
                if(document.getElementById("sale_type1").checked == true){
                    for(var incr = 0;incr <= incrs;incr++){
                        document.getElementById("customer_sale["+incr+"]").style.display = "inline";
                        document.getElementById("receipt["+incr+"]").readOnly = false;
                    }
                }
                else{
                    for(var incr = 0;incr <= incrs;incr++){
                        document.getElementById("customer_sale["+incr+"]").style.display = "none";
                        document.getElementById("receipt["+incr+"]").value = "";
                        document.getElementById("receipt["+incr+"]").readOnly = true;
                    }
                } 
            }
            function update_mnurf_flag(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("mnu_rf_flag["+d+"]").value = 1;
                calculate_total_amt(a);
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.-]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.-]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function validatebirdnum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validatebirdamount(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(0); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                //alert(window.innerWidth);
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