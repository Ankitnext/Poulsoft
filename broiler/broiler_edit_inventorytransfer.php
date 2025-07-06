<?php
//broiler_edit_inventorytransfer.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['inventorytransfer'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
$link_active_flag = 1;
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
        global $drng_cday; $drng_cday = 1; global $drng_furl; $drng_furl = str_replace("_edit_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        include "poulsoft_fetch_daterange_master.php";

        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $to_sector_code[$row['code']] = $row['code']; $to_sector_name[$row['code']] = $row['description']; }

		$farms = array();
		$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description']; }
        $farm_list = implode("','", $farms);
        
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $to_sector_code[$row['code']] = $sector_code[$row['code']] = $row['code']; $to_sector_name[$row['code']] = $sector_name[$row['code']] = $row['description']; $farm_code[$row['code']] = $row['farm_code']; }
        
		$sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
				
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }

        //Fetch Feed Details and Feed in Bags Flag
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $item_feed_code[$row['code']] = $row['code']; $item_feed_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Stock Transfer' AND `field_function` LIKE 'Bags' AND `flag` = 1"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
        
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Item Transfer' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }
        //echo $stockcheck_flag;
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
        }
        */
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <?php
            $id = $_GET['id'];
            $sql = "SELECT * FROM `item_stocktransfers` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = date("d.m.Y",strtotime($row['date']));
                $trnum = $row['trnum'];
                $dcno = $row['dcno'];
                $fromwarehouse = $row['fromwarehouse'];
                $code = $row['code'];
                $price = $row['amount'] / $row['quantity'];
                
                $feed_item =  $row['code'];
                if(!empty($item_feed_name[$feed_item]) && !empty($row['quantity']) && $bag_access_flag > 0){
                    $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$code' AND `active` = '1' AND `dflag` = '0'";
                    $bquery = mysqli_query($conn,$bsql); $bcount = $ibag_flag1 = mysqli_num_rows($bquery);
                  
                    if($bcount > 0){
                        if($ibag_flag1 > 0 && $bag_access_flag > 0){
                            while($brow = mysqli_fetch_assoc($bquery)){
                                if($brow['code'] != "all"){
                                    $quantity = $row['quantity'] / $brow['bag_size'];
                                    $price = $price * $brow['bag_size'];
                                }
                                else{
                                    $quantity = $row['quantity'] / $brow['bag_size'];
                                    $price = $price * $brow['bag_size'];
                                }
                            }
                        } else{
                            $quantity = $row['quantity'];
                            $price = $row['amount'] / $row['quantity'];
                        }
                    }
                    else{
                        $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'"; $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);

                        if($ibag_flag1 > 0 && $bag_access_flag > 0){
                            while($brow = mysqli_fetch_assoc($bquery)){
                                if($brow['code'] != "all"){
                                    $quantity = $row['quantity'] / $brow['bag_size'];
                                    $price = $price * $brow['bag_size'];
                                }
                                else{
                                    $quantity = $row['quantity'] / $brow['bag_size'];
                                    $price = $price * $brow['bag_size'];
                                }
                            }
                        } else{
                            $quantity = $row['quantity'];
                            $price = $row['amount'] / $row['quantity'];
                        }
                    }
                }
                else{
                    $quantity = $row['quantity'];
                    $price = $row['amount'] / $row['quantity'];
                }

                $towarehouse = $row['towarehouse'];
                $vcode = $row['vehicle_code'];
                $driver_code = $row['driver_code'];
                $driver_mobile = $row['driver_mobile'];
                $eexp_code = $row['emp_code'];
                $emp_bcoa = $row['emp_bcoa'];
                $emp_eamt = $row['emp_eamt']; if($emp_eamt == ""){ $emp_eamt = 0; } $emp_eamt = round($emp_eamt,5);
                $remarks = $row['remarks'];

                $batch = explode("-",$farms_batch[$towarehouse]);
                $rename_batch = $batch[1]."-".$batch[2];
            }

        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Inventory Transfer</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_inventorytransfer.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control range_picker" style="width:80px;" value="<?php echo $date; ?>" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Dc No.</label>
							                <input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $dcno; ?>" style="width:60px;" onchange="fetch_edit_stock_master();" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Item<b style="color:red;">&nbsp;*</b></label>
							                <select name="code" id="code" class="form-control select2" style="width:160px;" onchange="fetch_edit_stock_master();fetch_itemuom();">
                                                <option value="select">select</option>
                                                <?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($code == $prod_code){ echo "selected"; } ?>><?php echo $item_name[$prod_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>UOM</label>
							                <input type="text" name="uom" id="uom" class="form-control" value="<?php echo $item_cunit[$code]; ?>" style="width:80px;" />
                                        </div>
                                        <div class="form-group" style="width:100px;visibility:visible;">
                                            <label>Stock</label>
							                <input type="text" name="available_stock" id="available_stock" class="form-control" value="" style="width:90px;" onkeyup="validatemobile(this.id);" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Quantity<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="quantity" id="quantity" class="form-control" value="<?php echo $quantity; ?>" style="width:90px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Rate</label>
							                <input type="text" readonly name="price" id="price" class="form-control" value="<?php echo $price; ?>" style="width:90px;" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>From Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="fromwarehouse" id="fromwarehouse" class="form-control select2" style="width:160px;" onchange="fetch_edit_stock_master();">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($fromwarehouse == $whouse_code){ echo "selected"; } ?>><?php echo str_replace("()","",$sector_name[$whouse_code]."(".$farm_code[$whouse_code].")"); ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>To Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="towarehouse" id="towarehouse" class="form-control select2" style="width:160px;" onchange="fetch_edit_stock_master();fetch_batch(this.id)">
                                                <option value="select">select</option>
                                                <?php foreach($to_sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($towarehouse == $whouse_code){ echo "selected"; } ?>><?php echo str_replace("()","",$to_sector_name[$whouse_code]."(".$farm_code[$whouse_code].")"); ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Batch</label>
                                            <td><input readonly type="text" name="batch" id="batch"  value="<?php echo $rename_batch; ?>" class="form-control" style="width:180px;" /></td>
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Vehicle</label>
							                <select name="vehicle_code" id="vehicle_code" class="form-control select2" style="width:180px;">
                                                <option value="select">select</option>
                                                <?php foreach($vehicle_code as $truck_code){ ?><option value="<?php echo $truck_code; ?>" <?php if($vcode == $truck_code){ echo "selected"; } ?>><?php echo $vehicle_name[$truck_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Driver</label>
							                <select name="driver_code" id="driver_code" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($emp_code as $dcode){ ?><option value="<?php echo $dcode; ?>" <?php if($driver_code == $dcode){ echo "selected"; } ?>><?php echo $emp_name[$dcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <!--- <div class="form-group" style="width:130px;">
                                            <label>Driver Mobile</label>
							                <input type="text" name="driver_mobile" id="driver_mobile" class="form-control" value="<?php echo $driver_mobile; ?>" style="width:120px;" onkeyup="validatemobile(this.id);" />
                                        </div> --->
                                        <div class="form-group">
                                            <label>Remarks</label>
							                <textarea name="remarks" id="remarks" class="form-control" style="padding: 3px;width:120px;height:25px;"><?php echo $remarks; ?></textarea>
                                        </div>
                                        <?php if((int)$este_flag == 1){ ?>
                                        <div class="form-group" style="width:130px;">
                                            <label>Employee</label>
							                <select name="emp_code" id="emp_code" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($exp_ecode as $dcode){ ?><option value="<?php echo $dcode; ?>" <?php if($eexp_code == $dcode){ echo "selected"; } ?>><?php echo $exp_ename[$dcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>CoA</label>
							                <select name="emp_bcoa" id="emp_bcoa" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($exp_acode as $dcode){ ?><option value="<?php echo $dcode; ?>" <?php if($emp_bcoa == $dcode){ echo "selected"; } ?>><?php echo $exp_aname[$dcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Amount</label>
							                <input type="text" name="emp_eamt" id="emp_eamt" class="form-control" value="<?php echo $emp_eamt; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" />
                                        </div>
                                        <?php } ?>
                                        <div class="form-group" style="width:10px;visibility:hidden;">
                                            <label>P</label>
							                <input type="text" name="avg_price" id="avg_price" class="form-control" value="" style="width:10px;" onkeyup="validatemobile(this.id);" readonly />
                                        </div>
                                        <div class="form-group" style="width:10px;visibility:hidden;">
                                            <label>M</label>
							                <input type="text" name="mflag" id="mflag" class="form-control" value="0" style="width:10px;" onkeyup="validatemobile(this.id);" readonly />
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
                window.location.href = 'broiler_display_inventorytransfer.php?ccid='+ccid;
            }
            function fetch_batch(a){
                //var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var to_sector = document.getElementById("towarehouse").value;
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
                  document.getElementById("batch").value = batch;

            }
            function checkval(){
                document.getElementById("submit").style.visibility = "hidden";
                var item = from_loc = to_loc = ""; var c = quantity = mflag = 0; var l = true;
                item = document.getElementById("code").value;
                quantity = document.getElementById("quantity").value;
                from_loc = document.getElementById("fromwarehouse").value;
                to_loc = document.getElementById("towarehouse").value;
                mflag = document.getElementById("mflag").value;
               
                if(item.match("select")){
                    alert("Kindly select Item in row: "+c);
                    document.getElementById("code").focus();
                    l = false;
                }
                else if(quantity.length == 0 || quantity == 0 || quantity == "" || quantity == "0.00" || quantity == "0" || quantity == 0.00){
                    alert("Kindly enter Quantity in row: "+c);
                    document.getElementById("quantity").focus();
                    l = false;
                }
                else if(from_loc.match("select")){
                    alert("Kindly select From Location in row: "+c);
                    document.getElementById("fromwarehouse").focus();
                    l = false;
                }
                else if(to_loc.match("select")){
                    alert("Kindly select To Location in row: "+c);
                    document.getElementById("towarehouse").focus();
                    l = false;
                }
                else if(parseInt(mflag) == 1){
                    alert("Medicine/Vaccine price is not defined in masters for the item in row: "+c);
                    document.getElementById("code").focus();
                    l = false;
                }
                else{
                    l = true;
                }
                if(l == true){
                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                    if(stockcheck_flag == 1){
                        quantity = document.getElementById("quantity").value;
                        stock = document.getElementById("available_stock").value;
                        if(parseFloat(quantity) > parseFloat(stock)){
                            alert("Stock not Available in row: "+c);
                            document.getElementById("quantity").focus();
                            l = false;
                        }
                    }
                    else{ }
                }
                if(l == true){
                    document.getElementById("submit").style.visibility = "hidden";
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
                    return false;
                }
            }
            function fetch_edit_stock_master(){
                var trnum = '<?php echo $trnum; ?>';
                var date = document.getElementById("date").value;
                var sector = document.getElementById("fromwarehouse").value;
                var item_code = document.getElementById("code").value;

                if(sector != "select" && item_code != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&id="+trnum+"&trtype=stk_transfer";
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_price = this.responseText;
                            if(item_price.length > 0){
                                var item_details = item_price.split("@");
                                document.getElementById("available_stock").value = item_details[0];
                                document.getElementById("avg_price").value = item_details[1];

                                var autoavgprice_flag = '<?php echo $autoavgprice_flag; ?>';
                                if(autoavgprice_flag == 1){ document.getElementById("price").value = item_details[2]; }
                            }
                            else{
                                alert("Item Stock not available, Kindly check before saving ...!");
                                document.getElementById("available_stock").value = 0;
                                if(med_price == "" || med_price == "0" || med_price == "0.00"){ document.getElementById("price").value = 0; }
                                document.getElementById("avg_price").value = 0;
                            }
                        }
                    }
                }
                else{
                    document.getElementById("available_stock").value = 0;
                    if(med_price == "" || med_price == "0" || med_price == "0.00"){ document.getElementById("price").value = 0; }
                    document.getElementById("avg_price").value = 0;
                }
                check_medvac_masterprices();
            }          
            function check_medvac_masterprices(){
                var date = document.getElementById("date").value;
                var item_code = document.getElementById("code").value;
                var from_sector = document.getElementById("fromwarehouse").value;
                var to_sector = document.getElementById("towarehouse").value;
                
                if(date != "" && item_code != "select" && from_sector != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmasternew.php?date="+date+"&item_code="+item_code+"&from_sector="+from_sector+"&to_sector="+to_sector;
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
                                    document.getElementById("mflag").value = 0;  
                                }
                                else if(parseInt(mprc_details[2]) == 0){
                                    document.getElementById("mflag").value = 1;  
                                }
                            }
                            else{
                                document.getElementById("mflag").value = 0;
                            }
                        }
                    }
                }
                else{
                    document.getElementById("mflag").value = 0;
                }
            }
            function fetch_itemuom(){
                var code = document.getElementById("code").value;
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
                document.getElementById("uom").value = uom;
            }
            fetch_edit_stock_master();
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
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validatemobile(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 10){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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
        echo "You don't have access to this page \n Kindly contact your admin for more ininventorytransferion"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more ininventorytransferion";
}
?>