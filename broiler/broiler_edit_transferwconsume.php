<?php
//broiler_edit_transferwconsume.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['transferwconsume'];
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
        $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%Vaccine%' OR `description` LIKE '%bio%' OR `description` LIKE '%Water Soluble%') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($icat_code == ""){ $icat_code = $row['code']; } else{ $icat_code = $icat_code."','".$row['code']; } }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_code') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		$farms = $batch_name = array();
		$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code']; $batch_name[$row['farm_code']] = $row['description'];}
        $farm_list = implode("','", $farms);
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
		$sql = "SELECT * FROM `broiler_vehicle` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }
				
		$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
		while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
				
		$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
        
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'MedVac Transfer' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }
        //echo $stockcheck_flag;
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'MedVac Transfer' AND `field_function` LIKE 'Auto Avg Price'"; $query = mysqli_query($conn,$sql); $autoavgprice_flag = 0; $aapcount = mysqli_num_rows($query);
        if($aapcount > 0){ while($row = mysqli_fetch_assoc($query)){ $autoavgprice_flag = $row['flag']; } } else{ $autoavgprice_flag = 0; } if($autoavgprice_flag == "" || $autoavgprice_flag == 0){ $autoavgprice_flag = 0; }
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
        <?php
        $id = $_GET['trnum'];
        $sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $link_trnum = $row['link_trnum'];
            $date = $row['date'];
            $dcno = $row['dcno'];
            $fromwarehouse = $row['fromwarehouse'];
            $towarehouse = $row['towarehouse'];
            $code = $row['code'];
            $quantity = $row['quantity'];
            $price = $row['price'];
            $amount = $row['amount'];
            $farmer_price = $row['farmer_price'];
            $transport_cost = $row['transport_cost'];
            $paid_by = $row['paid_by'];
            $vcode = $row['vehicle_code'];
            $dcode = $row['driver_code'];
            $remarks = $row['remarks'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Inventory Transfer2</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_transferwconsume.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group" style="width:170px;">
                                            <label>From Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="fromwarehouse" id="fromwarehouse" class="form-control select2" style="width:160px;" onchange="fetch_edit_stock_master();fetch_farm_from(this.id);check_medvac_masterprices();">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($fromwarehouse == $whouse_code){ echo "selected"; } ?>><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Batch Name</label>
							                <input type="text" name="fromloc" id="fromloc" class="form-control" style="width:120px;" value="<?php echo $fromwarehouse; ?>" readonly />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>To Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="towarehouse" id="towarehouse" class="form-control select2" style="width:160px;" onchange="check_medvac_masterprices();fetch_farm_to(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($farm_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($towarehouse == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Batch Name</label>
							                <input type="text" name="toloc" id="toloc" class="form-control" style="width:120px;" value="<?php echo $towarehouse; ?>" readonly />
                                        </div>
                                        <div class="form-group" style="width:140px;">
                                            <label>Vehicle</label>
							                <select name="vehicle_code" id="vehicle_code" class="form-control select2" style="width:130px;">
                                                <option value="select">select</option>
                                                <?php foreach($vehicle_code as $truck_code){ ?><option value="<?php echo $truck_code; ?>" <?php if($vcode == $truck_code){ echo "selected"; } ?>><?php echo $vehicle_name[$truck_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Driver</label>
							                <select name="driver_code" id="driver_code" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($emp_code as $driver_code){ ?><option value="<?php echo $driver_code; ?>" <?php if($dcode == $driver_code){ echo "selected"; } ?>><?php echo $emp_name[$driver_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Transport Cost</label>
							                <input type="text" name="transport_cost" id="transport_cost" class="form-control" value="<?php echo $transport_cost; ?>" style="width:120px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Paid By<b style="color:red;">&nbsp;*</b></label>
							                <select name="paid_by" id="paid_by" class="form-control select2" style="width:120px;">
                                                <option value="select" <?php if($paid_by == "select"){ echo "selected"; } ?>>select</option>
                                                <option value="company" <?php if($paid_by == "company"){ echo "selected"; } ?>>Company</option>
                                                <option value="FromFarm" <?php if($paid_by == "FromFarm"){ echo "selected"; } ?>>From Farm</option>
                                                <option value="ToFarm" <?php if($paid_by == "ToFarm"){ echo "selected"; } ?>>To Farm</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control datepicker" style="width:100px;" value="<?php echo date('d.m.Y',strtotime($date)); ?>" onchange="broiler_check_futuredate();" />
                                        </div>
                                        <div class="form-group">
                                            <label>Dc No.</label>
							                <input type="text" name="dcno" id="dcno" value="<?php echo $dcno; ?>" class="form-control" style="width:70px;" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Item<b style="color:red;">&nbsp;*</b></label>
							                <select name="code" id="code" class="form-control select2" style="width:160px;" onchange="fetch_edit_stock_master();check_medvac_masterprices();fetch_itemuom();">
                                                <option value="select">select</option>
                                                <?php foreach($item_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($prod_code == $code){ echo "selected"; } ?>><?php echo $item_name[$prod_code]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>UOM</label>
							                <input type="text" name="uom" id="uom" class="form-control" value="<?php echo $item_cunit[$code]; ?>" style="width:80px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Quantity<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="quantity" id="quantity" class="form-control" value="<?php echo $quantity; ?>" style="width:90px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                        </div>
                                        <div class="form-group">
                                            <label>Rate</label>
							                <input type="text" name="price" id="price" class="form-control"  value="<?php echo $price; ?>"style="width:90px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                        </div>
                                        <div class="form-group">
                                            <label>Remarks</label>
							                <textarea name="remarks" id="remarks" class="form-control" style="width:120px;height:25px;"><?php echo $remarks; ?></textarea>
                                        </div>
                                        <div class="form-group" style="visibility:visible;">
                                            <label>Stock</label>
							                <input type="text" name="available_stock" id="available_stock" class="form-control" placeholder="0.00" style="width:50px;" readonly >
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>A.Price</label>
							                <input type="text" name="avg_price" id="avg_price" class="form-control" placeholder="0.00" style="width:50px;" readonly >
                                        </div>
                                        <div class="form-group" style="width:50px;visibility:hidden;">
                                            <label title="Future Date">M</label>
							                <input type="text" name="mflag" id="mflag" class="form-control" value="0" style="width:50px;" readonly >
                                        </div>
                                    </div>
                                    <div class="p-0 col-md-12" id="row_body">

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id.'@'.$link_trnum; ?>">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>FD Flag<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="fd_flag" id="fd_flag" class="form-control" value="0">
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
                window.location.href = 'broiler_display_transferwconsume.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var item = from_loc = to_loc = ""; var c = quantity = mflag = 0; var l = true;
                
                from_loc = document.getElementById("fromwarehouse").value;
                to_loc = document.getElementById("towarehouse").value;
                item = document.getElementById("code").value;
                quantity = document.getElementById("quantity").value;
                fd_flag = document.getElementById("fd_flag").value;
                mflag = document.getElementById("mflag").value;
                
                if(from_loc.match("select")){
                    alert("Kindly select From Location");
                    document.getElementById("fromwarehouse").focus();
                    l = false;
                }
                else if(to_loc.match("select")){
                    alert("Kindly select To Location");
                    document.getElementById("towarehouse").focus();
                    l = false;
                }
                else if(item.match("select")){
                    alert("Kindly select Item");
                    document.getElementById("code").focus();
                    l = false;
                }
                else if(fd_flag == 1 || fd_flag == "1"){
                    alert("Date need to be less than or equal to current Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(mflag == 1 || mflag == "1"){
                    alert("Medicine/Vaccine price is not defined in masters for the item");
                    document.getElementById("code").focus();
                    l = false;
                }
                else if(quantity.length == 0 || quantity == 0 || quantity == "" || quantity == "0.00" || quantity == "0" || quantity == 0.00){
                    alert("Kindly enter Quantity");
                    document.getElementById("quantity").focus();
                    l = false;
                }
                else{ }
                if(l == true){
                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>'; var stock = 0;
                    if(stockcheck_flag == 1){
                        quantity = document.getElementById("quantity").value;
                        stock = document.getElementById("available_stock").value;
                        if(parseFloat(quantity) > parseFloat(stock)){
                            alert("Stock not Available");
                            document.getElementById("quantity").focus();
                            l = false;
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
            function fetch_edit_stock_master(){
                var trnum = '<?php echo $link_trnum."@".$id; ?>';
                var date = document.getElementById("date").value;
                var sector = document.getElementById("fromwarehouse").value;
                var item_code = document.getElementById("code").value;

                if(sector != "select" && item_code != "select"){
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&id="+trnum;
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
            }

            function fetch_farm_from(a){
                var from_code = document.getElementById("fromwarehouse").value;
                var fbatch = "";
                <?php
                    foreach($farms as $fcode){
                        echo "if(from_code == '$fcode'){";
                ?>
                        fbatch = '<?php echo $batch_name[$fcode]; ?>';
                <?php
                    echo "}";
                    }
                ?>
                document.getElementById("fromloc").value = fbatch;
                }
                function fetch_farm_to(a) {
                    // Get the selected value from the dropdown
                    var to_code = document.getElementById("towarehouse").value;

                    // Initialize tbatch as an empty string
                    var tbatch = "";
                    
                    <?php
                        foreach ($farms as $fcode) {
                            echo "if (to_code == '$fcode') {";
                    ?>
                            // Assign the corresponding batch value
                            tbatch = '<?php echo $batch_name[$fcode]; ?>';
                    <?php
                            echo "}";
                        }
                    ?>
                    // Set the value of the "toloc" input field
                    document.getElementById("toloc").value = tbatch;
                }
            
            function broiler_check_futuredate(){
                var date = document.getElementById("date").value;
                var fd_flags = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_check_futuredate.php?&date="+date;
				var asynchronous = true;
				fd_flags.open(method, url, asynchronous);
				fd_flags.send();
				fd_flags.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						k = this.responseText;
                        document.getElementById("fd_flag").value = k;
                        
                    }
                }
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
            fetch_edit_stock_master(); broiler_check_futuredate(); check_medvac_masterprices();
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(5); document.getElementById(x).value = b; }
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