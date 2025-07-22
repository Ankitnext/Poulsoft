<?php
//broiler_add_inventorytransfer.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['inventorytransfer'];
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
        //check and fetch date range
        global $drng_cday; $drng_cday = 1; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        include "poulsoft_fetch_daterange_master.php";

        $today = date("d.m.Y");
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $from_sector_code[$row['code']] = $row['code']; $from_sector_name[$row['code']] = $row['description']; }
 				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $to_sector_code[$row['code']] = $row['code']; $to_sector_name[$row['code']] = $row['description']; }
        
		$farms = array();
		$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description']; }
        $farm_list = implode("','", $farms);
				
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list')".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $to_sector_code[$row['code']] = $from_sector_code[$row['code']] = $row['code']; $to_sector_name[$row['code']] = $from_sector_name[$row['code']] = $row['description']; $farm_code[$row['code']] = $row['farm_code']; }
        
		$sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
				
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
        
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Item Transfer' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }
        
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'broiler_display_inventorytransfer.php' AND `field_function` LIKE 'Vehicle and Driver'"; $query = mysqli_query($conn,$sql);  
        $vd_flag = mysqli_num_rows($query);
        
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Item Transfer' AND `field_function` LIKE 'Auto Avg Price'"; $query = mysqli_query($conn,$sql); $autoavgprice_flag = 0; $aapcount = mysqli_num_rows($query);
        if($aapcount > 0){ while($row = mysqli_fetch_assoc($query)){ $autoavgprice_flag = $row['flag']; } } else{ $autoavgprice_flag = 0; } if($autoavgprice_flag == "" || $autoavgprice_flag == 0){ $autoavgprice_flag = 0; }
        
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Item Transfer' AND `field_function` LIKE 'Employee-Stock Transfer Expense' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $este_flag = mysqli_num_rows($query);
        if((int)$este_flag == 1){
            $sql = "SELECT * FROM `broiler_employee` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
            while($row = mysqli_fetch_assoc($query)){ $exp_ecode[$row['code']] = $row['code']; $exp_ename[$row['code']] = $row['name']; }

            $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `dflag` = '0' AND `este_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $exp_acode[$row['code']] = $row['code']; $exp_aname[$row['code']] = $row['description']; }
        }
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            //transform: scale(0.9);
            //transform-origin: top left;
            overflow: auto;
        }
        .form-control{
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
        /*.select2-container {
            transform: scale(0.9);
            transform-origin: top left;
        }
        .select2-dropdown {
            transform: scale(0.9);
            transform-origin: top left;
        }*/
        ::-webkit-scrollbar { width: 8px; height:8px; } /*display: none;*/
        .row_body2{
            width:100%;
            overflow-y: auto;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Inventory Transfer</h3></div>
                        </div>
                        <div class="p-1 card-body">
                            <form action="broiler_save_inventorytransfer.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="p-1 row row_body2" style="margin-bottom:3px;">
                                    <table class="p-1">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                                <th><label>Dc No.</label></th>
                                                <th><label>Item<b style="color:red;">&nbsp;*</b></label></th>
                                                <th><label>UOM</label></th>
                                                <th style="visibility:visible;">Stock</th>
                                                <th><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
                                                <th><label>Purchase Rate</label></th>
                                                <th><label>Rate</label></th>
                                                <th><label>From Location<b style="color:red;">&nbsp;*</b></label></th>
                                                <th><label>To Location<b style="color:red;">&nbsp;*</b></label></th>
                                                <th><label>Batch</label></th>
                                                <th><label>Vehicle</label></th>
                                                <th><label>Driver</label></th>
                                                <!--- <th><label>Driver Mobile</label></th> --->
                                                <th><label>Remarks</label></th>
                                                <?php
                                                if((int)$este_flag == 1){
                                                ?>
                                                <th><label>Employee</label></th>
                                                <th><label>CoA</label></th>
                                                <th><label>Amount</label></th>
                                                <?php
                                                }
                                                ?>
                                                <th style="visibility:hidden;"><label>Action</label></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="width:10px;visibility:hidden;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><input type="text" name="date[]" id="date[0]" class="form-control range_picker" style="width:80px;" value="<?php echo date('d.m.Y'); ?>" onchange="check_medvac_masterprices(this.id);" readonly /></td>
                                                <td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:60px;" /></td>
                                                <td><select name="code[]" id="code[0]" class="form-control select2" style="width:130px;" onchange="fetch_stock_master(this.id);check_medvac_masterprices(this.id);fetch_itemuom(this.id);"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="uom[]" id="uom[0]" class="form-control" style="width:80px;" readonly /></td>
                                                <td style="visibility:visible;"><input type="text" name="available_stock[]" id="available_stock[0]" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td><input type="text" name="quantity[]" id="quantity[0]" class="form-control" style="width:80px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="purchaserate[]" id="purchaserate[0]" class="form-control" style="width:80px;" readonly /></td>
                                                <td><input type="text" readonly name="price[]" id="price[0]" class="form-control" style="width:70px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><select name="fromwarehouse[]" id="fromwarehouse[0]" class="form-control select2" style="width:200px;" onchange="fetch_stock_master(this.id);check_medvac_masterprices(this.id);" ><option value="select">select</option><?php foreach($from_sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo str_replace("()","",$from_sector_name[$whouse_code]."(".$farm_code[$whouse_code].")"); ?></option><?php } ?></select></td>
                                                <td><select name="towarehouse[]" id="towarehouse[0]" class="form-control select2" style="width:200px;" onchange="checkfarm(this.id);fetch_batch(this.id);" ><option value="select">select</option><?php foreach($to_sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo str_replace("()","",$to_sector_name[$whouse_code]."(". $farm_code[$whouse_code].")"); ?></option><?php } ?></select></td>
                                                <td><input readonly type="text" name="batch[]" id="batch[0]" class="form-control" style="width:180px;" /></td>
                                                <?php if($vd_flag > 0) { ?>
                                                    <td><input type="text" name="vehicle_code[]" id="vehicle_code[0]" class="form-control" style="width:80px;"  /></td>
                                                    <td><input type="text" name="driver_code[]" id="driver_code[0]" class="form-control" style="width:80px;"  /></td>
                                               <?php } else { ?>
                                                <td><select name="vehicle_code[]" id="vehicle_code[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($vehicle_code as $truck_code){ ?><option value="<?php echo $truck_code; ?>"><?php echo $vehicle_name[$truck_code]; ?></option><?php } ?></select></td>
                                                <td><select name="driver_code[]" id="driver_code[0]" class="form-control select2" style="width:110px;"><option value="select">select</option><?php foreach($emp_code as $driver_code){ ?><option value="<?php echo $driver_code; ?>"><?php echo $emp_name[$driver_code]; ?></option><?php } ?></select></td>
                                                <?php } ?>
                                                <!--<td><input type="text" name="driver_mobile[]" id="driver_mobile[0]" class="form-control" style="width:110px;" onkeyup="validatemobile(this.id);" /></td> --->
                                                <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="width:100px;height:25px;"></textarea></td>
                                                <?php
                                                if((int)$este_flag == 1){
                                                ?>
                                                <td><select name="emp_code[]" id="emp_code[0]" class="form-control select2" style="width:110px;"><option value="select">select</option><?php foreach($exp_ecode as $ecode){ ?><option value="<?php echo $ecode; ?>"><?php echo $exp_ename[$ecode]; ?></option><?php } ?></select></td>
                                                <td><select name="emp_bcoa[]" id="emp_bcoa[0]" class="form-control select2" style="width:110px;"><option value="select">select</option><?php foreach($exp_acode as $ecode){ ?><option value="<?php echo $ecode; ?>"><?php echo $exp_aname[$ecode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="emp_eamt[]" id="emp_eamt[0]" class="form-control" style="width:70px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <?php
                                                }
                                                ?>
                                                <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price[0]" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="mflag[]" id="mflag[0]" class="form-control" value="0" style="width:10px;" readonly ></td>
                                            </tr>
                                        </tbody>
                                    </table><br/>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-1" style="visibility:hidden;">
                                        <label>Incr<b style="color:red;">&ensp;*</b></label>
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
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            //Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_inventorytransfer.php?ccid='+ccid;
            }
            function fetch_batch(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var to_sector = document.getElementById("towarehouse["+d+"]").value;
                var batch = "";
                <?php
                    foreach($farms as $fcode){
                        echo "if(to_sector == '$fcode'){";
                ?>
                        batch = '<?php echo $farms_batch[$fcode]; ?>';
                
                <?php
                    echo "}";
                    }
                ?>
                /*Split using a space character
                var rname = "";
                if(batch != ""){
                    let arr = batch.split('-');
                    alert(batch);
                    if(arr[2] != ""){
                        rname = arr[1]+'-'+arr[2];
                    }
                    else{
                        rname = batch;
                    }
                }*/
                
                document.getElementById("batch["+d+"]").value = batch;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var item = from_loc = to_loc = ""; var c = quantity = stock = mflag = 0; var l = true;
                var incrs = document.getElementById("incr").value;
                for(var b = 0;b <= incrs;b++){
                    c = b + 1;
                    item = document.getElementById("code["+b+"]").value;
                    quantity = document.getElementById("quantity["+b+"]").value;
                    from_loc = document.getElementById("fromwarehouse["+b+"]").value;
                    to_loc = document.getElementById("towarehouse["+b+"]").value;
                    mflag = document.getElementById("mflag["+b+"]").value;
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
                        else if(parseInt(mflag) == 1){
                            alert("Medicine/Vaccine price is not defined in masters for the item in row: "+c);
                            document.getElementById("code["+b+"]").focus();
                            l = false;
                        }
                        else{
                            l = true;
                        }
                    }
                }
                if(l == true){
                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                    if(stockcheck_flag == 1){
                        for(var d = 0;d <= incrs;d++){
                            if(l == true){
                                c = d + 1;
                                quantity = document.getElementById("quantity["+d+"]").value;
                                stock = document.getElementById("available_stock["+d+"]").value;
                                if(parseFloat(quantity) > parseFloat(stock)){
                                    alert("Stock not Available in row: "+c);
                                    document.getElementById("quantity["+d+"]").focus();
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
                var today = '<?php echo $today; ?>';
                var este_flag = '<?php echo $este_flag; ?>';

                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control range_picker" style="width:80px;" value="<?php echo date('d.m.Y'); ?>" onchange="check_medvac_masterprices(this.id);" readonly /></td>';
                html += '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:60px;" /></td>';
                html += '<td><select name="code[]" id="code['+d+']" class="form-control select2" style="width:130px;" onchange="fetch_stock_master(this.id);check_medvac_masterprices(this.id);fetch_itemuom(this.id);"><option value="select">select</option><?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>"><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="uom[]" id="uom['+d+']" class="form-control" style="width:80px;" readonly /></td>';
                html += '<td style="visibility:visible;"><input type="text" name="available_stock[]" id="available_stock['+d+']" class="form-control" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td><input type="text" name="quantity[]" id="quantity['+d+']" class="form-control" style="width:80px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="purchaserate[]" id="purchaserate['+d+']" class="form-control" style="width:80px;" readonly /></td>';
                html += '<td><input type="text" readonly name="price[]" id="price['+d+']" class="form-control" style="width:70px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><select name="fromwarehouse[]" id="fromwarehouse['+d+']" class="form-control select2" style="width:200px;" onchange="fetch_stock_master(this.id);check_medvac_masterprices(this.id);" ><option value="select">select</option><?php foreach($from_sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo str_replace("()","", $from_sector_name[$whouse_code]."(".$farm_code[$whouse_code].")"); ?></option><?php } ?></select></td>';
                html += '<td><select name="towarehouse[]" id="towarehouse['+d+']" class="form-control select2" style="width:200px;" onchange="checkfarm(this.id);fetch_batch(this.id);" ><option value="select">select</option><?php foreach($to_sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>"><?php echo str_replace("()","",$to_sector_name[$whouse_code]."(".$farm_code[$whouse_code].")"); ?></option><?php } ?></select></td>';
                html += '<td><input readonly type="text" name="batch[]" id="batch['+d+']" class="form-control" style="width:180px;" /></td>';
                <?php if($vd_flag > 0) { ?>
                    html += '<td><input type="text" name="vehicle_code[]" id="vehicle_code['+d+']" class="form-control" style="width:80px;"  /></td>';
                    html += '<td><input type="text" name="driver_code[]" id="driver_code['+d+']" class="form-control" style="width:80px;" /></td>';
                <?php } else { ?>
                html += '<td><select name="vehicle_code[]" id="vehicle_code['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($vehicle_code as $truck_code){ ?><option value="<?php echo $truck_code; ?>"><?php echo $vehicle_name[$truck_code]; ?></option><?php } ?></select></td>';
                html += '<td><select name="driver_code[]" id="driver_code['+d+']" class="form-control select2" style="width:110px;"><option value="select">select</option><?php foreach($emp_code as $driver_code){ ?><option value="<?php echo $driver_code; ?>"><?php echo $emp_name[$driver_code]; ?></option><?php } ?></select></td>';
                <?php } ?>
                //html += '<td><input type="text" name="driver_mobile[]" id="driver_mobile['+d+']" class="form-control" style="width:110px;" onkeyup="validatemobile(this.id);" /></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:100px;height:25px;"></textarea></td>';
                if(parseInt(este_flag) == 1){
                    html += '<td><select name="emp_code[]" id="emp_code['+d+']" class="form-control select2" style="width:110px;"><option value="select">select</option><?php foreach($exp_ecode as $ecode){ ?><option value="<?php echo $ecode; ?>"><?php echo $exp_ename[$ecode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="emp_bcoa[]" id="emp_bcoa['+d+']" class="form-control select2" style="width:110px;"><option value="select">select</option><?php foreach($exp_acode as $ecode){ ?><option value="<?php echo $ecode; ?>"><?php echo $exp_aname[$ecode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="emp_eamt[]" id="emp_eamt['+d+']" class="form-control" style="width:70px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                }
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price['+d+']" class="form-control" placeholder="0.00" style="width:50px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="mflag[]" id="mflag['+d+']" class="form-control" placeholder="0.00" style="width:10px;" readonly ></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function checkfarm(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
              
                
                var from_sector = document.getElementById("fromwarehouse["+d+"]").value;
                var to_sector = document.getElementById("towarehouse["+d+"]").value;
                if(from_sector == to_sector){
                    alert("From Farm and To Farm Should not same.");
                    document.getElementById("towarehouse["+d+"]").selectedIndex = 1;
                }
            }            
            function fetch_stock_master(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date["+d+"]").value;
                var sector = document.getElementById("fromwarehouse["+d+"]").value;
                var item_code = document.getElementById("code["+d+"]").value;
                
                if(sector != "select" && item_code != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&trtype=stk_transfer";
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_price = this.responseText;
                            if(item_price.length > 0){
                                var item_dt1 = item_price.split("@");
                                document.getElementById("available_stock["+d+"]").value = item_dt1[0];
                                document.getElementById("avg_price["+d+"]").value = item_dt1[1];
                                document.getElementById("price["+d+"]").value = item_dt1[2];
                                document.getElementById("purchaserate["+d+"]").value = item_dt1[4]; 
                            }
                            else{
                                alert("Item Stock not available, Kindly check before saving ...!");
                                document.getElementById("available_stock["+d+"]").value = 0;
                                document.getElementById("price["+d+"]").value = 0;
                                document.getElementById("avg_price["+d+"]").value = 0;
                                document.getElementById("purchaserate["+d+"]").value = 0; 
                            }
                        }
                    }
                }
                else{
                    document.getElementById("available_stock["+d+"]").value = 0;
                    document.getElementById("price["+d+"]").value = 0;
                    document.getElementById("avg_price["+d+"]").value = 0;
                }
            }           
            function check_medvac_masterprices(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date["+d+"]").value;
                var item_code = document.getElementById("code["+d+"]").value;
                var from_sector = document.getElementById("fromwarehouse["+d+"]").value;
                var to_sector = document.getElementById("towarehouse["+d+"]").value;
                
                if(date != "" && item_code != "select" && from_sector != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmasternew.php?date="+date+"&item_code="+item_code+"&from_sector="+from_sector+"&to_sector="+to_sector+"&row_count="+d;
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var mprc = this.responseText;
                            var mprc_details = mprc.split("@");
                            if(parseInt(mprc_details[0]) == 1 && parseInt(mprc_details[1]) == 1){
                                if(parseInt(mprc_details[2]) == 1){
                                    document.getElementById("mflag["+mprc_details[3]+"]").value = 0;  
                                }
                                else if(parseInt(mprc_details[2]) == 0){
                                    document.getElementById("mflag["+mprc_details[3]+"]").value = 1;  
                                }
                            }
                            else{
                                document.getElementById("mflag["+mprc_details[3]+"]").value = 0;
                            }
                        }
                    }
                }
                else{
                    document.getElementById("mflag["+d+"]").value = 0;
                }
            }
            function fetch_itemuom(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var code = document.getElementById("code["+d+"]").value;
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
			function validatemobile(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 10){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
        <script>
            //Date Range selection
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
        </script>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more ininventorytransferion"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more ininventorytransferion";
}
?>